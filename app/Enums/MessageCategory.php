<?php

namespace App\Enums;

enum MessageCategory: string
{
    case General = 'general';
    case Investor = 'investor';

    public function labelAr(): string
    {
        return match ($this) {
            self::General => 'رسالة عادية',
            self::Investor => 'رسالة مستثمر',
        };
    }
}
