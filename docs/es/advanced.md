# Uso Avanzado

## Eventos de Laravel (Events)

El paquete dispara eventos nativos de Laravel durante el ciclo de vida de la publicación. Esto es sumamente útil para que las aplicaciones SaaS actualicen el estado de la publicación en la base de datos sin revisar manualmente el arreglo de respuesta.

### Eventos Disponibles

1. `HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostPublishing`
   - Se dispara *antes* de enviar la solicitud a la API.
   - Propiedades: `$platform`, `$method`, `$parameters`

2. `HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostPublished`
   - Se dispara *después* de una solicitud a la API exitosa.
   - Propiedades: `$platform`, `$method`, `$parameters`, `$result`

3. `HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostFailed`
   - Se dispara si la solicitud a la API lanza una excepción.
   - Propiedades: `$platform`, `$method`, `$parameters`, `$exception`

### Ejemplo de Uso

Registra tus oyentes (Listeners) en tu `EventServiceProvider`:

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

## Manejo de Errores (Error Handling)

El paquete proporciona un manejo integral de errores:

```php
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;

try {
    $result = SocialMedia::share(['facebook', 'twitter'], 'Contenido', 'https://example.com');
    
    // Verificar resultados
    if ($result['error_count'] > 0) {
        foreach ($result['errors'] as $platform => $error) {
            echo "Error en {$platform}: {$error}\n";
        }
    }
    
} catch (SocialMediaException $e) {
    echo "Error de redes sociales: " . $e->getMessage();
}
```

## Lógica de Reintento (Retry Logic)

El paquete distingue entre errores persistentes (4xx) y errores temporales (5xx, tiempo de espera de la red). Las llamadas a la API fallidas con 4xx fallarán rápidamente (fail-fast), mientras que los errores 5xx y de red se reintentan automáticamente con un retroceso exponencial.

Configura esto en `config/autopost.php` o dinámicamente:

```php
// Configurar el número de intentos de reintento
config(['autopost.retry_attempts' => 5]);

// Configurar la base del retroceso exponencial (ej. 2 para 2s, 4s, 8s)
config(['autopost.retry_backoff_base' => 2]);

// Configurar tiempo de espera (Timeout)
config(['autopost.timeout' => 60]);
```

## Seguridad (SSRF Protection)

Al descargar medios desde URLs remotas, el paquete utiliza `SafeMediaFetcher` para prevenir falsificación de peticiones del lado del servidor (SSRF) y ataques de revinculación DNS. Resuelve el nombre de host y se asegura de que no apunte a IPs privadas, reservadas o de bucle local (loopback) antes de obtener el archivo.

Configura los límites de seguridad en `config/autopost.php`:

```php
// Habilitar o deshabilitar protección SSRF
config(['autopost.enforce_ssrf_protection' => true]);

// Establecer tamaño máximo de medios permitido en bytes (ej. 10MB)
config(['autopost.max_media_size' => 10485760]);
```
