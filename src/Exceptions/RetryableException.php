<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Exceptions;

class RetryableException extends SocialMediaException
{
    protected ?string $platform = null;
    protected ?int $httpStatus = null;
    protected int $attempts = 1;

    /**
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     * @param string|null $platform The social media platform (e.g., 'twitter', 'facebook')
     * @param int|null $httpStatus The HTTP status code, if applicable
     * @param int $attempts Total number of HTTP attempts made
     */
    public function __construct(string $message = "A retryable error occurred", int $code = 0, \Throwable $previous = null, ?string $platform = null, ?int $httpStatus = null, int $attempts = 1)
    {
        parent::__construct($message, $code, $previous);
        
        $this->platform = $platform;
        $this->httpStatus = $httpStatus;
        $this->attempts = $attempts;
    }

    public function getPlatform(): ?string
    {
        return $this->platform;
    }

    public function getHttpStatus(): ?int
    {
        return $this->httpStatus;
    }

    public function getAttempts(): int
    {
        return $this->attempts;
    }
}
