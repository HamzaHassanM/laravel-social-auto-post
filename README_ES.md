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

Un paquete de Laravel potente y flexible para publicar automáticamente contenido en 8 plataformas de redes sociales diferentes al mismo tiempo desde tu aplicación.

## 🌟 Plataformas Compatibles
- Facebook (Páginas)
- X / Twitter
- Instagram (Cuentas de Negocios/Creadores)
- LinkedIn (Perfiles Personales y Páginas de Empresa)
- Telegram (Canales y Grupos)
- TikTok (Direct Post API v2)
- Pinterest
- YouTube (Comunidad y Videos)

## 📦 Instalación

Puedes instalar el paquete vía Composer:

```bash
composer require hamzahassanm/laravel-social-auto-post
```

Después de la instalación, publica el archivo de configuración:

```bash
php artisan vendor:publish --provider="HamzaHassanM\LaravelSocialAutoPost\SocialShareServiceProvider" --tag="autopost"
```

Este comando creará un archivo `autopost.php` en tu directorio `config`.

## ⚙️ Configuración

Añade las credenciales de las plataformas que deseas utilizar a tu archivo `.env`:

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
# LINKEDIN_ORGANIZATION_URN=your_org_urn # Para página de empresa

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

## 🚀 Uso Básico

### Gestión Multiplataforma (Global Facade)

Puedes utilizar el Facade `SocialMedia` para publicar simultáneamente en varias plataformas:

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;

$platforms = ['facebook', 'twitter', 'linkedin'];
$content = '¡Mi primera publicación con Laravel Social Auto Post! 🚀';
$link = 'https://github.com/HamzaHassanM/laravel-social-auto-post';

$results = SocialMedia::share($platforms, $content, $link);

// Verificar número de publicaciones exitosas
echo $results['success_count'];
```

### Credenciales Dinámicas (SaaS / Multi-Cuenta)

Para aplicaciones SaaS o sistemas que gestionan múltiples usuarios, puedes definir credenciales de manera dinámica sin utilizar el archivo `.env`:

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

// Publicar usando las cuentas dinámicas
$results = SocialMedia::withCredentials($customCredentials)
    ->share(['facebook', 'twitter'], '¡Saludos desde cuentas dinámicas!');
```

### Uso Individual de Plataforma

Para interactuar únicamente con una plataforma específica, puedes usar su Facade directamente:

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\Facebook;
use HamzaHassanM\LaravelSocialAutoPost\Facades\Twitter;

// Enviar solo a Facebook
Facebook::share('¡Un mensaje exclusivo para Facebook!', 'https://example.com');

// Enviar a Twitter usando credenciales dinámicas
Twitter::withCredentials('BEARER', 'KEY', 'SECRET', 'ACCESS', 'ACCESS_SECRET')
       ->share('¡Un mensaje dinámico exclusivo para Twitter!');
```

## 🤝 Contribuir

Si quieres contribuir a este proyecto, por favor revisa nuestra guía de contribución en [CONTRIBUTING.md](CONTRIBUTING.md).

## 📄 Licencia

Este paquete es de código abierto bajo la licencia MIT. Consulta el archivo [LICENSE.md](LICENSE.md) para más detalles.
