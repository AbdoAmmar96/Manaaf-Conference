@extends('layouts.public')

@section('title', 'بطاقة الدخول — ' . $guest->name)

@section('content')
<div class="narrow-page">
  <div class="panel fade-up">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <h1>بطاقة دخولك للحفل</h1>
    <p class="sub">قدّم هذا الكود عند بوابة الدخول ليتم تسجيل حضورك</p>

    <div style="margin-bottom:.6rem">
      @if($guest->guest_type === \App\Enums\GuestType::Vip)
        <span class="badge badge-vip">VIP</span>
      @else
        <span class="badge badge-green">{{ $guest->guest_type->labelAr() }}</span>
      @endif
    </div>

    <h2 style="color:var(--ink);font-size:1.1rem">{{ $guest->name }}</h2>
    @if($guest->organization)
      <p style="color:var(--muted);font-size:.85rem">{{ $guest->organization }} @if($guest->position) — {{ $guest->position }} @endif</p>
    @endif

    <div class="qr-frame">
      <img src="{{ route('guest.qr.image', ['token' => $guest->qr_token]) }}" alt="QR الدخول">
    </div>

    <p style="font-size:.8rem;color:var(--muted)">
      هذا الكود شخصي ويُستخدم لتسجيل الدخول مرة واحدة فقط.<br>
      احتفظ بهذه الصفحة أو خذ لقطة شاشة للكود.
    </p>
  </div>
</div>
@endsection
