# حسابات متعددة ديناميكية (Dynamic Credentials)

يمكنك تمرير بيانات الاعتماد ديناميكيًا في وقت التشغيل لإدارة حسابات تواصل اجتماعي متعددة دون تغيير ملف `.env`. هذا مفيد بشكل خاص لمنصات SaaS والتطبيقات متعددة المستأجرين (Multi-tenant).

## استخدام SocialMediaManager

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

// النشر باستخدام بيانات اعتماد مستخدم محدد
$result = SocialMedia::withCredentials($customCredentials)
    ->share(['facebook', 'twitter'], 'مرحباً من المستخدم أ!', 'https://example.com');
```

إذا تم حذف منصة معينة من `$customCredentials`، فستعود الحزمة تلقائيًا إلى استخدام بيانات الاعتماد الافتراضية المحددة في ملف `.env` الخاص بك.

## استخدام واجهات المنصات الفردية

يمكنك أيضًا استخدام بيانات الاعتماد الديناميكية على واجهات (Facades) المنصات الفردية:

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\Facebook;
use HamzaHassanM\LaravelSocialAutoPost\Facades\Twitter;

// خاص بـ Facebook
$facebook = Facebook::withCredentials('FACEBOOK_ACCESS_TOKEN', 'FACEBOOK_PAGE_ID');
$facebook->share('مرحباً من صفحة فيسبوك محددة!', 'https://example.com');

// خاص بـ Twitter
$twitter = Twitter::withCredentials('BEARER', 'API_KEY', 'API_SECRET', 'ACCESS_TOKEN', 'ACCESS_SECRET');
$twitter->share('مرحباً من حساب تويتر محدد!', 'https://example.com');
```
