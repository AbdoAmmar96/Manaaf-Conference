@extends('layouts.public')

@section('title', 'سجّل اهتمامك — ' . $zone->name)

@section('content')
<section class="page-hero">
  <div class="container">
    <span class="kicker" style="display:inline-block;background:rgba(153,189,62,.16);color:var(--lime);border:1px solid rgba(153,189,62,.4);border-radius:999px;padding:.3rem 1rem;font-size:.8rem;font-weight:700;margin-bottom:.9rem">{{ $zone->name }}</span>
    <h1>سجّل اهتمامك</h1>
    <p>اختر مجالات اهتمامك وسيتواصل معك فريق المبيعات خلال المؤتمر أو بعده مباشرة</p>
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
    <form method="POST" action="{{ route('zone.store', ['slug' => $zone->slug]) }}">
      @csrf
      <div class="form-grid">
        <div class="field"><label>الاسم الكامل *</label><input name="name" value="{{ old('name') }}" required></div>
        <div class="field"><label>رقم الجوال *</label><input name="mobile" value="{{ old('mobile') }}" required inputmode="tel"></div>
      </div>
      <div class="field">
        <label>مجالات الاهتمام *</label>
        <div class="check-grid">
          @foreach($interests as $interest)
            <label>
              <input type="checkbox" name="interests[]" value="{{ $interest->slug }}"
                     @checked(is_array(old('interests')) && in_array($interest->slug, old('interests')))>
              {{ $interest->name }}
            </label>
          @endforeach
        </div>
      </div>
      <div class="field"><label>ملاحظات</label><textarea name="notes" rows="3">{{ old('notes') }}</textarea></div>
      <button type="submit" class="btn btn-green btn-block">تسجيل الاهتمام</button>
    </form>
  </div>
</div>
@endsection
