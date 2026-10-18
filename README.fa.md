### زبان

[<img src="https://img.shields.io/badge/ReadMe(English)-018EF5.svg?style=for-the-badge&logo=ReadMe&logoColor=white">](README.md)
[<img src="https://img.shields.io/badge/ReadMe(فارسی)-018EF5.svg?style=for-the-badge&logo=ReadMe&logoColor=white" title="برای فارسی شدن این فایل کلیک کنید">](README.fa.md)

### تکنولوژی‌های استفاده شده

![Laravel](https://img.shields.io/badge/Laravel-FF2D20.svg?style=for-the-badge&logo=Laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-4E56A6.svg?style=for-the-badge&logo=Livewire&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4.svg?style=for-the-badge&logo=PHP&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3.svg?style=for-the-badge&logo=Bootstrap&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E.svg?style=for-the-badge&logo=JavaScript&logoColor=black)
![MySQL](https://img.shields.io/badge/MySQL-4479A1.svg?style=for-the-badge&logo=MySQL&logoColor=white)
![SQLite](https://img.shields.io/badge/SQLite-003B57.svg?style=for-the-badge&logo=SQLite&logoColor=white)
![Redis](https://img.shields.io/badge/Redis-FF4438.svg?style=for-the-badge&logo=Redis&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-646CFF.svg?style=for-the-badge&logo=Vite&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC.svg?style=for-the-badge&logo=tailwind-css&logoColor=white)

# نرم‌افزار مدیریت فروشگاه (POS و انبارداری)

نرم‌افزار جامع مدیریت فروشگاه مبتنی بر وب با استفاده از فریم‌ورک Laravel 12 و Livewire 4. این سیستم تمامی نیازهای فروش، انبارداری، خرید، مدیریت مشتریان و تامین‌کنندگان، صندوق، هزینه‌ها، گزارش‌گیری و بسیاری موارد دیگر را پوشش می‌دهد.

## ✨ امکانات

- **فروش سریع (POS)** - رابط کاربری ساده و سریع برای ثبت فروش
- **مدیریت کالا و محصولات** - افزودن، ویرایش و ردیابی محصولات همراه با دسته‌بندی، برند، تصاویر و بارکد
- **مدیریت انبار و موجودی** - پشتیبانی از چند انبار، ردیابی موجودی، انتقال بین انبارها و اصلاح موجودی
- **مدیریت خرید** - صدور فاکتور خرید، مدیریت تامین‌کنندگان و پیگیری خریدها
- **مدیریت فروش** - ثبت و پیگیری فروش‌ها، فاکتورها و تاریخچه فروش
- **مدیریت مشتریان** - پروفایل مشتریان، نقش‌ها، حساب کاربری، امتیازدهی و تاریخچه تراکنش‌ها
- **مدیریت تامین‌کنندگان** - اطلاعات تامین‌کنندگان و تاریخچه خرید از آن‌ها
- **مدیریت انبارها** - مدیریت چندین انبار با موجودی جداگانه برای هر انبار
- **مدیریت صندوق** - ردیابی تراکنش‌های نقدی و موجودی صندوق
- **مدیریت هزینه‌ها** - ثبت و دسته‌بندی هزینه‌های کسب‌وکار
- **مدیریت پرسنل** - حضور و غیاب، حقوق و دستمزد و سوابق کارکنان
- **گزارش‌گیری و تحلیل** - گزارش فروش، گزارش مالی و آمارهای کاربردی
- **مدیریت کاربران و دسترسی‌ها** - سیستم سطح دسترسی مبتنی بر نقش و مجوزها
- **سفارشات آنلاین** - اتصال به API فروشگاه آنلاین
- **سیستم پیام‌رسانی داخلی** - ارسال پیام بین کاربران
- **یادآوری‌ها و هشدارها** - مدیریت وظایف و اعلان‌های سیستم
- **پشتیبانی چندزبانه** - فارسی و انگلیسی
- **تولید بارکد و QR Code** - تولید بارکد و کیوآر کد برای محصولات

## 📋 پیش‌نیازها

قبل از نصب، اطمینان حاصل کنید که نرم‌افزارهای زیر روی سیستم شما نصب شده‌اند:

- [PHP](https://www.php.net/) نسخه ۸.۲ یا بالاتر
- [Composer](https://getcomposer.org/) نسخه ۲ یا بالاتر
- [Node.js](https://nodejs.org/) نسخه ۱۸ یا بالاتر
- [NPM](https://www.npmjs.com/) یا [Yarn](https://yarnpkg.com/)
- [MySQL](https://www.mysql.com/) نسخه ۸.۰ یا بالاتر یا [SQLite](https://www.sqlite.org/)
- [Redis](https://redis.io/) (اختیاری ولی برای صف‌ها و کش توصیه می‌شود)

## 🚀 نصب و راه‌اندازی

اپلیکیشن لاراول در پوشه `laravel-app/` قرار دارد.

### روش اول: نصب خودکار (توصیه شده)

#### برای لینوکس / مک‌اواس

```bash
chmod +x setup-linux.sh
./setup-linux.sh
```

#### برای ویندوز

فایل `setup-windows.bat` را اجرا کنید یا روی آن دابل کلیک نمایید.

### روش دوم: نصب دستی

```bash
cd laravel-app
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
```

## ⚙️ تنظیمات

فایل `laravel-app/.env` را باز کرده و تنظیمات پایگاه داده را وارد کنید:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=نام_پایگاه_داده
DB_USERNAME=نام_کاربری
DB_PASSWORD=رمز_عبور
```

## 🗄️ راه‌اندازی پایگاه داده

```bash
cd laravel-app
php artisan migrate
```

یا همراه با داده‌های نمونه:

```bash
cd laravel-app
php artisan migrate:fresh --seed
```

## 🏃 اجرای برنامه

```bash
cd laravel-app
composer run dev
```

## 📁 ساختار پروژه

```
.
├── laravel-app/
├── setup-linux.sh
├── setup-windows.bat
├── README.md
└── README.fa.md
```

## 🤝 مشارکت

از مشارکت شما استقبال می‌شود. Fork کنید، شاخه بسازید، تغییرات را commit و PR ارسال کنید.

## 🙏 تشکر و قدردانی

- [Laravel](https://laravel.com/) - فریم‌ورک PHP
- [Livewire](https://livewire.laravel.com/) - فریم‌ورک فول‌استک برای لاراول
- [Tabler](https://tabler.io/) - قالب داشبورد حرفه‌ای
- [Bootstrap](https://getbootstrap.com/) - فریم‌ورک فرانت‌اند

## 📄 مجوز

این پروژه تحت مجوز [MIT](https://opensource.org/licenses/MIT) منتشر شده است.