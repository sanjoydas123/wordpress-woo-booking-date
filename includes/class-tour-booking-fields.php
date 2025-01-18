<?php

class Tour_Booking_Fields
{
    public function __construct()
    {
        add_action('woocommerce_after_order_notes', [$this, 'add_fields']);
    }

    public function add_fields($checkout)
    {
        echo '<div id="tour_booking_fields"><h3>' . __('Tour Booking Details') . '</h3>';

        // Date range picker
        woocommerce_form_field('cust_tour_dates', [
            'type'        => 'text',
            'class'       => ['form-row-wide'],
            'label'       => __('Tour Dates'),
            'required'    => true,
            'placeholder' => __('Select date range'),
        ], $checkout->get_value('cust_tour_dates'));

        echo '<div id="booking-date-summary" style="margin-top: 10px; font-weight: bold; color: #333;"></div>';

        // Number of members
        woocommerce_form_field('cust_tour_members', [
            'type'              => 'number',
            'class'             => ['form-row-wide'],
            'label'             => __('Number of Members'),
            'required'          => true,
            'placeholder'       => __('Enter number of members'),
            'custom_attributes' => ['min' => 2],
        ], $checkout->get_value('cust_tour_members'));

        // Add the booking summary section
        echo '<div id="booking-summary" style="margin-top: 10px; font-weight: bold; color: #333;"></div>';

        echo '</div>';
    }
}
