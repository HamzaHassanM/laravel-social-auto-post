<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Services;

use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareImagePostInterface;
use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareInterface;
use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareVideoPostInterface;

/**
 * Class FacebookService
 *
 * Service for managing and posting content to Facebook using the Graph API.
 *
 * Implements sharing of general posts, images, and videos to a Facebook page.
 */
class FacebookService extends SocialMediaService implements ShareInterface, ShareImagePostInterface, ShareVideoPostInterface {

    /**
     * @var string Facebook access token
     */
    private $access_token;

    /**
     * @var string Facebook page ID
     */
    private $page_id;

    /**
     * @var FacebookService|null Singleton instance
     */
    private static ?FacebookService $instance = null;
    /**
     * Facebook API version
     */
    private const API_VERSION = 'v20.0';


    /**
     * Private constructor to prevent direct instantiation.
     */

    private function __construct(string $accessToken, string $pageId) {
        $this->access_token = $accessToken;
        $this->page_id = $pageId;
    }

    /**
     * Get the singleton instance of FacebookService.
     *
     * @return FacebookService
     */
    public static function getInstance() {
        if (self::$instance === null) {
            $accessToken = config('autopost.facebook_access_token');
            $pageId = config('autopost.facebook_page_id');
            self::$instance = new self($accessToken, $pageId);
        }
        return self::$instance;
    }

    /**
     * Share an image post with a caption and an image URL to Facebook.
     *
     * @param string $caption The caption to accompany the image.
     * @param string $image_url The URL of the image.
     *
     * @return mixed Response from the Facebook API.
     */
    public function shareImage($caption, $image_url) {
        $url = $this->buildApiUrl('photos');
        $params = $this->buildParams([
            'url'     => $image_url,
            'caption' => $caption,
        ]);

        return $this->sendRequest($url, 'post', $params);
    }

    /**
     * Share a text post with a caption and a URL to Facebook.
     *
     * @param string $caption The caption to accompany the post.
     * @param string $url The URL to share.
     *
     * @return mixed Response from the Facebook API.
     */

    public function share($caption, $url) {
        $feedUrl = $this->buildApiUrl('feed');
        $params = $this->buildParams([
            'message' => $caption,
            'link'    => $url,
        ]);

        return $this->sendRequest($feedUrl, 'post', $params);
    }

    /**
     * Share a video post with a caption and a video URL to Facebook.
     *
     * @param string $caption The caption to accompany the video.
     * @param string $video_url The URL of the video.
     *
     * @return mixed Response from the Facebook API.
     */
    public function shareVideo($caption, $video_url) {
        $url = $this->buildApiUrl('videos');
        $params = $this->buildParams([
            'description' => $caption,
            'source'      => $video_url,
        ]);

        return $this->sendRequest($url, 'post', $params);
    }

    /**
     * Retrieve insights for the Facebook page.
     *
     * @return mixed Response from the Facebook API.
     */
    public function getPageInsights(array $metrics = [], array $additionalParams = []): array {
        $url = $this->buildApiUrl('insights');
        $params = $this->buildParams(array_merge([
            'metric' => implode(',', $metrics),
        ], $additionalParams));

        return $this->sendRequest($url, 'get', $params);
    }


    /**
     * Retrieve information about the Facebook page.
     *
     * @return mixed Response from the Facebook API.
     */
    public function getPageInfo() {
        $url = $this->buildApiUrl();
        $params = $this->buildParams();

        return $this->sendRequest($url, 'get', $params);
    }


    /**
     * Helper to build Facebook Graph API URL.
     *
     * @param string $endpoint
     *
     * @return string
     */
    private function buildApiUrl(string $endpoint = ''): string {
        $apiVersion = config('autopost.facebook_api_version');
        return 'https://graph.facebook.com/' . $apiVersion . '/' . $this->page_id . '/' . $endpoint;
    }

    /**
     * Helper to build request parameters.
     *
     * @param array $params
     *
     * @return array
     */
    private function buildParams(array $params = []): array {
        return array_merge($params, ['access_token' => $this->access_token]);
    }
}