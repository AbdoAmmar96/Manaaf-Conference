<?php

namespace App\Enums;

enum LeadScore: string
{
    case Hot = 'hot';
    case Warm = 'warm';
    case Cold = 'cold';

    public function labelAr(): string
    {
        return match ($this) {
            self::Hot => 'ساخن',
            self::Warm => 'متوسط',
            self::Cold => 'بارد',
        };
    }
}
