<?php

namespace App\Http\Controllers;

use App\Enums\ApprovalStatus;
use App\Models\Guest;
use App\Models\Interest;
use App\Models\Lead;
use App\Models\Message;
use App\Models\Setting;
use App\Models\Zone;
use App\Support\Qr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function home(): View
    {
        return view('public.home', [
            'eventName' => Setting::get('event_name', 'حفل افتتاح مدينة منافع'),
            'eventDate' => Setting::get('event_date'),
            'eventVenue' => Setting::get('event_venue', 'الطائف — المملكة العربية السعودية'),
        ]);
    }

    /* ─────────────── تسجيل الحضور (الحجز الذاتي) ─────────────── */

    public function register(): View
    {
        return view('public.register');
    }

    public function registerStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'organization' => ['nullable', 'string', 'max:190'],
            'position' => ['nullable', 'string', 'max:190'],
            'mobile' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:190'],
            'guest_type' => ['required', Rule::in(['guest', 'investor', 'media', 'official', 'other'])],
        ]);

        if (Guest::where('mobile', $data['mobile'])->exists()) {
            return back()->withInput()->withErrors([
                'mobile' => 'يوجد طلب مسجّل بهذا الرقم بالفعل. إن لم تصلك بطاقة الدعوة بعد فتواصل معنا.',
            ]);
        }

        /*
         * التسجيل الذاتي صار «طلب حضور» لا حجزًا مؤكدًا: لا تُصدر بطاقة QR
         * هنا، بل يراجع الموظف الطلب ويعتمده ثم يرسل بطاقة الدعوة بنفسه.
         */
        Guest::create($data + [
            'approval_status' => ApprovalStatus::Pending,
            'rsvp_status' => 'pending',
            'registered_via' => 'self',
        ]);

        return redirect()
            ->route('register')
            ->with('success', 'تم استلام طلبكم بنجاح — سيراجعه فريق التنظيم، وعند اعتماده تصلكم بطاقة الدعوة على الواتساب أو البريد الإلكتروني.');
    }

    /* ─────────────── دعوة / تأكيد الحضور RSVP ─────────────── */

    public function rsvp(string $token): View
    {
        $guest = Guest::where('invite_token', $token)->firstOrFail();

        return view('public.rsvp', [
            'guest' => $guest,
            'eventName' => Setting::get('event_name'),
            'eventDate' => Setting::get('event_date'),
            'eventVenue' => Setting::get('event_venue'),
        ]);
    }

    public function rsvpRespond(Request $request, string $token): RedirectResponse
    {
        $guest = Guest::where('invite_token', $token)->firstOrFail();

        $action = $request->validate([
            'action' => ['required', Rule::in(['confirm', 'decline'])],
        ])['action'];

        if ($action === 'confirm') {
            $guest->update(['rsvp_status' => 'confirmed', 'rsvp_at' => now()]);

            return redirect()
                ->route('guest.qr', ['token' => $guest->qr_token])
                ->with('success', 'شكرًا لتأكيد حضوركم! هذه بطاقة الدخول الخاصة بكم.');
        }

        $guest->update(['rsvp_status' => 'declined', 'rsvp_at' => now()]);

        return redirect()->route('rsvp', ['token' => $token]);
    }

    /* ─────────────── بطاقة QR الضيف ─────────────── */

    public function myQr(string $token): View
    {
        $guest = Guest::where('qr_token', $token)->firstOrFail();

        // بطاقة الدخول لا تُعرض إلا بعد اعتماد الطلب من فريق التنظيم
        if (! $guest->isApproved()) {
            return view('public.request-status', ['guest' => $guest]);
        }

        return view('public.my-qr', ['guest' => $guest]);
    }

    public function qrImage(string $token): Response
    {
        $guest = Guest::where('qr_token', $token)->where('approval_status', ApprovalStatus::Approved)->firstOrFail();

        return response(Qr::png($guest->qrUrl()), 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /* ─────────────── المستثمرون + الطلب الاستثماري ─────────────── */

    public function investors(): View
    {
        return view('public.investors', [
            'zones' => Zone::where('active', true)->get(),
            'interests' => Interest::active()->orderBy('sort')->get(),
        ]);
    }

    public function investorQrImage(): Response
    {
        $url = route('investor.request', ['src' => 'qr']);

        return response(Qr::png($url), 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    public function investorRequest(Request $request): View
    {
        return view('public.investor-request', [
            'zones' => Zone::where('active', true)->orderBy('name')->get(),
            'interests' => Interest::active()->orderBy('sort')->get(),
            'fromQr' => $request->query('src') === 'qr',
        ]);
    }

    public function investorRequestStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'company' => ['nullable', 'string', 'max:190'],
            'mobile' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:190'],
            'interest' => ['nullable', Rule::exists('interests', 'slug')->where('active', true)],
            'zone_id' => ['nullable', Rule::exists('zones', 'id')->where('active', true)],
            'body' => ['required', 'string', 'max:3000'],
        ]);

        $subject = 'طلب استثماري';
        if (! empty($data['interest'])) {
            $subject .= ' — '.Interest::where('slug', $data['interest'])->value('name');
        }

        Message::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'mobile' => $data['mobile'],
            'subject' => $subject,
            'body' => (! empty($data['company']) ? 'الشركة/الجهة: '.$data['company']."\n\n" : '').$data['body'],
            'zone_id' => $data['zone_id'] ?? null,
            'category' => 'investor',
            'source' => $request->input('src') === 'qr' ? 'investor_qr' : 'contact_form',
        ]);

        return back()
            ->withFragment('investor-request')
            ->with('success', 'تم استلام طلبكم بنجاح — سيتواصل معكم فريق الاستثمار في أقرب وقت.');
    }

    /* ─────────────── تواصل معنا ─────────────── */

    public function contact(): View
    {
        return view('public.contact', [
            'zones' => Zone::where('active', true)->orderBy('name')->get(),
            'eventVenue' => Setting::get('event_venue'),
            'contact' => [
                'phone' => Setting::get('contact_phone'),
                'whatsapp' => Setting::get('contact_whatsapp'),
                'email' => Setting::get('contact_email'),
                'website' => Setting::get('contact_website'),
                'map' => Setting::get('contact_map_url'),
            ],
        ]);
    }

    public function contactStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'mobile' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:190'],
            'subject' => ['nullable', 'string', 'max:190'],
            'zone_id' => ['nullable', Rule::exists('zones', 'id')->where('active', true)],
            'body' => ['required', 'string', 'max:3000'],
        ]);

        Message::create($data + ['category' => 'general', 'source' => 'contact_form']);

        return back()->with('success', 'تم إرسال رسالتكم بنجاح — سنعود إليكم في أقرب وقت.');
    }

    /* ─────────────── المناطق في الواجهة العامة ─────────────── */

    public function zones(): View
    {
        return view('public.zones', [
            'zones' => Zone::where('active', true)
                ->withCount('leads')
                ->with(['projects' => fn ($q) => $q->where('active', true)->orderBy('sort')])
                ->orderBy('name')->get(),
        ]);
    }

    /** صورة QR الخاصة بمنطقة — عامة ليمكن عرضها وطباعتها من الموقع */
    public function zoneQrImage(string $slug): Response
    {
        $zone = Zone::where('slug', $slug)->where('active', true)->firstOrFail();

        return response(Qr::png(route('zone', ['slug' => $zone->slug])), 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    /* ─────────────── اهتمام المناطق (QR لكل ماكيت) ─────────────── */

    public function zone(string $slug): View
    {
        $zone = Zone::where('slug', $slug)->where('active', true)->firstOrFail();

        return view('public.zone', [
            'zone' => $zone,
            'interests' => Interest::active()->orderBy('sort')->get(),
        ]);
    }

    public function zoneStore(Request $request, string $slug): RedirectResponse
    {
        $zone = Zone::where('slug', $slug)->where('active', true)->firstOrFail();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'mobile' => ['required', 'string', 'max:30'],
            'interests' => ['required', 'array', 'min:1'],
            'interests.*' => [Rule::exists('interests', 'slug')->where('active', true)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $guest = Guest::where('mobile', $data['mobile'])->first();

        Lead::create([
            'guest_id' => $guest?->id,
            'walk_in_name' => $guest ? null : $data['name'],
            'walk_in_mobile' => $guest ? null : $data['mobile'],
            'interests' => $data['interests'],
            'score' => 'warm',
            'notes' => $data['notes'] ?? null,
            'source' => 'zone_qr',
            'zone_id' => $zone->id,
        ]);

        return back()->with('success', 'تم تسجيل اهتمامكم بنجاح — سيتواصل معكم فريق المبيعات قريبًا.');
    }
}
