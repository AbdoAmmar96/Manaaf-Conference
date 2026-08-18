<?php

namespace App\Enums;

enum RsvpStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Declined = 'declined';

    public function labelAr(): string
    {
        return match ($this) {
            self::Pending => 'بانتظار الرد',
            self::Confirmed => 'مؤكد الحضور',
            self::Declined => 'معتذر',
        };
    }
}
