<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    public $timestamps = false;

    protected $fillable = ['type', 'title', 'message', 'link', 'read', 'created_at'];

    protected $casts = [
        'read' => 'boolean',
        'created_at' => 'datetime',
    ];

    public static function notify(string $type, string $title, ?string $message = null, ?string $link = null): self
    {
        return self::create(compact('type', 'title', 'message', 'link'));
    }
}
