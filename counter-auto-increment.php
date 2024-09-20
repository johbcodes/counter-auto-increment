<?php
/*
Plugin Name: Counter Auto Increment with Animation
Plugin URI: https://github.com/johbcodes/counter-auto-increment
Description: This plugin auto-increments the daily counters for hours saved, grants approved, countries operated in, and projects completed on time, with animated counters.
Version: 1.0
Author: Joseah Biwott
Author URI: https://www.linkedin.com/in/joseahbiwott
License: MIT
*/

function daily_counters_increment()
{
    $current_hours_saved = get_option('wp_daily_hours_saved_counter', 80000);
    $current_hours_saved += 10;
    update_option('wp_daily_hours_saved_counter', $current_hours_saved);

    $current_grants_approved = get_option('wp_daily_grants_approved_counter', 300);
    $current_grants_approved += 5;
    update_option('wp_daily_grants_approved_counter', $current_grants_approved);

    $current_countries_operated = get_option('wp_daily_countries_operated_counter', 20);
    update_option('wp_daily_countries_operated_counter', $current_countries_operated);

    $current_projects_completed = get_option('wp_daily_projects_completed_counter', 650);
    $current_projects_completed += 10;
    update_option('wp_daily_projects_completed_counter', $current_projects_completed);
}

add_action('wp', 'my_custom_cron_schedule');

function my_custom_cron_schedule()
{
    if (!wp_next_scheduled('wp_daily_counters_event')) {
        wp_schedule_event(time(), 'daily', 'wp_daily_counters_event');
    }
}

add_action('wp_daily_counters_event', 'daily_counters_increment');

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

function enqueue_counter_scripts()
{
    wp_enqueue_script('counter-animation', plugin_dir_url(__FILE__) . 'counter-animation.js', ['jquery'], null, true);
    wp_enqueue_style('counter-animation-style', plugin_dir_url(__FILE__) . 'counter-animation.css');
}
add_action('wp_enqueue_scripts', 'enqueue_counter_scripts');

function check_and_notify_cron_scheduling()
{
    if (!wp_next_scheduled('wp_daily_counters_event')) {
        add_action('admin_notices', 'display_cron_schedule_admin_notice');
    }
}
add_action('admin_init', 'check_and_notify_cron_scheduling');

function display_cron_schedule_admin_notice()
{
?>
    <div class="notice notice-warning is-dismissible">
        <p><?php _e('The "Daily Counters Increment" cron job is not currently scheduled.', 'text-domain'); ?></p>
        <form method="post" action="">
            <?php submit_button('Schedule Now', 'primary', 'schedule_cron_event'); ?>
        </form>
    </div>
    <?php
}

function schedule_cron_job_on_button_click()
{
    if (isset($_POST['schedule_cron_event'])) {
        if (!wp_next_scheduled('wp_daily_counters_event')) {
            wp_schedule_event(time(), 'daily', 'wp_daily_counters_event');
        }

        wp_redirect(add_query_arg('cron_scheduled', '1', $_SERVER['REQUEST_URI']));
        exit;
    }
}
add_action('admin_init', 'schedule_cron_job_on_button_click');

function show_cron_scheduled_confirmation()
{
    if (isset($_GET['cron_scheduled']) && $_GET['cron_scheduled'] == '1') {
    ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('The "Daily Counters Increment" cron job has been successfully scheduled.', 'text-domain'); ?></p>
        </div>
<?php
    }
}
add_action('admin_notices', 'show_cron_scheduled_confirmation');
