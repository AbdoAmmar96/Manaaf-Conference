@extends('layouts.admin')

@section('title', 'تسجيل الضيوف')

@section('content')
<h1 class="page-title">تسجيل الضيوف</h1>

<div class="checkin-grid">
  <div class="scan-panel">
    <h3>📷 الماسح بالكاميرا</h3>
    <div id="qr-reader"></div>
    <p id="cam-hint" class="result-empty">جارٍ فتح الكاميرا…</p>
    <div class="scan-tools">
      <button type="button" id="switchCam" class="mini-btn" style="display:none">🔄 تبديل الكاميرا</button>
      <label class="mini-btn" for="qrFile">🖼️ رفع صورة الكود</label>
      <input type="file" id="qrFile" accept="image/*" hidden>
    </div>
    <div id="qr-file-tmp" hidden></div>
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
  var hint = document.getElementById('cam-hint');
  var lastTok = '', lastAt = 0;

  function esc(s) { return (s || '').toString().replace(/</g, '&lt;'); }
  function vip(g) { return g.type === 'vip' ? ' <span class="badge badge-vip">VIP</span>' : ''; }
  function say(msg, isErr) {
    hint.textContent = msg;
    hint.className = isErr ? 'result-empty cam-err' : 'result-empty';
  }

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

  /* البحث اليدوي */
  var t;
  document.getElementById('manualSearch').addEventListener('input', function (e) {
    clearTimeout(t);
    var q = e.target.value.trim();
    if (q.length < 2) return;
    t = setTimeout(function () { lookup('q=' + encodeURIComponent(q)).then(render); }, 350);
  });

  /* ─── معالجة أي نص ممسوح ─── */
  function handleText(txt) {
    var m = txt.match(/\/scan\/([A-Za-z0-9]+)/);
    var tok = m ? m[1] : txt.trim();
    if (tok === lastTok && Date.now() - lastAt < 4000) return;
    lastTok = tok; lastAt = Date.now();
    say('تم قراءة الكود — جارٍ البحث…');
    lookup('token=' + encodeURIComponent(tok)).then(function (list) {
      say(list.length ? 'وجّه الكاميرا نحو كود الضيف التالي…' : 'كود غير معروف — جرّب البحث بالاسم.', !list.length);
      render(list);
    });
  }

  /*
   * صندوق المسح يجب أن يتناسب مع حجم الصورة الحقيقي.
   * كان ثابتًا ٢٤٠×٢٤٠ فكان أي كود يظهر أكبر من ذلك يقع خارج الصندوق
   * فلا يُقرأ إطلاقًا ودون أي رسالة خطأ.
   */
  function qrbox(viewW, viewH) {
    var m = Math.floor(Math.min(viewW, viewH) * 0.8);
    return { width: m, height: m };
  }

  var scanner, camIds = [], camIdx = 0;

  function startWith(spec) {
    return scanner.start(spec, { fps: 10, qrbox: qrbox }, handleText, function () {});
  }

  function bootScanner() {
    try { scanner = new Html5Qrcode('qr-reader'); }
    catch (e) { say('الكاميرا غير مدعومة في هذا المتصفح — استخدم البحث اليدوي أو رفع صورة الكود.', true); return; }

    say('جارٍ فتح الكاميرا…');
    startWith({ facingMode: 'environment' })
      .then(function () { say('وجّه الكاميرا نحو كود الضيف…'); afterStart(); })
      .catch(function (e1) {
        /* بعض الأجهزة ترفض facingMode — نجرّب الكاميرات المتاحة بالاسم */
        Html5Qrcode.getCameras().then(function (cams) {
          if (!cams.length) throw e1;
          camIds = cams.map(function (c) { return c.id; });
          camIdx = cams.length - 1;                       /* الخلفية غالبًا الأخيرة */
          return startWith(camIds[camIdx]).then(function () {
            say('وجّه الكاميرا نحو كود الضيف…'); afterStart();
          });
        }).catch(function (e2) {
          say('تعذّر فتح الكاميرا: ' + (e2 && e2.message ? e2.message : e2)
              + ' — تأكد من منح الإذن، أو استخدم البحث اليدوي أو رفع صورة الكود.', true);
        });
      });
  }

  function afterStart() {
    Html5Qrcode.getCameras().then(function (cams) {
      if (cams.length > 1) {
        camIds = cams.map(function (c) { return c.id; });
        document.getElementById('switchCam').style.display = 'inline-block';
      }
    }).catch(function () {});
  }

  document.getElementById('switchCam').addEventListener('click', function () {
    if (!camIds.length) return;
    camIdx = (camIdx + 1) % camIds.length;
    scanner.stop().then(function () {
      return startWith(camIds[camIdx]);
    }).then(function () { say('تم تبديل الكاميرا — وجّه نحو الكود…'); })
      .catch(function (e) { say('تعذّر تبديل الكاميرا: ' + e, true); });
  });

  /* بديل: رفع صورة الكود (يعمل حتى لو مُنعت الكاميرا) */
  document.getElementById('qrFile').addEventListener('change', function (e) {
    var f = e.target.files && e.target.files[0];
    if (!f) return;
    var tmp = new Html5Qrcode('qr-file-tmp', true);
    tmp.scanFileV2(f, false)
      .then(function (res) { handleText(res.decodedText); })
      .catch(function () { say('تعذّرت قراءة الكود من الصورة — جرّب صورة أوضح أو ابحث بالاسم.', true); });
    e.target.value = '';
  });

  bootScanner();
})();
</script>
@endsection
