<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Tests\Feature;

use HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostFailed;
use HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostPublished;
use HamzaHassanM\LaravelSocialAutoPost\Events\SocialPostPublishing;
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;
use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;
use HamzaHassanM\LaravelSocialAutoPost\Tests\Feature\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;

class SocialMediaManagerCharacterizationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock credentials for all platforms so validation doesn't fail
        $platforms = ['facebook', 'twitter', 'linkedin', 'instagram', 'tiktok', 'youtube', 'pinterest', 'telegram'];
        foreach ($platforms as $platform) {
            config(["autopost.{$platform}_access_token" => 'test_token']);
            // Add specific required configs
            if ($platform === 'facebook') {
                config(['autopost.facebook_page_id' => 'test_page']);
            }
            if ($platform === 'twitter') {
                config(['autopost.twitter_bearer_token' => 'test_bearer']);
                config(['autopost.twitter_api_key' => 'test_key']);
                config(['autopost.twitter_api_secret' => 'test_secret']);
                config(['autopost.twitter_access_token_secret' => 'test_secret']);
            }
            if ($platform === 'linkedin') {
                config(['autopost.linkedin_person_urn' => 'test_urn']);
            }
            if ($platform === 'instagram') {
                config(['autopost.instagram_account_id' => 'test_account']);
            }
            if ($platform === 'tiktok') {
                config(['autopost.tiktok_client_key' => 'test_key']);
                config(['autopost.tiktok_client_secret' => 'test_secret']);
            }
            if ($platform === 'youtube') {
                config(['autopost.youtube_api_key' => 'test_key']);
                config(['autopost.youtube_channel_id' => 'test_channel']);
            }
            if ($platform === 'pinterest') {
                config(['autopost.pinterest_board_id' => 'test_board']);
            }
            if ($platform === 'telegram') {
                config(['autopost.telegram_bot_token' => 'test_bot']);
                config(['autopost.telegram_chat_id' => 'test_chat']);
            }
        }
    }

    public function test_it_returns_expected_array_structure_and_fires_synchronous_events_on_success()
    {
        Event::fake();
        
        Http::fake([
            'graph.facebook.com/*' => Http::response(['id' => '12345_67890'], 200),
        ]);

        $result = SocialMedia::share(['facebook'], 'Test caption', 'https://example.com');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('results', $result);
        $this->assertArrayHasKey('errors', $result);
        $this->assertArrayHasKey('success_count', $result);
        $this->assertArrayHasKey('error_count', $result);
        $this->assertArrayHasKey('total_platforms', $result);
        
        $this->assertTrue($result['results']['facebook']['success']);
        $this->assertEquals('12345_67890', $result['results']['facebook']['data']['id']);
        $this->assertEquals(1, $result['success_count']);
        
        Event::assertDispatched(SocialPostPublishing::class, function ($event) {
            return $event->platform === 'facebook';
        });
        Event::assertDispatched(SocialPostPublished::class, function ($event) use ($result) {
            return $event->platform === 'facebook' && $event->result === $result['results']['facebook']['data'];
        });
        Event::assertNotDispatched(SocialPostFailed::class);
    }

    public function test_it_catches_exceptions_and_returns_them_in_aggregated_array_on_failure()
    {
        Event::fake();
        
        Http::fake([
            'graph.facebook.com/*' => Http::response(['error' => ['message' => 'Invalid token']], 401),
        ]);
        
        config(['autopost.retry_attempts' => 1]);

        $result = SocialMedia::share(['facebook'], 'Test caption', 'https://example.com');
        
        $this->assertIsArray($result);
        $this->assertFalse($result['results']['facebook']['success']);
        $this->assertStringContainsString('Failed to share', $result['results']['facebook']['error']);
        $this->assertStringContainsString('Failed to share', $result['errors']['facebook']);
        $this->assertEquals(0, $result['success_count']);
        $this->assertEquals(1, $result['error_count']);

        Event::assertDispatched(SocialPostPublishing::class);
        Event::assertDispatched(SocialPostFailed::class, function ($event) {
            return $event->platform === 'facebook' && $event->exception instanceof \Exception;
        });
        Event::assertNotDispatched(SocialPostPublished::class);
    }
    
    public function test_share_to_all_partial_success_behavior()
    {
        Event::fake();
        
        Http::fake([
            'graph.facebook.com/*' => Http::response(['id' => 'fb_id'], 200),
            'api.twitter.com/*' => Http::response(['detail' => 'Rate limited'], 429),
            '*' => Http::response(['id' => 'success_id'], 200),
        ]);
        
        config(['autopost.retry_attempts' => 1]);

        $result = SocialMedia::shareToAll('Test caption', 'https://example.com');
        
        $this->assertEquals(8, $result['total_platforms']);
        $this->assertEquals(8, $result['success_count'] + $result['error_count']);
        
        $this->assertTrue($result['results']['facebook']['success']);
        $this->assertFalse($result['results']['twitter']['success']);
        $this->assertStringContainsString('Rate limited', $result['errors']['twitter']);

        Event::assertDispatched(SocialPostPublished::class);
        Event::assertDispatched(SocialPostFailed::class);
    }
}
