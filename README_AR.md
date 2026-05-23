# Laravel Social Auto Post

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hamzahassanm/laravel-social-auto-post.svg?style=flat-square)](https://packagist.org/packages/hamzahassanm/laravel-social-auto-post)
[![Total Downloads](https://img.shields.io/packagist/dt/hamzahassanm/laravel-social-auto-post.svg?style=flat-square)](https://packagist.org/packages/hamzahassanm/laravel-social-auto-post)
[![License](https://img.shields.io/packagist/l/hamzahassanm/laravel-social-auto-post.svg?style=flat-square)](https://packagist.org/packages/hamzahassanm/laravel-social-auto-post)
[![PHP Version](https://img.shields.io/packagist/php-v/hamzahassanm/laravel-social-auto-post.svg?style=flat-square)](https://packagist.org/packages/hamzahassanm/laravel-social-auto-post)

حزمة Laravel شاملة للنشر التلقائي على وسائل التواصل الاجتماعي عبر **8 منصات رئيسية**: Facebook، Twitter/X، LinkedIn، Instagram، TikTok، YouTube، Pinterest، و Telegram. انشر على منصة واحدة أو جميع المنصات في نفس الوقت باستخدام API موحد.

## 🌟 المميزات

- **8 منصات وسائل التواصل الاجتماعي**: Facebook، Twitter/X، LinkedIn، Instagram، TikTok، YouTube، Pinterest، Telegram
- **API موحد**: انشر على منصات متعددة بأمر واحد
- **وصول فردي للمنصات**: وصول مباشر لميزات كل منصة
- **أنواع محتوى شاملة**: نص، صور، فيديو، مستندات، قصص، كاروسيل
- **تحليلات متقدمة**: Facebook Page Insights، Twitter Analytics، LinkedIn Metrics
- **جاهز للإنتاج**: معالجة الأخطاء، منطق إعادة المحاولة، تحديد معدل الطلبات، تسجيل الأحداث
- **Laravel أصلي**: تكامل مثالي مع نظام Laravel
- **قابل للتوسع**: سهل إضافة منصات وميزات جديدة

## 📋 جدول المحتويات

- [التثبيت](#التثبيت)
- [الإعدادات](#الإعدادات)
- [البدء السريع](#البدء-السريع)
- [الاستخدام](#الاستخدام)
  - [API الموحد](#api-الموحد)
  - [المنصات الفردية](#المنصات-الفردية)
  - [الميزات الخاصة بكل منصة](#الميزات-الخاصة-بكل-منصة)
- [الميزات المتقدمة](#الميزات-المتقدمة)
- [معالجة الأخطاء](#معالجة-الأخطاء)
- [الاختبار](#الاختبار)
- [الأمثلة](#الأمثلة)
- [مرجع API](#مرجع-api)
- [المساهمة](#المساهمة)
- [الترخيص](#الترخيص)

## 🚀 التثبيت

### المتطلبات

- PHP 8.1 أو أعلى
- Laravel 11.0 أو أعلى
- Composer

### توافق إصدار Laravel

| إصدار الحزمة | إصدار Laravel | إصدار PHP | الحالة |
|----------------|-----------------|-------------|--------|
| 2.3.0          | Laravel 13.x    | PHP 8.2+    | الأحدث |
| 2.2.1          | Laravel 13.x    | PHP 8.2+    | مدعوم |
| 2.2.0          | Laravel 13.x    | PHP 8.2+    | مدعوم |
| 2.1.0          | Laravel 12.x    | PHP 8.1+    | مدعوم |
| 2.0.0          | Laravel 11.x    | PHP 8.1+    | مدعوم |
| 1.x            | Laravel 10.x    | PHP 8.0+    | قديم |

### التثبيت عبر Composer

```bash
composer require hamzahassanm/laravel-social-auto-post
```

### نشر ملف الإعدادات

```bash
php artisan vendor:publish --provider="HamzaHassanM\LaravelSocialAutoPost\SocialShareServiceProvider" --tag=autopost
```

## ⚙️ الإعدادات

### متغيرات البيئة

أضف متغيرات البيئة التالية إلى ملف `.env`:

```env
# Facebook
FACEBOOK_ACCESS_TOKEN=your_facebook_access_token
FACEBOOK_PAGE_ID=your_facebook_page_id

# Twitter/X
TWITTER_BEARER_TOKEN=your_twitter_bearer_token
TWITTER_API_KEY=your_twitter_api_key
TWITTER_API_SECRET=your_twitter_api_secret
TWITTER_ACCESS_TOKEN=your_twitter_access_token
TWITTER_ACCESS_TOKEN_SECRET=your_twitter_access_token_secret

# LinkedIn
LINKEDIN_ACCESS_TOKEN=your_linkedin_access_token
LINKEDIN_PERSON_URN=your_linkedin_person_urn
LINKEDIN_ORGANIZATION_URN=your_linkedin_organization_urn

# Instagram
INSTAGRAM_ACCESS_TOKEN=your_instagram_access_token
INSTAGRAM_ACCOUNT_ID=your_instagram_account_id

# TikTok
TIKTOK_ACCESS_TOKEN=your_tiktok_access_token
TIKTOK_CLIENT_KEY=your_tiktok_client_key
TIKTOK_CLIENT_SECRET=your_tiktok_client_secret

# YouTube
YOUTUBE_API_KEY=your_youtube_api_key
YOUTUBE_ACCESS_TOKEN=your_youtube_access_token
YOUTUBE_CHANNEL_ID=your_youtube_channel_id

# Pinterest
PINTEREST_ACCESS_TOKEN=your_pinterest_access_token
PINTEREST_BOARD_ID=your_pinterest_board_id

# Telegram
TELEGRAM_BOT_TOKEN=your_telegram_bot_token
TELEGRAM_CHAT_ID=your_telegram_chat_id
```

### ملف الإعدادات

ملف `config/autopost.php` المنشور يحتوي على جميع خيارات الإعدادات:

```php
return [
    // إعدادات المنصات
    'facebook_access_token' => env('FACEBOOK_ACCESS_TOKEN'),
    'facebook_page_id' => env('FACEBOOK_PAGE_ID'),
    'facebook_api_version' => env('FACEBOOK_API_VERSION', 'v20.0'),
    
    // ... إعدادات المنصات الأخرى
    
    // الإعدادات العامة
    'default_platforms' => ['facebook', 'twitter', 'linkedin'],
    'enable_logging' => env('SOCIAL_MEDIA_LOGGING', true),
    'timeout' => env('SOCIAL_MEDIA_TIMEOUT', 30),
    'retry_attempts' => env('SOCIAL_MEDIA_RETRY_ATTEMPTS', 3),
];
```

## 🎯 البدء السريع

### الاستخدام الأساسي

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;

// انشر على منصات متعددة
$result = SocialMedia::share(['facebook', 'twitter', 'linkedin'], 'مرحباً بالعالم!', 'https://example.com');

// انشر على جميع المنصات
$result = SocialMedia::shareToAll('مرحباً بالعالم!', 'https://example.com');

// شارك الصور
$result = SocialMedia::shareImage(['instagram', 'pinterest'], 'تحقق من هذا!', 'https://example.com/image.jpg');

// شارك الفيديوهات
$result = SocialMedia::shareVideo(['youtube', 'tiktok'], 'شاهد هذا!', 'https://example.com/video.mp4');
```

### الاعتمادات الديناميكية (Multi-Account & SaaS)

يمكنك تمرير بيانات الاعتماد ديناميكيًا في وقت التشغيل لإدارة حسابات تواصل اجتماعي متعددة دون تغيير ملف `.env`. هذا مفيد بشكل خاص لمنصات SaaS والتطبيقات متعددة المستخدمين (Multi-tenant).

*(شكر خاص لـ [@am0nshi](https://github.com/am0nshi) لطلب هذه الميزة!)*

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;

$customCredentials = [
    'facebook' => [
        'access_token' => 'USER_A_FACEBOOK_TOKEN',
        'page_id' => 'USER_A_FACEBOOK_PAGE_ID'
    ],
    'twitter' => [
        'bearer_token' => 'USER_A_TWITTER_BEARER',
        'api_key' => 'USER_A_TWITTER_KEY',
        'api_secret' => 'USER_A_TWITTER_SECRET',
        'access_token' => 'USER_A_TWITTER_ACCESS',
        'access_token_secret' => 'USER_A_TWITTER_ACCESS_SECRET'
    ]
];

// النشر باستخدام بيانات اعتماد المستخدم المحدد
$result = SocialMedia::withCredentials($customCredentials)
    ->share(['facebook', 'twitter'], 'مرحباً من المستخدم أ!', 'https://example.com');
```

إذا تم حذف منصة من مصفوفة `$customCredentials`، فستعود الحزمة تلقائيًا لاستخدام بيانات الاعتماد الافتراضية المحددة في ملف `.env`.

يمكنك أيضًا استخدام بيانات الاعتماد الديناميكية للمنصات الفردية بشكل مباشر:

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\Facebook;
use HamzaHassanM\LaravelSocialAutoPost\Facades\Twitter;

// النشر عبر فيسبوك ببيانات محددة
$facebook = Facebook::withCredentials('FACEBOOK_ACCESS_TOKEN', 'FACEBOOK_PAGE_ID');
$facebook->share('مرحباً من صفحة فيسبوك محددة!', 'https://example.com');

// النشر عبر تويتر ببيانات محددة
$twitter = Twitter::withCredentials('BEARER', 'API_KEY', 'API_SECRET', 'ACCESS_TOKEN', 'ACCESS_SECRET');
$twitter->share('مرحباً من حساب تويتر محدد!', 'https://example.com');
```

### الوصول الفردي للمنصات

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\FaceBook;
use HamzaHassanM\LaravelSocialAutoPost\Facades\Twitter;
use HamzaHassanM\LaravelSocialAutoPost\Facades\LinkedIn;

// Facebook
FaceBook::share('مرحباً Facebook!', 'https://example.com');
FaceBook::shareImage('تحقق من هذه الصورة!', 'https://example.com/image.jpg');

// Twitter
Twitter::share('مرحباً Twitter!', 'https://example.com');
Twitter::shareImage('تحقق من هذه الصورة!', 'https://example.com/image.jpg');

// LinkedIn
LinkedIn::share('مرحباً LinkedIn!', 'https://example.com');
LinkedIn::shareToCompanyPage('تحديث الشركة!', 'https://example.com');
```

## 📖 الاستخدام

### API الموحد

واجهة `SocialMedia` توفر واجهة موحدة للنشر على منصات متعددة:

#### انشر على منصات متعددة

```php
use SocialMedia;

// انشر على منصات محددة
$result = SocialMedia::share(['facebook', 'twitter', 'linkedin'], 'المحتوى', 'https://example.com');

// شارك الصور على المنصات المرئية
$result = SocialMedia::shareImage(['instagram', 'pinterest'], 'التعليق', 'https://example.com/image.jpg');

// شارك الفيديوهات على منصات الفيديو
$result = SocialMedia::shareVideo(['youtube', 'tiktok'], 'التعليق', 'https://example.com/video.mp4');
```

#### انشر على جميع المنصات

```php
// انشر على جميع المنصات المتاحة
$result = SocialMedia::shareToAll('المحتوى', 'https://example.com');

// شارك الصور على جميع المنصات
$result = SocialMedia::shareImageToAll('التعليق', 'https://example.com/image.jpg');

// شارك الفيديوهات على جميع المنصات
$result = SocialMedia::shareVideoToAll('التعليق', 'https://example.com/video.mp4');
```

#### الوصول الخاص بكل منصة

```php
// الوصول للمنصات الفردية
$facebookService = SocialMedia::facebook();
$twitterService = SocialMedia::twitter();
$linkedinService = SocialMedia::linkedin();

// استخدم الطرق الخاصة بكل منصة
$result = SocialMedia::linkedin()->shareToCompanyPage('المحتوى', 'https://example.com');
$result = SocialMedia::instagram()->shareCarousel('التعليق', ['img1.jpg', 'img2.jpg']);
```

### المنصات الفردية

كل منصة لها واجهة خاصة بها مع طرق محددة:

#### Facebook

```php
use FaceBook;

// النشر الأساسي
FaceBook::share('المحتوى', 'https://example.com');
FaceBook::shareImage('التعليق', 'https://example.com/image.jpg');
FaceBook::shareVideo('التعليق', 'https://example.com/video.mp4');

// التحليلات
$insights = FaceBook::getPageInsights(['page_impressions', 'page_engaged_users']);
$pageInfo = FaceBook::getPageInfo();
```

#### Twitter/X

```php
use Twitter;

// النشر
Twitter::share('المحتوى', 'https://example.com');
Twitter::shareImage('التعليق', 'https://example.com/image.jpg');
Twitter::shareVideo('التعليق', 'https://example.com/video.mp4');

// التحليلات
$timeline = Twitter::getTimeline(10);
$userInfo = Twitter::getUserInfo();
```

#### LinkedIn

```php
use LinkedIn;

// المنشورات الشخصية
LinkedIn::share('المحتوى', 'https://example.com');
LinkedIn::shareImage('التعليق', 'https://example.com/image.jpg');
LinkedIn::shareVideo('التعليق', 'https://example.com/video.mp4');

// منشورات صفحة الشركة
LinkedIn::shareToCompanyPage('المحتوى', 'https://example.com');

// معلومات المستخدم
$userInfo = LinkedIn::getUserInfo();
```

#### Instagram

```php
use Instagram;

// المنشورات
Instagram::shareImage('التعليق', 'https://example.com/image.jpg');
Instagram::shareVideo('التعليق', 'https://example.com/video.mp4');

// منشورات الكاروسيل
Instagram::shareCarousel('التعليق', ['img1.jpg', 'img2.jpg', 'img3.jpg']);

// القصص
Instagram::shareStory('التعليق', 'https://example.com');

// التحليلات
$accountInfo = Instagram::getAccountInfo();
$recentMedia = Instagram::getRecentMedia(25);
```

#### TikTok

```php
use TikTok;

// نشر الفيديوهات (Direct Post - ينشر تلقائياً في الصفحة)
TikTok::shareVideo('التعليق', 'https://example.com/video.mp4');

// نشر صور كاروسيل (Direct Post)
TikTok::shareImage('التعليق', 'https://example.com/image.jpg');

// ملاحظة: TikTok لا يدعم المنشورات النصية فقط (طريقة share() سترمي استثناء)
// ملاحظة: إذا كان تطبيق TikTok الخاص بك غير مراجع (unaudited)، فسيتم تعيين خصوصية جميع المنشورات إلى خاص (SELF_ONLY) افتراضياً.

// التحليلات
$userInfo = TikTok::getUserInfo();
$userVideos = TikTok::getUserVideos(20);
```

#### YouTube

```php
use YouTube;

// رفع الفيديوهات
YouTube::shareVideo('العنوان', 'https://example.com/video.mp4');

// منشورات المجتمع
YouTube::createCommunityPost('المحتوى', 'https://example.com');

// التحليلات
$channelInfo = YouTube::getChannelInfo();
$channelVideos = YouTube::getChannelVideos(25);
$videoAnalytics = YouTube::getVideoAnalytics('video_id');
```

#### Pinterest

```php
use Pinterest;

// الدبابيس
Pinterest::shareImage('التعليق', 'https://example.com/image.jpg');
Pinterest::shareVideo('التعليق', 'https://example.com/video.mp4');

// اللوحات
Pinterest::createBoard('اسم اللوحة', 'الوصف');

// التحليلات
$userInfo = Pinterest::getUserInfo();
$boards = Pinterest::getBoards(25);
$boardPins = Pinterest::getBoardPins('board_id', 25);
$pinAnalytics = Pinterest::getPinAnalytics('pin_id');
```

#### Telegram

```php
use Telegram;

// الرسائل
Telegram::share('المحتوى', 'https://example.com');
Telegram::shareImage('التعليق', 'https://example.com/image.jpg');
Telegram::shareVideo('التعليق', 'https://example.com/video.mp4');
Telegram::shareDocument('التعليق', 'https://example.com/document.pdf');

// تحديثات البوت
$updates = Telegram::getUpdates();
```

### الميزات الخاصة بكل منصة

#### تحليلات Facebook

```php
// احصل على رؤى الصفحة
$insights = FaceBook::getPageInsights([
    'page_impressions',
    'page_engaged_users',
    'page_fan_adds'
]);

// احصل على رؤى لنطاق تاريخي محدد
$insights = FaceBook::getPageInsights(
    ['page_impressions', 'page_engaged_users'],
    ['since' => '2024-01-01', 'until' => '2024-01-31']
);
```

#### كاروسيل Instagram

```php
// إنشاء كاروسيل مع صور متعددة
$images = [
    'https://example.com/image1.jpg',
    'https://example.com/image2.jpg',
    'https://example.com/image3.jpg'
];
$result = Instagram::shareCarousel('تحقق من منتجاتنا!', $images);
```

#### صفحات شركة LinkedIn

```php
// انشر على صفحة الشركة (يتطلب organization URN)
LinkedIn::shareToCompanyPage('تحديث الشركة: نحن نوظف!', 'https://example.com/careers');
```

#### منشورات مجتمع YouTube

```php
// إنشاء منشور مجتمع
YouTube::createCommunityPost('ماذا تريد أن ترى في فيديونا القادم؟', 'https://example.com/poll');
```

#### لوحات Pinterest

```php
// إنشاء لوحة
Pinterest::createBoard('وصفاتي', 'مجموعة من الوصفات المذهلة', 'PUBLIC');

// احصل على دبابيس اللوحة
$pins = Pinterest::getBoardPins('board_id', 25);
```

## 🔧 الميزات المتقدمة

### معالجة الأخطاء

الحزمة توفر معالجة شاملة للأخطاء:

```php
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;

try {
    $result = SocialMedia::share(['facebook', 'twitter'], 'المحتوى', 'https://example.com');
    
    // تحقق من النتائج
    if ($result['error_count'] > 0) {
        foreach ($result['errors'] as $platform => $error) {
            echo "خطأ في {$platform}: {$error}\n";
        }
    }
    
} catch (SocialMediaException $e) {
    echo "خطأ وسائل التواصل الاجتماعي: " . $e->getMessage();
}
```

### منطق إعادة المحاولة

الحزمة تعيد المحاولة تلقائياً للطلبات الفاشلة مع تأخير متزايد:

```php
// إعداد محاولات إعادة المحاولة
config(['autopost.retry_attempts' => 5]);

// إعداد المهلة الزمنية
config(['autopost.timeout' => 60]);
```

### التسجيل

جميع العمليات مسجلة تلقائياً:

```php
// تفعيل/إلغاء التسجيل
config(['autopost.enable_logging' => true]);

// تحقق من سجلات Laravel للحصول على معلومات مفصلة
tail -f storage/logs/laravel.log
```

### التحقق من المدخلات

الحزمة تتحقق من جميع المدخلات:

```php
// يتحقق من URLs
// يتحقق من طول النص
// يتحقق من أنواع المحتوى المطلوبة
// يرمي SocialMediaException للمدخلات غير الصحيحة
```

## 🧪 الاختبار

### تشغيل الاختبارات

```bash
# تشغيل جميع الاختبارات
./vendor/bin/phpunit

# تشغيل مجموعة اختبارات محددة
./vendor/bin/phpunit tests/Unit/
./vendor/bin/phpunit tests/Feature/

# تشغيل مع التغطية
./vendor/bin/phpunit --coverage-html coverage/
```

### إعداد الاختبار

```php
// في إعداد الاختبار
config([
    'autopost.facebook_access_token' => 'test_token',
    'autopost.facebook_page_id' => 'test_page_id',
    // ... إعدادات الاختبار الأخرى
]);
```

### محاكاة APIs

```php
use Illuminate\Support\Facades\Http;

Http::fake([
    'https://graph.facebook.com/v20.0/*' => Http::response(['id' => '123'], 200),
    'https://api.twitter.com/2/*' => Http::response(['data' => ['id' => '456']], 200),
]);
```

## 📚 الأمثلة

تحقق من مجلد `examples/` للحصول على أمثلة شاملة للاستخدام:

- **الاستخدام الأساسي**: منصة واحدة، منصات متعددة، حسابات متعددة، معالجة الأخطاء
- **الاستخدام المتقدم**: جدولة المحتوى، التحليلات، العمليات المجمعة
- **خاص بكل منصة**: تحليلات Facebook، كاروسيل Instagram، صفحات شركة LinkedIn
- **التكامل**: أوامر Laravel Artisan، وظائف Laravel Queue، مستمعي أحداث Laravel
- **الاختبار**: اختبارات الوحدة، اختبارات الميزات، محاكاة API

### أمثلة سريعة

```bash
# تشغيل الأمثلة الأساسية
php examples/basic-usage/single-platform.php
php examples/basic-usage/multi-platform.php
php examples/basic-usage/multi-account.php
php examples/basic-usage/error-handling.php

# تشغيل الأمثلة الخاصة بكل منصة
php examples/platform-specific/facebook-examples.php
php examples/platform-specific/instagram-examples.php
```

## 📖 مرجع API

### واجهة SocialMedia

| الطريقة | الوصف | المعاملات |
|---------|--------|-----------|
| `share($platforms, $caption, $url)` | انشر على منصات متعددة | `array $platforms, string $caption, string $url` |
| `shareImage($platforms, $caption, $image_url)` | انشر صورة على منصات متعددة | `array $platforms, string $caption, string $image_url` |
| `shareVideo($platforms, $caption, $video_url)` | انشر فيديو على منصات متعددة | `array $platforms, string $caption, string $video_url` |
| `shareToAll($caption, $url)` | انشر على جميع المنصات | `string $caption, string $url` |
| `shareImageToAll($caption, $image_url)` | انشر صورة على جميع المنصات | `string $caption, string $image_url` |
| `shareVideoToAll($caption, $video_url)` | انشر فيديو على جميع المنصات | `string $caption, string $video_url` |
| `platform($platform)` | احصل على خدمة المنصة | `string $platform` |
| `facebook()` | احصل على خدمة Facebook | - |
| `twitter()` | احصل على خدمة Twitter | - |
| `linkedin()` | احصل على خدمة LinkedIn | - |
| `instagram()` | احصل على خدمة Instagram | - |
| `tiktok()` | احصل على خدمة TikTok | - |
| `youtube()` | احصل على خدمة YouTube | - |
| `pinterest()` | احصل على خدمة Pinterest | - |
| `telegram()` | احصل على خدمة Telegram | - |

### واجهات المنصات

كل منصة لها واجهة خاصة بها مع طرق محددة لتلك المنصة. راجع الوثائق الفردية للمنصة أعلاه للحصول على توقيعات الطرق المفصلة.

### SocialMediaManager

| الطريقة | الوصف | المعاملات |
|---------|--------|-----------|
| `getAvailablePlatforms()` | احصل على قائمة المنصات المتاحة | - |
| `isPlatformAvailable($platform)` | تحقق من توفر المنصة | `string $platform` |
| `getPlatformService($platform)` | احصل على فئة خدمة المنصة | `string $platform` |

## 🤝 المساهمة

نرحب بالمساهمات! يرجى الاطلاع على [دليل المساهمة](CONTRIBUTING.md) للتفاصيل.

### إعداد التطوير

```bash
# استنساخ المستودع
git clone https://github.com/hamzahassanm/laravel-social-auto-post.git

# تثبيت التبعيات
composer install

# تشغيل الاختبارات
./vendor/bin/phpunit

# تشغيل الأمثلة
php examples/basic-usage/single-platform.php
```

### عملية طلب السحب

1. Fork المستودع
2. إنشاء فرع ميزة
3. إجراء التغييرات
4. إضافة اختبارات للوظائف الجديدة
5. التأكد من نجاح جميع الاختبارات
6. تقديم طلب سحب

## 📄 الترخيص

هذه الحزمة مرخصة تحت [رخصة MIT](LICENSE).

## 🆘 الدعم

- **الوثائق**: [GitHub Wiki](https://github.com/hamzahassanm/laravel-social-auto-post/wiki)
- **المشاكل**: [GitHub Issues](https://github.com/hamzahassanm/laravel-social-auto-post/issues)
- **المناقشات**: [GitHub Discussions](https://github.com/hamzahassanm/laravel-social-auto-post/discussions)
- **البريد الإلكتروني**: hamza.hassan.dev@gmail.com

## 🙏 شكر وتقدير

- إطار عمل Laravel
- جميع APIs منصات وسائل التواصل الاجتماعي
- مجتمع المصادر المفتوحة

---

**صُنع بـ ❤️ بواسطة [HamzaHassanM](https://github.com/hamzahassanm)**