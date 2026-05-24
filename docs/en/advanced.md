# Advanced Usage

## Laravel Events

The package fires native Laravel events during the posting lifecycle. This is extremely useful for SaaS applications to update the database state of a post without manually checking the response array.

### Available Events

1. `HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostPublishing`
   - Fired *before* the API request is sent.
   - Properties: `$platform`, `$method`, `$parameters`

2. `HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostPublished`
   - Fired *after* a successful API request.
   - Properties: `$platform`, `$method`, `$parameters`, `$result`

3. `HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostFailed`
   - Fired if the API request throws an exception.
   - Properties: `$platform`, `$method`, `$parameters`, `$exception`

### Example Usage

Register listeners in your `EventServiceProvider`:

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

## Error Handling

The package provides comprehensive error handling:

```php
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;

try {
    $result = SocialMedia::share(['facebook', 'twitter'], 'Content', 'https://example.com');
    
    // Check results
    if ($result['error_count'] > 0) {
        foreach ($result['errors'] as $platform => $error) {
            echo "Error on {$platform}: {$error}\n";
        }
    }
    
} catch (SocialMediaException $e) {
    echo "Social media error: " . $e->getMessage();
}
```

## Retry Logic

The package automatically retries failed requests with exponential backoff. Configure this in `config/autopost.php` or dynamically:

```php
// Configure retry attempts
config(['autopost.retry_attempts' => 5]);

// Configure timeout
config(['autopost.timeout' => 60]);
```
