<?php
// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * File Type: Foodbakery Deal
 */
if (!class_exists('Foodbakery_deal_frontend')) {

    class Foodbakery_deal_frontend {

        public $email_post_type_name;

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_action('wp_ajax_foodbakery_show_extra_menu_deal_item', array($this, 'foodbakery_show_extra_menu_deal_item_callback'));
            add_action('wp_ajax_nopriv_foodbakery_show_extra_menu_deal_item', array($this, 'foodbakery_show_extra_menu_deal_item_callback'));

            add_action('wp_ajax_foodbakery_restaurant_add_deal_item', array($this, 'foodbakery_restaurant_add_deal_item_callback'));
            add_action('wp_ajax_nopriv_foodbakery_restaurant_add_deal_item', array($this, 'foodbakery_restaurant_add_deal_item_callback'));

            add_action('wp_ajax_foodbakery_add_deal_frontend', array($this, 'foodbakery_add_deal_frontend_callback'));
            add_action('wp_ajax_foodbakery_add_deal_form_frontend', array($this, 'foodbakery_add_deal_form_frontend_callback'));
        }

        public function restaurant_detail_deals_list($restaurant_id, $single_restaurant_view = 'simple') {
            $menu_deals = get_post_meta($restaurant_id, 'foodbakery_menu_deals', true);
            $currency_sign = foodbakery_get_currency_sign();
            $Foodbakery_deal_backend = new Foodbakery_deal_backend();
            $get_foodbakeri_nutri_icons = get_post_meta($restaurant_id, 'nutri_icon_imgs', true);
            $get_foodbakeri_nutri_titles = get_post_meta($restaurant_id, 'nutri_icon_titles', true);
            $list_item_class = ( $single_restaurant_view == 'fancy') ? 'col-lg-6 col-md-6 col-sm-6 col-xs-12' : '';
            ob_start();
            if (!empty($menu_deals)) {
                if ($single_restaurant_view == 'simple') {
                    ?>
                    <ul>

                        <?php
                        foreach ($menu_deals as $menu_key => $menu_data) {
                            $deal_name = isset($menu_data['deal_name']) ? $menu_data['deal_name'] : '';
                            $deal_price = isset($menu_data['deal_price']) ? $menu_data['deal_price'] : 0;
                            $deal_image = isset($menu_data['deal_image']) ? $menu_data['deal_image'] : 0;
                            $deal_serving = isset($menu_data['deal_serving']) ? $menu_data['deal_serving'] : 0;
                            $deal_items = (isset($menu_data['deal_items']) && !empty($menu_data['deal_items'])) ? $menu_data['deal_items'] : array();
                            
                            $deal_items_array = (is_array($deal_items))? $deal_items : explode(',', $deal_items);

                            $deal_item_icon_img_arr = wp_get_attachment_image_src($deal_image, 'thumbnail');
                            $deal_item_icon_img_src = isset($deal_item_icon_img_arr[0]) ? $deal_item_icon_img_arr[0] : '';
                            $deal_item_icon_img_arr_p = wp_get_attachment_image_src($deal_image, 'large');
                            $deal_item_icon_img_src_p = isset($deal_item_icon_img_arr_p[0]) ? $deal_item_icon_img_arr_p[0] : '';
                            ?>
                            <li>

                                <div class="text-holder">

                                    <h6><?php echo $deal_name; ?></h6>
                                </div>
                                <div class="price-holder">
                                    <span class="price"><?php echo currency_symbol_possitions_html($currency_sign, $deal_price); ?></span>
                                    <a href="javascript:void(0)" data-deal_id="<?php echo $menu_key; ?>" data-menu_rand_id="<?php echo $menu_key; ?>" data-item_stock="1000" data-menu_item_identity="<?php echo $menu_key; ?>" class="menu_rand_id_<?php echo $menu_key; ?> foodbakery_show_extra_menu_deal_item restaurant-add-deal-btn1 restaurant-add-deal-btn-<?php echo $menu_key; ?>" data-rid="<?php echo $restaurant_id; ?>" data-id="0" data-cid="1"><i class="icon-plus4 text-color"></i></a>
                                    <span id="add-menu-loader-0"></span>
                                </div>

                                <?php
                                if (!empty($deal_items_array)) {
                                    foreach ($deal_items_array as $deal_item_id) {
                                        $menu_item_data = $Foodbakery_deal_backend->foodbakery_get_menu_item($restaurant_id, $deal_item_id);

                                        $menu_item_title = isset($menu_item_data['menu_item_title']) ? $menu_item_data['menu_item_title'] : '';
                                        $menu_item_unique_id = isset($menu_item_data['menu_item_unique_id']) ? $menu_item_data['menu_item_unique_id'] : '';
                                        $menu_item_description = isset($menu_item_data['menu_item_description']) ? $menu_item_data['menu_item_description'] : '';
                                        $menu_item_icon = isset($menu_item_data['menu_item_icon']) ? $menu_item_data['menu_item_icon'] : '';
                                        $menu_item_nutri = isset($menu_item_data['menu_item_nutri']) ? $menu_item_data['menu_item_nutri'] : '';
                                        $menu_item_price = isset($menu_item_data['menu_item_price']) ? $menu_item_data['menu_item_price'] : 0;
                                        $menu_item_stock = isset($menu_item_data['menu_item_stock']) ? $menu_item_data['menu_item_stock'] : 1000;
                                        $menu_item_extra = isset($menu_item_data['menu_item_extra']) ? $menu_item_data['menu_item_extra'] : 0;
                                        $menu_item_identity = isset($menu_item_data['menu_item_identity']) ? $menu_item_data['menu_item_identity'] : '';

                                        $menu_item_icon_img_arr = wp_get_attachment_image_src($menu_item_icon, 'thumbnail');
                                        $menu_item_icon_img_src = isset($menu_item_icon_img_arr[0]) ? $menu_item_icon_img_arr[0] : '';
                                        $menu_item_icon_img_arr_p = wp_get_attachment_image_src($menu_item_icon, 'large');
                                        $menu_item_icon_img_src_p = isset($menu_item_icon_img_arr_p[0]) ? $menu_item_icon_img_arr_p[0] : '';
                                        ?>

                                        <ul>
                                            <li style="border:none; width: 95%;" class="<?php echo esc_attr($list_item_class); ?>">

                                                <div class="image-holder"> <a   href="<?php echo esc_url($menu_item_icon_img_src_p); ?>" rel="prettyPhoto" ><img src="<?php echo esc_url($menu_item_icon_img_src); ?>" alt=""></a></div>
                                                <div class="text-holder">

                                                    <h6><?php echo esc_html($menu_item_title); ?></h6>
                                                    <span><?php echo esc_html($menu_item_description); ?></span>
                                                    <?php
                                                    if (is_array($menu_item_nutri) && sizeof($menu_item_nutri) > 0) {
                                                        ?>
                                                        <ul class="nutri-icons">
                                                            <?php
                                                            $nutri_count = 0;
                                                            foreach ($menu_item_nutri as $men_nutri) {
                                                                $menu_nutri_index = is_array($get_foodbakeri_nutri_icons) ? array_search($men_nutri, $get_foodbakeri_nutri_icons) : '';
                                                                $menu_nutri_title = isset($get_foodbakeri_nutri_titles[$menu_nutri_index]) ? $get_foodbakeri_nutri_titles[$menu_nutri_index] : '';
                                                                $men_nutri_icon_img_arr = wp_get_attachment_image_src($men_nutri, 'thumbnail');
                                                                $men_nutri_icon_img_src = isset($men_nutri_icon_img_arr[0]) ? $men_nutri_icon_img_arr[0] : '';
                                                                ?>
                                                                <li><a data-toggle="tooltip" title="<?php echo esc_html($menu_nutri_title) ?>"><img src="<?php echo esc_url($men_nutri_icon_img_src); ?>" alt=""></a></li>
                                                                <?php
                                                                $nutri_count ++;
                                                            }
                                                            ?>
                                                        </ul>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>

                                            </li>

                                        </ul>

                                        <?php
                                    }
                                }
                                ?>




                            </li>
                        <?php } ?>
                        <div id="show_extra_deals_modal"></div>
                    </ul>
                    <?php
                }
                if ($single_restaurant_view == 'fancy') {
                    ?>

                    <ul class="row">
                        <?php
                        foreach ($menu_deals as $menu_key => $menu_data) {
                            $deal_name = isset($menu_data['deal_name']) ? $menu_data['deal_name'] : '';
                            $deal_price = isset($menu_data['deal_price']) ? $menu_data['deal_price'] : 0;
                            $deal_image = isset($menu_data['deal_image']) ? $menu_data['deal_image'] : 0;
                            $deal_serving = isset($menu_data['deal_serving']) ? $menu_data['deal_serving'] : 0;
                            $deal_items = isset($menu_data['deal_items']) ? $menu_data['deal_items'] : '';
                            $deal_items_array = (!is_array($deal_items))? explode(',', $deal_items) : $deal_items;

                            $deal_item_icon_img_arr = wp_get_attachment_image_src($deal_image, 'small');
                            $deal_item_icon_img_src = isset($deal_item_icon_img_arr[0]) ? $deal_item_icon_img_arr[0] : '';
                            $deal_item_icon_img_arr_p = wp_get_attachment_image_src($deal_image, 'large');
                            $deal_item_icon_img_src_p = isset($deal_item_icon_img_arr_p[0]) ? $deal_item_icon_img_arr_p[0] : '';
                            ?>
                            <li class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                <div class="media-holder">
                                    <div class="image-holder">
                                        <a href="<?php echo esc_url($deal_item_icon_img_src_p); ?>" rel="prettyPhoto" ><img src="<?php echo esc_url($deal_item_icon_img_src); ?>" alt=""></a>
                                    </div>
                                    <div class="text-holder">
                                        <h6><?php echo $deal_name; ?></h6>
                                        <span class="deal-services">
                                            <?php
                                            $serving_count = 1;
                                            while ($serving_count <= $deal_serving) {
                                                echo '<i class="icon-user" aria-hidden="true"></i>';
                                                $serving_count++;
                                            }
                                            ?>
                                        </span>
                                        <?php
                                        if (!empty($deal_items_array)) {
                                            foreach ($deal_items_array as $deal_item_id) {
                                                $menu_item_data = $Foodbakery_deal_backend->foodbakery_get_menu_item($restaurant_id, $deal_item_id);

                                                $menu_item_title = isset($menu_item_data['menu_item_title']) ? $menu_item_data['menu_item_title'] : '';
                                                $menu_item_unique_id = isset($menu_item_data['menu_item_unique_id']) ? $menu_item_data['menu_item_unique_id'] : '';
                                                $menu_item_description = isset($menu_item_data['menu_item_description']) ? $menu_item_data['menu_item_description'] : '';
                                                $menu_item_icon = isset($menu_item_data['menu_item_icon']) ? $menu_item_data['menu_item_icon'] : '';
                                                $menu_item_nutri = isset($menu_item_data['menu_item_nutri']) ? $menu_item_data['menu_item_nutri'] : '';
                                                $menu_item_price = isset($menu_item_data['menu_item_price']) ? $menu_item_data['menu_item_price'] : 0;
                                                $menu_item_stock = isset($menu_item_data['menu_item_stock']) ? $menu_item_data['menu_item_stock'] : 1000;
                                                $menu_item_extra = isset($menu_item_data['menu_item_extra']) ? $menu_item_data['menu_item_extra'] : 0;
                                                $menu_item_identity = isset($menu_item_data['menu_item_identity']) ? $menu_item_data['menu_item_identity'] : '';

                                                $menu_item_icon_img_arr = wp_get_attachment_image_src($menu_item_icon, 'thumbnail');
                                                $menu_item_icon_img_src = isset($menu_item_icon_img_arr[0]) ? $menu_item_icon_img_arr[0] : '';
                                                $menu_item_icon_img_arr_p = wp_get_attachment_image_src($menu_item_icon, 'large');
                                                $menu_item_icon_img_src_p = isset($menu_item_icon_img_arr_p[0]) ? $menu_item_icon_img_arr_p[0] : '';
                                                ?>

                                                <?php
                                                if (is_array($menu_item_nutri) && sizeof($menu_item_nutri) > 0) {
                                                    ?>
                                                    <ul class="nutri-icons hide">
                                                        <?php
                                                        $nutri_count = 0;
                                                        foreach ($menu_item_nutri as $men_nutri) {
                                                            $menu_nutri_index = is_array($get_foodbakeri_nutri_icons) ? array_search($men_nutri, $get_foodbakeri_nutri_icons) : '';
                                                            $menu_nutri_title = isset($get_foodbakeri_nutri_titles[$menu_nutri_index]) ? $get_foodbakeri_nutri_titles[$menu_nutri_index] : '';
                                                            $men_nutri_icon_img_arr = wp_get_attachment_image_src($men_nutri, 'thumbnail');
                                                            $men_nutri_icon_img_src = isset($men_nutri_icon_img_arr[0]) ? $men_nutri_icon_img_arr[0] : '';
                                                            ?>
                                                            <li><a data-toggle="tooltip" title="<?php echo esc_html($menu_nutri_title) ?>"><img src="<?php echo esc_url($men_nutri_icon_img_src); ?>" alt=""></a></li>
                                                            <?php
                                                            $nutri_count ++;
                                                        }
                                                        ?>
                                                    </ul>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                        <div class="price-holder bgcolor">
                                            <span class="price"><?php echo currency_symbol_possitions_html($currency_sign, $deal_price); ?></span>
                                            <a href="javascript:void(0)" data-deal_id="<?php echo $menu_key; ?>" data-menu_rand_id="<?php echo $menu_key; ?>" data-item_stock="1000" data-menu_item_identity="<?php echo $menu_key; ?>" class="menu_rand_id_<?php echo $menu_key; ?> foodbakery_show_extra_menu_deal_item restaurant-add-deal-btn1 restaurant-add-deal-btn-<?php echo $menu_key; ?>" data-rid="<?php echo $restaurant_id; ?>" data-id="0" data-cid="1"><i class="icon-plus4"></i></a>
                                            <span id="add-menu-loader-0"></span>
                                        </div>

                                    </div>
                                </div>

                            </li>
                    <?php } ?>
                        <div id="show_extra_deals_modal"></div>
                    </ul>
                    <?php
                }
            }
            $deals_html_response = ob_get_clean();
            return $deals_html_response;
        }

        public function foodbakery_show_extra_menu_deal_item_callback() {
            $deal_id = isset($_POST['deal_id']) ? $_POST['deal_id'] : 0;
            $restaurant_id = isset($_POST['restaurant_id']) ? $_POST['restaurant_id'] : 0;
            $Foodbakery_deal_backend = new Foodbakery_deal_backend();
            $deal_data = $Foodbakery_deal_backend->foodbakery_get_menu_deal($restaurant_id, $deal_id);
            $deal_name = isset($deal_data['deal_name']) ? $deal_data['deal_name'] : '';
            $deal_items = isset( $deal_data['deal_items'] )? $deal_data['deal_items'] : '';
            $menu_items = (!is_array($deal_items)) ? explode(',', $deal_data['deal_items']) : $deal_items;

            if (!empty($menu_items)) {
                ?>

                <div class="modal fade menu-extras-modal extras-deal-modal add_extrass fb-fancy-extras" id="extras-<?php echo $deal_id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">


                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h2><a><?php echo $deal_name; ?></a></h2>
                        </div>
                        <div class="modal-body">
                            <div class="menu-selection-container">
                                <div class="extras-detail-main1">
                                    <div class="deal-tabs-modal">
                                        <ul class="nav-tabs">

                                            <?php
                                            if (!empty($menu_items)) {
                                                $deal_item_counter = 1;
                                                foreach ($menu_items as $menu_item_key) {
                                                    $menu_item_data = $Foodbakery_deal_backend->foodbakery_get_menu_item($restaurant_id, $menu_item_key);
                                                    $menu_item_title = ( $menu_item_data['menu_item_title'] ) ? $menu_item_data['menu_item_title'] : '';
                                                    $menu_item_identity = ( $menu_item_data['menu_item_identity'] ) ? $menu_item_data['menu_item_identity'] : '';

                                                    $active_class = ( $deal_item_counter == 1) ? 'active' : '';
                                                    echo '<li class="' . $active_class . '"><a data-toggle="tab" href="#deal_item_' . $menu_item_identity . '">' . $menu_item_title . '</a></li>';
                                                    $deal_item_counter++;
                                                }
                                            }
                                            ?>

                                        </ul>
                                    </div>
                                    <div class="tab-content">

                                        <?php
                                        if (!empty($menu_items)) {
                                            $deal_item_counter = 1;
                                            foreach ($menu_items as $menu_item_key) {
                                                $menu_item_data = $Foodbakery_deal_backend->foodbakery_get_menu_item($restaurant_id, $menu_item_key);

                                                $menu_item_icon = isset($menu_item_data['menu_item_icon']) ? $menu_item_data['menu_item_icon'] : '';

                                                $menu_item_icon_img_arr = wp_get_attachment_image_src($menu_item_icon, 'small');
                                                $menu_item_icon_img_src = isset($menu_item_icon_img_arr[0]) ? $menu_item_icon_img_arr[0] : '';
                                                $menu_item_icon_img_arr_p = wp_get_attachment_image_src($menu_item_icon, 'large');
                                                $menu_item_icon_img_src_p = isset($menu_item_icon_img_arr_p[0]) ? $menu_item_icon_img_arr_p[0] : '';


                                                $menu_item_title = ( $menu_item_data['menu_item_title'] ) ? $menu_item_data['menu_item_title'] : '';
                                                $menu_item_identity = ( $menu_item_data['menu_item_identity'] ) ? $menu_item_data['menu_item_identity'] : '';

                                                $menu_item_extra = isset($menu_item_data['menu_item_extra']) ? $menu_item_data['menu_item_extra'] : array();
                                                $menu_item_description = isset($menu_item_data['menu_item_description']) ? $menu_item_data['menu_item_description'] : '';
                                                $menu_item_calories = ( $menu_item_data['menu_item_calories'] ) ? $menu_item_data['menu_item_calories'] : 0;


                                                $active_class = ( $deal_item_counter == 1) ? 'active' : '';
                                                ?>

                                                <div id="deal_item_<?php echo $menu_item_identity; ?>" class="tab-pane fade in <?php echo $active_class; ?>">
                                                    <div class="extras-detail-main1">
                                                        <div class="main-holder">
                                                            <figure><img src="<?php echo esc_url($menu_item_icon_img_src_p); ?>"></figure>
                                                        </div>
                                                        <div class="extras-detail-options">
                                                            <div class="text-holder" style="margin-bottom: 30px;">
                                                                <h2 style="display: block; float: none; margin-bottom: 15px;"><?php echo esc_html($menu_item_title); ?>
                                                                <?php
                                                                    if( $menu_item_calories > 0){ 
                                                                        echo '<i class="icon-cutlery" style="font-size: 18px;background: #df6f6f;color: #ffffff;padding: 6px;border-radius: 50%;" title="Calories: '.$menu_item_calories.'"></i>';
                                                                    }
                                                                    ?>
                                                                </h2>
                                                                <span><?php echo esc_html($menu_item_description); ?></span>
                                                            </div>


                                                            <?php
                                                            if (isset($menu_item_extra[0]['title']) && is_array($menu_item_extra[0]['title']) && sizeof($menu_item_extra[0]['title']) > 0) {
                                                                foreach ($menu_item_extra['heading'] as $key => $value) {
                                                                    $type_value = isset($menu_item_extra['type'][$key]) ? $menu_item_extra['type'][$key] : '';
                                                                    $required_num_value = isset($menu_item_extra['required'][$key]) ? $menu_item_extra['required'][$key] : 0;
                                                                    $menu_item_extra_titles = isset($menu_item_extra[$key]['title']) ? $menu_item_extra[$key]['title'] : array();
                                                                    $menu_item_extra_prices = isset($menu_item_extra[$key]['price']) ? $menu_item_extra[$key]['price'] : array();
                                                                    $menu_item_extra_images = isset($menu_item_extra[$key]['image']) ? $menu_item_extra[$key]['image'] : array();
                                                                    $menu_item_extra_calories_array = isset($menu_item_extra[$key]['calories']) ? $menu_item_extra[$key]['calories'] : array();


                                                                    if (is_array($menu_item_extra_titles) && sizeof($menu_item_extra_titles) > 0) {
                                                                        $menu_extra_att_counter = 0;
                                                                        ?>
                                                                        <div class="extras-detail-main" id="menu_idd_<?php echo $menu_extra_counter; ?>">
                                                                            <input type="hidden" name="required_count" value="<?php echo $required_num_value; ?>">


                                                                            <div class="header-deal">

                                                                                <h2><?php echo esc_html($value); ?>
                                                                                    <?php if ($required_num_value != '') { ?>
                                                                                        <span class="required_extras"><?php echo esc_html__('Required ', 'foodbakery') . $required_num_value; ?></span>
                                                                                <?php } ?> </h2>
                                                                            </div>
                                                                            <div class="extras-options-inner">
                                                                                <?php
                                                                                foreach ($menu_item_extra_titles as $key => $menu_item_extra_title) {
                                                                                    $menu_item_extra_price = isset($menu_item_extra_prices[$key]) ? $menu_item_extra_prices[$key] : '';
                                                                                    $menu_item_extra_image = isset($menu_item_extra_images[$key]) ? $menu_item_extra_images[$key] : '';
                                                                                    $menu_item_extra_calories = isset($menu_item_extra_calories_array[$key]) ? $menu_item_extra_calories_array[$key] : 0;
                                                                                    $menu_extra_item_icon_img_arr = wp_get_attachment_image_src($menu_item_extra_image, 'small');
                                                                                    $menu_extra_item_icon_img_src = isset($menu_extra_item_icon_img_arr[0]) ? $menu_extra_item_icon_img_arr[0] : '';

                                                                                    $field_type = '';
                                                                                    if ($type_value == 'single') {
                                                                                        $field_type = 'radio';
                                                                                    } else {
                                                                                        $field_type = 'checkbox';
                                                                                    }
                                                                                    ?>


                                                                                    <div class="extras-detail-att">
                                                                                        <input type="<?php echo $field_type; ?>" data-menu_id="<?php echo $menu_item_key; ?>" id="extra-<?php echo absint($menu_extra_att_counter) ?>-<?php echo absint($menu_extra_counter) ?>-<?php echo absint($menu_items_loop) ?>" data-ind="<?php echo absint($menu_extra_att_counter) ?>" data-menucat-id="<?php echo isset($menu_loop) ? absint($menu_loop) : '' ?>" data-menu-id="<?php echo absint($menu_items_loop) ?>" name="extra-<?php echo absint($menu_extra_counter) ?>-<?php echo absint($menu_items_loop) ?>">
                                                                                        <label for="extra-<?php echo absint($menu_extra_att_counter) ?>-<?php echo absint($menu_extra_counter) ?>-<?php echo absint($menu_items_loop) ?>">
                                                                                            <figure><img src="<?php echo $menu_extra_item_icon_img_src; ?>"></figure>
                                                                                            <span class="extra-title"><?php echo esc_html($menu_item_extra_title) ?></span>
                                                                                            <span class="extra-price"><?php echo foodbakery_get_currency($menu_item_extra_price, true); ?></span>
                                                                                            <?php
                                                                                            if( $menu_item_extra_calories > 0){ 
                                                                                                echo '<span class="extra-price extra-calories">'. $menu_item_extra_calories .' K</span>';
                                                                                            }
                                                                                            ?>
                                                                                        </label>
                                                                                    </div>
                                                                                    <?php
                                                                                    $menu_extra_att_counter ++;
                                                                                }
                                                                                ?>
                                                                            </div>
                                                                            <div class="extras-detail-selected"></div>
                                                                        </div>
                                                                        <hr class="seperator-border"/>
                                                                        <?php
                                                                        $menu_extra_counter ++;
                                                                    }
                                                                }
                                                            }
                                                            ?>

                                                        </div>
                                                    </div>
                                                </div>


                                                <?php
                                                $deal_item_counter++;
                                            }
                                        }
                                        ?>

                                    </div>
                                </div>
                                <div class=""></div>
                            </div>


                        </div>
                    </div>
                    <div class="modal-footer">
                        <a data-menucat-id="<?php echo absint($data_cat_id) ?>" data-deal_id="<?php echo $deal_id; ?>" data-unique-menu-id="<?php echo isset($deal_id) ? absint($deal_id) : '' ?>" data-menu-id="<?php echo absint($menu_items_loop) ?>" data-rid="<?php echo absint($restaurant_id) ?>" class="add-extra-deal-btn btn-add-to-cart input-button-loader"><?php esc_html_e('Add to Cart', 'foodbakery') ?></a>
                    </div>
                </div>
                <?php
            }

            exit;
        }

        public function foodbakery_restaurant_add_deal_item_callback() {
            global $foodbakery_plugin_options, $current_user;

            $foodbakery_currency_sign = foodbakery_get_currency_sign();
            $rand_numb = rand(10000000, 99999999);
            $rand_numb_class = isset($_POST['rand_number']) ? $_POST['rand_number'] : $rand_numb;
            $restaurant_id = foodbakery_get_input('_rid', 0);
            $menu_cat_id = foodbakery_get_input('menu_cat_id', 0);
            $menu_item_id = foodbakery_get_input('menu_id', 0);
            $menu_rand_id = foodbakery_get_input('menu_rand_id', 0);
            $menu_item_identity = foodbakery_get_input('menu_item_identity', 0);
            $menu_updating = isset($_POST['act_updating']) ? $_POST['act_updating'] : '';
            $menu_extra_atts = isset($_POST['extra_atts']) ? $_POST['extra_atts'] : '';
            $menu_extra_atts = stripslashes($menu_extra_atts);
            $menu_extra_atts = json_decode($menu_extra_atts);
            $menu_extra_atts = (array) $menu_extra_atts;
            $extra_name = isset($_POST['extra_name']) ? $_POST['extra_name'] : '';

            $extra_name = stripslashes($extra_name);
            $extra_name = json_decode($extra_name);
            //pre($menu_extra_atts);

            $unique_menu_id = isset($_POST['menu_unique_id_']) ? $_POST['menu_unique_id_'] : '';
            $deal_id = isset($_POST['deal_id']) ? $_POST['deal_id'] : '';

            $Foodbakery_deal_backend = new Foodbakery_deal_backend();
            $deal_data = $Foodbakery_deal_backend->foodbakery_get_menu_deal($restaurant_id, $deal_id);
            $menu_items = isset($deal_data['deal_items']) ? $deal_data['deal_items'] : array();
            $menu_items = (!is_array($menu_items)) ? explode(',', $menu_items) : $menu_items;

            $restaurant_menu_list = get_post_meta($restaurant_id, 'foodbakery_menu_items', true);
            $menu_deals = get_post_meta($restaurant_id, 'foodbakery_menu_deals', true);
            $jus_pl = false;
            if (!is_user_logged_in()) {
                $jus_pl = true;
            } else {
                $user_id = $current_user->ID;
                $publisher_id = foodbakery_company_id_form_user_id($user_id);

                if ($publisher_id != '') {
                    $publisher_type = get_post_meta($publisher_id, 'foodbakery_publisher_profile_type', true);
                    if ($publisher_type != 'restaurant' && $publisher_type != '') {

                        $menu_t_price = 0;

                        // menu title
                        $this_item_title = isset($menu_deals[$deal_id]['deal_name']) ? $menu_deals[$deal_id]['deal_name'] : '';
                        // menu price
                        $this_item_price = isset($menu_deals[$deal_id]['deal_price']) ? $menu_deals[$deal_id]['deal_price'] : 0;

                        $menu_t_price += floatval($this_item_price);
                        $extras_arra = array();
                        $extras_html = '';
                        if (!empty($menu_extra_atts)) {

                            //$menu_extra_atts = explode(',', $menu_extra_atts);
                            //$extra_name = explode(',', $extra_name);
                            if (is_array($menu_extra_atts)) {

                                foreach ($menu_extra_atts as $menu_id => $menu_extra_array_data) {
                                    $menu_extra_array_data = (array) $menu_extra_array_data;
                                    // menu extras
                                    //$this_item_extras = isset($restaurant_menu_list[$menu_id]['menu_item_extra']) ? $restaurant_menu_list[$menu_id]['menu_item_extra'] : '';
                                    $menu_data_array = $Foodbakery_deal_backend->foodbakery_get_menu_item($restaurant_id, $menu_id);
                                    $menu_item_title = isset($menu_data_array['menu_item_title']) ? '<br>' . $menu_data_array['menu_item_title'] : '';
                                    //pre($menu_extra_array_data);
                                    $this_item_extras = isset($menu_data_array['menu_item_extra']) ? $menu_data_array['menu_item_extra'] : array();


                                    //  $menu_ext_counter = 0;

                                    foreach ($menu_extra_array_data as $key => $menu_extra_att) {


                                        //$this_item_heading = isset($restaurant_menu_list[$menu_item_id]['menu_item_extra']['heading'][$extra_name[$key]]) ? $restaurant_menu_list[$menu_item_id]['menu_item_extra']['heading'][$extra_name[$key]] : '';
                                        
                                        $menu_extra_at_label = isset($this_item_extras[$key]['title'][$menu_extra_att]) ? $this_item_extras[$key]['title'][$menu_extra_att] : '';
                                        $menu_extra_at_price = isset($this_item_extras[$key]['price'][$menu_extra_att]) ? $this_item_extras[$key]['price'][$menu_extra_att] : 0;


                                        if( $menu_extra_at_label != ''){
                                            $extras_arra[] = array(
                                                'title' => $menu_extra_at_label,
                                                'price' => $menu_extra_at_price,
                                                'title_id' => $key,
                                                'menu_id' => $menu_id,
                                                'menu_item_id' => $menu_extra_att,
                                                'menu_item_title' => strip_tags($menu_item_title),
                                                'deal_id' => $deal_id,
                                            );

                                            $extras_html .= '<li>' . strip_tags($menu_item_title) . ' - ' . $menu_extra_at_label . ' : <span class="category-price">' . foodbakery_get_currency($menu_extra_at_price, true) . '</span></li>';

                                            $menu_t_price += floatval($menu_extra_at_price);
                                        }

                                        // $menu_ext_counter ++;
                                    }
                                }
                            }
                            //pre($extras_arra);
                        }

                        $get_added_menus = get_transient('add_menu_items_' . $publisher_id);

                        if (empty($get_added_menus) && isset($_COOKIE['add_menu_items_temp']) && !is_user_logged_in()) {
                            $get_added_menus = unserialize(stripslashes($_COOKIE['add_menu_items_temp']));
                        }

                        if ($menu_updating == 'true') {
                            $menu_index = isset($_POST['menu_index']) ? $_POST['menu_index'] : '';
                            if (isset($get_added_menus[$restaurant_id][$menu_index])) {
                                $updated_menu = array(
                                    'menu_cat_id' => $menu_cat_id,
                                    'menu_id' => $menu_item_id,
                                    'price' => $this_item_price,
                                    'unique_id' => $rand_numb,
                                    'deal_item' => true,
                                    'unique_menu_id' => $unique_menu_id,
                                    'menu_item_identity' => $menu_item_identity,
                                    'extras' => $extras_arra,
                                    'deal_id' => $deal_id,
                                );
                                $get_added_menus[$restaurant_id][$menu_index] = $updated_menu;
                            }
                        } else {
                            if (is_array($get_added_menus) && sizeof($get_added_menus) > 0) {

                                $get_added_menus[$restaurant_id][] = array(
                                    'menu_cat_id' => $menu_cat_id,
                                    'menu_id' => $menu_item_id,
                                    'price' => $this_item_price,
                                    'unique_id' => $rand_numb,
                                    'deal_item' => true,
                                    'unique_menu_id' => $unique_menu_id,
                                    'menu_item_identity' => $menu_item_identity,
                                    'extras' => $extras_arra,
                                    'deal_id' => $deal_id,
                                );
                            } else {
                                $get_added_menus = array();
                                $get_added_menus[$restaurant_id][] = array(
                                    'menu_cat_id' => $menu_cat_id,
                                    'menu_id' => $menu_item_id,
                                    'price' => $this_item_price,
                                    'deal_item' => true,
                                    'unique_id' => $rand_numb,
                                    'unique_menu_id' => $unique_menu_id,
                                    'menu_item_identity' => $menu_item_identity,
                                    'extras' => $extras_arra,
                                    'deal_id' => $deal_id,
                                );
                            }
                        }


                        $get_added_menus_array = array();
                        $get_added_menus_array['unique_id'] = $rand_numb;
                        ob_start();
                        do_action('foodbakery_cart_order_item_details', $get_added_menus_array, $restaurant_id, $menu_rand_id);

                        $foodbakery_cart_order_item_details = ob_get_clean();

                        $li_html = '
				<li class="menu-added-' . $rand_numb_class . '" id="menu-added-' . $rand_numb . '" data-pr="' . foodbakery_get_currency($menu_t_price, false, '', '', false) . '" data-conpr="' . foodbakery_get_currency($menu_t_price, false, '', '', true) . '">
					<a href="javascript:void(0)" class="btn-cross dev-remove-menu-item"><i class=" icon-cross3"></i></a>
					<a>' . $this_item_title . '</a>
					<span class="category-price">' . foodbakery_get_currency($this_item_price, true) . '</span>';

                        $li_html .= $foodbakery_cart_order_item_details;
                        if ($extras_html != '') {
                            $li_html .= '<ul>';
                            $li_html .= $extras_html;
                            $li_html .= '</ul>';

                            $popup_id = 'edit_extras-' . $menu_cat_id . '-' . $menu_item_id;
                            $data_id = $menu_item_id;
                            $ajax_url = admin_url('admin-ajax.php');
                            $array_latest_added_menu = count($get_added_menus[$restaurant_id]) - 1;
                            $unique_id = $get_added_menus[$restaurant_id][$array_latest_added_menu]['unique_id'];
                            $extra_child_menu_id = isset($get_added_menus[$restaurant_id][$array_latest_added_menu]['extra_child_menu_id']) ? $get_added_menus[$restaurant_id][$array_latest_added_menu]['extra_child_menu_id'] : '';
                            //$li_html .= '<a href="javascript:void(0);" class="edit-menu-item update_menu_' . $rand_numb_class . '" onClick="foodbakery_edit_extra_menu_item(\'' . $popup_id . '\',\'' . $data_id . '\',\'' . $menu_cat_id . '\',\'' . $rand_numb_class . '\',\'' . $ajax_url . '\',\'' . $restaurant_id . '\',\'' . $unique_id . '\',\'' . $unique_menu_id . '\',\'' . $extra_child_menu_id . '\');">' . esc_html__('Edit', 'foodbakery') . '</a>';
                            //$li_html .= '<a href="javascript:void(0)" data-toggle="modal" data-target="#extras-' . $menu_cat_id . '-' . $menu_item_id . '" data-id="' . $menu_item_id . '" data-cid="' . $menu_cat_id . '" data-rand="' . $rand_numb . '" class="update-menu dev-update-menu-btn">' . esc_html__('Edit', 'foodbakery') . '</a>';
                        }
                        $li_html .= '
						</li>';
                        set_transient('add_menu_items_' . $publisher_id, $get_added_menus, 60 * 60 * 24 * 30);

                        if ($menu_updating == 'true') {
                            //$json = array('msg' => esc_html__('Menu item have been updated in your basket.', 'foodbakery'), 'type' => 'success', 'li_html' => $li_html);
                            $json = array('li_html' => $li_html);
                        } else {
                            //$json = array('msg' => esc_html__('Menu item have been added in your basket', 'foodbakery'), 'type' => 'success', 'li_html' => $li_html);
                            $json = array('li_html' => $li_html);
                        }
                    } else {
                        $jus_pl = true;
                    }
                } else {
                    $jus_pl = true;
                }
            }
            if ($jus_pl) {
                $menu_t_price = 0;

                // menu title
                $this_item_title = isset($menu_deals[$deal_id]['deal_name']) ? $menu_deals[$deal_id]['deal_name'] : '';
                // menu price
                $this_item_price = isset($menu_deals[$deal_id]['deal_price']) ? $menu_deals[$deal_id]['deal_price'] : 0;
                
                $deal_items = isset( $menu_deals[$deal_id] )? $menu_deals[$deal_id] : array();
                
                
                
                //pre($menu_deals[$deal_id]);

                $menu_t_price += floatval($this_item_price);
                $extras_arra = array();
                $extras_html = '';
                if (!empty($menu_extra_atts)) {

                    //$menu_extra_atts = explode(',', $menu_extra_atts);
                    //$extra_name = explode(',', $extra_name);
                    if (is_array($menu_extra_atts)) {

                        foreach ($menu_extra_atts as $menu_id => $menu_extra_array_data) {
                            $menu_extra_array_data = (array) $menu_extra_array_data;
                            // menu extras
                            //$this_item_extras = isset($restaurant_menu_list[$menu_id]['menu_item_extra']) ? $restaurant_menu_list[$menu_id]['menu_item_extra'] : '';
                            $menu_data_array = $Foodbakery_deal_backend->foodbakery_get_menu_item($restaurant_id, $menu_id);
                            $menu_item_title = isset($menu_data_array['menu_item_title']) ? '<br>' . $menu_data_array['menu_item_title'] : '';
                            //pre($menu_extra_array_data);
                            $this_item_extras = isset($menu_data_array['menu_item_extra']) ? $menu_data_array['menu_item_extra'] : array();

                            //  $menu_ext_counter = 0;

                            foreach ($menu_extra_array_data as $key => $menu_extra_att) {

                                //$this_item_heading = isset($restaurant_menu_list[$menu_item_id]['menu_item_extra']['heading'][$extra_name[$key]]) ? $restaurant_menu_list[$menu_item_id]['menu_item_extra']['heading'][$extra_name[$key]] : '';
                                $menu_extra_at_label = isset($this_item_extras[$key]['title'][$menu_extra_att]) ? $this_item_extras[$key]['title'][$menu_extra_att] : '';
                                $menu_extra_at_price = isset($this_item_extras[$key]['price'][$menu_extra_att]) ? $this_item_extras[$key]['price'][$menu_extra_att] : 0;
                                
                                if( $menu_extra_at_label != ''){
                                    $extras_arra[] = array(
                                        'title' => $menu_extra_at_label,
                                        'price' => $menu_extra_at_price,
                                        'title_id' => $key,
                                        'menu_id' => $menu_id,
                                        'menu_item_id' => $menu_extra_att,
                                        'menu_item_title' => strip_tags($menu_item_title),
                                        'deal_id' => $deal_id,
                                    );

                                    $extras_html .= '<li>' . strip_tags($menu_item_title) . ' - ' . $menu_extra_at_label . ' : <span class="category-price">' . foodbakery_get_currency($menu_extra_at_price, true) . '</span></li>';

                                    $menu_t_price += floatval($menu_extra_at_price);
                                }

                                // $menu_ext_counter ++;
                            }
                        }
                    }
                    //pre($extras_arra);
                }


                $get_added_menus = '';

                if (isset($_COOKIE['add_menu_items_temp'])) {
                    $get_added_menus = unserialize(stripslashes($_COOKIE['add_menu_items_temp']));
                }

                if ($menu_updating == 'true') {
                    $menu_index = isset($_POST['menu_index']) ? $_POST['menu_index'] : '';
                    if (isset($get_added_menus[$restaurant_id][$menu_index])) {
                        $updated_menu = array(
                            'menu_cat_id' => $menu_cat_id,
                            'menu_id' => $menu_item_id,
                            'price' => $this_item_price,
                            'unique_id' => $rand_numb,
                            'deal_item' => true,
                            'unique_menu_id' => $unique_menu_id,
                            'menu_item_identity' => $menu_item_identity,
                            'extra_child_menu_id' => rand(10000000, 99999999),
                            'extras' => $extras_arra,
                            'deal_id' => $deal_id,
                        );
                        $get_added_menus[$restaurant_id][$menu_index] = $updated_menu;
                    }
                } else {
                    if (is_array($get_added_menus) && sizeof($get_added_menus) > 0) {

                        $get_added_menus[$restaurant_id][] = array(
                            'menu_cat_id' => $menu_cat_id,
                            'menu_id' => $menu_item_id,
                            'price' => $this_item_price,
                            'unique_id' => $rand_numb,
                            'deal_item' => true,
                            'unique_menu_id' => $unique_menu_id,
                            'extra_child_menu_id' => rand(10000000, 99999999),
                            'menu_item_identity' => $menu_item_identity,
                            'extras' => $extras_arra,
                            'deal_id' => $deal_id,
                        );
                    } else {
                        $get_added_menus = array();
                        $get_added_menus[$restaurant_id][] = array(
                            'menu_cat_id' => $menu_cat_id,
                            'menu_id' => $menu_item_id,
                            'price' => $this_item_price,
                            'unique_id' => $rand_numb,
                            'deal_item' => true,
                            'unique_menu_id' => $unique_menu_id,
                            'menu_item_identity' => $menu_item_identity,
                            'extras' => $extras_arra,
                            'deal_id' => $deal_id,
                        );
                    }
                }

                $get_added_menus_array = array();
                $get_added_menus_array['unique_id'] = $rand_numb;

                $li_html = '
				<li class="menu-added-' . $rand_numb_class . '" id="menu-added-' . $rand_numb . '" data-pr="' . foodbakery_get_currency($menu_t_price, false, '', '', false) . '" data-conpr="' . foodbakery_get_currency($menu_t_price, false, '', '', true) . '">
			    <a href="javascript:void(0)" class="btn-cross dev-remove-menu-item"><i class=" icon-cross3"></i></a>
			    <a>' . $this_item_title . '</a>
			    <span class="category-price">' . foodbakery_get_currency($this_item_price, true) . '</span>';
                if ($extras_html != '') {
                    $li_html .= '<ul>';
                    $li_html .= $extras_html;
                    $li_html .= '</ul>';
                    $popup_id = 'edit_extras-' . $menu_cat_id . '-' . $menu_item_id;
                    $data_id = $menu_item_id;
                    $ajax_url = admin_url('admin-ajax.php');
                    $array_latest_added_menu = count($get_added_menus[$restaurant_id]) - 1;
                    $unique_id = isset($get_added_menus[$restaurant_id][$array_latest_added_menu]['unique_id']) ? $get_added_menus[$restaurant_id][$array_latest_added_menu]['unique_id'] : '';
                    $extra_child_menu_id = isset($get_added_menus[$restaurant_id][$array_latest_added_menu]['extra_child_menu_id']) ? $get_added_menus[$restaurant_id][$array_latest_added_menu]['extra_child_menu_id'] : '';
                    //$li_html .= '<a href="javascript:void(0);" class="edit-menu-item update_menu_' . $rand_numb_class . '" onClick="foodbakery_edit_extra_menu_item(\'' . $popup_id . '\',\'' . $data_id . '\',\'' . $menu_cat_id . '\',\'' . $rand_numb_class . '\',\'' . $ajax_url . '\',\'' . $restaurant_id . '\',\'' . $unique_id . '\',\'' . $unique_menu_id . '\',\'' . $extra_child_menu_id . '\');">' . esc_html__('Edit', 'foodbakery') . '</a>';
                }
                $li_html .= '</li>';




                setcookie('add_menu_items_temp', serialize($get_added_menus), time() + (10 * 365 * 24 * 60 * 60), '/');
                if ($menu_updating == 'true') {
                    //$json = array( 'msg' => esc_html__('Menu item have been updated in your basket.', 'foodbakery'), 'type' => 'success', 'li_html' => $li_html );
                    $json = array('li_html' => $li_html);
                } else {
                    //$json = array( 'msg' => esc_html__('Menu item have been added in your basket', 'foodbakery'), 'type' => 'success', 'li_html' => $li_html );
                    $json = array('li_html' => $li_html);
                }
            }
            echo json_encode($json);

            die;
        }

        public function foodbakery_add_deal_frontend_callback() {
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
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <div class="field-holder">
                            <label>Deal Image</label>
                            <div id="browse-btn-sec-1" class="browse-btn-sec" style="display: bolck !important;">
                                <input type="file" id="image-icon-1" data-id="1" name="image_icon_1" class="browse-menu-icon-file" style="display: none;">
                                <a id="browse-menu-icon-img-1" href="javascript:void(0)" class="browse-menu-icon-img btn bgcolor" data-id="1">Browse</a>
                            </div>
                            <div id="browse-img-sec-1" class="browse-image-sec" style="display: none !important;">
                                <div class="icon-img-holder">
                                    <a href="javascript:void(0)" data-id="1" class="remove-icon"><i class="icon-close2"></i></a>
                                    <img id="img-val-base-1" src="" alt=""  style="max-width:40px;">
                                </div>
                                <input id="hiden-img-val-1" type="hidden">
                            </div>
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
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="field-holder">
                        <a class="add-menu-item submit-deal-form-frontend" href="javascript:void(0);">Save</a>
                    </div>
                </div>
            </div>
            <?php
            exit;
        }

        public function foodbakery_add_deal_form_frontend_callback() {
            $deal_name = isset($_POST['deal_name']) ? $_POST['deal_name'] : '';
            $deal_price = isset($_POST['deal_price']) ? $_POST['deal_price'] : 0;
            $deal_image = isset($_POST['deal_image']) ? $_POST['deal_image'] : 0;
            $deal_serving = isset($_POST['deal_serving']) ? $_POST['deal_serving'] : 0;
            $foodbakery_deal_menu_items = isset($_POST['foodbakery_deal_menu_items']) ? $_POST['foodbakery_deal_menu_items'] : '';
            $currency_sign = foodbakery_get_currency_sign();
            $random_id = rand(100000, 9000000);
            ?>
            <div id="collapse-012345" class="panel-collapse collapse in">
                <li class="menu-item-<?php echo $random_id; ?>">
                    <div class="drag-list">
                        <span class="drag-option ui-sortable-handle"><i class="icon-bars"></i></span>

                        <div class="list-title">
                            <h6><?php echo $deal_name; ?></h6>
                            <input type="hidden" name="deal_data_name[<?php echo $random_id; ?>]" value="<?php echo $deal_name; ?>">

                        </div>
                        <div class="list-price">
                            <span><?php echo currency_symbol_possitions_html('<b>' . $currency_sign . '</b>', '<b>' . $deal_price . '</b>'); ?></span>
                            <input type="hidden" name="deal_data_price[<?php echo $random_id; ?>]" value="<?php echo $deal_price; ?>">
                        </div>
                        <div class="list-option">
                            <input type="hidden" name="deal_data_serving[<?php echo $random_id; ?>]" value="<?php echo $deal_serving; ?>">
                            <input type="hidden" name="deal_data_image[<?php echo $random_id; ?>]" value="<?php echo $deal_image; ?>">
                            <input type="hidden" name="deal_data_items[<?php echo $random_id; ?>]" value="<?php echo $foodbakery_deal_menu_items; ?>">
                            
                            <a href="javascript:void(0);" class="remove-menu-item" onclick="foodbakery_remove_menu_item('<?php echo $random_id; ?>');"><i class="icon-close2"></i></a>
                        </div>
                    </div>
                </li>
            </div>
            <?php
            exit;
        }

        public function foodbakery_menu_deals_frontend($restaurant_id) {
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
                    <div id="collapse-012345" class="panel-collapse collapse in">
                        <li class="menu-item-<?php echo $menu_key; ?>">
                            <div class="drag-list">
                                <span class="drag-option ui-sortable-handle"><i class="icon-bars"></i></span>

                                <div class="list-title">
                                    <h6><?php echo $deal_name; ?></h6>
                                    <input type="hidden" name="deal_data_name[<?php echo $menu_key; ?>]" value="<?php echo $deal_name; ?>">

                                </div>
                                <div class="list-price">
                                    <span><?php echo currency_symbol_possitions_html('<b>' . $currency_sign . '</b>', '<b>' . $deal_price . '</b>'); ?></span>
                                    <input type="hidden" name="deal_data_price[<?php echo $menu_key; ?>]" value="<?php echo $deal_price; ?>">
                                </div>
                                <div class="list-option">
                                    <input type="hidden" name="deal_data_serving[<?php echo $menu_key; ?>]" value="<?php echo $deal_serving; ?>">
                                    <input type="hidden" name="deal_data_image[<?php echo $menu_key; ?>]" value="<?php echo $deal_image; ?>">
                                    <input type="hidden" name="deal_data_items[<?php echo $menu_key; ?>]" value="<?php echo $deal_items; ?>">
                                    <a href="javascript:void(0);" class="edit-menu-item edit-deal-item"><i class="icon-mode_edit"></i></a>
                                    <a href="javascript:void(0);" class="remove-menu-item remove-deal_id-item"><i class="icon-close2"></i></a>
                                </div>
                            </div>
                            <?php $this->foodbakery_edit_deal_form_frontend_callback($menu_key, $restaurant_id, $menu_data); ?>
                        </li>
                        
                    </div>
                    <?php
                }
            }
            $menu_html_response = ob_get_clean();
            return $menu_html_response;
            ?>

            <?php
        }
        
        public function foodbakery_edit_deal_form_frontend_callback($menu_key, $restaurant_id, $menu_data) {
            global $foodbakery_html_fields;
            $get_restaurant_menu_items = get_post_meta($restaurant_id, 'foodbakery_menu_items', true);
            $deal_items = isset( $menu_data['deal_items'] )? $menu_data['deal_items'] : 0;
            $deal_image = isset( $menu_data['deal_image'] )? $menu_data['deal_image'] : 0;

            $menu_items_array = array();
            if (!empty($get_restaurant_menu_items)) {
                foreach ($get_restaurant_menu_items as $menu_item_data) {
                    $menu_item_identity = isset($menu_item_data['menu_item_identity']) ? $menu_item_data['menu_item_identity'] : 0;
                    $menu_item_title = isset($menu_item_data['menu_item_title']) ? $menu_item_data['menu_item_title'] : '';
                    $menu_items_array[$menu_item_identity] = $menu_item_title;
                }
            }
            $deal_image_src = wp_get_attachment_url($deal_image);
            $deal_image_src_display = ( $deal_image > 0)? 'display: block !important;': 'display: none !important;';
            $deal_image_src_browse_display = ( $deal_image > 0)? 'display: none !important;': 'display: block !important;';
            ?>

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 deal-form-block foodbakery-deal-edit-form" style="display:none;">

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <a href="javascript:void(0);" class="close-menu-item close-deal_add-item"><i class="icon-close2"></i></a>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="field-holder">
                            <label>Deal Name *</label>
                            <input class="menu-item-title" id="deal_name" name="deal_data_name[<?php echo $menu_key; ?>]" value="<?php echo isset( $menu_data['deal_name'] )? $menu_data['deal_name'] : ''; ?>" type="text" placeholder="Deal Name">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="field-holder">
                            <label>Deal Price *</label>
                            <input class="menu-item-price" id="deal_price" name="deal_data_price[<?php echo $menu_key; ?>]" value="<?php echo isset( $menu_data['deal_price'] )? $menu_data['deal_price'] : 0; ?>" type="number" placeholder="Deal Price">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="field-holder">
                            <label>Serving *</label>
                            <input class="menu-item-serving" id="deal_serving" name="deal_data_serving[<?php echo $menu_key; ?>]" min="0" value="<?php echo isset( $menu_data['deal_serving'] )? $menu_data['deal_serving'] : 0; ?>" type="number" placeholder="Deal Serving">
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                       
                        <div class="field-holder">
                            <label>Deal Image</label>
                            <div id="browse-btn-sec-<?php echo $menu_key; ?>" class="browse-btn-sec" style="<?php echo $deal_image_src_browse_display; ?>">
                                <input type="file" id="image-icon-<?php echo $menu_key; ?>" data-id="<?php echo $menu_key; ?>" name="image_icon_<?php echo $menu_key; ?>" class="browse-menu-icon-file" style="display: none;">
                                <a id="browse-menu-icon-img-<?php echo $menu_key; ?>" href="javascript:void(0)" class="browse-menu-icon-img btn bgcolor" data-id="<?php echo $menu_key; ?>">Browse</a>
                            </div>
                            <div id="browse-img-sec-<?php echo $menu_key; ?>" class="browse-image-sec" style="<?php echo $deal_image_src_display; ?>">
                                <div class="icon-img-holder">
                                    <a href="javascript:void(0)" data-id="<?php echo $menu_key; ?>" class="remove-icon"><i class="icon-close2"></i></a>
                                    <img id="img-val-base-<?php echo $menu_key; ?>" src="<?php echo $deal_image_src; ?>" alt="" style="max-width:40px;">
                                </div>
                                <input id="hiden-img-val-<?php echo $menu_key; ?>" name="deal_data_image[<?php echo $menu_key; ?>]" value="<?php echo $deal_image; ?>" type="hidden">
                            </div>
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
                                    'cust_name' => 'deal_data_items['.$menu_key.'][]',
                                    'id' => 'deal_menu_items',
                                    'classes' => 'chosen-select-no-single chosen-select',
                                    'options' => $menu_items_array,
                                ),
                            );

                            $foodbakery_html_fields->foodbakery_select_field($foodbakery_opt_array);
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
            <?php
        }

    }

    new Foodbakery_deal_frontend();
}
