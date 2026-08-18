<?php

namespace App\Enums;

enum LeadSource: string
{
    case Sales = 'sales';
    case ZoneQr = 'zone_qr';
    case InvestorQr = 'investor_qr';

    public function labelAr(): string
    {
        return match ($this) {
            self::Sales => 'موظف مبيعات',
            self::ZoneQr => 'QR منطقة',
            self::InvestorQr => 'QR المستثمرين',
        };
    }
}
