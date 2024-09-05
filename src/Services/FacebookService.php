<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Services;

use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareImagePostInterface;
use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareInterface;
use Illuminate\Support\Facades\Http;
use function HamzaHassanM\LaravelSocialAutoPost\config;

class FaceBook extends SocialMediaService implements ShareInterface, ShareImagePostInterface {

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
        $response = Http::post('https://graph.facebook.com/v14.0/' . $this->page_id . '/photos
            ?url=' . $image_url . '&
            caption=' . $caption . '
            &access_token=' . $this->access_token);
        return $response->json();
    }

    public function share($caption, $url) {
        $response = Http::post('https://graph.facebook.com/v14.0/' . $this->page_id . '/feed?message=' . $caption . '&link=' . $url . '/' . '&access_token=' . $this->access_token);
        return $response->json();
    }
}