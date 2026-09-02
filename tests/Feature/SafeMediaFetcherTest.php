<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Tests\Feature;

use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;
use HamzaHassanM\LaravelSocialAutoPost\Utils\SafeMediaFetcher;
use PHPUnit\Framework\TestCase;

class SafeMediaFetcherTest extends TestCase
{
    protected function tearDown(): void
    {
        // Clean up temp files that might have leaked
        $files = glob(sys_get_temp_dir() . '/social_post_media_*');
        foreach ($files as $file) {
            @unlink($file);
        }
        parent::tearDown();
    }

    public function test_it_rejects_invalid_schemes()
    {
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('Invalid URL scheme');
        
        SafeMediaFetcher::fetch('file:///etc/passwd');
    }

    public function test_it_rejects_localhost_ipv4()
    {
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('Access to private or reserved IP');
        
        SafeMediaFetcher::fetch('http://127.0.0.1/test.mp4');
    }

    public function test_it_rejects_private_ipv4()
    {
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('Access to private or reserved IP');
        
        SafeMediaFetcher::fetch('http://10.0.0.1/test.mp4');
    }

    public function test_it_rejects_aws_metadata()
    {
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('Access to private or reserved IP');
        
        SafeMediaFetcher::fetch('http://169.254.169.254/latest/meta-data/');
    }

    public function test_it_rejects_localhost_ipv6()
    {
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('Access to private or reserved IP');
        
        SafeMediaFetcher::fetch('http://[::1]/test.mp4');
    }

    public function test_it_rejects_ipv4_mapped_ipv6()
    {
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('Access to private or reserved IP');
        
        SafeMediaFetcher::fetch('http://[::ffff:127.0.0.1]/test.mp4');
    }

    public function test_it_rejects_dns_rebinding_to_localhost()
    {
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('Access to private or reserved IP');
        
        // This public DNS record is known to resolve to 127.0.0.1 for testing
        // localtest.me -> 127.0.0.1
        SafeMediaFetcher::fetch('http://localtest.me/test.mp4');
    }

    public function test_it_enforces_size_limit_and_cleans_up_temp_file()
    {
        $limit = 10; // 10 bytes max
        $url = 'https://raw.githubusercontent.com/HamzaHassanM/laravel-social-auto-post/master/README.md';

        $filesBefore = glob(sys_get_temp_dir() . '/social_post_media_*');
        
        try {
            SafeMediaFetcher::fetch($url, $limit);
            $this->fail('Expected exception for size limit exceeded was not thrown.');
        } catch (SocialMediaException $e) {
            $this->assertStringContainsString('File size exceeded the limit', $e->getMessage());
        }

        $filesAfter = glob(sys_get_temp_dir() . '/social_post_media_*');
        
        // Assert temp file was cleaned up on failure
        $this->assertEquals(count($filesBefore), count($filesAfter));
    }

    public function test_it_validates_mime_type_on_success()
    {
        $url = 'https://raw.githubusercontent.com/HamzaHassanM/laravel-social-auto-post/master/README.md';
        
        try {
            // Attempt to fetch, expecting image/video by default
            SafeMediaFetcher::fetch($url);
            $this->fail('Expected exception for invalid MIME type was not thrown.');
        } catch (SocialMediaException $e) {
            $this->assertStringContainsString('is not allowed', $e->getMessage());
        }
    }

    public function test_it_allows_disabling_mime_check()
    {
        // README.md is a text file.
        $url = 'https://raw.githubusercontent.com/HamzaHassanM/laravel-social-auto-post/master/README.md';
        
        $fetcher = new SafeMediaFetcher(1024 * 1024, []); // Empty array = no mime check
        $tempFile = $fetcher->execute($url);
        
        $this->assertFileExists($tempFile);
        $content = file_get_contents($tempFile);
        $this->assertStringContainsString('Laravel Social Auto Post', $content);
        
        // Caller is responsible for cleanup
        unlink($tempFile);
        $this->assertFileDoesNotExist($tempFile);
    }

    public function test_it_rejects_redirect_to_private_ip()
    {
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('Access to private or reserved IP');
        
        SafeMediaFetcher::fetch('https://httpbin.org/redirect-to?url=http://10.0.0.1/test.mp4');
    }

    public function test_it_rejects_redirect_to_invalid_scheme()
    {
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('Invalid URL scheme');
        
        SafeMediaFetcher::fetch('https://httpbin.org/redirect-to?url=file:///etc/passwd');
    }

    public function test_it_limits_redirects()
    {
        $this->expectException(SocialMediaException::class);
        $this->expectExceptionMessage('Too many redirects');
        
        SafeMediaFetcher::fetch('https://httpbin.org/redirect/6');
    }
}
