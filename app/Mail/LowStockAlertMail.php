<?php

namespace App\Mail;

use App\Models\Stock;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowStockAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Stock $stock)
    {
    }

    public function envelope(): Envelope
    {
        $product = $this->stock->variant->product->name ?? 'Produit';
        $variant = $this->stock->variant->nom ?? '';

        return new Envelope(
            subject: "Stock bas — {$product} ({$variant})",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.low-stock-alert',
            with: ['stock' => $this->stock->load('variant.product', 'variant.suppliers')],
        );
    }
}
