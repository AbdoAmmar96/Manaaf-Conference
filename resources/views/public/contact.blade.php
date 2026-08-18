@extends('layouts.public')

@section('title', 'تواصل معنا')

@section('content')
<section class="page-hero">
  <div class="container">
    <h1>تواصل معنا</h1>
    <p>لأي استفسار عن المؤتمر أو حفل الافتتاح — وللطلبات الاستثمارية استخدم صفحة المستثمرين</p>
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
    <p style="font-size:.88rem;color:var(--muted);margin-bottom:1.2rem;line-height:2">
      📍 {{ $eventVenue }} &nbsp;·&nbsp; الموقع الرسمي: <a href="https://manafi.sa" target="_blank" rel="noopener" style="color:var(--green);font-weight:700">manafi.sa</a>
    </p>
    <form method="POST" action="{{ route('contact.store') }}">
      @csrf
      <div class="form-grid">
        <div class="field"><label>الاسم *</label><input name="name" value="{{ old('name') }}" required></div>
        <div class="field"><label>رقم الجوال *</label><input name="mobile" value="{{ old('mobile') }}" required inputmode="tel"></div>
        <div class="field"><label>البريد الإلكتروني</label><input type="email" name="email" value="{{ old('email') }}"></div>
        <div class="field"><label>الموضوع</label><input name="subject" value="{{ old('subject') }}"></div>
        <div class="field full"><label>الرسالة *</label><textarea name="body" rows="4" required>{{ old('body') }}</textarea></div>
        <div class="full"><button type="submit" class="btn btn-green btn-block">إرسال الرسالة</button></div>
      </div>
    </form>
  </div>
</div>
@endsection
