<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbandonedCart extends Model
{
    protected $fillable = ['email', 'user_id', 'items', 'total', 'recovered', 'email_sent_at'];

    protected $casts = [
        'items' => 'array',
        'total' => 'decimal:2',
        'recovered' => 'boolean',
        'email_sent_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
