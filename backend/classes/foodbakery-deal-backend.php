<?php
// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * File Type: Foodbakery Deal
 */
if (!class_exists('Foodbakery_deal_backend')) {

    class Foodbakery_deal_backend {

        public $email_post_type_name;

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_action('wp_ajax_foodbakery_add_deal', array($this, 'foodbakery_add_deal_callback'));
            add_action('wp_ajax_foodbakery_add_deal_form', array($this, 'foodbakery_add_deal_form_callback'));
            add_action('save_post', array($this, 'foodbakery_insert_menu_deals'), 17);
            add_action('init', array($this, 'foodbakery_update_menu_structure_callback'));
        }
        
        public function foodbakery_update_menu_structure_callback(){
            $is_menu_updated = get_option('foodbakery_is_menu_updated');
            if( $is_menu_updated != 'Yes'){
                $restaurant_list = array(
                    'post_type' => 'restaurants',
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                    'fields' => 'ids'
                );
                $restaurant_list_loop = new WP_Query($restaurant_list);
                
                if ($restaurant_list_loop->have_posts()){
                    while ($restaurant_list_loop->have_posts()){
                        $restaurant_list_loop->the_post();
                        $restaurant_id = get_the_ID();
                        $get_restaurant_menu_items = get_post_meta($restaurant_id, 'foodbakery_menu_items', true);
                        update_post_meta($restaurant_id, 'foodbakery_menu_items_backup', $get_restaurant_menu_items);
                        $get_restaurant_menu_items_updated = array();
                        if( !empty( $get_restaurant_menu_items ) ){
                            foreach( $get_restaurant_menu_items as $menu_key => $menu_data){

                                $menu_item_identity = rand(1000, 99999);
                                $get_restaurant_menu_items_updated[$menu_item_identity] = $menu_data;
                                $get_restaurant_menu_items_updated[$menu_item_identity]['menu_item_identity'] = $menu_item_identity;
                            }
                        }
                        update_post_meta($restaurant_id, 'foodbakery_menu_items', $get_restaurant_menu_items_updated);
                    }
                    update_option('foodbakery_is_menu_updated', 'Yes');
                }
                
            }
            
        }
        

        public function foodbakery_add_deal_callback() {
            global $foodbakery_html_fields;
            $restaurant_id = isset($_POST['restaurant_id']) ? $_POST['restaurant_id'] : 0;
            $get_restaurant_menu_items = get_post_meta($restaurant_id, 'foodbakery_menu_items', true);
            $menu_items_array = array();
            if (!empty($get_restaurant_menu_items)) {
                foreach ($get_restaurant_menu_items as $menu_item_data) {
                    $menu_item_identity = isset($menu_item_data['menu_item_identity']) ? $menu_item_data['menu_item_identity'] : 0;
                    $menu_item_title = isset($menu_item_data['menu_item_title']) ? $menu_item_data['menu_item_title'] : '';
                    $menu_items_array[$menu_item_identity] = $menu_item_title;
                }
            }

            //pre($menu_items_array);
            ?>

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 deal-form-block">

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <a href="javascript:void(0);" class="close-menu-item close-deal_add-item"><i class="icon-close2"></i></a>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="field-holder">
                            <label>Deal Name *</label>
                            <input class="menu-item-title" id="deal_name" name="deal_name" value="" type="text" placeholder="Deal Name">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="field-holder">
                            <label>Deal Price *</label>
                            <input class="menu-item-price" id="deal_price" name="deal_price" value="" type="number" placeholder="Deal Price">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="field-holder">
                            <label>Serving *</label>
                            <input class="menu-item-serving" id="deal_serving" name="deal_serving" min="0" value="0" type="number" placeholder="Deal Serving">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="field-holder">
                            <label>Deal Menu *</label>
                            <?php
                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Deal Menu Items', 'foodbakery'),
                                'desc' => '',
                                'multi' => true,
                                'desc' => '',
                                'field_params' => array(
                                    'std' => '',
                                    'id' => 'deal_menu_items',
                                    'classes' => 'chosen-select-no-single chosen-select',
                                    'options' => $menu_items_array,
                                ),
                            );

                            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
                            ?>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="icon-image-browse-field">
                    <?php $foodbakery_opt_array = array(
                        'name' => '',
                        'desc' => '',
                        'hint_text' => '',
                        'echo' => false,
                        'without_html' => true,
                        'force_std' => true,
                        'std' => '',
                        'echo' => false,
                        'id' => 'deal_image',
                        'field_params' => array(
                            'id' => 'deal_image',
                            'cust_name' => 'deal_image',
                            'force_std' => true,
                            'std' => $menu_item_icon,
                            'return' => true,
                        ),
                    );
                    echo $foodbakery_html_fields->foodbakery_upload_file_field($foodbakery_opt_array);
                    ?>
                    </div>
                </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="field-holder">
                        <a class="add-menu-item submit-deal-form" href="javascript:void(0);">Save</a>
                    </div>
                </div>
            </div>
            <?php
            exit;
        }

        public function foodbakery_add_deal_form_callback() {
            $deal_name = isset($_POST['deal_name']) ? $_POST['deal_name'] : '';
            $deal_price = isset($_POST['deal_price']) ? $_POST['deal_price'] : 0;
            $deal_image = isset($_POST['deal_image']) ? $_POST['deal_image'] : 0;
            $deal_serving = isset($_POST['deal_serving']) ? $_POST['deal_serving'] : 0;
            $foodbakery_deal_menu_items = isset($_POST['foodbakery_deal_menu_items']) ? $_POST['foodbakery_deal_menu_items'] : '';
            $currency_sign = foodbakery_get_currency_sign();
            $random_id = rand(100000, 9000000);
            //pre($deal_name, false);
            //pre($deal_price, false);
            //pre($foodbakery_deal_menu_items);
            ?>
            <li class="menu-item-<?php echo $random_id; ?>">
                <div class="drag-list">
                    <span class="drag-option ui-sortable-handle"><i class="icon-bars"></i></span>
                    <div class="icon-holder">
                        <i class="3699"></i>
                    </div>
                    <div class="list-title">
                        <h6><?php echo $deal_name; ?></h6>
                        <input type="hidden" name="deal_data_name[<?php echo $random_id; ?>]" value="<?php echo $deal_name; ?>">
                    </div>
                    <div class="list-price">
                        <span><?php echo currency_symbol_possitions_html('<b>' . $currency_sign . '</b>', '<b>' . $deal_price . '</b>'); ?></span>
                        <input type="hidden" name="deal_data_price[<?php echo $random_id; ?>]" value="<?php echo $deal_price; ?>">
                    </div>
                    <input type="hidden" name="deal_data_serving[<?php echo $random_id; ?>]" value="<?php echo $deal_serving; ?>">
                    <input type="hidden" name="deal_data_items[<?php echo $random_id; ?>]" value="<?php echo $foodbakery_deal_menu_items; ?>">
                    <input type="hidden" name="deal_data_image[<?php echo $random_id; ?>]" value="<?php echo $deal_image; ?>">
                    
                    <div class="list-option">
                        
                        <a href="javascript:void(0);" class="remove-menu-item" onclick="foodbakery_remove_menu_item('394957466');"><i class="icon-cross-out"></i></a>
                    </div>
                </div>
            </li>
            <?php
            exit;
        }

        public function foodbakery_insert_menu_deals($restaurant_id) {
            $deal_data_name = foodbakery_get_input('deal_data_name', '', 'ARRAY');
            $deal_data_price = foodbakery_get_input('deal_data_price', '', 'ARRAY');
            $deal_data_image = foodbakery_get_input('deal_data_image', '', 'ARRAY');
            $deal_data_serving = foodbakery_get_input('deal_data_serving', '', 'ARRAY');
            $deal_data_items = foodbakery_get_input('deal_data_items', '', 'ARRAY');
            
            $deal_array = array();
            if (!empty($deal_data_name)) {
                foreach ($deal_data_name as $data_key => $deal_name) {
                    $deal_price = isset($deal_data_price[$data_key]) ? $deal_data_price[$data_key] : 0;
                    $deal_image = isset($deal_data_image[$data_key]) ? $deal_data_image[$data_key] : 0;
                    $deal_serving = isset($deal_data_serving[$data_key]) ? $deal_data_serving[$data_key] : 0;
                    $deal_items = isset($deal_data_items[$data_key]) ? $deal_data_items[$data_key] : 0;
                    $deal_array[$data_key] = array(
                        'deal_name' => $deal_name,
                        'deal_price' => $deal_price,
                        'deal_image' => $deal_image,
                        'deal_serving' => $deal_serving,
                        'deal_items' => $deal_items,
                    );
                }
                update_post_meta($restaurant_id, 'foodbakery_menu_deals', $deal_array);
            }else{
                update_post_meta($restaurant_id, 'foodbakery_menu_deals', '');
            }
            
        }

        public function foodbakery_get_menu_item($restaurant_id, $menu_item_key) {
            $get_restaurant_menu_items = get_post_meta($restaurant_id, 'foodbakery_menu_items', true);
            
            if (!empty($get_restaurant_menu_items)) {
                foreach ($get_restaurant_menu_items as $menu_item_data) {
                    $menu_item_identity = isset($menu_item_data['menu_item_identity']) ? $menu_item_data['menu_item_identity'] : 0; 
                    //pre($menu_item_identity, false);
                    //pre($menu_item_key, false);
                    //pre($menu_item_data);

                    if ($menu_item_identity == $menu_item_key) {
                        return $menu_item_data;
                    }
                }
            }
            return array();
        }
        
        public function foodbakery_get_menu_deal($restaurant_id, $deal_id) {
            $menu_deals = get_post_meta($restaurant_id, 'foodbakery_menu_deals', true);
            if (!empty($menu_deals)) {
                foreach ($menu_deals as $menu_deal_id => $menu_deal_data) {
                    if ($menu_deal_id == $deal_id) {
                        return $menu_deal_data;
                    }
                }
            }
            return array();
        }

        public function foodbakery_menu_deals_backend($restaurant_id) {
            $menu_deals = get_post_meta($restaurant_id, 'foodbakery_menu_deals', true);
            $currency_sign = foodbakery_get_currency_sign();
            ob_start();
            if (!empty($menu_deals)) {
                foreach ($menu_deals as $menu_key => $menu_data) {
                    $deal_name = isset($menu_data['deal_name']) ? $menu_data['deal_name'] : '';
                    $deal_price = isset($menu_data['deal_price']) ? $menu_data['deal_price'] : 0;
                    $deal_image = isset($menu_data['deal_image']) ? $menu_data['deal_image'] : 0;
                    $deal_serving = isset($menu_data['deal_serving']) ? $menu_data['deal_serving'] : 0; 
                    $deal_items = isset($menu_data['deal_items']) ? $menu_data['deal_items'] : '';
                    ?>
                    <li class="menu-item-<?php echo $menu_key; ?>">
                        <div class="drag-list">
                            <span class="drag-option ui-sortable-handle"><i class="icon-bars"></i></span>
                            <div class="icon-holder">
                                <i class="3699"></i>
                            </div>
                            <div class="list-title">
                                <h6><?php echo $deal_name; ?></h6>
                            </div>
                            <div class="list-price">
                                <span><?php echo currency_symbol_possitions_html('<b>' . $currency_sign . '</b>', '<b>' . $deal_price . '</b>'); ?></span>
                            </div>
                            <div class="list-option">
                                <a href="javascript:void(0);" data-deal_id="<?php echo esc_attr($menu_key); ?>" class="edit-deal-item">Edit</a>
                                <a href="javascript:void(0);" data-deal_id="<?php echo esc_attr($menu_key); ?>" class="remove-deal_id-item"><i class="icon-cross-out"></i></a>
                            </div>
                        </div>
                        <?php echo $this->foodbakery_edit_deal_callback($restaurant_id, $menu_key); ?>
                    </li>
                    <?php
                    
                }
            }
            ?>
            <?php
            $menu_html_response = ob_get_clean();
            return $menu_html_response;
            ?>

            <?php
        }
        
        public function foodbakery_edit_deal_callback($restaurant_id, $deal_id) {
            global $foodbakery_html_fields;
            
            $deal_data = $this->foodbakery_get_menu_deal($restaurant_id, $deal_id);
            $deal_name = isset($deal_data['deal_name']) ? $deal_data['deal_name'] : '';
            $deal_price = isset($deal_data['deal_price']) ? $deal_data['deal_price'] : 0;
            $deal_image = isset($deal_data['deal_image']) ? $deal_data['deal_image'] : 0;
            $deal_serving = isset($deal_data['deal_serving']) ? $deal_data['deal_serving'] : 0; 
            $deal_items = isset($deal_data['deal_items']) ? $deal_data['deal_items'] : '';
            $get_restaurant_menu_items = get_post_meta($restaurant_id, 'foodbakery_menu_items', true);
            $menu_items_array = array();
            if (!empty($get_restaurant_menu_items)) {
                foreach ($get_restaurant_menu_items as $menu_item_data) {
                    $menu_item_identity = isset($menu_item_data['menu_item_identity']) ? $menu_item_data['menu_item_identity'] : 0;
                    $menu_item_title = isset($menu_item_data['menu_item_title']) ? $menu_item_data['menu_item_title'] : '';
                    $menu_items_array[$menu_item_identity] = $menu_item_title;
                }
            }
            ?>
            <div class="foodbakery-deal-edit-form" style="display:none;">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 deal-form-block">

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <a href="javascript:void(0);" class="close-menu-item edit-deal-item"><i class="icon-close2"></i></a>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="field-holder">
                            <label>Deal Name *</label>
                            <input class="menu-item-title" id="deal_name" name="deal_data_name[<?php echo $deal_id; ?>]" value="<?php echo $deal_name; ?>" type="text" placeholder="Deal Name">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="field-holder">
                            <label>Deal Price *</label>
                            <input class="menu-item-price" id="deal_price" name="deal_data_price[<?php echo $deal_id; ?>]" value="<?php echo $deal_price; ?>" type="number" placeholder="Deal Price">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="field-holder">
                            <label>Serving *</label>
                            <input class="menu-item-serving" id="deal_serving" name="deal_data_serving[<?php echo $deal_id; ?>]" min="0" value="<?php echo $deal_serving; ?>" type="number" placeholder="Deal Serving">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="field-holder">
                            <label>Deal Menu *</label>
                            <?php
                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Deal Menu Items', 'foodbakery'),
                                'desc' => '',
                                'multi' => true,
                                'desc' => '',
                                'field_params' => array(
                                    'force_std' => $deal_items,
                                    'cust_name' => 'deal_data_items['.$deal_id.'][]',
                                    'id' => 'deal_menu_items',
                                    'classes' => 'chosen-select-no-single chosen-select',
                                    'options' => $menu_items_array,
                                ),
                            );

                            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
                            ?>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="icon-image-browse-field">
                    <?php $foodbakery_opt_array = array(
                        'name' => '',
                        'desc' => '',
                        'hint_text' => '',
                        'echo' => false,
                        'without_html' => true,
                        'force_std' => true,
                        'std' => $deal_image,
                        'echo' => false,
                        'id' => 'deal_image_'.$deal_id,
                        'field_params' => array(
                            'id' => 'deal_image_'.$deal_id,
                            'cust_name' => 'deal_data_image['.$deal_id.']',
                            'force_std' => true,
                            'std' => $deal_image,
                            'return' => true,
                        ),
                    );
                    echo $foodbakery_html_fields->foodbakery_upload_file_field($foodbakery_opt_array);
                    ?>
                    </div>
                </div>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="field-holder">
                        <a class="add-menu-item edit-deal-item" href="javascript:void(0);">Save</a>
                    </div>
                </div>
            </div>
            </div>
            <?php
            //exit;
        }

    }

    new Foodbakery_deal_backend();
}
