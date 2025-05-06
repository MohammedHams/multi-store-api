<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Order;

class OrderPdfService
{
    public function generatePdf(Order $order): string
    {
        $order->load(['products', 'store', 'user']);

        $pdf = PDF::loadView('pdf.order', [
            'order' => $order,
            'date' => now()->format('Y-m-d')
        ]);

        $fileName = "order_{$order->id}_".time().'.pdf';
        $path = storage_path("app/public/pdfs/{$fileName}");

        if (!file_exists(dirname($path))) {
            if (!mkdir($concurrentDirectory = dirname($path), 0755, true) && !is_dir($concurrentDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }
        }

        $pdf->save($path);

        return $path;
    }

}
