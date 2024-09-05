<?php

namespace HamzaHassanM\LaravelSocialAutoPost;

use HamzaHassanM\LaravelSocialAutoPost\Services\FacebookService;
use HamzaHassanM\LaravelSocialAutoPost\Services\TelegramService;
use Illuminate\Support\ServiceProvider;

class SocialShareServiceProvider extends ServiceProvider {

    public function boot() {

        $this->publishes([
             __DIR__.'/config/autopost.php' => config_path('autopost.php'),
        ], 'autopost');
    }

    public function register() {
        // Bind FacebookService as a singleton
        $this->app->singleton(FacebookService::class, function ($app) {
            return FacebookService::getInstance();
        });

        $this->app->singleton(TelegramService::class, function ($app) {
            return TelegramService::getInstance();
        });

        // Optionally, register the alias for the facade
        $this->app->alias(FacebookService::class, 'facebook');
        $this->app->alias(TelegramService::class, 'telegram');

        // Optionally, register your config file
        $this->mergeConfigFrom( __DIR__.'/config/autopost.php', 'autopost');
    }

}