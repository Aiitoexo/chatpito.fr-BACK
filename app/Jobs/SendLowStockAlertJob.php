<?php

namespace App\Jobs;

use App\Mail\LowStockAlertMail;
use App\Models\Stock;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class SendLowStockAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Stock $stock)
    {
    }

    public function handle(): void
    {
        $cacheKey = "low_stock_alert_{$this->stock->variant_id}";

        if (Cache::has($cacheKey)) {
            return;
        }

        $this->stock->load('variant.product');

        Mail::to(config('mail.admin_address'))
            ->queue(new LowStockAlertMail($this->stock));

        Cache::put($cacheKey, true, now()->addHours(24));
    }
}
