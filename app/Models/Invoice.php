<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'order_id', 'invoice_number', 'pdf_path',
        'subtotal_ht', 'tax_amount', 'total_ttc',
        'status', 'issued_at',
    ];

    protected $casts = [
        'subtotal_ht' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_ttc' => 'decimal:2',
        'issued_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public static function generateNumber(): string
    {
        $year = now()->year;
        $count = self::whereYear('issued_at', $year)->count() + 1;

        return sprintf('FAC-%d-%04d', $year, $count);
    }
}
