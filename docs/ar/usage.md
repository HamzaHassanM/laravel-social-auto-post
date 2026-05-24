# الاستخدام الأساسي

توفر الحزمة واجهة برمجة تطبيقات (API) موحدة من خلال الـ `SocialMedia` facade لنشر المحتوى عبر جميع المنصات المدعومة في وقت واحد أو بشكل فردي.

## النشر عبر منصات متعددة

يمكنك مشاركة النصوص والروابط عبر مجموعة مختارة من المنصات:

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;

// النشر على منصات محددة
$result = SocialMedia::share(
    ['facebook', 'twitter', 'linkedin'], 
    'مرحباً بالعالم!', 
    'https://example.com'
);
```

## النشر على جميع المنصات

إذا كنت ترغب في بث رسالة إلى كل منصة تم إعدادها في تطبيقك:

```php
// النشر على جميع المنصات الافتراضية
$result = SocialMedia::shareToAll('مرحباً بالعالم!', 'https://example.com');
```

## مشاركة الوسائط (الصور والفيديوهات)

تتعامل الحزمة بسلاسة مع عمليات رفع الوسائط إلى المنصات التي تدعم ذلك:

### الصور

```php
$result = SocialMedia::shareImage(
    ['instagram', 'pinterest', 'twitter'], 
    'تحقق من منتجنا الجديد!', 
    'https://example.com/product.jpg' // مسار محلي أو رابط حسب دعم المنصة
);
```

### الفيديوهات

```php
$result = SocialMedia::shareVideo(
    ['youtube', 'tiktok', 'facebook'], 
    'شاهد برنامجنا التعليمي الجديد!', 
    'https://example.com/tutorial.mp4'
);
```

## الوصول الفردي للمنصات

إذا كنت تفضل التفاعل مع منصة محددة مباشرة، يمكنك الوصول إلى الأساليب المخصصة لها عبر الـ facade الخاص بها أو من خلال المدير (Manager) الرئيسي:

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\FaceBook;
use HamzaHassanM\LaravelSocialAutoPost\Facades\Twitter;

// الوصول المباشر عبر الواجهة (Facade)
FaceBook::share('مرحباً فيسبوك!', 'https://example.com');
Twitter::share('مرحباً تويتر!', 'https://example.com');

// أو عبر مدير وسائل التواصل الاجتماعي (Manager)
SocialMedia::facebook()->share('مرحباً', 'https://example.com');
SocialMedia::twitter()->share('مرحباً', 'https://example.com');
SocialMedia::linkedin()->shareToCompanyPage('تحديث', 'https://example.com');
```
