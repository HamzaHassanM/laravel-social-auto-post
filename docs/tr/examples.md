# Kapsamlı Örnekler

Paket çeşitli pratik örneklerle birlikte gelir. Aşağıda Laravel uygulamanızda kullanabileceğiniz en yaygın ve gelişmiş desenlerden bazıları verilmiştir.

## 1. Çoklu Platform Sonuçlarını Kontrol Etme

Birden çok platformda yayınladığınızda ayrıntılı bir sonuç dizisi alırsınız. Bunu şu şekilde işleyebilirsiniz:

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;

try {
    $platforms = ['facebook', 'twitter', 'linkedin'];
    $result = SocialMedia::share($platforms, 'Heyecan verici haberler! Yeni özelliğimizi başlattık! 🚀', 'https://example.com/feature');
    
    echo "✅ Toplam " . $result['total_platforms'] . " platformun " . $result['success_count'] . " tanesine başarıyla gönderildi\n";
    
    foreach ($result['results'] as $platform => $platformResult) {
        if ($platformResult['success']) {
            echo "✅ {$platform}: Başarılı\n";
        } else {
            echo "❌ {$platform}: " . $platformResult['error'] . "\n";
        }
    }
    
} catch (SocialMediaException $e) {
    echo "❌ Çoklu platform hatası: " . $e->getMessage() . "\n";
}
```

## 2. Platforma Özgü Analitikler ve Özellikler

Facebook gibi bazı platformlar, içgörüleri ve sayfa bilgilerini almanıza olanak tanır.

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\FaceBook;

try {
    // Temel sayfa bilgilerini al
    $pageInfo = FaceBook::getPageInfo();
    
    echo "📋 Sayfa Bilgileri:\n";
    echo "   İsim: " . ($pageInfo['name'] ?? 'Bilinmiyor') . "\n";
    echo "   Kategori: " . ($pageInfo['category'] ?? 'Bilinmiyor') . "\n";
    echo "   Takipçiler: " . ($pageInfo['followers_count'] ?? 'Bilinmiyor') . "\n";
    
} catch (\Exception $e) {
    echo "Sayfa bilgisi alınırken hata oluştu: " . $e->getMessage();
}
```

## 3. Gelişmiş Hata Yönetimi ve Yeniden Denemeler

Uygulamanızı başarısız gönderileri otomatik olarak yeniden deneyecek şekilde yapılandırabilir veya belirli platform hatalarını manuel olarak yakalayabilirsiniz.

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;
use Illuminate\Support\Facades\Log;

// Büyük bir video için zaman aşımını ve yeniden deneme sayısını geçici olarak artırın
config(['autopost.timeout' => 120]);
config(['autopost.retry_attempts' => 5]);

$result = SocialMedia::shareVideo(
    ['youtube', 'facebook'], 
    'Laravel Auto Post hakkında ayrıntılı 10 dakikalık eğitim', 
    storage_path('app/videos/tutorial.mp4')
);

if (!empty($result['errors'])) {
    // Tüm yeniden denemeler tükendikten sonra belirli arızaları yönetin
    Log::error('Video bazı platformlara yüklenemedi', $result['errors']);
}
```
