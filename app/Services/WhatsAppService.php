<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ExternalServiceLog;
use Twilio\Rest\Client;

class WhatsAppService
{
    protected $twilio;

    public function __construct()
    {
        $this->twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
    }

    public function sendOrderNotification(Order $order, string $phoneNumber = null)
    {
        try {
            // Generate the order PDF
            $pdfPath = (new OrderPdfService())->generatePdf($order);

            // Use provided phone number or default
            $to = $phoneNumber ?? '+966500000000';

            // Send the WhatsApp message
            $message = $this->twilio->messages->create(
                "whatsapp:$to",
                [
                    'from' => 'whatsapp:'.config('services.twilio.whatsapp_from'),
                    'body' => "فاتورة طلبك #{$order->id} من متجر {$order->store->name}",
                    'mediaUrl' => [asset("storage/pdfs/".basename($pdfPath))]
                ]
            );

            // Log the successful attempt
            ExternalServiceLog::create([
                'order_id' => $order->id,
                'service_type' => 'whatsapp',
                'status' => 'success',
                'response' => json_encode($message->toArray()),
                'attempts' => 1
            ]);

            return [
                'success' => true,
                'message' => 'WhatsApp notification sent successfully',
                'whatsapp_status' => $message->status
            ];

        } catch (\Exception $e) {
            // Log the failed attempt
            ExternalServiceLog::create([
                'order_id' => $order->id,
                'service_type' => 'whatsapp',
                'status' => 'failed',
                'response' => $e->getMessage(),
                'attempts' => 1
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send WhatsApp notification',
                'error' => $e->getMessage()
            ];
        }
    }
}
