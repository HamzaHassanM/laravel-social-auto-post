# التثبيت والمتطلبات

## المتطلبات

- **PHP**: 8.1 أو أحدث
- **Laravel**: 8.x, 9.x, 10.x, 11.x, 12.x, أو 13.x

## التثبيت

يمكنك تثبيت الحزمة عبر composer:

```bash
composer require hamzahassanm/laravel-social-auto-post
```

## نشر ملفات الإعدادات

بعد تثبيت الحزمة، تحتاج إلى نشر ملف الإعدادات. قم بتشغيل الأمر التالي:

```bash
php artisan vendor:publish --provider="HamzaHassanM\LaravelSocialAutoPost\SocialShareServiceProvider" --tag="autopost"
```

سيؤدي ذلك إلى إنشاء ملف `config/autopost.php` في تطبيقك حيث يمكنك إدارة الإعدادات الافتراضية وبيانات الاعتماد لجميع المنصات المدعومة.
