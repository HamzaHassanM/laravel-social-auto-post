<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Services;

use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareImagePostInterface;
use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareInterface;
use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareVideoPostInterface;
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;
use Illuminate\Support\Facades\Log;

/**
 * Class InstagramService
 *
 * Service for managing and posting content to Instagram using the Instagram Basic Display API and Instagram Graph API.
 *
 * Implements sharing of images and videos to Instagram.
 */
class InstagramService extends SocialMediaService implements ShareInterface, ShareImagePostInterface, ShareVideoPostInterface
{
    /**
     * @var string Instagram Access Token
     */
    private $access_token;

    /**
     * @var string Instagram Business Account ID
     */
    private $instagram_account_id;

    /**
     * @var string Facebook Page ID (required for Instagram Business API)
     */
    private $facebook_page_id;

    /**
     * @var InstagramService|null Singleton instance
     */
    private static ?InstagramService $instance = null;

    /**
     * Instagram API base URL
     */
    private const API_BASE_URL = 'https://graph.facebook.com/v20.0';

    /**
     * Private constructor to prevent direct instantiation.
     */
    public function __construct(
        string $accessToken,
        string $instagramAccountId,
        string $facebookPageId
    ) {
        $this->access_token = $accessToken;
        $this->instagram_account_id = $instagramAccountId;
        $this->facebook_page_id = $facebookPageId;
    }

    
    /**
     * Create a new instance dynamically with custom credentials.
     */
    public static function withCredentials(
        string $accessToken,
        string $instagramAccountId,
        string $facebookPageId
    ): self 
    {
        return new self($accessToken, $instagramAccountId, $facebookPageId);
    }

    /**
     * Get the singleton instance of InstagramService.
     */
    public static function getInstance(): InstagramService
    {
        if (self::$instance === null) {
            $accessToken = \HamzaHassanM\LaravelSocialAutoPost\Utils\ConfigHelper::get('autopost.instagram_access_token');
            $instagramAccountId = \HamzaHassanM\LaravelSocialAutoPost\Utils\ConfigHelper::get('autopost.instagram_account_id');
            $facebookPageId = \HamzaHassanM\LaravelSocialAutoPost\Utils\ConfigHelper::get('autopost.facebook_page_id');

            if (!$accessToken || !$instagramAccountId || !$facebookPageId) {
                throw new SocialMediaException('Instagram credentials are not properly configured.');
            }

            self::$instance = new self($accessToken, $instagramAccountId, $facebookPageId);
        }
        return self::$instance;
    }

    /**
     * Share a text post with a URL to Instagram (as a story or feed post).
     * Note: Instagram doesn't support direct URL sharing in feed posts, so this creates a story.
     *
     * @param string $caption The text content of the post.
     * @param string $url The URL to share.
     * @return array Response from the Instagram API.
     * @throws SocialMediaException
     */
    public function share(string $caption, string $url): array
    {
        $this->validateInput($caption, $url);
        
        try {
            // Instagram doesn't support direct URL sharing in feed posts
            // We'll create a story with the URL
            return $this->shareStory($caption, $url);
        } catch (\Exception $e) {
            Log::error('Failed to share to Instagram', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to share to Instagram: ' . $e->getMessage());
        }
    }

    /**
     * Share an image post with a caption to Instagram.
     *
     * @param string $caption The caption to accompany the image.
     * @param string $image_url The URL of the image.
     * @return array Response from the Instagram API.
     * @throws SocialMediaException
     */
    public function shareImage(string $caption, string $image_url): array
    {
        $this->validateInput($caption, $image_url);
        
        try {
            // Step 1: Create media container
            $containerUrl = $this->buildApiUrl($this->instagram_account_id . '/media');
            $containerParams = [
                'image_url' => $image_url,
                'caption' => $caption,
                'access_token' => $this->access_token
            ];

            $containerResponse = $this->sendRequest($containerUrl, 'post', $containerParams);
            $containerId = $containerResponse['id'];

            // Step 2: Publish the media
            $publishUrl = $this->buildApiUrl($this->instagram_account_id . '/media_publish');
            $publishParams = [
                'creation_id' => $containerId,
                'access_token' => $this->access_token
            ];

            $response = $this->sendRequest($publishUrl, 'post', $publishParams);
            Log::info('Instagram image post shared successfully', ['media_id' => $response['id'] ?? null]);
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to share image to Instagram', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to share image to Instagram: ' . $e->getMessage());
        }
    }

