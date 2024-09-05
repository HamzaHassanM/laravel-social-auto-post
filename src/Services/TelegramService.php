<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Services;

use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareDocumentPostInterface;
use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareImagePostInterface;
use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareInterface;
use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareVideoPostInterface;

/**
 * Class TelegramService
 *
 * Service for sharing posts to Telegram using Telegram Bot API.
 *
 * Implements sharing of general posts, images, documents, and videos.
 */
class TelegramService extends SocialMediaService implements ShareInterface,
    ShareImagePostInterface, ShareVideoPostInterface, ShareDocumentPostInterface {


    /**
     * @var string Telegram bot token
     */
    private $telegram_bot_token;

    /**
     * @var string Telegram chat ID
     */
    private $chat_id;

    /**
     * @var TelegramService|null Singleton instance
     */
    private static $instance;

    /**
     * Private constructor to prevent direct instantiation.
     */

    private function __construct() {
        $this->telegram_bot_token = config('autopost.telegram_bot_token');
        $this->chat_id = config('autopost.telegram_chat_id');
    }

    /**
     * Get the singleton instance of TelegramService.
     *
     * @return TelegramService
     */

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Share a text post with a caption and a URL.
     *
     * @param string $caption The caption to accompany the post.
     * @param string $url The URL to share.
     *
     * @return mixed Response from the Telegram API.
     */
    public function share($caption, $url) {
        $sendMessageUrl = 'https://api.telegram.org/bot' . $this->telegram_bot_token . '/sendMessage';
        $params = [
            'chat_id'    => $this->chat_id,
            'text'       => $caption . "\n" . $url,
            'parse_mode' => 'Markdown',
        ];

        return $this->sendRequest($sendMessageUrl, 'get', $params);
    }

    /**
     * Share an image post with a caption and an image URL.
     *
     * @param string $caption The caption to accompany the image.
     * @param string $image_url The URL of the image.
     *
     * @return mixed Response from the Telegram API.
     */

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

    /**
     * Share a document post with a caption and a document URL.
     *
     * @param string $caption The caption to accompany the document.
     * @param string $document_url The URL of the document.
     *
     * @return mixed Response from the Telegram API.
     */
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

    /**
     * Share a video post with a caption and a video URL.
     *
     * @param string $caption The caption to accompany the video.
     * @param string $video_url The URL of the video.
     *
     * @return mixed Response from the Telegram API.
     */
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

    /**
     * Get updates from the Telegram bot.
     *
     * @return mixed Response from the Telegram API.
     */

    public function getUpdates() {
        $getUpdatesUrl = 'https://api.telegram.org/bot' . $this->telegram_bot_token . '/getUpdates';
        $params = [];

        return $this->sendRequest($getUpdatesUrl, 'get', $params);
    }
}