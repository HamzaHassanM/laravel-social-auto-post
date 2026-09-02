<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Services;

use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareImagePostInterface;
use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareInterface;
use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareVideoPostInterface;
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;
use Illuminate\Support\Facades\Log;

/**
 * Class TwitterService
 *
 * Service for managing and posting content to Twitter/X using the Twitter API v2.
 *
 * Implements sharing of general posts, images, and videos to Twitter.
 */
class TwitterService extends SocialMediaService implements ShareInterface, ShareImagePostInterface, ShareVideoPostInterface
{
    /**
     * @var string Twitter Bearer Token
     */
    private $bearer_token;

    /**
     * @var string Twitter API Key
     */
    private $api_key;

    /**
     * @var string Twitter API Secret
     */
    private $api_secret;

    /**
     * @var string Twitter Access Token
     */
    private $access_token;

    /**
     * @var string Twitter Access Token Secret
     */
    private $access_token_secret;

    /**
     * @var TwitterService|null Singleton instance
     */
    private static ?TwitterService $instance = null;

    /**
     * Twitter API base URL
     */
    private const API_BASE_URL = 'https://api.twitter.com/2';

    /**
     * Private constructor to prevent direct instantiation.
     */
    public function __construct(
        string $bearerToken,
        string $apiKey,
        string $apiSecret,
        string $accessToken,
        string $accessTokenSecret
    ) {
        $this->bearer_token = $bearerToken;
        $this->api_key = $apiKey;
        $this->api_secret = $apiSecret;
        $this->access_token = $accessToken;
        $this->access_token_secret = $accessTokenSecret;
    }

    
    /**
     * Create a new instance dynamically with custom credentials.
     */
    public static function withCredentials(
        string $bearerToken,
        string $apiKey,
        string $apiSecret,
        string $accessToken,
        string $accessTokenSecret
    ): self 
    {
        return new self($bearerToken, $apiKey, $apiSecret, $accessToken, $accessTokenSecret);
    }

    /**
     * Get the singleton instance of TwitterService.
     */
    public static function getInstance(): TwitterService
    {
        if (self::$instance === null) {
            $bearerToken = config('autopost.twitter_bearer_token');
            $apiKey = config('autopost.twitter_api_key');
            $apiSecret = config('autopost.twitter_api_secret');
            $accessToken = config('autopost.twitter_access_token');
            $accessTokenSecret = config('autopost.twitter_access_token_secret');

            if (!$bearerToken || !$apiKey || !$apiSecret || !$accessToken || !$accessTokenSecret) {
                throw new SocialMediaException('Twitter credentials are not properly configured.');
            }

            self::$instance = new self($bearerToken, $apiKey, $apiSecret, $accessToken, $accessTokenSecret);
        }
        return self::$instance;
    }