    /**
     * Share a video post with a caption to Instagram.
     *
     * @param string $caption The caption to accompany the video.
     * @param string $video_url The URL of the video.
     * @return array Response from the Instagram API.
     * @throws SocialMediaException
     */
    public function shareVideo(string $caption, string $video_url): array
    {
        $this->validateInput($caption, $video_url);
        
        try {
            // Step 1: Create media container
            $containerUrl = $this->buildApiUrl($this->instagram_account_id . '/media');
            $containerParams = [
                'media_type' => 'VIDEO',
                'video_url' => $video_url,
                'caption' => $caption,
                'access_token' => $this->access_token
            ];

            $containerResponse = $this->sendRequest($containerUrl, 'post', $containerParams);
            $containerId = $containerResponse['id'];

            // Step 2: Publish the media
            $publishUrl = $this->buildApiUrl($this->instagram_account_id . '/media_publish');
            $publishParams = [
                'creation_id' => $containerId,
                'access_token' => $this->access_token
            ];

            $response = $this->sendRequest($publishUrl, 'post', $publishParams);
            Log::info('Instagram video post shared successfully', ['media_id' => $response['id'] ?? null]);
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to share video to Instagram', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to share video to Instagram: ' . $e->getMessage());
        }
    }

    /**
     * Share a story with text and URL.
     *
     * @param string $caption The text content.
     * @param string $url The URL to share.
     * @return array Response from the Instagram API.
     * @throws SocialMediaException
     */
    public function shareStory(string $caption, string $url): array
    {
        $this->validateInput($caption, $url);
        
        try {
            // Create a story with text overlay
            $storyUrl = $this->buildApiUrl($this->instagram_account_id . '/media');
            $storyParams = [
                'media_type' => 'STORIES',
                'image_url' => $this->createTextImage($caption, $url),
                'access_token' => $this->access_token
            ];

            $response = $this->sendRequest($storyUrl, 'post', $storyParams);
            Log::info('Instagram story shared successfully', ['media_id' => $response['id'] ?? null]);
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to share story to Instagram', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to share story to Instagram: ' . $e->getMessage());
        }
    }

    /**
     * Share a carousel post with multiple images.
     *
     * @param string $caption The caption for the carousel.
     * @param array $image_urls Array of image URLs.
     * @return array Response from the Instagram API.
     * @throws SocialMediaException
     */
    public function shareCarousel(string $caption, array $image_urls): array
    {
        if (empty($image_urls) || count($image_urls) < 2 || count($image_urls) > 10) {
            throw new SocialMediaException('Carousel must contain between 2 and 10 images.');
        }

        try {
            $children = [];

            // Step 1: Create media containers for each image
            foreach ($image_urls as $image_url) {
                $containerUrl = $this->buildApiUrl($this->instagram_account_id . '/media');
                $containerParams = [
                    'image_url' => $image_url,
                    'is_carousel_item' => true,
                    'access_token' => $this->access_token
                ];

                $containerResponse = $this->sendRequest($containerUrl, 'post', $containerParams);
                $children[] = $containerResponse['id'];
            }

            // Step 2: Create carousel container
            $carouselUrl = $this->buildApiUrl($this->instagram_account_id . '/media');
            $carouselParams = [
                'media_type' => 'CAROUSEL',
                'children' => implode(',', $children),
                'caption' => $caption,
                'access_token' => $this->access_token
            ];

            $carouselResponse = $this->sendRequest($carouselUrl, 'post', $carouselParams);
            $carouselId = $carouselResponse['id'];

            // Step 3: Publish the carousel
            $publishUrl = $this->buildApiUrl($this->instagram_account_id . '/media_publish');
            $publishParams = [
                'creation_id' => $carouselId,
                'access_token' => $this->access_token
            ];

            $response = $this->sendRequest($publishUrl, 'post', $publishParams);
            Log::info('Instagram carousel post shared successfully', ['media_id' => $response['id'] ?? null]);
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to share carousel to Instagram', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to share carousel to Instagram: ' . $e->getMessage());
        }
    }

    /**
     * Get Instagram account information.
     *
     * @return array Response from the Instagram API.
     * @throws SocialMediaException
     */
    public function getAccountInfo(): array
    {
        try {
            $url = $this->buildApiUrl($this->instagram_account_id);
            $params = [
                'fields' => 'id,username,account_type,media_count,followers_count,follows_count',
                'access_token' => $this->access_token
            ];

            return $this->sendRequest($url, 'get', $params);
        } catch (\Exception $e) {
            Log::error('Failed to get Instagram account info', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to get Instagram account info: ' . $e->getMessage());
        }
    }

    /**
     * Get recent media from Instagram account.
     *
     * @param int $limit Number of media items to retrieve.
     * @return array Response from the Instagram API.
     * @throws SocialMediaException
     */
    public function getRecentMedia(int $limit = 25): array
    {
        try {
            $url = $this->buildApiUrl($this->instagram_account_id . '/media');
            $params = [
                'fields' => 'id,caption,media_type,media_url,thumbnail_url,permalink,timestamp',
                'limit' => min($limit, 25),
                'access_token' => $this->access_token
            ];

            return $this->sendRequest($url, 'get', $params);
        } catch (\Exception $e) {
            Log::error('Failed to get Instagram recent media', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to get Instagram recent media: ' . $e->getMessage());
        }
    }

    /**
     * Create a text image for stories.
     *
     * @param string $text The text to display.
     * @param string $url The URL to include.
     * @return string The URL of the generated image.
     * @throws SocialMediaException
     */
    private function createTextImage(string $text, string $url): string
    {
        // This is a simplified implementation
        // In a real scenario, you might want to use a service like Canva API or generate images programmatically
        $imageText = $text . "\n\n" . $url;
        
        // For now, return a placeholder image URL
        // In production, you should generate an actual image with the text
        return 'https://via.placeholder.com/1080x1920/000000/FFFFFF?text=' . urlencode($imageText);
    }

    /**
     * Validate input parameters.
     *
     * @param string $caption The caption text.
     * @param string $url The URL.
     * @throws SocialMediaException
     */
    private function validateInput(string $caption, string $url): void
    {
        if (empty(trim($caption))) {
            throw new SocialMediaException('Caption cannot be empty.');
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new SocialMediaException('Invalid URL provided.');
        }
    }

    /**
     * Build Instagram API URL.
     *
     * @param string $endpoint The API endpoint.
     * @return string Complete API URL.
     */
    private function buildApiUrl(string $endpoint): string
    {
        return self::API_BASE_URL . '/' . $endpoint;
    }

    /**
     * Send authenticated request to Instagram API.
     *
     * @param string $url The API URL.
     * @param string $method The HTTP method.
     * @param array $params The request parameters.
     * @return array Response from the API.
     * @throws SocialMediaException
     */
    protected function sendRequest(string $url, string $method = 'post', array $params = [], array $headers = []): array
    {
        $response = \Illuminate\Support\Facades\Http::withHeaders($headers)->{$method}($url, $params);

        if (!$response->successful()) {
            $errorData = $response->json();
            $errorMessage = $errorData['error']['message'] ?? 'Unknown error occurred';
            throw new SocialMediaException("Instagram API error: {$errorMessage}");
        }

        return $response->json();
    }
}
