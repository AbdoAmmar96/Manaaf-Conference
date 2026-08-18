@extends('layouts.admin')

@section('title', 'التقارير')

@section('content')
<h1 class="page-title">التقارير</h1>

<p class="page-hint">
  ملفات التصدير بصيغة CSV بترميز UTF-8 — تفتح مباشرة في Excel مع دعم كامل
  للعربية. صدّرها بعد انتهاء الحفل للحصول على السجل النهائي.
</p>

<div class="export-row">
  <a href="{{ route('admin.reports.guests') }}" class="btn btn-green">⬇ تصدير الضيوف</a>
  <a href="{{ route('admin.reports.leads') }}" class="btn btn-green">⬇ تصدير اهتمامات العملاء</a>
  <a href="{{ route('admin.reports.messages') }}" class="btn btn-green">⬇ تصدير الرسائل</a>
</div>

<h2 class="section-sub">الضيوف والحضور</h2>
<div class="stats-grid">
  <div class="stat"><b>{{ $guests['total'] }}</b><span>إجمالي المدعوين</span></div>
  <div class="stat"><b>{{ $guests['confirmed'] }}</b><span>أكّدوا الحضور</span></div>
  <div class="stat"><b>{{ $guests['declined'] }}</b><span>اعتذروا</span></div>
  <div class="stat"><b>{{ $guests['pending'] }}</b><span>لم يردّوا</span></div>
  <div class="stat accent"><b>{{ $guests['attended'] }}</b><span>حضروا فعليًا</span></div>
  <div class="stat"><b>{{ $guests['awaited'] }}</b><span>لم يصلوا</span></div>
  <div class="stat"><b>{{ $guests['cancelled'] }}</b><span>ملغاة</span></div>
</div>

<div class="report-cols">
  <div class="report-box">
    <h3>الضيوف حسب النوع</h3>
    <table class="data mini">
      @foreach($byType as $row)
        <tr><td>{{ $row['label'] }}</td><td class="num">{{ $row['count'] }}</td></tr>
      @endforeach
    </table>
  </div>

  <div class="report-box">
    <h3>الاهتمامات الأكثر طلبًا</h3>
    @if($leadsTotal === 0)
      <p class="follow-empty">لا توجد اهتمامات مسجلة بعد.</p>
    @else
      <table class="data mini">
        @foreach($byInterest as $row)
          <tr>
            <td>{{ $row['label'] }}</td>
            <td class="num">{{ $row['count'] }}</td>
            <td class="bar-cell">
              <span class="bar" style="width: {{ $leadsTotal ? round($row['count'] / $leadsTotal * 100) : 0 }}%"></span>
            </td>
          </tr>
        @endforeach
      </table>
    @endif
  </div>

  <div class="report-box">
    <h3>درجات الاهتمام</h3>
    <table class="data mini">
      @foreach($byScore as $row)
        <tr><td>{{ $row['label'] }}</td><td class="num">{{ $row['count'] }}</td></tr>
      @endforeach
    </table>
  </div>

  <div class="report-box">
    <h3>الاهتمامات حسب المنطقة</h3>
    <table class="data mini">
      @foreach($byZone as $zone)
        <tr><td>{{ $zone->name }}</td><td class="num">{{ $zone->leads_count }}</td></tr>
      @endforeach
    </table>
  </div>

  <div class="report-box">
    <h3>الرسائل حسب الحالة</h3>
    <table class="data mini">
      @foreach($messages as $row)
        <tr><td>{{ $row['label'] }}</td><td class="num">{{ $row['count'] }}</td></tr>
      @endforeach
      <tr><td><b>الإجمالي</b></td><td class="num"><b>{{ $messagesTotal }}</b></td></tr>
    </table>
  </div>
</div>
@endsection
