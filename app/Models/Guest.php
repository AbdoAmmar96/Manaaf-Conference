<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
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
        'guest_type', 'approval_status', 'approved_at', 'approved_by', 'rejection_reason',
        'card_sent_at', 'card_sent_via',
        'rsvp_status', 'attendance_status', 'rsvp_at',
        'checked_in_at', 'checked_in_by', 'created_by', 'registered_via', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'guest_type' => GuestType::class,
            'approval_status' => ApprovalStatus::class,
            'approved_at' => 'datetime',
            'card_sent_at' => 'datetime',
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

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isApproved(): bool
    {
        return $this->approval_status === ApprovalStatus::Approved;
    }

    public function scopePending($query)
    {
        return $query->where('approval_status', ApprovalStatus::Pending);
    }

    /** رابط بطاقة الدعوة الذي يُرسل للضيف بعد اعتماد طلبه */
    public function cardUrl(): string
    {
        return route('guest.qr', ['token' => $this->qr_token]);
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
