# Installation & Requirements

## Requirements

- **PHP**: 8.1 or higher
- **Laravel**: 8.x, 9.x, 10.x, 11.x, 12.x, or 13.x

## Installation

You can install the package via composer:

```bash
composer require hamzahassanm/laravel-social-auto-post
```

## Publishing Configuration

After installing the package, you need to publish the configuration file. Run the following command:

```bash
php artisan vendor:publish --provider="HamzaHassanM\LaravelSocialAutoPost\SocialShareServiceProvider" --tag="autopost"
```

This will create a `config/autopost.php` file in your application where you can manage your default settings and credentials for all supported platforms.
