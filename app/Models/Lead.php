<?php

namespace App\Models;

use App\Enums\LeadScore;
use App\Enums\LeadSource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    protected $fillable = [
        'guest_id', 'walk_in_name', 'walk_in_mobile', 'interests', 'score',
        'notes', 'assigned_to', 'follow_up_at', 'source', 'zone_id', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'interests' => 'array',
            'score' => LeadScore::class,
            'source' => LeadSource::class,
            'follow_up_at' => 'datetime',
        ];
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function displayName(): string
    {
        return $this->guest?->name ?? $this->walk_in_name ?? 'غير معروف';
    }
}
