# Exemples Complets

Le package est livré avec plusieurs exemples pratiques. Voici quelques-uns des modèles les plus courants et avancés que vous pouvez utiliser dans votre application Laravel.

## 1. Vérification des Résultats Multi-Plateformes

Lorsque vous publiez sur plusieurs plateformes, vous recevez un tableau détaillé des résultats. Voici comment vous pouvez le traiter :

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;

try {
    $platforms = ['facebook', 'twitter', 'linkedin'];
    $result = SocialMedia::share($platforms, 'Excellente nouvelle ! Nous venons de lancer notre nouvelle fonctionnalité ! 🚀', 'https://example.com/feature');
    
    echo "✅ Publié sur " . $result['success_count'] . " plateformes sur " . $result['total_platforms'] . "\n";
    
    foreach ($result['results'] as $platform => $platformResult) {
        if ($platformResult['success']) {
            echo "✅ {$platform}: Succès\n";
        } else {
            echo "❌ {$platform}: " . $platformResult['error'] . "\n";
        }
    }
    
} catch (SocialMediaException $e) {
    echo "❌ Erreur multi-plateformes : " . $e->getMessage() . "\n";
}
```

## 2. Analyses et Fonctionnalités Spécifiques

Certaines plateformes comme Facebook vous permettent de récupérer des informations et des statistiques de page.

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\FaceBook;

try {
    // Obtenir les informations de base de la page
    $pageInfo = FaceBook::getPageInfo();
    
    echo "📋 Informations de la Page :\n";
    echo "   Nom : " . ($pageInfo['name'] ?? 'Inconnu') . "\n";
    echo "   Catégorie : " . ($pageInfo['category'] ?? 'Inconnu') . "\n";
    echo "   Abonnés : " . ($pageInfo['followers_count'] ?? 'Inconnu') . "\n";
    
} catch (\Exception $e) {
    echo "Erreur lors de la récupération des informations de la page : " . $e->getMessage();
}
```

## 3. Gestion Avancée des Erreurs et Réessais

Vous pouvez configurer votre application pour réessayer automatiquement les publications échouées, ou intercepter manuellement les erreurs spécifiques aux plateformes.

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;
use Illuminate\Support\Facades\Log;

// Augmenter temporairement le délai d'attente et les tentatives pour une vidéo volumineuse
config(['autopost.timeout' => 120]);
config(['autopost.retry_attempts' => 5]);

$result = SocialMedia::shareVideo(
    ['youtube', 'facebook'], 
    'Tutoriel détaillé de 10 minutes sur Laravel Auto Post', 
    storage_path('app/videos/tutorial.mp4')
);

if (!empty($result['errors'])) {
    // Gérer les échecs spécifiques une fois toutes les tentatives épuisées
    Log::error('Échec du téléchargement de la vidéo sur certaines plateformes', $result['errors']);
}
```
