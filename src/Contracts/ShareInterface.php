<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Contracts;

interface ShareInterface {

    public function share(string $caption, string $url): array;

}
