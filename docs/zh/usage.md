# 基本使用

该包通过 `SocialMedia` facade 提供统一的 API，以同时或单独在所有支持的平台上发布内容。

## 发布到多个平台

您可以在一系列平台上分享文本和链接：

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;

// 发布到特定平台
$result = SocialMedia::share(
    ['facebook', 'twitter', 'linkedin'], 
    '你好世界！', 
    'https://example.com'
);
```

## 发布到所有平台

如果您想将消息广播到应用程序中配置的每个平台：

```php
// 发布到所有默认平台
$result = SocialMedia::shareToAll('你好世界！', 'https://example.com');
```

## 共享媒体（图片和视频）

该包无缝处理将媒体上传到支持它们的平台：

### 图片

```php
$result = SocialMedia::shareImage(
    ['instagram', 'pinterest', 'twitter'], 
    '看看我们的新产品！', 
    'https://example.com/product.jpg' // 本地路径或 URL，具体取决于平台支持
);
```

### 视频

```php
$result = SocialMedia::shareVideo(
    ['youtube', 'tiktok', 'facebook'], 
    '观看我们的新教程！', 
    'https://example.com/tutorial.mp4'
);
```

## 个人平台访问

如果您更喜欢直接与特定平台进行交互，可以通过其 facade 或主管理器访问其专用方法：

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\FaceBook;
use HamzaHassanM\LaravelSocialAutoPost\Facades\Twitter;

// 直接访问 facade
FaceBook::share('你好 Facebook！', 'https://example.com');
Twitter::share('你好 Twitter！', 'https://example.com');

// 或通过 SocialMedia 管理器
SocialMedia::facebook()->share('你好', 'https://example.com');
SocialMedia::twitter()->share('你好', 'https://example.com');
SocialMedia::linkedin()->shareToCompanyPage('更新', 'https://example.com');
```
