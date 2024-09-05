<?php

namespace HamzaHassanM\LaravelSocialAutoPost\Enms;

enum FacebookMetrics: string {

    // Page Content Metrics
    case PAGE_TAB_VIEWS_LOGIN_TOP_UNIQUE = 'page_tab_views_login_top_unique';
    case PAGE_TAB_VIEWS_LOGIN_TOP = 'page_tab_views_login_top';
    case PAGE_TAB_VIEWS_LOGOUT_TOP = 'page_tab_views_logout_top';

    // Page CTA Clicks Metrics
    case PAGE_TOTAL_ACTIONS = 'page_total_actions';
    case PAGE_CTA_CLICKS_LOGGED_IN_TOTAL = 'page_cta_clicks_logged_in_total';
    case PAGE_CTA_CLICKS_LOGGED_IN_UNIQUE = 'page_cta_clicks_logged_in_unique';
    case PAGE_GET_DIRECTIONS_CLICKS_LOGGED_IN_UNIQUE = 'page_get_directions_clicks_logged_in_unique';
    case PAGE_WEBSITE_CLICKS_LOGGED_IN_UNIQUE = 'page_website_clicks_logged_in_unique';

    // Page Engagement Metrics
    case PAGE_POST_ENGAGEMENTS = 'page_post_engagements';
    case PAGE_CONSUMPTIONS_UNIQUE = 'page_consumptions_unique';
    case PAGE_CONSUMPTIONS_BY_CONSUMPTION_TYPE = 'page_consumptions_by_consumption_type';
    case PAGE_CONSUMPTIONS_BY_CONSUMPTION_TYPE_UNIQUE = 'page_consumptions_by_consumption_type_unique';
    case PAGE_PLACES_CHECKIN_TOTAL = 'page_places_checkin_total';
    case PAGE_PLACES_CHECKIN_TOTAL_UNIQUE = 'page_places_checkin_total_unique';
    case PAGE_NEGATIVE_FEEDBACK = 'page_negative_feedback';
    case PAGE_NEGATIVE_FEEDBACK_UNIQUE = 'page_negative_feedback_unique';
    case PAGE_NEGATIVE_FEEDBACK_BY_TYPE = 'page_negative_feedback_by_type';
    case PAGE_NEGATIVE_FEEDBACK_BY_TYPE_UNIQUE = 'page_negative_feedback_by_type_unique';

    // Page Fans and Followers Metrics
    case PAGE_FANS_ONLINE = 'page_fans_online';
    case PAGE_FANS_ONLINE_PER_DAY = 'page_fans_online_per_day';
    case PAGE_FAN_ADDS_BY_PAID_NON_PAID_UNIQUE = 'page_fan_adds_by_paid_non_paid_unique';
    case PAGE_LIFETIME_ENGAGED_FOLLOWERS_UNIQUE = 'page_lifetime_engaged_followers_unique';
    case PAGE_DAILY_FOLLOWS = 'page_daily_follows';
    case PAGE_DAILY_FOLLOWS_UNIQUE = 'page_daily_follows_unique';
    case PAGE_DAILY_UNFOLLOWS_UNIQUE = 'page_daily_unfollows_unique';
    case PAGE_FOLLOWS = 'page_follows';

    // Page Impressions Metrics
    case PAGE_IMPRESSIONS = 'page_impressions';
    case PAGE_IMPRESSIONS_UNIQUE = 'page_impressions_unique';
    case PAGE_IMPRESSIONS_PAID = 'page_impressions_paid';
    case PAGE_IMPRESSIONS_PAID_UNIQUE = 'page_impressions_paid_unique';
    case PAGE_IMPRESSIONS_ORGANIC_V2 = 'page_impressions_organic_v2';
    case PAGE_IMPRESSIONS_ORGANIC_UNIQUE_V2 = 'page_impressions_organic_unique_v2';
    case PAGE_IMPRESSIONS_VIRAL = 'page_impressions_viral';
    case PAGE_IMPRESSIONS_VIRAL_UNIQUE = 'page_impressions_viral_unique';
    case PAGE_IMPRESSIONS_NONVIRAL = 'page_impressions_nonviral';
    case PAGE_IMPRESSIONS_NONVIRAL_UNIQUE = 'page_impressions_nonviral_unique';
    case PAGE_IMPRESSIONS_BY_STORY_TYPE = 'page_impressions_by_story_type';
    case PAGE_IMPRESSIONS_BY_STORY_TYPE_UNIQUE = 'page_impressions_by_story_type_unique';
    case PAGE_IMPRESSIONS_BY_CITY_UNIQUE = 'page_impressions_by_city_unique';
    case PAGE_IMPRESSIONS_BY_COUNTRY_UNIQUE = 'page_impressions_by_country_unique';
    case PAGE_IMPRESSIONS_BY_LOCALE_UNIQUE = 'page_impressions_by_locale_unique';
    case PAGE_IMPRESSIONS_BY_AGE_GENDER_UNIQUE = 'page_impressions_by_age_gender_unique';

