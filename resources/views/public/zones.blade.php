@extends('layouts.public')

@section('title', 'مناطق المعرض')

@section('content')
<section class="page-hero">
  <div class="container">
    <h1>مناطق المعرض</h1>
    <p>ست مناطق متخصصة، لكل منطقة كود QR خاص — امسحه بجوالك لتسجيل اهتمامك بمشاريعها مباشرة</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <p class="page-hint" style="max-width:760px;margin:0 auto 2.4rem;text-align:center">
      امسح كود المنطقة بكاميرا جوالك — سيفتح لك نموذجًا قصيرًا لتسجيل اهتمامك،
      ويتواصل معك فريق المبيعات بعد الحفل. الأكواد نفسها معروضة على الماكيتات في المعرض.
    </p>

    <div class="zone-public-grid">
      @foreach($zones as $zone)
        <div class="zone-public-card fade-up">
          <div class="zone-qr-box">
            <img src="{{ route('zone.qr', $zone->slug) }}" alt="كود {{ $zone->name }}" loading="lazy">
          </div>

          <h3>{{ $zone->name }}</h3>
          <p class="zone-public-desc">
            {{ $zone->description ?? 'منطقة متخصصة ضمن المخطط العام للمدينة بمواقع تأجيرية ومرافق مساندة.' }}
          </p>

          @if($zone->projects->isNotEmpty())
            <div class="zone-projects">
              <span class="zone-projects-title">مشاريع المنطقة</span>
              @foreach($zone->projects as $project)
                <span class="tag">{{ $project->name }}</span>
              @endforeach
            </div>
          @endif

          <div class="zone-public-links">
            <a href="{{ route('zone', $zone->slug) }}" class="btn btn-lime">سجّل اهتمامك</a>
            <a href="{{ route('zone.qr', $zone->slug) }}" download="qr-{{ $zone->slug }}.png" class="mini-btn">تحميل الكود</a>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

<section class="section alt">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">مهتم بالاستثمار؟</span>
      <h2>قدّم طلبك الاستثماري مباشرة</h2>
      <p>اختر مجال اهتمامك وسيتواصل معك فريق الاستثمار لعرض الفرص المناسبة</p>
    </div>
    <div class="section-cta">
      <a href="{{ route('investors') }}#investor-request" class="btn btn-green">طلب استثماري</a>
    </div>
  </div>
</section>
@endsection
