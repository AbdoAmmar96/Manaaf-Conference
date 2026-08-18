<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Available = 'available';
    case Reserved = 'reserved';
    case UnderConstruction = 'under_construction';
    case Completed = 'completed';

    public function labelAr(): string
    {
        return match ($this) {
            self::Available => 'متاح للحجز',
            self::Reserved => 'محجوز بالكامل',
            self::UnderConstruction => 'تحت الإنشاء',
            self::Completed => 'مكتمل',
        };
    }
}