    // Page Posts
    case PAGE_POSTS_IMPRESSIONS = 'page_posts_impressions';  // day, week, days_28
    case PAGE_POSTS_IMPRESSIONS_UNIQUE = 'page_posts_impressions_unique';  // day, week, days_28
    case PAGE_POSTS_IMPRESSIONS_PAID = 'page_posts_impressions_paid';  // day, week, days_28
    case PAGE_POSTS_IMPRESSIONS_PAID_UNIQUE = 'page_posts_impressions_paid_unique';  // day, week, days_28
    case PAGE_POSTS_IMPRESSIONS_ORGANIC = 'page_posts_impressions_organic';  // day, week, days_28
    case PAGE_POSTS_IMPRESSIONS_ORGANIC_UNIQUE = 'page_posts_impressions_organic_unique';  // day, week, days_28
    case PAGE_POSTS_SERVED_IMPRESSIONS_ORGANIC_UNIQUE = 'page_posts_served_impressions_organic_unique';  // day, week, days_28
    case PAGE_POSTS_IMPRESSIONS_VIRAL = 'page_posts_impressions_viral';  // day, week, days_28
    case PAGE_POSTS_IMPRESSIONS_VIRAL_UNIQUE = 'page_posts_impressions_viral_unique';  // day, week, days_28
    case PAGE_POSTS_IMPRESSIONS_NONVIRAL = 'page_posts_impressions_nonviral';  // day, week, days_28
    case PAGE_POSTS_IMPRESSIONS_NONVIRAL_UNIQUE = 'page_posts_impressions_nonviral_unique';  // day, week, days_28

    // Page Post Engagement
    case POST_ENGAGED_USERS = 'post_engaged_users';  // lifetime
    case POST_NEGATIVE_FEEDBACK = 'post_negative_feedback';  // lifetime
    case POST_NEGATIVE_FEEDBACK_UNIQUE = 'post_negative_feedback_unique';  // lifetime
    case POST_NEGATIVE_FEEDBACK_BY_TYPE = 'post_negative_feedback_by_type';  // lifetime
    case POST_NEGATIVE_FEEDBACK_BY_TYPE_UNIQUE = 'post_negative_feedback_by_type_unique';  // lifetime
    case POST_ENGAGED_FAN = 'post_engaged_fan';  // lifetime
    case POST_CLICKS = 'post_clicks';  // lifetime
    case POST_CLICKS_UNIQUE = 'post_clicks_unique';  // lifetime
    case POST_CLICKS_BY_TYPE = 'post_clicks_by_type';  // lifetime
    case POST_CLICKS_BY_TYPE_UNIQUE = 'post_clicks_by_type_unique';  // lifetime

