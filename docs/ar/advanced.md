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

تقوم الحزمة تلقائيًا بإعادة المحاولة في حالة فشل الطلبات باستخدام تأخير تزايدي (Exponential Backoff). قم بإعداد ذلك في `config/autopost.php` أو ديناميكيًا:

```php
// إعداد محاولات إعادة الاتصال
config(['autopost.retry_attempts' => 5]);

// إعداد وقت الانتظار
config(['autopost.timeout' => 60]);
```
