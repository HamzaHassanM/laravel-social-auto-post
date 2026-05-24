# Kurulum ve Gereksinimler

## Gereksinimler

- **PHP**: 8.1 veya daha yüksek
- **Laravel**: 8.x, 9.x, 10.x, 11.x, 12.x, veya 13.x

## Kurulum

Paketi composer aracılığıyla yükleyebilirsiniz:

```bash
composer require hamzahassanm/laravel-social-auto-post
```

## Yapılandırmayı Yayınlama

Paketi kurduktan sonra, yapılandırma dosyasını yayınlamanız gerekir. Aşağıdaki komutu çalıştırın:

```bash
php artisan vendor:publish --provider="HamzaHassanM\LaravelSocialAutoPost\SocialShareServiceProvider" --tag="autopost"
```

Bu, uygulamanızda desteklenen tüm platformlar için varsayılan ayarlarınızı ve kimlik bilgilerinizi yönetebileceğiniz bir `config/autopost.php` dosyası oluşturacaktır.
