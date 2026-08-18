<?php

namespace App\Enums;

enum GuestType: string
{
    case Vip = 'vip';
    case Guest = 'guest';
    case Investor = 'investor';
    case Media = 'media';
    case Official = 'official';
    case Other = 'other';

    public function labelAr(): string
    {
        return match ($this) {
            self::Vip => 'VIP',
            self::Guest => 'ضيف',
            self::Investor => 'مستثمر',
            self::Media => 'إعلام',
            self::Official => 'جهة رسمية',
            self::Other => 'أخرى',
        };
    }
}
