<?php


ob_start();

if (!class_exists('Payment_Processing')) {

    class Payment_Processing {

        public function __construct() {
            global $rcv_parameters;
            $rcv_parameters = array();
            $Payment_Processing = '';
            add_action('woocommerce_order_status_cancelled', array($this, 'custom_order_status_cancelled'));
            add_action('woocommerce_thankyou', array($this, 'custom_thankyou_page'));
            add_action('woocommerce_checkout_order_processed', array($this, 'action_woocommerce_new_order'), 1000);
            add_filter('woocommerce_order_status_pending_to_processing', array($this, 'custom_payment_complete'));
            add_action('woocommerce_payment_complete', array($this, 'custom_payment_complete'));
            add_action('woocommerce_order_status_on-hold', array($this, 'custom_payment_complete'));
            add_action('woocommerce_order_status_processing', array($this, 'custom_payment_complete'));
            add_filter('woocommerce_payment_complete_order_status', array($this, 'custom_payment_complete_order_status'), 10, 2);
            add_filter('woocommerce_billing_fields', array($this, 'woocommerce_billing_fields_callback'), 10, 1);
            add_filter('woocommerce_shipping_fields', array($this, 'woocommerce_shipping_fields_callback'), 10, 1);
            add_action('woocommerce_order_items_meta_display', array($this, 'woocommerce_order_items_meta_display_callback'), 10, 2);
            add_filter('woocommerce_cart_calculate_fees', array($this, 'woocommerce_cart_calculate_fees_callback'), 10, 1);
        }

        public function processing_payment($payment_args) {
            global $wpdb, $rcv_parameters, $woocommerce;
            $foodbakery_transaction_id = isset($payment_args['custom_var']['foodbakery_transaction_id']) ? $payment_args['custom_var']['foodbakery_transaction_id'] : 0;
            $order_id = get_post_meta($foodbakery_transaction_id, 'foodbakery_transaction_order_id', true);
            $price = get_post_meta($order_id, 'order_subtotal_price', true);
            if ($price != '') {
                $payment_args['price'] = get_post_meta($order_id, 'order_subtotal_price', true);
            }
            $rcv_parameters = $payment_args;
            extract($payment_args);

            if (isset($transaction_return_url) && $transaction_return_url != '') {
                $return_url = $transaction_return_url;
            }
            if (!isset($return_url) || $return_url == '') {
                $return_url = site_url();
            }

            $wpdb->query("DELETE " . $wpdb->prefix . "posts
			FROM " . $wpdb->prefix . "posts
			INNER JOIN " . $wpdb->prefix . "postmeta ON " . $wpdb->prefix . "postmeta.post_id = " . $wpdb->prefix . "posts.ID
			WHERE (" . $wpdb->prefix . "postmeta.meta_key = 'referance_ID' AND " . $wpdb->prefix . "postmeta.meta_value = '" . $package_id . "')");

            $post = array(
                'post_author' => 1,
                'post_content' => '',
                'post_status' => "publish",
                'post_title' => $package_name,
                'post_parent' => '',
                'post_type' => "product",
            );

            $post_id = wp_insert_post($post);

            update_post_meta($post_id, '_visibility', 'visible');
            update_post_meta($post_id, '_stock_status', 'instock');
            update_post_meta($post_id, '_regular_price', $price);
            update_post_meta($post_id, 'referance_ID', $package_id);
            update_post_meta($post_id, '_price', $price);
            update_post_meta($post_id, 'rcv_parameters', $payment_args);
            update_post_meta($post_id, '_virtual', 'yes');
            update_post_meta($post_id, 'return_url', $return_url);
            update_post_meta($post_id, '_visibility', 'hidden');

            if (isset($woocommerce->cart)) {
                $woocommerce->cart->empty_cart();
                $woocommerce->cart->add_to_cart($post_id, 1);
            }
            $checkout_url = wc_get_checkout_url();

            echo "<script>window.top.location.href='$checkout_url';</script>";
            if (isset($rcv_parameters) && is_array($rcv_parameters) && isset($rcv_parameters['payment_gateway']) && $rcv_parameters['payment_gateway'] == 'square') {
                // Call Square payment processing here if needed.
            }
            die();
        }

        // ... (other methods)

        // Add any other necessary methods here

    }

    $Payment_Processing = new Payment_Processing();
}

ob_end_clean();
?>