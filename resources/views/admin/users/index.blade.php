@extends('layouts.admin')

@section('title', 'المستخدمون')

@section('content')
<h1 class="page-title">المستخدمون</h1>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any() && ! old('editing'))<div class="alert alert-error">{{ $errors->first() }}</div>@endif

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
        @php($self = $u->id === auth()->id())
        {{-- الحقل المخفي editing يعيد فتح نافذة المستخدم نفسه بعد خطأ تحقّق --}}
        @php($editing = old('editing') == $u->id)
        <tr>
          <td><b>{{ $u->name }}</b>@if($self)<span class="badge badge-green">أنت</span>@endif</td>
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
            <button type="button" class="mini-btn" data-open="#edit-user-{{ $u->id }}">تعديل</button>

            <dialog id="edit-user-{{ $u->id }}" class="modal" @if($editing) data-autoopen @endif>
              <div class="modal-head">
                <h3 class="modal-title">تعديل «{{ $u->name }}»</h3>
                <button type="button" class="modal-close" data-close aria-label="إغلاق">✕</button>
              </div>

              <form method="POST" action="{{ route('admin.users.update', $u) }}" class="modal-body">
                @csrf @method('PATCH')
                <input type="hidden" name="editing" value="{{ $u->id }}">

                @if($editing && $errors->any())
                  <div class="alert alert-error">{{ $errors->first() }}</div>
                @endif

                <div class="form-grid">
                  <div class="field"><label>الاسم *</label><input name="name" value="{{ $editing ? old('name') : $u->name }}" required></div>
                  <div class="field"><label>البريد *</label><input type="email" name="email" value="{{ $editing ? old('email') : $u->email }}" dir="ltr" required></div>
                  <div class="field"><label>الجوال</label><input name="phone" value="{{ $editing ? old('phone') : $u->phone }}" inputmode="tel"></div>
                  <div class="field">
                    <label>الدور *</label>
                    <select name="role" required @disabled($self)>
                      @foreach($roles as $role)
                        <option value="{{ $role->value }}" @selected($editing ? old('role') === $role->value : $u->role === $role)>{{ $role->labelAr() }}</option>
                      @endforeach
                    </select>
                    @if($self)
                      <small class="field-hint">لا يمكنك تغيير دورك بنفسك.</small>
                    @endif
                  </div>
                  <div class="field"><label>كلمة مرور جديدة</label><input type="password" name="password" autocomplete="new-password"></div>
                  <div class="field"><label>تأكيد كلمة المرور</label><input type="password" name="password_confirmation" autocomplete="new-password"></div>
                  <div class="field full">
                    <label class="switch">
                      <input type="checkbox" name="active" value="1" @checked($editing ? old('active') : $u->active) @disabled($self)>
                      الحساب نشط
                    </label>
                  </div>
                  <div class="full modal-actions">
                    <button type="submit" class="btn btn-green">حفظ</button>
                    <button type="button" class="mini-btn" data-close>إلغاء</button>
                  </div>
                </div>
              </form>
            </dialog>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection

@section('scripts')
<script>
(function () {
  document.querySelectorAll('[data-open]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var dlg = document.querySelector(btn.dataset.open);
      if (dlg) dlg.showModal();
    });
  });

  document.querySelectorAll('dialog.modal').forEach(function (dlg) {
    dlg.querySelectorAll('[data-close]').forEach(function (btn) {
      btn.addEventListener('click', function () { dlg.close(); });
    });

    // النقر على الخلفية خارج النافذة يغلقها
    dlg.addEventListener('click', function (e) {
      if (e.target === dlg) dlg.close();
    });

    // فتح تلقائي بعد خطأ تحقّق ليرى المدير الخطأ في مكانه
    if (dlg.hasAttribute('data-autoopen')) dlg.showModal();
  });
})();
</script>
@endsection
