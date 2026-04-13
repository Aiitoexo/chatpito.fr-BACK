<?php

namespace App\Jobs;

use App\Mail\StockBackMail;
use App\Models\StockAlert;
use App\Models\Variant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyStockAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $variantId)
    {
    }

    public function handle(): void
    {
        $variant = Variant::with('product')->findOrFail($this->variantId);

        $alerts = StockAlert::where('variant_id', $this->variantId)
            ->where('notified', false)
            ->get();

        foreach ($alerts as $alert) {
            Mail::to($alert->email)->queue(new StockBackMail($variant));
            $alert->update(['notified' => true, 'notified_at' => now()]);
        }
    }
}
