<?php

namespace App\Support;

use App\Enums\UserRole;

/**
 * مرجع واحد لصلاحيات الأدوار — تقرأه صفحة «الصلاحيات» في لوحة التحكم،
 * وهو نفسه المشروح في docs/الصلاحيات.md.
 *
 * ملاحظة: هذا توثيق للعرض فقط. التطبيق الفعلي للصلاحية يتم عبر
 * middleware «role» على المسارات في routes/web.php.
 */
class Permissions
{
    /** @return array<string, array{title: string, description: string, roles: array<string>}> */
    public static function matrix(): array
    {
        return [
            'dashboard' => [
                'title' => 'لوحة المؤشرات اللحظية',
                'description' => 'أعداد المدعوين والمؤكدين والحاضرين وVIP وآخر عمليات الدخول.',
                'roles' => ['admin', 'reception', 'sales'],
            ],
            'guests_view' => [
                'title' => 'عرض قاعدة بيانات الضيوف',
                'description' => 'الاطلاع على بيانات المدعوين وبطاقات الدخول وحالات التأكيد.',
                'roles' => ['admin', 'reception'],
            ],
            'guests_create' => [
                'title' => 'إضافة ضيف وإصدار بطاقة QR',
                'description' => 'تسجيل ضيف جديد من الاستقبال وإرسال بطاقته عبر واتساب.',
                'roles' => ['admin', 'reception'],
            ],
            'checkin' => [
                'title' => 'تسجيل دخول الضيوف',
                'description' => 'مسح رمز QR بالكاميرا أو البحث بالاسم وتأكيد الحضور عند البوابة.',
                'roles' => ['admin', 'reception'],
            ],
            'messages_view' => [
                'title' => 'عرض الرسائل الواردة',
                'description' => 'رسائل نموذج التواصل وطلبات المستثمرين.',
                'roles' => ['admin', 'reception', 'sales'],
            ],
            'messages_follow' => [
                'title' => 'متابعة الرسائل',
                'description' => 'تغيير حالة الرسالة وتعيين موظف مسؤول وكتابة ملاحظات المتابعة.',
                'roles' => ['admin', 'reception', 'sales'],
            ],
            'leads_view' => [
                'title' => 'عرض اهتمامات العملاء',
                'description' => 'الاطلاع على الاهتمامات المسجلة من المناطق ومن فريق المبيعات.',
                'roles' => ['admin', 'reception', 'sales'],
            ],
            'leads_manage' => [
                'title' => 'تسجيل وتعديل الاهتمامات',
                'description' => 'إضافة اهتمام جديد وتحديد الدرجة والملاحظات والموظف المسؤول وموعد المتابعة.',
                'roles' => ['admin', 'reception', 'sales'],
            ],
            'zones' => [
                'title' => 'إدارة المناطق والماكيتات',
                'description' => 'إضافة وتعديل مناطق العرض وطباعة رمز QR الخاص بكل منطقة.',
                'roles' => ['admin'],
            ],
            'reports' => [
                'title' => 'التقارير والتصدير',
                'description' => 'التقارير التجميعية وتصدير الضيوف والاهتمامات والرسائل بصيغة Excel/CSV.',
                'roles' => ['admin'],
            ],
            'users' => [
                'title' => 'المستخدمون والصلاحيات',
                'description' => 'إضافة موظفين وتغيير أدوارهم وتفعيل أو تعطيل حساباتهم.',
                'roles' => ['admin'],
            ],
        ];
    }

    /** @return array<string, string> */
    public static function roleSummaries(): array
    {
        return [
            UserRole::Admin->value => 'صلاحية كاملة على النظام — كل الشاشات والتقارير وإدارة المستخدمين.',
            UserRole::Reception->value => 'الاستقبال والبوابة — الضيوف وتسجيل الدخول ومتابعة الرسائل وكتابة الملاحظات.',
            UserRole::Sales->value => 'فريق المبيعات — اهتمامات العملاء ومتابعتها والرسائل، دون الوصول لبيانات الضيوف أو الإعدادات.',
        ];
    }
}
