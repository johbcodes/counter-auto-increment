<?php
/*
Plugin Name: Counter Auto Increment with Animation
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: This plugin auto-increments the daily counters for hours saved, grants approved, countries operated in, and projects completed on time, with animated counters.
Version: 1.0
Author: Joseah Biwott
Author URI: http://linkedin.com/in/yoosinpaddy
License: MIT
*/

/**
 * Increment daily counters
 */
function daily_counters_increment()
{
    // Client Hours Saved Counter
    $current_hours_saved = get_option('wp_daily_hours_saved_counter', 80000);
    $current_hours_saved += rand(1, 10); // Increment by 1-10 daily
    update_option('wp_daily_hours_saved_counter', $current_hours_saved);

    // Government Grants Approved Counter
    $current_grants_approved = get_option('wp_daily_grants_approved_counter', 300);
    $current_grants_approved += rand(0, 1); // Increment by 0-1 daily
    update_option('wp_daily_grants_approved_counter', $current_grants_approved);

    // Countries Operated In Counter
    $current_countries_operated = get_option('wp_daily_countries_operated_counter', 20);
    update_option('wp_daily_countries_operated_counter', $current_countries_operated);

    // Projects Completed on Time Counter
    $current_projects_completed = get_option('wp_daily_projects_completed_counter', 650);
    $current_projects_completed += rand(0, 3); // Increment by 0-3 daily
    update_option('wp_daily_projects_completed_counter', $current_projects_completed);
}

/**
 * Schedule the daily event
 */
add_action('wp', 'my_custom_cron_schedule');
function my_custom_cron_schedule()
{
    if (!wp_next_scheduled('wp_daily_counters_event')) {
        wp_schedule_event(time(), 'daily', 'wp_daily_counters_event');
    }
}

/**
 * Hook the event to the counters increment function
 */
add_action('wp_daily_counters_event', 'daily_counters_increment');

/**
 * Display Counters Shortcodes with Animation
 */
function display_counter_shortcode($atts)
{
    $atts = shortcode_atts([
        'option' => 'wp_daily_hours_saved_counter',
        'default' => 0
    ], $atts);

    $counter_value = get_option($atts['option'], $atts['default']);
    return '<span class="animated-counter" data-count="' . esc_attr($counter_value) . '">0+</span>';
}

add_shortcode('display_hours_saved_counter', function () {
    return display_counter_shortcode(['option' => 'wp_daily_hours_saved_counter', 'default' => 80000]);
});

add_shortcode('display_grants_approved_counter', function () {
    return display_counter_shortcode(['option' => 'wp_daily_grants_approved_counter', 'default' => 300]);
});

add_shortcode('display_countries_operated_counter', function () {
    return display_counter_shortcode(['option' => 'wp_daily_countries_operated_counter', 'default' => 20]);
});

add_shortcode('display_projects_completed_counter', function () {
    return display_counter_shortcode(['option' => 'wp_daily_projects_completed_counter', 'default' => 650]);
});

/**
 * Enqueue scripts for counter animation
 */
function enqueue_counter_scripts()
{
    wp_enqueue_script('counter-animation', plugin_dir_url(__FILE__) . 'counter-animation.js', ['jquery'], null, true);
    wp_enqueue_style('counter-animation-style', plugin_dir_url(__FILE__) . 'counter-animation.css');
}
add_action('wp_enqueue_scripts', 'enqueue_counter_scripts');
