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
        $maxRetries = config('autopost.retry_attempts', 3);
        $timeout = config('autopost.timeout', 30);
        
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = Http::timeout($timeout)
                    ->withHeaders($headers)
                    ->{$method}($url, $params);

                if (!$response->successful()) {
                    $errorMessage = $this->extractErrorMessage($response);
                    
                    if ($attempt === $maxRetries) {
                        Log::error('Social media API request failed after all retries', [
                            'url' => $url,
                            'method' => $method,
                            'status' => $response->status(),
                            'error' => $errorMessage,
                            'attempts' => $attempt
                        ]);
                        
                        throw new SocialMediaException("API request failed: {$errorMessage}");
                    }
                    
                    Log::warning('Social media API request failed, retrying', [
                        'url' => $url,
                        'status' => $response->status(),
                        'error' => $errorMessage,
                        'attempt' => $attempt
                    ]);
                    
                    // Wait before retry (exponential backoff)
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
                
            } catch (\Exception $e) {
                if ($attempt === $maxRetries) {
                    Log::error('Social media API request failed with exception', [
                        'url' => $url,
                        'method' => $method,
                        'error' => $e->getMessage(),
                        'attempts' => $attempt
                    ]);
                    
                    throw new SocialMediaException("Request failed: " . $e->getMessage());
                }
                
                Log::warning('Social media API request failed with exception, retrying', [
                    'url' => $url,
                    'error' => $e->getMessage(),
                    'attempt' => $attempt
                ]);
                
                // Wait before retry
                sleep(pow(2, $attempt - 1));
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
}