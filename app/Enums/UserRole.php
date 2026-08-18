<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Reception = 'reception';
    case Sales = 'sales';

    public function labelAr(): string
    {
        return match ($this) {
            self::Admin => 'مدير النظام',
            self::Reception => 'استقبال',
            self::Sales => 'مبيعات',
        };
    }
}
