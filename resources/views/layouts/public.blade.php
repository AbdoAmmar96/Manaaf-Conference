<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'حفل الافتتاح') — مدينة منافع</title>
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
<body>

<header class="site-header">
  <div class="container">
    <a href="{{ route('home') }}" class="logo">
      <img src="{{ asset('brand/logo.png') }}" alt="منافع — خدمات حرفية صناعية">
      <span class="logo-tag">حفل الافتتاح</span>
    </a>
    <div class="nav-side">
      <nav class="main-nav">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">الرئيسية</a>
        <a href="{{ route('zones') }}" class="{{ request()->routeIs('zones') ? 'active' : '' }}">المناطق</a>
        <a href="{{ route('investors') }}" class="{{ request()->routeIs('investors') ? 'active' : '' }}">المستثمرون</a>
        <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">تواصل معنا</a>
        <a href="{{ route('register') }}" class="btn btn-lime">اطلب دعوتك</a>
      </nav>
      <button class="menu-toggle" id="menuToggle" aria-label="فتح القائمة" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>

<div class="backdrop" id="backdrop"></div>
<aside class="drawer" id="drawer" aria-hidden="true">
  <div class="drawer-head">
    <img src="{{ asset('brand/logo-white.png') }}" alt="منافع">
    <button class="drawer-close" id="drawerClose" aria-label="إغلاق القائمة">✕</button>
  </div>
  <nav class="drawer-nav">
    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">الرئيسية</a>
    <a href="{{ route('zones') }}" class="{{ request()->routeIs('zones') ? 'active' : '' }}">المناطق</a>
        <a href="{{ route('investors') }}" class="{{ request()->routeIs('investors') ? 'active' : '' }}">المستثمرون</a>
    <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">تواصل معنا</a>
    <a href="{{ route('register') }}" class="drawer-cta">اطلب دعوتك</a>
  </nav>
  <div class="drawer-foot">
    <a href="{{ route('login') }}">دخول الموظفين</a>
  </div>
</aside>

@yield('content')

<footer class="site-footer">
  <div class="container">
    <div class="cols">
      <div>
        <div class="logo foot-logo">
          <img src="{{ asset('brand/logo-white.png') }}" alt="منافع">
        </div>
        <p class="foot-blurb">
          حفل افتتاح مدينة الخدمات الحرفية والصناعية — مشروع تطوير وتنفيذ شركة البوابة الغربية للمقاولات العامة المحدودة.
        </p>
      </div>
      <div>
        <h4>روابط</h4>
        <a href="{{ route('home') }}">الرئيسية</a>
        <a href="{{ route('register') }}">طلب حضور</a>
        <a href="{{ route('zones') }}">مناطق المعرض</a>
        <a href="{{ route('investors') }}">المستثمرون</a>
        <a href="{{ route('contact') }}">تواصل معنا</a>
      </div>
      <div>
        <h4>الموقع الرسمي</h4>
        <a href="https://manafi.sa" target="_blank" rel="noopener">manafi.sa</a>
        <a href="https://manafi.sa/invest" target="_blank" rel="noopener">الفرص الاستثمارية</a>
      </div>
    </div>
    <div class="credit">
      © {{ date('Y') }} مدينة منافع الحرفية المتكاملة — تطوير وتنفيذ: شركة البوابة الغربية للمقاولات العامة المحدودة.<br>
      تصميم وتطوير الموقع: <a href="https://bp-eg.com/" target="_blank" rel="noopener" class="credit-link">شركة شريك الأعمال لتقنية المعلومات</a>
    </div>
  </div>
</footer>

<script>
(function () {
  var t = document.getElementById('menuToggle'),
      d = document.getElementById('drawer'),
      b = document.getElementById('backdrop'),
      c = document.getElementById('drawerClose');
  if (!t) return;
  function open()  {
    d.classList.add('open'); b.classList.add('open');
    document.body.classList.add('drawer-open');
    t.setAttribute('aria-expanded', 'true'); d.setAttribute('aria-hidden', 'false');
  }
  function close() {
    d.classList.remove('open'); b.classList.remove('open');
    document.body.classList.remove('drawer-open');
    t.setAttribute('aria-expanded', 'false'); d.setAttribute('aria-hidden', 'true');
  }
  t.addEventListener('click', open);
  c.addEventListener('click', close);
  b.addEventListener('click', close);
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
  d.querySelectorAll('a').forEach(function (a) { a.addEventListener('click', close); });
})();
</script>

@yield('scripts')
</body>
</html>
