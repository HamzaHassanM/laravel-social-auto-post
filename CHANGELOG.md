# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.3.0] - 2026-05-23

### Added
- **Multi-Account Support**: Added `withCredentials()` method to dynamically authenticate multiple social media accounts at runtime (Resolves #3). Special thanks to [@am0nshi](https://github.com/am0nshi) for suggesting and inspiring this feature!
- **Public Constructors**: Platform services can now be manually instantiated for SaaS and multi-tenant applications.

## [2.2.1] - 2026-05-23

### Fixed
- **TikTok API**: Upgraded TikTok API integration to fully support the new v2 Content Posting API (Direct Post) (Resolves #6).
- **TikTok Privacy**: Added `privacy_level` (PUBLIC_TO_EVERYONE) support for TikTok posts.

## [2.2.0] - 2025-03-25

### Updated
- **Laravel 13 Support**: Updated Laravel framework compatibility to ^13.0
- **PHPUnit 12**: Updated test dependencies to ^12.0
- **Orchestra Testbench 11**: Updated testing dependencies to ^11.0

## [2.1.0] - Previous Release

### Added
- Various improvements and bug fixes

## [2.0.0] - 2024-09-11

### 🚀 Major Release - Complete Social Media Platform Support

This is a major release that transforms the package from a basic Facebook/Telegram solution to a comprehensive social media automation platform supporting 8 major platforms.

### ✨ Added

#### New Social Media Platforms
- **Twitter/X Integration**: Complete Twitter API v2 support with tweet posting, image sharing, and timeline access
- **LinkedIn Integration**: Personal and company page posting with asset upload support
- **Instagram Integration**: Image/video posting, carousel posts, and story sharing
- **TikTok Integration**: Video sharing with hashtag support and user analytics
- **YouTube Integration**: Video uploads, community posts, and channel analytics
- **Pinterest Integration**: Pin creation, board management, and analytics

#### Unified API System
- **SocialMedia Facade**: Single entry point for multi-platform posting
- **SocialMediaManager**: Orchestrates posting across multiple platforms
- **Batch Operations**: Post to multiple platforms simultaneously
- **Platform-Specific Access**: Direct access to individual platform features

#### Advanced Features
- **Comprehensive Error Handling**: Custom exceptions with detailed error messages
- **Retry Logic**: Exponential backoff for failed requests
- **Input Validation**: URL validation, text length limits, content type validation
- **Logging System**: Detailed logging for all operations
- **Timeout Configuration**: Configurable request timeouts
- **Analytics Support**: Platform-specific analytics and insights

#### Testing & Quality
- **Complete Test Suite**: 33 tests with 101 assertions covering all platforms
- **Unit Tests**: Individual service testing with mocking
- **Feature Tests**: End-to-end functionality testing
- **85% Test Coverage**: Comprehensive test coverage across all components

#### Documentation & Examples
- **Professional Documentation**: Complete README with installation, configuration, and usage
- **Arabic Documentation**: Full Arabic translation (README_AR.md)
- **Example Directory**: 5 comprehensive example files
- **API Reference**: Complete method documentation and signatures

### 🔧 Enhanced

#### Existing Platforms
- **Facebook Service**: Enhanced with better error handling and validation
- **Telegram Service**: Improved method signatures and error handling
- **Service Provider**: Updated to register all new services and facades

#### Code Quality
- **Interface Compliance**: All services implement proper interfaces
- **Type Safety**: Complete type hints and return type declarations
- **PSR Standards**: Full PSR-4 autoloading compliance
- **Laravel Integration**: Native Laravel service provider and facade integration

### 🐛 Fixed

- **Method Signatures**: Fixed interface compatibility issues
- **Import Statements**: Added missing Log and Exception imports
- **Service Registration**: Proper singleton registration for all services
- **Configuration**: Complete configuration file with all platform settings

### 📦 Package Structure

```
src/
├── config/autopost.php              # Complete configuration
├── Contracts/                       # Interface definitions
├── Enms/FacebookMetrics.php         # Facebook analytics enums
├── Exceptions/SocialMediaException.php # Custom exception
├── Facades/                         # All platform facades
│   ├── FaceBook.php
│   ├── Telegram.php
│   ├── Twitter.php
│   ├── LinkedIn.php
│   ├── Instagram.php
│   ├── TikTok.php
│   ├── YouTube.php
│   ├── Pinterest.php
│   └── SocialMedia.php             # Unified facade
├── Services/                        # All platform services
│   ├── SocialMediaService.php      # Base service
│   ├── SocialMediaManager.php      # Multi-platform manager
│   ├── FacebookService.php
│   ├── TelegramService.php
│   ├── TwitterService.php
│   ├── LinkedInService.php
│   ├── InstagramService.php
│   ├── TikTokService.php
│   ├── YouTubeService.php
│   └── PinterestService.php
└── SocialShareServiceProvider.php   # Laravel service provider
```

### 🧪 Testing

- **33 Tests**: Complete test coverage
- **101 Assertions**: Comprehensive validation
- **15 Test Files**: Unit and feature tests
- **Docker Support**: Containerized testing environment

### 📚 Documentation

- **README.md**: 605 lines of comprehensive documentation
- **README_AR.md**: 604 lines of Arabic documentation
- **Examples**: 5 example files with usage demonstrations
- **API Reference**: Complete method documentation

### 🔄 Migration from v1.x

This is a **breaking change** release. Users upgrading from v1.x will need to:

1. Update their configuration to include new platform credentials
2. Update method calls to use new unified API (optional)
3. Review new error handling patterns

### 🎯 What's New for Users

#### Before (v1.x)
```php
// Limited to Facebook and Telegram
FaceBook::share('Hello', 'https://example.com');
Telegram::share('Hello', 'https://example.com');
```

#### After (v2.0.0)
```php
// Unified API - Post to all platforms
SocialMedia::shareToAll('Hello World!', 'https://example.com');

// Individual platform access
SocialMedia::facebook()->share('Hello', 'https://example.com');
SocialMedia::twitter()->share('Hello', 'https://example.com');
SocialMedia::linkedin()->shareToCompanyPage('Update', 'https://example.com');
SocialMedia::instagram()->shareCarousel('Check this out!', ['img1.jpg', 'img2.jpg']);

// Platform-specific features
$insights = SocialMedia::facebook()->getPageInsights(['page_impressions']);
$timeline = SocialMedia::twitter()->getTimeline(10);
$analytics = SocialMedia::youtube()->getVideoAnalytics('video_id');
```

## [1.0.2] - Previous Release

### Fixed
- Facebook video uploader improvements
- Arabic readme file updates
- General improvements and documentation

## [1.0.1] - Previous Release

### Fixed
- Minor bug fixes and improvements

## [1.0.0] - Initial Release

### Added
- Basic Facebook integration
- Basic Telegram integration
- Initial package structure
