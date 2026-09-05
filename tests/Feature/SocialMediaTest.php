<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Tests\Feature;

use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;
use HamzaHassanM\LaravelSocialAutoPost\Facades\FaceBook;
use HamzaHassanM\LaravelSocialAutoPost\Facades\Twitter;
use HamzaHassanM\LaravelSocialAutoPost\Facades\LinkedIn;
use HamzaHassanM\LaravelSocialAutoPost\Facades\Instagram;
use HamzaHassanM\LaravelSocialAutoPost\Facades\TikTok;
use HamzaHassanM\LaravelSocialAutoPost\Facades\YouTube;
use HamzaHassanM\LaravelSocialAutoPost\Facades\Pinterest;
use HamzaHassanM\LaravelSocialAutoPost\Facades\Telegram;
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class SocialMediaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up test configuration
        config([
            'autopost.facebook_access_token' => 'test_facebook_token',
            'autopost.facebook_page_id' => 'test_page_id',
            'autopost.twitter_bearer_token' => 'test_twitter_token',
            'autopost.twitter_api_key' => 'test_api_key',
            'autopost.twitter_api_secret' => 'test_api_secret',
            'autopost.twitter_access_token' => 'test_access_token',
            'autopost.twitter_access_token_secret' => 'test_access_token_secret',
            'autopost.linkedin_access_token' => 'test_linkedin_token',
            'autopost.linkedin_person_urn' => 'test_person_urn',
            'autopost.instagram_access_token' => 'test_instagram_token',
            'autopost.instagram_account_id' => 'test_account_id',
            'autopost.tiktok_access_token' => 'test_tiktok_token',
            'autopost.tiktok_client_key' => 'test_client_key',
            'autopost.tiktok_client_secret' => 'test_client_secret',
            'autopost.youtube_api_key' => 'test_youtube_key',
            'autopost.youtube_access_token' => 'test_youtube_token',
            'autopost.youtube_channel_id' => 'test_channel_id',
            'autopost.pinterest_access_token' => 'test_pinterest_token',
            'autopost.pinterest_board_id' => 'test_board_id',
            'autopost.telegram_bot_token' => 'test_telegram_token',
            'autopost.telegram_chat_id' => 'test_chat_id',
            'autopost.linkedin_organization_urn' => 'test_org_urn',
        ]);
    }

    protected function tearDown(): void
    {
        // LinkedInService uses a Singleton. Reset it between tests so each test
        // reads a fresh config (including linkedin_organization_urn).
        $reflection = new \ReflectionProperty(\HamzaHassanM\LaravelSocialAutoPost\Services\LinkedInService::class, 'instance');
        $reflection->setAccessible(true);
        $reflection->setValue(null, null);

        parent::tearDown();
    }

    public function testUnifiedSocialMediaSharing()
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/*' => Http::response(['id' => '123'], 200),
            'https://api.twitter.com/2/*' => Http::response(['data' => ['id' => '456']], 200),
            'https://api.linkedin.com/v2/*' => Http::response(['id' => '789'], 200),
        ]);

        $platforms = ['facebook', 'twitter', 'linkedin'];
        $result = SocialMedia::share($platforms, 'Test post', 'https://example.com');

        $this->assertArrayHasKey('results', $result);
        $this->assertArrayHasKey('success_count', $result);
        $this->assertArrayHasKey('error_count', $result);
        $this->assertArrayHasKey('total_platforms', $result);
        $this->assertEquals(3, $result['success_count']);
        $this->assertEquals(0, $result['error_count']);
        $this->assertEquals(3, $result['total_platforms']);
    }

    public function testShareToAllPlatforms()
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/*' => Http::response(['id' => '123'], 200),
            'https://api.telegram.org/bot*' => Http::response(['ok' => true], 200),
        ]);

        $result = SocialMedia::shareToAll('Test post to all platforms', 'https://example.com');

        $this->assertArrayHasKey('results', $result);
        $this->assertArrayHasKey('success_count', $result);
        $this->assertArrayHasKey('total_platforms', $result);
        $this->assertEquals(8, $result['total_platforms']); // All 8 platforms
        $this->assertGreaterThan(0, $result['success_count']);
    }

    public function testIndividualPlatformAccess()
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/*' => Http::response(['id' => '123'], 200),
        ]);

        $result = SocialMedia::facebook()->share('Test Facebook post', 'https://example.com');

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('123', $result['id']);
    }

    public function testFacebookSharing()
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/*' => Http::response(['id' => '123'], 200),
        ]);

        $result = FaceBook::share('Test Facebook post', 'https://example.com');

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('123', $result['id']);
    }

    public function testFacebookImageSharing()
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/*' => Http::response(['id' => '123'], 200),
        ]);

        $result = FaceBook::shareImage('Test Facebook image', 'https://example.com/image.jpg');

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('123', $result['id']);
    }

    public function testFacebookPageInsights()
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/*' => Http::response([
                'data' => [
                    [
                        'name' => 'page_impressions',
                        'values' => [['value' => 1000]]
                    ]
                ]
            ], 200),
        ]);

        $result = FaceBook::getPageInsights(['page_impressions']);

        $this->assertArrayHasKey('data', $result);
        $this->assertCount(1, $result['data']);
        $this->assertEquals('page_impressions', $result['data'][0]['name']);
    }

    public function testTwitterSharing()
    {
        Http::fake([
            'https://api.twitter.com/2/*' => Http::response(['data' => ['id' => '456']], 200),
        ]);

        $result = Twitter::share('Test tweet', 'https://example.com');

        $this->assertArrayHasKey('data', $result);
        $this->assertEquals('456', $result['data']['id']);
    }

    public function testTwitterImageSharing()
    {
        // Pass a local fixture file path instead of a URL so Http::fake() remains
        // in full control. SafeMediaFetcher is bypassed for local paths.
        Http::fake([
            'https://api.twitter.com/2/*'         => Http::response(['data' => ['id' => '456']], 200),
            'https://upload.twitter.com/1.1/*'    => Http::response(['media_id_string' => 'media123'], 200),
        ]);

        $fixturePath = realpath(__DIR__ . '/../../tests/Fixtures/test_image.jpg');
        $result = Twitter::shareImage('Test tweet with image', $fixturePath);

        $this->assertArrayHasKey('data', $result);
        $this->assertEquals('456', $result['data']['id']);
    }

    public function testTwitterImageSharingRemoteUrl()
    {
        // This tests that SafeMediaFetcher correctly downloads a remote file 
        // before passing it to the service's upload mechanism (which is mocked).
        Http::fake([
            'https://api.twitter.com/2/*'         => Http::response(['data' => ['id' => '456_remote']], 200),
            'https://upload.twitter.com/1.1/*'    => Http::response(['media_id_string' => 'media123_remote'], 200),
        ]);

        // A public URL that returns a small valid JPEG
        $url = 'https://httpbin.org/image/jpeg';
        
        $fixturePath = realpath(__DIR__ . '/../../tests/Fixtures/test_image.jpg');
        \HamzaHassanM\LaravelSocialAutoPost\Utils\SafeMediaFetcher::$fetchHandler = function($fetchUrl) use ($url, $fixturePath) {
            if ($fetchUrl === $url) {
                $tempPath = sys_get_temp_dir() . '/' . uniqid('mock_') . '.jpg';
                copy($fixturePath, $tempPath);
                return $tempPath;
            }
            throw new \Exception("Unexpected URL fetched: $fetchUrl");
        };
        
        $result = Twitter::shareImage('Test tweet with remote image', $url);

        $this->assertArrayHasKey('data', $result);
        $this->assertEquals('456_remote', $result['data']['id']);
        
        \HamzaHassanM\LaravelSocialAutoPost\Utils\SafeMediaFetcher::$fetchHandler = null;
    }

    public function testTwitterTimeline()
    {
        Http::fake([
            'https://api.twitter.com/2/*' => Http::response([
                'data' => [
                    ['id' => 'tweet1', 'text' => 'Tweet 1'],
                    ['id' => 'tweet2', 'text' => 'Tweet 2']
                ]
            ], 200),
        ]);

        $result = Twitter::getTimeline(5);

        $this->assertArrayHasKey('data', $result);
        $this->assertCount(2, $result['data']);
    }

    public function testLinkedInSharing()
    {
        Http::fake([
            'https://api.linkedin.com/v2/*' => Http::response(['id' => '789'], 200),
        ]);

        $result = LinkedIn::share('Test LinkedIn post', 'https://example.com');

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('789', $result['id']);
    }

    public function testLinkedInCompanyPageSharing()
    {
        Http::fake([
            'https://api.linkedin.com/v2/*' => Http::response(['id' => '789'], 200),
        ]);

        $result = LinkedIn::shareToCompanyPage('Test company post', 'https://example.com');

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('789', $result['id']);
    }

    public function testInstagramImageSharing()
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/*' => Http::response(['id' => '123'], 200),
        ]);

        $result = Instagram::shareImage('Test Instagram image', 'https://example.com/image.jpg');

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('123', $result['id']);
    }

    public function testInstagramCarouselSharing()
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/*' => Http::response(['id' => '123'], 200),
        ]);

        $images = [
            'https://example.com/image1.jpg',
            'https://example.com/image2.jpg'
        ];
        $result = Instagram::shareCarousel('Test carousel', $images);

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('123', $result['id']);
    }

    public function testTikTokVideoSharing()
    {
        Http::fake([
            'https://open.tiktokapis.com/v2/post/publish/video/init/' => Http::response([
                'data' => [
                    'publish_id' => 'tiktok123',
                    'upload_url' => 'https://upload.tiktok.com/upload'
                ],
                'error' => ['code' => 'ok']
            ], 200),
            'https://upload.tiktok.com/upload' => Http::response([], 200)
        ]);

        // TikTok accepts a local file path — bypasses SafeMediaFetcher entirely
        $fixturePath = realpath(__DIR__ . '/../../tests/Fixtures/test_video.mp4');
        $result = TikTok::shareVideo('Test TikTok video', $fixturePath);

        $this->assertArrayHasKey('data', $result);
        $this->assertEquals('tiktok123', $result['data']['publish_id']);
    }

    public function testTikTokVideoSharingRemoteUrl()
    {
        Http::fake([
            'https://open.tiktokapis.com/v2/post/publish/video/init/' => Http::response([
                'data' => [
                    'publish_id' => 'tiktok123_remote',
                    'upload_url' => 'https://upload.tiktok.com/upload'
                ],
                'error' => ['code' => 'ok']
            ], 200),
            'https://upload.tiktok.com/upload' => Http::response([], 200)
        ]);

        // Use our own raw GitHub fixture as a reliable remote video URL.
        // It triggers SafeMediaFetcher, downloads the video, validates it,
        // and then passes it to the mocked upload flow.
        $url = 'https://raw.githubusercontent.com/HamzaHassanM/laravel-social-auto-post/fix-ssrf-phase-2/tests/Fixtures/test_video.mp4';
        
        $result = TikTok::shareVideo('Test TikTok remote video', $url);

        $this->assertArrayHasKey('data', $result);
        $this->assertEquals('tiktok123_remote', $result['data']['publish_id']);
    }

    public function testYouTubeVideoSharing()
    {
        Http::fake([
            'https://www.googleapis.com/youtube/v3/*' => Http::response(['id' => 'youtube123'], 200),
        ]);

        // YouTube now accepts a local file path — bypasses SafeMediaFetcher entirely
        $fixturePath = realpath(__DIR__ . '/../../tests/Fixtures/test_video.mp4');
        $result = YouTube::shareVideo('Test YouTube video', $fixturePath);

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('youtube123', $result['id']);
    }

    public function testYouTubeVideoSharingRemoteUrl()
    {
        Http::fake([
            'https://www.googleapis.com/youtube/v3/*' => Http::response(['id' => 'youtube123_remote'], 200),
        ]);

        $url = 'https://raw.githubusercontent.com/HamzaHassanM/laravel-social-auto-post/fix-ssrf-phase-2/tests/Fixtures/test_video.mp4';
        
        $fixturePath = realpath(__DIR__ . '/../../tests/Fixtures/test_video.mp4');
        \HamzaHassanM\LaravelSocialAutoPost\Utils\SafeMediaFetcher::$fetchHandler = function($fetchUrl) use ($url, $fixturePath) {
            if ($fetchUrl === $url) {
                $tempPath = sys_get_temp_dir() . '/' . uniqid('mock_') . '.mp4';
                copy($fixturePath, $tempPath);
                return $tempPath;
            }
            throw new \Exception("Unexpected URL fetched: $fetchUrl");
        };
        
        $result = YouTube::shareVideo('Test YouTube remote video', $url);

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('youtube123_remote', $result['id']);
        
        \HamzaHassanM\LaravelSocialAutoPost\Utils\SafeMediaFetcher::$fetchHandler = null;
    }

    public function testYouTubeCommunityPost()
    {
        Http::fake([
            'https://www.googleapis.com/youtube/v3/*' => Http::response(['id' => 'community123'], 200),
        ]);

        $result = YouTube::createCommunityPost('Test community post', 'https://example.com');

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('community123', $result['id']);
    }

    public function testPinterestImageSharing()
    {
        Http::fake([
            'https://api.pinterest.com/v5/*' => Http::response(['id' => 'pinterest123'], 200),
        ]);

        $result = Pinterest::shareImage('Test Pinterest pin', 'https://example.com/');

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('pinterest123', $result['id']);
    }

    public function testPinterestBoardCreation()
    {
        Http::fake([
            'https://api.pinterest.com/v5/*' => Http::response(['id' => 'board123'], 200),
        ]);

        $result = Pinterest::createBoard('Test Board', 'Test board description');

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('board123', $result['id']);
    }

    public function testTelegramMessageSharing()
    {
        Http::fake([
            'https://api.telegram.org/bot*' => Http::response(['ok' => true, 'result' => ['message_id' => 123]], 200),
        ]);

        $result = Telegram::share('Test Telegram message', 'https://example.com');

        $this->assertArrayHasKey('ok', $result);
        $this->assertTrue($result['ok']);
        $this->assertEquals(123, $result['result']['message_id']);
    }

    public function testTelegramImageSharing()
    {
        Http::fake([
            'https://api.telegram.org/bot*' => Http::response(['ok' => true, 'result' => ['message_id' => 123]], 200),
        ]);

        $result = Telegram::shareImage('Test Telegram image', 'https://example.com/image.jpg');

        $this->assertArrayHasKey('ok', $result);
        $this->assertTrue($result['ok']);
    }

    public function testTelegramDocumentSharing()
    {
        Http::fake([
            'https://api.telegram.org/bot*' => Http::response(['ok' => true, 'result' => ['message_id' => 123]], 200),
        ]);

        $result = Telegram::shareDocument('Test Telegram document', 'https://example.com/document.pdf');

        $this->assertArrayHasKey('ok', $result);
        $this->assertTrue($result['ok']);
    }

    public function testErrorHandling()
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/*' => Http::response(['error' => ['message' => 'Invalid token']], 400),
        ]);

        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('Failed to share to Facebook');

        FaceBook::share('Test post', 'https://example.com');
    }

    public function testInputValidation()
    {
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('Text content cannot be empty.');

        FaceBook::share('', 'https://example.com');
    }

    public function testUrlValidation()
    {
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('Invalid URL provided');

        FaceBook::share('Test post', 'invalid-url');
    }

    public function testMultiPlatformErrorHandling()
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/*' => Http::response(['id' => '123'], 200),
            'https://api.twitter.com/2/*' => Http::response(['error' => ['message' => 'Invalid token']], 401),
            'https://api.linkedin.com/v2/*' => Http::response(['id' => '789'], 200),
        ]);

        $platforms = ['facebook', 'twitter', 'linkedin'];
        $result = SocialMedia::share($platforms, 'Test post', 'https://example.com');

        $this->assertEquals(3, $result['total_platforms']);
        $this->assertEquals(2, $result['success_count']);
        $this->assertEquals(1, $result['error_count']);

        $this->assertTrue($result['results']['facebook']['success']);
        $this->assertFalse($result['results']['twitter']['success']);
        $this->assertTrue($result['results']['linkedin']['success']);

        $this->assertArrayHasKey('twitter', $result['errors']);
    }

    public function testPlatformAvailability()
    {
        $manager = app(\HamzaHassanM\LaravelSocialAutoPost\Services\SocialMediaManager::class);
        
        $this->assertTrue($manager->isPlatformAvailable('facebook'));
        $this->assertTrue($manager->isPlatformAvailable('twitter'));
        $this->assertTrue($manager->isPlatformAvailable('linkedin'));
        $this->assertTrue($manager->isPlatformAvailable('instagram'));
        $this->assertTrue($manager->isPlatformAvailable('tiktok'));
        $this->assertTrue($manager->isPlatformAvailable('youtube'));
        $this->assertTrue($manager->isPlatformAvailable('pinterest'));
        $this->assertTrue($manager->isPlatformAvailable('telegram'));
        $this->assertFalse($manager->isPlatformAvailable('nonexistent'));
    }

    public function testGetAvailablePlatforms()
    {
        $manager = app(\HamzaHassanM\LaravelSocialAutoPost\Services\SocialMediaManager::class);
        $platforms = $manager->getAvailablePlatforms();

        $this->assertIsArray($platforms);
        $this->assertCount(8, $platforms);
        $this->assertContains('facebook', $platforms);
        $this->assertContains('twitter', $platforms);
        $this->assertContains('linkedin', $platforms);
        $this->assertContains('instagram', $platforms);
        $this->assertContains('tiktok', $platforms);
        $this->assertContains('youtube', $platforms);
        $this->assertContains('pinterest', $platforms);
        $this->assertContains('telegram', $platforms);
    }

    public function testLoggingEnabled()
    {
        Log::shouldReceive('info')->with('Social media API request successful', \Mockery::any())->zeroOrMoreTimes();
        Log::shouldReceive('info')->with('Facebook post shared successfully', \Mockery::type('array'))->once();
        Log::shouldReceive('error')->zeroOrMoreTimes();
        Log::shouldReceive('warning')->zeroOrMoreTimes();

        Http::fake([
            'https://graph.facebook.com/v20.0/*' => Http::response(['id' => '123'], 200),
        ]);

        FaceBook::share('Test post', 'https://example.com');
    }

    public function testServiceProviderRegistration()
    {
        $this->assertTrue(app()->bound('HamzaHassanM\LaravelSocialAutoPost\Services\FacebookService'));
        $this->assertTrue(app()->bound('HamzaHassanM\LaravelSocialAutoPost\Services\TwitterService'));
        $this->assertTrue(app()->bound('HamzaHassanM\LaravelSocialAutoPost\Services\LinkedInService'));
        $this->assertTrue(app()->bound('HamzaHassanM\LaravelSocialAutoPost\Services\InstagramService'));
        $this->assertTrue(app()->bound('HamzaHassanM\LaravelSocialAutoPost\Services\TikTokService'));
        $this->assertTrue(app()->bound('HamzaHassanM\LaravelSocialAutoPost\Services\YouTubeService'));
        $this->assertTrue(app()->bound('HamzaHassanM\LaravelSocialAutoPost\Services\PinterestService'));
        $this->assertTrue(app()->bound('HamzaHassanM\LaravelSocialAutoPost\Services\TelegramService'));
        $this->assertTrue(app()->bound('HamzaHassanM\LaravelSocialAutoPost\Services\SocialMediaManager'));
    }

    public function testFacadeAliases()
    {
        $this->assertTrue(app()->bound('facebook'));
        $this->assertTrue(app()->bound('twitter'));
        $this->assertTrue(app()->bound('linkedin'));
        $this->assertTrue(app()->bound('instagram'));
        $this->assertTrue(app()->bound('tiktok'));
        $this->assertTrue(app()->bound('youtube'));
        $this->assertTrue(app()->bound('pinterest'));
        $this->assertTrue(app()->bound('telegram'));
        $this->assertTrue(app()->bound('socialmedia'));
    }

    public function testSocialMediaEventsDispatched()
    {
        Event::fake();

        Http::fake([
            'https://graph.facebook.com/v20.0/*' => Http::response(['id' => '123'], 200),
        ]);

        SocialMedia::share(['facebook'], 'Test post', 'https://example.com');

        Event::assertDispatched(\HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostPublishing::class, function ($event) {
            return $event->platform === 'facebook' 
                && $event->method === 'share' 
                && $event->parameters === ['Test post', 'https://example.com'];
        });

        Event::assertDispatched(\HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostPublished::class, function ($event) {
            return $event->platform === 'facebook' 
                && $event->method === 'share' 
                && $event->parameters === ['Test post', 'https://example.com']
                && $event->result === ['id' => '123'];
        });
    }

    public function testSocialMediaEventFailedDispatched()
    {
        Event::fake();

        Http::fake([
            'https://graph.facebook.com/v20.0/*' => Http::response(['error' => ['message' => 'Invalid token']], 400),
        ]);

        SocialMedia::share(['facebook'], 'Test post', 'https://example.com');

        Event::assertDispatched(\HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostPublishing::class);
        Event::assertDispatched(\HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostFailed::class, function ($event) {
            return $event->platform === 'facebook' 
                && $event->method === 'share' 
                && $event->parameters === ['Test post', 'https://example.com']
                && $event->exception instanceof \Exception;
        });
    }
}
