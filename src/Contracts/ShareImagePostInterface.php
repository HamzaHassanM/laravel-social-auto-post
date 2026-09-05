<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Contracts;

interface ShareImagePostInterface {

    public function shareImage(string $caption, string $image_url): array;

}
