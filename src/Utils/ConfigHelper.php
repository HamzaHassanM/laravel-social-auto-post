<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Utils;

class ConfigHelper
{
    /**
     * Safely retrieve a configuration value.
     * 
     * This wrapper prevents exceptions when running in raw PHPUnit environments
     * where the Laravel application container is not fully booted and the 'config'
     * binding is missing.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        try {
            if (function_exists('config')) {
                return config($key, $default);
            }
        } catch (\Throwable $t) {
            // Laravel container not available, fallback to default
        }

        return $default;
    }
}
