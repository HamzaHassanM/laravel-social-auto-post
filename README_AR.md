# Laravel Social Auto Post

حزمة Laravel لتسهيل النشر التلقائي على وسائل التواصل الاجتماعي مثل Facebook و Telegram.

## التثبيت

لتثبيت الحزمة، قم بتشغيل الأمر التالي:

```bash
composer require hamzahassanm/laravel-social-auto-post
```

## التكوين

بعد تثبيت الحزمة، قم بنشر ملف التكوين باستخدام الأمر التالي:

```bash
php artisan vendor:publish --provider="HamzaHassanM\LaravelSocialAutoPost\SocialShareServiceProvider" --tag=autopost
```

سيقوم هذا الأمر بإنشاء ملف `config/autopost.php` حيث يمكنك تكوين بيانات الاعتماد الخاصة بمنصات التواصل الاجتماعي والإعدادات الأخرى.

### مثال على التكوين

في ملف `config/autopost.php`، أضف بيانات اعتماد Facebook و Telegram الخاصة بك:

```php
return [
    'facebook_access_token' => env('FACEBOOK_ACCESS_TOKEN'),
    'facebook_page_id'      => env('FACEBOOK_PAGE_ID'),
    'facebook_api_version'  => env('FACEBOOK_API_VERSION', 'v20.0'),
    'telegram_bot_token'    => env('TELEGRAM_BOT_TOKEN'),
    'telegram_chat_id'      => env('TELEGRAM_CHAT_ID'),
    'telegram_api_base_url' => env('TELEGRAM_API_BASE_URL', 'https://api.telegram.org/bot'),
];
```

تأكد من تحديث ملف `.env` الخاص بك بالقيم اللازمة:

```
FACEBOOK_ACCESS_TOKEN=your_facebook_access_token
FACEBOOK_PAGE_ID=your_facebook_page_id
TELEGRAM_BOT_TOKEN=your_telegram_bot_token
TELEGRAM_CHAT_ID=your_telegram_chat_id
```

## الاستخدام

تتيح الحزمة الوصول إلى منصات Facebook و Telegram من خلال واجهات استخدام مبسطة (Facades).

### طرق Facebook

يوفر الـ `FaceBook` الواجهة التالية للتعامل مع Facebook:

#### 1. `share(string $caption, string $url)`

نشر تحديث حالة مع رابط إلى صفحتك على Facebook.

```php
use FaceBook;

$caption = 'اطلع على هذا المقال!';
$url = 'https://example.com/post/123';

FaceBook::share($caption, $url);
```

#### 2. `shareImage(string $caption, string $image_url)`

نشر صورة مع تعليق على صفحتك على Facebook.

```php
use FaceBook;

$caption = 'شاهد هذا المنظر الرائع!';
$imageUrl = 'https://example.com/images/view.jpg';

FaceBook::shareImage($caption, $imageUrl);
```

#### 3. `shareVideo(string $caption, string $video_url)`

نشر فيديو مع تعليق على صفحتك على Facebook.

```php
use FaceBook;

$caption = 'شاهد هذا الفيديو الرائع!';
$videoUrl = 'https://example.com/videos/video.mp4';

FaceBook::shareVideo($caption, $videoUrl);
```

#### 4. `getPageInsights(array $metrics = [], array $additionalParams = [])`

استرجاع البيانات التحليلية (Insights) لصفحتك على Facebook. يمكنك تحديد مجموعة من المعايير للحصول على بيانات معينة أو تركها فارغة للحصول على البيانات الافتراضية.

```php
use FaceBook;

$metrics = ['page_impressions', 'page_engaged_users'];
$additionalParams = ['since' => '2024-01-01', 'until' => '2024-01-31'];

$insights = FaceBook::getPageInsights($metrics, $additionalParams);
```

#### 5. `getPageInfo()`

استرجاع المعلومات الأساسية حول صفحتك على Facebook مثل الاسم، التصنيف، والتفاصيل الأخرى.

```php
use FaceBook;

$pageInfo = FaceBook::getPageInfo();
```

---

### طرق Telegram

يوفر الـ `Telegram` الواجهة التالية للتعامل مع Telegram:

#### 1. `share(string $caption, string $url)`

نشر رسالة تحتوي على تعليق ورابط في محادثة Telegram الخاصة بك.

```php
use Telegram;

$caption = 'اطلع على هذا المقال!';
$url = 'https://example.com/post/123';

Telegram::share($caption, $url);
```

#### 2. `shareImage(string $caption, string $image_url)`

نشر صورة مع تعليق في محادثة Telegram الخاصة بك.

```php
use Telegram;

$caption = 'شاهد هذه الصورة الرائعة!';
$imageUrl = 'https://example.com/images/cool_image.jpg';

Telegram::shareImage($caption, $imageUrl);
```

#### 3. `shareDocument(string $caption, string $document_url)`

مشاركة مستند مع تعليق في محادثة Telegram الخاصة بك.

