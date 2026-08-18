# النشر — نظام مؤتمر منافع

**الموقع منشور ويعمل على:** <https://conference.manafi.sa>

المشروع: Laravel 13 + MySQL — يعمل على نفس سيرفر manafi.sa بدون تكلفة إضافية.

---

## التخطيط المُطبَّق على الخادم

ملفات لارافيل **خارج جذر الويب** عمدًا — جذر النطاق الفرعي لا يحوي إلا ما
يجب أن يكون عامًا:

```
~/laravel/conference/                              ← التطبيق كاملًا (خارج الويب)
    app/ config/ database/ resources/ routes/
    vendor/  .env  storage/  bootstrap/

~/domains/manafi.sa/public_html/conference/        ← جذر النطاق الفرعي
    index.php   ← معدَّل ليشير إلى ~/laravel/conference
    .htaccess   ← ملف لارافيل القياسي
    css/ js/ brand/ favicon.ico robots.txt
```

**لماذا؟** التخطيط الشائع (وضع المشروع كاملًا داخل مجلد النطاق مع `.htaccess`
يوجّه إلى `public/`) يعتمد على ملف واحد لإخفاء `.env`. لو تعطّل `mod_rewrite`
أو حُذف الملف، يصبح `https://conference.manafi.sa/.env` مفتوحًا للجميع —
أي كلمة مرور قاعدة البيانات ومفتاح التطبيق مكشوفان. التخطيط الحالي يجعل ذلك
مستحيلًا بنيويًا.

`index.php` في جذر الويب يحسب مسار التطبيق هكذا:

```php
$app_base = dirname(__DIR__, 4).'/laravel/conference';
```

> إن نقلت المشروع لمسار مختلف العمق، عدّل الرقم `4` أو ضع مسارًا مطلقًا.

---

## بيانات الخادم

| العنصر | القيمة |
|---|---|
| SSH | `147.79.103.136` منفذ `65002` — المستخدم `u876452760` |
| PHP | 8.3.31 (كل الإضافات المطلوبة مفعّلة) |
| قاعدة البيانات | MariaDB 11.8 — `u876452760_conferance` |
| SSL | مفعّل ✅ (إجباري لعمل كاميرا تسجيل الدخول) |

---

## تحديث الموقع بعد تعديل الكود

من جهازك، داخل مجلد المشروع:

```bash
# ١) رفع الملفات (بدون vendor و .env وقاعدة البيانات)
rsync -az --delete -e "ssh -p 65002" \
  --exclude='.git/' --exclude='vendor/' --exclude='node_modules/' \
  --exclude='.env' --exclude='database/*.sqlite' \
  --exclude='storage/logs/*.log' --exclude='storage/framework/cache/data/*' \
  --exclude='storage/framework/sessions/*' --exclude='storage/framework/views/*' \
  --exclude='bootstrap/cache/*.php' \
  ./ u876452760@147.79.103.136:~/laravel/conference/

# ٢) نسخ ملفات public إلى جذر الويب (لو تغيّرت css/js/brand)
ssh -p 65002 u876452760@147.79.103.136 \
  'cp -r ~/laravel/conference/public/. ~/domains/manafi.sa/public_html/conference/'
```

> ⚠️ الخطوة ٢ تعيد كتابة `index.php` بالنسخة الأصلية. أعِد بعدها سطر
> `$app_base` أو استثنِ الملف: `cp -r --exclude=index.php` غير مدعوم في cp،
> فاستخدم rsync مع `--exclude='index.php'`.

ثم على الخادم:

```bash
cd ~/laravel/conference
composer install --no-dev --optimize-autoloader   # عند تغيّر composer.json
php artisan migrate --force                        # عند وجود migrations جديدة
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

**مهم:** بعد أي تعديل على `.env` نفّذ `php artisan config:cache` مجددًا،
وإلا بقيت الإعدادات القديمة في الكاش.

---

## حسابات الدخول

الرابط: <https://conference.manafi.sa/admin/login>

| الدور | البريد | كلمة المرور |
|---|---|---|
| مدير النظام | admin@manafi.sa | ChangeMe@2026 |
| استقبال | reception@manafi.sa | ChangeMe@2026 |
| مبيعات | sales@manafi.sa | ChangeMe@2026 |

⚠️ **غيّرها فورًا** من شاشة المستخدمين — كلمات المرور هذه مكتوبة في الكود.
راجع [docs/الصلاحيات.md](docs/الصلاحيات.md) لصلاحيات كل دور.

---

## قبل الحفل

- [ ] تغيير كلمات مرور الحسابات الثلاثة
- [ ] تعبئة بيانات التواصل (تظهر في صفحة «تواصل معنا» عند تعبئتها فقط):
  ```bash
  cd ~/laravel/conference && php artisan tinker --execute="
    App\Models\Setting::set('contact_phone', '...');
    App\Models\Setting::set('contact_whatsapp', '...');
    App\Models\Setting::set('contact_email', '...');"
  ```
- [ ] ضبط تاريخ الحفل الفعلي (يغيّر العد التنازلي):
  `App\Models\Setting::set('event_date', '2026-10-15 19:00:00');`
- [ ] حذف الضيوف التجريبيين الثلاثة
- [ ] طباعة أكواد QR للمناطق الستة من شاشة «المناطق»
- [ ] تجربة الماسح بالكاميرا على جوال حقيقي

---

تصميم وتطوير: **[شركة شريك الأعمال لتقنية المعلومات](https://bp-eg.com/)**
