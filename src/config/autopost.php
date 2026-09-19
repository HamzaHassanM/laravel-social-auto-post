<?php

return [
    // Facebook Configuration
    'facebook_access_token' => env('FACEBOOK_ACCESS_TOKEN'),
    'facebook_page_id'      => env('FACEBOOK_PAGE_ID'),
    'facebook_api_version'  => env('FACEBOOK_API_VERSION', 'v20.0'),

    // Telegram Configuration
    'telegram_bot_token'    => env('TELEGRAM_BOT_TOKEN'),
    'telegram_chat_id'      => env('TELEGRAM_CHAT_ID'),
    'telegram_api_base_url' => env('TELEGRAM_API_BASE_URL', 'https://api.telegram.org/bot'),

    // Twitter/X Configuration
    'twitter_bearer_token'         => env('TWITTER_BEARER_TOKEN'),
    'twitter_api_key'              => env('TWITTER_API_KEY'),
    'twitter_api_secret'           => env('TWITTER_API_SECRET'),
    'twitter_access_token'         => env('TWITTER_ACCESS_TOKEN'),
    'twitter_access_token_secret'  => env('TWITTER_ACCESS_TOKEN_SECRET'),

    // LinkedIn Configuration
    'linkedin_access_token'     => env('LINKEDIN_ACCESS_TOKEN'),
    'linkedin_person_urn'       => env('LINKEDIN_PERSON_URN'),
    'linkedin_organization_urn' => env('LINKEDIN_ORGANIZATION_URN'),

    // Instagram Configuration
    'instagram_access_token'  => env('INSTAGRAM_ACCESS_TOKEN'),
    'instagram_account_id'    => env('INSTAGRAM_ACCOUNT_ID'),

    // TikTok Configuration
    'tiktok_access_token'  => env('TIKTOK_ACCESS_TOKEN'),
    'tiktok_client_key'    => env('TIKTOK_CLIENT_KEY'),
    'tiktok_client_secret' => env('TIKTOK_CLIENT_SECRET'),

    // YouTube Configuration
    'youtube_api_key'      => env('YOUTUBE_API_KEY'),
    'youtube_access_token' => env('YOUTUBE_ACCESS_TOKEN'),
    'youtube_channel_id'   => env('YOUTUBE_CHANNEL_ID'),

    // Pinterest Configuration
    'pinterest_access_token' => env('PINTEREST_ACCESS_TOKEN'),
    'pinterest_board_id'     => env('PINTEREST_BOARD_ID'),

    // General Configuration
    'default_platforms' => ['facebook', 'twitter', 'linkedin'],
    'enable_logging'    => env('SOCIAL_MEDIA_LOGGING', true),
    'timeout'           => env('SOCIAL_MEDIA_TIMEOUT', 30),
    'connect_timeout'   => env('SOCIAL_MEDIA_CONNECT_TIMEOUT', 10),
    'retry_attempts'    => env('SOCIAL_MEDIA_RETRY_ATTEMPTS', 3),
    'retry_backoff_base'=> env('SOCIAL_MEDIA_RETRY_BACKOFF_BASE', 2),
    
    // Media Configuration
    'max_media_size'    => env('SOCIAL_MEDIA_MAX_MEDIA_SIZE', 50 * 1024 * 1024), // 50MB default
    'max_redirects'     => env('SOCIAL_MEDIA_MAX_REDIRECTS', 5),
    'low_speed_limit'   => env('SOCIAL_MEDIA_LOW_SPEED_LIMIT', 1024), // 1KB/s
    'low_speed_time'    => env('SOCIAL_MEDIA_LOW_SPEED_TIME', 10),

    // Security Configuration
    'enforce_ssrf_protection' => env('SOCIAL_MEDIA_ENFORCE_SSRF_PROTECTION', true),
    'verify_media_mime_type'  => env('SOCIAL_MEDIA_VERIFY_MIME_TYPE', true),
];



