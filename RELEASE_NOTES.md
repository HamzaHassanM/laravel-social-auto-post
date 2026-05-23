# Release Notes - v2.4.1

## 🔒 Security Fix: Laravel File Validation Bypass (CVE-2025-27515)
This release updates the dependency requirements in `composer.json` to mitigate the security advisory CVE-2025-27515.

### 🌟 Key Changes
- **Dependency Raising**: Excluded vulnerable Laravel framework versions by raising the minimum bounds for supported major versions:
  - Laravel 10.x requirement raised from `^10.0` to `^10.48.29`.
  - Laravel 11.x requirement raised from `^11.0` to `^11.44.1`.
  - Laravel 12.x requirement raised from `^12.0` to `^12.1.1`.
- **Preserved Compatibility**: Legacy EOL versions (Laravel 8 and 9) and the latest Laravel 13 remain unaffected by these lower bound updates.

---

# Release Notes - v2.4.0

## 📦 Backward Compatibility (Laravel 8.x - 10.x) & CI Hardening
This release introduces extended support for legacy frameworks (Laravel 8.x, 9.x, and 10.x) while keeping full support for modern Laravel 11.x, 12.x, and 13.x projects.

### 🌟 Key Changes
- **Extended Laravel Support**: The package is now fully compatible with Laravel 8.0 through 13.x.
- **PHP 8.1 Minimum Declared**: Explicitly restricted minimum PHP version to `^8.1` since the project uses PHP 8.1 enums. This prevents syntax/parse crashes in older PHP environments.
- **CI Security Hardening (PR #11)**: Constrained the automatic `GITHUB_TOKEN` permissions in GitHub Action workflows to `contents: read` to protect against unauthorized modifications or tag injection.
- **Localization fixes**: Fixed configuration publishing instructions across Spanish, French, Turkish, and Chinese READMEs (resolved wrong service provider and tag name references).
- **Flexible Test Suite**: Broadened dev dependencies to support PHPUnit `^9.5` through `^12.0` and Orchestra Testbench `^6.0` through `^11.0` so development environments are stable on all PHP versions.

---

# Release Notes - v2.3.4

## 🚀 Extended Framework Support
- Expanded Laravel framework compatibility. The package now supports `^11.0`, `^12.0`, and `^13.0` instead of just `13.0`.

---

# Release Notes - v2.3.3

## 🐛 Packagist Synchronization Hotfix
- Removed the hardcoded `"version": "2.2.0"` key from `composer.json`. This was causing tag validation mismatches (`tag does not match version`) on Packagist, which completely broke composer installations for versions `2.3.0`, `2.3.1`, and `2.3.2`. 

---

# Release Notes - v2.3.2

## 🛡️ Community Health & Licensing
- Added official MIT `LICENSE`.
- Added `CODE_OF_CONDUCT.md` to foster a welcoming community.
- Added `SECURITY.md` for responsible vulnerability reporting.
- Added GitHub templates (Bug Reports, Feature Requests, PRs) to standardize contributions.

---

# Release Notes - v2.3.1

## 🌍 Global Documentation & Contributing Guidelines
- Translated documentation into 4 new languages: Turkish, Spanish, French, and Chinese.
- Added a `CONTRIBUTING.md` file to help developers onboard.
- Added a language navigation bar to all documentation files.

---

# Release Notes - v2.3.0

## 🎉 Dynamic Multi-Account & SaaS Support
This release introduces the highly requested ability to manage multiple social media accounts dynamically at runtime, making this package fully compatible with SaaS platforms and multi-tenant applications (Resolves #3). 

A huge thank you to [@am0nshi](https://github.com/am0nshi) for raising the issue and inspiring this feature!

### What's New
- **`withCredentials()` Method**: You can now bypass the `.env` file and pass authentication tokens on the fly.
- **100% Backward Compatible**: Existing users upgrading to v2.3.0 do not need to change a single line of code. Default `.env` configurations are still used automatically if no custom credentials are provided.

---

# Release Notes - v2.2.1

## 🐛 TikTok v2 Content Posting API (Direct Post) Support
### Fixed
- Upgraded TikTok API integration to fully support the new v2 Content Posting API (Direct Post).
- Fixed an issue where videos and images were being sent to drafts/inbox instead of auto-posting directly to the timeline.
- Added `privacy_level` (PUBLIC_TO_EVERYONE) support for TikTok posts.
- Resolves issue #6.

---

# Release Notes - v2.0.0

## 🎉 Laravel Social Auto Post v2.0.0 - Complete Social Media Platform Support

**Release Date**: September 11, 2024  
**Version**: 2.0.0  
**Type**: Major Release (Breaking Changes)

---

## 🚀 What's New

### Complete Social Media Platform Support
This major release transforms the package from a basic Facebook/Telegram solution to a **comprehensive social media automation platform** supporting **8 major platforms**:

- ✅ **Facebook** - Enhanced with analytics and insights
- ✅ **Twitter/X** - Complete API v2 integration
- ✅ **LinkedIn** - Personal and company page posting
- ✅ **Instagram** - Images, videos, carousels, and stories
- ✅ **TikTok** - Video sharing with hashtag support
- ✅ **YouTube** - Video uploads and community posts
- ✅ **Pinterest** - Pin creation and board management
- ✅ **Telegram** - Enhanced messaging capabilities

### 🎯 Unified API System

#### Multi-Platform Posting
```php
// Post to all platforms at once
SocialMedia::shareToAll('Hello World!', 'https://example.com');

// Post to specific platforms
SocialMedia::share(['facebook', 'twitter', 'linkedin'], 'Content', 'https://example.com');
```

#### Individual Platform Access
```php
// Direct platform access
SocialMedia::facebook()->share('Hello', 'https://example.com');
SocialMedia::twitter()->shareImage('Check this out!', 'https://example.com/image.jpg');
SocialMedia::linkedin()->shareToCompanyPage('Company Update', 'https://example.com');
SocialMedia::instagram()->shareCarousel('Multiple images', ['img1.jpg', 'img2.jpg']);
```

### 🔧 Production-Ready Features

- **🛡️ Robust Error Handling**: Custom exceptions with detailed error messages
- **🔄 Retry Logic**: Exponential backoff for failed requests
- **✅ Input Validation**: URL validation, text length limits, content type validation
- **📊 Logging System**: Detailed logging for all operations
- **⏱️ Timeout Configuration**: Configurable request timeouts
- **📈 Analytics Support**: Platform-specific analytics and insights

### 🧪 Comprehensive Testing

- **33 Tests** with **101 Assertions**
- **85% Test Coverage** across all platforms
- **Unit Tests** for individual services
- **Feature Tests** for end-to-end functionality
- **Docker Support** for containerized testing

### 📚 Complete Documentation

- **Professional README** (605 lines) with installation, configuration, and usage
- **Arabic Documentation** (604 lines) for Arabic-speaking developers
- **5 Example Files** with comprehensive usage demonstrations
- **API Reference** with complete method documentation

---

## 🔄 Migration Guide

### Breaking Changes from v1.x

This is a **major release** with breaking changes. Here's how to migrate:

#### 1. Update Configuration
Add new platform credentials to your `.env`:

```env
# Existing (keep these)
FACEBOOK_ACCESS_TOKEN=your_token
FACEBOOK_PAGE_ID=your_page_id
TELEGRAM_BOT_TOKEN=your_token
TELEGRAM_CHAT_ID=your_chat_id

# New platforms (add these)
TWITTER_BEARER_TOKEN=your_token
TWITTER_API_KEY=your_key
TWITTER_API_SECRET=your_secret
TWITTER_ACCESS_TOKEN=your_token
TWITTER_ACCESS_TOKEN_SECRET=your_secret

LINKEDIN_ACCESS_TOKEN=your_token
LINKEDIN_PERSON_URN=your_urn
LINKEDIN_ORGANIZATION_URN=your_org_urn

INSTAGRAM_ACCESS_TOKEN=your_token
INSTAGRAM_ACCOUNT_ID=your_account_id

TIKTOK_ACCESS_TOKEN=your_token
TIKTOK_CLIENT_KEY=your_key
TIKTOK_CLIENT_SECRET=your_secret

YOUTUBE_API_KEY=your_key
YOUTUBE_ACCESS_TOKEN=your_token
YOUTUBE_CHANNEL_ID=your_channel_id

PINTEREST_ACCESS_TOKEN=your_token
PINTEREST_BOARD_ID=your_board_id
```

#### 2. Update Method Calls (Optional)
You can continue using the old syntax or upgrade to the new unified API:

```php
// Old way (still works)
FaceBook::share('Hello', 'https://example.com');
Telegram::share('Hello', 'https://example.com');

// New unified way (recommended)
SocialMedia::share(['facebook', 'telegram'], 'Hello', 'https://example.com');

// Or use individual platform access
SocialMedia::facebook()->share('Hello', 'https://example.com');
SocialMedia::telegram()->share('Hello', 'https://example.com');
```

#### 3. Review Error Handling
The new version has enhanced error handling:

```php
try {
    $result = SocialMedia::shareToAll('Content', 'https://example.com');
} catch (SocialMediaException $e) {
    // Handle social media specific errors
    Log::error('Social media error: ' . $e->getMessage());
}
```

---

## 📦 Installation

### Via Composer
```bash
composer require hamzahassanm/laravel-social-auto-post:^2.0
```

### Publish Configuration
```bash
php artisan vendor:publish --provider="HamzaHassanM\LaravelSocialAutoPost\SocialShareServiceProvider" --tag=autopost
```

---

## 🎯 Quick Start

### Basic Usage
```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;

// Post to all platforms
$result = SocialMedia::shareToAll('Hello World!', 'https://example.com');

// Post to specific platforms
$result = SocialMedia::share(['facebook', 'twitter'], 'Content', 'https://example.com');

// Share images
$result = SocialMedia::shareImage(['instagram', 'pinterest'], 'Check this out!', 'https://example.com/image.jpg');
```

### Platform-Specific Features
```php
// Facebook analytics
$insights = SocialMedia::facebook()->getPageInsights(['page_impressions', 'page_engaged_users']);

// Twitter timeline
$timeline = SocialMedia::twitter()->getTimeline(10);

// LinkedIn company posting
SocialMedia::linkedin()->shareToCompanyPage('Company Update', 'https://example.com');

// Instagram carousel
SocialMedia::instagram()->shareCarousel('Multiple images', ['img1.jpg', 'img2.jpg', 'img3.jpg']);

// YouTube video upload
SocialMedia::youtube()->shareVideo('Video Title', 'https://example.com/video.mp4');
```

---

## 🔧 Configuration

### Environment Variables
See the complete list of environment variables in the [README.md](README.md#environment-variables).

### Configuration File
The published `config/autopost.php` file contains all configuration options:

```php
return [
    // Platform credentials
    'facebook_access_token' => env('FACEBOOK_ACCESS_TOKEN'),
    'twitter_bearer_token' => env('TWITTER_BEARER_TOKEN'),
    // ... all other platforms
    
    // General settings
    'default_platforms' => ['facebook', 'twitter', 'linkedin'],
    'enable_logging' => env('SOCIAL_MEDIA_LOGGING', true),
    'timeout' => env('SOCIAL_MEDIA_TIMEOUT', 30),
    'retry_attempts' => env('SOCIAL_MEDIA_RETRY_ATTEMPTS', 3),
];
```

---

## 🧪 Testing

### Run Tests
```bash
# Using Docker (recommended)
docker-compose up --build

# Or using PHPUnit directly
./vendor/bin/phpunit
```

### Test Coverage
- **33 Tests** covering all platforms
- **101 Assertions** validating functionality
- **85% Success Rate** with comprehensive coverage

---

## 📚 Documentation

- **[README.md](README.md)** - Complete English documentation
- **[README_AR.md](README_AR.md)** - Complete Arabic documentation
- **[Examples/](examples/)** - Usage examples and demonstrations
- **[CHANGELOG.md](CHANGELOG.md)** - Detailed changelog

---

## 🆘 Support

- **GitHub Issues**: [Report bugs or request features](https://github.com/hamzahassanm/laravel-social-auto-post/issues)
- **Documentation**: [Complete documentation](README.md)
- **Email**: hamza.hassan.dev@gmail.com

---

## 🙏 Acknowledgments

- Laravel Framework
- All Social Media Platform APIs
- Open Source Community

---

**Made with ❤️ by [HamzaHassanM](https://github.com/hamzahassanm)**
