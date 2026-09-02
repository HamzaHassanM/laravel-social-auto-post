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

该包区分持久性错误（4xx）和暂时性错误（5xx，网络超时）。带有 4xx 的失败 API 调用将快速失败（fail-fast），而 5xx 和网络错误将通过指数退避自动重试。

在 `config/autopost.php` 中或动态地配置它：

```php
// 配置重试次数
config(['autopost.retry_attempts' => 5]);

// 配置指数退避基数 (例如 2 代表 2s, 4s, 8s)
config(['autopost.retry_backoff_base' => 2]);

// 配置超时 (Timeout)
config(['autopost.timeout' => 60]);
```

## 安全性 (SSRF Protection)

从远程 URL 下载媒体时，该包使用 `SafeMediaFetcher` 来防止服务器端请求伪造 (SSRF) 和 DNS 重新绑定攻击。在获取文件之前，它会解析主机名并确保它不指向私有、保留或环回 (loopback) IP。

在 `config/autopost.php` 中配置安全限制：

```php
// 启用或禁用 SSRF 保护
config(['autopost.enforce_ssrf_protection' => true]);

// 设置允许的最大媒体大小 (以字节为单位，例如 10MB)
config(['autopost.max_media_size' => 10485760]);
```
