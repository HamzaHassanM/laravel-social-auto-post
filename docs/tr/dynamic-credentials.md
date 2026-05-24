# Dinamik Kimlik Bilgileri (Çoklu Hesap ve SaaS)

`.env` dosyasını değiştirmeden birden fazla sosyal medya hesabını yönetmek için kimlik bilgilerini çalışma zamanında dinamik olarak aktarabilirsiniz. Bu, özellikle SaaS platformları ve çok kiracılı uygulamalar için kullanışlıdır.

## SocialMediaManager Kullanımı

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

// Belirli kullanıcının kimlik bilgilerini kullanarak paylaşın
$result = SocialMedia::withCredentials($customCredentials)
    ->share(['facebook', 'twitter'], 'A Kullanıcısından Merhaba!', 'https://example.com');
```

Bir platform `$customCredentials` listesinden çıkarılırsa, paket otomatik olarak `.env` dosyanızda ayarlanan varsayılan kimlik bilgilerine geri döner.

## Bireysel Platform Facade'lerini Kullanma

Dinamik kimlik bilgilerini bireysel platform facade'lerinde de kullanabilirsiniz:

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\Facebook;
use HamzaHassanM\LaravelSocialAutoPost\Facades\Twitter;

// Facebook'a özel
$facebook = Facebook::withCredentials('FACEBOOK_ACCESS_TOKEN', 'FACEBOOK_PAGE_ID');
$facebook->share('Belirli bir Facebook sayfasından merhaba!', 'https://example.com');

// Twitter'a özel
$twitter = Twitter::withCredentials('BEARER', 'API_KEY', 'API_SECRET', 'ACCESS_TOKEN', 'ACCESS_SECRET');
$twitter->share('Belirli bir Twitter hesabından merhaba!', 'https://example.com');
```
