<?php

namespace App\Mail;

use App\Models\Variant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StockBackMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Variant $variant)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "{$this->variant->product->name} est de retour en stock !",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.stock-back',
            with: ['variant' => $this->variant->load('product')],
        );
    }
}
