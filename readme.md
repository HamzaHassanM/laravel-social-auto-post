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
    'facebook_page_id'      => env('FACEBOOK_PAGE_ID'),
    'facebook_api_version'  => env('FACEBOOK_API_VERSION', 'v20.0'),
    'telegram_bot_token'    => env('TELEGRAM_BOT_TOKEN'),
    'telegram_chat_id'      => env('TELEGRAM_CHAT_ID'),
    'telegram_api_base_url' => env('TELEGRAM_API_BASE_URL', 'https://api.telegram.org/bot'),
];
```

Ensure you update your `.env` file with the necessary credentials:

```
FACEBOOK_ACCESS_TOKEN=your_facebook_access_token
FACEBOOK_PAGE_ID=your_facebook_page_id
TELEGRAM_BOT_TOKEN=your_telegram_bot_token
TELEGRAM_CHAT_ID=your_telegram_chat_id
```

## Usage

The package provides two facades for simple interaction with Facebook and Telegram.

### Facebook Methods

The `FaceBook` facade provides the following methods:

#### 1. `share(string $caption, string $url)`

Posts a status update with a URL to your Facebook page.

```php
use FaceBook;

$caption = 'Check out this article!';
$url = 'https://example.com/post/123';

FaceBook::share($caption, $url);
```

#### 2. `shareImage(string $caption, string $image_url)`

Posts an image with a caption to your Facebook page.

```php
use FaceBook;

$caption = 'Look at this amazing view!';
$imageUrl = 'https://example.com/images/view.jpg';

FaceBook::shareImage($caption, $imageUrl);
```

#### 3. `shareVideo(string $caption, string $video_url)`

Posts a video with a caption to your Facebook page.

```php
use FaceBook;

$caption = 'Watch this awesome video!';
$videoUrl = 'https://example.com/videos/video.mp4';

FaceBook::shareVideo($caption, $videoUrl);
```

#### 4. `getPageInsights(array $metrics = [], array $additionalParams = [])`

Retrieves insights (metrics) about your Facebook page. You can specify an array of metrics to retrieve or leave it empty to get default metrics.

```php
use FaceBook;

// Fetch insights for page impressions and engagement
$metrics = ['page_impressions', 'page_engaged_users'];
$additionalParams = ['since' => '2024-01-01', 'until' => '2024-01-31'];

$insights = FaceBook::getPageInsights($metrics, $additionalParams);
```

This method returns various insights depending on the metrics requested. The metrics can include page impressions, page views, page engagement, and more.

#### 5. `getPageInfo()`

Retrieves basic information about your Facebook page, such as name, category, and other details.

```php
use FaceBook;

$pageInfo = FaceBook::getPageInfo();
```

This method is useful when you want to fetch the current details of your Facebook page, such as the number of likes, category, or description.

---

### Telegram Methods

The `Telegram` facade offers the following methods for interacting with Telegram:

#### 1. `share(string $caption, string $url)`

Posts a message with a caption and link to your Telegram chat.

```php
use Telegram;

$caption = 'Check out this article!';
$url = 'https://example.com/post/123';

Telegram::share($caption, $url);
```

#### 2. `shareImage(string $caption, string $image_url)`

Posts an image with a caption to your Telegram chat.

```php
use Telegram;

$caption = 'Here is a cool image!';
$imageUrl = 'https://example.com/images/cool_image.jpg';

Telegram::shareImage($caption, $imageUrl);
```

#### 3. `shareDocument(string $caption, string $document_url)`

Shares a document with a caption in your Telegram chat.

```php
use Telegram;

$caption = 'Here is an important document!';
$documentUrl = 'https://example.com/files/document.pdf';

Telegram::shareDocument($caption, $documentUrl);
```

#### 4. `shareVideo(string $caption, string $video_url)`

Posts a video with a caption to your Telegram chat.

```php
use Telegram;

$caption = 'Watch this video!';
$videoUrl = 'https://example.com/videos/video.mp4';

Telegram::shareVideo($caption, $videoUrl);
```

#### 5. `getUpdates()`

Retrieves updates (messages and events) from your Telegram bot.

```php
use Telegram;

$updates = Telegram::getUpdates();
```

---


### Error Handling

When using the package to post on social media, exceptions might occur if the API tokens are invalid, the API limits are exceeded, or there's an error with the request. You can handle such exceptions as follows:

```php
try {
    FaceBook::share($caption, $url);
} catch (\Exception $e) {
    \Log::error("Error posting to Facebook: " . $e->getMessage());
}

try {
    Telegram::share($caption, $url);
} catch (\Exception $e) {
    \Log::error("Error posting to Telegram: " . $e->getMessage());
}
```

## Facades

The package provides two facades for easy access to social media platforms:

- `FaceBook` - For posting to Facebook via the Facebook Graph API.
- `Telegram` - For posting to Telegram via the Telegram Bot API.

### Facebook Facade Methods

- **share($caption, $url)**: Posts a status update with a URL.
- **shareImage($caption, $image_url)**: Posts an image with a caption.
- **shareVideo($caption, $video_url)**: Posts a video with a caption.
- **getPageInsights($metrics = [], $additionalParams = [])**: Retrieves Facebook Page insights.
- **getPageInfo()**: Retrieves Facebook Page information.

### Telegram Facade Methods

- **share($caption, $url)**: Posts a message with a URL.
- **shareImage($caption, $image_url)**: Posts an image with a caption.
- **shareVideo($caption, $video_url)**: Posts a video with a caption.
- **getUpdates()**: Retrieves updates, such as messages or events, from the Telegram bot.

## Service Provider (Optional for Laravel 11)

For Laravel 11, you need to manually register the service provider in the `bootstrap/providers.php` file if auto-discovery isn't enabled:

```php
<?php

return [
    // Other providers
    HamzaHassanM\LaravelSocialAutoPost\SocialShareServiceProvider::class,
];
```

For Laravel versions that support package auto-discovery, this step is unnecessary.

## Testing the Package

### Using Docker for Testing

To ensure that the package works correctly in all environments, you can use Docker to build a consistent testing environment.

### 1. Clone the Repository

```bash
git clone https://github.com/hamzahassanm/laravel-social-auto-post.git
cd laravel-social-auto-post
```

### 2. Build the Docker Image

Use Docker to build the testing environment:

```bash
docker-compose build
```

### 3. Run the Tests

After building the Docker image, you can run the tests:

```bash
docker-compose up
```

This command will start a container and execute the tests using PHPUnit.

### 4. Manually Access the Docker Container (Optional)

For debugging purposes, you can manually enter the container:

```bash
docker-compose run --rm app bash
```

Within the container, you can run additional tests or commands:

```bash
./vendor/bin/phpunit
```

### 5. Clean Up Docker Containers

Once testing is complete, remove the containers:

```bash
docker-compose down
```

---

## License

This package is licensed under the [MIT License](LICENSE).

## Contributing

Feel free to open issues or submit pull requests to improve this package. Please ensure that your contributions adhere to the existing code style and pass all tests.

## Contact

For any questions or issues, feel free to reach out to [HamzaHassanM](mailto:hamza.hassan.dev@gmail.com).

---

### Additional Notes

1.**Future Enhancements**: The package could be expanded to support additional platforms such as Twitter, LinkedIn, or Instagram.

