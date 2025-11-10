# WordPress Product License Management

This repository is designed for managing and controlling product licenses in WordPress plugins, helping developers secure access and restrict usage to authorized domains only.

---

## 📌 Commit History (English)

### ✅ Commit 1 – Add basic local domain license check
A simple local domain validation was introduced. If the plugin runs on any domain other than the allowed one defined in the file, it gets instantly deactivated and execution stops.

---

### ✅ Commit 2 – Add remote domain license check with immediate stop
Domain validation is now handled via a remote server API. The current domain is checked against a server-side list, and if it’s not approved, the plugin is immediately disabled for improved security.

---

### ✅ Commit 3 – License validation on activation (security 5/10)
License validation now only occurs during plugin activation. Instead of checking on every load, the domain is verified once when the plugin is activated, and if the license is invalid, activation is blocked and the plugin is deactivated immediately.

---

### ✅ Commit 4 – Real-time license check, disable plugin and prevent execution of other code (security 7/10)
The license is now checked instantly when the plugin runs. If the current domain fails validation, the plugin is immediately disabled and the rest of its code stops executing. This ensures strict enforcement and blocks all functionality when the license is not valid.

---

### ✅ Commit 5 – Implement JWT-based license validation with 24h token caching (security 9.5/10)
License validation now uses a JWT-based system with a 24-hour cached token to reduce server calls. On each run, the token’s signature, expiration, and domain are checked locally. If invalid or expired, a new token is requested — otherwise the plugin is deactivated and the admin is alerted. This provides strong security but requires secure server-side secret key management.

---

### ✅ Commit 6 – General optimization and add 6-hour grace timeout if no response from server (security 9.6/10)
- Added 6-hour grace period with secure start/last signature tracking  
- Automatic retry every 15 minutes while in grace mode  
- Deactivate plugin if server unreachable beyond 6 hours  
- Improved token verification and HMAC integrity checks  
- Retains JWT validation and caching system (security 9.6/10)

---

## 📌 تاریخچه کامیت‌ها (فارسی)

### ✅ کامیت 1 – بررسی ساده لایسنس دامنه به‌صورت محلی
در این کامیت بررسی دامنه به‌صورت داخلی انجام می‌شود. اگر سایت روی دامنه غیرمجاز اجرا شود، افزونه بلافاصله غیرفعال شده و اجرا ادامه نمی‌یابد.

---

### ✅ کامیت 2 – اعتبارسنجی دامنه از طریق سرور با توقف فوری
در این نسخه، اعتبار دامنه از طریق API سرور بررسی می‌شود و در صورت غیرمجاز بودن دامنه، افزونه فوراً غیرفعال می‌شود تا امنیت بالاتری فراهم گردد.

---

### ✅ کامیت 3 – اعتبارسنجی هنگام فعال‌سازی افزونه (امنیت ۵ از ۱۰)
لایسنس تنها هنگام فعال‌سازی افزونه بررسی می‌شود. اگر دامنه نامعتبر باشد، افزونه در همان لحظه غیرفعال شده و فعال‌سازی تکمیل نمی‌شود.

---

### ✅ کامیت 4 – بررسی لایسنس در لحظه و جلوگیری از اجرای سایر کدها (امنیت ۷ از ۱۰)
در این مرحله، لایسنس هنگام اجرای افزونه بررسی می‌شود و در صورت عدم اعتبار، افزونه فوراً متوقف شده و اجازه اجرای هیچ بخشی از افزونه داده نمی‌شود.

---

### ✅ کامیت 5 – استفاده از JWT با کش توکن ۲۴ ساعته (امنیت ۹.۵ از ۱۰)
لایسنس با استفاده از توکن JWT بررسی می‌شود و توکن معتبر تا ۲۴ ساعت در دیتابیس ذخیره می‌گردد. اگر توکن معتبر نباشد یا منقضی شده باشد، افزونه توکن جدید درخواست کرده و در صورت عدم موفقیت، غیرفعال می‌شود. امضا، تاریخ انقضا و دامنه داخل توکن بررسی می‌شود که امنیت بالا را تضمین می‌کند؛ هرچند نگهداری امن کلید مخفی در سمت سرور ضروری است.

---

### ✅ کامیت ۶ – بهینه‌سازی کلی و افزودن مهلت موقت ۶ ساعته در صورت عدم پاسخ سرور (امنیت ۹.۶ از ۱۰)
- افزودن مهلت موقت ۶ ساعته با امضای امن برای زمان شروع و آخرین تلاش  
- تلاش خودکار هر ۱۵ دقیقه در حالت مهلت موقت  
- غیرفعال‌سازی افزونه پس از گذشت بیش از ۶ ساعت بدون پاسخ سرور  
- بهبود منطق بررسی توکن و امضای HMAC  
- حفظ ساختار اعتبارسنجی JWT و کش توکن (امنیت ۹.۶ از ۱۰)
