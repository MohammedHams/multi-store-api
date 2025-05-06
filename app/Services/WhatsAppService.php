<?php

namespace App\Services;

use App\Models\Order;
use Twilio\Rest\Client;

class WhatsAppService
{
    public function sendOrderNotification(Order $order): array
    {
        $order->load(['store', 'user']);

        $pdfPath = (new OrderPdfService())->generatePdf($order);
        $pdfUrl = asset("storage/pdfs/".basename($pdfPath));

        $message = "🛒 تم استلام طلب جديد #{$order->id}\n"
            . "📌 المتجر: {$order->store->name}\n"
            . "💰 الإجمالي: {$order->total} ر.س\n"
            . "🔗 تفاصيل الطلب: {$pdfUrl}";

        $twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );

        return $twilio->messages->create(
            "whatsapp:{$order->user->phone}",
            [
                'from' => 'whatsapp:'.config('services.twilio.whatsapp_from'),
                'body' => $message,
                'mediaUrl' => [$pdfUrl]
            ]
        )->toArray();
    }
}
