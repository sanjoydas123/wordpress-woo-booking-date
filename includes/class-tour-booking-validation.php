<?php

class Tour_Booking_Validation
{
    public function __construct()
    {
        add_action('woocommerce_checkout_process', [$this, 'validate_fields']);
        add_action('woocommerce_checkout_update_order_meta', [$this, 'save_fields']);

        add_action('woocommerce_before_cart_item_quantity_zero', [$this, 'clear_cart_before_add'], 10, 1);
        add_filter('woocommerce_add_to_cart_validation', [$this, 'clear_cart_before_add'], 10, 2);
        add_filter('woocommerce_add_to_cart', [$this, 'ensure_single_product_in_cart'], 10, 6);
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
        $order = wc_get_order($order_id); // Get the order object

        if (!empty($_POST['cust_tour_dates'])) {
            $selected_dates = sanitize_text_field($_POST['cust_tour_dates']);

            // Parse dates
            $date_range = explode(' to ', $selected_dates);
            if (count($date_range) === 2) {
                $start_date = DateTime::createFromFormat('d-m-Y', trim($date_range[0]));
                $end_date = DateTime::createFromFormat('d-m-Y', trim($date_range[1]));

                if ($start_date && $end_date) {
                    $formatted_dates = $start_date->format('Y-m-d') . ' to ' . $end_date->format('Y-m-d');
                    $order->update_meta_data('_cust_tour_dates', $formatted_dates);
                }
            }
        }

        if (!empty($_POST['cust_tour_members'])) {
            $order->update_meta_data('_cust_tour_members', intval($_POST['cust_tour_members']));
        }

        $order->save();
    }

    // Clear the cart before adding a new product
    public function clear_cart_before_add($passed, $product_id)
    {
        // Empty the cart before adding a new product
        WC()->cart->empty_cart();

        // Return true to continue adding the product
        return $passed;
    }

    // Ensure only one product with quantity 1 in the cart
    public function ensure_single_product_in_cart($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data)
    {
        $cart = WC()->cart;
        // Set the quantity to 1 for the added product
        $cart->set_quantity($cart_item_key, 1);

        return $cart_item_key;
    }
}
