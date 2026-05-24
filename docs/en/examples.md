# Comprehensive Examples

The package comes with several practical examples. Below are some of the most common and advanced patterns you can use in your Laravel application.

## 1. Checking Multi-Platform Results

When you post to multiple platforms, you receive a detailed result array. Here is how you can process it:

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;

try {
    $platforms = ['facebook', 'twitter', 'linkedin'];
    $result = SocialMedia::share($platforms, 'Exciting news! We just launched our new feature! 🚀', 'https://example.com/feature');
    
    echo "✅ Posted to " . $result['success_count'] . " out of " . $result['total_platforms'] . " platforms\n";
    
    foreach ($result['results'] as $platform => $platformResult) {
        if ($platformResult['success']) {
            echo "✅ {$platform}: Success\n";
        } else {
            echo "❌ {$platform}: " . $platformResult['error'] . "\n";
        }
    }
    
} catch (SocialMediaException $e) {
    echo "❌ Multi-platform error: " . $e->getMessage() . "\n";
}
```

## 2. Platform-Specific Analytics & Features

Some platforms like Facebook allow you to retrieve insights and page information.

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\FaceBook;

try {
    // Get basic page information
    $pageInfo = FaceBook::getPageInfo();
    
    echo "📋 Page Information:\n";
    echo "   Name: " . ($pageInfo['name'] ?? 'Unknown') . "\n";
    echo "   Category: " . ($pageInfo['category'] ?? 'Unknown') . "\n";
    echo "   Followers: " . ($pageInfo['followers_count'] ?? 'Unknown') . "\n";
    
} catch (\Exception $e) {
    echo "Error fetching page info: " . $e->getMessage();
}
```

## 3. Advanced Error Handling & Retries

You can configure your application to automatically retry failed posts, or manually catch specific platform errors.

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;
use Illuminate\Support\Facades\Log;

// Temporarily increase timeout and retry attempts for a large video
config(['autopost.timeout' => 120]);
config(['autopost.retry_attempts' => 5]);

$result = SocialMedia::shareVideo(
    ['youtube', 'facebook'], 
    'Detailed 10-minute tutorial on Laravel Auto Post', 
    storage_path('app/videos/tutorial.mp4')
);

if (!empty($result['errors'])) {
    // Handle the specific failures after all retries have been exhausted
    Log::error('Failed to upload video to some platforms', $result['errors']);
}
```
