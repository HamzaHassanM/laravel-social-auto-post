<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Tests\Unit;

use HamzaHassanM\LaravelSocialAutoPost\Tests\Unit\TestCase;
use HamzaHassanM\LaravelSocialAutoPost\Services\SocialMediaManager;
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;
use Illuminate\Support\Facades\Http;

class SocialMediaManagerTest extends TestCase
{
    protected SocialMediaManager $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new SocialMediaManager();
    }

    public function testGetAvailablePlatforms()
    {
        $platforms = $this->manager->getAvailablePlatforms();

        $this->assertIsArray($platforms);
        $this->assertContains('facebook', $platforms);
        $this->assertContains('twitter', $platforms);
        $this->assertContains('linkedin', $platforms);
        $this->assertContains('instagram', $platforms);
        $this->assertContains('tiktok', $platforms);
        $this->assertContains('youtube', $platforms);
        $this->assertContains('pinterest', $platforms);
        $this->assertContains('telegram', $platforms);
    }

    public function testIsPlatformAvailable()
    {
        $this->assertTrue($this->manager->isPlatformAvailable('facebook'));
        $this->assertTrue($this->manager->isPlatformAvailable('twitter'));
        $this->assertTrue($this->manager->isPlatformAvailable('linkedin'));
        $this->assertFalse($this->manager->isPlatformAvailable('nonexistent'));
        $this->assertFalse($this->manager->isPlatformAvailable(''));
    }

    public function testGetPlatformService()
    {
        $facebookService = $this->manager->getPlatformService('facebook');
        $this->assertEquals('HamzaHassanM\LaravelSocialAutoPost\Services\FacebookService', $facebookService);

        $twitterService = $this->manager->getPlatformService('twitter');
        $this->assertEquals('HamzaHassanM\LaravelSocialAutoPost\Services\TwitterService', $twitterService);

        $this->assertNull($this->manager->getPlatformService('nonexistent'));
    }

    public function testShareToMultiplePlatforms()
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/*' => Http::response(['id' => '123'], 200),
            'https://api.twitter.com/2/*' => Http::response(['data' => ['id' => '456']], 200),
            'https://api.linkedin.com/v2/*' => Http::response(['id' => '789'], 200),
        ]);

        $platforms = ['facebook', 'twitter', 'linkedin'];
        $result = $this->manager->share($platforms, 'Test post', 'https://example.com');

        $this->assertArrayHasKey('results', $result);
        $this->assertArrayHasKey('success_count', $result);
        $this->assertArrayHasKey('error_count', $result);
        $this->assertArrayHasKey('total_platforms', $result);

        $this->assertEquals(3, $result['total_platforms']);
        $this->assertEquals(3, $result['success_count']);
        $this->assertEquals(0, $result['error_count']);

        $this->assertArrayHasKey('facebook', $result['results']);
        $this->assertArrayHasKey('twitter', $result['results']);
        $this->assertArrayHasKey('linkedin', $result['results']);

        $this->assertTrue($result['results']['facebook']['success']);
        $this->assertTrue($result['results']['twitter']['success']);
        $this->assertTrue($result['results']['linkedin']['success']);
    }

    public function testShareToAllPlatforms()
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/*' => Http::response(['id' => '123'], 200),
            'https://api.telegram.org/bot*' => Http::response(['ok' => true], 200),
        ]);

        $result = $this->manager->shareToAll('Test post to all', 'https://example.com');

        $this->assertArrayHasKey('results', $result);
        $this->assertArrayHasKey('success_count', $result);
        $this->assertArrayHasKey('total_platforms', $result);

        $this->assertEquals(8, $result['total_platforms']); // All 8 platforms
        $this->assertGreaterThan(0, $result['success_count']);
    }

    public function testShareImageToMultiplePlatforms()
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/*' => Http::response(['id' => '123'], 200),
            'https://api.pinterest.com/v5/*' => Http::response(['id' => '456'], 200),
        ]);

        $platforms = ['facebook', 'pinterest'];
        $result = $this->manager->shareImage($platforms, 'Test image', 'https://example.com/image.jpg');

        $this->assertArrayHasKey('results', $result);
        $this->assertArrayHasKey('success_count', $result);
        $this->assertEquals(2, $result['total_platforms']);
    }

    public function testShareVideoToMultiplePlatforms()
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/*' => Http::response(['id' => '123'], 200),
            'https://open.tiktokapis.com/v2/post/publish/inbox/video/init/' => Http::response([
                'data' => [
                    'publish_id' => '456',
                    'upload_url' => 'https://upload.tiktok.com/upload'
                ],
                'error' => ['code' => 'ok']
            ], 200),
            'https://upload.tiktok.com/upload' => Http::response([], 200),
        ]);

        $platforms = ['facebook', 'tiktok'];
        $result = $this->manager->shareVideo($platforms, 'Test video', 'https://example.com/video.mp4');

        $this->assertArrayHasKey('results', $result);
        $this->assertArrayHasKey('success_count', $result);
        $this->assertEquals(2, $result['total_platforms']);
    }

    public function testShareWithErrors()
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/*' => Http::response(['id' => '123'], 200),
            'https://api.twitter.com/2/*' => Http::response(['error' => ['message' => 'Invalid token']], 401),
            'https://api.linkedin.com/v2/*' => Http::response(['id' => '789'], 200),
        ]);

        $platforms = ['facebook', 'twitter', 'linkedin'];
        $result = $this->manager->share($platforms, 'Test post', 'https://example.com');

        $this->assertEquals(3, $result['total_platforms']);
        $this->assertEquals(2, $result['success_count']);
        $this->assertEquals(1, $result['error_count']);

        $this->assertTrue($result['results']['facebook']['success']);
        $this->assertFalse($result['results']['twitter']['success']);
        $this->assertTrue($result['results']['linkedin']['success']);

        $this->assertArrayHasKey('twitter', $result['errors']);
        $this->assertStringContains('Invalid token', $result['errors']['twitter']);
    }

    public function testPlatformMethodReturnsService()
    {
        $facebookService = $this->manager->platform('facebook');
        $this->assertInstanceOf('HamzaHassanM\LaravelSocialAutoPost\Services\FacebookService', $facebookService);

        $twitterService = $this->manager->platform('twitter');
        $this->assertInstanceOf('HamzaHassanM\LaravelSocialAutoPost\Services\TwitterService', $twitterService);
    }

    public function testPlatformMethodWithInvalidPlatform()
    {
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage("Platform 'nonexistent' is not supported");

        $this->manager->platform('nonexistent');
    }

    public function testFacebookMethod()
    {
        $facebookService = $this->manager->facebook();
        $this->assertInstanceOf('HamzaHassanM\LaravelSocialAutoPost\Services\FacebookService', $facebookService);
    }

    public function testTwitterMethod()
    {
        $twitterService = $this->manager->twitter();
        $this->assertInstanceOf('HamzaHassanM\LaravelSocialAutoPost\Services\TwitterService', $twitterService);
    }

    public function testLinkedInMethod()
    {
        $linkedinService = $this->manager->linkedin();
        $this->assertInstanceOf('HamzaHassanM\LaravelSocialAutoPost\Services\LinkedInService', $linkedinService);
    }

    public function testInstagramMethod()
    {
        $instagramService = $this->manager->instagram();
        $this->assertInstanceOf('HamzaHassanM\LaravelSocialAutoPost\Services\InstagramService', $instagramService);
    }

    public function testTikTokMethod()
    {
        $tiktokService = $this->manager->tiktok();
        $this->assertInstanceOf('HamzaHassanM\LaravelSocialAutoPost\Services\TikTokService', $tiktokService);
    }

    public function testYouTubeMethod()
    {
        $youtubeService = $this->manager->youtube();
        $this->assertInstanceOf('HamzaHassanM\LaravelSocialAutoPost\Services\YouTubeService', $youtubeService);
    }

    public function testPinterestMethod()
    {
        $pinterestService = $this->manager->pinterest();
        $this->assertInstanceOf('HamzaHassanM\LaravelSocialAutoPost\Services\PinterestService', $pinterestService);
    }

    public function testTelegramMethod()
    {
        $telegramService = $this->manager->telegram();
        $this->assertInstanceOf('HamzaHassanM\LaravelSocialAutoPost\Services\TelegramService', $telegramService);
    }

    public function testExecuteOnPlatformsWithEmptyArray()
    {
        $result = $this->manager->share([], 'Test post', 'https://example.com');

        $this->assertEquals(0, $result['total_platforms']);
        $this->assertEquals(0, $result['success_count']);
        $this->assertEquals(0, $result['error_count']);
        $this->assertEmpty($result['results']);
        $this->assertEmpty($result['errors']);
    }

    public function testExecuteOnPlatformsWithInvalidPlatform()
    {
        $platforms = ['facebook', 'nonexistent', 'twitter'];
        $result = $this->manager->share($platforms, 'Test post', 'https://example.com');

        $this->assertEquals(3, $result['total_platforms']);
        $this->assertEquals(0, $result['success_count']);
        $this->assertEquals(3, $result['error_count']);

        $this->assertArrayHasKey('nonexistent', $result['errors']);
        $this->assertStringContains('not supported', $result['errors']['nonexistent']);
    }
}
