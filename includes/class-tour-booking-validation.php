<?php

class Tour_Booking_Validation
{
    public function __construct()
    {
        add_action('woocommerce_checkout_process', [$this, 'validate_fields']);
        add_action('woocommerce_checkout_update_order_meta', [$this, 'save_fields']);
    }

    public function validate_fields()
    {
        $min_members = get_option('tour_min_group_size', 2);

        if (empty($_POST['cust_tour_dates'])) {
            wc_add_notice(__('Please select a date range for the tour.'), 'error');
        }

        if (empty($_POST['cust_tour_members']) || intval($_POST['cust_tour_members']) < $min_members) {
            wc_add_notice(
                sprintf(__('Please enter at least %d members.', 'skd-woo-booking-date'), $min_members),
                'error'
            );
        }

        if ($_POST['cust_tour_dates']) {
            $selected_dates = sanitize_text_field($_POST['cust_tour_dates']);
            if (!$this->validate_tour_dates($selected_dates)) {
                wc_add_notice(__('The selected dates are not available for booking.', 'skd-woo-booking-date'), 'error');
            }
        }
    }

    private function validate_tour_dates($selected_dates)
    {
        global $wpdb;

        if (empty($selected_dates)) {
            return false;
        }

        // Split the date range
        $date_range = explode(' to ', $selected_dates);
        if (count($date_range) !== 2) {
            return false;
        }

        $start_date = DateTime::createFromFormat('d-m-Y', trim($date_range[0]));
        $end_date = DateTime::createFromFormat('d-m-Y', trim($date_range[1]));

        if (!$start_date || !$end_date) {
            return false;
        }

        $start_date = $start_date->format('Y-m-d');
        $end_date = $end_date->format('Y-m-d');

        // Check if the selected dates overlap with existing bookings
        $existing_orders = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT post_id FROM {$wpdb->postmeta}
             WHERE meta_key = '_cust_tour_dates'
             AND (
                 meta_value LIKE %s
                 OR meta_value LIKE %s
                 OR meta_value LIKE %s
             )",
                "%$start_date%",
                "%$end_date%",
                "%$start_date to $end_date%"
            )
        );

        return empty($existing_orders);
    }

    public function save_fields($order_id)
    {
        if (!empty($_POST['cust_tour_dates'])) {
            $selected_dates = sanitize_text_field($_POST['cust_tour_dates']);

            // Parse dates
            $date_range = explode(' to ', $selected_dates);
            if (count($date_range) === 2) {
                $start_date = DateTime::createFromFormat('d-m-Y', trim($date_range[0]));
                $end_date = DateTime::createFromFormat('d-m-Y', trim($date_range[1]));

                if ($start_date && $end_date) {
                    $formatted_dates = $start_date->format('Y-m-d') . ' to ' . $end_date->format('Y-m-d');
                    update_post_meta($order_id, '_cust_tour_dates', $formatted_dates);
                }
            }
        }

        if (!empty($_POST['cust_tour_members'])) {
            update_post_meta($order_id, '_cust_tour_members', intval($_POST['cust_tour_members']));
        }
    }
}
