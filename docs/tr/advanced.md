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

Paket, kalıcı hatalar (4xx) ve geçici hatalar (5xx, ağ zaman aşımları) arasında ayrım yapar. 4xx ile başarısız olan API çağrıları hemen başarısız (fail-fast) olurken, 5xx ve ağ hataları üstel geri çekilme (exponential backoff) ile otomatik olarak yeniden denenir.

Bunu `config/autopost.php` dosyasında veya dinamik olarak yapılandırın:

```php
// Yeniden deneme girişimlerinin sayısını yapılandır
config(['autopost.retry_attempts' => 5]);

// Üstel geri çekilme tabanını yapılandır (ör. 2sn, 4sn, 8sn için 2)
config(['autopost.retry_backoff_base' => 2]);

// Zaman aşımını (Timeout) yapılandır
config(['autopost.timeout' => 60]);
```

## Güvenlik (SSRF Protection)

Paket, uzak URL'lerden medya indirirken Sunucu Tarafı İstek Sahteciliğini (SSRF) ve DNS yeniden bağlama saldırılarını önlemek için `SafeMediaFetcher` kullanır. Dosyayı getirmeden önce ana bilgisayar adını (hostname) çözümler ve bunun özel (private), ayrılmış (reserved) veya geri döngü (loopback) IP'lerine işaret etmediğinden emin olur.

Güvenlik sınırlarını `config/autopost.php` dosyasında yapılandırın:

```php
// SSRF korumasını etkinleştirin veya devre dışı bırakın
config(['autopost.enforce_ssrf_protection' => true]);

// İzin verilen maksimum medya boyutunu bayt cinsinden ayarlayın (ör. 10MB)
config(['autopost.max_media_size' => 10485760]);
```
