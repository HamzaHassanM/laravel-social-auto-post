<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Services;
use Illuminate\Support\Facades\Http;
abstract class SocialMediaService
{
    protected function sendRequest($url, $method = 'post', $params = [])
    {
        $response = Http::{$method}($url, $params);
        return $response->json();
    }
}