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

Le package réessaie automatiquement les requêtes échouées avec un recul exponentiel (exponential backoff). Configurez ceci dans `config/autopost.php` ou dynamiquement :

```php
// Configurer les tentatives de réessai
config(['autopost.retry_attempts' => 5]);

// Configurer le délai d'attente
config(['autopost.timeout' => 60]);
```
