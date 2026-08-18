<?php

namespace App\Enums;

enum MessageStatus: string
{
    case New = 'new';
    case InReview = 'in_review';
    case InProgress = 'in_progress';
    case Closed = 'closed';

    public function labelAr(): string
    {
        return match ($this) {
            self::New => 'جديدة',
            self::InReview => 'قيد المراجعة',
            self::InProgress => 'جاري التنفيذ',
            self::Closed => 'مغلقة',
        };
    }
}
