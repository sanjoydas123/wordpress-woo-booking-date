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

// add_action('admin_footer', 'inject_custom_columns_with_js');
// function inject_custom_columns_with_js()
// {
//     $screen = get_current_screen();
//     if ($screen->id === 'edit-shop_order') {
// 
?>
// <script>
    //             jQuery(document).ready(function($) {
    //                 // Add new columns headers
    //                 $('thead tr, tfoot tr').each(function() {
    //                     $(this).append('<th class="custom-tour-dates">Tour Dates</th>');
    //                     $(this).append('<th class="custom-tour-members">Tour Members</th>');
    //                 });

    //                 // Populate new columns in rows
    //                 $('tbody tr').each(function() {
    //                     const orderId = $(this).find('.check-column input').val();
    //                     const tourDates = 'Loading...'; // Placeholder
    //                     const tourMembers = 'Loading...'; // Placeholder

    //                     // Append new columns with placeholders
    //                     $(this).append('<td>' + tourDates + '</td>');
    //                     $(this).append('<td>' + tourMembers + '</td>');

    //                     // Fetch data via AJAX
    //                     $.post(ajaxurl, {
    //                         action: 'fetch_order_meta',
    //                         order_id: orderId
    //                     }, function(response) {
    //                         const data = JSON.parse(response);
    //                         if (data.success) {
    //                             $(this).find('.custom-tour-dates').text(data.tour_dates || '-');
    //                             $(this).find('.custom-tour-members').text(data.tour_members || '-');
    //                         }
    //                     });
    //                 });
    //             });
    //         
</script>
// <?php
//     }
// }

// // Handle the AJAX request to fetch order meta data
// add_action('wp_ajax_fetch_order_meta', 'fetch_order_meta');
// function fetch_order_meta()
// {
//     $order_id = intval($_POST['order_id']);
//     $tour_dates = get_post_meta($order_id, '_cust_tour_dates', true);
//     $tour_members = get_post_meta($order_id, '_cust_tour_members', true);

//     wp_send_json([
//         'success' => true,
//         'tour_dates' => $tour_dates,
//         'tour_members' => $tour_members
//     ]);
// }
