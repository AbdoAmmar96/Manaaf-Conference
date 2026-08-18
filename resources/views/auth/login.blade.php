@extends('layouts.public')

@section('title', 'دخول الموظفين')

@section('content')
<div class="narrow-page">
  <div class="panel fade-up">
    <img src="{{ asset('brand/logo.png') }}" alt="منافع" style="height:78px;margin:0 auto 1.2rem">
    <h1>دخول الموظفين</h1>
    <p class="sub">لوحة إدارة المؤتمر وحفل الافتتاح</p>

    <form method="POST" action="{{ route('login.attempt') }}">
      @csrf
      <div class="field">
        <label for="email">البريد الإلكتروني</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
      </div>
      <div class="field">
        <label for="password">كلمة المرور</label>
        <input type="password" id="password" name="password" required>
        @error('email') <div class="form-error">{{ $message }}</div> @enderror
      </div>
      <button type="submit" class="btn btn-green btn-block">تسجيل الدخول</button>
    </form>
  </div>
</div>
@endsection