    // Page Post Impressions
    case POST_IMPRESSIONS = 'post_impressions';  // lifetime
    case POST_IMPRESSIONS_UNIQUE = 'post_impressions_unique';  // lifetime
    case POST_IMPRESSIONS_PAID = 'post_impressions_paid';  // lifetime
    case POST_IMPRESSIONS_PAID_UNIQUE = 'post_impressions_paid_unique';  // lifetime
    case POST_IMPRESSIONS_FAN = 'post_impressions_fan';  // lifetime
    case POST_IMPRESSIONS_FAN_UNIQUE = 'post_impressions_fan_unique';  // lifetime
    case POST_IMPRESSIONS_ORGANIC = 'post_impressions_organic';  // lifetime
    case POST_IMPRESSIONS_ORGANIC_UNIQUE = 'post_impressions_organic_unique';  // lifetime
    case POST_IMPRESSIONS_VIRAL = 'post_impressions_viral';  // lifetime
    case POST_IMPRESSIONS_VIRAL_UNIQUE = 'post_impressions_viral_unique';  // lifetime
    case POST_IMPRESSIONS_NONVIRAL = 'post_impressions_nonviral';  // lifetime
    case POST_IMPRESSIONS_NONVIRAL_UNIQUE = 'post_impressions_nonviral_unique';  // lifetime
    case POST_IMPRESSIONS_BY_STORY_TYPE = 'post_impressions_by_story_type';  // lifetime
    case POST_IMPRESSIONS_BY_STORY_TYPE_UNIQUE = 'post_impressions_by_story_type_unique';  // lifetime
    case POST_IMPRESSIONS_BY_PAID_NON_PAID = 'post_impressions_by_paid_non_paid';  // lifetime

    // Page Post Reactions
    case POST_REACTIONS_LIKE_TOTAL = 'post_reactions_like_total';  // lifetime
    case POST_REACTIONS_LOVE_TOTAL = 'post_reactions_love_total';  // lifetime
    case POST_REACTIONS_WOW_TOTAL = 'post_reactions_wow_total';  // lifetime
    case POST_REACTIONS_HAHA_TOTAL = 'post_reactions_haha_total';  // lifetime
    case POST_REACTIONS_SORRY_TOTAL = 'post_reactions_sorry_total';  // lifetime
    case POST_REACTIONS_ANGER_TOTAL = 'post_reactions_anger_total';  // lifetime
    case POST_REACTIONS_BY_TYPE_TOTAL = 'post_reactions_by_type_total';  // lifetime

    // Page Reactions
    case PAGE_ACTIONS_POST_REACTIONS_LIKE_TOTAL = 'page_actions_post_reactions_like_total';  // day, week, days_28
    case PAGE_ACTIONS_POST_REACTIONS_LOVE_TOTAL = 'page_actions_post_reactions_love_total';  // day, week, days_28
    case PAGE_ACTIONS_POST_REACTIONS_WOW_TOTAL = 'page_actions_post_reactions_wow_total';  // day, week, days_28
    case PAGE_ACTIONS_POST_REACTIONS_HAHA_TOTAL = 'page_actions_post_reactions_haha_total';  // day, week, days_28
    case PAGE_ACTIONS_POST_REACTIONS_SORRY_TOTAL = 'page_actions_post_reactions_sorry_total';  // day, week, days_28
    case PAGE_ACTIONS_POST_REACTIONS_ANGER_TOTAL = 'page_actions_post_reactions_anger_total';  // day, week, days_28
    case PAGE_ACTIONS_POST_REACTIONS_TOTAL = 'page_actions_post_reactions_total';  // day
    // Page User Demographics
    case PAGE_FANS = 'page_fans';
    case PAGE_FANS_LOCALE = 'page_fans_locale';
    case PAGE_FANS_CITY = 'page_fans_city';
    case PAGE_FANS_COUNTRY = 'page_fans_country';
    case PAGE_FAN_ADDS = 'page_fan_adds';
    case PAGE_FAN_ADDS_UNIQUE = 'page_fan_adds_unique';
    case PAGE_FAN_REMOVES = 'page_fan_removes';
    case PAGE_FAN_REMOVES_UNIQUE = 'page_fan_removes_unique';

    // Page Like Sources
    case ADS = 'ads';
    case NEWS_FEED = 'news_feed';
    case PAGE_SUGGESTIONS = 'page_suggestions';
    case RESTORED_LIKES = 'restored_likes_from_reactivated_accounts';
    case SEARCH = 'search';
    case YOUR_PAGE = 'your_page';

