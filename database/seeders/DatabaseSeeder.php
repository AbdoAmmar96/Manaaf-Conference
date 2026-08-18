<?php

namespace Database\Seeders;

use App\Models\Guest;
use App\Models\Setting;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── حسابات الموظفين ──────────────────────────────────────────────
        User::updateOrCreate(['email' => 'admin@manafi.sa'], [
            'name' => 'مدير النظام',
            'password' => 'ChangeMe@2026',
            'role' => 'admin',
        ]);

        User::updateOrCreate(['email' => 'reception@manafi.sa'], [
            'name' => 'موظف الاستقبال',
            'password' => 'ChangeMe@2026',
            'role' => 'reception',
        ]);

        User::updateOrCreate(['email' => 'sales@manafi.sa'], [
            'name' => 'موظف المبيعات',
            'password' => 'ChangeMe@2026',
            'role' => 'sales',
        ]);

        // ── إعدادات الحفل ────────────────────────────────────────────────
        Setting::set('event_name', 'حفل افتتاح مدينة منافع الصناعية المتكاملة');
        Setting::set('event_date', '2026-10-15 19:00:00');
        Setting::set('event_venue', 'مدينة منافع — الطائف، المملكة العربية السعودية');
        Setting::set('investor_qr_token', Str::random(32));

        // ── بيانات التواصل (تظهر في صفحة «تواصل معنا») ────────────────────
        // اتركها فارغة ولن تظهر في الصفحة — املأها ببيانات منافع الفعلية
        Setting::set('contact_website', 'manafi.sa');
        Setting::set('contact_phone', '');
        Setting::set('contact_whatsapp', '');
        Setting::set('contact_email', '');
        Setting::set('contact_hours', 'الأحد – الخميس · ٩:٠٠ صباحًا – ٥:٠٠ مساءً');
        Setting::set('contact_map_url', '');

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
