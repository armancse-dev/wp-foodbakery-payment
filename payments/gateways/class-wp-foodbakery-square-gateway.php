<?php

/**
 *  File Type: Square Gateway
 *
 */
if ( ! class_exists('FOODBAKERY_SQUARE_GATEWAY') ) {

    class FOODBAKERY_SQUARE_GATEWAY extends FOODBAKERY_PAYMENTS {

        public function __construct() {
            global $foodbakery_gateway_options;

            $foodbakery_gateway_options = get_option('foodbakery_plugin_options');

            $foodbakery_lister_url = '';
            if ( isset($foodbakery_gateway_options['foodbakery_dir_square_ipn_url']) ) {
                $foodbakery_lister_url = $foodbakery_gateway_options['foodbakery_dir_square_ipn_url'];
            }

            if ( isset($foodbakery_gateway_options['foodbakery_square_sandbox']) && $foodbakery_gateway_options['foodbakery_square_sandbox'] == 'on' ) {
                $this->gateway_url = "https://connect.squareupsandbox.com/v2/payments";
            } else {
                $this->gateway_url = "https://connect.squareup.com/v2/payments";
            }
            $this->listner_url = $foodbakery_lister_url;
        }

        // Start function for Square setting 

        public function settings($foodbakery_gateways_id = '') {
            global $post;

            $foodbakery_rand_id = rand(10000000, 99999999);

            $on_off_option = array( "show" => esc_html__("on", "foodbakery"), "hide" => esc_html__("off", "foodbakery") );

            $foodbakery_settings[] = array(
                "name" => esc_html__("Square Settings", 'foodbakery'),
                "id" => "tab-heading-options",
                "std" => esc_html__("Square Settings", "foodbakery"),
                "type" => "section",
                "options" => "",
                "parrent_id" => "$foodbakery_gateways_id",
                "active" => true,
            );

            $foodbakery_settings[] = array( 
                "name" => esc_html__("Custom Logo ", "foodbakery"),
                "desc" => "",
                "hint_text" => "",
                "id" => "square_gateway_logo",
                "std" => wp_foodbakery::plugin_url() . 'payments/images/square.png',
                "display" => "none",
                "type" => "upload logo"
            );

            $foodbakery_settings[] = array( 
                "name" => esc_html__("Default Status", "foodbakery"),
                "desc" => "",
                "hint_text" => esc_html__("If this switch will be OFF, no payment will be processed via Square. ", "foodbakery"),
                "id" => "square_gateway_status",
                "std" => "on",
                "type" => "checkbox",
                "options" => $on_off_option
            );

            $foodbakery_settings[] = array( 
                "name" => esc_html__("Square Sandbox", "foodbakery"),
                "desc" => "",
                "hint_text" => esc_html__("Control Square sandbox Account with this switch. If this switch is set to ON, payments will be proceed with sandbox account.", "foodbakery"),
                "id" => "square_sandbox",
                "std" => "on",
                "type" => "checkbox",
                "options" => $on_off_option
            );

            $foodbakery_settings[] = array( 
                "name" => esc_html__("Square Application ID", "foodbakery"),
                "desc" => "",
                "hint_text" => esc_html__("Add your Square Application ID here.", "foodbakery"),
                "id" => "square_application_id",
                "std" => "",
                "type" => "text"
            );

            $foodbakery_settings[] = array( 
                "name" => esc_html__("Square Access Token", "foodbakery"),
                "desc" => "",
                "hint_text" => esc_html__("Add your Square Access Token here.", "foodbakery"),
                "id" => "square_access_token",
                "std" => "",
                "type" => "text"
            );

            $ipn_url = wp_foodbakery::plugin_url() . 'payments/listner.php';
            $foodbakery_settings[] = array( 
                "name" => esc_html__("Square IPN Url", "foodbakery"),
                "desc" => '',
                "hint_text" => esc_html__("Here you can add your Square IPN URL.", "foodbakery"),
                "id" => "dir_square_ipn_url",
                "std" => $ipn_url,
                "type" => "text"
            );

            return $foodbakery_settings;
        }

        // Start function for Square process request  

        public function foodbakery_proress_request($params = '') {
            global $post, $foodbakery_gateway_options, $foodbakery_form_fields;
            extract($params);

            $foodbakery_current_date = date('Y-m-d H:i:s');
            $output = '';
            $rand_id = $this->foodbakery_get_string(5);

            $foodbakery_package_title = get_the_title($transaction_package);
            $currency = foodbakery_get_base_currency();
            $transaction_amount = $transaction_amount * 100; // Convert to cents for Square API

            $return_url = isset( $transaction_return_url ) ? $transaction_return_url : esc_url( home_url( '/' ) );

            // Generate the request body for Square API
            $request_body = json_encode([
                'idempotency_key' => uniqid(),
                'amount_money' => [
                    'amount' => $transaction_amount,
                    'currency' => $currency
                ],
                'source_id' => $square_nonce, // Square Payment nonce from the frontend
                'note' => $foodbakery_package_title,
                'autocomplete' => true,
            ]);

            $output .= '<form name="SquareForm" id="direcotry-square-form" action="' . $this->gateway_url . '" method="post">
                        <input type="hidden" name="request_body" value=\'' . $request_body . '\'>
                        </form>'
                        . '<h3>' . __( 'Redirecting to payment gateway website...', 'foodbakery' ) . '</h3>';

            $data = force_balance_tags($output);
            $data .= '<script>
                          jQuery("#direcotry-square-form").submit();
                      </script>';
            echo force_balance_tags($data);
        }

        public function foodbakery_gateway_listner() {
            // Implement Square listener here
        }
    }
}
