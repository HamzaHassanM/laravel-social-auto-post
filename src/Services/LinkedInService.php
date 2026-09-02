<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Services;

use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareImagePostInterface;
use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareInterface;
use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareVideoPostInterface;
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;
use Illuminate\Support\Facades\Log;

/**
 * Class LinkedInService
 *
 * Service for managing and posting content to LinkedIn using the LinkedIn API.
 *
 * Implements sharing of general posts, images, and videos to LinkedIn.
 */
class LinkedInService extends SocialMediaService implements ShareInterface, ShareImagePostInterface, ShareVideoPostInterface
{
    /**
     * @var string LinkedIn Access Token
     */
    private $access_token;

    /**
     * @var string LinkedIn Person URN
     */
    private $person_urn;

    /**
     * @var string LinkedIn Organization URN
     */
    private $organization_urn;

    /**
     * @var LinkedInService|null Singleton instance
     */
    private static ?LinkedInService $instance = null;

    /**
     * LinkedIn API base URL
     */
    private const API_BASE_URL = 'https://api.linkedin.com/v2';

    /**
     * Private constructor to prevent direct instantiation.
     */
    public function __construct(
        string $accessToken,
        string $personUrn,
        string $organizationUrn = null
    ) {
        $this->access_token = $accessToken;
        $this->person_urn = $personUrn;
        $this->organization_urn = $organizationUrn;
    }

    
    /**
     * Create a new instance dynamically with custom credentials.
     */
    public static function withCredentials(
        string $accessToken,
        string $personUrn,
        string $organizationUrn = null
    ): self 
    {
        return new self($accessToken, $personUrn, $organizationUrn);
    }

    /**
     * Get the singleton instance of LinkedInService.
     */
    public static function getInstance(): LinkedInService
    {
        if (self::$instance === null) {
            $accessToken = \HamzaHassanM\LaravelSocialAutoPost\Utils\ConfigHelper::get('autopost.linkedin_access_token');
            $personUrn = \HamzaHassanM\LaravelSocialAutoPost\Utils\ConfigHelper::get('autopost.linkedin_person_urn');
            $organizationUrn = \HamzaHassanM\LaravelSocialAutoPost\Utils\ConfigHelper::get('autopost.linkedin_organization_urn');

            if (!$accessToken || !$personUrn) {
                throw new SocialMediaException('LinkedIn credentials are not properly configured.');
            }

            self::$instance = new self($accessToken, $personUrn, $organizationUrn);
        }
        return self::$instance;
    }

