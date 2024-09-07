<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Tests\Feature;

use HamzaHassanM\LaravelSocialAutoPost\SocialShareServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {

    protected function getPackageProviders($app) {
        return [
            SocialShareServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app) {
        // Setup default config for testing
        $app['config']->set('autopost.facebook_access_token', 'fake-facebook-token');
        $app['config']->set('autopost.facebook_page_id', 'fake-facebook-page-id');
        $app['config']->set('autopost.facebook_api_version', 'v20.0');

        $app['config']->set('autopost.telegram_bot_token', 'fake-telegram-token');
        $app['config']->set('autopost.telegram_chat_id', 'fake-telegram-chat-id');
        $app['config']->set('autopost.telegram_api_base_url', 'https://api.telegram.org/bot');
    }
}