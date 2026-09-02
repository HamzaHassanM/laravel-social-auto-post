<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Tests\Feature;

use HamzaHassanM\LaravelSocialAutoPost\Exceptions\RateLimitException;
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\RetryableException;
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;
use HamzaHassanM\LaravelSocialAutoPost\Services\SocialMediaService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use HamzaHassanM\LaravelSocialAutoPost\Tests\Feature\TestCase;

class TestableSocialMediaService extends SocialMediaService
{
    // Expose the protected sendRequest for testing
    public function publicSendRequest(string $url, string $method = 'post', array $params = [], array $headers = []): array
    {
        return $this->sendRequest($url, $method, $params, $headers);
    }

    // Stub abstract methods if any were added, though none are abstract in the base class.
}

class SocialMediaServiceRetryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Config::set('autopost.retry_attempts', 3);
    }

    public function test_2xx_success_returns_immediately_with_one_attempt()
    {
        Http::fake([
            '*' => Http::response(['success' => true], 200),
        ]);

        $service = new TestableSocialMediaService();
        $response = $service->publicSendRequest('https://api.example.com/post');

        $this->assertEquals(['success' => true], $response);
        Http::assertSentCount(1);
    }

    public function test_400_fails_fast_with_one_attempt_and_no_sleep()
    {
        Http::fake([
            '*' => Http::response(['error' => 'Bad Request'], 400),
        ]);

        $service = new TestableSocialMediaService();

        $start = microtime(true);
        try {
            $service->publicSendRequest('https://api.example.com/post');
            $this->fail('Expected exception was not thrown.');
        } catch (SocialMediaException $e) {
            $this->assertNotInstanceOf(RateLimitException::class, $e);
            $this->assertNotInstanceOf(RetryableException::class, $e);
            $this->assertStringContainsString('API request failed (HTTP 400)', $e->getMessage());
        }
        $duration = microtime(true) - $start;

        Http::assertSentCount(1);
        $this->assertLessThan(0.5, $duration, 'Execution should be fast (no sleep).');
    }

    public function test_429_fails_fast_throws_rate_limit_exception_with_retry_after()
    {
        Http::fake([
            '*' => Http::response(['error' => 'Too Many Requests'], 429, ['Retry-After' => '30']),
        ]);

        $service = new TestableSocialMediaService();

        $start = microtime(true);
        try {
            $service->publicSendRequest('https://api.example.com/post');
            $this->fail('Expected exception was not thrown.');
        } catch (RateLimitException $e) {
            $this->assertEquals(30, $e->getRetryAfter());
        }
        $duration = microtime(true) - $start;

        Http::assertSentCount(1);
        $this->assertLessThan(0.5, $duration, 'Execution should be fast (no sleep).');
    }

    public function test_5xx_retries_and_throws_retryable_exception_after_exhaustion()
    {
        Http::fake([
            '*' => Http::response(['error' => 'Internal Server Error'], 500),
        ]);

        $service = new TestableSocialMediaService();

        try {
            $service->publicSendRequest('https://api.example.com/post');
            $this->fail('Expected exception was not thrown.');
        } catch (RetryableException $e) {
            $this->assertEquals(500, $e->getHttpStatus());
            $this->assertEquals(3, $e->getAttempts());
        }

        Http::assertSentCount(3);
    }

    public function test_transient_connection_timeout_retries_and_throws_retryable_exception()
    {
        // Fake a ConnectionException on every request
        Http::fake([
            '*' => function (Request $request) {
                throw new ConnectionException('cURL error 28: Connection timed out');
            },
        ]);

        $service = new TestableSocialMediaService();

        try {
            $service->publicSendRequest('https://api.example.com/post');
            $this->fail('Expected exception was not thrown.');
        } catch (RetryableException $e) {
            $this->assertEquals(3, $e->getAttempts());
            $this->assertStringContainsString('Network/Connection error', $e->getMessage());
        }

        // Http::assertSentCount does not count exceptions thrown inside fakes in some Laravel versions, 
        // but we verify the attempts passed to the Exception itself.
    }

    public function test_non_transient_exceptions_fail_fast_without_retrying()
    {
        Http::fake([
            '*' => function (Request $request) {
                // Not a ConnectionException, just a generic LogicException
                throw new \LogicException('Malformed configuration');
            },
        ]);

        $service = new TestableSocialMediaService();

        try {
            $service->publicSendRequest('https://api.example.com/post');
            $this->fail('Expected exception was not thrown.');
        } catch (SocialMediaException $e) {
            $this->assertNotInstanceOf(RetryableException::class, $e);
            $this->assertStringContainsString('Request failed unexpectedly: Malformed configuration', $e->getMessage());
        }
    }
}
