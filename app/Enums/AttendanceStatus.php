<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case Awaited = 'awaited';
    case Attended = 'attended';
    case NoShow = 'no_show';
    case Cancelled = 'cancelled';

    public function labelAr(): string
    {
        return match ($this) {
            self::Awaited => 'لم يصل بعد',
            self::Attended => 'حضر',
            self::NoShow => 'لم يحضر',
            self::Cancelled => 'ملغي',
        };
    }
}
