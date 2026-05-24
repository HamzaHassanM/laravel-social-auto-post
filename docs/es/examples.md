# Ejemplos Completos

El paquete viene con varios ejemplos prácticos. A continuación, se muestran algunos de los patrones más comunes y avanzados que puede usar en su aplicación Laravel.

## 1. Comprobación de Resultados Multiplataforma

Cuando publica en varias plataformas, recibe una matriz de resultados detallada. Así es como puede procesarlo:

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;

try {
    $platforms = ['facebook', 'twitter', 'linkedin'];
    $result = SocialMedia::share($platforms, '¡Excelentes noticias! ¡Acabamos de lanzar nuestra nueva función! 🚀', 'https://example.com/feature');
    
    echo "✅ Publicado en " . $result['success_count'] . " de " . $result['total_platforms'] . " plataformas\n";
    
    foreach ($result['results'] as $platform => $platformResult) {
        if ($platformResult['success']) {
            echo "✅ {$platform}: Éxito\n";
        } else {
            echo "❌ {$platform}: " . $platformResult['error'] . "\n";
        }
    }
    
} catch (SocialMediaException $e) {
    echo "❌ Error multiplataforma: " . $e->getMessage() . "\n";
}
```

## 2. Análisis y Características Específicas

Algunas plataformas como Facebook le permiten recuperar información y estadísticas de la página.

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\FaceBook;

try {
    // Obtener información básica de la página
    $pageInfo = FaceBook::getPageInfo();
    
    echo "📋 Información de la Página:\n";
    echo "   Nombre: " . ($pageInfo['name'] ?? 'Desconocido') . "\n";
    echo "   Categoría: " . ($pageInfo['category'] ?? 'Desconocido') . "\n";
    echo "   Seguidores: " . ($pageInfo['followers_count'] ?? 'Desconocido') . "\n";
    
} catch (\Exception $e) {
    echo "Error al obtener la información de la página: " . $e->getMessage();
}
```

## 3. Manejo Avanzado de Errores y Reintentos

Puede configurar su aplicación para que reintente automáticamente las publicaciones fallidas, o capture manualmente errores específicos de la plataforma.

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;
use Illuminate\Support\Facades\Log;

// Aumentar temporalmente el tiempo de espera y los intentos de un video grande
config(['autopost.timeout' => 120]);
config(['autopost.retry_attempts' => 5]);

$result = SocialMedia::shareVideo(
    ['youtube', 'facebook'], 
    'Tutorial detallado de 10 minutos sobre Laravel Auto Post', 
    storage_path('app/videos/tutorial.mp4')
);

if (!empty($result['errors'])) {
    // Manejar las fallas específicas después de que se hayan agotado todos los reintentos
    Log::error('Error al subir el video a algunas plataformas', $result['errors']);
}
```
