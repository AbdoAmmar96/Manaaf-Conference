<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use App\Enums\GuestType;
use App\Enums\RsvpStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Guest extends Model
{
    protected $fillable = [
        'name', 'organization', 'position', 'mobile', 'email',
        'guest_type', 'rsvp_status', 'attendance_status', 'rsvp_at',
        'checked_in_at', 'checked_in_by', 'created_by', 'registered_via', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'guest_type' => GuestType::class,
            'rsvp_status' => RsvpStatus::class,
            'attendance_status' => AttendanceStatus::class,
            'rsvp_at' => 'datetime',
            'checked_in_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Guest $guest) {
            $guest->invite_token ??= Str::random(32);
            $guest->qr_token ??= Str::random(32);
        });
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isCheckedIn(): bool
    {
        return $this->attendance_status === AttendanceStatus::Attended;
    }

    public function qrUrl(): string
    {
        return route('checkin.scan', ['token' => $this->qr_token]);
    }
}