    // Page Video Views
    case PAGE_VIDEO_VIEWS = 'page_video_views';
    case PAGE_VIDEO_VIEWS_PAID = 'page_video_views_paid';
    case PAGE_VIDEO_VIEWS_ORGANIC = 'page_video_views_organic';
    case PAGE_VIDEO_VIEWS_AUTOPLAYED = 'page_video_views_autoplayed';
    case PAGE_VIDEO_VIEWS_CLICK_TO_PLAY = 'page_video_views_click_to_play';
    case PAGE_VIDEO_COMPLETE_VIEWS_30S = 'page_video_complete_views_30s';
    case PAGE_VIDEO_COMPLETE_VIEWS_30S_PAID = 'page_video_complete_views_30s_paid';
    case PAGE_VIDEO_COMPLETE_VIEWS_30S_ORGANIC = 'page_video_complete_views_30s_organic';
    case PAGE_VIDEO_COMPLETE_VIEWS_30S_AUTOPLAYED = 'page_video_complete_views_30s_autoplayed';
    case PAGE_VIDEO_COMPLETE_VIEWS_30S_CLICK_TO_PLAY = 'page_video_complete_views_30s_click_to_play';
    case PAGE_VIDEO_COMPLETE_VIEWS_30S_UNIQUE = 'page_video_complete_views_30s_unique';

    // Page Views
    case PAGE_VIEWS_TOTAL = 'page_views_total';

    // Video Metrics
    case POST_VIDEO_AVG_TIME_WATCHED = 'post_video_avg_time_watched'; // Lifetime
    case POST_VIDEO_COMPLETE_VIEWS_ORGANIC = 'post_video_complete_views_organic'; // Lifetime
    case POST_VIDEO_COMPLETE_VIEWS_ORGANIC_UNIQUE = 'post_video_complete_views_organic_unique'; // Lifetime
    case POST_VIDEO_COMPLETE_VIEWS_PAID = 'post_video_complete_views_paid'; // Lifetime
    case POST_VIDEO_COMPLETE_VIEWS_PAID_UNIQUE = 'post_video_complete_views_paid_unique'; // Lifetime
    case POST_VIDEO_RETENTION_GRAPH = 'post_video_retention_graph'; // Lifetime
    case POST_VIDEO_RETENTION_GRAPH_CLICKED_TO_PLAY = 'post_video_retention_graph_clicked_to_play'; // Lifetime
    case POST_VIDEO_RETENTION_GRAPH_AUTOPLAYED = 'post_video_retention_graph_autoplayed'; // Lifetime
    case POST_VIDEO_VIEWS_ORGANIC = 'post_video_views_organic'; // Lifetime, Day
    case POST_VIDEO_VIEWS_ORGANIC_UNIQUE = 'post_video_views_organic_unique'; // Lifetime
    case POST_VIDEO_VIEWS_PAID = 'post_video_views_paid'; // Lifetime, Day
    case POST_VIDEO_VIEWS_PAID_UNIQUE = 'post_video_views_paid_unique'; // Lifetime
    case POST_VIDEO_LENGTH = 'post_video_length'; // Lifetime
    case POST_VIDEO_VIEWS = 'post_video_views'; // Lifetime, Day
    case POST_VIDEO_VIEWS_UNIQUE = 'post_video_views_unique'; // Lifetime, Day
    case POST_VIDEO_VIEWS_AUTOPLAYED = 'post_video_views_autoplayed'; // Lifetime
    case POST_VIDEO_VIEWS_CLICKED_TO_PLAY = 'post_video_views_clicked_to_play'; // Lifetime
    case POST_VIDEO_VIEWS_15S = 'post_video_views_15s'; // Lifetime
    case POST_VIDEO_VIEWS_60S_EXCLUDES_SHORTER = 'post_video_views_60s_excludes_shorter'; // Lifetime, Day
    case POST_VIDEO_VIEWS_10S = 'post_video_views_10s'; // Lifetime, Day (Deprecated)
    case POST_VIDEO_VIEWS_10S_UNIQUE = 'post_video_views_10s_unique'; // Lifetime (Deprecated)
    case POST_VIDEO_VIEWS_10S_AUTOPLAYED = 'post_video_views_10s_autoplayed'; // Lifetime (Deprecated)
    case POST_VIDEO_VIEWS_10S_CLICKED_TO_PLAY = 'post_video_views_10s_clicked_to_play'; // Lifetime (Deprecated)
    case POST_VIDEO_VIEWS_10S_ORGANIC = 'post_video_views_10s_organic'; // Lifetime (Deprecated)
    case POST_VIDEO_VIEWS_10S_PAID = 'post_video_views_10s_paid'; // Lifetime, Day (Deprecated)
    case POST_VIDEO_VIEWS_10S_SOUND_ON = 'post_video_views_10s_sound_on'; // Lifetime (Deprecated)
    case POST_VIDEO_VIEWS_SOUND_ON = 'post_video_views_sound_on'; // Lifetime
    case POST_VIDEO_VIEW_TIME = 'post_video_view_time'; // Lifetime, Day
    case POST_VIDEO_VIEW_TIME_ORGANIC = 'post_video_view_time_organic'; // Lifetime, Day
    case POST_VIDEO_VIEW_TIME_BY_AGE_BUCKET_AND_GENDER = 'post_video_view_time_by_age_bucket_and_gender'; // Lifetime
    case POST_VIDEO_VIEW_TIME_BY_REGION_ID = 'post_video_view_time_by_region_id'; // Lifetime, Day
    case POST_VIDEO_VIEWS_BY_DISTRIBUTION_TYPE = 'post_video_views_by_distribution_type'; // Lifetime
    case POST_VIDEO_VIEW_TIME_BY_DISTRIBUTION_TYPE = 'post_video_view_time_by_distribution_type'; // Lifetime
    case POST_VIDEO_VIEW_TIME_BY_COUNTRY_ID = 'post_video_view_time_by_country_id'; // Lifetime
    case POST_VIDEO_VIEWS_LIVE = 'post_video_views_live'; // Lifetime
    case POST_VIDEO_SOCIAL_ACTIONS_COUNT_UNIQUE = 'post_video_social_actions_count_unique'; // Lifetime, Day
    case POST_VIDEO_PLAY_COUNT = 'post_video_play_count'; // Lifetime
    case POST_VIDEO_LIVE_CURRENT_VIEWERS = 'post_video_live_current_viewers'; // Lifetime
    case POST_VIDEO_15S_TO_60S_EXCLUDES_SHORTER_VIEWS_RATE = 'post_video_15s_to_60s_excludes_shorter_views_rate'; // Lifetime
    case POST_VIDEO_VIEWS_BY_LIVE_STATUS = 'post_video_views_by_live_status'; // Lifetime

