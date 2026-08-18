@extends('layouts.public')

@section('title', 'تسجيل الحضور')

@section('content')
<section class="page-hero">
  <div class="container">
    <h1>طلب حضور الحفل</h1>
    <p>قدّم طلب حضورك، وبعد اعتماده من فريق التنظيم تصلك بطاقة الدعوة بكود QR على الواتساب أو البريد</p>
  </div>
</section>

<div class="form-page">
  <div class="panel wide fade-up">
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
</div>
@endsection
