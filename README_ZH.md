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

一个强大灵活的 Laravel 扩展包，可以让您的应用自动向 8 个不同的社交媒体平台同步发布内容。

## 🌟 支持的平台
- Facebook (公共主页)
- X / Twitter
- Instagram (商业版/创作者账户)
- LinkedIn (个人主页和公司主页)
- Telegram (频道和群组)
- TikTok (Direct Post API v2)
- Pinterest
- YouTube (社区和视频)

## 📦 安装

您可以通过 Composer 安装此扩展包：

```bash
composer require hamzahassanm/laravel-social-auto-post
```

安装完成后，发布配置文件：

```bash
php artisan vendor:publish --provider="HamzaHassanM\LaravelSocialAutoPost\SocialShareServiceProvider" --tag="autopost"
```

该命令将在您的 `config` 目录中生成 `autopost.php` 配置文件。

## ⚙️ 配置

将您需要使用的社交平台凭证添加到 `.env` 文件中：

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
# LINKEDIN_ORGANIZATION_URN=your_org_urn # 适用于公司主页

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

## 🚀 基础使用

### 多平台管理 (全局 Facade)

您可以使用 `SocialMedia` 门面（Facade）同时在多个平台上发布：

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;

$platforms = ['facebook', 'twitter', 'linkedin'];
$content = '我的第一条 Laravel Social Auto Post 推送！ 🚀';
$link = 'https://github.com/HamzaHassanM/laravel-social-auto-post';

$results = SocialMedia::share($platforms, $content, $link);

// 检查成功发布的平台数量
echo $results['success_count'];
```

### 动态凭证 (适用于 SaaS / 多账户系统)

针对 SaaS 项目或管理多个用户账户的应用程序，您可以动态定义账户凭证，而无需更改 `.env` 文件：

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

// 使用动态凭证发布
$results = SocialMedia::withCredentials($customCredentials)
    ->share(['facebook', 'twitter'], '来自动态账户的问候！');
```

### 单一平台调用

如果您只需要与特定平台进行交互，可以直接使用该平台的门面：

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\Facebook;
use HamzaHassanM\LaravelSocialAutoPost\Facades\Twitter;

// 仅发送至 Facebook
Facebook::share('仅限于 Facebook 的独家消息！', 'https://example.com');

// 使用动态凭证发送至 Twitter
Twitter::withCredentials('BEARER', 'KEY', 'SECRET', 'ACCESS', 'ACCESS_SECRET')
       ->share('仅限于 Twitter 的独家动态消息！');
```

## 🤝 贡献代码

如果您想为该项目做出贡献，请参阅我们的贡献指南 [CONTRIBUTING.md](CONTRIBUTING.md)。

## 📄 许可证

本扩展包遵循 MIT 开源许可证。详情请参阅 [LICENSE.md](LICENSE.md) 文件。
