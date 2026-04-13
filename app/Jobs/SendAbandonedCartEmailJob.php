<?php

namespace App\Jobs;

use App\Mail\AbandonedCartMail;
use App\Models\AbandonedCart;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendAbandonedCartEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        AbandonedCart::where('recovered', false)
            ->whereNull('email_sent_at')
            ->where('created_at', '<', now()->subHour())
            ->where('created_at', '>', now()->subDay())
            ->each(function (AbandonedCart $cart) {
                $hasOrdered = Order::where('shipping_email', $cart->email)
                    ->where('created_at', '>', $cart->created_at)
                    ->exists();

                if ($hasOrdered) {
                    $cart->update(['recovered' => true]);
                    return;
                }

                Mail::to($cart->email)->queue(new AbandonedCartMail($cart));
                $cart->update(['email_sent_at' => now()]);
            });
    }
}
