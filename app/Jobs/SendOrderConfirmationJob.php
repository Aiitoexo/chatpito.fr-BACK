<?php

namespace App\Jobs;

use App\Mail\AdminNewOrderMail;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function handle(): void
    {
        $this->order->load('items.variant.product', 'user');

        Mail::to($this->order->shipping_email)
            ->queue(new OrderConfirmationMail($this->order));

        $adminEmail = config('mail.admin_address', 'admin@chatpito.fr');
        Mail::to($adminEmail)
            ->queue(new AdminNewOrderMail($this->order));
    }
}
