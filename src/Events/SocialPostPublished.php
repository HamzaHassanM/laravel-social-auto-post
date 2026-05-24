<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SocialPostPublished
{
    use Dispatchable, SerializesModels;

    public string $platform;
    public string $method;
    public array $parameters;
    public array $result;

    /**
     * Create a new event instance.
     *
     * @param string $platform
     * @param string $method
     * @param array $parameters
     * @param array $result
     */
    public function __construct(string $platform, string $method, array $parameters, array $result)
    {
        $this->platform = $platform;
        $this->method = $method;
        $this->parameters = $parameters;
        $this->result = $result;
    }
}
