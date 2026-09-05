<?php

namespace HamzaHassanM\LaravelSocialAutoPost;

use HamzaHassanM\LaravelSocialAutoPost\Services\FacebookService;
use HamzaHassanM\LaravelSocialAutoPost\Services\TelegramService;
use HamzaHassanM\LaravelSocialAutoPost\Services\TwitterService;
use HamzaHassanM\LaravelSocialAutoPost\Services\LinkedInService;
use HamzaHassanM\LaravelSocialAutoPost\Services\InstagramService;
use HamzaHassanM\LaravelSocialAutoPost\Services\TikTokService;
use HamzaHassanM\LaravelSocialAutoPost\Services\YouTubeService;
use HamzaHassanM\LaravelSocialAutoPost\Services\PinterestService;
use HamzaHassanM\LaravelSocialAutoPost\Services\SocialMediaManager;
use Illuminate\Support\ServiceProvider;

class SocialShareServiceProvider extends ServiceProvider {

    public function boot() {
        $this->publishes([
             __DIR__.'/config/autopost.php' => config_path('autopost.php'),
        ], 'autopost');
    }

    public function register() {
        // Register all social media services as singletons
        $this->app->singleton(FacebookService::class, function ($app) {
            return FacebookService::getInstance();
        });

        $this->app->singleton(TelegramService::class, function ($app) {
            return TelegramService::getInstance();
        });

        $this->app->singleton(TwitterService::class, function ($app) {
            return TwitterService::getInstance();
        });

        $this->app->singleton(LinkedInService::class, function ($app) {
            return LinkedInService::getInstance();
        });

        $this->app->singleton(InstagramService::class, function ($app) {
            return InstagramService::getInstance();
        });

        $this->app->singleton(TikTokService::class, function ($app) {
            return TikTokService::getInstance();
        });

        $this->app->singleton(YouTubeService::class, function ($app) {
            return YouTubeService::getInstance();
        });

        $this->app->singleton(PinterestService::class, function ($app) {
            return PinterestService::getInstance();
        });

        $this->app->singleton(SocialMediaManager::class, function ($app) {
            return new SocialMediaManager();
        });

        // Register service aliases
        $this->app->alias(FacebookService::class, 'facebook');
        $this->app->alias(TelegramService::class, 'telegram');
        $this->app->alias(TwitterService::class, 'twitter');
        $this->app->alias(LinkedInService::class, 'linkedin');
        $this->app->alias(InstagramService::class, 'instagram');
        $this->app->alias(TikTokService::class, 'tiktok');
        $this->app->alias(YouTubeService::class, 'youtube');
        $this->app->alias(PinterestService::class, 'pinterest');
        $this->app->alias(SocialMediaManager::class, 'socialmedia');

        // Register config file
        $this->mergeConfigFrom( __DIR__.'/config/autopost.php', 'autopost');
    }

}
