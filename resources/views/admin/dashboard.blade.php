@extends('layouts.admin')

@section('title', 'لوحة التحكم')

@section('content')
<h1 class="page-title">نظرة لحظية على الحفل</h1>

<div class="stats-grid">
  <div class="stat"><b>{{ $stats['invited'] }}</b><span>إجمالي المدعوين</span></div>
  <div class="stat"><b>{{ $stats['confirmed'] }}</b><span>مؤكدو الحضور</span></div>
  <div class="stat"><b>{{ $stats['attended'] }}</b><span>حضروا فعليًا</span></div>
  <div class="stat"><b>{{ $stats['awaited'] }}</b><span>لم يصلوا بعد</span></div>
  <div class="stat accent"><b>{{ $stats['vip'] }}</b><span>ضيوف VIP</span></div>
  <div class="stat accent"><b>{{ $stats['leads'] }}</b><span>اهتمامات مسجلة</span></div>
  <div class="stat"><b>{{ $stats['messages_new'] }}</b><span>رسائل جديدة</span></div>
</div>

<h2 class="page-title" style="font-size:1.05rem">آخر عمليات تسجيل الدخول</h2>
<div class="table-wrap">
  <table class="data">
    <thead>
      <tr>
        <th>الضيف</th>
        <th>النوع</th>
        <th>وقت الدخول</th>
        <th>سجّله</th>
      </tr>
    </thead>
    <tbody>
      @forelse($latestCheckins as $guest)
        <tr>
          <td>{{ $guest->name }}</td>
          <td>
            @if($guest->guest_type === \App\Enums\GuestType::Vip)
              <span class="badge badge-vip">VIP</span>
            @else
              <span class="badge badge-gray">{{ $guest->guest_type->labelAr() }}</span>
            @endif
          </td>
          <td>{{ $guest->checked_in_at?->format('h:i A') }}</td>
          <td>{{ $guest->checkedInBy?->name ?? '—' }}</td>
        </tr>
      @empty
        <tr><td colspan="4" style="text-align:center;color:var(--muted)">لم يبدأ تسجيل الدخول بعد.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
