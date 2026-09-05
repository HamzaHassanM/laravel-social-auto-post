# الاستخدام المتقدم (Advanced Usage)

## أحداث لارافيل (Laravel Events)

تُطلق الحزمة أحداث لارافيل (Events) أصلية أثناء دورة حياة النشر. يُعد هذا مفيدًا للغاية لتطبيقات SaaS لتحديث حالة النشر في قاعدة البيانات دون الحاجة إلى فحص مصفوفة الاستجابة يدوياً.

### الأحداث المتاحة

1. `HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostPublishing`
   - يُطلق *قبل* إرسال طلب واجهة برمجة التطبيقات (API).
   - الخصائص: `$platform`, `$method`, `$parameters`

2. `HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostPublished`
   - يُطلق *بعد* نجاح طلب واجهة برمجة التطبيقات (API).
   - الخصائص: `$platform`, `$method`, `$parameters`, `$result`

3. `HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostFailed`
   - يُطلق في حال ألقى طلب الـ API خطأ (Exception).
   - الخصائص: `$platform`, `$method`, `$parameters`, `$exception`

### مثال للاستخدام

قم بتسجيل الـ Listeners في `EventServiceProvider` الخاص بك:

```php
use HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostPublished;
use HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostFailed;

protected $listen = [
    SocialPostPublished::class => [
        UpdatePostStatusToPublished::class,
    ],
    SocialPostFailed::class => [
        LogSocialMediaError::class,
    ],
];
```

## التعامل مع الأخطاء (Error Handling)

توفر الحزمة معالجة شاملة للأخطاء:

```php
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;

try {
    $result = SocialMedia::share(['facebook', 'twitter'], 'محتوى', 'https://example.com');
    
    // فحص النتائج
    if ($result['error_count'] > 0) {
        foreach ($result['errors'] as $platform => $error) {
            echo "خطأ في {$platform}: {$error}\n";
        }
    }
    
} catch (SocialMediaException $e) {
    echo "خطأ في وسائل التواصل الاجتماعي: " . $e->getMessage();
}
```

## منطق إعادة المحاولة (Retry Logic)

تُميز الحزمة بين الأخطاء الدائمة (4xx) والأخطاء المؤقتة (5xx، انقطاع الشبكة). ستفشل طلبات 4xx فوراً (fail-fast)، بينما سيتم إعادة المحاولة تلقائياً للأخطاء المؤقتة (5xx) باستخدام تأخير تزايدي (Exponential Backoff).

قم بإعداد ذلك في `config/autopost.php` أو ديناميكيًا:

```php
// إعداد عدد محاولات إعادة الاتصال
config(['autopost.retry_attempts' => 5]);

// إعداد معامل التأخير التزايدي (مثلاً: 2 يعني تأخير 2ث، 4ث، 8ث)
config(['autopost.retry_backoff_base' => 2]);

// إعداد وقت الانتظار (Timeout)
config(['autopost.timeout' => 60]);
```

## الأمان والحماية من (SSRF Protection)

عند تحميل وسائط (Media) من روابط خارجية، تستخدم الحزمة صنف `SafeMediaFetcher` للحماية من ثغرات تزوير الطلبات من جانب الخادم (SSRF) وهجمات DNS Rebinding. تقوم الحزمة بتحليل النطاق والتأكد من أنه لا يشير إلى عناوين IP داخلية، محجوزة، أو محلية (Loopback) قبل تحميل الملف.

يمكنك تخصيص إعدادات الأمان في `config/autopost.php`:

```php
// تفعيل أو تعطيل الحماية من SSRF
config(['autopost.enforce_ssrf_protection' => true]);

// الحد الأقصى المسموح به لحجم الوسائط بالبايت (مثلاً 10 ميغابايت)
config(['autopost.max_media_size' => 10485760]);
```
