@extends('layouts.admin')

@section('title', 'المستخدمون')

@section('content')
<h1 class="page-title">المستخدمون</h1>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-error">{{ $errors->first() }}</div>@endif

<p class="page-hint">
  كل موظف له دور واحد يحدد ما يراه في النظام — راجع
  <a href="{{ route('admin.permissions') }}">جدول الصلاحيات</a> للتفاصيل.
  تعطيل الحساب يمنع صاحبه من الدخول فورًا دون حذف بياناته أو سجل عمله.
</p>

<details class="panel-box">
  <summary>+ إضافة مستخدم جديد</summary>
  <form method="POST" action="{{ route('admin.users.store') }}" class="box-body">
    @csrf
    <div class="form-grid">
      <div class="field"><label>الاسم *</label><input name="name" value="{{ old('name') }}" required></div>
      <div class="field"><label>البريد الإلكتروني *</label><input type="email" name="email" value="{{ old('email') }}" dir="ltr" required></div>
      <div class="field"><label>رقم الجوال</label><input name="phone" value="{{ old('phone') }}" inputmode="tel"></div>
      <div class="field">
        <label>الدور *</label>
        <select name="role" required>
          @foreach($roles as $role)
            <option value="{{ $role->value }}" @selected(old('role') === $role->value)>{{ $role->labelAr() }}</option>
          @endforeach
        </select>
      </div>
      <div class="field"><label>كلمة المرور * (٨ أحرف فأكثر)</label><input type="password" name="password" required></div>
      <div class="field"><label>تأكيد كلمة المرور *</label><input type="password" name="password_confirmation" required></div>
      <div class="full"><button type="submit" class="btn btn-green">إضافة المستخدم</button></div>
    </div>
  </form>
</details>

<div class="table-wrap">
  <table class="data">
    <thead>
      <tr><th>الاسم</th><th>البريد</th><th>الجوال</th><th>الدور</th><th>الحالة</th><th></th></tr>
    </thead>
    <tbody>
      @foreach($users as $u)
        <tr>
          <td><b>{{ $u->name }}</b>@if($u->id === auth()->id())<span class="badge badge-green">أنت</span>@endif</td>
          <td dir="ltr" style="text-align:right;font-size:.8rem">{{ $u->email }}</td>
          <td dir="ltr" style="text-align:right;font-size:.8rem">{{ $u->phone ?? '—' }}</td>
          <td><span class="badge badge-vip">{{ $u->role->labelAr() }}</span></td>
          <td>
            @if($u->active)
              <span class="badge badge-green">نشط</span>
            @else
              <span class="badge badge-red">معطّل</span>
            @endif
          </td>
          <td>
            <details class="row-edit">
              <summary class="mini-btn">تعديل</summary>
              <form method="POST" action="{{ route('admin.users.update', $u) }}" class="box-body">
                @csrf @method('PATCH')
                <div class="form-grid">
                  <div class="field"><label>الاسم *</label><input name="name" value="{{ $u->name }}" required></div>
                  <div class="field"><label>البريد *</label><input type="email" name="email" value="{{ $u->email }}" dir="ltr" required></div>
                  <div class="field"><label>الجوال</label><input name="phone" value="{{ $u->phone }}" inputmode="tel"></div>
                  <div class="field">
                    <label>الدور *</label>
                    <select name="role" required @disabled($u->id === auth()->id())>
                      @foreach($roles as $role)
                        <option value="{{ $role->value }}" @selected($u->role === $role)>{{ $role->labelAr() }}</option>
                      @endforeach
                    </select>
                    @if($u->id === auth()->id())
                      <small class="field-hint">لا يمكنك تغيير دورك بنفسك.</small>
                    @endif
                  </div>
                  <div class="field"><label>كلمة مرور جديدة (اتركها فارغة للإبقاء)</label><input type="password" name="password"></div>
                  <div class="field"><label>تأكيد كلمة المرور</label><input type="password" name="password_confirmation"></div>
                  <div class="field full">
                    <label class="switch">
                      <input type="checkbox" name="active" value="1" @checked($u->active) @disabled($u->id === auth()->id())>
                      الحساب نشط
                    </label>
                  </div>
                  <div class="full"><button type="submit" class="btn btn-green">حفظ</button></div>
                </div>
              </form>
            </details>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
