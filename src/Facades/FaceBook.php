<?php

// src/Facades/FaceBook.php
namespace HamzaHassanM\LaravelSocialAutoPost\Facades;

use Illuminate\Support\Facades\Facade;

class FaceBook extends Facade {

    protected static function getFacadeAccessor() {
        // This is the key used to bind the singleton in the service container
        return \HamzaHassanM\LaravelSocialAutoPost\Services\FaceBook::class;
    }
}
