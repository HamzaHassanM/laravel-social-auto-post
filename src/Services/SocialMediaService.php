<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;

abstract class SocialMediaService
{
    /**
     * Send HTTP request with error handling and retry logic.
     *
     * @param string $url The request URL.
     * @param string $method The HTTP method.
     * @param array $params The request parameters.
     * @param array $headers Additional headers.
     * @return array Response data.
     * @throws SocialMediaException
     */
    protected function sendRequest(string $url, string $method = 'post', array $params = [], array $headers = []): array
    {
        // Total HTTP attempts to make (including the first attempt)
        $maxAttempts = config('autopost.retry_attempts', 3);
        $timeout = config('autopost.timeout', 30);
        
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $response = Http::timeout($timeout)
                    ->withHeaders($headers)
                    ->{$method}($url, $params);

                if (!$response->successful()) {
                    $errorMessage = $this->extractErrorMessage($response);
                    $status = $response->status();

                    // 429 Too Many Requests -> RateLimitException (No retry loop)
                    if ($status === 429) {
                        $retryAfter = $response->header('Retry-After');
                        throw new \HamzaHassanM\LaravelSocialAutoPost\Exceptions\RateLimitException(
                            "Rate limit exceeded: {$errorMessage}",
                            $retryAfter
                        );
                    }

                    // 4xx Client Errors (except 429) -> Fail Fast (No retry loop)
                    if ($status >= 400 && $status < 500) {
                        throw new SocialMediaException("API request failed (HTTP {$status}): {$errorMessage}");
                    }
                    
                    // 5xx Server Errors -> Retryable
                    if ($attempt === $maxAttempts) {
                        Log::error('Social media API request failed after all retries', [
                            'url' => $url,
                            'method' => $method,
                            'status' => $status,
                            'error' => $errorMessage,
                            'attempts' => $attempt
                        ]);
                        
                        throw new \HamzaHassanM\LaravelSocialAutoPost\Exceptions\RetryableException(
                            "API request failed (HTTP {$status}) after {$attempt} attempts: {$errorMessage}",
                            0,
                            null,
                            null,
                            $status,
                            $attempt
                        );
                    }
                    
                    Log::warning('Social media API request failed with 5xx, retrying', [
                        'url' => $url,
                        'status' => $status,
                        'error' => $errorMessage,
                        'attempt' => $attempt
                    ]);
                    
                    // Bounded backoff for 5xx only
                    sleep(pow(2, $attempt - 1));
                    continue;
                }

                $data = $response->json();
                
                Log::info('Social media API request successful', [
                    'url' => $url,
                    'method' => $method,
                    'status' => $response->status(),
                    'attempt' => $attempt
                ]);
                
                return $data;
                
            } catch (\HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException $e) {
                // Let our specific exceptions (RateLimit, Retryable, or 4xx Fail-fast) bubble up
                throw $e;
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                if (!$this->isTransientNetworkError($e)) {
                    throw new SocialMediaException("Non-transient network error: " . $e->getMessage(), 0, $e);
                }

                // Transient network errors
                if ($attempt === $maxAttempts) {
                    throw new \HamzaHassanM\LaravelSocialAutoPost\Exceptions\RetryableException(
                        "Network/Connection error after {$attempt} attempts: " . $e->getMessage(),
                        0,
                        $e,
                        null,
                        null,
                        $attempt
                    );
                }
                
                Log::warning('Social media API connection failed, retrying', [
                    'url' => $url,
                    'error' => $e->getMessage(),
                    'attempt' => $attempt
                ]);
                
                sleep(pow(2, $attempt - 1));
            } catch (\Exception $e) {
                // Non-transient or generic exceptions -> Fail Fast
                throw new SocialMediaException("Request failed unexpectedly: " . $e->getMessage(), 0, $e);
            }
        }
        
        throw new SocialMediaException('Request failed after all retry attempts');
    }

    /**
     * Extract error message from response.
     *
     * @param \Illuminate\Http\Client\Response $response The HTTP response.
     * @return string The error message.
     */
    private function extractErrorMessage($response): string
    {
        $data = $response->json();
        
        if (isset($data['error']['message'])) {
            return $data['error']['message'];
        }
        
        if (isset($data['message'])) {
            return $data['message'];
        }
        
        if (isset($data['error'])) {
            return is_string($data['error']) ? $data['error'] : json_encode($data['error']);
        }
        
        return "HTTP {$response->status()}: {$response->body()}";
    }

    /**
     * Validate URL format.
     *
     * @param string $url The URL to validate.
     * @throws SocialMediaException
     */
    protected function validateUrl(string $url): void
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new SocialMediaException('Invalid URL provided: ' . $url);
        }
    }

    /**
     * Validate text content.
     *
     * @param string $text The text to validate.
     * @param int $maxLength Maximum allowed length.
     * @throws SocialMediaException
     */
    protected function validateText(string $text, int $maxLength = 1000): void
    {
        if (empty(trim($text))) {
            throw new SocialMediaException('Text content cannot be empty.');
        }
        
        if (strlen($text) > $maxLength) {
            throw new SocialMediaException("Text content exceeds maximum length of {$maxLength} characters.");
        }
    }

    /**
     * Download file from URL securely using SafeMediaFetcher.
     * Note: This returns a temporary local file path. It is the caller's responsibility
     * to ensure the file is cleaned up after use.
     *
     * @param string $url The file URL.
     * @return string The local path to the downloaded file.
     * @throws SocialMediaException
     */
    protected function downloadFile(string $url): string
    {
        // SafeMediaFetcher handles URL validation, SSRF, DNS Rebinding,
        // streaming limits, timeouts, and private IP blocking.
        return \HamzaHassanM\LaravelSocialAutoPost\Utils\SafeMediaFetcher::fetch($url);
    }

    /**
     * Determine if a ConnectionException is transient (retryable) based on cURL error codes.
     */
    protected function isTransientNetworkError(\Exception $e): bool
    {
        $message = $e->getMessage();
        
        // Extract cURL error code if present (Guzzle/Laravel standard format: "cURL error XX: ...")
        if (preg_match('/cURL error (\d+):/', $message, $matches)) {
            $curlErrorCode = (int) $matches[1];
            
            // Known transient cURL errors:
            // 28: CURLE_OPERATION_TIMEDOUT (Timeout)
            // 7: CURLE_COULDNT_CONNECT (Connection refused - might be temporary)
            // 52: CURLE_GOT_NOTHING (Empty reply from server)
            // 56: CURLE_RECV_ERROR (Failure in receiving network data)
            $transientCodes = [28, 7, 52, 56];
            
            // Known NON-transient cURL errors:
            // 6: CURLE_COULDNT_RESOLVE_HOST (DNS failure)
            // 3: CURLE_URL_MALFORMAT (Malformed URL)
            // 35, 51, 58, 60, 77: SSL/TLS related errors
            $nonTransientCodes = [6, 3, 35, 51, 58, 60, 77];
            
            if (in_array($curlErrorCode, $nonTransientCodes, true)) {
                return false; // Definitely not transient
            }
            
            if (in_array($curlErrorCode, $transientCodes, true)) {
                return true; // Definitely transient
            }
        }
        
        // Fallback: Check if the message contains timeout-related keywords
        $lowerMessage = strtolower($message);
        if (str_contains($lowerMessage, 'timeout') || str_contains($lowerMessage, 'timed out')) {
            return true;
        }
        
        // By default, assume non-transient to prevent retry amplification on unknown errors
        return false;
    }
}