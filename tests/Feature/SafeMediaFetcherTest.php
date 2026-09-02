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
        
        \HamzaHassanM\LaravelSocialAutoPost\Utils\ConfigHelper::clearOverrides();
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
        $this->expectExceptionMessage('Access to private or reserved IP (127.0.0.1) is forbidden.');
        
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
            $this->assertStringContainsString('Security error: Downloaded file has invalid MIME type', $e->getMessage());
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

    // ─────────────────────────────────────────────────────────────────────────
    // TDD: Bug 2 — protocol-relative redirect URL construction
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * BUG: ltrim($relativeUrl, '/') strips both slashes from "//host/path",
     * producing "https:host/path" instead of "https://host/path".
     *
     * @test
     */
    public function test_resolves_protocol_relative_redirect_url_correctly(): void
    {
        $fetcher = new SafeMediaFetcher();
        $method  = new \ReflectionMethod(SafeMediaFetcher::class, 'resolveRelativeUrl');
        $method->setAccessible(true);

        $result = $method->invoke(
            $fetcher,
            'https://example.com/some/path',
            '//cdn.example.com/media/file.mp4'
        );

        $this->assertEquals(
            'https://cdn.example.com/media/file.mp4',
            $result,
            'Protocol-relative redirect must produce a valid absolute URL with scheme and double-slash.'
        );
    }

    /**
     * Absolute redirect URL must pass through unchanged.
     *
     * @test
     */
    public function test_resolves_absolute_redirect_url_unchanged(): void
    {
        $fetcher = new SafeMediaFetcher();
        $method  = new \ReflectionMethod(SafeMediaFetcher::class, 'resolveRelativeUrl');
        $method->setAccessible(true);

        $result = $method->invoke(
            $fetcher,
            'https://example.com/',
            'https://other.example.com/file.mp4'
        );

        $this->assertEquals('https://other.example.com/file.mp4', $result);
    }

    /**
     * Root-relative redirect (e.g. "/media/file.mp4") must be resolved
     * against the origin of the base URL.
     *
     * @test
     */
    public function test_resolves_root_relative_redirect_url_correctly(): void
    {
        $fetcher = new SafeMediaFetcher();
        $method  = new \ReflectionMethod(SafeMediaFetcher::class, 'resolveRelativeUrl');
        $method->setAccessible(true);

        $result = $method->invoke(
            $fetcher,
            'https://example.com/some/path',
            '/media/file.mp4'
        );

        $this->assertEquals('https://example.com/media/file.mp4', $result);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TDD: Bug 1 — redirect response body must not be prepended to final file
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * When downloading a URL that redirects to the real media, the final
     * temp file must contain ONLY the body of the final (200) response.
     *
     * httpbin.org/redirect-to?url=<target>&status_code=302 returns a 302
     * with a small HTML body, then the target.
     * Before the fix, the HTML body was prepended to the image bytes,
     * corrupting the file and making finfo see "text/html".
     *
     * We verify: MIME type of the saved file is image/jpeg (not text/html).
     *
     * @test
     */
    public function test_redirect_final_file_contains_only_target_body(): void
    {
        // httpbin.org/image/jpeg reliably returns a real JPEG (Content-Type: image/jpeg).
        // httpbin.org/redirect-to?url=<target>&status_code=302 returns a 302 whose
        // response body is a short HTML snippet — exactly what triggers Bug 1.
        // Before the fix, the HTML body was prepended to the JPEG bytes, making
        // finfo_file() report "text/html" instead of "image/jpeg".
        $targetUrl   = 'https://httpbin.org/image/jpeg';
        $redirectUrl = 'https://httpbin.org/redirect-to?url=' . urlencode($targetUrl) . '&status_code=302';

        $fetcher  = new SafeMediaFetcher(1024 * 1024, []); // disable MIME check; we inspect manually
        $tempFile = $fetcher->execute($redirectUrl);

        try {
            $this->assertFileExists($tempFile);

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $tempFile);
            finfo_close($finfo);

            $this->assertEquals(
                'image/jpeg',
                $mime,
                'File after redirect must be pure JPEG — not corrupted by the redirect response HTML body.'
            );
        } finally {
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }
        }
    }
    public function test_ignores_ambient_proxy_environment_variables(): void
    {
        // Set a dummy HTTP_PROXY that points to an invalid/non-existent server
        putenv('HTTP_PROXY=http://127.0.0.1:9999');
        putenv('HTTPS_PROXY=http://127.0.0.1:9999');

        try {
            $url = 'https://httpbin.org/image/jpeg';
            $fetcher = new SafeMediaFetcher(1024 * 1024, []); // disable MIME checking for simplicity

            // If the proxy is used, this will fail with a connection refused error or timeout.
            // If CURLOPT_PROXY => '' works, it will bypass the proxy and succeed.
            $tempFile = $fetcher->execute($url);
            
            $this->assertFileExists($tempFile);
            $this->assertGreaterThan(0, filesize($tempFile));

            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }
        } finally {
            // Clean up environment variables
            putenv('HTTP_PROXY');
            putenv('HTTPS_PROXY');
        }
    }

    public function test_it_allows_private_ipv4_when_ssrf_protection_is_disabled()
    {
        \HamzaHassanM\LaravelSocialAutoPost\Utils\ConfigHelper::$testOverrides['autopost.enforce_ssrf_protection'] = false;

        $this->expectException(SocialMediaException::class);
        
        try {
            SafeMediaFetcher::fetch('http://10.255.255.255/test.mp4', null); // Use an unroutable IP
        } catch (SocialMediaException $e) {
            $this->assertStringNotContainsString('Security error: Hostname resolves to a private or reserved IP address', $e->getMessage());
            $this->assertStringNotContainsString('Access to private or reserved IP', $e->getMessage());
            throw $e; // Re-throw to satisfy expectException
        }
    }

    public function test_it_allows_localhost_when_ssrf_protection_is_disabled()
    {
        \HamzaHassanM\LaravelSocialAutoPost\Utils\ConfigHelper::$testOverrides['autopost.enforce_ssrf_protection'] = false;
        
        $this->expectException(SocialMediaException::class);
        
        try {
            SafeMediaFetcher::fetch('http://127.0.0.1:9999/test.mp4', null);
        } catch (SocialMediaException $e) {
            $this->assertStringNotContainsString('Security error: Hostname resolves to a private or reserved IP address', $e->getMessage());
            $this->assertStringNotContainsString('Access to private or reserved IP', $e->getMessage());
            throw $e;
        }
    }

    public function test_it_allows_invalid_mime_type_when_mime_verification_is_disabled()
    {
        \HamzaHassanM\LaravelSocialAutoPost\Utils\ConfigHelper::$testOverrides['autopost.verify_media_mime_type'] = false;
        
        // README.md is a text file (text/plain).
        $url = 'https://raw.githubusercontent.com/HamzaHassanM/laravel-social-auto-post/master/README.md';
        
        // This should NOT throw any MIME type exceptions because verification is disabled.
        $tempFile = SafeMediaFetcher::fetch($url);
        
        $this->assertFileExists($tempFile);
        @unlink($tempFile);
    }

}
