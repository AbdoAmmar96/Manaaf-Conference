@extends('layouts.public')

@section('title', $guest ? 'بطاقة الدخول — ' . $guest->name : 'بطاقة الدخول')

@section('content')

@php
  $valid = $guest
    && $guest->approval_status === \App\Enums\ApprovalStatus::Approved
    && $guest->attendance_status !== \App\Enums\AttendanceStatus::Cancelled;
@endphp

<section class="card-hero {{ $valid ? '' : 'bad' }}">
  <div class="container">

    @if(! $guest)
      <span class="card-state bad">✕ كود غير صالح</span>
      <h1>هذه البطاقة غير معروفة</h1>
      <p>الرمز غير مرتبط بأي دعوة. تأكد من مسح الكود الصحيح، أو
        <a href="{{ route('register') }}">قدّم طلب حضور</a>.</p>

    @elseif($guest->approval_status === \App\Enums\ApprovalStatus::Pending)
      <span class="card-state wait">⏳ بانتظار الموافقة</span>
      <h1>طلبك قيد المراجعة</h1>
      <p>لم تُعتمد الدعوة بعد، فالبطاقة غير صالحة للدخول حتى الآن. سنبلغك على
        الواتساب أو البريد فور اعتمادها.</p>

    @elseif($guest->approval_status === \App\Enums\ApprovalStatus::Rejected)
      <span class="card-state bad">✕ الطلب مرفوض</span>
      <h1>هذه الدعوة غير سارية</h1>
      <p>@if($guest->rejection_reason){{ $guest->rejection_reason }}@else للاستفسار تواصل مع فريق التنظيم.@endif</p>

    @elseif($guest->attendance_status === \App\Enums\AttendanceStatus::Cancelled)
      <span class="card-state bad">✕ الدعوة ملغاة</span>
      <h1>هذه الدعوة ملغاة</h1>
      <p>للاستفسار تواصل مع فريق التنظيم.</p>

    @elseif($guest->isCheckedIn())
      <span class="card-state ok">✓ تم تسجيل دخولك</span>
      <h1>أهلًا بك في {{ $eventName }}</h1>
      <p>سُجّل حضورك الساعة {{ $guest->checked_in_at?->format('h:i A') }} — هذه البطاقة
        استُخدمت ولا تصلح للدخول مرة أخرى.</p>

    @else
      <span class="card-state ok">✓ البطاقة فعّالة</span>
      <h1>بطاقتك جاهزة للدخول</h1>
      <p>هذه بطاقة دخول سارية لحفل {{ $eventName }} — يمسحها موظف البوابة عند
        وصولك ليُسجّل حضورك.</p>
    @endif

  </div>
</section>

@if($guest)
  <div class="card-status-page">
    <div class="panel fade-up">

      <div style="margin-bottom:.7rem">
        @if($guest->guest_type === \App\Enums\GuestType::Vip)
          <span class="badge badge-vip">VIP</span>
        @else
          <span class="badge badge-green">{{ $guest->guest_type->labelAr() }}</span>
        @endif
      </div>

      <h2 style="color:var(--ink);font-size:1.15rem">{{ $guest->name }}</h2>
      @if($guest->organization)
        <p style="color:var(--muted);font-size:.85rem">{{ $guest->organization }}@if($guest->position) — {{ $guest->position }}@endif</p>
      @endif

      @if($valid && ! $guest->isCheckedIn())
        <p class="card-hint">
          احتفظ ببطاقتك — تجدها دائمًا على
          <a href="{{ route('guest.qr', ['token' => $guest->qr_token]) }}">هذا الرابط</a>.
        </p>
      @endif

      @if($eventVenue)
        <p class="card-venue">📍 {{ $eventVenue }}</p>
      @endif
    </div>
  </div>

  @if($valid && ! $guest->isCheckedIn() && $eventDate)
    <section class="card-countdown">
      <div class="container">
        <span class="eyebrow-light">يبدأ الحفل بعد</span>
        <div class="countdown" id="countdown" data-date="{{ $eventDate }}">
          <div class="unit"><b id="cd-days">—</b><small>يوم</small></div>
          <div class="unit"><b id="cd-hours">—</b><small>ساعة</small></div>
          <div class="unit"><b id="cd-mins">—</b><small>دقيقة</small></div>
          <div class="unit"><b id="cd-secs">—</b><small>ثانية</small></div>
        </div>
        <p class="card-started" id="cd-started" hidden>بدأ الحفل — نراك عند البوابة.</p>
      </div>
    </section>
  @endif
@endif

@endsection

@section('scripts')
<script>
(function () {
  var el = document.getElementById('countdown');
  if (!el) return;
  var target = new Date(el.dataset.date.replace(' ', 'T')).getTime();
  var started = document.getElementById('cd-started');
  function pad(n) { return n < 10 ? '0' + n : n; }
  function tick() {
    var diff = target - Date.now();
    if (diff <= 0) {
      diff = 0;
      el.hidden = true;
      if (started) started.hidden = false;
    }
    document.getElementById('cd-days').textContent  = Math.floor(diff / 86400000);
    document.getElementById('cd-hours').textContent = pad(Math.floor(diff / 3600000) % 24);
    document.getElementById('cd-mins').textContent  = pad(Math.floor(diff / 60000) % 60);
    document.getElementById('cd-secs').textContent  = pad(Math.floor(diff / 1000) % 60);
  }
  tick();
  setInterval(tick, 1000);
})();
</script>
@endsection
