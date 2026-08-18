<?php

namespace App\Enums;

enum LeadInterest: string
{
    case Investment = 'investment';
    case UnitRental = 'unit_rental';
    case Warehouses = 'warehouses';
    case Workshops = 'workshops';
    case Showrooms = 'showrooms';
    case Factories = 'factories';
    case Partnership = 'partnership';
    case ContactRequest = 'contact_request';

    public function labelAr(): string
    {
        return match ($this) {
            self::Investment => 'استثمار',
            self::UnitRental => 'تأجير وحدة',
            self::Warehouses => 'مستودعات',
            self::Workshops => 'ورش',
            self::Showrooms => 'معارض',
            self::Factories => 'مصانع',
            self::Partnership => 'شراكة',
            self::ContactRequest => 'طلب تواصل',
        };
    }
}
