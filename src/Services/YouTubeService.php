<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Services;

use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareImagePostInterface;
use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareInterface;
use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareVideoPostInterface;
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;
use Illuminate\Support\Facades\Log;

/**
 * Class YouTubeService
 *
 * Service for managing and posting content to YouTube using the YouTube Data API v3.
 *
 * Implements sharing of videos to YouTube.
 */
class YouTubeService extends SocialMediaService implements ShareInterface, ShareImagePostInterface, ShareVideoPostInterface
{
    /**
     * @var string YouTube API Key
     */
    private $api_key;

    /**
     * @var string YouTube OAuth Access Token
     */
    private $access_token;

    /**
     * @var string YouTube Channel ID
     */
    private $channel_id;

    /**
     * @var YouTubeService|null Singleton instance
     */
    private static ?YouTubeService $instance = null;

    /**
     * YouTube API base URL
     */
    private const API_BASE_URL = 'https://www.googleapis.com/youtube/v3';

    /**
     * Private constructor to prevent direct instantiation.
     */
    public function __construct(
        string $apiKey,
        string $accessToken,
        string $channelId
    ) {
        $this->api_key = $apiKey;
        $this->access_token = $accessToken;
        $this->channel_id = $channelId;
    }

    
    /**
     * Create a new instance dynamically with custom credentials.
     */
    public static function withCredentials(
        string $apiKey,
        string $accessToken,
        string $channelId
    ): self 
    {
        return new self($apiKey, $accessToken, $channelId);
    }

    /**
     * Get the singleton instance of YouTubeService.
     */
    public static function getInstance(): YouTubeService
    {
        if (self::$instance === null) {
            $apiKey = config('autopost.youtube_api_key');
            $accessToken = config('autopost.youtube_access_token');
            $channelId = config('autopost.youtube_channel_id');

            if (!$apiKey || !$accessToken || !$channelId) {
                throw new SocialMediaException('YouTube credentials are not properly configured.');
            }

            self::$instance = new self($apiKey, $accessToken, $channelId);
        }
        return self::$instance;
    }

    /**
     * Share a text post with a URL to YouTube.
     * Note: YouTube doesn't support direct text posts, so this creates a community post.
     *
     * @param string $caption The text content of the post.
     * @param string $url The URL to share.
     * @return array Response from the YouTube API.
     * @throws SocialMediaException
     */
    public function share(string $caption, string $url): array
    {
        $this->validateInput($caption, $url);
        
        try {
            // YouTube doesn't support direct text posts
            // We'll create a community post
            return $this->createCommunityPost($caption, $url);
        } catch (\Exception $e) {
            Log::error('Failed to share to YouTube', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to share to YouTube: ' . $e->getMessage());
        }
    }

    /**
     * Share an image post with a caption to YouTube.
     * Note: YouTube doesn't support direct image posts, so this creates a community post.
     *
     * @param string $caption The caption to accompany the image.
     * @param string $image_url The URL of the image.
     * @return array Response from the YouTube API.
     * @throws SocialMediaException
     */
    public function shareImage(string $caption, string $image_url): array
    {
        $this->validateInput($caption, $image_url);
        
        try {
            // YouTube doesn't support direct image posts
            // We'll create a community post with the image
            return $this->createCommunityPost($caption, $image_url, 'image');
        } catch (\Exception $e) {
            Log::error('Failed to share image to YouTube', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to share image to YouTube: ' . $e->getMessage());
        }
    }

    /**
     * Share a video post with a caption to YouTube.
     *
     * @param string $caption The caption to accompany the video.
     * @param string $video_url The URL of the video.
     * @return array Response from the YouTube API.
     * @throws SocialMediaException
     */
    public function shareVideo(string $caption, string $video_url): array
    {
        $this->validateInput($caption, $video_url);
        
        try {
            // Step 1: Upload video metadata
            $metadata = [
                'snippet' => [
                    'title' => $this->extractTitleFromCaption($caption),
                    'description' => $caption,
                    'tags' => $this->extractTagsFromCaption($caption),
                    'categoryId' => '22' // People & Blogs category
                ],
                'status' => [
                    'privacyStatus' => 'public'
                ]
            ];

            // Step 2: Upload video — accepts a remote URL (downloaded securely via
            // SafeMediaFetcher) or a local file path. Local paths bypass the fetcher
            // and are used in tests / when the caller has already validated the file.
            if (filter_var($video_url, FILTER_VALIDATE_URL)) {
                $tempFile = $this->downloadFile($video_url);
                $isTemp   = true;
            } else {
                $tempFile = $video_url;
                $isTemp   = false;
            }

            try {
                $videoContent = file_get_contents($tempFile);
                if ($videoContent === false) {
                    throw new SocialMediaException('Failed to read video file: ' . $tempFile);
                }

                $uploadUrl = $this->buildApiUrl('videos');
                $response  = $this->uploadVideo($uploadUrl, $metadata, $videoContent);

                Log::info('YouTube video post shared successfully', ['video_id' => $response['id'] ?? null]);
                return $response;
            } finally {
                if ($isTemp && file_exists($tempFile)) {
                    @unlink($tempFile);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to share video to YouTube', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to share video to YouTube: ' . $e->getMessage());
        }
    }

    /**
     * Create a community post on YouTube.
     *
     * @param string $text The text content.
     * @param string $url The URL to share.
     * @param string $type The type of content (text, image, etc.).
     * @return array Response from the YouTube API.
     * @throws SocialMediaException
     */
    public function createCommunityPost(string $text, string $url, string $type = 'text'): array
    {
        try {
            $postUrl = $this->buildApiUrl('activities');
            $params = [
                'part' => 'snippet',
                'snippet' => [
                    'channelId' => $this->channel_id,
                    'type' => 'post',
                    'contentDetails' => [
                        'communityPost' => [
                            'text' => $text . ' ' . $url
                        ]
                    ]
                ]
            ];

            $response = $this->sendRequest($postUrl, 'post', $params);
            Log::info('YouTube community post created successfully', ['post_id' => $response['id'] ?? null]);
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to create YouTube community post', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to create YouTube community post: ' . $e->getMessage());
        }
    }

    /**
     * Upload a video to YouTube.
     *
     * @param string $uploadUrl The YouTube upload URL.
     * @param array $metadata The video metadata.
     * @param string $videoContent The video content.
     * @return array Response from the YouTube API.
     * @throws SocialMediaException
     */
    private function uploadVideo(string $uploadUrl, array $metadata, string $videoContent): array
    {
        $boundary = uniqid();
        $delimiter = '-------------' . $boundary;
        
        $postData = '';
        $postData .= "--" . $delimiter . "\r\n";
        $postData .= 'Content-Disposition: form-data; name="metadata"' . "\r\n";
        $postData .= 'Content-Type: application/json; charset=UTF-8' . "\r\n";
        $postData .= "\r\n";
        $postData .= json_encode($metadata) . "\r\n";
        $postData .= "--" . $delimiter . "\r\n";
        $postData .= 'Content-Disposition: form-data; name="video"; filename="video.mp4"' . "\r\n";
        $postData .= 'Content-Type: video/mp4' . "\r\n";
        $postData .= "\r\n";
        $postData .= $videoContent . "\r\n";
        $postData .= "--" . $delimiter . "--\r\n";

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->access_token,
            'Content-Type' => 'multipart/related; boundary=' . $delimiter,
            'Content-Length' => strlen($postData)
        ])->post($uploadUrl, $postData);

        if (!$response->successful()) {
            $errorData = $response->json();
            $errorMessage = $errorData['error']['message'] ?? 'Unknown error occurred';
            throw new SocialMediaException("YouTube API error: {$errorMessage}");
        }

        return $response->json();
    }

