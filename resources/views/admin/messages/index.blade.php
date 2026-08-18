@extends('layouts.admin')

@section('title', 'الرسائل')

@section('content')
<h1 class="page-title">الرسائل الواردة</h1>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif

<div class="stats-grid">
  <div class="stat"><b>{{ $stats['total'] }}</b><span>إجمالي الرسائل</span></div>
  <div class="stat accent"><b>{{ $stats['new'] }}</b><span>جديدة</span></div>
  <div class="stat"><b>{{ $stats['in_progress'] }}</b><span>جاري التنفيذ</span></div>
  <div class="stat"><b>{{ $stats['closed'] }}</b><span>مغلقة</span></div>
</div>

<form method="GET" class="filter-bar">
  <select name="category" onchange="this.form.submit()">
    <option value="">كل التصنيفات</option>
    <option value="general" @selected(request('category') === 'general')>رسائل عادية</option>
    <option value="investor" @selected(request('category') === 'investor')>رسائل المستثمرين</option>
  </select>
  <select name="status" onchange="this.form.submit()">
    <option value="">كل الحالات</option>
    @foreach($statuses as $s)
      <option value="{{ $s->value }}" @selected(request('status') === $s->value)>{{ $s->labelAr() }}</option>
    @endforeach
  </select>
  <select name="zone" onchange="this.form.submit()">
    <option value="">كل المناطق</option>
    @foreach($zones as $z)
      <option value="{{ $z->id }}" @selected(request('zone') == $z->id)>{{ $z->name }}</option>
    @endforeach
  </select>
  @if(request()->hasAny(['category', 'status', 'zone']))
    <a href="{{ route('admin.messages.index') }}" class="mini-btn">إلغاء الفلترة</a>
  @endif
</form>

@forelse($messages as $message)
  <div class="lead-card">
    <div class="lead-head">
      <div>
        <b>{{ $message->name }}</b>
        @if($message->category === \App\Enums\MessageCategory::Investor)
          <span class="badge badge-vip">مستثمر</span>
        @else
          <span class="badge badge-gray">عادية</span>
        @endif
        <span class="lead-meta">
          {{ $message->mobile }}
          @if($message->email) · {{ $message->email }} @endif
        </span>
      </div>
      <div class="lead-head-side">
        <span class="badge badge-green">{{ $message->status->labelAr() }}</span>
        <span class="lead-meta">{{ $message->created_at->format('Y/m/d — h:i A') }}</span>
      </div>
    </div>

    @if($message->subject)<div class="msg-subject">{{ $message->subject }}</div>@endif
    <div class="msg-body">{{ $message->body }}</div>

    <div class="lead-facts">
      <span><b>المصدر:</b> {{ $message->source === 'investor_qr' ? 'QR المستثمرين' : 'نموذج الموقع' }}</span>
      <span><b>المنطقة:</b> {{ $message->zone?->name ?? '—' }}</span>
      <span><b>المسؤول:</b> {{ $message->assignee?->name ?? 'غير معيّن' }}</span>
      <span><b>المتابعات:</b> {{ $message->comments->count() }}</span>
    </div>

    {{-- ═══ متابعة الحالة ═══ --}}
    <details class="lead-edit" @if($message->comments->isNotEmpty()) open @endif>
      <summary>متابعة الحالة ({{ $message->comments->count() }})</summary>

      <div class="box-body">
        @forelse($message->comments as $comment)
          <div class="follow-item">
            <div class="follow-head">
              <b>{{ $comment->user->name }}</b>
              <span>{{ $comment->created_at->format('Y/m/d — h:i A') }}</span>
            </div>
            <p>{{ $comment->body }}</p>
          </div>
        @empty
          <p class="follow-empty">لا توجد متابعات على هذه الرسالة بعد.</p>
        @endforelse

        {{-- إضافة متابعة مع إمكانية تغيير الحالة في نفس الخطوة --}}
        <form method="POST" action="{{ route('admin.messages.comment', $message) }}">
          @csrf
          <div class="form-grid">
            <div class="field full">
              <label>أضف متابعة</label>
              <textarea name="body" rows="2" required placeholder="ما الذي تم في هذه الرسالة؟"></textarea>
            </div>
            <div class="field">
              <label>تغيير الحالة (اختياري)</label>
              <select name="status">
                <option value="">— إبقاء الحالة كما هي —</option>
                @foreach($statuses as $s)
                  <option value="{{ $s->value }}" @selected($message->status === $s)>{{ $s->labelAr() }}</option>
                @endforeach
              </select>
            </div>
            <div class="field" style="align-self:end">
              <button type="submit" class="btn btn-green btn-block">إضافة المتابعة</button>
            </div>
          </div>
        </form>

        {{-- تعيين موظف مسؤول وتغيير الحالة مباشرة --}}
        <form method="POST" action="{{ route('admin.messages.update', $message) }}" class="inline-form">
          @csrf @method('PATCH')
          <div class="form-grid">
            <div class="field">
              <label>الحالة</label>
              <select name="status" required>
                @foreach($statuses as $s)
                  <option value="{{ $s->value }}" @selected($message->status === $s)>{{ $s->labelAr() }}</option>
                @endforeach
              </select>
            </div>
            <div class="field">
              <label>الموظف المسؤول</label>
              <select name="assigned_to">
                <option value="">— غير معيّن —</option>
                @foreach($team as $u)
                  <option value="{{ $u->id }}" @selected($message->assigned_to == $u->id)>{{ $u->name }} ({{ $u->role->labelAr() }})</option>
                @endforeach
              </select>
            </div>
            <div class="field" style="align-self:end">
              <button type="submit" class="btn btn-outline-green btn-block">تحديث الحالة والمسؤول</button>
            </div>
          </div>
        </form>
      </div>
    </details>
  </div>
@empty
  <div class="table-wrap" style="padding:2.5rem;text-align:center;color:var(--muted)">
    لا توجد رسائل بعد.
  </div>
@endforelse

@if($messages->hasPages())
  <div class="pager">
    @if($messages->previousPageUrl())<a href="{{ $messages->previousPageUrl() }}">السابق</a>@endif
    <span>صفحة {{ $messages->currentPage() }} من {{ $messages->lastPage() }}</span>
    @if($messages->nextPageUrl())<a href="{{ $messages->nextPageUrl() }}">التالي</a>@endif
  </div>
@endif
@endsection
