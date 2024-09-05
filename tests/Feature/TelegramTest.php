<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Tests\Feature;

use HamzaHassanM\LaravelSocialAutoPost\Facades\Telegram;
use Illuminate\Support\Facades\Http;

class TelegramTest extends TestCase {

    public function testShareFacade() {
        Http::fake([
            'https://api.telegram.org/bot*' => Http::response(['ok' => true], 200),
        ]);

        $response = Telegram::share('Test Message', 'http://example.com');

        $this->assertArrayHasKey('ok', $response);
        $this->assertTrue($response['ok']);
    }

}