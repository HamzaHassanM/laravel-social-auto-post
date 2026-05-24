# Dynamic Credentials (Multi-Account & SaaS)

You can dynamically pass credentials at runtime to manage multiple social media accounts without changing the `.env` file. This is particularly useful for SaaS platforms and multi-tenant applications.

## Using the SocialMediaManager

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

// Share using the specific user's credentials
$result = SocialMedia::withCredentials($customCredentials)
    ->share(['facebook', 'twitter'], 'Hello from User A!', 'https://example.com');
```

If a platform is omitted from `$customCredentials`, the package will automatically fall back to the default credentials set in your `.env` file.

## Using Individual Platform Facades

You can also use dynamic credentials on individual platform facades:

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\Facebook;
use HamzaHassanM\LaravelSocialAutoPost\Facades\Twitter;

// Facebook specific
$facebook = Facebook::withCredentials('FACEBOOK_ACCESS_TOKEN', 'FACEBOOK_PAGE_ID');
$facebook->share('Hello from a specific Facebook page!', 'https://example.com');

// Twitter specific
$twitter = Twitter::withCredentials('BEARER', 'API_KEY', 'API_SECRET', 'ACCESS_TOKEN', 'ACCESS_SECRET');
$twitter->share('Hello from a specific Twitter account!', 'https://example.com');
```
