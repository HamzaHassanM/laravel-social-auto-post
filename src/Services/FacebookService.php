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
    private static $instance;

    /**
     * Private constructor to prevent direct instantiation.
     */

    private function __construct() {
        $this->access_token = config('autopost.facebook_access_token');
        $this->page_id = config('autopost.facebook_page_id');
    }

    /**
     * Get the singleton instance of FacebookService.
     *
     * @return FacebookService
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
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
        $url = 'https://graph.facebook.com/v20.0/' . $this->page_id . '/photos';
        $params = [
            'url'          => $image_url,
            'caption'      => $caption,
            'access_token' => $this->access_token,
        ];
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
        $feedUrl = 'https://graph.facebook.com/v20.0/' . $this->page_id . '/feed';
        $params = [
            'message'      => $caption,
            'link'         => $url,
            'access_token' => $this->access_token,
        ];

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
        $url = 'https://graph.facebook.com/v20.0/' . $this->page_id . '/videos';
        $params = [
            'description'  => $caption,
            'source'       => $video_url,
            'access_token' => $this->access_token,
        ];

        return $this->sendRequest($url, 'post', $params);
    }

    /**
     * Retrieve insights for the Facebook page.
     *
     * @return mixed Response from the Facebook API.
     */
    public function getPageInsights(array $metrics = [], array $additionalParams = []): array {
        $url = 'https://graph.facebook.com/v20.0/' . $this->page_id . '/insights';

        // Default parameters
        $params = [
            'metric'       => implode(',', $metrics), // Join metrics with commas
            'access_token' => $this->access_token,
        ];

        // Merge with additional parameters
        $params = array_merge($params, $additionalParams);

        return $this->sendRequest($url, 'get', $params);
    }


    /**
     * Retrieve information about the Facebook page.
     *
     * @return mixed Response from the Facebook API.
     */
    public function getPageInfo() {
        $url = 'https://graph.facebook.com/v20.0/' . $this->page_id;
        $params = [
            'access_token' => $this->access_token,
        ];

        return $this->sendRequest($url, 'get', $params);
    }
}