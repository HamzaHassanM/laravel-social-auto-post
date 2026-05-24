# Utilisation de Base

Le package fournit une API unifiée via la façade `SocialMedia` pour publier du contenu sur toutes les plateformes prises en charge simultanément ou individuellement.

## Publier sur Plusieurs Plateformes

Vous pouvez partager du texte et des liens sur une sélection de plateformes :

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;

// Publier sur des plateformes spécifiques
$result = SocialMedia::share(
    ['facebook', 'twitter', 'linkedin'], 
    'Bonjour le monde !', 
    'https://example.com'
);
```

## Publier sur Toutes les Plateformes

Si vous souhaitez diffuser un message sur toutes les plateformes configurées dans votre application :

```php
// Publier sur toutes les plateformes par défaut
$result = SocialMedia::shareToAll('Bonjour le monde !', 'https://example.com');
```

## Partager des Médias (Images et Vidéos)

Le package gère de manière transparente les téléchargements de médias sur les plateformes qui les prennent en charge :

### Images

```php
$result = SocialMedia::shareImage(
    ['instagram', 'pinterest', 'twitter'], 
    'Découvrez notre nouveau produit !', 
    'https://example.com/product.jpg' // Chemin local ou URL selon le support de la plateforme
);
```

### Vidéos

```php
$result = SocialMedia::shareVideo(
    ['youtube', 'tiktok', 'facebook'], 
    'Regardez notre nouveau tutoriel !', 
    'https://example.com/tutorial.mp4'
);
```

## Accès Individuel aux Plateformes

Si vous préférez interagir directement avec une plateforme spécifique, vous pouvez accéder à ses méthodes dédiées via sa façade ou par l'intermédiaire du gestionnaire principal :

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\FaceBook;
use HamzaHassanM\LaravelSocialAutoPost\Facades\Twitter;

// Accès direct par la façade
FaceBook::share('Bonjour Facebook !', 'https://example.com');
Twitter::share('Bonjour Twitter !', 'https://example.com');

// Ou via le gestionnaire SocialMedia
SocialMedia::facebook()->share('Bonjour', 'https://example.com');
SocialMedia::twitter()->share('Bonjour', 'https://example.com');
SocialMedia::linkedin()->shareToCompanyPage('Mise à jour', 'https://example.com');
```
