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

The package distinguishes between persistent errors (4xx) and transient errors (5xx, network timeouts). Failed 4xx API calls will fail-fast, while 5xx and network errors are automatically retried with exponential backoff.

Configure this in `config/autopost.php` or dynamically:

```php
// Configure retry attempts
config(['autopost.retry_attempts' => 5]);

// Configure retry exponential backoff base (e.g. 2 for 2s, 4s, 8s)
config(['autopost.retry_backoff_base' => 2]);

// Configure timeout
config(['autopost.timeout' => 60]);
```

## Security (SSRF Protection)

When downloading media from remote URLs, the package utilizes `SafeMediaFetcher` to prevent Server-Side Request Forgery (SSRF) and DNS rebinding attacks. It resolves the hostname and ensures it does not map to private, reserved, or loopback IPs before fetching the file.

Configure security limits in `config/autopost.php`:

```php
// Enable or disable SSRF protection
config(['autopost.enforce_ssrf_protection' => true]);

// Set maximum allowed media size in bytes (e.g. 10MB)
config(['autopost.max_media_size' => 10485760]);
```
