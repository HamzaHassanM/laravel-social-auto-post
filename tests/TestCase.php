<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Tests;


use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTestCase;
use HamzaHassanM\LaravelSocialAutoPost\SocialShareServiceProvider;

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
        $app['config']->set('autopost.telegram_bot_token', 'fake-telegram-token');
        $app['config']->set('autopost.telegram_chat_id', 'fake-telegram-chat-id');
    }
}