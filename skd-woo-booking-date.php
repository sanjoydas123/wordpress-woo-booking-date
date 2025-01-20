<?php
/*
Plugin Name: SKD Woo Booking Date
Description: A custom plugin to add booking date field in WooCommerce product.
Version: 1.0
Author: SKD
Text Domain: skd-woo-booking-date
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define constants
define('TOUR_BOOKING_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('TOUR_BOOKING_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once TOUR_BOOKING_PLUGIN_PATH . 'includes/class-tour-booking-fields.php';
require_once TOUR_BOOKING_PLUGIN_PATH . 'includes/class-tour-booking-validation.php';
require_once TOUR_BOOKING_PLUGIN_PATH . 'includes/class-tour-booking-display.php';
require_once TOUR_BOOKING_PLUGIN_PATH . 'includes/class-tour-booking-settings.php';

// Initialize plugin features
add_action('plugins_loaded', function () {
    new Tour_Booking_Fields();
    new Tour_Booking_Validation();
    new Tour_Booking_Display();
    new Tour_Booking_Settings();
});

// Enqueue scripts and styles
add_action('wp_enqueue_scripts', function () {
    if (is_checkout()) {
        wp_enqueue_script('flatpickr', TOUR_BOOKING_PLUGIN_URL . 'assets/js/flatpickr.min.js', ['jquery'], null, true);
        // wp_enqueue_script('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr', array(), null, true);
        wp_enqueue_script('woo-booking', TOUR_BOOKING_PLUGIN_URL . 'assets/js/woo-booking.js', ['jquery', 'flatpickr'], null, true);
        wp_enqueue_style('woo-booking', TOUR_BOOKING_PLUGIN_URL . 'assets/css/woo-booking.css');
        wp_enqueue_style('flatpickr', TOUR_BOOKING_PLUGIN_URL . 'assets/css/flatpickr.min.css');
        // wp_enqueue_style('flatpickr', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');

        // Pass PHP data to JavaScript
        $tour_booking_config = [
            'minGroupSize' => get_option('tour_min_group_size', 2),
            'calDayCount' => get_option('tour_calendar_day_count', ''),
        ];

        wp_localize_script('woo-booking', 'tourBookingConfig', $tour_booking_config);
    }
});

// Load text domain for translations
add_action('init', function () {
    load_plugin_textdomain('tour-booking-plugin', false, dirname(plugin_basename(__FILE__)) . '/languages');
});
