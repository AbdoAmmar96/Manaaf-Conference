<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'لوحة التحكم') — منافع</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/theme.css') }}">
<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('brand/favicon-32.png') }}">
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('brand/icon-192.png') }}">
<link rel="apple-touch-icon" href="{{ asset('brand/apple-touch-icon.png') }}">
<meta name="theme-color" content="#06782C">
</head>
<body class="admin-body">

@php($user = auth()->user())

<div class="admin-topbar">
  <div class="container">
    <a href="{{ route('admin.dashboard') }}" class="logo">
      <img src="{{ asset('brand/logo-white.png') }}" alt="منافع">
      <span class="logo-tag">إدارة الحفل</span>
    </a>

    <nav class="admin-nav">
      <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">الرئيسية</a>
      @if($user->hasRole(\App\Enums\UserRole::Admin, \App\Enums\UserRole::Reception))
        <a href="{{ route('admin.guests.index') }}" class="{{ request()->routeIs('admin.guests.*') ? 'active' : '' }}">الضيوف</a>
        <a href="{{ route('admin.checkin') }}" class="{{ request()->routeIs('admin.checkin') ? 'active' : '' }}">تسجيل دخول الضيوف</a>
      @endif
      <a href="{{ route('admin.messages.index') }}" class="{{ request()->routeIs('admin.messages.*') ? 'active' : '' }}">الرسائل</a>
      @if($user->hasRole(\App\Enums\UserRole::Admin, \App\Enums\UserRole::Sales, \App\Enums\UserRole::Reception))
        <a href="{{ route('admin.leads.index') }}" class="{{ request()->routeIs('admin.leads.*') ? 'active' : '' }}">اهتمامات العملاء</a>
      @endif
      @if($user->isAdmin())
        <a href="{{ route('admin.zones.index') }}" class="{{ request()->routeIs('admin.zones.*') ? 'active' : '' }}">المناطق</a>
        <a href="{{ route('admin.projects.index') }}" class="{{ request()->routeIs('admin.projects.*') ? 'active' : '' }}">المشاريع</a>
        <a href="{{ route('admin.interests.index') }}" class="{{ request()->routeIs('admin.interests.*') ? 'active' : '' }}">مجالات الاهتمام</a>
        <a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports') ? 'active' : '' }}">التقارير</a>
        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">المستخدمون</a>
        <a href="{{ route('admin.permissions') }}" class="{{ request()->routeIs('admin.permissions') ? 'active' : '' }}">الصلاحيات</a>
      @endif
    </nav>

    <div class="admin-user">
      <div class="admin-user-id">
        {{-- يُخفى الاسم إذا كان مطابقًا لمسمّى الدور حتى لا يتكرر --}}
        @if($user->name !== $user->role->labelAr())
          <div class="user-name">{{ $user->name }}</div>
        @endif
        <span class="role-chip">{{ $user->role->labelAr() }}</span>
      </div>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-logout">خروج</button>
      </form>
    </div>
  </div>
</div>

<main class="admin-main">
  <div class="container">
    @yield('content')
  </div>
</main>

@yield('scripts')
</body>
</html>
