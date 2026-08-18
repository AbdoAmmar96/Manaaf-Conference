@extends('layouts.admin')

@section('title', 'الرسائل')

@section('content')
<h1 class="page-title">الرسائل الواردة</h1>

<form method="GET" class="filter-bar">
  <select name="category" onchange="this.form.submit()">
    <option value="">كل التصنيفات</option>
    <option value="general" @selected(request('category') == 'general')>رسائل عادية</option>
    <option value="investor" @selected(request('category') == 'investor')>رسائل المستثمرين</option>
  </select>
</form>

<div class="table-wrap">
  <table class="data">
    <thead>
      <tr>
        <th>التاريخ</th>
        <th>المرسل</th>
        <th>الموضوع / الرسالة</th>
        <th>التصنيف</th>
        <th>الحالة</th>
        <th>المصدر</th>
      </tr>
    </thead>
    <tbody>
      @forelse($messages as $message)
        <tr>
          <td style="white-space:nowrap">{{ $message->created_at->format('m/d h:i A') }}</td>
          <td>
            <b>{{ $message->name }}</b>
            <div style="font-size:.76rem;color:var(--muted)">{{ $message->mobile }}</div>
          </td>
          <td>
            @if($message->subject)<b style="font-size:.85rem">{{ $message->subject }}</b><br>@endif
            <span style="font-size:.8rem;color:var(--muted)">{{ \Illuminate\Support\Str::limit($message->body, 70) }}</span>
          </td>
          <td>
            @if($message->category === \App\Enums\MessageCategory::Investor)
              <span class="badge badge-vip">مستثمر</span>
            @else
              <span class="badge badge-gray">عادية</span>
            @endif
          </td>
          <td><span class="badge badge-green">{{ $message->status->labelAr() }}</span></td>
          <td style="font-size:.78rem;color:var(--muted)">{{ $message->source === 'investor_qr' ? 'QR المستثمرين' : 'نموذج الموقع' }}</td>
        </tr>
      @empty
        <tr><td colspan="6" style="text-align:center;color:var(--muted)">لا توجد رسائل بعد.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

@if($messages->hasPages())
  <div class="pager">
    @if($messages->previousPageUrl())<a href="{{ $messages->previousPageUrl() }}">السابق</a>@endif
    <span>صفحة {{ $messages->currentPage() }} من {{ $messages->lastPage() }}</span>
    @if($messages->nextPageUrl())<a href="{{ $messages->nextPageUrl() }}">التالي</a>@endif
  </div>
@endif

<p style="margin-top:1rem;font-size:.8rem;color:var(--muted)">التعليقات الداخلية وتغيير حالات الطلبات وتعيين الموظفين — تُضاف في المرحلة الرابعة.</p>
@endsection
