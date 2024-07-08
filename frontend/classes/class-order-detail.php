<?php
/**
 * File Type: Order Detail
 */
if (!class_exists('Foodbakery_Order_Detail')) {

    class Foodbakery_Order_Detail {

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_action('wp_enqueue_scripts', array($this, 'foodbakery_order_element_scripts'), 11);
            add_action('foodbakery_order_detail', array($this, 'foodbakery_order_detail_callback'), 11, 2);
            add_action('wp_ajax_foodbakery_update_order_status', array($this, 'foodbakery_update_order_status_callback'), 10);
            add_action('foodbakery_order_cart_display', array($this, 'foodbakery_order_cart_display_callback'));
            add_action('foodbakery_order_service_total_fee', array($this, 'foodbakery_order_service_total_fee_callback'), 11);
            add_filter('foodbakery_trans_meta_array', array($this, 'foodbakery_trans_meta_array_callback'), 11, 2);
            add_filter('foodbakery_transaction_fields', array($this, 'foodbakery_transaction_fields_callback'), 11, 3);
        }
        
        public function foodbakery_order_service_total_fee_callback($trans_id){
            $menu_order_fee = get_post_meta($trans_id, 'menu_order_fee', true);
            $order_subtotal_price = get_post_meta($trans_id, 'order_subtotal_price', true);
            update_post_meta($trans_id, 'services_total_price', restaurant_menu_price_calc('defined', $order_subtotal_price, $menu_order_fee, true, false, false, $trans_id, false));
        }
        
         public function foodbakery_transaction_fields_callback($foodbakery_transaction_fields, $postData, $trans_id) {
            $menu_order_fee = get_post_meta($trans_id, 'menu_order_fee', true);
            $order_subtotal_price = get_post_meta($trans_id, 'order_subtotal_price', true);
            $transaction_amount = restaurant_menu_price_calc('defined', $order_subtotal_price, $menu_order_fee, true, false, false, $trans_id, false);
            update_post_meta($trans_id, 'services_total_price', $transaction_amount);
            $foodbakery_transaction_fields['transaction_amount'] = $transaction_amount;
            update_post_meta($trans_id, "foodbakery_transaction_amount", $transaction_amount);
            return $foodbakery_transaction_fields;
        }
        
        public function foodbakery_trans_meta_array_callback($trans_meta_arr, $postData) {
            $order_id = isset( $trans_meta_arr['foodbakery_order_id'] )? $trans_meta_arr['foodbakery_order_id'] : 0;
            
            $foodbakery_transaction_ptype   = get_post_meta( $order_id, 'foodbakery_transaction_ptype', true);
            if( $foodbakery_transaction_ptype   == 'add-restaurant'){
                return $trans_meta_arr;
            }
            
            
            $transaction_order_id = isset( $trans_meta_arr['transaction_order_id'] )? $trans_meta_arr['transaction_order_id'] : 0;
            $foodbakery_transaction_ptype   = get_post_meta( $transaction_order_id, 'foodbakery_transaction_ptype', true);
            if( $foodbakery_transaction_ptype   == 'add-restaurant'){
                return $trans_meta_arr;
            }
            $order_id   = ( $order_id > 0)? $order_id : $transaction_order_id;
            $menu_order_fee = get_post_meta($order_id, 'menu_order_fee', true);
            $order_subtotal_price = get_post_meta($order_id, 'order_subtotal_price', true);
            $transaction_amount = restaurant_menu_price_calc('defined', $order_subtotal_price, $menu_order_fee, true, false, false, $order_id, false);
            if( isset( $trans_id )){
                update_post_meta($trans_id, 'services_total_price', $transaction_amount);
            }
            $trans_meta_arr['transaction_amount'] = $transaction_amount;
            return $trans_meta_arr;
        }

        public function foodbakery_order_element_scripts() {
            wp_enqueue_script('foodbakery-orders-functions');
            wp_enqueue_script('jquery-mCustomScrollbar');
            wp_enqueue_style('jquery-mCustomScrollbar');
            wp_enqueue_script('jquery-print');
        }

        public function foodbakery_order_detail_callback($order_id = '', $type = 'my') {
            global $post, $foodbakery_var_options;
            $foodbakery_custom_logo = isset($foodbakery_var_options['foodbakery_var_custom_logo']) ? $foodbakery_var_options['foodbakery_var_custom_logo'] : '';
            if ($foodbakery_custom_logo != '') {
                $logo = $foodbakery_custom_logo;
            } else {
                $logo = esc_url(wp_foodbakery::plugin_url()) . '/assets/frontend/images/logo-classic.png';
            }

            if ($order_id == '') {
                $order_id = $post->ID;
            }

            $args = array(
                'post_type' => 'foodbakery-trans',
                'posts_per_page' => '1',
                'fields' => 'ids',
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key' => 'foodbakery_transaction_order_id',
                        'value' => $order_id,
                        'compare' => '=',
                    ),
                ),
            );
            $trans_query = new WP_Query($args);
            $trans_query_f = $trans_query->posts;

            $order_trans_f = isset($trans_query_f[0]) ? $trans_query_f[0] : 0;

            $pay_mode = get_post_meta($order_trans_f, 'foodbakery_transaction_pay_method', true);

            $payment_status_array = array(
                'pending' => foodbakery_plugin_text_srt('foodbakery_transaction_status_pending'),
                'approved' => foodbakery_plugin_text_srt('foodbakery_transaction_status_approved'),
                'cancelled' => foodbakery_plugin_text_srt('foodbakery_transaction_status_cancelled'),
                'Cancelled' => foodbakery_plugin_text_srt('foodbakery_transaction_status_cancelled'),
                'processing' => foodbakery_plugin_text_srt('foodbakery_status_processing'),
                'Processing' => foodbakery_plugin_text_srt('foodbakery_status_processing'),
                'Completed' => foodbakery_plugin_text_srt('foodbakery_status_completed'),
                'completed' => foodbakery_plugin_text_srt('foodbakery_status_completed'),
            );
            $order_status = get_post_meta($order_id, 'foodbakery_order_status', true);
            $payment_status_array[$order_status];
            $args = array(
                'post_type' => 'orders_inquiries',
                'post_status' => 'publish',
                'p' => $order_id,
            );
            $order_query = new WP_Query($args);
            while ($order_query->have_posts()): $order_query->the_post();

                $order_id = get_the_ID();
                $foodbakery_restaurant_id = get_post_meta($order_id, 'foodbakery_restaurant_id', true);
                $order_type = get_post_meta($order_id, 'foodbakery_order_type', true);
                $foodbakery_delivery_date = get_post_meta($order_id, 'foodbakery_delivery_date', true);
                $order_date = '';
                ?>
                <?php if ($type != 'my') { ?>
                    <div class="print-order-detail menu-order-detail order-detail"
                         id="print-order-det-<?php echo intval($order_id); ?>" style="display: none;">
                        <div class="logo"><img src="<?php echo esc_url($logo); ?>"
                                               alt="<?php esc_html(bloginfo('name')) ?>"/></div>
                        <h2><?php esc_html_e('Order Detail', 'foodbakery') ?></h2>
                        <div class="order-detail-inner">
                            <div class="description-holder">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <div class="list-detail-options has-checkbox">
                                            <h3>
                                                <?php echo get_the_title($foodbakery_restaurant_id); ?>
                                            </h3>
                                            <ul class="order-detail-options">
                                                <li class="order-number">
                                                    <strong><?php esc_html_e('Order ID:', 'foodbakery') ?></strong>
                                                    <span><?php echo esc_html($order_id); ?></span>
                                                </li>
                                                <?php $this->order_pick_delivery_time($order_id); ?>
                                                <li class="created-date">
                                                    <strong><?php esc_html_e('Delivery Date:', 'foodbakery') ?></strong>
                                                    <span>
                                                        <?php
                                                        if ($foodbakery_delivery_date != '') {
                                                            echo date_i18n('M j, Y h:i A', $foodbakery_delivery_date);
                                                        } else {
                                                            echo date_i18n('M j, Y h:i A', $order_date);
                                                        }
                                                        ?>
                                                    </span>
                                                </li>
                                                <li class="order-type">
                                                    <strong><?php esc_html_e('Type:', 'foodbakery') ?></strong>
                                                    <span><?php echo esc_html($order_type); ?></span>
                                                </li>
                                                <li class="order-type">
                                                    <strong><?php esc_html_e('Payment Status:', 'foodbakery') ?></strong>
                                                    <span><?php echo $payment_status_array[$this->order_payment_status($order_id)]; ?><?php echo($pay_mode == 'cash' ? ' <small><em>' . esc_html__('(Cash on Delivery)', 'foodbakery') . '</em></small>' : '') ?></span>
                                                </li>

                                            </ul>
                                        </div>
                                    </div>
                                    <?php
                                    // Order buyer info.
                                    $this->order_buyer_info($order_id);
                                    // Order status
                                    $this->print_order_status($order_id);
                                    // Order menu list.
                                    $this->order_menu_list($order_id);
                                    // Order price.
                                    $this->order_price($order_id);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="modal fade menu-order-detail order-detail" id="order-det-<?php echo intval($order_id); ?>"
                     tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                                <h2><?php esc_html_e('Order Detail', 'foodbakery') ?></h2>
                                <?php if ($type != 'my') { ?>
                                    <button class="btn-print"
                                            onclick="CallPrint('print-order-det-<?php echo($order_id); ?>');"><i
                                            class="icon-printer"></i><span><?php echo esc_html__('Receipt', 'foodbakery'); ?></span>
                                    </button>
                                    <script type="text/javascript">
                                        function CallPrint(divName) {
                                            var title = "<?php esc_html_e('Order Detail', 'foodbakery'); ?>";
                                            var stylesheet_url = "<?php echo esc_url(wp_foodbakery::plugin_url()) . '/assets/frontend/css/cm-print.css'; ?>";
                                            jQuery('#' + divName).show();
                                            jQuery("#" + divName).print({
                                                stylesheet: stylesheet_url,
                                                title: title,
                                            });
                                            jQuery('#' + divName).hide();
                                        }
                                    </script>
                                <?php } ?>
                            </div>
                            <div class="modal-body">
                                <div class="order-detail-inner">
                                    <div class="description-holder">
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <div class="list-detail-options has-checkbox">
                                                    <h3>
                                                        <?php echo get_the_title($foodbakery_restaurant_id); ?>
                                                    </h3>
                                                    <ul class="order-detail-options">
                                                        <li class="order-number">
                                                            <strong><?php esc_html_e('Order ID:', 'foodbakery') ?></strong>
                                                            <span><?php echo esc_html($order_id); ?></span>
                                                        </li>
                                                        <?php $this->order_pick_delivery_time($order_id); ?>
                                                        <li class="created-date">
                                                            <strong><?php esc_html_e('Delivery Date:', 'foodbakery') ?></strong>
                                                            <span>
                                                                <?php
                                                                if ($foodbakery_delivery_date != '') {
                                                                    echo date_i18n('M j, Y h:i A', $foodbakery_delivery_date);
                                                                } else {
                                                                    echo date_i18n('M j, Y h:i A', $order_date);
                                                                }
                                                                ?>
                                                            </span>
                                                        </li>
                                                        <li class="order-type">
                                                            <strong><?php esc_html_e('Type:', 'foodbakery') ?></strong>
                                                            <span><?php echo esc_html($order_type); ?></span>
                                                        </li>
                                                        <li class="order-type">
                                                            <strong><?php esc_html_e('Payment Status:', 'foodbakery') ?></strong>
                                                            <span><?php echo $payment_status_array[$this->order_payment_status($order_id)]; ?><?php echo($pay_mode == 'cash' ? ' <small><em>' . esc_html__('(Cash on Delivery)', 'foodbakery') . '</em></small>' : '') ?></span>
                                                        </li>

                                                    </ul>
                                                </div>

                                            </div>
                                            <?php
                                            // Order buyer info.

                                            $this->order_buyer_info($order_id);

                                            // Order Status.
                                            $this->order_status($order_id);

                                            // Order menu list.
                                            $this->order_menu_list($order_id);

                                            // Order price.
                                            $this->order_price($order_id);
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            endwhile;
            wp_reset_postdata();
            ?>
            <script>
                (function ($) {
                    $(document).ready(function () {
                        $(".order-detail .modal-dialog .modal-content").mCustomScrollbar({
                            setHeight: 550,
                            theme: "minimal-dark",
                            mouseWheelPixels: 100
                        });
                    });
                })(jQuery);
            </script>
            <?php
        }

        public function order_pick_delivery_time($order_id = '') {
            global $post;

            if ($order_id == '') {
                $order_id = $post->ID;
            }

            $menu_order_fee_type = get_post_meta($order_id, 'menu_order_fee_type', true);
            $order_restaurant_id = get_post_meta($order_id, 'foodbakery_restaurant_id', true);

            if ($menu_order_fee_type != '') {
                ?>
                <li class="req-delivery">
                    <?php
                    $delivery_time = get_post_meta($order_restaurant_id, 'foodbakery_delivery_time', true);
                    $pickup_time = get_post_meta($order_restaurant_id, 'foodbakery_restaurant_pickup_time', true);
                    if ($menu_order_fee_type == 'delivery' && is_numeric($delivery_time) && $delivery_time > 0) {
                        ?>
                        <strong><?php esc_html_e('Delivery Time:', 'foodbakery') ?></strong>
                        <span><?php echo($this->convertFromMinutes($delivery_time)); ?></span>
                    <?php } else if (is_numeric($pickup_time) && $pickup_time > 0) { ?>
                        <strong><?php esc_html_e('Pick Up Time:', 'foodbakery') ?></strong>
                        <span><?php echo($this->convertFromMinutes($pickup_time)); ?></span>
                    <?php } ?>
                </li>
                <?php
            }
        }

        public function convertFromMinutes($minutes) {
            $time = '';
            $days = floor($minutes / 1440);
            $hours = (int) ($minutes / 60);
            $minutes = $minutes - ($hours * 60);
            if ($days > 0) {
                $days_string = esc_html__(' day ', 'foodbakery');
                if ($days > 1) {
                    $days_string = esc_html__(' days ', 'foodbakery');
                }
                $time .= $days . $days_string;
            }
            if ($hours > 0) {
                $hours_string = esc_html__(' Hour ', 'foodbakery');
                if ($hours > 1) {
                    $hours_string = esc_html__(' Hours ', 'foodbakery');
                }
                $time .= $hours . $hours_string;
            }
            if ($minutes > 0) {
                $minutes_string = esc_html__(' Minute ', 'foodbakery');
                if ($minutes > 1) {
                    $minutes_string = esc_html__(' Minutes ', 'foodbakery');
                }
                $time .= $minutes . $minutes_string;
            }
            return $time;
        }

        public function order_payment_status($order_id = '') {
            global $post;

            if ($order_id == '') {
                $order_id = $post->ID;
            }

            $transaction_payment_status = 'pending';
            $transaction_order_id = $this->order_transaction_id($order_id);
            if ($transaction_order_id != '') {
                $transaction_status = get_post_meta($transaction_order_id, 'foodbakery_transaction_status', true);
                if ($transaction_status != '') {
                    $transaction_payment_status = $transaction_status;
                }
            }
            return $transaction_payment_status;
        }

        public function order_transaction_id($order_id = '') {
            global $post;

            if ($order_id == '') {
                $order_id = $post->ID;
            }

            $order_trans_id = '';
            $args = array(
                'post_type' => 'foodbakery-trans',
                'posts_per_page' => 1,
                'post_status' => 'publish',
                'orderby' => 'ID',
                'order' => 'ASC',
                'meta_query' => array(
                    array(
                        'key' => 'foodbakery_transaction_order_id',
                        'value' => $order_id,
                        'compare' => '=',
                    )
                ),
            );
            $order_trans = new WP_Query($args);
            if ($order_trans->have_posts()) {
                while ($order_trans->have_posts()): $order_trans->the_post();
                    $order_trans_id = get_the_ID();
                endwhile;
            }
            wp_reset_postdata();
            return $order_trans_id;
        }

        public function order_buyer_info($order_id = '') {
            global $post;

            if ($order_id == '') {
                $order_id = $post->ID;
            }

            $transaction_order_id = $this->order_transaction_id($order_id);
            if ($transaction_order_id != '') {
                $trans_first_name = get_post_meta($transaction_order_id, 'foodbakery_trans_first_name', true);
                $trans_last_name = get_post_meta($transaction_order_id, 'foodbakery_trans_last_name', true);
                $trans_email = get_post_meta($transaction_order_id, 'foodbakery_trans_email', true);
                $trans_phone_number = get_post_meta($transaction_order_id, 'foodbakery_trans_phone_number', true);
                $trans_address = get_post_meta($transaction_order_id, 'foodbakery_trans_address', true);
                if ($trans_first_name != '' || $trans_last_name != '' || $trans_email != '' || $trans_phone_number != '' || $trans_address != '') {
                    ?>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="customer-detail-holder">
                            <h3><?php esc_html_e('Customer Detail', 'foodbakery'); ?></h3>
                            <ul class="customer-detail">
                                <?php if ($trans_first_name != '' || $trans_last_name) { ?>
                                    <li>
                                        <strong><?php esc_html_e('Name :', 'foodbakery') ?></strong>
                                        <span><?php echo esc_html($trans_first_name) . ' ' . esc_html($trans_last_name); ?></span>
                                    </li>
                                <?php } ?>
                                <?php if ('' != $trans_phone_number) { ?>
                                    <li>
                                        <strong><?php esc_html_e('Phone Number :', 'foodbakery') ?></strong>
                                        <span><?php echo esc_html($trans_phone_number); ?></span>
                                    </li>
                                <?php } ?>
                                <?php if ('' != $trans_email) { ?>
                                    <li>
                                        <strong><?php esc_html_e('Email :', 'foodbakery') ?></strong>
                                        <span><?php echo esc_html($trans_email); ?></span>
                                    </li>
                                <?php } ?>
                                <?php
                                $flag_address = apply_filters('foodbakery_check_delivery_tax', false);
                                if ($flag_address) {
                                    do_action('foodbakery_show_delivery_details', $order_id);
                                } else {
                                    if ('' != $trans_address) {
                                        ?>
                                        <li>
                                            <strong><?php esc_html_e('Address :', 'foodbakery') ?></strong>
                                            <span><?php echo esc_html($trans_address); ?></span>
                                        </li>
                                        <?php
                                    }
                                }
                                ?>

                            </ul>
                        </div>
                    </div>
                    <?php
                }
            }
        }

        public function print_order_status($order_id = '') {
            global $post, $foodbakery_plugin_options, $foodbakery_form_fields, $current_user;

            if ($order_id == '') {
                $order_id = $post->ID;
            }

            $user_id = $current_user->ID;
            $publisher_id = foodbakery_company_id_form_user_id($user_id);
            $publisher_type = get_post_meta($publisher_id, 'foodbakery_publisher_profile_type', true);

            $order_status = get_post_meta($order_id, 'foodbakery_order_status', true);
            $order_status_color = $this->order_status_color($order_status);
            ?>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="order-status-holder">
                        <h3> <?php esc_html_e('Order Status:', 'foodbakery'); ?></h3>
                        <div class="order-status-process order-status">
                            <p><?php echo esc_html__($order_status); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }

        public function order_status($order_id = '') {
            global $post, $foodbakery_plugin_options, $foodbakery_form_fields, $current_user;

            if ($order_id == '') {
                $order_id = $post->ID;
            }

            $user_id = $current_user->ID;
            $publisher_id = foodbakery_company_id_form_user_id($user_id);
            $publisher_type = get_post_meta($publisher_id, 'foodbakery_publisher_profile_type', true);

            $order_status = get_post_meta($order_id, 'foodbakery_order_status', true);
            ?>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="order-status-holder">
                    <?php if ($publisher_type == 'restaurant') { ?>
                        <div class="row">
                            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                                <h3>
                                    <?php esc_html_e('Order Status', 'foodbakery'); ?>
                                </h3>
                            </div>
                            <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
                                <div class="input-field">
                                    <?php
                                    $orders_status = isset($foodbakery_plugin_options['orders_status']) ? $foodbakery_plugin_options['orders_status'] : '';
                                    if (is_array($orders_status) && sizeof($orders_status) > 0) {
                                        foreach ($orders_status as $key => $label) {
                                            $drop_down_options[$label] = esc_html__($label, 'foodbakery');
                                        }
                                    } else {
                                        $drop_down_options = array(
                                            'Processing' => esc_html__('Processing', 'foodbakery'),
                                            'Completed' => esc_html__('Completed', 'foodbakery'),
                                        );
                                    }
                                    $foodbakery_opt_array = array();
                                    $foodbakery_opt_array['std'] = $order_status;
                                    $foodbakery_opt_array['cust_id'] = 'order-inquiry-status';
                                    $foodbakery_opt_array['cust_name'] = 'order-inquiry-status';
                                    $foodbakery_opt_array['options'] = $drop_down_options;
                                    $foodbakery_opt_array['classes'] = 'chosen-select-no-single';
                                    $foodbakery_opt_array['extra_atr'] = ' onchange="foodbakery_update_order_status(this, \'' . $order_id . '\', \'' . admin_url('admin-ajax.php') . '\')"';
                                    $foodbakery_opt_array['return'] = false;
                                    $foodbakery_form_fields->foodbakery_form_select_render($foodbakery_opt_array);
                                    ?>
                                    <script type="text/javascript">
                                        jQuery(document).ready(function () {
                                            chosen_selectionbox();
                                        });
                                    </script>
                                    <span class="status-loader order-status-loader-<?php echo intval($order_id); ?>"></span>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <?php $order_status_color = $this->order_status_color($order_status); ?>
                        <div class="order-status-process order-status">
                            <p style="background:<?php echo esc_html($order_status_color); ?>;"><?php
                                $order_status = ucfirst($order_status);
                                $order_status = __($order_status, 'foodbakery');
                                printf(esc_html__('Your order is %s', 'foodbakery'), strtolower($order_status));
                                ?>
                            </p>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php
        }

        public function order_menu_list($order_id = '') {
            global $post;

            if ($order_id == '') {
                $order_id = $post->ID;
            }

            $order_menu_list = get_post_meta($order_id, 'menu_items_list', true);
            $currency_sign = get_post_meta($order_id, 'foodbakery_currency', true);
            if (is_array($order_menu_list)) {
                $menu_order_fee = get_post_meta($order_id, 'menu_order_fee', true);
                $menu_order_fee_type = get_post_meta($order_id, 'menu_order_fee_type', true);
                
                ob_start();
                do_action('foodbakery_order_details_column_titles');
                $foodbakery_order_details_column_titles = ob_get_clean();
                
                
                $payment_list = '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
                $payment_list .= '
				<h2 class="heading">' . esc_html__('Food Menu', 'foodbakery') . '</h2>
				<div class="responsive-table">    
				<ul class="categories-order table-generic">';
                $payment_list .= '<li class="order-heading-titles">
					<div>' . esc_html__('Products', 'foodbakery') . '</div>	
                                        '. $foodbakery_order_details_column_titles .'
					<div>' . esc_html__('Price per', 'foodbakery') . '</div>
				    </li>';
                $order_m_total = 0;
                foreach ($order_menu_list as $_menu_list) {
                    $title_item_cat = isset($_menu_list['category']) ? $_menu_list['category'] : '';
                    $title_item = isset($_menu_list['title']) ? $_menu_list['title'] : '';
                    $price_item = isset($_menu_list['price']) ? $_menu_list['price'] : '';
                    $extras_item = isset($_menu_list['extras']) ? $_menu_list['extras'] : '';
                    $menu_item_identity = isset($_menu_list['menu_item_identity']) ? $_menu_list['menu_item_identity'] : '';
                    $restaurant_id = isset($_menu_list['restaurant_id']) ? $_menu_list['restaurant_id'] : '';
                    $deal_id = isset($_menu_list['deal_id']) ? $_menu_list['deal_id'] : 0;
                    
                    $menu_deals = get_post_meta($restaurant_id, 'foodbakery_menu_deals', true);
                    $deal_items = isset($menu_deals[$deal_id]['deal_items']) ? $menu_deals[$deal_id]['deal_items'] : '';
                    $deal_items_array = (!is_array($deal_items))?explode(',', $deal_items) : $deal_items;
                    
                    
                    
                    $is_deal_item = isset($_menu_list['is_deal_item']) ? $_menu_list['is_deal_item'] : '';
                   
                    
                    ob_start();
                    do_action('foodbakery_order_details_column_values', $_menu_list);
                    $foodbakery_order_details_column_values = ob_get_clean();

                    $order_m_total += floatval($price_item);
                    
                    
                    $menu_item_title_html = '';
                    if( $is_deal_item == true){
                        
                        $title_item_cat = $title_item;
                        $title_item = '';

                        $Foodbakery_deal_backend = new Foodbakery_deal_backend();

                        if (!empty($deal_items_array)) {
                            foreach ($deal_items_array as $deal_item_id) {
                                $menu_item_data = $Foodbakery_deal_backend->foodbakery_get_menu_item($restaurant_id, $deal_item_id);

                                $menu_item_title_html .= isset( $menu_item_data['menu_item_title'] )? '<br>'.$menu_item_data['menu_item_title'] : '';
                            }
                        }
                        
                        $title_item .= $menu_item_title_html;
                        
                        
                        //$title_item = '';
                    }
                    
                    
                    
                    
                    
                    $payment_list .= '
					<li  class="order-heading-titles">
					<div>
						<h4>' . $title_item_cat . '</h4>
						<h5>' . $title_item . '</h5>
						';
                    $payment_list .= apply_filters('order_item_detail_after_title', '', $_menu_list);
                    if (is_array($extras_item) && sizeof($extras_item) > 0) {
                        $payment_list .= '<ul>';
                        foreach ($extras_item as $extra_item) {
                            //pre($extra_item, false);
                            $title_extra_item = isset($extra_item['title']) ? $extra_item['title'] : '';
                            $is_deal_item = isset($extra_item['is_deal_item']) ? $extra_item['is_deal_item'] : false;
                            $menu_item_title = isset($extra_item['menu_item_title']) ? strip_tags($extra_item['menu_item_title']) : '';
                            
                            $heading_extra_item = '';
                            if( $is_deal_item == true){
                                $heading_extra_item .= $menu_item_title.' - ';
                            }
                            $heading_extra_item .= isset($extra_item['heading']) ? $extra_item['heading'] : '';
                            
                            
                            $price_extra_item = isset($extra_item['price']) ? $extra_item['price'] : '';
                            if ($title_extra_item != '' || $price_extra_item > 0) {
                                $payment_list .= '<li>' . $heading_extra_item . ' - ' . $title_extra_item . ' : ' . foodbakery_get_currency($price_extra_item, true, '', '', false) . '</li>';
                            }
                            $order_m_total += floatval($price_extra_item);
                        }
                        $payment_list .= '</ul>';
                    }
                    $payment_list .= '</div>';
                    $payment_list .= $foodbakery_order_details_column_values;
                    $payment_list .= '<div>';
                    $payment_list .= '<span class="category-price">' . foodbakery_get_currency($price_item, true, '', '', false) . '</span>';
                    $payment_list .= '</div>';
                    $payment_list .= '
					</li>';
                }
                $payment_list .= '
					</ul></div>';
            }
            $payment_list .= '</div>';

            echo force_balance_tags($payment_list);
        }

        public function order_price($order_id = '') {
            global $post;

            if ($order_id == '') {
                $order_id = $post->ID;
            }

            $menu_order_fee_type = get_post_meta($order_id, 'menu_order_fee_type', true);
            $menu_order_fee = get_post_meta($order_id, 'menu_order_fee', true);
            $currency_sign = get_post_meta($order_id, 'foodbakery_currency', true);
            $order_subtotal_price = get_post_meta($order_id, 'order_subtotal_price', true);

            $services_total_price = get_post_meta($order_id, 'services_total_price', true);
            $order_vat_percent = get_post_meta($order_id, 'order_vat_percent', true);
            $order_vat_cal_price = get_post_meta($order_id, 'order_vat_cal_price', true);


            $wooc_order_all_data = get_post_meta($order_id, 'foodbakery_wooc_order_data', true);

            if ($order_subtotal_price != '' || ($menu_order_fee_type != '' && $menu_order_fee != '') || $order_vat_cal_price != '' || $services_total_price != '') {
                ?>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="row">
                        <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12">
                            <h3><?php echo esc_html__('Order Total', 'foodbakery'); ?></h3>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <ul class="order-detail-options order-total">
                                <?php if ($order_subtotal_price != '') { ?>
                                    <li class="created-date">
                                        <strong><?php esc_html_e('Subtotal:', 'foodbakery'); ?></strong>
                                        <span><?php echo foodbakery_get_currency($order_subtotal_price, true, '', '', false); ?></span>
                                    </li>
                                <?php } ?>
                                <?php
                                $check_delivery_fee = apply_filters('foodbakery_check_delivery_tax', false);
                                if (!$check_delivery_fee) {
                                    if (!isset($wooc_order_all_data) || empty($wooc_order_all_data)) {
                                        ?>
                                        <?php if ($menu_order_fee_type != '' && $menu_order_fee != '') { ?>
                                            <li class="order-type">
                                                <strong>
                                                    <?php
                                                    if ($menu_order_fee_type == 'delivery') {
                                                        esc_html_e('Delivery Fee:', 'foodbakery');
                                                    } else {
                                                        esc_html_e('Pick Up Fee:', 'foodbakery');
                                                    }
                                                    ?>
                                                </strong>
                                                <span><?php echo foodbakery_get_currency($menu_order_fee, true, '', '', false); ?></span>
                                            </li>
                                        <?php } ?>
                                        <?php if ($order_vat_cal_price != '') { ?>
                                            <li class="order-type">
                                                <strong><?php printf(esc_html__('VAT (%s&#37;)', 'foodbakery'), $order_vat_percent) ?></strong>
                                                <span><?php echo foodbakery_get_currency($order_vat_cal_price, true, '', '', false) ?></span>
                                            </li>
                                            <?php
                                        }
                                    }
                                    ?>
                                    <?php if (isset($wooc_order_all_data) && !empty($wooc_order_all_data)) {
                                        ?>
                                        <?php
                                        $pickup_fee_flag = false;
                                        foreach ($wooc_order_all_data as $wooc_order_data) {
                                            if (strip_tags($wooc_order_data['label']) == 'Pickup:' || strip_tags($wooc_order_data['label']) == 'Delivery:') {
                                                $pickup_fee_flag = true;
                                            }
                                            ?>
                                            <li class="order-type">
                                                <strong><?php echo esc_html(strip_tags($wooc_order_data['label'])); ?></strong>
                                                <?php
                                                // $value = number_format(floatval(strip_tags($wooc_order_data['value'])), 2);
                                                // $value = foodbakery_get_currency($value, true, '', '', false); 
                                                ?>
                                                <?php //echo esc_html($value); ?>
                                                <span><?php echo $wooc_order_data['value']; ?></span>
                                            </li>
                                        <?php } ?>

                                        <?php
                                        if ($pickup_fee_flag == false) {
                                            if ($menu_order_fee_type != '' && $menu_order_fee != '') {
                                                ?>
                                                <li class="order-type">
                                                    <strong>
                                                        <?php
                                                        if ($menu_order_fee_type == 'delivery') {
                                                            esc_html_e('Delivery Fee:', 'foodbakery');
                                                        } else {
                                                            esc_html_e('Pick Up Fee:', 'foodbakery');
                                                        }
                                                        ?>
                                                    </strong>
                                                    <span>
                                                        <?php echo foodbakery_get_currency($menu_order_fee, true, '', '', false); ?>
                                                    </span>
                                                </li>
                                                <?php
                                            }
                                        }
                                    }
                                } /* else {
                                  do_action('foodbakery_add_delivery_taxes_in_buyer_order_details', $order_id, $currency_sign);
                                  } */
                                ?>
                                                
                                <?php do_action('foodbakery_order_calculation_before_total', $order_id); ?>
                                                
                                                
                                                


                                <?php if ($services_total_price != '') {?>
                                    <li class="order-type total-price">
                                        <strong><?php esc_html_e('Total:', 'foodbakery') ?></strong>
                                        <span><?php
                                            if ($check_delivery_fee) {
                                                echo foodbakery_get_currency($services_total_price, true, '', '', false);
                                            } else {
                                                echo restaurant_menu_price_calc('defined', $order_subtotal_price, $menu_order_fee, true, false, false, $order_id, true);
                                            }
                                            ?></span>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php
            }
        }

        public function order_status_color($order_name = 'Processing') {
            global $foodbakery_plugin_options;

            $orders_status = isset($foodbakery_plugin_options['orders_status']) ? $foodbakery_plugin_options['orders_status'] : '';
            $orders_color = isset($foodbakery_plugin_options['orders_color']) ? $foodbakery_plugin_options['orders_color'] : '';
            if (is_array($orders_status) && sizeof($orders_status) > 0) {
                foreach ($orders_status as $key => $lable) {
                    if (strtolower($lable) == strtolower($order_name)) {
                        return $order_color = isset($orders_color[$key]) ? $orders_color[$key] : '';
                        break;
                    }
                }
            }
        }

        public function foodbakery_update_order_status_callback() {
            $json = array();

            $order_id = foodbakery_get_input('order_id', NULL, 'STRING');
            $order_status = foodbakery_get_input('order_status', NULL, 'STRING');

            if ($order_id && $order_status) {
                update_post_meta($order_id, 'foodbakery_order_status', $order_status);
                $order_paytype = get_post_meta($order_id, 'foodbakery_order_paytype', true);
                $order_comision_charge = get_post_meta($order_id, 'order_amount_charged', true);

                $earning_subtract = get_post_meta($order_id, 'earning_subtract_to_publisher', true);
                echo($earning_subtract);
                update_post_meta($order_id, 'earning_subtract_to_publisher', '');

                if ($earning_subtract != 'yes' && $order_status == 'Completed' && $order_paytype == 'cash' && $order_comision_charge > 0) {

                    $order_restaurant = get_post_meta($order_id, 'foodbakery_publisher_id', true);
                    $restaurant_subtracts = get_post_meta($order_restaurant, 'total_cash_subtracts', true);
                    if ($restaurant_subtracts < 0) {
                        $restaurant_subtracts = 0;
                    }

                    if (is_numeric($order_comision_charge) && is_numeric($restaurant_subtracts)) {
                        $restaurant_subtracts = $restaurant_subtracts + $order_comision_charge;
                    }
                    if (isset($restaurant_earnings)) {
                        update_post_meta($order_restaurant, 'total_cash_subtracts', $restaurant_earnings);
                    }


                    $order_restaurantsss = get_post_meta($order_restaurant, 'total_cash_subtracts', true);
                    echo($order_restaurantsss);

                    update_post_meta($order_id, 'earning_subtract_to_publisher', 'yes');
                }

                // Update order status email
                do_action('foodbakery_order_status_updated_email', $order_id);


                $json['type'] = "success";
                $json['msg'] = esc_html__("Order status has been changed.", "foodbakery");
                $json['status'] = esc_html($order_status);
            } else {
                $json['type'] = "error";
                $json['msg'] = esc_html__("Order status not changed.", "foodbakery");
                $json['status'] = '';
            }

            echo json_encode($json);
            exit();
        }

        /*
         * Show Cart for Restaurant in Right Side 
         */

        public function foodbakery_order_cart_display_callback($foodbakery_restaurant_id) {
            global $current_user, $foodbakery_plugin_options;
            $get_added_menus = array();
            $user_id = $current_user->ID;
            
            $restaurant_table_booking = get_post_meta($foodbakery_restaurant_id, 'foodbakery_restaurant_table_booking', true);
            $restaurant_pickup_delivery = get_post_meta($foodbakery_restaurant_id, 'foodbakery_restaurant_pickup_delivery', true);
            $foodbakery_delivery_fee = get_post_meta($foodbakery_restaurant_id, 'foodbakery_delivery_fee', true);
            $foodbakery_pickup_fee = get_post_meta($foodbakery_restaurant_id, 'foodbakery_pickup_fee', true);
            $foodbakery_vat_switch = isset($foodbakery_plugin_options['foodbakery_vat_switch']) ? $foodbakery_plugin_options['foodbakery_vat_switch'] : '';
            $foodbakery_payment_vat = isset($foodbakery_plugin_options['foodbakery_payment_vat']) ? $foodbakery_plugin_options['foodbakery_payment_vat'] : '';

            $restaurant_menu_list = get_post_meta($foodbakery_restaurant_id, 'foodbakery_menu_items', true);

            $total_items = (is_array($restaurant_menu_list) || is_object($restaurant_menu_list)) ? count($restaurant_menu_list) : 0;
            $total_menu = array();
            if (isset($restaurant_menu_list) && $restaurant_menu_list != '') {
                for ($menu_count = 0; $menu_count < $total_items; $menu_count++) {
                    if (isset($restaurant_menu_list[$menu_count]['restaurant_menu'])) {
                        $menu_exists = in_array($restaurant_menu_list[$menu_count]['restaurant_menu'], $total_menu);
                        if (!$menu_exists) {
                            $total_menu[] = $restaurant_menu_list[$menu_count]['restaurant_menu'];
                        }
                    }
                }
            }


            $publisher_id = foodbakery_company_id_form_user_id($user_id);

            $publisher_type = get_post_meta($publisher_id, 'foodbakery_publisher_profile_type', true);
            if ($publisher_id != '' && $publisher_type != '' && $publisher_type != 'restaurant') {
                //pre($get_added_menus);
                $get_added_menus = get_transient('add_menu_items_' . $publisher_id);

                if (empty($get_added_menus[$foodbakery_restaurant_id]) && isset($_COOKIE['add_menu_items_temp'])) {
                    $get_added_menus = unserialize(stripslashes($_COOKIE['add_menu_items_temp']));
                    set_transient('add_menu_items_' . $publisher_id, $get_added_menus, 60 * 60 * 24 * 30);
                }
            } else {
                if (isset($_COOKIE['add_menu_items_temp'])) {
                    $get_added_menus = unserialize(stripslashes($_COOKIE['add_menu_items_temp']));
                }
            }
            
            //pre($get_added_menus);

            $have_menu_orders = false;

            if (isset($get_added_menus[$foodbakery_restaurant_id]) && is_array($get_added_menus[$foodbakery_restaurant_id]) && sizeof($get_added_menus[$foodbakery_restaurant_id]) > 0) {
                $have_menu_orders = true;
            }

            update_post_meta($foodbakery_restaurant_id, 'foodbakery_restaurant_disable_cashes', 'no');
            $foodbakery_cash_payments = get_post_meta($foodbakery_restaurant_id, 'foodbakery_restaurant_disable_cashes', true);
            ?>
            <div class="user-order">
                <h6>
                    <i class="icon-shopping-basket"></i><?php esc_html_e('Your Order', 'foodbakery') ?>
                </h6>
                <?php
                $restaurant_allow_pre_order = get_post_meta($foodbakery_restaurant_id, 'foodbakery_restaurant_pre_order', true);
                if ($restaurant_allow_pre_order == 'yes') {
                    echo '<span class="error-message pre-order-msg" style="display: ' . ($have_menu_orders === false ? 'block' : 'none') . ';">' . esc_html__('This restaurant allows Pre orders.', 'foodbakery') . '</span>';
                }
                $selected_fee_type = isset($get_added_menus[$foodbakery_restaurant_id . '_fee_type']) ? $get_added_menus[$foodbakery_restaurant_id . '_fee_type'] : '';
                //pre($get_added_menus);
                $show_fee_type = '';
                if ($selected_fee_type == 'delivery' && $foodbakery_delivery_fee >= 0 && $foodbakery_pickup_fee > 0) {
                    $show_fee_type = 'delivery';
                } else if ($selected_fee_type == 'pickup' && $foodbakery_delivery_fee >= 0 && $foodbakery_pickup_fee > 0) {
                    $show_fee_type = 'pickup';
                } else {
                    if ($foodbakery_delivery_fee >= 0 && $restaurant_pickup_delivery == 'pickup') {
                        $show_fee_type = 'pickup';
                    } else if ($foodbakery_pickup_fee >= 0 && $restaurant_pickup_delivery == 'delivery') {
                        $show_fee_type = 'delivery';
                    } else if ($foodbakery_pickup_fee >= 0 && $restaurant_pickup_delivery == 'delivery_and_pickup') {
                        $show_fee_type = 'pickup';
                    }
                }
                ?>
                <span class="discount-info"
                      style="display: <?php echo($have_menu_orders === false ? 'block' : 'none') ?>;"><?php _e('If you have a discount code,<br> you will be able to input it<br> at the payments stage.', 'foodbakery') ?></span>
                      <?php
                      $flag_delivery_tax = apply_filters('foodbakery_check_delivery_tax', false);
                      if (!$flag_delivery_tax) {
                          ?>
                    <div class="select-option dev-select-fee-option"
                         data-rid="<?php echo esc_html($foodbakery_restaurant_id) ?>">
                        <ul>
                            <?php
                            if (($restaurant_pickup_delivery == 'pickup' || $restaurant_pickup_delivery == 'delivery_and_pickup')) {
                                ?>
                                <li>
                                    <input id="order-pick-up-fee"
                                           type="radio" <?php echo (($show_fee_type == 'pickup') ? 'checked="checked"' : '') ?>
                                           value="pickup"
                                           name="order_fee_type"
                                           data-fee="<?php echo foodbakery_get_currency($foodbakery_pickup_fee, false, '', '', false); ?>"
                                           data-label="<?php esc_html_e('Pick-Up', 'foodbakery') ?>"
                                           data-type="pickup"/>
                                    <label for="order-pick-up-fee"><?php esc_html_e('Pick-Up', 'foodbakery') ?></label>
                                    <span><?php echo foodbakery_get_currency($foodbakery_pickup_fee, true); ?></span>
                                </li>
                                <?php
                            }
                            if (($restaurant_pickup_delivery == 'delivery' || $restaurant_pickup_delivery == 'delivery_and_pickup')) {
                                ?>
                                <li>
                                    <input id="order-delivery-fee" <?php echo (($show_fee_type == 'delivery') ? 'checked="checked"' : ''); ?>
                                           type="radio" name="order_fee_type"
                                           value="delivery"
                                           data-fee="<?php echo foodbakery_get_currency($foodbakery_delivery_fee, false, '', '', false); ?>"
                                           data-label="<?php esc_html_e('Delivery', 'foodbakery') ?>"
                                           data-type="delivery"/>
                                    <label for="order-delivery-fee"><?php esc_html_e('Delivery', 'foodbakery') ?></label>
                                    <span><?php echo foodbakery_get_currency($foodbakery_delivery_fee, true); ?></span>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <?php
                }
                ?>

                <div class="dev-menu-orders-list"
                     style="display: <?php echo($have_menu_orders === true ? 'block' : 'none') ?>;">

                    <ul class="categories-order"
                        data-rid="<?php echo absint($foodbakery_restaurant_id) ?>">
                            <?php
                            if (isset($get_added_menus[$foodbakery_restaurant_id]) && is_array($get_added_menus[$foodbakery_restaurant_id]) && sizeof($get_added_menus[$foodbakery_restaurant_id]) > 0) {
                                $rand_numb_class = 10000001;
                                $item_count = 1;
                                foreach ($get_added_menus[$foodbakery_restaurant_id] as $menu_key => $menu_ord_item) {
                                    //foreach ($get_added_menus[$foodbakery_restaurant_id] as $menu_ord_item) {

                                    if (isset($menu_ord_item['menu_id']) && isset($menu_ord_item['price'])) {
                                        $rand_numb = rand(10000000, 99999999);
                                        $menu_t_price = 0;
                                        $this_item_quantity = (isset($menu_ord_item['quantity']) && $menu_ord_item['quantity'] > 0) ? $menu_ord_item['quantity'] : 1;
                                        $this_menu_cat_id = isset($menu_ord_item['menu_cat_id']) ? $menu_ord_item['menu_cat_id'] : '';
                                        $this_item_id = $menu_ord_item['menu_id'];
                                        $this_item_id = $menu_ord_item['menu_id'];
                                        $this_item_price = (isset( $menu_ord_item['price']) && $menu_ord_item['price'] > 0 )? $menu_ord_item['price'] : 0;
                                        $this_item_price = ($this_item_price * $this_item_quantity);

                                        $this_item_extras = isset($menu_ord_item['extras']) ? $menu_ord_item['extras'] : '';

                                        $menu_t_price += floatval($this_item_price);
                                        $this_item_title = isset($restaurant_menu_list[$this_item_id]['menu_item_title']) ? $restaurant_menu_list[$this_item_id]['menu_item_title'] : '';

                                        $menu_extra_li = '';
                                        if (is_array($this_item_extras) && sizeof($this_item_extras) > 0) {
                                            $extra_m_counter = 0;
                                            $menu_extra_li .= '<ul>';
                                            foreach ($this_item_extras as $this_item_extra_at) {
                                                $this_item_heading = isset($restaurant_menu_list[$this_item_id]['menu_item_extra']['heading'][$this_item_extra_at['title_id']]) ? $restaurant_menu_list[$this_item_id]['menu_item_extra']['heading'][$this_item_extra_at['title_id']] : '';
                                                $item_extra_at_title = isset($this_item_extra_at['title']) ? $this_item_extra_at['title'] : '';
                                                $item_extra_at_price = (isset($this_item_extra_at['price']) && $this_item_extra_at['price'] > 0) ? $this_item_extra_at['price'] : 0;
                                                
                                                
                                                $item_extra_at_price = ($item_extra_at_price * $this_item_quantity);
                                                
                                                if ($item_extra_at_title != '' || $item_extra_at_price > 0) {
                                                    $menu_extra_li .= '<li>' . $this_item_heading . ' - ' . $item_extra_at_title . ' : <span class="category-price">' . foodbakery_get_currency($item_extra_at_price, true) . '</span></li>';
                                                }

                                                $menu_t_price += floatval($item_extra_at_price);
                                                $extra_m_counter++;
                                            }
                                            $menu_extra_li .= '</ul>';
                                            $popup_id = 'edit_extras-' . $this_menu_cat_id . '-' . $this_item_id;
                                            $data_id = $this_item_id;
                                            $data_cat_id = $this_menu_cat_id;
                                            $ajax_url = admin_url('admin-ajax.php');
                                            $unique_id = $get_added_menus[$foodbakery_restaurant_id][$menu_key]['unique_id'];
                                            $unique_menu_id = $get_added_menus[$foodbakery_restaurant_id][$menu_key]['unique_menu_id'];
                                            $extra_child_menu_id = isset($get_added_menus[$foodbakery_restaurant_id][$menu_key]['extra_child_menu_id']) ? $get_added_menus[$foodbakery_restaurant_id][$menu_key]['extra_child_menu_id'] : '';
                                            $menu_extra_li .= '<a href="javascript:void(0);" class="edit-menu-item update_menu_' . $rand_numb_class . '" onClick="foodbakery_edit_extra_menu_item(\'' . $popup_id . '\',\'' . $data_id . '\',\'' . $data_cat_id . '\',\'' . $rand_numb_class . '\',\'' . $ajax_url . '\',\'' . $foodbakery_restaurant_id . '\',\'' . $unique_id . '\',\'' . $unique_menu_id . '\',\'' . $extra_child_menu_id . '\');">' . esc_html__('Edit', 'foodbakery') . '</a>';
                                        }
                                        ?>
                                    <li class="menu-added-<?php echo $rand_numb_class; ?>" id="menu-added-<?php echo absint($rand_numb) ?>" class="item_count_<?php echo $item_count; ?>"
                                        data-pr="<?php echo foodbakery_get_currency($menu_t_price, false, '', '', false); ?>"
                                        data-conpr="<?php echo foodbakery_get_currency($menu_t_price, false, '', '', true); ?>">
                                        <a href="javascript:void(0)"
                                           class="btn-cross dev-remove-menu-item"><i
                                                class=" icon-cross3"></i></a>
                                        <a><?php echo esc_html($this_item_title) ?></a>
                                        <span class="category-price"><?php echo foodbakery_get_currency($this_item_price, true, '', '', true); ?></span>
                                        <?php $menu_item_identity = isset( $menu_ord_item['menu_item_identity'] )? $menu_ord_item['menu_item_identity'] : 0; ?>
                                        <?php do_action('foodbakery_cart_order_item_details', $menu_ord_item, $foodbakery_restaurant_id, $menu_item_identity); ?>
                                        <?php echo force_balance_tags($menu_extra_li) ?>
                                    </li>
                                    <?php
                                }
                                $item_count++;
                                $rand_numb_class++;
                            }
                        }
                        ?>
                    </ul>
                    <div class="price-area dev-menu-price-con"
                         data-vatsw="<?php echo esc_html($foodbakery_vat_switch) ?>"
                         data-vat="<?php echo floatval($foodbakery_payment_vat) ?>">
                        <ul>
                            <input type="hidden" id="order_subtotal_price"
                                   name="order_subtotal_price"
                                   value="<?php echo restaurant_menu_price_calc($get_added_menus, $foodbakery_restaurant_id, '', '', '', false) ?>">
                            <li><?php esc_html_e('Subtotal', 'foodbakery') ?>
                                <span class="price"><?php echo currency_symbol_possitions_html('<em class="dev-menu-subtotal">' . restaurant_menu_price_calc($get_added_menus, $foodbakery_restaurant_id, false, false, false, true) . '</em>', foodbakery_get_currency_sign()); ?>
                                </span>
                            </li>

                            <?php
                            do_action('foodbakery_add_delivery_countytax_list', restaurant_menu_price_calc($get_added_menus, $foodbakery_restaurant_id, false, false, false, true));
                            $flag_delivery_tax = apply_filters('foodbakery_check_delivery_tax', false);
                            if (!$flag_delivery_tax) {
                                if ($show_fee_type == 'delivery') {
                                    ?>
                                    <li class="restaurant-fee-con"><span
                                            class="fee-title"><?php esc_html_e('Delivery fee', 'foodbakery') ?></span>
                                        <span class="price"><?php echo currency_symbol_possitions_html('<em class="dev-menu-charges"
                                                                                        data-confee="' . foodbakery_get_currency($foodbakery_delivery_fee, false, '', '', true) . '"
                                                                                        data-fee="' . foodbakery_get_currency($foodbakery_delivery_fee, false, '', '', false) . '">' . foodbakery_get_currency($foodbakery_delivery_fee, false, '', '', true) . '</em>', foodbakery_get_currency_sign()); ?>
                                        </span>
                                    </li>
                                    <?php
                                } else if ($show_fee_type == 'pickup') {
                                    ?>
                                    <li class="restaurant-fee-con 3333"><span
                                            class="fee-title"><?php esc_html_e('Pickup fee', 'foodbakery') ?></span>
                                        <span class="price"><?php echo currency_symbol_possitions_html('<em class="dev-menu-charges"
                                                                                        data-confee="' . foodbakery_get_currency($foodbakery_pickup_fee, false, '', '', true) . '"
                                                                                        data-fee="' . foodbakery_get_currency($foodbakery_pickup_fee, false, '', '', false) . '">' . foodbakery_get_currency($foodbakery_pickup_fee, false, '', '', true) . '</em>', foodbakery_get_currency_sign()); ?>
                                        </span>
                                    </li>
                                    <?php
                                }

                                if ($foodbakery_vat_switch == 'on' && $foodbakery_payment_vat > 0) {
                                    ?>
                                    <input type="hidden" id="order_vat_percent"
                                           name="order_vat_percent"
                                           value="<?php echo($foodbakery_payment_vat); ?>">
                                    <input type="hidden"
                                           id="order_vat_cal_price"
                                           name="order_vat_cal_price"
                                           value="<?php echo restaurant_menu_price_calc($get_added_menus, $foodbakery_restaurant_id, true, false, true); ?>">
                                    <li><?php printf(esc_html__('VAT (%s&#37;)', 'foodbakery'), $foodbakery_payment_vat) ?>
                                        <span class="price"><?php echo currency_symbol_possitions_html('<em class="dev-menu-vtax">' . restaurant_menu_price_calc($get_added_menus, $foodbakery_restaurant_id, true, false, true, true) . '</em>', foodbakery_get_currency_sign()); ?>
                                        </span>
                                    </li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>
                        <?php
                        $grant_total = '';
                        if ($flag_delivery_tax) {
                            ?>
                            <?php
                            $grant_total = restaurant_menu_price_calc($get_added_menus, $foodbakery_restaurant_id, true, true, false, true);
                        }
                        ?>
                        <p class="total-price"><?php esc_html_e('Total', 'foodbakery') ?>
                            <span class="price">
                                <?php echo currency_symbol_possitions_html('<em class="dev-menu-grtotal" data-grant_total="' . $grant_total . '" >' . restaurant_menu_price_calc($get_added_menus, $foodbakery_restaurant_id, true, true, false, true) . '</em>', foodbakery_get_currency_sign()); ?>


                            </span>
                        </p>
                    </div>
                </div>
                <div id="dev-no-menu-orders-list"
                     style="display: <?php echo($have_menu_orders === false ? 'block' : 'none') ?>;">
                         <?php echo '<span class="success-message">' . esc_html__('There are no items in your basket.', 'foodbakery') . '</span>' ?>
                </div>
                <?php
                if ($foodbakery_cash_payments != 'yes') {
                    ?>
                    <div class="pay-option dev-order-pay-options">
                        <ul>
                            <?php
                            $foodbakery_restaurant_disable_cash = get_post_meta($foodbakery_restaurant_id, 'foodbakery_restaurant_disable_cash', true);
                            if (empty($foodbakery_restaurant_disable_cash) || $foodbakery_restaurant_disable_cash == '') {
                                $foodbakery_restaurant_disable_cash = 'no';
                            }
                            $foodbakery_restaurant_disable_cash  = apply_filters('foodbakery_cash_module_status', $foodbakery_restaurant_disable_cash);
                            if ($foodbakery_restaurant_disable_cash == 'no') {
                                ?>

                                <li>
                                    <input id="order-cash-payment" value="cash" type="radio" name="order_payment_method" data-type="cash" />
                                    <label for="order-cash-payment">
                                        <i class="icon-coins"></i>
                                        <?php esc_html_e('Cash', 'foodbakery') ?>
                                    </label>
                                </li>
                            <?php }
                            ?>
                            <li>
                                <input id="order-card-payment" value="card" type="radio" checked="checked" name="order_payment_method" data-type="card" />
                                <label for="order-card-payment">
                                    <i class="icon-credit-card4"></i>
                                    <?php esc_html_e('Card', 'foodbakery') ?>
                                </label>
                            </li>
                        </ul>
                    </div>
                    <?php
                }
                ?>
                <div class="row">

                    <?php do_action('foodbakery_add_delivery_address_field', $foodbakery_restaurant_id); ?>

                    <div class="col-sm-12">
                        <div class="form-group">
                            <div class="input-group date">
                                <input type="text" name="delivery_date"
                                       id="datetimepicker1" class="form-control"
                                       value="<?php echo date('d-m-Y H:i'); ?>"
                                       placeholder="Select Date and Time"/>
                                <span class="input-group-addon">
                                    <span class="icon-event_available"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <script type="text/javascript">
                        jQuery(function () {
                            jQuery('#datetimepicker1').datetimepicker({
                                format: 'd-m-Y H:i',
                                timepicker: true,
                                minDate: '<?php echo date('d-m-Y H:i'); ?>',
                                step: 15
                            });
                        });
                    </script>
                </div>
                <a href="javascript:void(0)" class="menu-order-confirm" id="menu-order-confirm"
                   data-rid="<?php echo absint($foodbakery_restaurant_id) ?>"><?php esc_html_e('Confirm Order', 'foodbakery') ?></a>
                <span class="menu-loader"></span>
            </div>
            <?php
        }

    }

    global $foodbakery_order_detail;
    $foodbakery_order_detail = new Foodbakery_Order_Detail();
}