    // Stories
    case POST_ACTIVITY_BY_ACTION_TYPE = 'post_activity_by_action_type'; // Lifetime
    case POST_ACTIVITY_BY_ACTION_TYPE_UNIQUE = 'post_activity_by_action_type_unique'; // Lifetime

    // Video Ad Breaks
    case PAGE_DAILY_VIDEO_AD_BREAK_AD_IMPRESSIONS_BY_CROSSPOST_STATUS = 'page_daily_video_ad_break_ad_impressions_by_crosspost_status'; // Day
    case PAGE_DAILY_VIDEO_AD_BREAK_CPM_BY_CROSSPOST_STATUS = 'page_daily_video_ad_break_cpm_by_crosspost_status'; // Day
    case PAGE_DAILY_VIDEO_AD_BREAK_EARNINGS_BY_CROSSPOST_STATUS = 'page_daily_video_ad_break_earnings_by_crosspost_status'; // Day
    case POST_VIDEO_AD_BREAK_AD_IMPRESSIONS = 'post_video_ad_break_ad_impressions'; // Day, Lifetime
    case POST_VIDEO_AD_BREAK_EARNINGS = 'post_video_ad_break_earnings'; // Day, Lifetime
    case POST_VIDEO_AD_BREAK_AD_CPM = 'post_video_ad_break_ad_cpm'; // Day, Lifetime
    case CREATOR_MONETIZATION_QUALIFIED_VIEWS = 'creator_monetization_qualified_views'; // Day, Lifetime


    // Custom Methods to Get Metric Name
    public function getMetricName(): string {
        return $this->value;
    }
}