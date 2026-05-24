# أمثلة شاملة

تأتي الحزمة مع العديد من الأمثلة العملية. فيما يلي بعض الأنماط الأكثر شيوعًا وتقدماً التي يمكنك استخدامها في تطبيق لارافيل الخاص بك.

## 1. التحقق من نتائج النشر المتعدد

عند النشر على منصات متعددة، تتلقى مصفوفة نتائج مفصلة. إليك كيفية معالجتها:

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;

try {
    $platforms = ['facebook', 'twitter', 'linkedin'];
    $result = SocialMedia::share($platforms, 'أخبار رائعة! أطلقنا ميزتنا الجديدة! 🚀', 'https://example.com/feature');
    
    echo "✅ تم النشر بنجاح على " . $result['success_count'] . " من أصل " . $result['total_platforms'] . " منصات\n";
    
    foreach ($result['results'] as $platform => $platformResult) {
        if ($platformResult['success']) {
            echo "✅ {$platform}: نجاح\n";
        } else {
            echo "❌ {$platform}: " . $platformResult['error'] . "\n";
        }
    }
    
} catch (SocialMediaException $e) {
    echo "❌ خطأ في النشر المتعدد: " . $e->getMessage() . "\n";
}
```

## 2. ميزات وتحليلات خاصة بالمنصة

تسمح لك بعض المنصات مثل فيسبوك باسترداد رؤى ومعلومات الصفحة.

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\FaceBook;

try {
    // الحصول على معلومات الصفحة الأساسية
    $pageInfo = FaceBook::getPageInfo();
    
    echo "📋 معلومات الصفحة:\n";
    echo "   الاسم: " . ($pageInfo['name'] ?? 'غير معروف') . "\n";
    echo "   الفئة: " . ($pageInfo['category'] ?? 'غير معروف') . "\n";
    echo "   المتابعين: " . ($pageInfo['followers_count'] ?? 'غير معروف') . "\n";
    
} catch (\Exception $e) {
    echo "خطأ في جلب معلومات الصفحة: " . $e->getMessage();
}
```

## 3. معالجة الأخطاء المتقدمة وإعادة المحاولة

يمكنك تكوين تطبيقك لإعادة المحاولة تلقائياً للمنشورات الفاشلة، أو التقاط أخطاء منصة معينة يدوياً.

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;
use Illuminate\Support\Facades\Log;

// زيادة مهلة الاتصال ومحاولات إعادة الإرسال مؤقتاً لمقطع فيديو كبير
config(['autopost.timeout' => 120]);
config(['autopost.retry_attempts' => 5]);

$result = SocialMedia::shareVideo(
    ['youtube', 'facebook'], 
    'دليل مفصل مدته 10 دقائق حول Laravel Auto Post', 
    storage_path('app/videos/tutorial.mp4')
);

if (!empty($result['errors'])) {
    // معالجة الإخفاقات المحددة بعد استنفاد جميع محاولات إعادة الاتصال
    Log::error('فشل تحميل الفيديو إلى بعض المنصات', $result['errors']);
}
```
