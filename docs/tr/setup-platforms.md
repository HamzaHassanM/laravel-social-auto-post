# Platform Kurulumu ve Yapılandırması

Bu belge, paketi farklı sosyal medya platformları için nasıl yapılandıracağınızı kapsar.

## Ortam Değişkenleri

Aşağıdaki ortam değişkenlerini `.env` dosyanıza ekleyin:

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

## Yapılandırma Dosyası

Yayınlanan `config/autopost.php` dosyası tüm yapılandırma seçeneklerini içerir:

```php
return [
    // Platform yapılandırmaları
    'facebook_access_token' => env('FACEBOOK_ACCESS_TOKEN'),
    'facebook_page_id' => env('FACEBOOK_PAGE_ID'),
    'facebook_api_version' => env('FACEBOOK_API_VERSION', 'v20.0'),
    
    // ... diğer platform yapılandırmaları
    
    // Genel ayarlar
    'default_platforms' => ['facebook', 'twitter', 'linkedin'],
    'enable_logging' => env('SOCIAL_MEDIA_LOGGING', true),
    'timeout' => env('SOCIAL_MEDIA_TIMEOUT', 30),
    'retry_attempts' => env('SOCIAL_MEDIA_RETRY_ATTEMPTS', 3),
];
```
