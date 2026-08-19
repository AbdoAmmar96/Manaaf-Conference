@extends('layouts.public')

@section('title', 'تسجيل الحضور')

@section('content')
<section class="page-hero">
  <div class="container">
    <h1>طلب حضور الحفل</h1>
    <p>قدّم طلب حضورك، وبعد اعتماده من فريق التنظيم تصلك بطاقة الدعوة بكود QR على الواتساب أو البريد</p>
  </div>
</section>

{{--
  تخطيط عمودين يعيد استخدام .invest-grid: قاعدة الجوال فيه تعطي .side-card
  ترتيب -1، فيظهر كود الدعوة قبل النموذج على الجوال ويُمسح مباشرة.
--}}
<section class="section">
  <div class="container">
    <div class="invest-grid fade-up">
      <div class="panel wide">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
      <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif
    <p class="form-note">
      الحضور بالدعوة. بعد إرسال الطلب يراجعه فريق التنظيم، وعند اعتماده تصلك
      بطاقة الدعوة الخاصة بك — احتفظ بها وقدّمها عند البوابة.
    </p>
    <form method="POST" action="{{ route('register.store') }}">
      @csrf
      @if($fromQr)<input type="hidden" name="src" value="qr">@endif
      <div class="form-grid">
        <div class="field"><label>الاسم الكامل *</label><input name="name" value="{{ old('name') }}" required></div>
        <div class="field"><label>رقم الجوال *</label><input name="mobile" value="{{ old('mobile') }}" required placeholder="05xxxxxxxx" inputmode="tel"></div>
        <div class="field"><label>الجهة / الشركة</label><input name="organization" value="{{ old('organization') }}"></div>
        <div class="field"><label>المنصب</label><input name="position" value="{{ old('position') }}"></div>
        <div class="field"><label>البريد الإلكتروني</label><input type="email" name="email" value="{{ old('email') }}"></div>
        <div class="field">
          <label>صفة الحضور *</label>
          <select name="guest_type" required>
            <option value="guest" @selected(old('guest_type', 'guest') == 'guest')>ضيف</option>
            <option value="investor" @selected(old('guest_type') == 'investor')>مستثمر</option>
            <option value="media" @selected(old('guest_type') == 'media')>إعلام</option>
            <option value="official" @selected(old('guest_type') == 'official')>جهة رسمية</option>
            <option value="other" @selected(old('guest_type') == 'other')>أخرى</option>
          </select>
        </div>
        <div class="full" style="margin-top:.6rem">
          <button type="submit" class="btn btn-green btn-block">إرسال طلب الحضور</button>
        </div>
      </div>
    </form>
      </div>

      <aside class="side-card">
        <h3>أو امسح الكود بجوالك</h3>
        <div class="qr-frame">
          <img src="{{ route('invite.qr.image') }}" alt="كود دعوة الحفل" loading="lazy">
        </div>
        <p>كود واحد ثابت للحفل — يفتح هذا النموذج نفسه على أي جوال، فشاركه مع
          من تودّ دعوته.</p>
        <ul class="side-points">
          <li>تكتب بياناتك بنفسك</li>
          <li>يراجع الطلب فريق التنظيم</li>
          <li>تصلك بطاقة الدعوة على واتساب أو البريد</li>
        </ul>
      </aside>

    </div>
  </div>
</section>
@endsection
