<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Tests\Unit;

use HamzaHassanM\LaravelSocialAutoPost\Utils\ConfigHelper;
use PHPUnit\Framework\TestCase;

class ConfigHelperTest extends TestCase
{
    public function test_it_returns_default_when_config_function_fails()
    {
        // This test runs without Laravel container booted, 
        // so it simulates the environment where `config()` throws or returns null.
        $default = 12345;
        $value = ConfigHelper::get('autopost.some_fake_key', $default);
        
        $this->assertEquals($default, $value);
    }
}
