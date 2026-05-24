# Basic Usage

The package provides a unified API through the `SocialMedia` facade to post content across all supported platforms simultaneously or individually.

## Posting to Multiple Platforms

You can share text and links across a selection of platforms:

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;

// Post to specific platforms
$result = SocialMedia::share(
    ['facebook', 'twitter', 'linkedin'], 
    'Hello World!', 
    'https://example.com'
);
```

## Posting to All Platforms

If you want to broadcast a message to every platform configured in your application:

```php
// Post to all default platforms
$result = SocialMedia::shareToAll('Hello World!', 'https://example.com');
```

## Sharing Media (Images & Videos)

The package seamlessly handles media uploads to platforms that support them:

### Images

```php
$result = SocialMedia::shareImage(
    ['instagram', 'pinterest', 'twitter'], 
    'Check out our new product!', 
    'https://example.com/product.jpg' // Local path or URL depending on platform support
);
```

### Videos

```php
$result = SocialMedia::shareVideo(
    ['youtube', 'tiktok', 'facebook'], 
    'Watch our new tutorial!', 
    'https://example.com/tutorial.mp4'
);
```

## Individual Platform Access

If you prefer to interact with a specific platform directly, you can access its dedicated methods via its facade or through the main manager:

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\FaceBook;
use HamzaHassanM\LaravelSocialAutoPost\Facades\Twitter;

// Direct facade access
FaceBook::share('Hello Facebook!', 'https://example.com');
Twitter::share('Hello Twitter!', 'https://example.com');

// Or via the SocialMedia manager
SocialMedia::facebook()->share('Hello', 'https://example.com');
SocialMedia::twitter()->share('Hello', 'https://example.com');
SocialMedia::linkedin()->shareToCompanyPage('Update', 'https://example.com');
```
