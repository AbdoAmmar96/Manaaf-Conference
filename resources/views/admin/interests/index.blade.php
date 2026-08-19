@extends('layouts.admin')

@section('title', 'مجالات الاهتمام')

@section('content')
<h1 class="page-title">مجالات الاهتمام</h1>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif

<p class="page-hint">
  هذه المجالات تظهر للعميل في نموذج الطلب الاستثماري وفي صفحات المناطق،
  ويستخدمها فريق المبيعات عند تسجيل الاهتمامات. يمكنك ربط كل مجال بالمشاريع
  المناسبة له. <a href="{{ route('admin.projects.index') }}">إدارة المشاريع</a>
</p>

<details class="panel-box">
  <summary>+ إضافة مجال اهتمام جديد</summary>
  <form method="POST" action="{{ route('admin.interests.store') }}" class="box-body">
    @csrf
    <div class="form-grid">
      <div class="field"><label>اسم المجال *</label><input name="name" value="{{ old('name') }}" required placeholder="مثال: مكاتب إدارية"></div>
      <div class="field"><label>الترتيب</label><input type="number" name="sort" value="{{ old('sort', 0) }}" min="0" max="999"></div>
      <div class="field full"><label>الوصف</label><textarea name="description" rows="2">{{ old('description') }}</textarea></div>
      <div class="field full">
        <label>المشاريع المرتبطة</label>
        @if($projects->isEmpty())
          <p class="field-hint">لا توجد مشاريع بعد — أضفها من <a href="{{ route('admin.projects.index') }}">صفحة المشاريع</a>.</p>
        @else
          <div class="check-grid">
            @foreach($projects as $pr)
              <label><input type="checkbox" name="projects[]" value="{{ $pr->id }}"
                @checked(in_array($pr->id, old('projects', []))) > {{ $pr->name }}</label>
            @endforeach
          </div>
        @endif
      </div>
      <div class="full"><button type="submit" class="btn btn-green">إضافة المجال</button></div>
    </div>
  </form>
</details>

<div class="interest-grid">
  @foreach($interests as $interest)
    <div class="interest-card">
      <div class="interest-head">
        <h3>{{ $interest->name }}</h3>
        @if($interest->active)
          <span class="badge badge-green">مفعّل</span>
        @else
          <span class="badge badge-gray">معطّل</span>
        @endif
      </div>

      <p class="interest-desc">{{ $interest->description ?: 'بدون وصف' }}</p>

      <div class="interest-stats">
        <span><b>{{ $counts[$interest->id] ?? 0 }}</b> اهتمام مسجّل</span>
        <span><b>{{ $interest->projects->count() }}</b> مشروع مرتبط</span>
      </div>

      @if($interest->projects->isNotEmpty())
        <div class="lead-tags">
          @foreach($interest->projects as $pr)
            <span class="tag">{{ $pr->name }}</span>
          @endforeach
        </div>
      @endif

      <details class="lead-edit">
        <summary>تعديل المجال</summary>
        <div class="box-body">
          <form method="POST" action="{{ route('admin.interests.update', $interest) }}">
            @csrf @method('PATCH')
            <div class="form-grid">
              <div class="field full"><label>الاسم *</label><input name="name" value="{{ $interest->name }}" required></div>
              <div class="field full"><label>الوصف</label><textarea name="description" rows="2">{{ $interest->description }}</textarea></div>
              <div class="field"><label>الترتيب</label><input type="number" name="sort" value="{{ $interest->sort }}" min="0" max="999"></div>
              <div class="field">
                <label>الحالة</label>
                <label class="switch switch-box">
                  <input type="checkbox" name="active" value="1" @checked($interest->active)> مفعّل
                </label>
              </div>
              @if($projects->isNotEmpty())
                <div class="field full">
                  <label>المشاريع المرتبطة</label>
                  <div class="check-grid">
                    @foreach($projects as $pr)
                      <label><input type="checkbox" name="projects[]" value="{{ $pr->id }}"
                        @checked($interest->projects->contains($pr->id)) > {{ $pr->name }}</label>
                    @endforeach
                  </div>
                </div>
              @endif
              <div class="full"><button type="submit" class="btn btn-green btn-block">حفظ</button></div>
            </div>
          </form>

          <form method="POST" action="{{ route('admin.interests.destroy', $interest) }}" class="inline-form"
                onsubmit="return confirm('حذف مجال «{{ $interest->name }}»؟')">
            @csrf @method('DELETE')
            <button type="submit" class="mini-btn danger">حذف المجال</button>
            <span class="field-hint">المجال المستخدم في اهتمامات مسجّلة لا يُحذف — عطّله بدلًا من ذلك.</span>
          </form>
        </div>
      </details>
    </div>
  @endforeach
</div>
@endsection
