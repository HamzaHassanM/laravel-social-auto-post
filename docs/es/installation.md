# Instalación y Requisitos

## Requisitos

- **PHP**: 8.1 o superior
- **Laravel**: 8.x, 9.x, 10.x, 11.x, 12.x o 13.x

## Instalación

Puedes instalar el paquete a través de composer:

```bash
composer require hamzahassanm/laravel-social-auto-post
```

## Publicación de la Configuración

Después de instalar el paquete, debes publicar el archivo de configuración. Ejecuta el siguiente comando:

```bash
php artisan vendor:publish --provider="HamzaHassanM\LaravelSocialAutoPost\SocialShareServiceProvider" --tag="autopost"
```

Esto creará un archivo `config/autopost.php` en tu aplicación donde puedes administrar tu configuración predeterminada y tus credenciales para todas las plataformas compatibles.
