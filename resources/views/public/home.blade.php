@extends('layouts.public')

@section('title', 'حفل الافتتاح')

@section('content')

<section class="hero">
  <div class="container">
    <span class="kicker fade-up">بدعوة كريمة من شركة البوابة الغربية للمقاولات العامة</span>
    <h1 class="fade-up d1">{{ $eventName }}</h1>
    <p class="lead fade-up d2">
      يشرفنا حضوركم فعاليات المؤتمر وحفل الافتتاح — منظومة متكاملة للخدمات
      الحرفية والصناعية وخدمات المركبات شمال وجنوب الطائف.
    </p>

    @if($eventDate)
      <div class="countdown fade-up d2" id="countdown" data-date="{{ $eventDate }}">
        <div class="unit"><b id="cd-days">—</b><small>يوم</small></div>
        <div class="unit"><b id="cd-hours">—</b><small>ساعة</small></div>
        <div class="unit"><b id="cd-mins">—</b><small>دقيقة</small></div>
        <div class="unit"><b id="cd-secs">—</b><small>ثانية</small></div>
      </div>
    @endif

    <div class="actions fade-up d3">
      <a href="{{ route('register') }}" class="btn btn-lime">سجّل حضورك</a>
      <a href="{{ route('investors') }}" class="btn btn-outline">فرص المستثمرين</a>
    </div>

    <p style="margin-top:2rem;font-size:.85rem;color:rgba(255,255,255,.65)" class="fade-up d3">
      {{ $eventVenue }}
    </p>
  </div>
</section>

<div class="pattern-strip"></div>

<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">عن الحدث</span>
      <h2>مؤتمرٌ يفتتح مرحلة جديدة للطائف</h2>
      <p>
        تفاصيل الأجندة والمتحدثين وخريطة المعرض تُضاف في المرحلة القادمة من بناء الموقع —
        هذه الصفحة أساس الثيم والهوية.
      </p>
    </div>
    <div class="cards-grid">
      <div class="card">
        <h3>حفل الافتتاح</h3>
        <p>فقرات الحفل الرسمية وجولة الماكيتات والمناطق التصميمية.</p>
      </div>
      <div class="card">
        <h3>جناح المستثمرين</h3>
        <p>فريق مبيعات متكامل لعرض الفرص وتسجيل الاهتمامات مباشرة.</p>
      </div>
      <div class="card">
        <h3>ست مناطق عرض</h3>
        <p>منطقة لكل قطاع من قطاعات المدينة، ولكل منطقة كود QR خاص.</p>
      </div>
    </div>
  </div>
</section>
@endsection

@section('scripts')
<script>
(function () {
  var el = document.getElementById('countdown');
  if (!el) return;
  var target = new Date(el.dataset.date.replace(' ', 'T')).getTime();
  function pad(n) { return n < 10 ? '0' + n : n; }
  function tick() {
    var diff = target - Date.now();
    if (diff < 0) diff = 0;
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
