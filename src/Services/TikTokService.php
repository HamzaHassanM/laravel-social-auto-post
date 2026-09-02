<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Services;

use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareImagePostInterface;
use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareInterface;
use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareVideoPostInterface;
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class TikTokService
 *
 * Service for managing and posting content to TikTok using the TikTok Content Posting API v2.
 */
class TikTokService extends SocialMediaService implements ShareInterface, ShareImagePostInterface, ShareVideoPostInterface
{
    /**
     * @var string TikTok Access Token
     */
    private $access_token;

    /**
     * @var string TikTok Client Key
     */
    private $client_key;

    /**
     * @var string TikTok Client Secret
     */
    private $client_secret;

    /**
     * @var TikTokService|null Singleton instance
     */
    private static ?TikTokService $instance = null;

    /**
     * TikTok API base URL
     */
    private const API_BASE_URL = 'https://open.tiktokapis.com/v2';

    /**
     * Private constructor to prevent direct instantiation.
     */
    public function __construct(
        string $accessToken,
        string $clientKey,
        string $clientSecret
    ) {
        $this->access_token = $accessToken;
        $this->client_key = $clientKey;
        $this->client_secret = $clientSecret;
    }

    
    /**
     * Create a new instance dynamically with custom credentials.
     */
    public static function withCredentials(
        string $accessToken,
        string $clientKey,
        string $clientSecret
    ): self 
    {
        return new self($accessToken, $clientKey, $clientSecret);
    }

    /**
     * Get the singleton instance of TikTokService.
     */
    public static function getInstance(): TikTokService
    {
        if (self::$instance === null) {
            $accessToken = \HamzaHassanM\LaravelSocialAutoPost\Utils\ConfigHelper::get('autopost.tiktok_access_token');
            $clientKey = \HamzaHassanM\LaravelSocialAutoPost\Utils\ConfigHelper::get('autopost.tiktok_client_key');
            $clientSecret = \HamzaHassanM\LaravelSocialAutoPost\Utils\ConfigHelper::get('autopost.tiktok_client_secret');

            if (!$accessToken || !$clientKey || !$clientSecret) {
                throw new SocialMediaException('TikTok credentials are not properly configured.');
            }

            self::$instance = new self($accessToken, $clientKey, $clientSecret);
        }
        return self::$instance;
    }

    /**
     * TikTok does not support plain text posts.
     *
     * @param string $caption The text content of the post.
     * @param string $url The URL to share.
     * @return array
     * @throws SocialMediaException
     */
    public function share(string $caption, string $url): array
    {
        throw new SocialMediaException('TikTok does not support plain text posts. Use shareVideo() or shareImage() instead.');
    }

