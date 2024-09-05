<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Services;

use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareImagePostInterface;
use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareInterface;
use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareVideoPostInterface;
use Illuminate\Support\Facades\Http;
use function HamzaHassanM\LaravelSocialAutoPost\config;

class FacebookService extends SocialMediaService implements ShareInterface, ShareImagePostInterface , ShareVideoPostInterface {

    private        $access_token;
    private        $page_id;
    private static $instance;

    private function __construct() {
        $this->access_token = config('autopost.facebook_access_token');
        $this->page_id = config('autopost.facebook_page_id');
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function shareImage($caption, $image_url) {
        $url = 'https://graph.facebook.com/v14.0/' . $this->page_id . '/photos';
        $params = [
            'url'          => $image_url,
            'caption'      => $caption,
            'access_token' => $this->access_token,
        ];
        return $this->sendRequest($url, 'post', $params);
    }

    public function share($caption, $url) {
        $feedUrl = 'https://graph.facebook.com/v14.0/' . $this->page_id . '/feed';
        $params = [
            'message'      => $caption,
            'link'         => $url,
            'access_token' => $this->access_token,
        ];

        return $this->sendRequest($feedUrl, 'post', $params);
    }


    public function shareVideo($caption, $video_url) {
        $url = 'https://graph.facebook.com/v14.0/' . $this->page_id . '/videos';
        $params = [
            'description'  => $caption,
            'source'       => $video_url,
            'access_token' => $this->access_token,
        ];

        return $this->sendRequest($url, 'post', $params);
    }

    public function getPageInsights() {
        $url = 'https://graph.facebook.com/v14.0/' . $this->page_id . '/insights';
        $params = [
            'access_token' => $this->access_token,
        ];

        return $this->sendRequest($url, 'get', $params);
    }

    public function getPageInfo() {
        $url = 'https://graph.facebook.com/v14.0/' . $this->page_id;
        $params = [
            'access_token' => $this->access_token,
        ];

        return $this->sendRequest($url, 'get', $params);
    }
}