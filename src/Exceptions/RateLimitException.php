<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Exceptions;

class RateLimitException extends SocialMediaException
{
    protected ?int $retryAfter = null;

    /**
     * @param string $message
     * @param string|int|null $retryAfterHeader The value of the Retry-After header
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = "Too Many Requests", $retryAfterHeader = null, int $code = 429, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        if ($retryAfterHeader !== null) {
            $this->parseRetryAfter($retryAfterHeader);
        }
    }

    /**
     * Get the suggested number of seconds to wait before retrying.
     *
     * @return int|null
     */
    public function getRetryAfter(): ?int
    {
        return $this->retryAfter;
    }

    /**
     * Parse the Retry-After header which can be an integer (seconds) or HTTP-date.
     *
     * @param string|int $headerValue
     */
    protected function parseRetryAfter($headerValue): void
    {
        if (is_numeric($headerValue)) {
            $this->retryAfter = (int) $headerValue;
            return;
        }

        $time = strtotime((string) $headerValue);
        if ($time !== false) {
            $seconds = $time - time();
            $this->retryAfter = $seconds > 0 ? $seconds : 0;
        }
    }
}
