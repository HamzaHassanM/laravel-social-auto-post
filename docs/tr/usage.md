# Temel Kullanım

Paket, desteklenen tüm platformlarda aynı anda veya ayrı ayrı içerik yayınlamak için `SocialMedia` facade aracılığıyla birleşik bir API sağlar.

## Birden Fazla Platformda Yayınlama

Metni ve bağlantıları seçilen platformlarda paylaşabilirsiniz:

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;

// Belirli platformlarda yayınla
$result = SocialMedia::share(
    ['facebook', 'twitter', 'linkedin'], 
    'Merhaba Dünya!', 
    'https://example.com'
);
```

## Tüm Platformlarda Yayınlama

Mesajı uygulamanızda yapılandırılmış her platforma yayınlamak isterseniz:

```php
// Tüm varsayılan platformlarda yayınla
$result = SocialMedia::shareToAll('Merhaba Dünya!', 'https://example.com');
```

## Medya Paylaşımı (Resimler ve Videolar)

Paket, destekleyen platformlara medya yüklemelerini sorunsuz bir şekilde işler:

### Resimler

```php
$result = SocialMedia::shareImage(
    ['instagram', 'pinterest', 'twitter'], 
    'Yeni ürünümüze göz atın!', 
    'https://example.com/product.jpg' // Platform desteğine bağlı olarak yerel yol veya URL
);
```

### Videolar

```php
$result = SocialMedia::shareVideo(
    ['youtube', 'tiktok', 'facebook'], 
    'Yeni eğitimimizi izleyin!', 
    'https://example.com/tutorial.mp4'
);
```

## Bireysel Platform Erişimi

Belirli bir platformla doğrudan etkileşim kurmayı tercih ederseniz, ona özel yöntemlere facade veya ana yönetici üzerinden erişebilirsiniz:

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\FaceBook;
use HamzaHassanM\LaravelSocialAutoPost\Facades\Twitter;

// Doğrudan facade erişimi
FaceBook::share('Merhaba Facebook!', 'https://example.com');
Twitter::share('Merhaba Twitter!', 'https://example.com');

// Veya SocialMedia yöneticisi aracılığıyla
SocialMedia::facebook()->share('Merhaba', 'https://example.com');
SocialMedia::twitter()->share('Merhaba', 'https://example.com');
SocialMedia::linkedin()->shareToCompanyPage('Güncelleme', 'https://example.com');
```
