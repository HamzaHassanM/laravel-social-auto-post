# Laravel Social Auto Post

A Laravel package to facilitate automatic social media posting on platforms like Facebook and Telegram.

## Installation

To install the package, run the following command:

```bash
composer require hamzahassanm/laravel-social-auto-post
```

## Configuration

After installing the package, publish the configuration file using the following command:

```bash
php artisan vendor:publish --provider="HamzaHassanM\LaravelSocialAutoPost\SocialShareServiceProvider" --tag=autopost
```

This command will create a `config/autopost.php` file where you can configure your social media credentials and other settings.

### Example Configuration

In your `config/autopost.php` file, add your Facebook and Telegram credentials:

```php
return [
    'facebook_access_token' => env('FACEBOOK_ACCESS_TOKEN'),
    'facebook_page_id' => env('FACEBOOK_PAGE_ID'),
    'telegram_bot_token' => env('TELEGRAM_BOT_TOKEN'),
    'telegram_chat_id' => env('TELEGRAM_CHAT_ID'),
];
```

Make sure to add these values to your `.env` file:

```
FACEBOOK_ACCESS_TOKEN=your_facebook_access_token
FACEBOOK_PAGE_ID=your_facebook_page_id
TELEGRAM_BOT_TOKEN=your_telegram_bot_token
TELEGRAM_CHAT_ID=your_telegram_chat_id
```

## Usage

### Posting to Facebook

To post to Facebook, use the `FaceBook` facade:

```php
use FaceBook;

$caption = 'This is a caption';
$url = 'https://example.com/post/123';

FaceBook::share($caption, $url);
```

### Posting to Telegram

To post to Telegram, use the `Telegram` facade:

```php
use Telegram;

$caption = 'This is a caption';
$url = 'https://example.com/post/123';

Telegram::share($caption, $url);
```

## Facades

The package provides two facades for easy access:

- `FaceBook` - For posting to Facebook.
- `Telegram` - For posting to Telegram.

## Service Provider

For Laravel 11, you need to register the service provider in the `bootstrap/providers.php` file:

```php
<?php

return [
    ... All other providers
    HamzaHassanM\LaravelSocialAutoPost\SocialShareServiceProvider::class,
];
```

If you are using Laravel's auto-discovery feature, the service provider will be automatically registered, and you don't need to manually add it.

## License

This package is licensed under the [MIT License](LICENSE).

## Contributing

Feel free to open issues or submit pull requests to improve this package. Please ensure that your code adheres to the existing code style and passes all tests.

## Contact

For any questions or issues, please contact [HamzaHassanM](mailto:hamza.hassan.dev@gmail.com).
