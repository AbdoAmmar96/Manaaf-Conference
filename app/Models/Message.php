<?php

namespace App\Models;

use App\Enums\MessageCategory;
use App\Enums\MessageStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    protected $fillable = [
        'name', 'email', 'mobile', 'subject', 'body',
        'category', 'status', 'source', 'zone_id', 'assigned_to',
    ];

    protected function casts(): array
    {
        return [
            'category' => MessageCategory::class,
            'status' => MessageStatus::class,
        ];
    }

    public function comments(): HasMany
    {
        return $this->hasMany(MessageComment::class)->latest();
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }
}
