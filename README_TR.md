<div align="center">
  🌐 
  <a href="README.md">English</a> •
  <a href="README_AR.md">العربية</a> •
  <a href="README_TR.md">Türkçe</a> •
  <a href="README_ES.md">Español</a> •
  <a href="README_FR.md">Français</a> •
  <a href="README_ZH.md">简体中文</a>
</div>
<br>

# Laravel Social Auto Post

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hamzahassanm/laravel-social-auto-post.svg?style=flat-square)](https://packagist.org/packages/hamzahassanm/laravel-social-auto-post)
[![Total Downloads](https://img.shields.io/packagist/dt/hamzahassanm/laravel-social-auto-post.svg?style=flat-square)](https://packagist.org/packages/hamzahassanm/laravel-social-auto-post)
[![License](https://img.shields.io/packagist/l/hamzahassanm/laravel-social-auto-post.svg?style=flat-square)](https://packagist.org/packages/hamzahassanm/laravel-social-auto-post)

Laravel uygulamanızdan aynı anda 8 farklı sosyal medya platformuna otomatik olarak gönderi yayınlamak için güçlü ve esnek bir Laravel paketi.

## 🌟 Desteklenen Platformlar
- Facebook (Sayfalar)
- X / Twitter
- Instagram (İşletme/İçerik Üreticisi)
- LinkedIn (Kişisel Profil & Şirket Sayfaları)
- Telegram (Kanallar & Gruplar)
- TikTok (Direct Post API v2)
- Pinterest
- YouTube (Topluluk & Videolar)

## 📦 Kurulum

Paketi Composer üzerinden yükleyebilirsiniz:

```bash
composer require hamzahassanm/laravel-social-auto-post
```

Kurulumdan sonra yapılandırma dosyasını yayınlayın:

```bash
php artisan vendor:publish --provider="HamzaHassanM\LaravelSocialAutoPost\SocialAutoPostServiceProvider" --tag="config"
```

Bu komut, `config` dizininize bir `social-auto-post.php` dosyası oluşturacaktır.

## ⚙️ Yapılandırma

`.env` dosyanıza kullanmak istediğiniz platformların kimlik bilgilerini ekleyin:

```env
# Facebook
FACEBOOK_ACCESS_TOKEN=your_token
FACEBOOK_PAGE_ID=your_page_id

# Twitter / X
TWITTER_BEARER_TOKEN=your_bearer
TWITTER_API_KEY=your_key
TWITTER_API_SECRET=your_secret
TWITTER_ACCESS_TOKEN=your_access
TWITTER_ACCESS_TOKEN_SECRET=your_token_secret

# Instagram
INSTAGRAM_ACCESS_TOKEN=your_token
INSTAGRAM_ACCOUNT_ID=your_account_id
INSTAGRAM_FACEBOOK_PAGE_ID=your_facebook_page_id

# LinkedIn
LINKEDIN_ACCESS_TOKEN=your_token
LINKEDIN_PERSON_URN=your_person_urn
# LINKEDIN_ORGANIZATION_URN=your_org_urn # Şirket sayfası için

# TikTok
TIKTOK_ACCESS_TOKEN=your_token
TIKTOK_CLIENT_KEY=your_key
TIKTOK_CLIENT_SECRET=your_secret

# Telegram
TELEGRAM_BOT_TOKEN=your_bot_token
TELEGRAM_CHAT_ID=your_chat_id

# Pinterest
PINTEREST_ACCESS_TOKEN=your_token
PINTEREST_BOARD_ID=your_board_id

# YouTube
YOUTUBE_API_KEY=your_api_key
YOUTUBE_ACCESS_TOKEN=your_access_token
YOUTUBE_CHANNEL_ID=your_channel_id
```

## 🚀 Temel Kullanım

### Çoklu Platform Yönetimi (Global Facade)

Aynı anda birden fazla platforma gönderi yayınlamak için `SocialMedia` facade'ını kullanabilirsiniz:

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;

$platforms = ['facebook', 'twitter', 'linkedin'];
$content = 'Laravel Social Auto Post ile ilk gönderim! 🚀';
$link = 'https://github.com/HamzaHassanM/laravel-social-auto-post';

$results = SocialMedia::share($platforms, $content, $link);

// Başarılı platform sayısını kontrol edin
echo $results['success_count'];
```

### Dinamik Kimlik Bilgileri (SaaS / Çoklu Hesap)

SaaS projeleri veya birden fazla hesap yöneten uygulamalar için, `.env` dosyasını kullanmadan dinamik olarak hesap bilgilerini tanımlayabilirsiniz:

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;

$customCredentials = [
    'facebook' => [
        'access_token' => 'USER_FACEBOOK_TOKEN',
        'page_id'      => 'USER_FACEBOOK_PAGE_ID'
    ],
    'twitter' => [
        'bearer_token'        => 'USER_TWITTER_BEARER',
        'api_key'             => 'USER_TWITTER_KEY',
        'api_secret'          => 'USER_TWITTER_SECRET',
        'access_token'        => 'USER_TWITTER_ACCESS',
        'access_token_secret' => 'USER_TWITTER_ACCESS_SECRET'
    ]
];

// Hesapları dinamik olarak belirleyip paylaşım yapma
$results = SocialMedia::withCredentials($customCredentials)
    ->share(['facebook', 'twitter'], 'Dinamik hesaplardan selamlar!');
```

### Tekil Platform Kullanımı

Sadece belirli bir platformla etkileşime geçmek için ilgili facade'ı doğrudan kullanabilirsiniz:

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\Facebook;
use HamzaHassanM\LaravelSocialAutoPost\Facades\Twitter;

// Sadece Facebook'a gönder
Facebook::share('Sadece Facebook için bir mesaj!', 'https://example.com');

// Dinamik bilgilerle Twitter'a gönder
Twitter::withCredentials('BEARER', 'KEY', 'SECRET', 'ACCESS', 'ACCESS_SECRET')
       ->share('Sadece Twitter için dinamik mesaj!');
```

## 🤝 Katkıda Bulunma

Bu projeye katkıda bulunmak isterseniz, lütfen [CONTRIBUTING.md](CONTRIBUTING.md) dosyamızı inceleyin.

## 📄 Lisans

Bu paket MIT lisansı altındadır. Detaylar için [LICENSE.md](LICENSE.md) dosyasına bakabilirsiniz.
