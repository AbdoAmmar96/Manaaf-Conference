@extends('layouts.admin')

@section('title', 'Leads')

@section('content')
<h1 class="page-title">Leads — اهتمامات العملاء</h1>

<form method="GET" class="filter-bar">
  <select name="score" onchange="this.form.submit()">
    <option value="">كل الدرجات</option>
    <option value="hot" @selected(request('score') == 'hot')>ساخن</option>
    <option value="warm" @selected(request('score') == 'warm')>متوسط</option>
    <option value="cold" @selected(request('score') == 'cold')>بارد</option>
  </select>
</form>

<div class="table-wrap">
  <table class="data">
    <thead>
      <tr>
        <th>التاريخ</th>
        <th>العميل</th>
        <th>الاهتمامات</th>
        <th>الدرجة</th>
        <th>المصدر</th>
        <th>المنطقة</th>
      </tr>
    </thead>
    <tbody>
      @forelse($leads as $lead)
        <tr>
          <td style="white-space:nowrap">{{ $lead->created_at->format('m/d h:i A') }}</td>
          <td>
            <b>{{ $lead->displayName() }}</b>
            @if($lead->guest)<span class="badge badge-green" style="font-size:.66rem">ضيف مسجل</span>@endif
            <div style="font-size:.76rem;color:var(--muted)">{{ $lead->guest?->mobile ?? $lead->walk_in_mobile }}</div>
          </td>
          <td style="font-size:.8rem">
            {{ collect($lead->interests)->map(fn ($i) => \App\Enums\LeadInterest::from($i)->labelAr())->implode('، ') }}
          </td>
          <td>
            @if($lead->score === \App\Enums\LeadScore::Hot)
              <span class="badge badge-red">ساخن</span>
            @elseif($lead->score === \App\Enums\LeadScore::Warm)
              <span class="badge badge-vip">متوسط</span>
            @else
              <span class="badge badge-gray">بارد</span>
            @endif
          </td>
          <td style="font-size:.8rem">{{ $lead->source->labelAr() }}</td>
          <td style="font-size:.8rem">{{ $lead->zone?->name ?? '—' }}</td>
        </tr>
      @empty
        <tr><td colspan="6" style="text-align:center;color:var(--muted)">لا توجد Leads بعد.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

@if($leads->hasPages())
  <div class="pager">
    @if($leads->previousPageUrl())<a href="{{ $leads->previousPageUrl() }}">السابق</a>@endif
    <span>صفحة {{ $leads->currentPage() }} من {{ $leads->lastPage() }}</span>
    @if($leads->nextPageUrl())<a href="{{ $leads->nextPageUrl() }}">التالي</a>@endif
  </div>
@endif

<p style="margin-top:1rem;font-size:.8rem;color:var(--muted)">فورم المبيعات الكامل (الدرجة، الموظف المسؤول، مواعيد المتابعة) — يُضاف في المرحلة الخامسة.</p>
@endsection
