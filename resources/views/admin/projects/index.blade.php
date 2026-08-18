@extends('layouts.admin')

@section('title', 'المشاريع')

@section('content')
<h1 class="page-title">المشاريع</h1>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif

<p class="page-hint">
  كل مشروع يُربط بمجال اهتمام واحد أو أكثر. الفائدة: حين يسجّل عميل اهتمامه
  بـ«مستودعات» مثلًا، يعرف فريق المبيعات فورًا أي المشاريع تناسبه.
  <a href="{{ route('admin.interests.index') }}">إدارة مجالات الاهتمام</a>
</p>

<details class="panel-box">
  <summary>+ إضافة مشروع جديد</summary>
  <form method="POST" action="{{ route('admin.projects.store') }}" class="box-body">
    @csrf
    <div class="form-grid">
      <div class="field"><label>اسم المشروع *</label><input name="name" value="{{ old('name') }}" required></div>
      <div class="field">
        <label>الحالة *</label>
        <select name="status" required>
          @foreach($statuses as $st)
            <option value="{{ $st->value }}" @selected(old('status') === $st->value)>{{ $st->labelAr() }}</option>
          @endforeach
        </select>
      </div>
      <div class="field"><label>الموقع</label><input name="location" value="{{ old('location') }}"></div>
      <div class="field"><label>المساحة</label><input name="area" value="{{ old('area') }}" placeholder="12,000 م²"></div>
      <div class="field"><label>عدد الوحدات</label><input name="units" value="{{ old('units') }}" placeholder="٤٥ وحدة"></div>
      <div class="field">
        <label>المنطقة</label>
        <select name="zone_id">
          <option value="">— بدون —</option>
          @foreach($zones as $z)
            <option value="{{ $z->id }}" @selected(old('zone_id') == $z->id)>{{ $z->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="field full"><label>وصف مختصر</label><textarea name="summary" rows="2">{{ old('summary') }}</textarea></div>
      <div class="field full"><label>التفاصيل</label><textarea name="description" rows="3">{{ old('description') }}</textarea></div>

      <div class="field full">
        <label>مجالات الاهتمام المرتبطة</label>
        <div class="check-grid">
          @foreach($interests as $i)
            <label><input type="checkbox" name="interests[]" value="{{ $i->id }}"
              @checked(in_array($i->id, old('interests', []))) > {{ $i->name }}</label>
          @endforeach
        </div>
      </div>
      <div class="full"><button type="submit" class="btn btn-green">إضافة المشروع</button></div>
    </div>
  </form>
</details>

<form method="GET" class="filter-bar">
  <select name="interest" onchange="this.form.submit()">
    <option value="">كل مجالات الاهتمام</option>
    @foreach($interests as $i)
      <option value="{{ $i->id }}" @selected(request('interest') == $i->id)>{{ $i->name }}</option>
    @endforeach
  </select>
  <select name="status" onchange="this.form.submit()">
    <option value="">كل الحالات</option>
    @foreach($statuses as $st)
      <option value="{{ $st->value }}" @selected(request('status') === $st->value)>{{ $st->labelAr() }}</option>
    @endforeach
  </select>
  @if(request()->hasAny(['interest', 'status']))
    <a href="{{ route('admin.projects.index') }}" class="mini-btn">إلغاء الفلترة</a>
  @endif
</form>

@forelse($projects as $project)
  <div class="lead-card">
    <div class="lead-head">
      <div>
        <b>{{ $project->name }}</b>
        @if(! $project->active)<span class="badge badge-gray">متوقف</span>@endif
        <span class="lead-meta">
          {{ $project->location ?: 'بدون موقع محدد' }}
          @if($project->zone) · {{ $project->zone->name }} @endif
        </span>
      </div>
      <div class="lead-head-side">
        <span class="badge badge-green">{{ $project->status->labelAr() }}</span>
      </div>
    </div>

    @if($project->summary)<div class="msg-body">{{ $project->summary }}</div>@endif

    <div class="lead-tags">
      @forelse($project->interests as $i)
        <span class="tag">{{ $i->name }}</span>
      @empty
        <span class="tag tag-warn">غير مرتبط بأي مجال اهتمام</span>
      @endforelse
    </div>

    <div class="lead-facts">
      <span><b>المساحة:</b> {{ $project->area ?: '—' }}</span>
      <span><b>الوحدات:</b> {{ $project->units ?: '—' }}</span>
      <span><b>الترتيب:</b> {{ $project->sort }}</span>
    </div>

    <details class="lead-edit">
      <summary>تعديل المشروع</summary>
      <div class="box-body">
        <form method="POST" action="{{ route('admin.projects.update', $project) }}">
          @csrf @method('PATCH')
          <div class="form-grid">
            <div class="field"><label>الاسم *</label><input name="name" value="{{ $project->name }}" required></div>
            <div class="field">
              <label>الحالة *</label>
              <select name="status" required>
                @foreach($statuses as $st)
                  <option value="{{ $st->value }}" @selected($project->status === $st)>{{ $st->labelAr() }}</option>
                @endforeach
              </select>
            </div>
            <div class="field"><label>الموقع</label><input name="location" value="{{ $project->location }}"></div>
            <div class="field"><label>المساحة</label><input name="area" value="{{ $project->area }}"></div>
            <div class="field"><label>عدد الوحدات</label><input name="units" value="{{ $project->units }}"></div>
            <div class="field">
              <label>المنطقة</label>
              <select name="zone_id">
                <option value="">— بدون —</option>
                @foreach($zones as $z)
                  <option value="{{ $z->id }}" @selected($project->zone_id == $z->id)>{{ $z->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="field"><label>الترتيب</label><input type="number" name="sort" value="{{ $project->sort }}" min="0" max="999"></div>
            <div class="field full"><label>وصف مختصر</label><textarea name="summary" rows="2">{{ $project->summary }}</textarea></div>
            <div class="field full"><label>التفاصيل</label><textarea name="description" rows="3">{{ $project->description }}</textarea></div>
            <div class="field full">
              <label>مجالات الاهتمام المرتبطة</label>
              <div class="check-grid">
                @foreach($interests as $i)
                  <label><input type="checkbox" name="interests[]" value="{{ $i->id }}"
                    @checked($project->interests->contains($i->id)) > {{ $i->name }}</label>
                @endforeach
              </div>
            </div>
            <div class="field full">
              <label class="switch"><input type="checkbox" name="active" value="1" @checked($project->active)> المشروع مفعّل</label>
            </div>
            <div class="full"><button type="submit" class="btn btn-green">حفظ التعديلات</button></div>
          </div>
        </form>

        <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" class="inline-form"
              onsubmit="return confirm('حذف مشروع «{{ $project->name }}» نهائيًا؟')">
          @csrf @method('DELETE')
          <button type="submit" class="mini-btn danger">حذف المشروع</button>
        </form>
      </div>
    </details>
  </div>
@empty
  <div class="table-wrap" style="padding:2.5rem;text-align:center;color:var(--muted)">
    لا توجد مشاريع بعد — أضف أول مشروع من الأعلى.
  </div>
@endforelse
@endsection
