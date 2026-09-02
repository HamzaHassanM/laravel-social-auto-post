<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Utils;

use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;
use Illuminate\Support\Facades\Log;

class SafeMediaFetcher
{
    private const MAX_REDIRECTS = 5;
    private const CONNECT_TIMEOUT = 10;
    private const TOTAL_TIMEOUT = 120;
    private const LOW_SPEED_LIMIT = 1024; // 1 KB/s
    private const LOW_SPEED_TIME = 15; // abort if below 1KB/s for 15s
    
    private int $maxBytes;
    private array $allowedMimeTypes;
    
    private int $bytesReceived = 0;
    private ?string $tempFilePath = null;
    private $fileHandle = null;

    /**
     * @param int $maxBytes Maximum file size in bytes (default 50MB)
     * @param array $allowedMimeTypes Allowed MIME types prefix (empty array = any)
     */
    public function __construct(int $maxBytes = 52428800, array $allowedMimeTypes = ['image/', 'video/'])
    {
        $this->maxBytes = $maxBytes;
        $this->allowedMimeTypes = $allowedMimeTypes;
    }

    /**
     * Static helper for ease of use.
     *
     * @throws SocialMediaException
     */
    public static function fetch(string $url, ?int $maxBytes = null): string
    {
        $maxBytes = $maxBytes ?? 52428800;
        try {
            if (function_exists('config')) {
                $maxBytes = config('autopost.max_media_size', $maxBytes);
            }
        } catch (\Throwable $t) {
            // Fallback for tests where Laravel container isn't fully booted
        }
        
        $fetcher = new self($maxBytes);
        return $fetcher->execute($url);
    }

