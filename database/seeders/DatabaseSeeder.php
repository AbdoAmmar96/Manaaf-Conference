<?php

namespace Database\Seeders;

use App\Models\Guest;
use App\Enums\LeadInterest;
use App\Models\Interest;
use App\Models\Setting;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * ── حسابات الموظفين ───────────────────────────────────────────────
         * firstOrCreate لا updateOrCreate: البذرة تُنشئ الحساب إن غاب ولا
         * تمسّه إن وُجد. مع updateOrCreate كانت إعادة تشغيل البذرة على بيئة
         * التشغيل تُعيد كلمات المرور إلى القيمة الافتراضية المعروفة.
         *
         * كلمة المرور هنا للتهيئة المحلية فقط — تُضبط عبر SEED_PASSWORD في
         * .env، وكلمات مرور بيئة التشغيل تُغيَّر من شاشة المستخدمين.
         */
        $seedPassword = env('SEED_PASSWORD', 'ChangeMe@2026');

        $staff = [
            ['email' => 'admin@manafi.sa', 'name' => 'مدير النظام', 'role' => 'admin'],
            ['email' => 'reception@manafi.sa', 'name' => 'موظف الاستقبال', 'role' => 'reception'],
            ['email' => 'sales@manafi.sa', 'name' => 'موظف المبيعات', 'role' => 'sales'],
        ];

        foreach ($staff as $member) {
            User::firstOrCreate(
                ['email' => $member['email']],
                ['name' => $member['name'], 'role' => $member['role'], 'password' => $seedPassword],
            );
        }

        // ── إعدادات الحفل ────────────────────────────────────────────────
        Setting::set('event_name', 'حفل افتتاح مدينة منافع الحرفية المتكاملة');
        Setting::set('event_date', '2026-10-15 19:00:00');
        Setting::set('event_venue', 'مدينة منافع — الطائف، المملكة العربية السعودية');
        Setting::set('investor_qr_token', Str::random(32));

        // ── بيانات التواصل (تظهر في صفحة «تواصل معنا») ────────────────────
        // اتركها فارغة ولن تظهر في الصفحة — املأها ببيانات منافع الفعلية
        Setting::set('contact_website', 'manafi.sa');
        Setting::set('contact_phone', '');
        Setting::set('contact_whatsapp', '');
        Setting::set('contact_email', '');
        Setting::set('contact_map_url', '');

        // ── مجالات الاهتمام ──────────────────────────────────────────────
        // الـslug يطابق قيم enum القديم حرفيًا، فبيانات leads.interests
        // المخزّنة سابقًا تبقى صالحة دون أي ترحيل
        foreach (LeadInterest::cases() as $i => $case) {
            Interest::updateOrCreate(
                ['slug' => $case->value],
                ['name' => $case->labelAr(), 'sort' => $i, 'active' => true]
            );
        }

        // ── مناطق المعرض / الماكيتات (بأسماء القطاعات الستة) ────────────
        $zones = [
            ['name' => 'الورش الحرفية والصناعية', 'slug' => 'workshops'],
            ['name' => 'المركبات الخفيفة', 'slug' => 'light-vehicles'],
            ['name' => 'المركبات والمعدات الثقيلة', 'slug' => 'heavy-vehicles'],
            ['name' => 'معارض المركبات', 'slug' => 'showrooms'],
            ['name' => 'قطع الغيار والتجارة المتخصصة', 'slug' => 'parts'],
            ['name' => 'المستودعات والخدمات اللوجستية', 'slug' => 'logistics'],
        ];

        foreach ($zones as $zone) {
            Zone::updateOrCreate(['slug' => $zone['slug']], ['name' => $zone['name']]);
        }

        // ── ضيوف تجريبيون (للتجربة — يحذفون قبل التشغيل الفعلي) ─────────
        if (Guest::count() === 0) {
            Guest::create([
                'name' => 'ضيف تجريبي — VIP',
                'organization' => 'شركة البوابة الغربية',
                'position' => 'الرئيس التنفيذي',
                'mobile' => '0500000001',
                'email' => 'vip@example.com',
                'guest_type' => 'vip',
                'rsvp_status' => 'confirmed',
            ]);

            Guest::create([
                'name' => 'ضيف تجريبي — مستثمر',
                'organization' => 'مجموعة استثمارية',
                'position' => 'مدير الاستثمار',
                'mobile' => '0500000002',
                'guest_type' => 'investor',
            ]);

            Guest::create([
                'name' => 'ضيف تجريبي — عام',
                'mobile' => '0500000003',
                'guest_type' => 'guest',
            ]);
        }
    }
}
