<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Facades;

use Illuminate\Support\Facades\Facade;

class Telegram extends Facade {

    protected static function getFacadeAccessor() {
        return \HamzaHassanM\LaravelSocialAutoPost\Services\TelegramService::class;
    }
}