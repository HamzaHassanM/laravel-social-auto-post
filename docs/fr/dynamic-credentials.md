# Identifiants Dynamiques (Multi-Comptes & SaaS)

Vous pouvez transmettre des identifiants dynamiquement au moment de l'exécution pour gérer plusieurs comptes de réseaux sociaux sans modifier le fichier `.env`. C'est particulièrement utile pour les plateformes SaaS et les applications multi-locataires (multi-tenant).

## Utilisation de SocialMediaManager

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

// Partager en utilisant les identifiants spécifiques de l'utilisateur
$result = SocialMedia::withCredentials($customCredentials)
    ->share(['facebook', 'twitter'], 'Bonjour de la part de Utilisateur A !', 'https://example.com');
```

Si une plateforme est omise dans `$customCredentials`, le package reviendra automatiquement aux identifiants par défaut définis dans votre fichier `.env`.

## Utilisation des Façades Individuelles

Vous pouvez également utiliser des identifiants dynamiques sur les façades individuelles des plateformes :

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\Facebook;
use HamzaHassanM\LaravelSocialAutoPost\Facades\Twitter;

// Spécifique à Facebook
$facebook = Facebook::withCredentials('FACEBOOK_ACCESS_TOKEN', 'FACEBOOK_PAGE_ID');
$facebook->share('Bonjour depuis une page Facebook spécifique !', 'https://example.com');

// Spécifique à Twitter
$twitter = Twitter::withCredentials('BEARER', 'API_KEY', 'API_SECRET', 'ACCESS_TOKEN', 'ACCESS_SECRET');
$twitter->share('Bonjour depuis un compte Twitter spécifique !', 'https://example.com');
```
