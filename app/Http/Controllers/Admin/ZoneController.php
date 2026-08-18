<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use App\Support\Qr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ZoneController extends Controller
{
    public function index(): View
    {
        return view('admin.zones.index', [
            'zones' => Zone::withCount('leads')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'slug' => ['nullable', 'string', 'max:190', 'alpha_dash', 'unique:zones,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        // الحقل قد لا يُرسَل إطلاقًا، فلا يوجد مفتاحه في نتيجة التحقق
        $data['slug'] = ($data['slug'] ?? null) ?: Str::slug($data['name']);

        // أسماء لا ينتج عنها معرّف صالح (رموز فقط مثلًا) — نولّد بديلًا فريدًا
        if ($data['slug'] === '' || Zone::where('slug', $data['slug'])->exists()) {
            $data['slug'] = 'zone-'.Str::lower(Str::random(6));
        }

        Zone::create($data + ['active' => true]);

        return back()->with('success', 'تمت إضافة المنطقة بنجاح.');
    }

    public function update(Request $request, Zone $zone): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'slug' => ['required', 'string', 'max:190', 'alpha_dash', Rule::unique('zones', 'slug')->ignore($zone->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'active' => ['nullable', 'boolean'],
        ]);

        $zone->update($data + ['active' => $request->boolean('active')]);

        return back()->with('success', 'تم تحديث المنطقة.');
    }

    /** صورة QR الخاصة بالمنطقة — تُطبع وتوضع على الماكيت */
    public function qrImage(Zone $zone): Response
    {
        return response(Qr::png(route('zone', ['slug' => $zone->slug])), 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