    /**
     * Share a text post with a URL to LinkedIn.
     *
     * @param string $caption The text content of the post.
     * @param string $url The URL to share.
     * @return array Response from the LinkedIn API.
     * @throws SocialMediaException
     */
    public function share(string $caption, string $url): array
    {
        $this->validateInput($caption, $url);
        
        try {
            $url_endpoint = $this->buildApiUrl('ugcPosts');
            $params = [
                'author' => $this->person_urn,
                'lifecycleState' => 'PUBLISHED',
                'specificContent' => [
                    'com.linkedin.ugc.ShareContent' => [
                        'shareCommentary' => [
                            'text' => $caption
                        ],
                        'shareMediaCategory' => 'ARTICLE',
                        'media' => [
                            [
                                'status' => 'READY',
                                'description' => [
                                    'text' => $caption
                                ],
                                'media' => $url,
                                'title' => [
                                    'text' => $this->extractTitleFromUrl($url)
                                ]
                            ]
                        ]
                    ]
                ],
                'visibility' => [
                    'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC'
                ]
            ];

            $response = $this->sendRequest($url_endpoint, 'post', $params);
            Log::info('LinkedIn post shared successfully', ['post_id' => $response['id'] ?? null]);
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to share to LinkedIn', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to share to LinkedIn: ' . $e->getMessage());
        }
    }

    /**
     * Share an image post with a caption to LinkedIn.
     *
     * @param string $caption The caption to accompany the image.
     * @param string $image_url The URL of the image.
     * @return array Response from the LinkedIn API.
     * @throws SocialMediaException
     */
    public function shareImage(string $caption, string $image_url): array
    {
        $this->validateInput($caption, $image_url);
        
        try {
            // Step 1: Upload image
            $imageUrn = $this->uploadImage($image_url);
            
            // Step 2: Create post with image
            $url = $this->buildApiUrl('ugcPosts');
            $params = [
                'author' => $this->person_urn,
                'lifecycleState' => 'PUBLISHED',
                'specificContent' => [
                    'com.linkedin.ugc.ShareContent' => [
                        'shareCommentary' => [
                            'text' => $caption
                        ],
                        'shareMediaCategory' => 'IMAGE',
                        'media' => [
                            [
                                'status' => 'READY',
                                'description' => [
                                    'text' => $caption
                                ],
                                'media' => $imageUrn,
                                'title' => [
                                    'text' => 'Image Post'
                                ]
                            ]
                        ]
                    ]
                ],
                'visibility' => [
                    'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC'
                ]
            ];

            $response = $this->sendRequest($url, 'post', $params);
            Log::info('LinkedIn image post shared successfully', ['post_id' => $response['id'] ?? null]);
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to share image to LinkedIn', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to share image to LinkedIn: ' . $e->getMessage());
        }
    }

    /**
     * Share a video post with a caption to LinkedIn.
     *
     * @param string $caption The caption to accompany the video.
     * @param string $video_url The URL of the video.
     * @return array Response from the LinkedIn API.
     * @throws SocialMediaException
     */
    public function shareVideo(string $caption, string $video_url): array
    {
        $this->validateInput($caption, $video_url);
        
        try {
            // Step 1: Upload video
            $videoUrn = $this->uploadVideo($video_url);
            
            // Step 2: Create post with video
            $url = $this->buildApiUrl('ugcPosts');
            $params = [
                'author' => $this->person_urn,
                'lifecycleState' => 'PUBLISHED',
                'specificContent' => [
                    'com.linkedin.ugc.ShareContent' => [
                        'shareCommentary' => [
                            'text' => $caption
                        ],
                        'shareMediaCategory' => 'VIDEO',
                        'media' => [
                            [
                                'status' => 'READY',
                                'description' => [
                                    'text' => $caption
                                ],
                                'media' => $videoUrn,
                                'title' => [
                                    'text' => 'Video Post'
                                ]
                            ]
                        ]
                    ]
                ],
                'visibility' => [
                    'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC'
                ]
            ];

            $response = $this->sendRequest($url, 'post', $params);
            Log::info('LinkedIn video post shared successfully', ['post_id' => $response['id'] ?? null]);
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to share video to LinkedIn', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to share video to LinkedIn: ' . $e->getMessage());
        }
    }

    /**
     * Share to LinkedIn Company Page.
     *
     * @param string $caption The text content of the post.
     * @param string $url The URL to share.
     * @return array Response from the LinkedIn API.
     * @throws SocialMediaException
     */
    public function shareToCompanyPage(string $caption, string $url): array
    {
        if (!$this->organization_urn) {
            throw new SocialMediaException('Organization URN not configured for company page posting.');
        }

        $this->validateInput($caption, $url);
        
        try {
            $url_endpoint = $this->buildApiUrl('ugcPosts');
            $params = [
                'author' => $this->organization_urn,
                'lifecycleState' => 'PUBLISHED',
                'specificContent' => [
                    'com.linkedin.ugc.ShareContent' => [
                        'shareCommentary' => [
                            'text' => $caption
                        ],
                        'shareMediaCategory' => 'ARTICLE',
                        'media' => [
                            [
                                'status' => 'READY',
                                'description' => [
                                    'text' => $caption
                                ],
                                'media' => $url,
                                'title' => [
                                    'text' => $this->extractTitleFromUrl($url)
                                ]
                            ]
                        ]
                    ]
                ],
                'visibility' => [
                    'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC'
                ]
            ];

            $response = $this->sendRequest($url_endpoint, 'post', $params);
            Log::info('LinkedIn company page post shared successfully', ['post_id' => $response['id'] ?? null]);
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to share to LinkedIn company page', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to share to LinkedIn company page: ' . $e->getMessage());
        }
    }

    /**
     * Get user profile information.
     *
     * @return array Response from the LinkedIn API.
     * @throws SocialMediaException
     */
    public function getUserInfo(): array
    {
        try {
            $url = $this->buildApiUrl('people/~');
            $params = [
                'projection' => '(id,firstName,lastName,profilePicture(displayImage~:playableStreams))'
            ];

            return $this->sendRequest($url, 'get', $params);
        } catch (\Exception $e) {
            Log::error('Failed to get LinkedIn user info', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to get LinkedIn user info: ' . $e->getMessage());
        }
    }

    /**
     * Upload image to LinkedIn.
     *
     * @param string $imageUrl The URL of the image to upload.
     * @return string The image URN.
     * @throws SocialMediaException
     */
    private function uploadImage(string $imageUrl): string
    {
        // Step 1: Register upload
        $registerUrl = $this->buildApiUrl('assets?action=registerUpload');
        $registerParams = [
            'registerUploadRequest' => [
                'recipes' => ['urn:li:digitalmediaRecipe:feedshare-image'],
                'owner' => $this->person_urn,
                'serviceRelationships' => [
                    [
                        'relationshipType' => 'OWNER',
                        'identifier' => 'urn:li:userGeneratedContent'
                    ]
                ]
            ]
        ];

        $registerResponse = $this->sendRequest($registerUrl, 'post', $registerParams);
        $uploadUrl = $registerResponse['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'];
        $asset = $registerResponse['value']['asset'];

        // Step 2: Upload image
        $tempFile = \HamzaHassanM\LaravelSocialAutoPost\Utils\SafeMediaFetcher::fetch($imageUrl);

        try {
            $imageContent = file_get_contents($tempFile);
            if ($imageContent === false) {
                throw new SocialMediaException('Failed to read downloaded image from temp file');
            }

            $uploadResponse = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->access_token
            ])->put($uploadUrl, $imageContent);

            if (!$uploadResponse->successful()) {
                throw new SocialMediaException('Failed to upload image to LinkedIn');
            }
        } finally {
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }
        }

        return $asset;
    }

    /**
     * Upload video to LinkedIn.
     *
     * @param string $videoUrl The URL of the video to upload.
     * @return string The video URN.
     * @throws SocialMediaException
     */
    private function uploadVideo(string $videoUrl): string
    {
        // Step 1: Register upload
        $registerUrl = $this->buildApiUrl('assets?action=registerUpload');
        $registerParams = [
            'registerUploadRequest' => [
                'recipes' => ['urn:li:digitalmediaRecipe:feedshare-video'],
                'owner' => $this->person_urn,
                'serviceRelationships' => [
                    [
                        'relationshipType' => 'OWNER',
                        'identifier' => 'urn:li:userGeneratedContent'
                    ]
                ]
            ]
        ];

        $registerResponse = $this->sendRequest($registerUrl, 'post', $registerParams);
        $uploadUrl = $registerResponse['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'];
        $asset = $registerResponse['value']['asset'];

        // Step 2: Upload video
        $tempFile = \HamzaHassanM\LaravelSocialAutoPost\Utils\SafeMediaFetcher::fetch($videoUrl);

        try {
            $videoContent = file_get_contents($tempFile);
            if ($videoContent === false) {
                throw new SocialMediaException('Failed to read downloaded video from temp file');
            }

            $uploadResponse = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->access_token
            ])->put($uploadUrl, $videoContent);

            if (!$uploadResponse->successful()) {
                throw new SocialMediaException('Failed to upload video to LinkedIn');
            }
        } finally {
            if (file_exists($tempFile)) {
                @unlink($tempFile);
            }
        }

        return $asset;
    }

    /**
     * Extract title from URL.
     *
     * @param string $url The URL to extract title from.
     * @return string The extracted title.
     */
    private function extractTitleFromUrl(string $url): string
    {
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'] ?? 'Link';
        return 'Shared from ' . $host;
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
     * Build LinkedIn API URL.
     *
     * @param string $endpoint The API endpoint.
     * @return string Complete API URL.
     */
    private function buildApiUrl(string $endpoint): string
    {
        return self::API_BASE_URL . '/' . $endpoint;
    }

    /**
     * Send authenticated request to LinkedIn API.
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
            'Content-Type' => 'application/json',
            'X-Restli-Protocol-Version' => '2.0.0'
        ];
        
        $headers = array_merge($defaultHeaders, $headers);

        $response = \Illuminate\Support\Facades\Http::withHeaders($headers)
            ->{$method}($url, $params);

        if (!$response->successful()) {
            $errorMessage = $response->json()['message'] ?? 'Unknown error occurred';
            throw new SocialMediaException("LinkedIn API error: {$errorMessage}");
        }

        return $response->json();
    }
}
