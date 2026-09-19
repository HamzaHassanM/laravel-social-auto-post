<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Tests\Unit;

use HamzaHassanM\LaravelSocialAutoPost\Tests\Unit\TestCase;
use HamzaHassanM\LaravelSocialAutoPost\Services\LinkedInService;
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LinkedInServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        config([
            'autopost.linkedin_access_token' => 'test_linkedin_token',
            'autopost.linkedin_person_urn' => 'test_person_urn',
            'autopost.linkedin_organization_urn' => 'test_organization_urn',
        ]);
    }

    public function testLinkedInServiceSingleton()
    {
        $service1 = LinkedInService::getInstance();
        $service2 = LinkedInService::getInstance();
        
        $this->assertSame($service1, $service2);
    }

    public function testLinkedInServiceWithMissingCredentials()
    {
        config(['autopost.linkedin_access_token' => null]);
        
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('LinkedIn API credentials are not fully configured');
        
        LinkedInService::getInstance();
    }

    public function testShareSuccess()
    {
        Http::fake([
            'https://api.linkedin.com/v2/ugcPosts' => Http::response(['id' => '123'], 200),
        ]);

        $service = LinkedInService::getInstance();
        $result = $service->share('Test post', 'https://example.com');

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('123', $result['id']);
    }

    public function testShareImageSuccess()
    {
        Http::fake([
            'https://api.linkedin.com/v2/assets' => Http::response(['value' => ['asset' => 'urn:li:digitalmediaAsset:123']], 200),
            'https://api.linkedin.com/v2/ugcPosts' => Http::response(['id' => '456'], 200),
        ]);

        $service = LinkedInService::getInstance();
        $result = $service->shareImage('Test image post', 'https://example.com/image.jpg');

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('456', $result['id']);
    }

    public function testShareVideoSuccess()
    {
        Http::fake([
            'https://api.linkedin.com/v2/assets' => Http::response(['value' => ['asset' => 'urn:li:digitalmediaAsset:123']], 200),
            'https://api.linkedin.com/v2/ugcPosts' => Http::response(['id' => '789'], 200),
        ]);

        $service = LinkedInService::getInstance();
        $result = $service->shareVideo('Test video post', 'https://example.com/video.mp4');

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('789', $result['id']);
    }

    public function testShareToCompanyPageSuccess()
    {
        Http::fake([
            'https://api.linkedin.com/v2/ugcPosts' => Http::response(['id' => 'company123'], 200),
        ]);

        $service = LinkedInService::getInstance();
        $result = $service->shareToCompanyPage('Company post', 'https://example.com');

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('company123', $result['id']);
    }

    public function testGetUserInfoSuccess()
    {
        Http::fake([
            'https://api.linkedin.com/v2/people/~' => Http::response([
                'id' => 'user123',
                'firstName' => 'Test',
                'lastName' => 'User'
            ], 200),
        ]);

        $service = LinkedInService::getInstance();
        $result = $service->getUserInfo();

        $this->assertArrayHasKey('firstName', $result);
        $this->assertEquals('Test', $result['firstName']);
    }

    public function testShareWithEmptyCaption()
    {
        $service = LinkedInService::getInstance();
        
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('Caption cannot be empty');
        
        $service->share('', 'https://example.com');
    }

    public function testShareWithCaptionTooLong()
    {
        $service = LinkedInService::getInstance();
        $longCaption = str_repeat('a', 3001); // Over 3000 character limit
        
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('Text content exceeds maximum length of 3000 characters');
        
        $service->share($longCaption, 'https://example.com');
    }

    public function testShareWithInvalidUrl()
    {
        $service = LinkedInService::getInstance();
        
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('Invalid URL provided');
        
        $service->share('Test post', 'invalid-url');
    }

    public function testShareWithApiError()
    {
        Http::fake([
            'https://api.linkedin.com/v2/ugcPosts' => Http::response([
                'message' => 'Invalid access token'
            ], 401),
        ]);

        $service = LinkedInService::getInstance();
        
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('Failed to share to LinkedIn');
        
        $service->share('Test post', 'https://example.com');
    }

    public function testShareImageWithApiError()
    {
        Http::fake([
            'https://api.linkedin.com/v2/assets' => Http::response([
                'message' => 'Invalid image URL'
            ], 400),
        ]);

        $service = LinkedInService::getInstance();
        
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('Failed to share image to LinkedIn');
        
        $service->shareImage('Test image post', 'https://example.com/image.jpg');
    }

    public function testShareVideoWithApiError()
    {
        Http::fake([
            'https://api.linkedin.com/v2/assets' => Http::response([
                'message' => 'Invalid video URL'
            ], 400),
        ]);

        $service = LinkedInService::getInstance();
        
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('Failed to share video to LinkedIn');
        
        $service->shareVideo('Test video post', 'https://example.com/video.mp4');
    }

    public function testShareToCompanyPageWithApiError()
    {
        Http::fake([
            'https://api.linkedin.com/v2/ugcPosts' => Http::response([
                'message' => 'Invalid organization URN'
            ], 400),
        ]);

        $service = LinkedInService::getInstance();
        
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('Failed to share to LinkedIn company page');
        
        $service->shareToCompanyPage('Company post', 'https://example.com');
    }

    public function testGetUserInfoWithApiError()
    {
        Http::fake([
            'https://api.linkedin.com/v2/people/~' => Http::response([
                'message' => 'Invalid user request'
            ], 400),
        ]);

        $service = LinkedInService::getInstance();
        
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('Failed to get LinkedIn user info');
        
        $service->getUserInfo();
    }

    public function testShareToCompanyPageWithoutOrganizationUrn()
    {
        config(['autopost.linkedin_organization_urn' => null]);
        
        $service = LinkedInService::getInstance();
        
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('LinkedIn organization URN is not configured');
        
        $service->shareToCompanyPage('Company post', 'https://example.com');
    }

    public function testLoggingOnSuccess()
    {
        Log::shouldReceive('info')
            ->once()
            ->with('LinkedIn post shared successfully', \Mockery::type('array'));

        Http::fake([
            'https://api.linkedin.com/v2/ugcPosts' => Http::response(['id' => '123'], 200),
        ]);

        $service = LinkedInService::getInstance();
        $service->share('Test post', 'https://example.com');
    }

    public function testLoggingOnError()
    {
        Log::shouldReceive('error')
            ->once()
            ->with('Failed to share to LinkedIn', \Mockery::type('array'));

        Http::fake([
            'https://api.linkedin.com/v2/ugcPosts' => Http::response([
                'message' => 'API Error'
            ], 400),
        ]);

        $service = LinkedInService::getInstance();
        
        $this->expectException(SocialMediaException::class);
        $service->share('Test post', 'https://example.com');
    }

    public function testRetryLogic()
    {
        Http::fake([
            'https://api.linkedin.com/v2/ugcPosts' => Http::sequence()
                ->push(['message' => 'Rate limited'], 429)
                ->push(['message' => 'Rate limited'], 429)
                ->push(['id' => '123'], 200),
        ]);

        $service = LinkedInService::getInstance();
        $result = $service->share('Test post', 'https://example.com');

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('123', $result['id']);
    }

    public function testTimeoutConfiguration()
    {
        config(['autopost.timeout' => 60]);

        Http::fake([
            'https://api.linkedin.com/v2/ugcPosts' => Http::response(['id' => '123'], 200),
        ]);

        $service = LinkedInService::getInstance();
        $service->share('Test post', 'https://example.com');

        Http::assertSent(function ($request) {
            return $request->timeout() === 60;
        });
    }

    public function testAssetUploadForImage()
    {
        Http::fake([
            'https://api.linkedin.com/v2/assets' => Http::response([
                'value' => ['asset' => 'urn:li:digitalmediaAsset:123']
            ], 200),
            'https://api.linkedin.com/v2/ugcPosts' => Http::response(['id' => '456'], 200),
        ]);

        $service = LinkedInService::getInstance();
        $result = $service->shareImage('Test image post', 'https://example.com/image.jpg');

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('456', $result['id']);
    }

    public function testAssetUploadForVideo()
    {
        Http::fake([
            'https://api.linkedin.com/v2/assets' => Http::response([
                'value' => ['asset' => 'urn:li:digitalmediaAsset:123']
            ], 200),
            'https://api.linkedin.com/v2/ugcPosts' => Http::response(['id' => '789'], 200),
        ]);

        $service = LinkedInService::getInstance();
        $result = $service->shareVideo('Test video post', 'https://example.com/video.mp4');

        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('789', $result['id']);
    }

    public function testShareImageWithInvalidLocalFile()
    {
        $service = LinkedInService::getInstance();
        
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('Invalid or unreadable local file provided');
        
        $service->shareImage('Test image post', '/path/to/non/existent/image.jpg');
    }

    public function testShareVideoWithInvalidLocalFile()
    {
        $service = LinkedInService::getInstance();
        
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('Invalid or unreadable local file provided');
        
        $service->shareVideo('Test video post', '/path/to/non/existent/video.mp4');
    }
}
