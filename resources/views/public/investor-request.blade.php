@extends('layouts.public')

@section('title', 'طلب استثماري')

@section('content')
<section class="page-hero">
  <div class="container">
    <h1>طلب استثماري</h1>
    <p>عبّئ النموذج وسيتواصل معك فريق الاستثمار لعرض الفرص المناسبة لاهتمامك</p>
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
    <form method="POST" action="{{ route('investor.request.store') }}">
      @csrf
      @if($fromQr)<input type="hidden" name="src" value="qr">@endif
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
        <div class="field full">
          <label>المنطقة التي تهتم بها</label>
          <select name="zone_id">
            <option value="">— طلب عام (غير مرتبط بمنطقة) —</option>
            @foreach($zones as $zone)
              <option value="{{ $zone->id }}" @selected(old('zone_id') == $zone->id)>{{ $zone->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="field full"><label>تفاصيل الطلب *</label><textarea name="body" rows="4" required>{{ old('body') }}</textarea></div>
        <div class="full"><button type="submit" class="btn btn-green btn-block">إرسال الطلب</button></div>
      </div>
    </form>
  </div>
</div>
@endsection
