<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Contracts;

interface ShareVideoPostInterface {

    public function shareVideo(string $caption, string $video_url): array;

}
