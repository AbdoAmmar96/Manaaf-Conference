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

@if($pendingCount > 0)
  <div class="alert alert-pending">
    ⏳ يوجد <b>{{ $pendingCount }}</b> طلب حضور بانتظار المراجعة —
    <a href="{{ route('admin.guests.index', ['approval' => 'pending']) }}">اعرضها الآن</a>
  </div>
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
  <select name="approval">
    <option value="">كل حالات الطلب</option>
    @foreach($approvals as $ap)
      <option value="{{ $ap->value }}" @selected(request('approval') === $ap->value)>{{ $ap->labelAr() }}</option>
    @endforeach
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
        <th>حالة الطلب</th>
        <th>الحضور</th>
        <th>بطاقة الدعوة</th>
        @if(auth()->user()->isAdmin())<th>حذف</th>@endif
      </tr>
    </thead>
    <tbody>
      @forelse($guests as $guest)
        @php($waNumber = preg_replace('/^0/', '966', preg_replace('/\D/', '', $guest->mobile)))
        @php($mailSubject = rawurlencode('بطاقة دعوتكم — ' . \App\Models\Setting::get('event_name', 'حفل افتتاح مدينة منافع')))
        @php($mailBody = rawurlencode('حياكم الله،' . "\n\n" . 'يسعدنا دعوتكم لحضور ' . \App\Models\Setting::get('event_name', 'حفل افتتاح مدينة منافع') . '.' . "\n\n" . 'بطاقة دخولكم بكود QR على الرابط التالي — احتفظوا بها وقدّموها عند البوابة:' . "\n" . route('guest.qr', ['token' => $guest->qr_token]) . "\n\n" . 'مع خالص التقدير.'))
        @php($waText = rawurlencode('حياكم الله في ' . \App\Models\Setting::get('event_name', 'حفل افتتاح مدينة منافع') . " — بطاقة دخولكم:\n" . route('guest.qr', ['token' => $guest->qr_token])))
        <tr>
          <td>
            <b>{{ $guest->name }}</b>
            @if($guest->guest_type === \App\Enums\GuestType::Vip) <span class="badge badge-vip">VIP</span> @endif
            @if($guest->organization)<div style="font-size:.76rem;color:var(--muted)">{{ $guest->organization }}</div>@endif
          </td>
          <td>{{ $guest->mobile }}</td>
          <td>{{ $guest->guest_type->labelAr() }}</td>
          <td>
            @if($guest->approval_status === \App\Enums\ApprovalStatus::Pending)
              <span class="badge badge-vip">بانتظار الموافقة</span>
              @if($guest->registered_via === 'self')<div class="row-note">طلب من الموقع</div>@endif
            @elseif($guest->approval_status === \App\Enums\ApprovalStatus::Rejected)
              <span class="badge badge-red">مرفوض</span>
              @if($guest->rejection_reason)<div class="row-note">{{ $guest->rejection_reason }}</div>@endif
            @else
              <span class="badge badge-green">معتمد</span>
              <div class="row-note">{{ $guest->rsvp_status->labelAr() }}</div>
            @endif
          </td>
          <td>
            @if($guest->isCheckedIn())
              <span class="badge badge-green">حضر {{ $guest->checked_in_at?->format('h:i A') }}</span>
            @else
              <span class="badge badge-gray">{{ $guest->attendance_status->labelAr() }}</span>
            @endif
          </td>
          <td style="white-space:nowrap">
            @if($guest->approval_status === \App\Enums\ApprovalStatus::Pending)
              {{-- الطلب لم يُعتمد بعد: لا تُرسل بطاقة قبل الموافقة --}}
              <form method="POST" action="{{ route('admin.guests.approve', $guest) }}" class="cell-form">
                @csrf @method('PATCH')
                <button type="submit" class="mini-btn ok">✓ اعتماد</button>
              </form>
              <details class="cell-details">
                <summary class="mini-btn danger">✕ رفض</summary>
                <form method="POST" action="{{ route('admin.guests.reject', $guest) }}" class="cell-reject">
                  @csrf @method('PATCH')
                  <input name="rejection_reason" placeholder="سبب الرفض (اختياري)" maxlength="500">
                  <button type="submit" class="mini-btn danger">تأكيد الرفض</button>
                </form>
              </details>
            @elseif($guest->approval_status === \App\Enums\ApprovalStatus::Rejected)
              <form method="POST" action="{{ route('admin.guests.approve', $guest) }}" class="cell-form">
                @csrf @method('PATCH')
                <button type="submit" class="mini-btn ok">إعادة الاعتماد</button>
              </form>
            @else
              <a class="mini-btn" href="{{ route('guest.qr', ['token' => $guest->qr_token]) }}" target="_blank" rel="noopener">عرض</a>
              <a class="mini-btn wa" href="https://wa.me/{{ $waNumber }}?text={{ $waText }}" target="_blank" rel="noopener">واتساب</a>
              @if($guest->email)
                <a class="mini-btn" href="mailto:{{ $guest->email }}?subject={{ $mailSubject }}&body={{ $mailBody }}">بريد</a>
              @endif
              @if($guest->card_sent_at)
                <div class="row-note sent">
                  أُرسلت {{ $guest->card_sent_at->format('m/d h:i A') }}
                  ({{ $guest->card_sent_via === 'whatsapp' ? 'واتساب' : 'بريد' }})
                </div>
              @else
                <form method="POST" action="{{ route('admin.guests.sent', $guest) }}" class="cell-form">
                  @csrf @method('PATCH')
                  <select name="via" class="cell-select">
                    <option value="whatsapp">واتساب</option>
                    <option value="email">بريد</option>
                  </select>
                  <button type="submit" class="mini-btn">تسجيل الإرسال</button>
                </form>
              @endif
            @endif
          </td>

          @if(auth()->user()->isAdmin())
            @php($delMsg = 'حذف «'.$guest->name.'» نهائيًا؟'
              .($guest->isCheckedIn() ? "\n\n⚠ هذا الضيف سجّل حضوره بالفعل، وحذفه ينقص عدد الحضور في التقارير." : '')
              .($guest->leads_count ? "\n\nله {$guest->leads_count} اهتمام مسجّل — سيبقى محفوظًا باسمه وجواله." : ''))
            <td style="white-space:nowrap">
              <form method="POST" action="{{ route('admin.guests.destroy', $guest) }}" class="cell-form"
                    onsubmit="return confirm(@js($delMsg))">
                @csrf @method('DELETE')
                <button type="submit" class="mini-btn danger">حذف</button>
              </form>
            </td>
          @endif
        </tr>
      @empty
        <tr><td colspan="{{ auth()->user()->isAdmin() ? 7 : 6 }}" style="text-align:center;color:var(--muted)">لا يوجد ضيوف مطابقون.</td></tr>
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
