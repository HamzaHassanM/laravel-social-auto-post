<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SocialPostPublishing
{
    use Dispatchable, SerializesModels;

    public string $platform;
    public string $method;
    public array $parameters;

    /**
     * Create a new event instance.
     *
     * @param string $platform
     * @param string $method
     * @param array $parameters
     */
    public function __construct(string $platform, string $method, array $parameters)
    {
        $this->platform = $platform;
        $this->method = $method;
        $this->parameters = $parameters;
    }
}
