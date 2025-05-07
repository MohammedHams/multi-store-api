<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\OrderPdfService;
use Twilio\Rest\Client;
use App\Models\ExternalServiceLog;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OrderController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $user = auth()->user();

        if ($this->authorize('viewAny')) {
            $orders = Order::with(['products', 'store', 'user'])->get();
        } else{

            $orders = Order::with(['products', 'store', 'user'])
                ->where('store_id', $user->store_id)
                ->get();
        }


        return response()->json($orders);
    }

    public function store(OrderRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $user = auth()->user();

            if ($user->type === User::STORE_OWNER && $request->store_id !== $user->store_id) {
                return response()->json(['message' => 'Unauthorized to create orders for this store'], 403);
            }

            $order = Order::create([
                'store_id' => $request->store_id,
                'user_id' => $user->id,
                'total' => 0,
                'status' => 'pending'
            ]);

            // Process products and calculate total
            $total = 0;
            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['product_id']);
                $order->products()->attach($product->id, [
                    'quantity' => $item['quantity'],
                    'price' => $product->price
                ]);
                $total += $product->price * $item['quantity'];
            }
            $order->update(['total' => $total]);

            // Send WhatsApp notification in the background
            dispatch(function () use ($order) {
                // Using the WhatsAppService
                $whatsAppService = new WhatsAppService();
                $whatsAppService->sendOrderNotification($order);
            })->afterResponse();

            return response()->json([
                'message' => 'تم إنشاء الطلب بنجاح وسيتم إرسال الإشعار قريباً',
                'order' => $order->load('products')
            ], 201);
        });
    }

    public function show($storeId, $orderId): JsonResponse
    {
        $user = Auth::user();
        $order = Order::where('id', $orderId)
            ->where('store_id', $storeId)
            ->firstOrFail();

        if ($user->type === User::STORE_OWNER && $order->store_id !== $user->store_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }



        return response()->json($order->load(['products', 'store', 'user']));
    }

    public function update(OrderRequest $request, Order $order): JsonResponse
    {
        $user = auth()->user();

        if ($user->type === User::STORE_OWNER && $order->store_id !== $user->store_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Additional validation for staff permissions
        if ($user->type === User::STAFF) {
            $this->authorize('updateOrder', $order);
        }

        $order->update(['status' => $request->status]);
        return response()->json($order);
    }

    public function destroy(Order $order): JsonResponse
    {
        $user = auth()->user();

        if ($user->type === User::STORE_OWNER && $order->store_id !== $user->store_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($user->type !== User::SUPER_ADMIN) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $order->delete();
        return response()->json(null, 204);
    }
    public function sendToWhatsApp(Order $order): JsonResponse
    {
        $whatsAppService = new WhatsAppService();
        $result = $whatsAppService->sendOrderNotification($order);

        if ($result['success']) {
            return response()->json([
                'message' => 'تم إرسال الفاتورة عبر واتساب بنجاح',
                'whatsapp_status' => $result['whatsapp_status']
            ]);
        } else {
            return response()->json([
                'message' => 'فشل إرسال الفاتورة عبر واتساب',
                'error' => $result['error']
            ], 500);
        }
    }

}