    /**
     * Get channel information.
     *
     * @return array Response from the YouTube API.
     * @throws SocialMediaException
     */
    public function getChannelInfo(): array
    {
        try {
            $url = $this->buildApiUrl('channels');
            $params = [
                'part' => 'snippet,statistics,contentDetails',
                'id' => $this->channel_id,
                'key' => $this->api_key
            ];

            return $this->sendRequest($url, 'get', $params);
        } catch (\Exception $e) {
            Log::error('Failed to get YouTube channel info', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to get YouTube channel info: ' . $e->getMessage());
        }
    }

    /**
     * Get channel's videos.
     *
     * @param int $maxResults Maximum number of videos to retrieve.
     * @return array Response from the YouTube API.
     * @throws SocialMediaException
     */
    public function getChannelVideos(int $maxResults = 25): array
    {
        try {
            $url = $this->buildApiUrl('search');
            $params = [
                'part' => 'snippet',
                'channelId' => $this->channel_id,
                'type' => 'video',
                'order' => 'date',
                'maxResults' => min($maxResults, 50),
                'key' => $this->api_key
            ];

            return $this->sendRequest($url, 'get', $params);
        } catch (\Exception $e) {
            Log::error('Failed to get YouTube channel videos', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to get YouTube channel videos: ' . $e->getMessage());
        }
    }

    /**
     * Get video analytics.
     *
     * @param string $videoId The YouTube video ID.
     * @return array Response from the YouTube API.
     * @throws SocialMediaException
     */
    public function getVideoAnalytics(string $videoId): array
    {
        try {
            $url = $this->buildApiUrl('videos');
            $params = [
                'part' => 'statistics,snippet',
                'id' => $videoId,
                'key' => $this->api_key
            ];

            return $this->sendRequest($url, 'get', $params);
        } catch (\Exception $e) {
            Log::error('Failed to get YouTube video analytics', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to get YouTube video analytics: ' . $e->getMessage());
        }
    }

    /**
     * Extract title from caption.
     *
     * @param string $caption The caption text.
     * @return string The extracted title.
     */
    private function extractTitleFromCaption(string $caption): string
    {
        $lines = explode("\n", $caption);
        $title = trim($lines[0]);
        
        // Limit title to 100 characters (YouTube limit)
        if (strlen($title) > 100) {
            $title = substr($title, 0, 97) . '...';
        }
        
        return $title ?: 'Shared Video';
    }

    /**
     * Extract tags from caption.
     *
     * @param string $caption The caption text.
     * @return array Array of tags.
     */
    private function extractTagsFromCaption(string $caption): array
    {
        // Simple tag extraction - look for words starting with #
        preg_match_all('/#(\w+)/', $caption, $matches);
        $tags = $matches[1] ?? [];
        
        // Limit to 15 tags (YouTube limit)
        return array_slice($tags, 0, 15);
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
     * Build YouTube API URL.
     *
     * @param string $endpoint The API endpoint.
     * @return string Complete API URL.
     */
    private function buildApiUrl(string $endpoint): string
    {
        return self::API_BASE_URL . '/' . $endpoint;
    }

    /**
     * Send authenticated request to YouTube API.
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
            'Authorization' => 'Bearer ' . $this->access_token,
            'Content-Type' => 'application/json'
        ];
        
        $headers = array_merge($defaultHeaders, $headers);

        $response = \Illuminate\Support\Facades\Http::withHeaders($headers)
            ->{$method}($url, $params);

        if (!$response->successful()) {
            $errorData = $response->json();
            $errorMessage = $errorData['error']['message'] ?? 'Unknown error occurred';
            throw new SocialMediaException("YouTube API error: {$errorMessage}");
        }

        return $response->json();
    }
}
