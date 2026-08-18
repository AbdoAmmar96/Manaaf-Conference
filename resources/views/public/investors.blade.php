@extends('layouts.public')

@section('title', 'المستثمرون')

@section('content')
<section class="page-hero">
  <div class="container">
    <h1>الفرص الاستثمارية</h1>
    <p>منظومة متكاملة للخدمات الحرفية والصناعية على مساحة تتجاوز 2.5 مليون متر مربع شمال وجنوب الطائف — بعقود طويلة الأجل وبيئة أعمال جاهزة</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="stats-band fade-up">
      <div class="stat-pub"><b>+2.53</b><span>مليون م² المساحة الإجمالية</span></div>
      <div class="stat-pub"><b>3,263</b><span>موقعًا تأجيريًا</span></div>
      <div class="stat-pub"><b>6,119</b><span>فرصة عمل متوقعة</span></div>
      <div class="stat-pub"><b>6</b><span>قطاعات متكاملة</span></div>
    </div>
  </div>
</section>

<section class="section alt">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">قطاعات المدينة</span>
      <h2>ست مناطق متخصصة تتكامل في منظومة واحدة</h2>
    </div>
    <div class="cards-grid">
      @foreach($zones as $zone)
        <div class="card">
          <h3>{{ $zone->name }}</h3>
          <p>{{ $zone->description ?? 'منطقة متخصصة ضمن المخطط العام للمدينة بمواقع تأجيرية ومرافق مساندة.' }}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">قدّم طلبك</span>
      <h2>طلب استثماري مباشر</h2>
    </div>
    <div class="qr-cta fade-up">
      <div class="qr-frame" style="margin:0">
        <img src="{{ route('investor.qr.image') }}" alt="QR طلبات المستثمرين">
      </div>
      <div class="txt">
        <h3>امسح الكود بجوالك</h3>
        <p>كود ثابت لطلبات المستثمرين — يفتح نموذج الطلب مباشرة، ويمكنكم أيضًا تعبئته من هنا.</p>
        <a href="{{ route('investor.request') }}" class="btn btn-lime">تقديم طلب استثماري</a>
      </div>
    </div>
  </div>
</section>
@endsection