    /**
     * Execute the secure fetch.
     *
     * @throws SocialMediaException
     */
    public function execute(string $url, int $redirectCount = 0): string
    {
        if ($redirectCount > self::MAX_REDIRECTS) {
            throw new SocialMediaException("Too many redirects (max " . self::MAX_REDIRECTS . ").");
        }

        // Validate scheme
        $scheme = parse_url($url, PHP_URL_SCHEME);
        if (!in_array(strtolower($scheme ?? ''), ['http', 'https'], true)) {
            throw new SocialMediaException("Invalid URL scheme. Only HTTP and HTTPS are allowed.");
        }

        // Validate hostname and resolve to IP
        $host = parse_url($url, PHP_URL_HOST);
        if (empty($host)) {
            throw new SocialMediaException("Invalid URL: Hostname missing.");
        }
        
        // Remove IPv6 brackets if present
        $host = trim($host, '[]');

        // Get port (default to 80/443 based on scheme)
        $port = parse_url($url, PHP_URL_PORT);
        if (empty($port)) {
            $port = (strtolower($scheme) === 'https') ? 443 : 80;
        }

        // Resolve DNS and validate the resolved IP
        $resolvedIp = $this->resolveAndValidateHost($host);

        // Prepare temp file for streaming
        if ($redirectCount === 0) {
            $this->bytesReceived = 0;
            $this->tempFilePath = tempnam(sys_get_temp_dir(), 'social_post_media_');
            if ($this->tempFilePath === false) {
                throw new SocialMediaException("Failed to create a temporary file for download.");
            }
        }
        
        // Always truncate ('wb') so that a redirect response body from a previous
        // iteration is never prepended to the final media bytes. (Bug fix: was 'ab')
        $this->fileHandle = fopen($this->tempFilePath, 'wb');
        if ($this->fileHandle === false) {
            $this->cleanup();
            throw new SocialMediaException("Failed to open temporary file for writing.");
        }

        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        
        // Prevent cURL from following redirects automatically so we can validate them
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        
        // Force cURL to use the resolved IP (mitigates DNS rebinding)
        // CURLOPT_RESOLVE format: array("HOST:PORT:IP")
        curl_setopt($ch, CURLOPT_RESOLVE, ["{$host}:{$port}:{$resolvedIp}"]);
        
        // Disable proxy by explicitly setting CURLOPT_PROXY to an empty string.
        // This ensures the request uses the validated destination IP directly and
        // ignores any ambient proxy environment variables (e.g. HTTP_PROXY).
        curl_setopt($ch, CURLOPT_PROXY, '');
        
        // Ensure a fresh connection is made using the resolved IP
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        
        // Timeouts
        $timeout = self::TOTAL_TIMEOUT;
        try {
            if (function_exists('config')) {
                $timeout = config('autopost.timeout', $timeout);
            }
        } catch (\Throwable $t) {
            // Fallback for tests
        }
        
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CONNECT_TIMEOUT);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_LOW_SPEED_LIMIT, self::LOW_SPEED_LIMIT);
        curl_setopt($ch, CURLOPT_LOW_SPEED_TIME, self::LOW_SPEED_TIME);
        
        // Header callback for redirects
        $redirectUrl = null;
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($ch, $header) use (&$redirectUrl, $url) {
            $len = strlen($header);
            $headerParts = explode(':', $header, 2);
            if (count($headerParts) < 2) {
                return $len;
            }

            if (strtolower(trim($headerParts[0])) === 'location') {
                $location = trim($headerParts[1]);
                // Handle relative URLs
                if (preg_match('~^(http|https)://~i', $location)) {
                    $redirectUrl = $location;
                } else {
                    $redirectUrl = $this->resolveRelativeUrl($url, $location);
                }
            }
            return $len;
        });

        // Write callback for streaming and size enforcement
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $data) {
            $chunkSize = strlen($data);
            $this->bytesReceived += $chunkSize;
            
            if ($this->bytesReceived > $this->maxBytes) {
                // Abort transfer
                return -1; 
            }
            
            return fwrite($this->fileHandle, $data);
        });

        // Execute
        $result = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($this->fileHandle);

        if ($result === false) {
            $this->cleanup();
            if ($this->bytesReceived > $this->maxBytes) {
                throw new SocialMediaException("Download aborted: File size exceeded the limit of {$this->maxBytes} bytes.");
            }
            throw new SocialMediaException("Failed to download file: " . $error);
        }

        // Handle Redirects manually
        if ($httpCode >= 300 && $httpCode < 400 && $redirectUrl) {
            return $this->execute($redirectUrl, $redirectCount + 1);
        }

        // Validate HTTP Code
        if ($httpCode < 200 || $httpCode >= 300) {
            $this->cleanup();
            throw new SocialMediaException("Download failed with HTTP status {$httpCode}.");
        }

        // Validate MIME type
        if ($redirectCount === 0 || $this->bytesReceived > 0) {
             $this->validateMimeType();
        }

        return $this->tempFilePath;
    }

    /**
     * Delete the temporary file if it exists.
     */
    public function cleanup(): void
    {
        if ($this->tempFilePath && file_exists($this->tempFilePath)) {
            @unlink($this->tempFilePath);
        }
    }

    /**
     * Resolves hostname and validates the IP against SSRF blacklists.
     *
     * @throws SocialMediaException
     */
    private function resolveAndValidateHost(string $host): string
    {
        // First check if host is already an IP
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            $this->validateIpAddress($host);
            return $host;
        }

        // Resolve DNS
        $records = dns_get_record($host, DNS_A | DNS_AAAA);
        if ($records === false || empty($records)) {
            throw new SocialMediaException("Could not resolve hostname: {$host}");
        }

        // Validate the first successfully resolved IP
        $resolvedIp = $records[0]['ipv6'] ?? $records[0]['ip'] ?? null;
        
        if (!$resolvedIp) {
            throw new SocialMediaException("Hostname resolved to an empty IP record.");
        }

        $this->validateIpAddress($resolvedIp);
        
        return $resolvedIp;
    }

    /**
     * Validates IP address against a strict blacklist.
     *
     * @throws SocialMediaException
     */
    private function validateIpAddress(string $ip): void
    {
        // Handle IPv4-mapped IPv6 addresses (e.g. ::ffff:192.168.1.1)
        if (preg_match('/^::ffff:(\d+\.\d+\.\d+\.\d+)$/i', $ip, $matches)) {
            $ip = $matches[1];
        }

        // FILTER_VALIDATE_IP flags to easily block private and reserved ranges
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            throw new SocialMediaException("Access to private or reserved IP ({$ip}) is forbidden.");
        }
        
        // Manual checks for specific IPv6 ranges that might slip through
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            // Loopback
            if ($ip === '::1') {
                throw new SocialMediaException("Access to loopback IP is forbidden.");
            }
            // IPv4-compatible (::/96)
            if (str_starts_with($ip, '::') && substr_count($ip, ':') >= 2) {
                // Not a robust check for ::/96 but we rely on filter_var mainly
                // Just block completely 0-prefixed other than ::1
                if (preg_match('/^::[0-9a-f]{1,4}$/i', $ip) && $ip !== '::1') {
                    throw new SocialMediaException("Access to IPv4-compatible IPv6 is forbidden.");
                }
            }
        }
    }

    /**
     * Validates the MIME type of the fully downloaded file.
     *
     * @throws SocialMediaException
     */
    private function validateMimeType(): void
    {
        if (!empty($this->allowedMimeTypes)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $this->tempFilePath);
            finfo_close($finfo);

            if ($mime === false) {
                $this->cleanup();
                throw new SocialMediaException("Failed to detect MIME type of downloaded file.");
            }

            $allowed = false;
            foreach ($this->allowedMimeTypes as $prefix) {
                if (str_starts_with($mime, $prefix)) {
                    $allowed = true;
                    break;
                }
            }

            if (!$allowed) {
                $this->cleanup();
                throw new SocialMediaException("MIME type '{$mime}' is not allowed.");
            }
        }
    }

    /**
     * Helper to resolve relative redirects against the base URL.
     */
    private function resolveRelativeUrl(string $baseUrl, string $relativeUrl): string
    {
        if (parse_url($relativeUrl, PHP_URL_SCHEME) != '') {
            return $relativeUrl;
        }

        if (str_starts_with($relativeUrl, '//')) {
            // Protocol-relative URL (e.g. "//cdn.example.com/file.mp4"):
            // prepend the scheme only — do NOT strip the "//" from the path.
            // Bug fix: ltrim($relativeUrl, '/') was removing both slashes,
            // producing "https:cdn.example.com" instead of "https://cdn.example.com".
            $scheme = parse_url($baseUrl, PHP_URL_SCHEME);
            return $scheme . ':' . $relativeUrl;
        }

        $parsedBase = parse_url($baseUrl);
        $scheme = $parsedBase['scheme'];
        $host = $parsedBase['host'];
        $port = isset($parsedBase['port']) ? ':' . $parsedBase['port'] : '';
        
        if (str_starts_with($relativeUrl, '/')) {
            return "{$scheme}://{$host}{$port}{$relativeUrl}";
        }

        $path = $parsedBase['path'] ?? '/';
        $path = preg_replace('#/[^/]*$#', '', $path);
        
        return "{$scheme}://{$host}{$port}{$path}/{$relativeUrl}";
    }
}
