@extends('layouts.public')

@section('title', 'المستثمرون')

@section('content')
<section class="page-hero">
  <div class="container">
    <h1>الفرص الاستثمارية</h1>
    <p>منظومة متكاملة للخدمات الحرفية والصناعية على مساحة تتجاوز 2.5 مليون متر مربع شمال وجنوب الطائف — بعقود طويلة الأجل وبيئة أعمال جاهزة</p>
  </div>
</section>

{{-- ═══ طلب استثماري مباشر — أسفل الهيرو مباشرة ═══ --}}
<section class="section" id="investor-request">
  <div class="container">
    <div class="invest-grid fade-up">

      <div class="panel wide invest-form">
        <span class="eyebrow">قدّم طلبك الآن</span>
        <h2 class="invest-title">طلب استثماري مباشر</h2>
        <p class="invest-sub">عبّئ النموذج وسيتواصل معك فريق الاستثمار لعرض الفرص المناسبة لاهتمامك.</p>

        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
          <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('investor.request.store') }}">
          @csrf
          <div class="form-grid">
            <div class="field"><label>الاسم الكامل *</label><input name="name" value="{{ old('name') }}" required></div>
            <div class="field"><label>رقم الجوال *</label><input name="mobile" value="{{ old('mobile') }}" required inputmode="tel"></div>
            <div class="field"><label>الشركة / الجهة</label><input name="company" value="{{ old('company') }}"></div>
            <div class="field"><label>البريد الإلكتروني</label><input type="email" name="email" value="{{ old('email') }}"></div>
            <div class="field full">
              <label>مجال الاهتمام</label>
              <select name="interest">
                <option value="">— اختر المجال —</option>
                @foreach($interests as $interest)
                  <option value="{{ $interest->slug }}" @selected(old('interest') == $interest->slug)>{{ $interest->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="field full"><label>تفاصيل الطلب *</label><textarea name="body" rows="4" required>{{ old('body') }}</textarea></div>
            <div class="full"><button type="submit" class="btn btn-green btn-block">إرسال الطلب</button></div>
          </div>
        </form>
      </div>

      <aside class="side-card">
        <h3>أو امسح الكود بجوالك</h3>
        <div class="qr-frame">
          <img src="{{ route('investor.qr.image') }}" alt="QR طلبات المستثمرين">
        </div>
        <p>كود ثابت لطلبات المستثمرين — يفتح نموذج الطلب مباشرة على جوالك، ويمكن طباعته ووضعه في جناح الاستثمار بالمعرض.</p>
        <ul class="side-points">
          <li>عقود تأجير طويلة الأجل</li>
          <li>بنية تحتية ومرافق جاهزة</li>
          <li>ستة قطاعات متخصصة متكاملة</li>
        </ul>
      </aside>

    </div>
  </div>
</section>

<section class="section alt">
  <div class="container">
    <div class="stats-band fade-up">
      <div class="stat-pub"><b>+2.53</b><span>مليون م² المساحة الإجمالية</span></div>
      <div class="stat-pub"><b>3,263</b><span>موقعًا تأجيريًا</span></div>
      <div class="stat-pub"><b>6,119</b><span>فرصة عمل متوقعة</span></div>
      <div class="stat-pub"><b>6</b><span>قطاعات متكاملة</span></div>
    </div>
  </div>
</section>

<section class="section">
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
    <div class="section-cta">
      <a href="#investor-request" class="btn btn-lime">قدّم طلبك الاستثماري</a>
    </div>
  </div>
</section>
@endsection
