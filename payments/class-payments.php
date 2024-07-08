<?php

global $gateways;
/**
 *  File Type: Payments Base Class
 *
 */
if (!class_exists('FOODBAKERY_PAYMENTS')) {

    class FOODBAKERY_PAYMENTS {

        public $gateways;

        public function __construct() {
            global $gateways;
            $gateways['FOODBAKERY_PAYPAL_GATEWAY'] = 'Paypal';
            $gateways['FOODBAKERY_AUTHORIZEDOTNET_GATEWAY'] = 'Authorize.net';
            $gateways['FOODBAKERY_PRE_BANK_TRANSFER'] = 'Pre Bank Transfer';
            $gateways['FOODBAKERY_SKRILL_GATEWAY'] = 'Skrill-MoneyBooker';
            $gateways['FOODBAKERY_SQUARE_GATEWAY'] = 'Square';
        }

        // Start function currency general setting 

        public function foodbakery_general_settings() {
            global $foodbakery_settings, $foodbakery_plugin_options;
            $base_currency = isset($foodbakery_plugin_options['foodbakery_base_currency']) ? $foodbakery_plugin_options['foodbakery_base_currency'] : 'USD';
            $currencies = array();
            $foodbakery_currencuies = foodbakery_get_currencies();
            if (is_array($foodbakery_currencuies)) {
                foreach ($foodbakery_currencuies as $key => $value) {
                    $currencies[$key] = $value['name'] . '-' . $value['code'];
                }
            }
            $foodbakery_settings[] = array(
                "name" => esc_html__("Base Currency", "foodbakery"),
                "desc" => "",
                "hint_text" => esc_html__("All the transactions will be placed in this currency.", "foodbakery"),
                "id" => "base_currency",
                "std" => "USD",
                'classes' => 'dropdown chosen-select-no-single base-currency-change',
                "type" => "select_values",
                "options" => $currencies
            );

            $foodbakery_settings[] = array(
                "name" => esc_html__("Currency Alignment", "foodbakery"),
                "desc" => "",
                "id" => "currency_alignment",
                "std" => "Left",
                'classes' => 'dropdown chosen-select-no-single',
                "type" => "select",
                "custom" => true,
                "options" => array('Left' => 'Left', 'Right' => 'Right'),
            );

            return $foodbakery_settings;
        }

        // Start function get string length

        public function foodbakery_get_string($length = 3) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }
            return $randomString;
        }

        // Start function for add transaction 

        public function foodbakery_add_transaction($fields = array()) {
            global $foodbakery_plugin_options;
            define("DEBUG", 1);
            define("USE_SANDBOX", 1);
            define("LOG_FILE", "./ipn.log");
            include_once('../../../../wp-load.php');
            if (is_array($fields)) {
                foreach ($fields as $key => $value) {
                    update_post_meta((int)$fields['foodbakery_transaction_id'], "$key", $value);
                }
            }
            return true;
        }

        // Square payment processing

        public function process_square_payment($order_id) {
            $order = wc_get_order($order_id);

            // Get payment nonce from the form
            $nonce = $_POST['square_nonce'];

            // Square API request to create payment
            $amount = $order->get_total() * 100; // Amount in cents
            $currency = get_woocommerce_currency();

            $client = new \Square\SquareClient([
                'accessToken' => $this->get_option('square_access_token'),
                'environment' => 'sandbox' // or 'production'
            ]);

            $payments_api = $client->getPaymentsApi();
            $money = new \Square\Models\Money();
            $money->setAmount($amount);
            $money->setCurrency($currency);

            $create_payment_request = new \Square\Models\CreatePaymentRequest($nonce, uniqid(), $money);

            try {
                $response = $payments_api->createPayment($create_payment_request);
                $payment = $response->getResult()->getPayment();

                // Mark order as complete
                $order->payment_complete($payment->getId());
                $order->add_order_note('Square payment completed. Transaction ID: ' . $payment->getId());

                // Reduce stock levels
                $order->reduce_order_stock();

                // Empty the cart
                WC()->cart->empty_cart();

                return array(
                    'result' => 'success',
                    'redirect' => $this->get_return_url($order)
                );

            } catch (\Square\Exceptions\ApiException $e) {
                wc_add_notice('Payment error: ' . $e->getMessage(), 'error');
                return array(
                    'result' => 'fail',
                    'redirect' => ''
                );
            }
        }
    }

    $foodbakery_payments = new FOODBAKERY_PAYMENTS();
}
