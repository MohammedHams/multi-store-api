<?php

namespace App\Http\Controllers\Api;
use App\Models\Order;
use App\Models\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Services\OrderPdfService;
use Twilio\Rest\Client;
use App\Models\ExternalServiceLog;
use App\Services\WhatsAppService;

class OrderController extends Controller
{

    public function index()
    {
        $orders = Order::with(['products', 'store'])->get();
        return response()->json($orders);
    }

    public function store(OrderRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $order = Order::create([
                'store_id' => $request->store_id,
                'user_id' => auth()->id(),
                'total' => 0,
                'status' => 'pending'
            ]);

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

            dispatch(function () use ($order) {
                try {
                    $whatsappService = new WhatsAppService();
                    $response = $whatsappService->sendOrderNotification($order);

                    ExternalServiceLog::create([
                        'order_id' => $order->id,
                        'service_type' => 'whatsapp',
                        'status' => 'success',
                        'response' => json_encode($response),
                        'attempts' => 1
                    ]);
                } catch (\Exception $e) {
                    ExternalServiceLog::create([
                        'order_id' => $order->id,
                        'service_type' => 'whatsapp',
                        'status' => 'failed',
                        'response' => $e->getMessage(),
                        'attempts' => 1
                    ]);
                }
            })->afterResponse();

            return response()->json([
                'message' => 'تم إنشاء الطلب بنجاح وسيتم إرسال الإشعار قريباً',
                'order' => $order->load('products')
            ], 201);
        });
    }
    public function show(Order $order): JsonResponse
    {
        $order->load(['products', 'store', 'user']);
        return response()->json($order);
    }
    public function update(OrderRequest $request, Order $order): JsonResponse
    {
        $order->update(['status' => $request->status]);
        return response()->json($order);
    }

    public function destroy(Order $order): JsonResponse
    {
        $order->delete();
        return response()->json(null, 204);
    }
    public function sendToWhatsApp(Order $order): JsonResponse
    {
        try {
            $pdfPath = (new OrderPdfService())->generatePdf($order);
            $twilio = new Client(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );

            $to = '+966500000000';

            $message = $twilio->messages->create(
                "whatsapp:$to",
                [
                    'from' => 'whatsapp:'.config('services.twilio.whatsapp_from'),
                    'body' => "فاتورة طلبك #{$order->id} من متجر {$order->store->name}",
                    'mediaUrl' => [asset("storage/pdfs/".basename($pdfPath))]
                ]
            );

            ExternalServiceLog::create([
                'order_id' => $order->id,
                'service_type' => 'whatsapp',
                'status' => 'success',
                'response' => json_encode($message->toArray()),
                'attempts' => 1
            ]);

            return response()->json([
                'message' => 'تم إرسال الفاتورة عبر واتساب بنجاح',
                'whatsapp_status' => $message->status
            ]);

        } catch (\Exception $e) {
            ExternalServiceLog::create([
                'order_id' => $order->id,
                'service_type' => 'whatsapp',
                'status' => 'failed',
                'response' => $e->getMessage(),
                'attempts' => 1
            ]);

            return response()->json([
                'message' => 'فشل إرسال الفاتورة عبر واتساب',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