    /**
     * Post an image carousel to TikTok.
     * Uses Content Posting API with media_type = PHOTO and post_mode = MEDIA_UPLOAD.
     *
     * @param string $caption The caption to accompany the image.
     * @param string $image_url The URL of the image.
     * @return array Response from the TikTok API.
     * @throws SocialMediaException
     */
    public function shareImage(string $caption, string $image_url): array
    {
        $this->validateTextUrl($caption, $image_url);
        
        try {
            $url = $this->buildApiUrl('post/publish/content/init/');
            $params = [
                'post_info' => [
                    'title' => mb_substr($caption, 0, 2200),
                    'privacy_level' => 'PUBLIC_TO_EVERYONE',
                    'disable_comment' => false,
                ],
                'source_info' => [
                    'source' => 'PULL_FROM_URL',
                    'photo_images' => [$image_url],
                    'photo_cover_index' => 0,
                ],
                'post_mode' => 'DIRECT_POST',
                'media_type' => 'PHOTO',
            ];

            $response = $this->sendRequest($url, 'post', $params);
            Log::info('TikTok photo upload initialized', ['publish_id' => $response['data']['publish_id'] ?? null]);
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to share image to TikTok', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to share image to TikTok: ' . $e->getMessage());
        }
    }

    /**
     * Send a video to the user's TikTok inbox for publishing.
     *
     * @param string $caption The caption to accompany the video.
     * @param string $video_url The URL of the video.
     * @return array Response from the TikTok API.
     * @throws SocialMediaException
     */
    public function shareVideo(string $caption, string $video_url): array
    {
        $this->validateMediaInput($caption, $video_url);
        
        $video_path = filter_var($video_url, FILTER_VALIDATE_URL)
            ? \HamzaHassanM\LaravelSocialAutoPost\Utils\SafeMediaFetcher::fetch($video_url)
            : $video_url;

        try {
            if (!file_exists($video_path)) {
                throw new SocialMediaException('Failed to download video or file does not exist.');
            }

            // Check if we should use FILE_UPLOAD or PULL_FROM_URL. 
            // For general public URLs, PULL_FROM_URL requires a verified domain,
            // so using FILE_UPLOAD with chunking is generally safer for a package.
            $sourceInfo = $this->buildFileUploadSourceInfo($video_path);

            $url = $this->buildApiUrl('post/publish/video/init/');
            $params = [
                'post_info' => [
                    'title' => mb_substr($caption, 0, 2200),
                    'privacy_level' => 'PUBLIC_TO_EVERYONE',
                    'disable_comment' => false,
                    'disable_duet' => false,
                    'disable_stitch' => false,
                    'video_cover_timestamp_ms' => 1000,
                ],
                'source_info' => $sourceInfo,
            ];

            $initResponse = $this->sendRequest($url, 'post', $params);
            $publishId = $initResponse['data']['publish_id'] ?? null;
            $uploadUrl = $initResponse['data']['upload_url'] ?? null;

            if (!$uploadUrl) {
                throw new SocialMediaException('TikTok did not return an upload_url.');
            }

            $this->uploadVideoChunks($video_path, $uploadUrl);

            Log::info('TikTok video upload initialized', compact('publishId'));
            return $initResponse;
        } catch (\Exception $e) {
            Log::error('Failed to share video to TikTok', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to share video to TikTok: ' . $e->getMessage());
        } finally {
            if ($video_path !== $video_url && file_exists($video_path)) {
                @unlink($video_path);
            }
        }
    }

    /**
     * Get user profile information.
     *
     * @return array Response from the TikTok API.
     * @throws SocialMediaException
     */
    public function getUserInfo(): array
    {
        try {
            $url = $this->buildApiUrl('user/info/');
            $params = [
                'fields' => 'open_id,union_id,avatar_url,display_name,follower_count,following_count,likes_count,video_count'
            ];

            return $this->sendRequest($url, 'get', $params);
        } catch (\Exception $e) {
            Log::error('Failed to get TikTok user info', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to get TikTok user info: ' . $e->getMessage());
        }
    }

    /**
     * Get user's videos.
     *
     * @param int $max_count Maximum number of videos to retrieve.
     * @return array Response from the TikTok API.
     * @throws SocialMediaException
     */
    public function getUserVideos(int $max_count = 20): array
    {
        try {
            $url = $this->buildApiUrl('video/list/');
            $params = [
                'max_count' => min($max_count, 20),
                'fields' => 'id,title,cover_image_url,share_url,embed_url,create_time'
            ];

            return $this->sendRequest($url, 'get', $params);
        } catch (\Exception $e) {
            Log::error('Failed to get TikTok user videos', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to get TikTok user videos: ' . $e->getMessage());
        }
    }

    /**
     * Check publish status by publish_id
     *
     * @param string $publishId
     * @return array
     * @throws SocialMediaException
     */
    public function checkPublishStatus(string $publishId): array
    {
        $url = $this->buildApiUrl('post/publish/status/fetch/');
        return $this->sendRequest($url, 'post', ['publish_id' => $publishId]);
    }

    /**
     * Get Creator Info
     *
     * @return array
     * @throws SocialMediaException
     */
    public function queryCreatorInfo(): array
    {
        $url = $this->buildApiUrl('post/publish/creator_info/query/');
        return $this->sendRequest($url, 'post', []);
    }

    /**
     * Build source_info for the FILE_UPLOAD source type.
     * 
     * @return array<string, mixed>
     * @throws SocialMediaException
     */
    private function buildFileUploadSourceInfo(string $videoPath): array
    {
        $videoSize = filesize($videoPath);

        if ($videoSize === false || $videoSize === 0) {
            throw new SocialMediaException('Could not determine video file size.');
        }

        $chunkSize = 10 * 1024 * 1024; // 10 MB per chunk
        $totalChunks = (int) ceil($videoSize / $chunkSize);

        return [
            'source' => 'FILE_UPLOAD',
            'video_size' => $videoSize,
            'chunk_size' => $chunkSize,
            'total_chunk_count' => $totalChunks,
        ];
    }

    /**
     * Download a video and PUT it to TikTok's upload URL in chunks.
     * 
     * @throws SocialMediaException
     */
    private function uploadVideoChunks(string $videoPath, string $uploadUrl): void
    {
        $totalSize = filesize($videoPath);
        if ($totalSize === false) {
            throw new SocialMediaException("Failed to read video file size.");
        }

        $fileHandle = fopen($videoPath, 'rb');
        if ($fileHandle === false) {
            throw new SocialMediaException("Failed to open video file for reading.");
        }

        $chunkSize = 10 * 1024 * 1024;
        $offset = 0;
        $chunkIndex = 0;

        try {
            while ($offset < $totalSize) {
                $chunk = fread($fileHandle, $chunkSize);
                if ($chunk === false) {
                    throw new SocialMediaException("Failed to read chunk from video file.");
                }
                
                $chunkLength = strlen($chunk);
                $end = $offset + $chunkLength - 1;

                $response = Http::timeout(120)
                    ->withHeaders([
                        'Content-Type' => 'video/mp4',
                        'Content-Length' => $chunkLength,
                        'Content-Range' => "bytes {$offset}-{$end}/{$totalSize}",
                    ])
                    ->put($uploadUrl, $chunk);

                if (!$response->successful()) {
                    throw new SocialMediaException("Failed to upload chunk {$chunkIndex}: {$response->body()}");
                }

                $offset += $chunkLength;
                $chunkIndex++;
            }
        } finally {
            fclose($fileHandle);
        }
    }

    private function validateTextUrl(string $caption, string $url): void
    {
        if (empty(trim($caption))) {
            throw new SocialMediaException('Caption cannot be empty.');
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new SocialMediaException('Invalid URL provided.');
        }
    }

    /**
     * Validate the input for media uploads (accepts URL or local path).
     *
     * @param string $caption The caption.
     * @param string $urlOrPath The media URL or file path.
     * @throws SocialMediaException
     */
    private function validateMediaInput(string $caption, string $urlOrPath): void
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
     * Build TikTok API URL.
     *
     * @param string $endpoint The API endpoint.
     * @return string Complete API URL.
     */
    private function buildApiUrl(string $endpoint): string
    {
        return self::API_BASE_URL . '/' . ltrim($endpoint, '/');
    }

    /**
     * Send authenticated request to TikTok API.
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

        $response = Http::withHeaders($headers)
            ->{$method}($url, $params);

        $json = $response->json();
        $errorCode = $json['error']['code'] ?? 'ok';

        if (!$response->successful() || $errorCode !== 'ok') {
            $errorMessage = $json['error']['message'] ?? $response->body() ?? 'Unknown error occurred';
            throw new SocialMediaException("TikTok API error: {$errorMessage}");
        }

        return $json ?? [];
    }
}
