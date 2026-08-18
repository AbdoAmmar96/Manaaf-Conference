@extends('layouts.admin')

@section('title', 'اهتمامات العملاء')

@section('content')
<h1 class="page-title">اهتمامات العملاء</h1>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif

<div class="stats-grid">
  <div class="stat"><b>{{ $stats['total'] }}</b><span>إجمالي الاهتمامات</span></div>
  <div class="stat accent"><b>{{ $stats['hot'] }}</b><span>ساخن</span></div>
  <div class="stat"><b>{{ $stats['warm'] }}</b><span>متوسط</span></div>
  <div class="stat"><b>{{ $stats['cold'] }}</b><span>بارد</span></div>
</div>

{{-- ═══ تسجيل اهتمام جديد ═══ --}}
<details class="panel-box" @if($errors->any() && old('_form') === 'create') open @endif>
  <summary>+ تسجيل اهتمام عميل جديد</summary>
  <form method="POST" action="{{ route('admin.leads.store') }}" class="box-body">
    @csrf
    <input type="hidden" name="_form" value="create">
    <div class="form-grid">
      <div class="field full">
        <label>الضيف المسجل</label>
        <select name="guest_id" id="guestPick">
          <option value="">— زائر غير مسجل (اكتب بياناته يدويًا) —</option>
          @foreach($guests as $g)
            <option value="{{ $g->id }}" @selected(old('guest_id') == $g->id)>{{ $g->name }} — {{ $g->mobile }}</option>
          @endforeach
        </select>
      </div>
      <div class="field"><label>اسم الزائر</label><input name="walk_in_name" value="{{ old('walk_in_name') }}"></div>
      <div class="field"><label>جوال الزائر</label><input name="walk_in_mobile" value="{{ old('walk_in_mobile') }}" inputmode="tel"></div>

      <div class="field full">
        <label>الاهتمامات * (اختر واحدًا أو أكثر)</label>
        <div class="check-grid">
          @foreach($interests as $i)
            <label><input type="checkbox" name="interests[]" value="{{ $i->value }}"
              @checked(in_array($i->value, old('interests', []))) > {{ $i->labelAr() }}</label>
          @endforeach
        </div>
      </div>

      <div class="field">
        <label>الدرجة *</label>
        <select name="score" required>
          @foreach($scores as $s)
            <option value="{{ $s->value }}" @selected(old('score', 'warm') === $s->value)>{{ $s->labelAr() }}</option>
          @endforeach
        </select>
      </div>
      <div class="field">
        <label>المنطقة</label>
        <select name="zone_id">
          <option value="">— بدون —</option>
          @foreach($zones as $z)
            <option value="{{ $z->id }}" @selected(old('zone_id') == $z->id)>{{ $z->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="field">
        <label>الموظف المسؤول</label>
        <select name="assigned_to">
          <option value="">— غير معيّن —</option>
          @foreach($salesTeam as $u)
            <option value="{{ $u->id }}" @selected(old('assigned_to') == $u->id)>{{ $u->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="field"><label>موعد المتابعة</label><input type="date" name="follow_up_at" value="{{ old('follow_up_at') }}"></div>

      <div class="field full"><label>ملاحظات</label><textarea name="notes" rows="3">{{ old('notes') }}</textarea></div>
      <div class="full"><button type="submit" class="btn btn-green">حفظ الاهتمام</button></div>
    </div>
  </form>
</details>

{{-- ═══ الفلاتر ═══ --}}
<form method="GET" class="filter-bar">
  <select name="score" onchange="this.form.submit()">
    <option value="">كل الدرجات</option>
    @foreach($scores as $s)
      <option value="{{ $s->value }}" @selected(request('score') === $s->value)>{{ $s->labelAr() }}</option>
    @endforeach
  </select>
  <select name="interest" onchange="this.form.submit()">
    <option value="">كل الاهتمامات</option>
    @foreach($interests as $i)
      <option value="{{ $i->value }}" @selected(request('interest') === $i->value)>{{ $i->labelAr() }}</option>
    @endforeach
  </select>
  <select name="zone" onchange="this.form.submit()">
    <option value="">كل المناطق</option>
    @foreach($zones as $z)
      <option value="{{ $z->id }}" @selected(request('zone') == $z->id)>{{ $z->name }}</option>
    @endforeach
  </select>
  @if(request()->hasAny(['score', 'interest', 'zone']))
    <a href="{{ route('admin.leads.index') }}" class="mini-btn">إلغاء الفلترة</a>
  @endif
</form>

{{-- ═══ القائمة ═══ --}}
@forelse($leads as $lead)
  <div class="lead-card">
    <div class="lead-head">
      <div>
        <b>{{ $lead->displayName() }}</b>
        @if($lead->guest)<span class="badge badge-green">ضيف مسجل</span>@endif
        <span class="lead-meta">
          {{ $lead->guest?->mobile ?? $lead->walk_in_mobile }}
          @if($lead->guest?->organization) · {{ $lead->guest->organization }} @endif
        </span>
      </div>
      <div class="lead-head-side">
        @if($lead->score === \App\Enums\LeadScore::Hot)
          <span class="badge badge-red">ساخن</span>
        @elseif($lead->score === \App\Enums\LeadScore::Warm)
          <span class="badge badge-vip">متوسط</span>
        @else
          <span class="badge badge-gray">بارد</span>
        @endif
        <span class="lead-meta">{{ $lead->created_at->format('Y/m/d — h:i A') }}</span>
      </div>
    </div>

    <div class="lead-tags">
      @foreach($lead->interests as $i)
        <span class="tag">{{ \App\Enums\LeadInterest::from($i)->labelAr() }}</span>
      @endforeach
    </div>

    <div class="lead-facts">
      <span><b>المصدر:</b> {{ $lead->source->labelAr() }}</span>
      <span><b>المنطقة:</b> {{ $lead->zone?->name ?? '—' }}</span>
      <span><b>المسؤول:</b> {{ $lead->assignee?->name ?? 'غير معيّن' }}</span>
      <span><b>المتابعة:</b> {{ $lead->follow_up_at?->format('Y/m/d') ?? '—' }}</span>
    </div>

    @if($lead->notes)
      <div class="lead-notes">{{ $lead->notes }}</div>
    @endif

    <details class="lead-edit">
      <summary>تعديل الدرجة والاهتمامات والملاحظات</summary>
      <form method="POST" action="{{ route('admin.leads.update', $lead) }}" class="box-body">
        @csrf @method('PATCH')
        <div class="form-grid">
          <div class="field full">
            <label>الاهتمامات *</label>
            <div class="check-grid">
              @foreach($interests as $i)
                <label><input type="checkbox" name="interests[]" value="{{ $i->value }}"
                  @checked(in_array($i->value, $lead->interests ?? [])) > {{ $i->labelAr() }}</label>
              @endforeach
            </div>
          </div>
          <div class="field">
            <label>الدرجة *</label>
            <select name="score" required>
              @foreach($scores as $s)
                <option value="{{ $s->value }}" @selected($lead->score === $s)>{{ $s->labelAr() }}</option>
              @endforeach
            </select>
          </div>
          <div class="field">
            <label>الموظف المسؤول</label>
            <select name="assigned_to">
              <option value="">— غير معيّن —</option>
              @foreach($salesTeam as $u)
                <option value="{{ $u->id }}" @selected($lead->assigned_to == $u->id)>{{ $u->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="field">
            <label>موعد المتابعة</label>
            <input type="date" name="follow_up_at" value="{{ $lead->follow_up_at?->format('Y-m-d') }}">
          </div>
          <div class="field full"><label>ملاحظات</label><textarea name="notes" rows="3">{{ $lead->notes }}</textarea></div>
          <div class="full"><button type="submit" class="btn btn-green">حفظ التعديلات</button></div>
        </div>
      </form>
    </details>
  </div>
@empty
  <div class="table-wrap" style="padding:2.5rem;text-align:center;color:var(--muted)">
    لا توجد اهتمامات مسجلة بعد.
  </div>
@endforelse

@if($leads->hasPages())
  <div class="pager">
    @if($leads->previousPageUrl())<a href="{{ $leads->previousPageUrl() }}">السابق</a>@endif
    <span>صفحة {{ $leads->currentPage() }} من {{ $leads->lastPage() }}</span>
    @if($leads->nextPageUrl())<a href="{{ $leads->nextPageUrl() }}">التالي</a>@endif
  </div>
@endif
@endsection
