# Uso Básico

El paquete proporciona una API unificada a través de la fachada `SocialMedia` para publicar contenido en todas las plataformas compatibles de forma simultánea o individual.

## Publicación en Varias Plataformas

Puedes compartir texto y enlaces en una selección de plataformas:

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;

// Publicar en plataformas específicas
$result = SocialMedia::share(
    ['facebook', 'twitter', 'linkedin'], 
    '¡Hola Mundo!', 
    'https://example.com'
);
```

## Publicación en Todas las Plataformas

Si deseas emitir un mensaje a cada plataforma configurada en tu aplicación:

```php
// Publicar en todas las plataformas predeterminadas
$result = SocialMedia::shareToAll('¡Hola Mundo!', 'https://example.com');
```

## Compartir Medios (Imágenes y Videos)

El paquete maneja sin problemas las cargas de medios a plataformas que los admiten:

### Imágenes

```php
$result = SocialMedia::shareImage(
    ['instagram', 'pinterest', 'twitter'], 
    '¡Mira nuestro nuevo producto!', 
    'https://example.com/product.jpg' // Ruta local o URL dependiendo del soporte de la plataforma
);
```

### Videos

```php
$result = SocialMedia::shareVideo(
    ['youtube', 'tiktok', 'facebook'], 
    '¡Mira nuestro nuevo tutorial!', 
    'https://example.com/tutorial.mp4'
);
```

## Acceso Individual a las Plataformas

Si prefieres interactuar con una plataforma específica directamente, puedes acceder a sus métodos dedicados a través de su fachada o mediante el administrador principal:

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\FaceBook;
use HamzaHassanM\LaravelSocialAutoPost\Facades\Twitter;

// Acceso directo a la fachada
FaceBook::share('¡Hola Facebook!', 'https://example.com');
Twitter::share('¡Hola Twitter!', 'https://example.com');

// O a través del administrador SocialMedia
SocialMedia::facebook()->share('Hola', 'https://example.com');
SocialMedia::twitter()->share('Hola', 'https://example.com');
SocialMedia::linkedin()->shareToCompanyPage('Actualización', 'https://example.com');
```
