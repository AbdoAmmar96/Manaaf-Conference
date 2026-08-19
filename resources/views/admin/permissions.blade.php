@extends('layouts.admin')

@section('title', 'الصلاحيات')

@section('content')
<h1 class="page-title">صلاحيات الأدوار</h1>

<p class="page-hint">
  النظام يعتمد ثلاثة أدوار، ولكل مستخدم دور واحد. الصلاحيات مطبّقة على مستوى
  المسارات في الخادم — أي أن إخفاء رابط من القائمة ليس هو ما يمنع الوصول،
  بل الخادم نفسه يرفض الطلب برسالة ٤٠٣.
</p>

<div class="role-cards">
  @foreach($roles as $role)
    <div class="role-card">
      <h3>{{ $role->labelAr() }}</h3>
      <span class="role-count">{{ $counts[$role->value] ?? 0 }} مستخدم نشط</span>
      <p>{{ $summaries[$role->value] }}</p>
    </div>
  @endforeach
</div>

<h2 class="section-sub">جدول الصلاحيات التفصيلي</h2>
<div class="table-wrap">
  <table class="data perms">
    <thead>
      <tr>
        <th>الصلاحية</th>
        @foreach($roles as $role)<th class="perm-col">{{ $role->labelAr() }}</th>@endforeach
      </tr>
    </thead>
    <tbody>
      @foreach($matrix as $item)
        <tr>
          <td>
            <b>{{ $item['title'] }}</b>
            <div class="perm-desc">{{ $item['description'] }}</div>
          </td>
          @foreach($roles as $role)
            <td class="perm-col">
              @if(in_array($role->value, $item['roles'], true))
                <span class="perm-yes" title="مسموح">✓</span>
              @else
                <span class="perm-no" title="ممنوع">—</span>
              @endif
            </td>
          @endforeach
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
