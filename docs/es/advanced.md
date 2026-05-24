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

El paquete reintenta automáticamente las solicitudes fallidas con un retroceso exponencial (exponential backoff). Configura esto en `config/autopost.php` o dinámicamente:

```php
// Configurar intentos de reintento
config(['autopost.retry_attempts' => 5]);

// Configurar tiempo de espera
config(['autopost.timeout' => 60]);
```
