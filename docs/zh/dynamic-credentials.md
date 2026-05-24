# 动态凭证（多账户和 SaaS）

您可以在运行时动态传递凭证以管理多个社交媒体帐户，而无需更改 `.env` 文件。这对于 SaaS 平台和多租户应用程序特别有用。

## 使用 SocialMediaManager

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

// 使用特定用户的凭证进行分享
$result = SocialMedia::withCredentials($customCredentials)
    ->share(['facebook', 'twitter'], '来自用户 A 的问候！', 'https://example.com');
```

如果从 `$customCredentials` 中省略某个平台，该包将自动退回到 `.env` 文件中设置的默认凭证。

## 使用个人平台 Facades

您还可以在个人平台 facade 上使用动态凭证：

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\Facebook;
use HamzaHassanM\LaravelSocialAutoPost\Facades\Twitter;

// Facebook 特定
$facebook = Facebook::withCredentials('FACEBOOK_ACCESS_TOKEN', 'FACEBOOK_PAGE_ID');
$facebook->share('来自特定 Facebook 页面的问候！', 'https://example.com');

// Twitter 特定
$twitter = Twitter::withCredentials('BEARER', 'API_KEY', 'API_SECRET', 'ACCESS_TOKEN', 'ACCESS_SECRET');
$twitter->share('来自特定 Twitter 帐户的问候！', 'https://example.com');
```
