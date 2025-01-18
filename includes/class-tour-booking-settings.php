<?php

class Tour_Booking_Settings
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function add_settings_page()
    {
        add_menu_page(
            __('Tour Booking Settings', 'tour-booking-plugin'),
            __('Tour Booking', 'tour-booking-plugin'),
            'manage_options',
            'tour-booking-settings',
            [$this, 'render_settings_page'],
            'dashicons-calendar-alt'
        );
    }

    public function register_settings()
    {
        register_setting('tour_booking_settings', 'tour_min_group_size');
        // register_setting('tour_booking_settings', 'tour_date_format');
        register_setting('tour_booking_settings', 'tour_calendar_day_count');

        add_settings_section(
            'general_settings',
            __('General Settings', 'tour-booking-plugin'),
            null,
            'tour-booking-settings'
        );

        add_settings_field(
            'tour_min_group_size',
            __('Minimum Group Size', 'tour-booking-plugin'),
            [$this, 'render_min_group_size_field'],
            'tour-booking-settings',
            'general_settings'
        );

        // add_settings_field(
        //     'tour_date_format',
        //     __('Date Format', 'tour-booking-plugin'),
        //     [$this, 'render_date_format_field'],
        //     'tour-booking-settings',
        //     'general_settings'
        // );

        add_settings_field(
            'tour_calendar_day_count',
            __('Day Count', 'tour-booking-plugin'),
            [$this, 'render_calendar_day_count'],
            'tour-booking-settings',
            'general_settings'
        );
    }

    public function render_settings_page()
    {
        echo '<div class="wrap">';
        echo '<h1>' . __('Tour Booking Settings', 'tour-booking-plugin') . '</h1>';
        echo '<form method="post" action="options.php">';
        settings_fields('tour_booking_settings');
        do_settings_sections('tour-booking-settings');
        submit_button();
        echo '</form>';
        echo '</div>';
    }

    public function render_min_group_size_field()
    {
        $value = get_option('tour_min_group_size', 2);
        echo '<input type="number" name="tour_min_group_size" value="' . esc_attr($value) . '" min="1">';
    }

    // public function render_date_format_field()
    // {
    //     $value = get_option('tour_date_format', 'd-m-Y');
    //     echo '<input type="text" name="tour_date_format" value="' . esc_attr($value) . '">';
    //     echo '<p class="description">' . __('Example: d-m-Y for day-month-year.', 'tour-booking-plugin') . '</p>';
    // }

    public function render_calendar_day_count()
    {
        $value = get_option('tour_calendar_day_count', '');
        echo '<input type="text" name="tour_calendar_day_count" value="' . esc_attr($value) . '">';
        echo '<p class="description">' . __('This count will be add to the selected booking date', 'tour-booking-plugin') . '</p>';
    }
}
