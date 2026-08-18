@extends('layouts.admin')

@section('title', 'تسجيل دخول ضيف')

@section('content')
<h1 class="page-title">تسجيل دخول ضيف</h1>

<div style="max-width:560px">

  @if(! $guest)
    <div class="big-result err">
      <h3>كود غير صحيح</h3>
      <p>هذا الكود غير مرتبط بأي ضيف مسجل — جرّب البحث بالاسم من صفحة الماسح.</p>
    </div>

  @elseif(session('just_checked_in'))
    <div class="big-result ok">
      <h3>✓ تم تسجيل الدخول</h3>
      <p style="font-size:1.15rem"><b>{{ $guest->name }}</b>
        @if($guest->guest_type === \App\Enums\GuestType::Vip) <span class="badge badge-vip">VIP</span> @endif
      </p>
      <p>الساعة {{ $guest->checked_in_at?->format('h:i A') }}</p>
    </div>

  @elseif($guest->attendance_status === \App\Enums\AttendanceStatus::Cancelled)
    <div class="big-result err"><h3>الدعوة ملغاة</h3><p>{{ $guest->name }}</p></div>

  @elseif($guest->isCheckedIn())
    <div class="big-result err">
      <h3>⚠ تم تسجيل الدخول مسبقًا</h3>
      <p style="font-size:1.05rem"><b>{{ $guest->name }}</b></p>
      <p>الساعة {{ $guest->checked_in_at?->format('h:i A') }}
        @if($guest->checkedInBy) — بواسطة {{ $guest->checkedInBy->name }} @endif
      </p>
    </div>

  @else
    <div class="table-wrap" style="padding:1.8rem;text-align:center">
      @if($guest->guest_type === \App\Enums\GuestType::Vip)
        <span class="badge badge-vip" style="font-size:.95rem;padding:.4rem 1.2rem">VIP</span>
      @else
        <span class="badge badge-green">{{ $guest->guest_type->labelAr() }}</span>
      @endif
      <h2 style="margin:.7rem 0 .2rem;color:var(--ink)">{{ $guest->name }}</h2>
      @if($guest->organization)
        <p style="color:var(--muted);font-size:.88rem">{{ $guest->organization }}@if($guest->position) — {{ $guest->position }}@endif</p>
      @endif
      <p style="color:var(--muted);font-size:.85rem;margin:.3rem 0 1.3rem">{{ $guest->mobile }} · {{ $guest->rsvp_status->labelAr() }}</p>
      <form method="POST" action="{{ route('checkin.scan.confirm', ['token' => $guest->qr_token]) }}">
        @csrf
        <button type="submit" class="btn-checkin" style="font-size:1.05rem;padding:.9rem 2.4rem">تسجيل الدخول الآن</button>
      </form>
    </div>
  @endif

  <p style="margin-top:1.2rem"><a href="{{ route('admin.checkin') }}" class="mini-btn">↩ فتح صفحة الماسح</a></p>
</div>
@endsection
