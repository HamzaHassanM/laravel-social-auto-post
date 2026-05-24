# Gelişmiş Kullanım

## Laravel Events

Paket, yayın döngüsü sırasında yerel Laravel olaylarını tetikler. Bu, bir gönderinin veritabanı durumunu yanıt dizisini manuel olarak kontrol etmeden güncellemek için SaaS uygulamaları açısından son derece kullanışlıdır.

### Kullanılabilir Olaylar

1. `HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostPublishing`
   - API isteği gönderilmeden *önce* tetiklenir.
   - Özellikler: `$platform`, `$method`, `$parameters`

2. `HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostPublished`
   - Başarılı bir API isteğinden *sonra* tetiklenir.
   - Özellikler: `$platform`, `$method`, `$parameters`, `$result`

3. `HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostFailed`
   - API isteği bir istisna fırlatırsa tetiklenir.
   - Özellikler: `$platform`, `$method`, `$parameters`, `$exception`

### Örnek Kullanım

Dinleyicileri (Listeners) `EventServiceProvider` dosyanıza kaydedin:

```php
use HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostPublished;
use HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostFailed;

protected $listen = [
    SocialPostPublished::class => [
        UpdatePostStatusToPublished::class,
    ],
    SocialPostFailed::class => [
        LogSocialMediaError::class,
    ],
];
```

## Hata Yönetimi (Error Handling)

Paket kapsamlı bir hata yönetimi sunar:

```php
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;

try {
    $result = SocialMedia::share(['facebook', 'twitter'], 'İçerik', 'https://example.com');
    
    // Sonuçları kontrol et
    if ($result['error_count'] > 0) {
        foreach ($result['errors'] as $platform => $error) {
            echo "{$platform} üzerinde hata: {$error}\n";
        }
    }
    
} catch (SocialMediaException $e) {
    echo "Sosyal medya hatası: " . $e->getMessage();
}
```

## Yeniden Deneme Mantığı (Retry Logic)

Paket, başarısız istekleri üstel geri çekilme (exponential backoff) ile otomatik olarak yeniden dener. Bunu `config/autopost.php` dosyasında veya dinamik olarak yapılandırın:

```php
// Yeniden deneme girişimlerini yapılandır
config(['autopost.retry_attempts' => 5]);

// Zaman aşımını yapılandır
config(['autopost.timeout' => 60]);
```
