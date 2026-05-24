# 安装与要求

## 要求

- **PHP**: 8.1 或更高版本
- **Laravel**: 8.x, 9.x, 10.x, 11.x, 12.x 或 13.x

## 安装

您可以通过 composer 安装该包：

```bash
composer require hamzahassanm/laravel-social-auto-post
```

## 发布配置

安装该包后，您需要发布配置文件。运行以下命令：

```bash
php artisan vendor:publish --provider="HamzaHassanM\LaravelSocialAutoPost\SocialShareServiceProvider" --tag="autopost"
```

这将在您的应用程序中创建一个 `config/autopost.php` 文件，您可以在其中管理所有受支持平台的默认设置和凭据。
