<?php

// src/Facades/FacebookService.php
namespace HamzaHassanM\LaravelSocialAutoPost\Facades;

use Illuminate\Support\Facades\Facade;

class FaceBook extends Facade {

    protected static function getFacadeAccessor() {
        return \HamzaHassanM\LaravelSocialAutoPost\Services\FacebookService::class;
    }
}
