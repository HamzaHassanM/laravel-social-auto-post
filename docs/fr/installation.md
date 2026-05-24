# Installation et Prérequis

## Prérequis

- **PHP** : 8.1 ou supérieur
- **Laravel** : 8.x, 9.x, 10.x, 11.x, 12.x ou 13.x

## Installation

Vous pouvez installer le package via composer :

```bash
composer require hamzahassanm/laravel-social-auto-post
```

## Publication de la Configuration

Après avoir installé le package, vous devez publier le fichier de configuration. Exécutez la commande suivante :

```bash
php artisan vendor:publish --provider="HamzaHassanM\LaravelSocialAutoPost\SocialShareServiceProvider" --tag="autopost"
```

Cela créera un fichier `config/autopost.php` dans votre application où vous pourrez gérer vos paramètres par défaut et vos identifiants pour toutes les plateformes prises en charge.
