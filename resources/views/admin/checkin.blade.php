@extends('layouts.admin')

@section('title', 'Check-in')

@section('content')
<h1 class="page-title">Check-in — تسجيل دخول الضيوف</h1>

<div class="checkin-grid">
  <div class="scan-panel">
    <h3>📷 الماسح بالكاميرا</h3>
    <div id="qr-reader"></div>
    <p id="cam-hint" class="result-empty">وجّه الكاميرا نحو كود الضيف…</p>
  </div>

  <div class="scan-panel">
    <h3>🔍 بحث يدوي (لو الضيف نسي الكود)</h3>
    <input type="text" id="manualSearch" class="search-input" placeholder="ابحث بالاسم أو رقم الجوال…" autocomplete="off">
    <div id="results">
      <p class="result-empty">امسح كودًا أو ابحث بالاسم لعرض بيانات الضيف وتسجيل دخوله</p>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/html5-qrcode.min.js') }}"></script>
<script>
(function () {
  var csrf = document.querySelector('meta[name=csrf-token]').content;
  var results = document.getElementById('results');
  var lastTok = '', lastAt = 0;

  function esc(s) { return (s || '').toString().replace(/</g, '&lt;'); }
  function vip(g) { return g.type === 'vip' ? ' <span class="badge badge-vip">VIP</span>' : ''; }

  function render(list) {
    if (!list.length) {
      results.innerHTML = '<div class="big-result err"><h3>غير موجود</h3><p>الكود غير صحيح أو الضيف غير مسجل — جرّب البحث بالاسم.</p></div>';
      return;
    }
    results.innerHTML = list.map(function (g) {
      if (g.attendance === 'attended') {
        return '<div class="big-result err"><h3>⚠ تم تسجيل الدخول مسبقًا</h3>'
          + '<p style="font-size:1.05rem"><b>' + esc(g.name) + '</b>' + vip(g) + '</p>'
          + '<p>الساعة ' + esc(g.checked_in_at) + (g.checked_in_by ? ' — بواسطة ' + esc(g.checked_in_by) : '') + '</p></div>';
      }
      if (g.attendance === 'cancelled') {
        return '<div class="big-result err"><h3>الدعوة ملغاة</h3><p>' + esc(g.name) + '</p></div>';
      }
      return '<div class="guest-hit"><div class="info"><b>' + esc(g.name) + '</b>' + vip(g)
        + '<span>' + esc(g.organization) + (g.position ? ' — ' + esc(g.position) : '') + '</span>'
        + '<span>' + esc(g.mobile) + ' · ' + esc(g.type_label) + ' · ' + esc(g.rsvp_label) + '</span></div>'
        + '<button class="btn-checkin" onclick="confirmGuest(' + g.id + ')">تسجيل الدخول</button></div>';
    }).join('');
  }

  function lookup(params) {
    return fetch('{{ route('admin.checkin.lookup') }}?' + params)
      .then(function (r) { return r.json(); })
      .then(function (d) { return d.guests; });
  }

  window.confirmGuest = function (id) {
    fetch('{{ route('admin.checkin.confirm') }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
      body: JSON.stringify({ guest_id: id })
    })
    .then(function (r) { return r.json(); })
    .then(function (d) {
      var g = d.guest;
      if (d.status === 'ok') {
        results.innerHTML = '<div class="big-result ok"><h3>✓ تم تسجيل الدخول</h3>'
          + '<p style="font-size:1.1rem"><b>' + esc(g.name) + '</b>' + vip(g) + '</p>'
          + '<p>الساعة ' + esc(g.checked_in_at) + '</p></div>';
      } else {
        render([g]);
      }
    });
  };

  var t;
  document.getElementById('manualSearch').addEventListener('input', function (e) {
    clearTimeout(t);
    var q = e.target.value.trim();
    if (q.length < 2) return;
    t = setTimeout(function () {
      lookup('q=' + encodeURIComponent(q)).then(render);
    }, 350);
  });

  try {
    var scanner = new Html5Qrcode('qr-reader');
    scanner.start(
      { facingMode: 'environment' },
      { fps: 10, qrbox: { width: 240, height: 240 } },
      function (txt) {
        var m = txt.match(/\/scan\/([A-Za-z0-9]+)/);
        var tok = m ? m[1] : txt;
        if (tok === lastTok && Date.now() - lastAt < 4000) return;
        lastTok = tok; lastAt = Date.now();
        lookup('token=' + encodeURIComponent(tok)).then(render);
      }
    ).catch(function () {
      document.getElementById('cam-hint').textContent = 'تعذّر فتح الكاميرا — تأكد من فتح الصفحة عبر HTTPS ومنح الإذن، أو استخدم البحث اليدوي.';
    });
  } catch (e) {
    document.getElementById('cam-hint').textContent = 'الكاميرا غير مدعومة هنا — استخدم البحث اليدوي.';
  }
})();
</script>
@endsection
