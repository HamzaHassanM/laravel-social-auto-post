<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Services;

use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareDocumentPostInterface;
use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareImagePostInterface;
use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareInterface;
use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareVideoPostInterface;
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;
use Illuminate\Support\Facades\Log;

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
    private static ?TelegramService $instance = null;

    /**
     * Private constructor to prevent direct instantiation.
     */

    public function __construct(string $telegram_bot_token, string $chat_id) {
        $this->telegram_bot_token = $telegram_bot_token;
        $this->chat_id = $chat_id;
    }

    
    /**
     * Create a new instance dynamically with custom credentials.
     */
    public static function withCredentials(string $telegram_bot_token, string $chat_id): self 
    {
        return new self($telegram_bot_token, $chat_id);
    }

    /**
     * Get the singleton instance of TelegramService.
     *
     * @return TelegramService
     */
    public static function getInstance(): TelegramService {
        if (self::$instance === null) {
            $telegramBotToken = \HamzaHassanM\LaravelSocialAutoPost\Utils\ConfigHelper::get('autopost.telegram_bot_token');
            $chatId = \HamzaHassanM\LaravelSocialAutoPost\Utils\ConfigHelper::get('autopost.telegram_chat_id');
            self::$instance = new self($telegramBotToken, $chatId);
        }
        return self::$instance;
    }

    /**
     * Share a text post with a caption and a URL.
     *
     * @param string $caption The caption to accompany the post.
     * @param string $url The URL to share.
     *
     * @return array Response from the Telegram API.
     * @throws SocialMediaException
     */
    public function share(string $caption, string $url): array
    {
        $this->validateText($caption, 4096);
        $this->validateUrl($url);
        
        try {
            $sendMessageUrl = $this->buildApiUrl('sendMessage');
            $params = [
                'chat_id'    => $this->chat_id,
                'text'       => $caption . "\n" . $url,
                'parse_mode' => 'Markdown',
            ];

            $response = $this->sendRequest($sendMessageUrl, 'post', $params);
            Log::info('Telegram message sent successfully', ['message_id' => $response['result']['message_id'] ?? null]);
            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to send Telegram message', ['error' => $e->getMessage()]);
            throw new SocialMediaException('Failed to send Telegram message: ' . $e->getMessage());
        }
    }

    /**
     * Share an image post with a caption and an image URL.
     *
     * @param string $caption The caption to accompany the image.
     * @param string $image_url The URL of the image.
     *
     * @return mixed Response from the Telegram API.
     */

    public function shareImage(string $caption, string $image_url): array {
        $sendPhotoUrl = $this->buildApiUrl('sendPhoto');
        $params = [
            'chat_id'    => $this->chat_id,
            'photo'      => $image_url,
            'caption'    => $caption,
            'parse_mode' => 'Markdown',
        ];

        return $this->sendRequest($sendPhotoUrl, 'post', $params);
    }

    /**
     * Share a document post with a caption and a document URL.
     *
     * @param string $caption The caption to accompany the document.
     * @param string $document_url The URL of the document.
     *
     * @return mixed Response from the Telegram API.
     */
    public function shareDocument(string $caption, string $document_url): array {
        $sendDocumentUrl = $this->buildApiUrl('sendDocument');
        $params = [
            'chat_id'    => $this->chat_id,
            'document'   => $document_url,
            'caption'    => $caption,
            'parse_mode' => 'Markdown',
        ];

        return $this->sendRequest($sendDocumentUrl, 'post', $params);
    }

    /**
     * Share a video post with a caption and a video URL.
     *
     * @param string $caption The caption to accompany the video.
     * @param string $video_url The URL of the video.
     *
     * @return mixed Response from the Telegram API.
     */
    public function shareVideo(string $caption, string $video_url): array {
        $sendVideoUrl = $this->buildApiUrl('sendVideo');
        $params = [
            'chat_id'    => $this->chat_id,
            'video'      => $video_url,
            'caption'    => $caption,
            'parse_mode' => 'Markdown',
        ];

        return $this->sendRequest($sendVideoUrl, 'post', $params);
    }

    /**
     * Get updates from the Telegram bot.
     *
     * @return mixed Response from the Telegram API.
     */

    public function getUpdates() {
        $getUpdatesUrl = $this->buildApiUrl('getUpdates');
        $params = [];

        return $this->sendRequest($getUpdatesUrl, 'get', $params);
    }

    /**
     * Helper to build Telegram API URLs.
     *
     * @param string $endpoint
     *
     * @return string
     */
    private function buildApiUrl(string $endpoint): string {
        $baseUrl = \HamzaHassanM\LaravelSocialAutoPost\Utils\ConfigHelper::get('autopost.telegram_api_base_url');
        return $baseUrl . $this->telegram_bot_token . '/' . $endpoint;
    }
}
