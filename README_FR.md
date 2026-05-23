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

Un package Laravel puissant et flexible pour publier automatiquement du contenu sur 8 plateformes de réseaux sociaux différentes simultanément depuis votre application.

## 🌟 Plateformes Supportées
- Facebook (Pages)
- X / Twitter
- Instagram (Comptes Professionnels/Créateurs)
- LinkedIn (Profils Personnels & Pages Entreprise)
- Telegram (Chaînes & Groupes)
- TikTok (Direct Post API v2)
- Pinterest
- YouTube (Communauté & Vidéos)

## 📦 Installation

Vous pouvez installer le package via Composer :

```bash
composer require hamzahassanm/laravel-social-auto-post
```

Après l'installation, publiez le fichier de configuration :

```bash
php artisan vendor:publish --provider="HamzaHassanM\LaravelSocialAutoPost\SocialShareServiceProvider" --tag="autopost"
```

Cette commande créera un fichier `autopost.php` dans votre répertoire `config`.

## ⚙️ Configuration

Ajoutez les identifiants des plateformes que vous souhaitez utiliser dans votre fichier `.env` :

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
# LINKEDIN_ORGANIZATION_URN=your_org_urn # Pour une page entreprise

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

## 🚀 Utilisation de Base

### Gestion Multi-plateforme (Facade Globale)

Vous pouvez utiliser la façade `SocialMedia` pour publier simultanément sur plusieurs plateformes :

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;

$platforms = ['facebook', 'twitter', 'linkedin'];
$content = 'Ma première publication avec Laravel Social Auto Post ! 🚀';
$link = 'https://github.com/HamzaHassanM/laravel-social-auto-post';

$results = SocialMedia::share($platforms, $content, $link);

// Vérifier le nombre de plateformes réussies
echo $results['success_count'];
```

### Identifiants Dynamiques (SaaS / Multi-Comptes)

Pour les projets SaaS ou les applications gérant plusieurs utilisateurs, vous pouvez définir dynamiquement les informations d'identification sans modifier le fichier `.env` :

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

// Publier avec les comptes dynamiques
$results = SocialMedia::withCredentials($customCredentials)
    ->share(['facebook', 'twitter'], 'Salutations depuis des comptes dynamiques !');
```

### Utilisation Spécifique à une Plateforme

Pour interagir avec une seule plateforme spécifique, vous pouvez utiliser sa propre façade :

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\Facebook;
use HamzaHassanM\LaravelSocialAutoPost\Facades\Twitter;

// Envoyer uniquement sur Facebook
Facebook::share('Un message exclusif pour Facebook !', 'https://example.com');

// Envoyer sur Twitter avec des identifiants dynamiques
Twitter::withCredentials('BEARER', 'KEY', 'SECRET', 'ACCESS', 'ACCESS_SECRET')
       ->share('Un message dynamique exclusif pour Twitter !');
```

## 🤝 Contribution

Si vous souhaitez contribuer à ce projet, veuillez consulter notre guide de contribution dans le fichier [CONTRIBUTING.md](CONTRIBUTING.md).

## 📄 Licence

Ce package est open-sourced sous la licence MIT. Voir le fichier [LICENSE.md](LICENSE.md) pour plus de détails.
