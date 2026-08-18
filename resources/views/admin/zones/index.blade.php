@extends('layouts.admin')

@section('title', 'المناطق والماكيتات')

@section('content')
<h1 class="page-title">المناطق والماكيتات</h1>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif

<p class="page-hint">
  لكل منطقة رمز QR خاص يُطبع ويوضع على الماكيت. الضيف يمسحه بجواله فيفتح نموذج
  تسجيل الاهتمام، ويُربط تلقائيًا بملفه إن كان رقم جواله مسجلًا لدينا.
</p>

<details class="panel-box">
  <summary>+ إضافة منطقة جديدة</summary>
  <form method="POST" action="{{ route('admin.zones.store') }}" class="box-body">
    @csrf
    <div class="form-grid">
      <div class="field"><label>اسم المنطقة *</label><input name="name" value="{{ old('name') }}" required></div>
      <div class="field">
        <label>المعرّف بالرابط (إنجليزي)</label>
        <input name="slug" value="{{ old('slug') }}" dir="ltr" placeholder="workshops">
      </div>
      <div class="field full"><label>الوصف</label><textarea name="description" rows="2">{{ old('description') }}</textarea></div>
      <div class="full"><button type="submit" class="btn btn-green">إضافة المنطقة</button></div>
    </div>
  </form>
</details>

<div class="zone-grid">
  @foreach($zones as $zone)
    <div class="zone-card">
      <div class="zone-qr">
        <img src="{{ route('admin.zones.qr', $zone) }}" alt="QR {{ $zone->name }}">
      </div>
      <h3>{{ $zone->name }}</h3>
      <p class="zone-desc">{{ $zone->description ?? 'بدون وصف' }}</p>

      <div class="zone-facts">
        <span><b>{{ $zone->leads_count }}</b> اهتمام مسجل</span>
        @if($zone->active)
          <span class="badge badge-green">مفعّلة</span>
        @else
          <span class="badge badge-gray">متوقفة</span>
        @endif
      </div>

      <div class="zone-links">
        <a href="{{ route('zone', $zone->slug) }}" target="_blank" rel="noopener" class="mini-btn">فتح الصفحة</a>
        <a href="{{ route('admin.zones.qr', $zone) }}" download="qr-{{ $zone->slug }}.png" class="mini-btn">تحميل QR</a>
      </div>

      <details class="lead-edit">
        <summary>تعديل المنطقة</summary>
        <form method="POST" action="{{ route('admin.zones.update', $zone) }}" class="box-body">
          @csrf @method('PATCH')
          <div class="form-grid">
            <div class="field full"><label>الاسم *</label><input name="name" value="{{ $zone->name }}" required></div>
            <div class="field full"><label>المعرّف *</label><input name="slug" value="{{ $zone->slug }}" dir="ltr" required></div>
            <div class="field full"><label>الوصف</label><textarea name="description" rows="2">{{ $zone->description }}</textarea></div>
            <div class="field full">
              <label class="switch"><input type="checkbox" name="active" value="1" @checked($zone->active)> مفعّلة للزوار</label>
            </div>
            <div class="full"><button type="submit" class="btn btn-green btn-block">حفظ</button></div>
          </div>
        </form>
      </details>
    </div>
  @endforeach
</div>
@endsection