    /**
     * Share a text post with a URL to Twitter.
     *
     * @param string $caption The text content of the tweet.
     * @param string $url The URL to share.
     * @return array Response from the Twitter API.
     * @throws SocialMediaException
     */
    public function share(string $caption, string $url): array
    {
        $this->validateInput($caption, $url);
        
        $text = $this->formatTweetText($caption, $url);
        
        if (strlen($text) > 280) {
            throw new SocialMediaException('Tweet text exceeds 280 character limit.');
        }

        $url = $this->buildApiUrl('tweets');
        $params = [
            'text' => $text
        ];

        try {
            $response = $this->sendRequest($url, 'post', $params);
            Log::info('Twitter post shared successfully', ['tweet_id' => $response['data']['id'] ?? null]);
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to share to Twitter', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to share to Twitter: ' . $e->getMessage());
        }
    }

    /**
     * Share an image post with a caption to Twitter.
     *
     * @param string $caption The caption to accompany the image.
     * @param string $image_url The URL of the image.
     * @return array Response from the Twitter API.
     * @throws SocialMediaException
     */
    public function shareImage(string $caption, string $image_url): array
    {
        $this->validateInput($caption, $image_url);
        
        try {
            // Step 1: Upload media
            $mediaId = $this->uploadMedia($image_url, 'image');
            
            // Step 2: Create tweet with media
            $url = $this->buildApiUrl('tweets');
            $params = [
                'text' => $caption,
                'media' => [
                    'media_ids' => [$mediaId]
                ]
            ];

            $response = $this->sendRequest($url, 'post', $params);
            Log::info('Twitter image post shared successfully', ['tweet_id' => $response['data']['id'] ?? null]);
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to share image to Twitter', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to share image to Twitter: ' . $e->getMessage());
        }
    }

    /**
     * Share a video post with a caption to Twitter.
     *
     * @param string $caption The caption to accompany the video.
     * @param string $video_url The URL of the video.
     * @return array Response from the Twitter API.
     * @throws SocialMediaException
     */
    public function shareVideo(string $caption, string $video_url): array
    {
        $this->validateInput($caption, $video_url);
        
        try {
            // Step 1: Upload media
            $mediaId = $this->uploadMedia($video_url, 'video');
            
            // Step 2: Create tweet with media
            $url = $this->buildApiUrl('tweets');
            $params = [
                'text' => $caption,
                'media' => [
                    'media_ids' => [$mediaId]
                ]
            ];

            $response = $this->sendRequest($url, 'post', $params);
            Log::info('Twitter video post shared successfully', ['tweet_id' => $response['data']['id'] ?? null]);
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to share video to Twitter', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to share video to Twitter: ' . $e->getMessage());
        }
    }

    /**
     * Get user's timeline tweets.
     *
     * @param int $limit Number of tweets to retrieve (max 100).
     * @return array Response from the Twitter API.
     * @throws SocialMediaException
     */
    public function getTimeline(int $limit = 10): array
    {
        try {
            $url = $this->buildApiUrl('users/me/tweets');
            $params = [
                'max_results' => min($limit, 100),
                'tweet.fields' => 'created_at,public_metrics'
            ];

            return $this->sendRequest($url, 'get', $params);
        } catch (\Exception $e) {
            Log::error('Failed to get Twitter timeline', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to get Twitter timeline: ' . $e->getMessage());
        }
    }

    /**
     * Get user profile information.
     *
     * @return array Response from the Twitter API.
     * @throws SocialMediaException
     */
    public function getUserInfo(): array
    {
        try {
            $url = $this->buildApiUrl('users/me');
            $params = [
                'user.fields' => 'public_metrics,verified,description'
            ];

            return $this->sendRequest($url, 'get', $params);
        } catch (\Exception $e) {
            Log::error('Failed to get Twitter user info', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to get Twitter user info: ' . $e->getMessage());
        }
    }

    /**
     * Upload media to Twitter.
     *
     * @param string $mediaUrl The URL of the media to upload.
     * @param string $type The type of media (image or video).
     * @return string The media ID.
     * @throws SocialMediaException
     */
    private function uploadMedia(string $mediaUrl, string $type): string
    {
        // Accept both a remote URL (downloaded securely) and a local file path.
        // Local paths are used in tests and allow callers that have already
        // validated / downloaded the file themselves.
        if (filter_var($mediaUrl, FILTER_VALIDATE_URL)) {
            $tempFile = $this->downloadFile($mediaUrl);
            $isTemp   = true;
        } else {
            $tempFile = $mediaUrl;
            $isTemp   = false;
        }

        try {
            // Read the safely limited file into memory for Twitter API
            $mediaContent = file_get_contents($tempFile);
            if ($mediaContent === false) {
                throw new SocialMediaException('Failed to read media file: ' . $tempFile);
            }

            // Upload to Twitter
            $url    = 'https://upload.twitter.com/1.1/media/upload.json';
            $params = [
                'media'          => base64_encode($mediaContent),
                'media_category' => $type === 'video' ? 'tweet_video' : 'tweet_image',
            ];

            $response = $this->sendRequest($url, 'post', $params);

            if (!isset($response['media_id_string'])) {
                throw new SocialMediaException('Failed to upload media to Twitter');
            }

            return $response['media_id_string'];
        } finally {
            if ($isTemp && file_exists($tempFile)) {
                @unlink($tempFile);
            }
        }
    }

    /**
     * Format tweet text with URL.
     *
     * @param string $caption The caption text.
     * @param string $url The URL to include.
     * @return string Formatted tweet text.
     */
    private function formatTweetText(string $caption, string $url): string
    {
        return $caption . ' ' . $url;
    }

    /**
     * Validate input parameters.
     *
     * @param string $caption The caption text.
     * @param string $url The URL.
     * @throws SocialMediaException
     */
    private function validateInput(string $caption, string $urlOrPath): void
    {
        if (empty(trim($caption))) {
            throw new SocialMediaException('Caption cannot be empty.');
        }

        // Accept a valid URL or an existing local file path (for pre-downloaded media).
        if (!filter_var($urlOrPath, FILTER_VALIDATE_URL) && !file_exists($urlOrPath)) {
            throw new SocialMediaException('Invalid URL provided: must be a valid URL or an existing local file path.');
        }
    }

    /**
     * Build Twitter API URL.
     *
     * @param string $endpoint The API endpoint.
     * @return string Complete API URL.
     */
    private function buildApiUrl(string $endpoint): string
    {
        return self::API_BASE_URL . '/' . $endpoint;
    }

    /**
     * Send authenticated request to Twitter API.
     *
     * @param string $url The API URL.
     * @param string $method The HTTP method.
     * @param array $params The request parameters.
     * @return array Response from the API.
     * @throws SocialMediaException
     */
    protected function sendRequest(string $url, string $method = 'post', array $params = [], array $headers = []): array
    {
        $defaultHeaders = [
            'Authorization' => 'Bearer ' . $this->bearer_token,
            'Content-Type' => 'application/json'
        ];
        
        $headers = array_merge($defaultHeaders, $headers);

        $response = \Illuminate\Support\Facades\Http::withHeaders($headers)
            ->{$method}($url, $params);

        if (!$response->successful()) {
            $errorMessage = $response->json()['detail'] ?? 'Unknown error occurred';
            throw new SocialMediaException("Twitter API error: {$errorMessage}");
        }

        return $response->json();
    }
}
