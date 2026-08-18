@extends('layouts.admin')

@section('title', 'إدارة الضيوف')

@section('content')
<h1 class="page-title">إدارة الضيوف</h1>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
  <div class="alert alert-error">{{ $errors->first() }}</div>
@endif

<details class="add-box" @if($errors->any()) open @endif>
  <summary>＋ إضافة ضيف جديد وتوليد بطاقته</summary>
  <div class="box-body">
    <form method="POST" action="{{ route('admin.guests.store') }}">
      @csrf
      <div class="form-grid">
        <div class="field"><label>الاسم الكامل *</label><input name="name" value="{{ old('name') }}" required></div>
        <div class="field"><label>رقم الجوال *</label><input name="mobile" value="{{ old('mobile') }}" required inputmode="tel"></div>
        <div class="field"><label>الجهة</label><input name="organization" value="{{ old('organization') }}"></div>
        <div class="field"><label>المنصب</label><input name="position" value="{{ old('position') }}"></div>
        <div class="field"><label>البريد الإلكتروني</label><input type="email" name="email" value="{{ old('email') }}"></div>
        <div class="field">
          <label>نوع الضيف *</label>
          <select name="guest_type" required>
            @foreach($types as $type)
              <option value="{{ $type->value }}" @selected(old('guest_type') == $type->value)>{{ $type->labelAr() }}</option>
            @endforeach
          </select>
        </div>
        <div class="full"><button type="submit" class="btn btn-green">إضافة الضيف</button></div>
      </div>
    </form>
  </div>
</details>

<form method="GET" class="filter-bar">
  <input type="text" name="q" value="{{ request('q') }}" placeholder="بحث بالاسم أو الجوال…">
  <select name="type">
    <option value="">كل الأنواع</option>
    @foreach($types as $type)
      <option value="{{ $type->value }}" @selected(request('type') == $type->value)>{{ $type->labelAr() }}</option>
    @endforeach
  </select>
  <select name="attendance">
    <option value="">كل حالات الحضور</option>
    <option value="awaited" @selected(request('attendance') == 'awaited')>لم يصل بعد</option>
    <option value="attended" @selected(request('attendance') == 'attended')>حضر</option>
    <option value="no_show" @selected(request('attendance') == 'no_show')>لم يحضر</option>
    <option value="cancelled" @selected(request('attendance') == 'cancelled')>ملغي</option>
  </select>
  <button type="submit" class="mini-btn" style="padding:.55rem 1.2rem">تصفية</button>
</form>

<div class="table-wrap">
  <table class="data">
    <thead>
      <tr>
        <th>الضيف</th>
        <th>الجوال</th>
        <th>النوع</th>
        <th>RSVP</th>
        <th>الحضور</th>
        <th>البطاقة</th>
      </tr>
    </thead>
    <tbody>
      @forelse($guests as $guest)
        @php($waNumber = preg_replace('/^0/', '966', preg_replace('/\D/', '', $guest->mobile)))
        @php($waText = rawurlencode('حياكم الله في ' . \App\Models\Setting::get('event_name', 'حفل افتتاح مدينة منافع') . " — بطاقة دخولكم:\n" . route('guest.qr', ['token' => $guest->qr_token])))
        <tr>
          <td>
            <b>{{ $guest->name }}</b>
            @if($guest->guest_type === \App\Enums\GuestType::Vip) <span class="badge badge-vip">VIP</span> @endif
            @if($guest->organization)<div style="font-size:.76rem;color:var(--muted)">{{ $guest->organization }}</div>@endif
          </td>
          <td>{{ $guest->mobile }}</td>
          <td>{{ $guest->guest_type->labelAr() }}</td>
          <td>{{ $guest->rsvp_status->labelAr() }}</td>
          <td>
            @if($guest->isCheckedIn())
              <span class="badge badge-green">حضر {{ $guest->checked_in_at?->format('h:i A') }}</span>
            @else
              <span class="badge badge-gray">{{ $guest->attendance_status->labelAr() }}</span>
            @endif
          </td>
          <td style="white-space:nowrap">
            <a class="mini-btn" href="{{ route('guest.qr', ['token' => $guest->qr_token]) }}" target="_blank" rel="noopener">البطاقة</a>
            <a class="mini-btn wa" href="https://wa.me/{{ $waNumber }}?text={{ $waText }}" target="_blank" rel="noopener">واتساب</a>
          </td>
        </tr>
      @empty
        <tr><td colspan="6" style="text-align:center;color:var(--muted)">لا يوجد ضيوف مطابقون.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

@if($guests->hasPages())
  <div class="pager">
    @if($guests->previousPageUrl())<a href="{{ $guests->previousPageUrl() }}">السابق</a>@endif
    <span>صفحة {{ $guests->currentPage() }} من {{ $guests->lastPage() }}</span>
    @if($guests->nextPageUrl())<a href="{{ $guests->nextPageUrl() }}">التالي</a>@endif
  </div>
@endif
@endsection
