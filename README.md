<div align="center">
  🌐 
  <a href="https://hamzahassanm.github.io/laravel-social-auto-post/en/">English</a> •
  <a href="https://hamzahassanm.github.io/laravel-social-auto-post/ar/">العربية</a> •
  <a href="https://hamzahassanm.github.io/laravel-social-auto-post/tr/">Türkçe</a> •
  <a href="https://hamzahassanm.github.io/laravel-social-auto-post/es/">Español</a> •
  <a href="https://hamzahassanm.github.io/laravel-social-auto-post/fr/">Français</a> •
  <a href="https://hamzahassanm.github.io/laravel-social-auto-post/zh/">简体中文</a>
</div>
<br>

# Laravel Social Auto Post

[![Latest Version](https://img.shields.io/packagist/v/hamzahassanm/laravel-social-auto-post.svg)](https://packagist.org/packages/hamzahassanm/laravel-social-auto-post)
[![Total Downloads](https://img.shields.io/packagist/dt/hamzahassanm/laravel-social-auto-post.svg)](https://packagist.org/packages/hamzahassanm/laravel-social-auto-post)
[![License](https://img.shields.io/packagist/l/hamzahassanm/laravel-social-auto-post.svg)](https://packagist.org/packages/hamzahassanm/laravel-social-auto-post)
[![PHP Version](https://img.shields.io/packagist/php-v/hamzahassanm/laravel-social-auto-post.svg)](https://packagist.org/packages/hamzahassanm/laravel-social-auto-post)
[![Plumb score](https://plumbphp.dev/badges/hamzahassanm/laravel-social-auto-post/composite.svg)](https://plumbphp.dev/hamzahassanm/laravel-social-auto-post)


A comprehensive Laravel package for automatic social media posting across 8 major platforms using a unified API.

## 📚 Complete Documentation

We have moved our extensive documentation to a dedicated website to provide a better reading experience with support for 6 different languages!

👉 **[Visit the Official Documentation Website](https://hamzahassanm.github.io/laravel-social-auto-post/)**

## ✨ Features

- **8 Platforms Supported:** Facebook, Twitter/X, LinkedIn, Instagram, TikTok, YouTube, Pinterest, Telegram.
- **Unified API:** One interface to post text, images, and videos everywhere.
- **Multi-Account / SaaS Ready:** Pass credentials dynamically at runtime.
- **Laravel Events:** Hook into `SocialPostPublishing`, `SocialPostPublished`, and `SocialPostFailed` for perfect observability.
- **Extensively Tested:** Built with high test coverage and solid error handling.

## 🚀 Quick Installation

```bash
composer require hamzahassanm/laravel-social-auto-post
```

Publish the configuration file:

```bash
php artisan vendor:publish --provider="HamzaHassanM\LaravelSocialAutoPost\SocialShareServiceProvider" --tag="autopost"
```

## 🤝 Contributing
Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## 🔒 Security
If you discover any security related issues, please email instead of using the issue tracker. See [SECURITY.md](SECURITY.md) for more details.

## 📄 License
The MIT License (MIT). Please see [License File](LICENSE) for more information.
