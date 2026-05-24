<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SocialPostFailed
{
    use Dispatchable, SerializesModels;

    public string $platform;
    public string $method;
    public array $parameters;
    public \Exception $exception;

    /**
     * Create a new event instance.
     *
     * @param string $platform
     * @param string $method
     * @param array $parameters
     * @param \Exception $exception
     */
    public function __construct(string $platform, string $method, array $parameters, \Exception $exception)
    {
        $this->platform = $platform;
        $this->method = $method;
        $this->parameters = $parameters;
        $this->exception = $exception;
    }
}
