<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Services;

use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareDocumentPostInterface;
use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareImagePostInterface;
use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareInterface;
use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareVideoPostInterface;
use Illuminate\Support\Facades\Http;

class TelegramService extends SocialMediaService implements ShareInterface,
    ShareImagePostInterface, ShareVideoPostInterface, ShareDocumentPostInterface {

    private        $telegram_bot_token;
    private        $chat_id;
    private static $instance;

    private function __construct() {
        $this->telegram_bot_token = config('autopost.telegram_bot_token');
        $this->chat_id = config('autopost.telegram_chat_id');
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function share($caption, $url) {
        $sendMessageUrl = 'https://api.telegram.org/bot' . $this->telegram_bot_token . '/sendMessage';
        $params = [
            'chat_id'    => $this->chat_id,
            'text'       => $caption . "\n" . $url,
            'parse_mode' => 'Markdown',
        ];

        return $this->sendRequest($sendMessageUrl, 'get', $params);
    }

    public function shareImage($caption, $image_url) {
        $sendPhotoUrl = 'https://api.telegram.org/bot' . $this->telegram_bot_token . '/sendPhoto';
        $params = [
            'chat_id'    => $this->chat_id,
            'photo'      => $image_url,
            'caption'    => $caption,
            'parse_mode' => 'Markdown',
        ];

        return $this->sendRequest($sendPhotoUrl, 'get', $params);
    }

    public function shareDocument($caption, $document_url) {
        $sendDocumentUrl = 'https://api.telegram.org/bot' . $this->telegram_bot_token . '/sendDocument';
        $params = [
            'chat_id'    => $this->chat_id,
            'document'   => $document_url,
            'caption'    => $caption,
            'parse_mode' => 'Markdown',
        ];

        return $this->sendRequest($sendDocumentUrl, 'get', $params);
    }

    public function shareVideo($caption, $video_url) {
        $sendVideoUrl = 'https://api.telegram.org/bot' . $this->telegram_bot_token . '/sendVideo';
        $params = [
            'chat_id'    => $this->chat_id,
            'video'      => $video_url,
            'caption'    => $caption,
            'parse_mode' => 'Markdown',
        ];

        return $this->sendRequest($sendVideoUrl, 'get', $params);
    }

    public function getUpdates() {
        $getUpdatesUrl = 'https://api.telegram.org/bot' . $this->telegram_bot_token . '/getUpdates';
        $params = [];

        return $this->sendRequest($getUpdatesUrl, 'get', $params);
    }
}