<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Services;

use HamzaHassanM\LaravelSocialAutoPost\Contracts\ShareInterface;
use Illuminate\Support\Facades\Http;

class TelegramService implements ShareInterface {

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
        $response = Http::get('https://api.telegram.org/bot' . $this->telegram_bot_token . '/sendMessage', [
            'chat_id'    => $this->chat_id,
            'text'       => $caption . "\n" . $url,
            'parse_mode' => 'Markdown',
        ]);
        return $response->json();
    }
}