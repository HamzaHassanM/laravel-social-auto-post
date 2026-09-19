<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Utils {
    // Namespace mock for dns_get_record
    function dns_get_record($hostname, $type = DNS_ANY) {
        if ($hostname === 'mock-mixed-dns.com') {
            return [
                ['ip' => '8.8.8.8'],
                ['ip' => '10.0.0.1']
            ];
        }
        if ($hostname === 'mock-safe-dns.com') {
            return [
                ['ip' => '8.8.8.8'],
                ['ip' => '1.1.1.1']
            ];
        }
        return \dns_get_record($hostname, $type);
    }
}

namespace HamzaHassanM\LaravelSocialAutoPost\Tests\Unit {

    use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;
    use HamzaHassanM\LaravelSocialAutoPost\Utils\SafeMediaFetcher;
    use HamzaHassanM\LaravelSocialAutoPost\Tests\Unit\TestCase;
    
    class SafeMediaFetcherSsrfTest extends TestCase
    {
        public function test_it_rejects_host_when_any_dns_record_is_private(): void
        {
            $this->expectException(SocialMediaException::class);
            $this->expectExceptionMessage('Access to private or reserved IP');
            
            SafeMediaFetcher::fetch('http://mock-mixed-dns.com/file.mp4');
        }

        public function test_it_allows_host_when_all_dns_records_are_public(): void
        {
            try {
                // Should fail due to timeout/not found, but NOT due to SSRF
                SafeMediaFetcher::fetch('http://mock-safe-dns.com/file.mp4');
            } catch (SocialMediaException $e) {
                $this->assertStringNotContainsString('Access to private or reserved IP', $e->getMessage());
            }
        }
        public function test_it_rejects_ipv6_with_zone_id(): void
        {
            $this->expectException(SocialMediaException::class);
            $this->expectExceptionMessage('Access to private or reserved IP');
            SafeMediaFetcher::fetch('http://[fe80::1%25eth0]/file.mp4');
        }

        public function test_it_rejects_loopback_ipv6_with_zone_id(): void
        {
            $this->expectException(SocialMediaException::class);
            $this->expectExceptionMessage('Access to private or reserved IP');
            SafeMediaFetcher::fetch('http://[::1%25lo]/file.mp4');
        }
    }
}
