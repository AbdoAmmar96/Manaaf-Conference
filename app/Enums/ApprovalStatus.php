<?php

namespace App\Enums;

enum ApprovalStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function labelAr(): string
    {
        return match ($this) {
            self::Pending => 'بانتظار الموافقة',
            self::Approved => 'معتمد',
            self::Rejected => 'مرفوض',
        };
    }
}