```php
use Telegram;

$caption = 'إليك هذا المستند المهم!';
$documentUrl = 'https://example.com/files/document.pdf';

Telegram::shareDocument($caption, $documentUrl);
```

#### 4. `shareVideo(string $caption, string $video_url)`

نشر فيديو مع تعليق في محادثة Telegram الخاصة بك.

```php
use Telegram;

$caption = 'شاهد هذا الفيديو!';
$videoUrl = 'https://example.com/videos/video.mp4';

Telegram::shareVideo($caption, $videoUrl);
```

#### 5. `getUpdates()`

استرجاع التحديثات (الرسائل والأحداث) من الروبوت الخاص بك على Telegram.

```php
use Telegram;

$updates = Telegram::getUpdates();
```

---

### معالجة الأخطاء

في حالة حدوث استثناءات أثناء النشر على وسائل التواصل الاجتماعي، مثل خطأ في رموز الـ API أو تجاوز حدود الـ API، يمكنك معالجة هذه الاستثناءات كما يلي:

```php
try {
    FaceBook::share($caption, $url);
} catch (\Exception $e) {
    \Log::error("حدث خطأ أثناء النشر على Facebook: " . $e->getMessage());
}

try {
    Telegram::share($caption, $url);
} catch (\Exception $e) {
    \Log::error("حدث خطأ أثناء النشر على Telegram: " . $e->getMessage());
}
```

## الواجهات (Facades)

توفر الحزمة واجهتين للتفاعل بسهولة مع منصات التواصل الاجتماعي:

- `FaceBook` - للنشر على Facebook باستخدام Facebook Graph API.
- `Telegram` - للنشر على Telegram باستخدام Telegram Bot API.

### طرق واجهة Facebook

- **share($caption, $url)**: نشر تحديث حالة مع رابط.
- **shareImage($caption, $image_url)**: نشر صورة مع تعليق.
- **shareVideo($caption, $video_url)**: نشر فيديو مع تعليق.
- **getPageInsights($metrics = [], $additionalParams = [])**: استرجاع التحليلات (Insights) الخاصة بصفحتك على Facebook.
- **getPageInfo()**: استرجاع المعلومات الأساسية حول صفحتك على Facebook.

### طرق واجهة Telegram

- **share($caption, $url)**: نشر رسالة مع رابط.
- **shareImage($caption, $image_url)**: نشر صورة مع تعليق.
- **shareVideo($caption, $video_url)**: نشر فيديو مع تعليق.
- **getUpdates()**: استرجاع التحديثات (الرسائل أو الأحداث) من روبوت Telegram.

## مزود الخدمة (Service Provider) (اختياري في Laravel 11)

بالنسبة لـ Laravel 11، قد تحتاج إلى تسجيل مزود الخدمة يدويًا في ملف `bootstrap/providers.php` إذا لم يكن الاكتشاف التلقائي (auto-discovery) مفعلًا:

```php
<?php

return [
    // مقدمو الخدمة الآخرون
    HamzaHassanM\LaravelSocialAutoPost\SocialShareServiceProvider::class,
];
```

في الإصدارات التي تدعم الـ auto-discovery، لا حاجة لإجراء هذا الخطوة.

## اختبار الحزمة

### استخدام Docker للاختبار

لضمان عمل الحزمة بشكل صحيح في جميع البيئات، يمكنك استخدام Docker لإنشاء بيئة اختبار متسقة.

### 1. استنساخ المستودع

```bash
git clone https://github.com/hamzahassanm/laravel-social-auto-post.git
cd laravel-social-auto-post
```

### 2. بناء الصورة الخاصة بـ Docker

استخدم Docker لإنشاء بيئة الاختبار:

```bash
docker-compose build
```

### 3. تشغيل الاختبارات

بعد إنشاء صورة Docker، يمكنك تشغيل الاختبارات:

```bash
docker-compose up
```

### 4. الدخول يدويًا إلى حاوية Docker (اختياري)

للأغراض التصحيحية (debugging)، يمكنك الدخول يدويًا إلى الحاوية:

```bash
docker-compose run --rm app bash
```

داخل الحاوية، يمكنك تشغيل الاختبارات أو الأوامر الإضافية:

```bash
./vendor/bin/phpunit
```

### 5. تنظيف حاويات Docker

بمجرد الانتهاء من الاختبارات، قم بإزالة الحاويات:

```bash
docker-compose down
```

---

## الرخصة

هذه الحزمة مرخصة بموجب [MIT License](LICENSE).

## المساهمة

لا تتردد في فتح المشاكل أو تقديم طلبات سحب لتحسين هذه الحزمة. يُرجى التأكد من أن المساهمات تتبع نمط الكود الموجود بالفعل وتنجح في جميع الاختبارات.

## الاتصال

لأي استفسارات أو مشاكل، لا تتردد في التواصل مع [HamzaHassanM](mailto:hamza.hassan.dev@gmail.com).

---

### ملاحظات إضافية

1. **التحسينات المستقبلية**: يمكن توسيع الحزمة لدعم منصات إضاف

ية مثل Twitter أو LinkedIn أو Instagram.
