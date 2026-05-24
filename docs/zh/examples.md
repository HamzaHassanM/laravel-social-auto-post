# 综合示例

该包提供了几个实用的示例。以下是您可以在 Laravel 应用程序中使用的一些最常见和高级的模式。

## 1. 检查多平台结果

在多个平台上发布时，您会收到详细的结果数组。以下是如何处理它：

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;

try {
    $platforms = ['facebook', 'twitter', 'linkedin'];
    $result = SocialMedia::share($platforms, '好消息！我们刚刚推出了新功能！ 🚀', 'https://example.com/feature');
    
    echo "✅ 已成功发布到 " . $result['total_platforms'] . " 个平台中的 " . $result['success_count'] . " 个\n";
    
    foreach ($result['results'] as $platform => $platformResult) {
        if ($platformResult['success']) {
            echo "✅ {$platform}: 成功\n";
        } else {
            echo "❌ {$platform}: " . $platformResult['error'] . "\n";
        }
    }
    
} catch (SocialMediaException $e) {
    echo "❌ 多平台错误: " . $e->getMessage() . "\n";
}
```

## 2. 平台特定的分析和功能

像 Facebook 这样的某些平台允许您检索见解和页面信息。

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\FaceBook;

try {
    // 获取基本页面信息
    $pageInfo = FaceBook::getPageInfo();
    
    echo "📋 页面信息:\n";
    echo "   名称: " . ($pageInfo['name'] ?? '未知') . "\n";
    echo "   类别: " . ($pageInfo['category'] ?? '未知') . "\n";
    echo "   关注者: " . ($pageInfo['followers_count'] ?? '未知') . "\n";
    
} catch (\Exception $e) {
    echo "获取页面信息时出错: " . $e->getMessage();
}
```

## 3. 高级错误处理与重试

您可以将应用程序配置为自动重试失败的帖子，或手动捕获特定的平台错误。

```php
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;
use Illuminate\Support\Facades\Log;

// 暂时为大型视频增加超时和重试次数
config(['autopost.timeout' => 120]);
config(['autopost.retry_attempts' => 5]);

$result = SocialMedia::shareVideo(
    ['youtube', 'facebook'], 
    '关于 Laravel Auto Post 的详细 10 分钟教程', 
    storage_path('app/videos/tutorial.mp4')
);

if (!empty($result['errors'])) {
    // 在用尽所有重试次数后处理特定的失败
    Log::error('未能将视频上传到某些平台', $result['errors']);
}
```
