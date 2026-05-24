# Credenciales Dinámicas (Multicuentas y SaaS)

Puedes pasar credenciales dinámicamente en tiempo de ejecución para administrar múltiples cuentas de redes sociales sin cambiar el archivo `.env`. Esto es particularmente útil para plataformas SaaS y aplicaciones multiinquilino (multi-tenant).

## Uso de SocialMediaManager

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;

$customCredentials = [
    'facebook' => [
        'access_token' => 'USER_A_FACEBOOK_TOKEN',
        'page_id' => 'USER_A_FACEBOOK_PAGE_ID'
    ],
    'twitter' => [
        'bearer_token' => 'USER_A_TWITTER_BEARER',
        'api_key' => 'USER_A_TWITTER_KEY',
        'api_secret' => 'USER_A_TWITTER_SECRET',
        'access_token' => 'USER_A_TWITTER_ACCESS',
        'access_token_secret' => 'USER_A_TWITTER_ACCESS_SECRET'
    ]
];

// Compartir usando las credenciales del usuario específico
$result = SocialMedia::withCredentials($customCredentials)
    ->share(['facebook', 'twitter'], '¡Hola desde Usuario A!', 'https://example.com');
```

Si se omite una plataforma de `$customCredentials`, el paquete volverá automáticamente a las credenciales predeterminadas establecidas en tu archivo `.env`.

## Uso de Fachadas de Plataformas Individuales

También puedes usar credenciales dinámicas en las fachadas de plataformas individuales:

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\Facebook;
use HamzaHassanM\LaravelSocialAutoPost\Facades\Twitter;

// Específico para Facebook
$facebook = Facebook::withCredentials('FACEBOOK_ACCESS_TOKEN', 'FACEBOOK_PAGE_ID');
$facebook->share('¡Hola desde una página de Facebook específica!', 'https://example.com');

// Específico para Twitter
$twitter = Twitter::withCredentials('BEARER', 'API_KEY', 'API_SECRET', 'ACCESS_TOKEN', 'ACCESS_SECRET');
$twitter->share('¡Hola desde una cuenta de Twitter específica!', 'https://example.com');
```
