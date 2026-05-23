<?php

/**
 * Multi-Account Usage Examples
 * 
 * This file demonstrates how to dynamically authenticate multiple social media accounts
 * at runtime without relying on the .env file. This is extremely useful for SaaS
 * platforms or multi-tenant applications.
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use HamzaHassanM\LaravelSocialAutoPost\Facades\SocialMedia;
use HamzaHassanM\LaravelSocialAutoPost\Facades\Facebook;
use HamzaHassanM\LaravelSocialAutoPost\Facades\Twitter;
use HamzaHassanM\LaravelSocialAutoPost\Exceptions\SocialMediaException;

echo "🚀 Laravel Social Auto Post - Multi-Account Examples\n";
echo "====================================================\n\n";

// Example 1: Passing Custom Credentials to the Global Manager
echo "👤 Example 1: Multi-Platform Post with Custom Credentials (User A)\n";
echo "-----------------------------------------------------------------\n";

try {
    // These credentials typically come from your database for a specific user or tenant
    $userACredentials = [
        'facebook' => [
            'access_token' => 'USER_A_FACEBOOK_TOKEN',
            'page_id'      => 'USER_A_FACEBOOK_PAGE_ID'
        ],
        'twitter' => [
            'bearer_token'        => 'USER_A_TWITTER_BEARER',
            'api_key'             => 'USER_A_TWITTER_KEY',
            'api_secret'          => 'USER_A_TWITTER_SECRET',
            'access_token'        => 'USER_A_TWITTER_ACCESS',
            'access_token_secret' => 'USER_A_TWITTER_ACCESS_SECRET'
        ],
        // You can mix and match platforms
    ];

    // Use withCredentials() to temporarily override the .env defaults for this specific call
    $result = SocialMedia::withCredentials($userACredentials)
        ->share(['facebook', 'twitter'], 'Hello from User A using dynamic credentials!', 'https://example.com');
    
    echo "✅ Posted to " . $result['success_count'] . " out of " . $result['total_platforms'] . " platforms for User A\n";
    
} catch (SocialMediaException $e) {
    echo "❌ Error posting for User A: " . $e->getMessage() . "\n";
}

echo "\n";


// Example 2: Platform-Specific Facades with Custom Credentials
echo "👤 Example 2: Platform-Specific Post with Custom Credentials (User B)\n";
echo "-----------------------------------------------------------------\n";

try {
    // If you only want to post to a single platform for a specific user, you can use the specific facade.
    
    // Facebook specific
    $facebookService = Facebook::withCredentials('USER_B_FACEBOOK_TOKEN', 'USER_B_FACEBOOK_PAGE_ID');
    $fbResult = $facebookService->share('Hello from User B specific Facebook page!', 'https://example.com');
    echo "✅ Facebook post successful for User B\n";

    // Twitter specific
    $twitterService = Twitter::withCredentials(
        'USER_B_BEARER', 
        'USER_B_API_KEY', 
        'USER_B_API_SECRET', 
        'USER_B_ACCESS_TOKEN', 
        'USER_B_ACCESS_SECRET'
    );
    $twResult = $twitterService->share('Hello from User B specific Twitter account!', 'https://example.com');
    echo "✅ Twitter post successful for User B\n";

} catch (SocialMediaException $e) {
    echo "❌ Error posting for User B: " . $e->getMessage() . "\n";
}

echo "\n";


// Example 3: Falling Back to Default (.env) Credentials
echo "🌍 Example 3: Fallback to .env Configuration\n";
echo "--------------------------------------------\n";

try {
    $mixedCredentials = [
        'facebook' => [
            'access_token' => 'USER_C_FACEBOOK_TOKEN',
            'page_id'      => 'USER_C_FACEBOOK_PAGE_ID'
        ]
        // Twitter is NOT provided here
    ];

    // If Twitter is called but not provided in customCredentials, it automatically falls back
    // to the default configuration set in your .env file.
    $result = SocialMedia::withCredentials($mixedCredentials)
        ->share(['facebook', 'twitter'], 'Testing fallback mechanisms!', 'https://example.com');
    
    echo "✅ Mixed posting complete. Handled " . $result['total_platforms'] . " platforms.\n";
    echo "   - Facebook used custom credentials.\n";
    echo "   - Twitter fell back to the .env default configuration.\n";

} catch (SocialMediaException $e) {
    echo "❌ Error testing mixed credentials: " . $e->getMessage() . "\n";
}

echo "\n";
echo "🎉 Multi-account examples completed!\n";
echo "====================================\n";
