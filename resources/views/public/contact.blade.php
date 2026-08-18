@extends('layouts.public')

@section('title', 'تواصل معنا')

@section('content')
<section class="page-hero">
  <div class="container">
    <h1>تواصل معنا</h1>
    <p>لأي استفسار عن المؤتمر أو حفل الافتتاح — وللطلبات الاستثمارية استخدم صفحة المستثمرين</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="contact-grid fade-up">

      {{-- ─── نموذج الرسالة ─── --}}
      <div class="panel wide">
        <h2 class="invest-title">أرسل لنا رسالة</h2>
        <p class="invest-sub">اكتب استفسارك وسنعود إليك في أقرب وقت.</p>

        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
          <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('contact.store') }}">
          @csrf
          <div class="form-grid">
            <div class="field"><label>الاسم *</label><input name="name" value="{{ old('name') }}" required></div>
            <div class="field"><label>رقم الجوال *</label><input name="mobile" value="{{ old('mobile') }}" required inputmode="tel"></div>
            <div class="field"><label>البريد الإلكتروني</label><input type="email" name="email" value="{{ old('email') }}"></div>
            <div class="field"><label>الموضوع</label><input name="subject" value="{{ old('subject') }}"></div>
            <div class="field full">
              <label>المنطقة التي يخصّها استفسارك</label>
              <select name="zone_id">
                <option value="">— استفسار عام (غير مرتبط بمنطقة) —</option>
                @foreach($zones as $zone)
                  <option value="{{ $zone->id }}" @selected(old('zone_id') == $zone->id)>{{ $zone->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="field full"><label>الرسالة *</label><textarea name="body" rows="5" required>{{ old('body') }}</textarea></div>
            <div class="full"><button type="submit" class="btn btn-green btn-block">إرسال الرسالة</button></div>
          </div>
        </form>
      </div>

      {{-- ─── بيانات التواصل — بجانب النموذج ─── --}}
      <aside class="side-card contact-aside">
        <h3>بيانات التواصل</h3>
        <ul class="info-list">

          @if($eventVenue)
            <li>
              <span class="ico" aria-hidden="true">📍</span>
              <div><b>العنوان</b>
                @if($contact['map'])
                  <a href="{{ $contact['map'] }}" target="_blank" rel="noopener">{{ $eventVenue }}</a>
                @else
                  <span>{{ $eventVenue }}</span>
                @endif
              </div>
            </li>
          @endif

          @if($contact['phone'])
            <li>
              <span class="ico" aria-hidden="true">📞</span>
              <div><b>الهاتف</b>
                <a href="tel:{{ preg_replace('/\s+/', '', $contact['phone']) }}" dir="ltr">{{ $contact['phone'] }}</a>
              </div>
            </li>
          @endif

          @if($contact['whatsapp'])
            <li>
              <span class="ico" aria-hidden="true">💬</span>
              <div><b>واتساب</b>
                <a href="https://wa.me/{{ preg_replace('/\D/', '', $contact['whatsapp']) }}" target="_blank" rel="noopener" dir="ltr">{{ $contact['whatsapp'] }}</a>
              </div>
            </li>
          @endif

          @if($contact['email'])
            <li>
              <span class="ico" aria-hidden="true">✉️</span>
              <div><b>البريد الإلكتروني</b>
                <a href="mailto:{{ $contact['email'] }}" dir="ltr">{{ $contact['email'] }}</a>
              </div>
            </li>
          @endif

          @if($contact['website'])
            <li>
              <span class="ico" aria-hidden="true">🌐</span>
              <div><b>الموقع الرسمي</b>
                @php($siteHost = preg_replace('#^https?://#', '', rtrim($contact['website'], '/')))
                <a href="https://{{ $siteHost }}" target="_blank" rel="noopener" dir="ltr">{{ $siteHost }}</a>
              </div>
            </li>
          @endif

          @if($contact['hours'])
            <li>
              <span class="ico" aria-hidden="true">🕒</span>
              <div><b>ساعات العمل</b><span>{{ $contact['hours'] }}</span></div>
            </li>
          @endif

        </ul>

        <div class="aside-cta">
          <p>هل تبحث عن فرصة استثمارية؟</p>
          <a href="{{ route('investors') }}" class="btn btn-lime btn-block">صفحة المستثمرين</a>
        </div>
      </aside>

    </div>
  </div>
</section>
@endsection
