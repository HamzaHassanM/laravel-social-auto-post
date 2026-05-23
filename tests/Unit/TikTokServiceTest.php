<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Tests\Unit;

use HamzaHassanM\LaravelSocialAutoPost\Tests\Unit\TestCase;
use HamzaHassanM\LaravelSocialAutoPost\Services\TikTokService;
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TikTokServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Reset singleton instance to ensure clean state for tests
        $reflection = new \ReflectionClass(TikTokService::class);
        $instance = $reflection->getProperty('instance');
        $instance->setAccessible(true);
        $instance->setValue(null, null);
        
        config([
            'autopost.tiktok_access_token' => 'test_tiktok_token',
            'autopost.tiktok_client_key' => 'test_client_key',
            'autopost.tiktok_client_secret' => 'test_client_secret',
        ]);
    }

    public function testTikTokServiceSingleton()
    {
        $service1 = TikTokService::getInstance();
        $service2 = TikTokService::getInstance();
        
        $this->assertSame($service1, $service2);
    }

    public function testTikTokServiceWithMissingCredentials()
    {
        config(['autopost.tiktok_access_token' => null]);
        
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('TikTok credentials are not properly configured.');
        
        TikTokService::getInstance();
    }

    public function testShareThrowsException()
    {
        $service = TikTokService::getInstance();
        
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('TikTok does not support plain text posts.');
        
        $service->share('Test post', 'https://example.com');
    }

    public function testShareImageSuccess()
    {
        Http::fake([
            'https://open.tiktokapis.com/v2/post/publish/content/init/' => Http::response([
                'data' => ['publish_id' => 'tiktok_photo_123'],
                'error' => ['code' => 'ok']
            ], 200),
        ]);

        $service = TikTokService::getInstance();
        $result = $service->shareImage('Test image post', 'https://example.com/image.jpg');

        $this->assertArrayHasKey('data', $result);
        $this->assertEquals('tiktok_photo_123', $result['data']['publish_id']);
    }

    public function testShareVideoSuccess()
    {
        Http::fake([
            'https://open.tiktokapis.com/v2/post/publish/video/init/' => Http::response([
                'data' => [
                    'publish_id' => 'tiktok_video_123',
                    'upload_url' => 'https://upload.tiktok.com/upload'
                ],
                'error' => ['code' => 'ok']
            ], 200),
            'https://upload.tiktok.com/upload' => Http::response([], 200)
        ]);

        $service = TikTokService::getInstance();
        // Using a general domain that will return an HTML payload allowing file_get_contents to work
        $result = $service->shareVideo('Test TikTok video', 'https://example.com/');

        $this->assertArrayHasKey('data', $result);
        $this->assertEquals('tiktok_video_123', $result['data']['publish_id']);
    }

    public function testGetUserInfoSuccess()
    {
        Http::fake([
            'https://open.tiktokapis.com/v2/user/info/*' => Http::response([
                'data' => [
                    'display_name' => 'Test User',
                    'follower_count' => 1000,
                    'following_count' => 500
                ],
                'error' => ['code' => 'ok']
            ], 200),
        ]);

        $service = TikTokService::getInstance();
        $result = $service->getUserInfo();

        $this->assertArrayHasKey('data', $result);
        $this->assertEquals('Test User', $result['data']['display_name']);
    }

    public function testGetUserVideosSuccess()
    {
        Http::fake([
            'https://open.tiktokapis.com/v2/video/list/*' => Http::response([
                'data' => [
                    'videos' => [
                        ['video_id' => 'video1', 'title' => 'Video 1'],
                        ['video_id' => 'video2', 'title' => 'Video 2']
                    ]
                ],
                'error' => ['code' => 'ok']
            ], 200),
        ]);

        $service = TikTokService::getInstance();
        $result = $service->getUserVideos(20);

        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('videos', $result['data']);
        $this->assertCount(2, $result['data']['videos']);
    }
    
    public function testCheckPublishStatus()
    {
        Http::fake([
            'https://open.tiktokapis.com/v2/post/publish/status/fetch/' => Http::response([
                'data' => [
                    'status' => 'PUBLISHED'
                ],
                'error' => ['code' => 'ok']
            ], 200),
        ]);

        $service = TikTokService::getInstance();
        $result = $service->checkPublishStatus('tiktok_video_123');

        $this->assertArrayHasKey('data', $result);
        $this->assertEquals('PUBLISHED', $result['data']['status']);
    }

    public function testQueryCreatorInfo()
    {
        Http::fake([
            'https://open.tiktokapis.com/v2/post/publish/creator_info/query/' => Http::response([
                'data' => [
                    'creator_avatar_url' => 'https://example.com/avatar.jpg'
                ],
                'error' => ['code' => 'ok']
            ], 200),
        ]);

        $service = TikTokService::getInstance();
        $result = $service->queryCreatorInfo();

        $this->assertArrayHasKey('data', $result);
    }

    public function testShareVideoWithEmptyCaption()
    {
        $service = TikTokService::getInstance();
        
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('Caption cannot be empty.');
        
        $service->shareVideo('', 'https://example.com/video.mp4');
    }

    public function testShareVideoWithInvalidUrl()
    {
        $service = TikTokService::getInstance();
        
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('Invalid URL provided.');
        
        $service->shareVideo('Test video', 'invalid-url');
    }

    public function testShareVideoWithApiError()
    {
        Http::fake([
            'https://open.tiktokapis.com/v2/post/publish/video/init/' => Http::response([
                'error' => ['message' => 'Invalid access token', 'code' => 'error']
            ], 401),
        ]);

        $service = TikTokService::getInstance();
        
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('TikTok API error: Invalid access token');
        
        $service->shareVideo('Test TikTok video', 'https://example.com/');
    }

    public function testGetUserInfoWithApiError()
    {
        Http::fake([
            'https://open.tiktokapis.com/v2/user/info/*' => Http::response([
                'error' => ['message' => 'Invalid user request', 'code' => 'error']
            ], 400),
        ]);

        $service = TikTokService::getInstance();
        
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('Failed to get TikTok user info');
        
        $service->getUserInfo();
    }
}
