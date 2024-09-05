<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Tests\Feature;


use HamzaHassanM\LaravelSocialAutoPost\Facades\FaceBook;
use HamzaHassanM\tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

class FaceBookTest extends TestCase {

    public function testShareImageFacade()
    {
        Http::fake([
            'https://graph.facebook.com/v14.0/*' => Http::response(['id' => '123'], 200),
        ]);

        $response = FaceBook::shareImage('Test Caption', 'http://example.com/image.jpg');

        $this->assertArrayHasKey('id', $response);
        $this->assertEquals('123', $response['id']);
    }

    public function testSharePostFacade()
    {
        Http::fake([
            'https://graph.facebook.com/v14.0/*' => Http::response(['id' => '456'], 200),
        ]);

        $response = FaceBook::share('Test Post Caption', 'http://example.com');

        $this->assertArrayHasKey('id', $response);
        $this->assertEquals('456', $response['id']);
    }
}