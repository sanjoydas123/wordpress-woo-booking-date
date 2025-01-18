<?php

class Tour_Booking_Display
{
    public function __construct()
    {
        add_action('woocommerce_admin_order_data_after_billing_address', [$this, 'display_in_admin'], 10, 1);
        add_action('woocommerce_order_details_after_order_table', [$this, 'display_in_user_order'], 10, 1);

        // Add custom columns to the orders list page in Admin
        add_filter('manage_woocommerce_page_wc-orders_columns', [$this, 'add_custom_order_columns']);
        add_action('manage_woocommerce_page_wc-orders_custom_column', [$this, 'populate_custom_order_columns'], 10, 2);
    }

    public function display_in_admin($order)
    {
        $cust_tour_dates = get_post_meta($order->get_id(), '_cust_tour_dates', true);
        $cust_tour_members = get_post_meta($order->get_id(), '_cust_tour_members', true);
        echo '<h2>' . __('Tour Booking Details') . '</h2>';
        if ($cust_tour_dates) {
            $parsed_dates = explode(' to ', $cust_tour_dates);
            if (count($parsed_dates) === 2) {
                $start_date = DateTime::createFromFormat('Y-m-d', trim($parsed_dates[0]));
                $end_date = DateTime::createFromFormat('Y-m-d', trim($parsed_dates[1]));
                echo '<p><strong>' . __('Tour Dates', 'skd-woo-booking-date') . ':</strong> ' .
                    $start_date->format('d M Y') . ' - ' . $end_date->format('d M Y') . '</p>';
            } else {
                echo '<p><strong>' . __('Tour Dates', 'skd-woo-booking-date') . ':</strong> ' . $cust_tour_dates . '</p>';
            }
        }
        if ($cust_tour_members) {
            echo '<p><strong>' . __('Number of Members') . ':</strong> ' . $cust_tour_members . '</p>';
        }
    }

    public function display_in_user_order($order)
    {
        $cust_tour_dates = get_post_meta($order->get_id(), '_cust_tour_dates', true);
        $convertDate = '';
        if ($cust_tour_dates) {
            $parsed_dates = explode(' to ', $cust_tour_dates);
            if (count($parsed_dates) === 2) {
                $start_date = DateTime::createFromFormat('Y-m-d', trim($parsed_dates[0]));
                $end_date = DateTime::createFromFormat('Y-m-d', trim($parsed_dates[1]));
                $convertDate = $start_date->format('d M Y') . ' - ' . $end_date->format('d M Y');
            } else {
                $convertDate = $cust_tour_dates;
            }
        }

        $cust_tour_members = get_post_meta($order->get_id(), '_cust_tour_members', true);

        if ($cust_tour_dates || $cust_tour_members) {
            echo '<h2>' . __('Tour Booking Details') . '</h2>';
            echo '<p><strong>' . __('Tour Dates') . ':</strong> ' . $convertDate . '</p>';
            echo '<p><strong>' . __('Number of Members') . ':</strong> ' . $cust_tour_members . '</p>';
        }
    }

    // Add custom columns to the order list page in the Admin
    public function add_custom_order_columns($columns)
    {
        // Insert new columns after 'order_status'
        $new_columns = [];
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            if ($key === 'order_status') {
                $new_columns['cust_tour_dates'] = __('Tour Dates', 'text-domain');
                $new_columns['cust_tour_members'] = __('Tour Members', 'text-domain');
            }
        }
        return $new_columns;
    }

    // Populate custom columns with data in the order list page
    public function populate_custom_order_columns($column, $order_id)
    {
        $order = wc_get_order($order_id);
        if ('cust_tour_dates' === $column) {
            $cust_tour_dates = $order->get_meta('_cust_tour_dates');

            if (!empty($cust_tour_dates)) {
                $parsed_dates = explode(' to ', $cust_tour_dates);
                if (count($parsed_dates) === 2) {
                    $start_date = DateTime::createFromFormat('Y-m-d', trim($parsed_dates[0]));
                    $end_date = DateTime::createFromFormat('Y-m-d', trim($parsed_dates[1]));
                    echo $start_date && $end_date
                        ? $start_date->format('d M Y') . ' - ' . $end_date->format('d M Y')
                        : esc_html($cust_tour_dates);
                } else {
                    echo esc_html($cust_tour_dates);
                }
            } else {
                echo '-';
            }
        }

        if ('cust_tour_members' === $column) {
            $cust_tour_members = $order->get_meta('_cust_tour_members');
            echo !empty($cust_tour_members)
                ? esc_html($cust_tour_members) . ' Members'
                : '-';
        }
    }
}
