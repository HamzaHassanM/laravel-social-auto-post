# 高级用法

## Laravel 事件 (Events)

该包在发布生命周期内触发原生 Laravel 事件。这对于 SaaS 应用程序更新数据库中的帖子状态而无需手动检查响应数组非常有用。

### 可用事件

1. `HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostPublishing`
   - 在发送 API 请求 *之前* 触发。
   - 属性：`$platform`, `$method`, `$parameters`

2. `HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostPublished`
   - 在成功的 API 请求 *之后* 触发。
   - 属性：`$platform`, `$method`, `$parameters`, `$result`

3. `HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostFailed`
   - 如果 API 请求引发异常则触发。
   - 属性：`$platform`, `$method`, `$parameters`, `$exception`

### 使用示例

在您的 `EventServiceProvider` 中注册 Listeners：

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

## 错误处理 (Error Handling)

该包提供全面的错误处理：

```php
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;

try {
    $result = SocialMedia::share(['facebook', 'twitter'], '内容', 'https://example.com');
    
    // 检查结果
    if ($result['error_count'] > 0) {
        foreach ($result['errors'] as $platform => $error) {
            echo "在 {$platform} 上的错误: {$error}\n";
        }
    }
    
} catch (SocialMediaException $e) {
    echo "社交媒体错误: " . $e->getMessage();
}
```

## 重试逻辑 (Retry Logic)

该包通过指数退避自动重试失败的请求。在 `config/autopost.php` 中或动态地配置它：

```php
// 配置重试次数
config(['autopost.retry_attempts' => 5]);

// 配置超时
config(['autopost.timeout' => 60]);
```
