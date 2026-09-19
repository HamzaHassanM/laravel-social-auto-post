# Utilisation Avancée

## Événements Laravel (Events)

Le package déclenche des événements natifs Laravel pendant le cycle de publication. Ceci est extrêmement utile pour les applications SaaS afin de mettre à jour l'état de la publication dans la base de données sans avoir à vérifier manuellement le tableau de réponse.

### Événements Disponibles

1. `HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostPublishing`
   - Déclenché *avant* l'envoi de la requête API.
   - Propriétés : `$platform`, `$method`, `$parameters`

2. `HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostPublished`
   - Déclenché *après* une requête API réussie.
   - Propriétés : `$platform`, `$method`, `$parameters`, `$result`

3. `HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostFailed`
   - Déclenché si la requête API lance une exception.
   - Propriétés : `$platform`, `$method`, `$parameters`, `$exception`

### Exemple d'Utilisation

Enregistrez vos Listeners dans votre `EventServiceProvider` :

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

## Gestion des Erreurs (Error Handling)

Le package fournit une gestion complète des erreurs :

```php
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;

try {
    $result = SocialMedia::share(['facebook', 'twitter'], 'Contenu', 'https://example.com');
    
    // Vérifier les résultats
    if ($result['error_count'] > 0) {
        foreach ($result['errors'] as $platform => $error) {
            echo "Erreur sur {$platform} : {$error}\n";
        }
    }
    
} catch (SocialMediaException $e) {
    echo "Erreur réseaux sociaux : " . $e->getMessage();
}
```

## Logique de Réessai (Retry Logic)

Le package fait la distinction entre les erreurs persistantes (4xx) et les erreurs transitoires (5xx, délais d'attente réseau). Les appels API échouant avec 4xx échoueront rapidement (fail-fast), tandis que les erreurs 5xx et réseau sont réessayées automatiquement avec un recul exponentiel.

Configurez ceci dans `config/autopost.php` ou dynamiquement :

```php
// Configurer le nombre de tentatives
config(['autopost.retry_attempts' => 5]);

// Configurer la base du recul exponentiel (ex. 2 pour 2s, 4s, 8s)
config(['autopost.retry_backoff_base' => 2]);

// Configurer le délai d'attente (Timeout)
config(['autopost.timeout' => 60]);
```

## Sécurité (SSRF Protection)

Lors du téléchargement de médias à partir d'URL distantes, le package utilise `SafeMediaFetcher` pour prévenir les falsifications de requêtes côté serveur (SSRF) et les attaques par rebinding DNS. Il résout le nom d'hôte et s'assure qu'il ne pointe pas vers des IP privées, réservées ou de bouclage (loopback) avant de récupérer le fichier.

Configurez les limites de sécurité dans `config/autopost.php` :

```php
// Activer ou désactiver la protection SSRF
config(['autopost.enforce_ssrf_protection' => true]);

// Définir la taille maximale autorisée pour les médias en octets (ex. 10 Mo)
config(['autopost.max_media_size' => 10485760]);
```
