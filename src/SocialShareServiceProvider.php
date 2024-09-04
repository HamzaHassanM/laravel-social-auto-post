<?php

namespace HamzaHassanM\LaravelSocialAutoPost;

use HamzaHassanM\LaravelSocialAutoPost\Services\FaceBook;
use HamzaHassanM\LaravelSocialAutoPost\Services\TelegramService;
use Illuminate\Support\ServiceProvider;

class SocialShareServiceProvider extends ServiceProvider {

    public function boot() {

        $this->publishes([
             __DIR__.'/config/autopost.php' => config_path('autopost.php'),
        ], 'autopost');
    }

    public function register() {
        // Bind FaceBook as a singleton
        $this->app->singleton(FaceBook::class, function ($app) {
            return FaceBook::getInstance();
        });

        $this->app->singleton(TelegramService::class, function ($app) {
            return TelegramService::getInstance();
        });

        // Optionally, register the alias for the facade
        $this->app->alias(FaceBook::class, 'facebook');
        $this->app->alias(TelegramService::class, 'telegram');

        // Optionally, register your config file
        $this->mergeConfigFrom( __DIR__.'/config/autopost.php', 'autopost');
    }

}