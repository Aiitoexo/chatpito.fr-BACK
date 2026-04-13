<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function handle(): void
    {
        $this->order->load('items.variant.product', 'user');

        $invoice = $this->order->invoice;

        if (!$invoice) {
            $invoice = Invoice::create([
                'order_id' => $this->order->id,
                'invoice_number' => Invoice::generateNumber(),
                'subtotal_ht' => $this->order->subtotal_ht,
                'tax_amount' => $this->order->tax_amount,
                'total_ttc' => $this->order->total,
                'status' => 'generated',
                'issued_at' => now(),
            ]);
        }

        $pdf = Pdf::loadView('invoices.template', [
            'order' => $this->order,
            'invoice' => $invoice,
        ]);

        $filename = "factures/{$invoice->invoice_number}.pdf";
        Storage::disk('local')->put($filename, $pdf->output());

        $invoice->update(['pdf_path' => $filename]);
    }
}
