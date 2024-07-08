<?php
/**
 * The template for displaying single restaurant
 *
 */
get_header();
global $post, $foodbakery_plugin_options, $foodbakery_theme_options, $current_user, $foodbakery_booking_element;

$foodbakery_currency_sign = foodbakery_get_currency_sign();
$foodbakery_vat_switch = isset($foodbakery_plugin_options['foodbakery_vat_switch']) ? $foodbakery_plugin_options['foodbakery_vat_switch'] : '';

$foodbakery_payment_vat = isset($foodbakery_plugin_options['foodbakery_payment_vat']) ? $foodbakery_plugin_options['foodbakery_payment_vat'] : '';

$cover_padding_top = isset($foodbakery_plugin_options['foodbakery_restaurant_cover_pading_top']) ? $foodbakery_plugin_options['foodbakery_restaurant_cover_pading_top'] : '';
$cover_padding_bottom = isset($foodbakery_plugin_options['foodbakery_restaurant_cover_pading_botom']) ? $foodbakery_plugin_options['foodbakery_restaurant_cover_pading_botom'] : '';

$foodbakery_restaurant_id = $post->ID;

do_action('call_class_obj_sticky_cart', $foodbakery_restaurant_id);

$restaurant_table_booking = get_post_meta($foodbakery_restaurant_id, 'foodbakery_restaurant_table_booking', true);
$restaurant_pickup_delivery = get_post_meta($foodbakery_restaurant_id, 'foodbakery_restaurant_pickup_delivery', true);
$foodbakery_delivery_fee = get_post_meta($foodbakery_restaurant_id, 'foodbakery_delivery_fee', true);
$foodbakery_delivery_fee = apply_filters('foodbakery_resturant_delivery_fee', $foodbakery_delivery_fee, $foodbakery_restaurant_id);
$foodbakery_delivery_fee = apply_filters('foodbakery_retaurant_delivery_fee', $foodbakery_delivery_fee, $foodbakery_restaurant_id);
$foodbakery_pickup_fee = get_post_meta($foodbakery_restaurant_id, 'foodbakery_pickup_fee', true);

$restaurant_menu_list = get_post_meta($foodbakery_restaurant_id, 'foodbakery_menu_items', true);

//$total_items = (is_array($restaurant_menu_list) || is_object($restaurant_menu_list)) ? count($restaurant_menu_list) :  0 ;
$menu_cat_titles = get_post_meta($foodbakery_restaurant_id, 'menu_cat_titles', true);
$category_titles_array = array();
if (!empty($menu_cat_titles)) {
    foreach ($menu_cat_titles as $cat_title_key => $cat_title_value) {
        $category_titles_array[$cat_title_value] = $cat_title_key;
    }
}
$menu_cat_icons = get_post_meta($foodbakery_restaurant_id, 'menu_cat_icons', true);
$total_items = is_array($restaurant_menu_list) ? count($restaurant_menu_list) : array();
$total_menu = $menu_array_check = array();


if (!empty($restaurant_menu_list)) {
    $menu_cat_counter = 0;
    foreach ($restaurant_menu_list as $menu_item_data) {
        $cousine_name = isset($menu_item_data['restaurant_menu']) ? $menu_item_data['restaurant_menu'] : '';
        $menu_exists = in_array($cousine_name, $menu_array_check);

        if (!$menu_exists) {
            //pre($menu_array_check, false);
            $cousine_icon_key = isset($category_titles_array[$cousine_name]) ? $category_titles_array[$cousine_name] : '';
            $cousine_icon = isset($menu_cat_icons[$cousine_icon_key]) ? $menu_cat_icons[$cousine_icon_key] : '';
            $total_menu[$menu_cat_counter]['cat_title'] = $cousine_name;
            $total_menu[$menu_cat_counter]['cat_icon'] = $cousine_icon;
            $total_menu[$menu_cat_counter] = apply_filters('foodbakery_menu_cats_array_frontend', $total_menu[$menu_cat_counter], $category_titles_array, $cousine_name, $foodbakery_restaurant_id);
            $menu_array_check[] = $cousine_name;
            $menu_cat_counter++;
        }
    }
}
$extras_modal_boxes = '';
$total_menu_count = count($total_menu);
wp_enqueue_script('foodbakery-restaurant-single');
wp_enqueue_script('prettyPhoto');
wp_enqueue_style('foodbakery-pretty-photo-css');

wp_deregister_style('bootstrap-datepicker');
wp_deregister_script('bootstrap-datepicker');

if (isset($_GET['price']) && $_GET['price'] == 'yes') {
    echo foodbakery_all_currencies(foodbakery_get_base_currency());
    echo foodbakery_get_currency(100, true);
}
$foodbakery_minimum_order_value = get_post_meta($foodbakery_restaurant_id, 'foodbakery_minimum_order_value', true);
$foodbakery_maximum_order_value = get_post_meta($foodbakery_restaurant_id, 'foodbakery_maximum_order_value', true);
$foodbakery_restaurant_category = get_post_meta($foodbakery_restaurant_id, 'foodbakery_restaurant_category', true);
$foodbakery_restaurant_category_str = '';
$cat_flag = 0;
$count_flag = is_array($foodbakery_restaurant_category) ? sizeof($foodbakery_restaurant_category) : '';
if (isset($foodbakery_restaurant_category) && is_array($foodbakery_restaurant_category))
    foreach ($foodbakery_restaurant_category as $single_restaurant_category) {

        $term_single = get_term_by('slug', $single_restaurant_category, 'restaurant-category');
        $term_name = isset($term_single->name) ? $term_single->name : '';

        if ($cat_flag != 0) {
            if ($cat_flag != ($count_flag - 1)) {
                $foodbakery_restaurant_category_str .= ', ';
            }

            if ($cat_flag == ($count_flag - 1)) {
                $foodbakery_restaurant_category_str .= ' &amp; ';
            }
        }

        $foodbakery_restaurant_category_str .= $term_name;
        $cat_flag++;
    }


// get all reviews
$ratings_data = array(
    'overall_rating' => 0.0,
    'count' => 0,
);
$ratings_data = apply_filters('reviews_ratings_data', $ratings_data, $foodbakery_restaurant_id);
// get opening hours
$days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
$opening_hours_list = array();
/*
  foreach ($days as $key => $day) {
  $opening_time = get_post_meta($foodbakery_restaurant_id, 'foodbakery_opening_hours_' . $day . '_opening_time', true);
  $opening_time_2 = get_post_meta($foodbakery_restaurant_id, 'foodbakery_opening_hours_' . $day . '_opening_time_2', true);


  $opening_time_2 = ($opening_time_2 != '' ? date('h:i a', $opening_time_2) : '');

  if (!is_array($opening_time)) {
  $opening_time = ($opening_time != '' ? date('h:i a', $opening_time) : '');
  } else {
  $opening_time = date('h:i a');
  }
  $closing_time = get_post_meta($foodbakery_restaurant_id, 'foodbakery_opening_hours_' . $day . '_closing_time', true);
  $closing_time_2 = get_post_meta($foodbakery_restaurant_id, 'foodbakery_opening_hours_' . $day . '_closing_time_2', true);
  $closing_time_2 = ($closing_time_2 != '' ? date('h:i a', $closing_time_2) : '');
  if (!is_array($closing_time)) {
  $closing_time = ($opening_time != '' ? date('h:i a', $closing_time) : '');
  } else {
  $closing_time = date('h:i a');
  }
  $opening_hours_list[$day] = array(
  'day_status' => get_post_meta($foodbakery_restaurant_id, 'foodbakery_opening_hours_' . $day . '_day_status', true),
  'opening_time' => $opening_time,
  'closing_time' => $closing_time,
  'opening_time_2' => $opening_time_2,
  'closing_time_2' => $closing_time_2,
  );
  } */

foreach ($days as $key => $day) {
    $_opening_time = get_post_meta($foodbakery_restaurant_id, 'foodbakery_opening_hours_' . $day . '_opening_time', true);

    $opening_time = ( $_opening_time != '' ? date('h:i a', $_opening_time) : '' );

    $opening_time = apply_filters('opening_hours_open_time_format', $opening_time, $_opening_time);


    $_closing_time = get_post_meta($foodbakery_restaurant_id, 'foodbakery_opening_hours_' . $day . '_closing_time', true);
    $closing_time = ( $_closing_time != '' ? date('h:i a', $_closing_time) : '' );

    $closing_time = apply_filters('opening_hours_close_time_format', $closing_time, $_closing_time);

    $_opening_time_2 = get_post_meta($foodbakery_restaurant_id, 'foodbakery_opening_hours_' . $day . '_opening_time_2', true);
    $opening_time_2 = ( $_opening_time_2 != '' ? date('h:i a', $_opening_time_2) : '' );

    $opening_time_2 = apply_filters('opening_hours_open_time_format', $opening_time_2, $_opening_time_2);

    $_closing_time_2 = get_post_meta($foodbakery_restaurant_id, 'foodbakery_opening_hours_' . $day . '_closing_time_2', true);
    $closing_time_2 = ( $_closing_time_2 != '' ? date('h:i a', $_closing_time_2) : '' );
    $closing_time_2 = apply_filters('opening_hours_open_time_format', $closing_time_2, $_closing_time_2);
    $opening_hours_list[$day] = array(
        'day_status' => get_post_meta($foodbakery_restaurant_id, 'foodbakery_opening_hours_' . $day . '_day_status', true),
        'opening_time' => $opening_time,
        'closing_time' => $closing_time,
        'opening_time_2' => $opening_time_2,
        'closing_time_2' => $closing_time_2,
    );
}


$foodbakery_restaurant_cover_styles = '';
$foodbakery_restaurant_featured_image_id = get_post_meta($foodbakery_restaurant_id, 'foodbakery_cover_image', true);
$foodbakery_restaurant_cover_image_id = get_post_meta($foodbakery_restaurant_id, 'foodbakery_restaurant_cover_image', true);
if ($foodbakery_restaurant_cover_image_id == '') {
    $foodbakery_restaurant_cover_image_id = isset($foodbakery_plugin_options['foodbakery_restaurant_cover_image']) ? $foodbakery_plugin_options['foodbakery_restaurant_cover_image'] : '';
}
if ($foodbakery_restaurant_cover_image_id != '') {
    $foodbakery_restaurant_cover_image_src = wp_get_attachment_url($foodbakery_restaurant_cover_image_id);
    if ($foodbakery_restaurant_cover_image_src != '') {
        $sec_height = restaurant_get_image_height($foodbakery_restaurant_cover_image_src);
        $foodbakery_restaurant_cover_styles .= ' background: url(' . $foodbakery_restaurant_cover_image_src . ') no-repeat scroll 0 0 / cover;';
    }
}

$single_restaurant_view = isset($foodbakery_plugin_options['foodbakery_single_restaurant_style']) ? $foodbakery_plugin_options['foodbakery_single_restaurant_style'] : 'simple';


if ($cover_padding_top != '') {
    $foodbakery_restaurant_cover_styles .= ' padding-top: ' . $cover_padding_top . 'px !important;';
}

if ($cover_padding_bottom != '') {
    $foodbakery_restaurant_cover_styles .= ' padding-bottom: ' . $cover_padding_bottom . 'px !important;';
}



if ($foodbakery_restaurant_cover_styles != '') {
    $foodbakery_restaurant_cover_styles = ' style="' . $foodbakery_restaurant_cover_styles . '"';
}

$foodbakery_restaurant_type = get_post_meta($foodbakery_restaurant_id, 'foodbakery_restaurant_type', true);
$foodbakery_restaurant_type = isset($foodbakery_restaurant_type) ? $foodbakery_restaurant_type : '';
if ($restaurant_type_post = get_page_by_path($foodbakery_restaurant_type, OBJECT, 'restaurant-type')) {
    $restaurant_type_id = $restaurant_type_post->ID;
}
$restaurant_type_id = isset($restaurant_type_id) ? $restaurant_type_id : '';
$foodbakery_user_reviews = get_post_meta($restaurant_type_id, 'foodbakery_user_reviews', true);
$transaction_restaurant_reviews = get_post_meta($foodbakery_restaurant_id, 'foodbakery_transaction_restaurant_reviews', true);

$single_restaurant_view_style = '';
if ($single_restaurant_view == 'fancy') {
    $single_restaurant_view_style = ' style="background: #ffffff;"';
}

do_action('restaurant_detail_page_rendering', $foodbakery_restaurant_id);
?>
<div class="main-section ">
    <div class="page-content-fullwidth">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="page-section restaurant-detail-image-section"<?php echo force_balance_tags($foodbakery_restaurant_cover_styles); ?>>
                    <!-- Container Start -->
                    <div class="container">
                        <!-- Row Start -->
                        <div class="row">
                            <!-- Column Start -->
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="company-info-detail">
                                    <div class="company-info">
                                        <?php
                                        if ($foodbakery_restaurant_featured_image_id != '') {
                                            $foodbakery_restaurant_featured_image_src = wp_get_attachment_url($foodbakery_restaurant_featured_image_id);
                                            if ($foodbakery_restaurant_featured_image_src != '') {
                                                ?>
                                                <div class="img-holder">
                                                    <figure>
                                                        <img src="<?php echo esc_html($foodbakery_restaurant_featured_image_src); ?>"
                                                             alt="">
                                                    </figure>
                                                </div>
                                                <?php
                                            }
                                        }
                                        ?>
                                        <div class="text-holder">
                                            <?php if (isset($ratings_data['count']) && $ratings_data['count'] > 0) { ?>
                                                <div class="rating-star">
                                                    <span class="rating-box"
                                                          style="width: <?php echo intval($ratings_data['overall_rating']); ?>%;"></span>
                                                </div>
                                                <span class="reviews">(<?php echo($ratings_data['count'] > 0 ? $ratings_data['count'] : 0); ?> <?php echo esc_html__('Reviews', 'foodbakery'); ?>
                                                    )</span>
                                            <?php } ?>
                                            <span class="restaurant-title"><?php echo esc_html(get_the_title($foodbakery_restaurant_id)) ?></span>
                                            <?php if ($foodbakery_restaurant_category_str != '') { ?>
                                                <div class="text">
                                                    <i class="icon-local_pizza"></i>
                                                    <p><?php echo esc_html($foodbakery_restaurant_category_str); ?></p>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="delivery-timing reviews-sortby">
                                        <?php if ($foodbakery_delivery_fee != '') { ?>
                                            <div class="text">
                                                <i class="icon-motorcycle"></i>
                                                <p>
                                                    <?php echo esc_html__('Delivery fee: ', 'foodbakery') . esc_html(foodbakery_get_currency($foodbakery_delivery_fee, true)); ?>
                                                    <span>
                                                        <?php
                                                        if (isset($foodbakery_minimum_order_value) && $foodbakery_minimum_order_value != '' && $foodbakery_minimum_order_value > 0) {
                                                            echo esc_html__(' Min Order : ', 'foodbakery');
                                                            echo esc_html(foodbakery_get_currency($foodbakery_minimum_order_value, true));
                                                        }
                                                        if (isset($foodbakery_maximum_order_value) && $foodbakery_maximum_order_value != '' && $foodbakery_maximum_order_value > 0) {
                                                            echo esc_html__(' Max Order : ', 'foodbakery');
                                                            echo esc_html(foodbakery_get_currency($foodbakery_maximum_order_value, true));
                                                        }
                                                        ?>
                                                        <?php do_action('foodbakery_restaurant_list_data', $foodbakery_restaurant_id); ?>
                                                    </span>
                                                </p>
                                            </div>
                                            <?php
                                        }

                                        $foodbakery_restaurant_type = get_post_meta($foodbakery_restaurant_id, 'foodbakery_restaurant_type', true);
                                        $foodbakery_restaurant_type = isset($foodbakery_restaurant_type) ? $foodbakery_restaurant_type : '';
                                        if ($restaurant_type_post = get_page_by_path($foodbakery_restaurant_type, OBJECT, 'restaurant-type')) {
                                            $restaurant_type_id = $restaurant_type_post->ID;
                                        }
                                        $foodbakery_full_data = get_post_meta($restaurant_type_id, 'foodbakery_full_data', true);
                                        if (!isset($foodbakery_full_data['foodbakery_opening_hours_element']) || $foodbakery_full_data['foodbakery_opening_hours_element'] == 'on') {
                                            if (isset($opening_hours_list) && !empty($opening_hours_list) && is_array($opening_hours_list)) {
                                                $current_day = strtolower(date('l'));
                                                $current_close = false;
                                                $current_day_text = esc_html__('Open', 'foodbakery');
                                                $closed_flag = false;
                                                $current_time = current_time('H:i a');
                                                $date1 = DateTime::createFromFormat('H:i a', $current_time);
                                                $date2 = DateTime::createFromFormat('H:i a', $opening_hours_list[$current_day]['opening_time']);
                                                $date3 = DateTime::createFromFormat('H:i a', $opening_hours_list[$current_day]['closing_time']);

                                                if ($opening_hours_list[$current_day]['day_status'] != 'on') {
                                                    $current_close = true;
                                                    //$current_day_text = 'Today : Closed';
                                                    $current_day_text = esc_html__('Today : Closed', 'foodbakery');
                                                    $closed_flag = true;
                                                } else if ($date1 >= $date2 && $date1 <= $date3) {
                                                    //$current_day_text = 'Today Timing :';
                                                    $current_day_text = esc_html__('Today :', 'foodbakery');
                                                } else {
                                                    //$current_day_text = 'Today : Closed';
                                                    $current_day_text = esc_html__('Today :', 'foodbakery');
                                                    $closed_flag = true;
                                                }
                                                $days_array = array(
                                                    'monday' => foodbakery_plugin_text_srt('foodbakery_restaurant_monday'),
                                                    'tuesday' => foodbakery_plugin_text_srt('foodbakery_restaurant_tuesday'),
                                                    'wednesday' => foodbakery_plugin_text_srt('foodbakery_restaurant_wednesday'),
                                                    'thursday' => foodbakery_plugin_text_srt('foodbakery_restaurant_thursday'),
                                                    'friday' => foodbakery_plugin_text_srt('foodbakery_restaurant_friday'),
                                                    'saturday' => foodbakery_plugin_text_srt('foodbakery_restaurant_saturday'),
                                                    'sunday' => foodbakery_plugin_text_srt('foodbakery_restaurant_sunday'),
                                                );
                                                ?>
                                                <ul>
                                                    <li>
                                                        <a href="#" class="reviews-sortby-active">
                                                            <span><?php echo esc_html($current_day_text); ?></span>
                                                            <?php if ($current_close != true) {
                                                                ?>
                                                                <?php esc_html__(': Opens at', 'foodbakery'); ?><?php echo esc_html($opening_hours_list[$current_day]['opening_time']) ?> - <?php echo esc_html($opening_hours_list[$current_day]['closing_time']) ?>
                                                                <?php do_action('wp_multi_opening_hour_current_day', $opening_hours_list[$current_day]); ?>
                                                            <?php } ?>
                                                            <i class="icon-chevron-small-down"></i>
                                                        </a>
                                                        <ul class="delivery-dropdown" style="display: none;">
                                                            <?php
                                                            global $foodbakery_opening_hours_element;
                                                            $opening_hours_list = $foodbakery_opening_hours_element->foodbakery_opening_hours_array($foodbakery_restaurant_id);

                                                            foreach ($opening_hours_list as $opening_hours_single_day_var => $opening_hours_single_day_val) {
                                                                if ($opening_hours_single_day_val['day_status'] == 'on') {
                                                                    ?>
                                                                    <li><a href="#"><span
                                                                                class="opend-day"><?php echo $days_array[$opening_hours_single_day_var]; ?></span>
                                                                            <span class="opend-time"><small>:</small> <?php esc_html__('Opens at', 'foodbakery'); ?> <?php echo esc_html($opening_hours_single_day_val['opening_time']); ?>
                                                                                - <?php echo esc_html($opening_hours_single_day_val['closing_time']); ?>
                                                                                <?php do_action('wp_multi_opening_hour_fields_frontend', $days_array[$opening_hours_single_day_var], $opening_hours_single_day_val); ?>

                                                                            </span></a>
                                                                    </li>
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                    <li><a href="javascript:void(0)"><span
                                                                                class="opend-day"><?php echo $days_array[$opening_hours_single_day_var]; ?></span>
                                                                            <span class="opend-time close-day"><small>:</small><?php echo esc_html__('Closed', 'foodbakery'); ?></span></a>
                                                                    </li>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </ul>
                                                    </li>
                                                </ul>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <!-- Column End -->
                        </div>
                        <!-- Row End -->
                    </div>
                    <!-- Container End -->
                </div>
                <div id="main">
                    <!-- Page Section Start -->
                    <div class="page-section" <?php echo $single_restaurant_view_style; ?>>
                        <!-- Container Start -->
                        <div class="container">
                            <!-- Row Start -->
                            <div class="row">
                                <?php
                                $tab_class = ($single_restaurant_view == 'fancy') ? 'tab-single-page' : '';
                                ?>


                                <div class="<?php echo $tab_class; ?>" style="transform: none;">

                                    <?php if ($single_restaurant_view == 'fancy') { ?>
                                        <div class="col-lg-2 col-md-12 col-sm-12 col-xs-12 sticky-sidebar sidebar-menu" style="position: relative; overflow: visible; box-sizing: border-box; min-height: 1px;">
                                            <div class="filter-toggle"><span class="filter-toggle-text"><?php echo esc_html__('Categories By', 'foodbakery'); ?></span><i class="icon-chevron-down"></i></div>
                                            <div class="theiaStickySidebar" style="padding-top: 0px; padding-bottom: 1px; position: static; transform: none; top: 0px; left: 0px;">
                                                <div class='filter-wrapper'>
                                                    <div class="categories-menu mCustomScrollbar _mCS_1" style="height: 800px;">
                                                        <div id="mCSB_1" class="mCustomScrollBox mCS-dark-3 mCSB_vertical mCSB_inside" style="max-height: none;" tabindex="0">
                                                            <div id="mCSB_1_container" class="mCSB_container" style="position:relative; top:0; left:0;" dir="ltr">
                                                                <ul class="menu-list">
                                                                    <li class="hide"><a href="javascript:void(0)" class="deal-left-menu-link" data-id="deals-id"> <i class="icon-local_pizza"></i> <?php esc_html_e('Deals', 'foodbakery') ?> </a></li>
                                                                    <?php
                                                                    if ($total_items > 0) {
                                                                        $active_class = 'active';
                                                                        for ($menu_loop = 0; $menu_loop < $total_menu_count; $menu_loop ++) {
                                                                            for ($menu_items_loop = 0; $menu_items_loop < $total_items; $menu_items_loop ++) {
                                                                                if (isset($restaurant_menu_list[$menu_items_loop]['restaurant_menu']) && $total_menu[$menu_loop]['cat_title'] == $restaurant_menu_list[$menu_items_loop]['restaurant_menu']) {
                                                                                    
                                                                                }
                                                                            }
                                                                            if (isset($total_menu[$menu_loop]['cat_title'])) {
                                                                                ?>
                                                                                <li class="<?php echo ($active_class); ?>"><a href="javascript:void(0)" class="menu-category-link" data-id="<?php echo absint($menu_loop) ?>"> <i class="<?php echo esc_attr($total_menu[$menu_loop]['cat_icon']); ?>"></i> <?php echo esc_html($total_menu[$menu_loop]['cat_title']); ?> </a></li>
                                                                                <?php
                                                                                $active_class = '';
                                                                            }
                                                                        }
                                                                    }
                                                                    ?>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-12 sticky-sidebar">
                                            <div class="filter-toggle"><span
                                                    class="filter-toggle-text"><?php echo esc_html__('Categories By', 'foodbakery'); ?></span><i
                                                    class="icon-chevron-down"></i></div>
                                            <div class='filter-wrapper'>
                                                <div class="categories-menu">
                                                    <h6>
                                                        <i class="icon-restaurant_menu"></i><?php echo esc_html__('Categories', 'foodbakery') ?>
                                                    </h6>
                                                    <ul class="menu-list">

                                                        <?php
                                                        if ($total_items > 0) {
                                                            $active_class = 'active';
                                                            for ($menu_loop = 0; $menu_loop < $total_menu_count; $menu_loop ++) {

                                                                $show_menu_cat = apply_filters('foodbakery_show_menu_cat', true, $total_menu[$menu_loop]);
                                                                if ($show_menu_cat == true) {
                                                                    for ($menu_items_loop = 0; $menu_items_loop < $total_items; $menu_items_loop ++) {
                                                                        if (isset($restaurant_menu_list[$menu_items_loop]['restaurant_menu']) && $total_menu[$menu_loop]['cat_title'] == $restaurant_menu_list[$menu_items_loop]['restaurant_menu']) {
                                                                            
                                                                        }
                                                                    }
                                                                    if (isset($total_menu[$menu_loop]['cat_title'])) {
                                                                        ?>
                                                                        <li class="<?php echo ($active_class); ?>"><a href="javascript:void(0)" class="menu-category-link" data-id="<?php echo absint($menu_loop) ?>"> <?php echo esc_html($total_menu[$menu_loop]['cat_title']); ?> </a></li>
                                                                        <?php
                                                                        $active_class = '';
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        ?>



                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>


                                    <?php
                                    $Foodbakery_deal_frontend = new Foodbakery_deal_frontend();
                                    $restaurant_deals_data = $Foodbakery_deal_frontend->restaurant_detail_deals_list($foodbakery_restaurant_id, $single_restaurant_view);

                                    $restaurant_detail_menu_area_cols = apply_filters('restaurant_detail_menu_area_cols', 'col-lg-7 col-md-7 col-sm-9 col-xs-12', $foodbakery_restaurant_id);

                                    if ($single_restaurant_view == 'fancy') {
                                        ?>    
                                        <div class="<?php echo esc_attr($restaurant_detail_menu_area_cols); ?>">
                                            <!--Tabs Start-->
                                            <div class="back-to-t"></div>
                                            <div class="tabs-holder horizontal">
                                                <ul class="stickynav-tabs nav nav-tabs">
                                                    <?php
                                                    $menu_active = 'class="active"';
                                                    $menu_active_tab = 'in active';
                                                    $deal_active_tab = '';
                                                    $review_active = '';
                                                    $review_active_tab = '';
                                                    $deals_active = '';
                                                    if ($restaurant_deals_data != '') {
                                                        
                                                    }
                                                    if (isset($_GET['review_id']) && $_GET['review_id'] != '') {
                                                        $review_active = 'class="active"';
                                                        $menu_active = '';
                                                        $menu_active_tab = '';
                                                        //$deal_active_tab = '';
                                                        //$deal_active_tab = '';
                                                        $review_active_tab = 'in active';
                                                    }
                                                    ?>


                                                    <li <?php echo ($menu_active); ?>><a data-toggle="tab" href="#home"><i class="icon- icon-room_service"></i><?php esc_html_e('Menu', 'foodbakery') ?></a></li>


                                                    <?php if ($restaurant_deals_data != '') { ?>
                                                        <li <?php echo ($deals_active); ?>><a data-toggle="tab" href="#menu_deals"><i class="icon- icon-room_service"></i><?php esc_html_e('Deals', 'foodbakery') ?></a></li>
                                                    <?php } ?>


                                                    <?php
                                                    if ($foodbakery_user_reviews == 'on' && $transaction_restaurant_reviews == 'on') {
                                                        $ratings_data = array(
                                                            'overall_rating' => 0.0,
                                                            'count' => 0,
                                                        );
                                                        $ratings_data = apply_filters('reviews_ratings_data', $ratings_data, $foodbakery_restaurant_id);
                                                        ?>
                                                        <li <?php echo ($review_active); ?>>
                                                            <a data-toggle="tab" href="#menu1"><i class="icon- icon-textsms"></i>
                                                                <?php
                                                                esc_html_e('Reviews', 'foodbakery');
                                                                if ($ratings_data['count'] > 0) {
                                                                    ?> (<?php echo esc_html($ratings_data['count']); ?>)
                                                                <?php } ?>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                    <?php if (isset($restaurant_table_booking) && $restaurant_table_booking == 'yes') { ?>
                                                        <li><a data-toggle="tab" href="#menu2"><i class="icon- icon-food"></i><?php esc_html_e('Book a Table', 'foodbakery') ?> </a></li>
                                                    <?php } ?>
                                                    <li><a data-toggle="tab" href="#menu3"><i class="icon- icon-info3"></i><?php esc_html_e('Restaurant Info', 'foodbakery') ?></a></li>
                                                </ul>
                                                <div class="tab-content">
                                                    <div id="home" class="tab-pane fade <?php echo ($menu_active_tab); ?>">
                                                        <div class="menu-itam-holder">
                                                            <div class="field-holder sticky-search">
                                                                <input id="menu-srch-<?php echo absint($foodbakery_restaurant_id) ?>" data-id="<?php echo absint($foodbakery_restaurant_id) ?>" class="input-field dev-menu-search-field" type="text" placeholder="<?php esc_html_e('Search food item', 'foodbakery') ?>">
                                                            </div>
                                                            <div id="menu-item-list-<?php echo absint($foodbakery_restaurant_id) ?>" class="menu-itam-list thumbnail-circle" style="margin-top: 0px;">
                                                                <div class="row">
                                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                        <?php
                                                                        $menu_items_bu = restaurant_detail_menu_list($foodbakery_restaurant_id, $single_restaurant_view);
                                                                        $menu_items_b_list = isset($menu_items_bu['items']) ? $menu_items_bu['items'] : '';
                                                                        $menu_items_b_bs = isset($menu_items_bu['bs_boxes']) ? $menu_items_bu['bs_boxes'] : '';
                                                                        echo force_balance_tags($menu_items_b_list);
                                                                        $extras_modal_boxes .= $menu_items_b_bs;
                                                                        ?>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>


                                                    <div id="menu_deals" class="tab-pane fade <?php echo ($deal_active_tab); ?>">
                                                        <div class="menu-itam-holder">
                                                            <div class="menu-itam-list-grid">
                                                                <br>
                                                                <?php
                                                                echo $restaurant_deals_data;
                                                                ?>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <?php if ($foodbakery_user_reviews == 'on' && $transaction_restaurant_reviews == 'on') { ?>
                                                        <div id="menu1" class="tab-pane fade <?php echo ($review_active_tab); ?>">
                                                            <?php
                                                            do_action('foodbakery_reviews_ui', $foodbakery_restaurant_id, 'yes', 'no');
                                                            ?>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if (isset($restaurant_table_booking) && $restaurant_table_booking == 'yes') { ?>
                                                        <div id="menu2" class="tab-pane fade">
                                                            <?php do_action('foodbakery_booking_element_html', $foodbakery_restaurant_id); ?>
                                                        </div>
                                                    <?php } ?>
                                                    <div id="menu3" class="tab-pane fade">
                                                        <?php
                                                        do_action('foodbakery_contact_element_html', $foodbakery_restaurant_id);
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <div class="<?php echo esc_attr($restaurant_detail_menu_area_cols); ?>">
                                            <!--Tabs Start-->
                                            <div class="back-to-t"></div>
                                            <div class="tabs-holder horizontal">
                                                <ul class="stickynav-tabs nav nav-tabs">
                                                    <?php
                                                    $menu_active = 'class="active"';
                                                    $menu_active_tab = 'in active';
                                                    $review_active = '';
                                                    $review_active_tab = '';
                                                    $deals_active = '';
                                                    if (isset($_GET['review_id']) && $_GET['review_id'] != '') {
                                                        $review_active = 'class="active"';
                                                        $menu_active = '';
                                                        $menu_active_tab = '';
                                                        $review_active_tab = 'in active';
                                                    }
                                                    ?>
                                                    <li <?php echo($menu_active); ?>><a data-toggle="tab"
                                                                                        href="#home"><i
                                                                class="icon- icon-room_service"></i><?php esc_html_e('Menu', 'foodbakery') ?>
                                                        </a></li>

                                                    <?php
                                                    if ($foodbakery_user_reviews == 'on' && $transaction_restaurant_reviews == 'on') {
                                                        $ratings_data = array(
                                                            'overall_rating' => 0.0,
                                                            'count' => 0,
                                                        );
                                                        $ratings_data = apply_filters('reviews_ratings_data', $ratings_data, $foodbakery_restaurant_id);
                                                        ?>
                                                        <li <?php echo($review_active); ?>>
                                                            <a data-toggle="tab" href="#menu1"><i
                                                                    class="icon- icon-textsms"></i>
                                                                    <?php
                                                                    esc_html_e('Reviews', 'foodbakery');
                                                                    if ($ratings_data['count'] > 0) {
                                                                        ?> (<?php echo esc_html($ratings_data['count']); ?>)
                                                                <?php } ?>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                    <?php if (isset($restaurant_table_booking) && $restaurant_table_booking == 'yes') { ?>
                                                        <li><a data-toggle="tab" href="#menu2"><i
                                                                    class="icon- icon-food"></i><?php esc_html_e('Book a Table', 'foodbakery') ?>
                                                            </a></li>
                                                    <?php } ?>
                                                    <li><a data-toggle="tab" href="#menu3"><i
                                                                class="icon- icon-info3"></i><?php esc_html_e('Restaurant Info', 'foodbakery') ?>
                                                        </a></li>
                                                </ul>
                                                <div class="tab-content">
                                                    <div id="home"
                                                         class="tab-pane fade <?php echo($menu_active_tab); ?>">
                                                        
                                                        <?php do_action('foodbakery_before_restaurant_menu_items', $foodbakery_restaurant_id); ?>
                                                        
                                                        <div class="menu-itam-holder">
                                                            <div class="field-holder sticky-search">
                                                                <input id="menu-srch-<?php echo absint($foodbakery_restaurant_id) ?>"
                                                                       data-id="<?php echo absint($foodbakery_restaurant_id) ?>"
                                                                       class="input-field dev-menu-search-field"
                                                                       type="text"
                                                                       placeholder="<?php esc_html_e('Search food item', 'foodbakery') ?>">
                                                            </div>
                                                            <div id="menu-item-list-<?php echo absint($foodbakery_restaurant_id) ?>"
                                                                 class="menu-itam-list">
                                                                     <?php
                                                                     $Foodbakery_deal_frontend = new Foodbakery_deal_frontend();
                                                                     //echo $Foodbakery_deal_frontend->restaurant_detail_deals_list($foodbakery_restaurant_id);


                                                                     $menu_items_bu = restaurant_detail_menu_list($foodbakery_restaurant_id);
                                                                     $menu_items_b_list = isset($menu_items_bu['items']) ? $menu_items_bu['items'] : '';
                                                                     $menu_items_b_bs = isset($menu_items_bu['bs_boxes']) ? $menu_items_bu['bs_boxes'] : '';
                                                                     echo force_balance_tags($menu_items_b_list);
                                                                     $extras_modal_boxes .= $menu_items_b_bs;
                                                                     ?>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <?php if ($foodbakery_user_reviews == 'on' && $transaction_restaurant_reviews == 'on') { ?>
                                                        <div id="menu1"
                                                             class="tab-pane fade <?php echo($review_active_tab); ?>">
                                                                 <?php
                                                                 do_action('foodbakery_reviews_ui', $foodbakery_restaurant_id, 'yes', 'no');
                                                                 ?>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if (isset($restaurant_table_booking) && $restaurant_table_booking == 'yes') { ?>
                                                        <div id="menu2" class="tab-pane fade">
                                                            <?php do_action('foodbakery_booking_element_html', $foodbakery_restaurant_id); ?>
                                                        </div>
                                                    <?php } ?>
                                                    <div id="menu3" class="tab-pane fade">
                                                        <?php
                                                        do_action('foodbakery_contact_element_html', $foodbakery_restaurant_id);
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php
                                    $restaurant_menu_order_active = apply_filters('restaurant_menu_order_active', true, $foodbakery_restaurant_id);
                                    if ($restaurant_menu_order_active == true) {
                                        $get_added_menus = array();
                                        $user_id = $current_user->ID;
                                        $publisher_id = foodbakery_company_id_form_user_id($user_id);
                                        $publisher_type = get_post_meta($publisher_id, 'foodbakery_publisher_profile_type', true);

                                        if ($publisher_id != '' && $publisher_type != '' && $publisher_type != 'restaurant') {
                                            $get_added_menus = get_transient('add_menu_items_' . $publisher_id);
                                            if (empty($get_added_menus) && isset($_COOKIE['add_menu_items_temp'])) {
                                                $get_added_menus = unserialize(stripslashes($_COOKIE['add_menu_items_temp']));
                                            }
                                        } else {
                                            if (isset($_COOKIE['add_menu_items_temp'])) {
                                                $get_added_menus = unserialize(stripslashes($_COOKIE['add_menu_items_temp']));
                                            }
                                        }

                                        if ($publisher_id != '' && $publisher_type != '' && $publisher_type != 'restaurant') {
                                            if (isset($_COOKIE['add_menu_items_temp'])) {
                                                $temp_items = unserialize(stripslashes($_COOKIE['add_menu_items_temp']));
                                                if (is_array($temp_items) && count($temp_items) > 0) {
                                                    $get_added_menus = unserialize(stripslashes($_COOKIE['add_menu_items_temp']));
                                                    set_transient('add_menu_items_' . $publisher_id, $get_added_menus, 60 * 60 * 24 * 30);
                                                }
                                            }
                                            //set_transient('add_menu_items_' . $publisher_id, $get_added_menus, 60 * 60 * 24 * 30);
                                        }

                                        $have_menu_orders = false;
                                        if (isset($get_added_menus[$foodbakery_restaurant_id]) && is_array($get_added_menus[$foodbakery_restaurant_id]) && sizeof($get_added_menus[$foodbakery_restaurant_id]) > 0) {
                                            $have_menu_orders = true;
                                        }
                                        update_post_meta($foodbakery_restaurant_id, 'foodbakery_restaurant_disable_cashes', 'no');
                                        $foodbakery_cash_payments = get_post_meta($foodbakery_restaurant_id, 'foodbakery_restaurant_disable_cashes', true);
                                        ?>
                                        <div class="sticky-sidebar col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                            <div class="user-order-holder 9">
                                                <div class="user-order">
                                                    <h6><i class="icon-shopping-basket"></i><?php esc_html_e('Your Order', 'foodbakery') ?></h6>
                                                    <?php
                                                    $restaurant_allow_pre_order = get_post_meta($foodbakery_restaurant_id, 'foodbakery_restaurant_pre_order', true);
                                                    if ($restaurant_allow_pre_order == 'yes') {
                                                        echo '<span class="error-message pre-order-msg" style="display: ' . ($have_menu_orders === false ? 'block' : 'none') . ';">' . esc_html__('This restaurant allows Pre orders.', 'foodbakery') . '</span>';
                                                    }
                                                    $selected_fee_type = isset($get_added_menus[$foodbakery_restaurant_id . '_fee_type']) ? $get_added_menus[$foodbakery_restaurant_id . '_fee_type'] : '';
                                                    ?>
                                                    <span class="discount-info" style="display: <?php echo ($have_menu_orders === false ? 'block' : 'none') ?>;"><?php _e('If you have a discount code,<br> you will be able to input it<br> at the payments stage.', 'foodbakery') ?></span>
                                                    <?php
                                                    if ($restaurant_pickup_delivery == 'pickup' || $restaurant_pickup_delivery == 'delivery' || $restaurant_pickup_delivery == 'delivery_and_pickup') {
                                                        ?>
                                                        <div class="select-option dev-select-fee-option" data-rid="<?php echo esc_html($foodbakery_restaurant_id) ?>">
                                                            <ul>
                                                                <?php
                                                                if (($restaurant_pickup_delivery == 'pickup' || $restaurant_pickup_delivery == 'delivery_and_pickup')) {
                                                                    ?>
                                                                    <li>
                                                                        <input id="order-pick-up-fee" type="radio" <?php echo ($selected_fee_type == 'pickup' ? 'checked="checked"' : '') ?> name="order_fee_type" data-fee="<?php echo foodbakery_get_currency($foodbakery_pickup_fee, false, '', '', false); ?>" data-label="<?php esc_html_e('Pick-Up', 'foodbakery') ?>" data-type="pickup" />
                                                                        <label for="order-pick-up-fee"><?php esc_html_e('Pick-Up', 'foodbakery') ?></label>
                                                                        <span><?php echo foodbakery_get_currency($foodbakery_pickup_fee, true); ?></span>
                                                                    </li>
                                                                    <?php
                                                                }
                                                                if (($restaurant_pickup_delivery == 'delivery' || $restaurant_pickup_delivery == 'delivery_and_pickup')) {
                                                                    ?>
                                                                    <li>
                                                                        <input id="order-delivery-fee" <?php echo ($selected_fee_type != 'pickup' ? 'checked="checked"' : '') ?> type="radio" name="order_fee_type" data-fee="<?php echo foodbakery_get_currency($foodbakery_delivery_fee, false, '', '', false); ?>" data-label="<?php esc_html_e('Delivery', 'foodbakery') ?>" data-type="delivery" />
                                                                        <label for="order-delivery-fee"><?php esc_html_e('Delivery', 'foodbakery') ?></label>
                                                                        <span><?php echo foodbakery_get_currency($foodbakery_delivery_fee, true); ?></span>
                                                                    </li>
                                                                <?php } ?>
                                                            </ul>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                    <div class="dev-menu-orders-list" style="display: <?php echo ($have_menu_orders === true ? 'block' : 'none') ?>;">

                                                        <ul class="categories-order" data-rid="<?php echo absint($foodbakery_restaurant_id) ?>">
                                                            <?php
                                                            if (isset($get_added_menus[$foodbakery_restaurant_id]) && is_array($get_added_menus[$foodbakery_restaurant_id]) && sizeof($get_added_menus[$foodbakery_restaurant_id]) > 0) {
                                                                $item_count = 1;
                                                                $rand_numb_class = 10000001;
                                                                foreach ($get_added_menus[$foodbakery_restaurant_id] as $menu_key => $menu_ord_item) {

                                                                    if (isset($menu_ord_item['menu_id']) && isset($menu_ord_item['price'])) {
                                                                        $rand_numb = rand(10000000, 99999999);
                                                                        $menu_t_price = 0;
                                                                        $this_menu_cat_id = isset($menu_ord_item['menu_cat_id']) ? $menu_ord_item['menu_cat_id'] : '';
                                                                        $this_item_id = $menu_ord_item['menu_id'];
                                                                        $this_item_price = $menu_ord_item['price'];
                                                                        $this_item_extras = isset($menu_ord_item['extras']) ? $menu_ord_item['extras'] : '';

                                                                        $menu_t_price += floatval($this_item_price);
                                                                        $this_item_title = isset($restaurant_menu_list[$this_item_id]['menu_item_title']) ? $restaurant_menu_list[$this_item_id]['menu_item_title'] : '';

                                                                        $menu_extra_li = '';
                                                                        if (is_array($this_item_extras) && sizeof($this_item_extras) > 0) {
                                                                            $extra_m_counter = 0;
                                                                            $menu_extra_li .= '<ul>';
                                                                            foreach ($this_item_extras as $this_item_extra_at) {
                                                                                $this_item_heading = isset($restaurant_menu_list[$this_item_id]['menu_item_extra']['heading'][$extra_m_counter]) ? $restaurant_menu_list[$this_item_id]['menu_item_extra']['heading'][$extra_m_counter] : '';
                                                                                $item_extra_at_title = isset($this_item_extra_at['title']) ? $this_item_extra_at['title'] : '';
                                                                                $item_extra_at_price = isset($this_item_extra_at['price']) ? $this_item_extra_at['price'] : '';
                                                                                if ($item_extra_at_title != '' || $item_extra_at_price > 0) {
                                                                                    $menu_extra_li .= '<li>' . $this_item_heading . ' - ' . $item_extra_at_title . ' : <span class="category-price">' . foodbakery_get_currency($item_extra_at_price, true) . '</span></li>';
                                                                                }

                                                                                $menu_t_price += floatval($item_extra_at_price);
                                                                                $extra_m_counter ++;
                                                                            }
                                                                            $menu_extra_li .= '</ul>';
                                                                            $popup_id = 'edit_extras-' . $this_menu_cat_id . '-' . $this_item_id;
                                                                            $data_id = $this_item_id;
                                                                            $data_cat_id = $this_menu_cat_id;
                                                                            $ajax_url = admin_url('admin-ajax.php');
                                                                            $unique_id = $get_added_menus[$foodbakery_restaurant_id][$menu_key]['unique_id'];
                                                                            $extra_child_menu_id = isset($get_added_menus[$foodbakery_restaurant_id][$menu_key]['extra_child_menu_id']) ? $get_added_menus[$foodbakery_restaurant_id][$menu_key]['extra_child_menu_id'] : '';
                                                                            $menu_extra_li .= '<a href="javascript:void(0);" class="edit-menu-item update_menu_' . $rand_numb_class . '" onClick="foodbakery_edit_extra_menu_item(\'' . $popup_id . '\',\'' . $data_id . '\',\'' . $data_cat_id . '\',\'' . $rand_numb_class . '\',\'' . $ajax_url . '\',\'' . $foodbakery_restaurant_id . '\',\'' . $unique_id . '\',\'' . $extra_child_menu_id . '\');">' . esc_html__('Edit', 'foodbakery') . '</a>';
                                                                        }
                                                                        ?>
                                                                        <li class="menu-added-<?php echo $rand_numb_class; ?>" id="menu-added-<?php echo absint($rand_numb) ?>" class="item_count_<?php echo $item_count; ?>" data-pr="<?php echo foodbakery_get_currency($menu_t_price, false, '', '', false); ?>" data-conpr="<?php echo foodbakery_get_currency($menu_t_price, false, '', '', true); ?>">
                                                                            <a href="javascript:void(0)" class="btn-cross dev-remove-menu-item"><i class=" icon-cross3"></i></a>
                                                                            <a><?php echo esc_html($this_item_title) ?></a>
                                                                            <span class="category-price"><?php echo foodbakery_get_currency($this_item_price, true, '', '', true); ?></span>
                                                                            <?php echo force_balance_tags($menu_extra_li) ?>
                                                                            <?php echo apply_filters('foodbakery_cart_after_menu_item', '', $menu_key, $menu_ord_item, $foodbakery_restaurant_id); ?>
                                                                        </li>
                                                                        <?php
                                                                    }
                                                                    $item_count++;
                                                                    $rand_numb_class++;
                                                                }
                                                            }
                                                            ?>
                                                        </ul>
                                                        <div class="price-area dev-menu-price-con" data-vatsw="<?php echo esc_html($foodbakery_vat_switch) ?>" data-vat="<?php echo floatval($foodbakery_payment_vat) ?>">
                                                            <ul>
                                                                <input type="hidden" id="order_subtotal_price" name="order_subtotal_price" value="<?php echo restaurant_menu_price_calc($get_added_menus, $foodbakery_restaurant_id, '', '', '', false) ?>">
                                                                <li><?php esc_html_e('Subtotal', 'foodbakery') ?> <span class="price"><?php echo currency_symbol_possitions_html('<em class="dev-menu-subtotal">' . restaurant_menu_price_calc($get_added_menus, $foodbakery_restaurant_id, false, false, false, true) . '</em>', foodbakery_get_currency_sign()); ?>
                                                                    </span></li>

                                                                <?php
                                                                $show_fee_type = '';
                                                                if ($selected_fee_type == 'delivery' && $foodbakery_delivery_fee > 0 && $foodbakery_pickup_fee > 0) {
                                                                    $show_fee_type = 'delivery';
                                                                } else if ($selected_fee_type == 'pickup' && $foodbakery_delivery_fee > 0 && $foodbakery_pickup_fee > 0) {
                                                                    $show_fee_type = 'pickup';
                                                                } else {
                                                                    if ($foodbakery_delivery_fee > 0 && $restaurant_pickup_delivery != 'pickup') {
                                                                        $show_fee_type = 'delivery';
                                                                    } else if ($foodbakery_pickup_fee > 0 && $restaurant_pickup_delivery != 'delivery') {
                                                                        $show_fee_type = 'pickup';
                                                                    }
                                                                }

                                                                if ($show_fee_type == 'delivery') {
                                                                    ?>
                                                                    <li class="restaurant-fee-con"><span class="fee-title"><?php esc_html_e('Delivery fee', 'foodbakery') ?></span> <span class="price"><?php echo currency_symbol_possitions_html('<em class="dev-menu-charges" data-confee="' . foodbakery_get_currency($foodbakery_delivery_fee, false, '', '', true) . '" data-fee="' . foodbakery_get_currency($foodbakery_delivery_fee, false, '', '', false) . '">' . foodbakery_get_currency($foodbakery_delivery_fee, false, '', '', true) . '</em>', foodbakery_get_currency_sign()); ?>
                                                                        </span></li>
                                                                    <?php
                                                                } else if ($show_fee_type == 'pickup') {
                                                                    ?>
                                                                    <li class="restaurant-fee-con"><span class="fee-title"><?php esc_html_e('Pickup fee', 'foodbakery') ?></span> <span class="price"><?php echo currency_symbol_possitions_html('<em class="dev-menu-charges" data-confee="' . foodbakery_get_currency($foodbakery_pickup_fee, false, '', '', true) . '" data-fee="' . foodbakery_get_currency($foodbakery_pickup_fee, false, '', '', false) . '">' . foodbakery_get_currency($foodbakery_pickup_fee, false, '', '', true) . '</em>', foodbakery_get_currency_sign()); ?></span></li>
                                                                    <?php
                                                                }

                                                                if ($foodbakery_vat_switch == 'on' && $foodbakery_payment_vat > 0) {
                                                                    ?>
                                                                    <input type="hidden" id="order_vat_percent" name="order_vat_percent" value="<?php echo ($foodbakery_payment_vat); ?>">
                                                                    <input type="hidden" id="order_vat_cal_price" name="order_vat_cal_price" value="<?php echo restaurant_menu_price_calc($get_added_menus, $foodbakery_restaurant_id, true, false, true); ?>">
                                                                    <li><?php printf(esc_html__('VAT (%s&#37;)', 'foodbakery'), $foodbakery_payment_vat) ?> <span class="price"><?php echo currency_symbol_possitions_html('<em class="dev-menu-vtax">' . restaurant_menu_price_calc($get_added_menus, $foodbakery_restaurant_id, true, false, true, true) . '</em>', foodbakery_get_currency_sign()); ?></span></li>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </ul>
                                                            <?php do_action('foodbakery_order_cart_field_before_total', $foodbakery_restaurant_id, restaurant_menu_price_calc($get_added_menus, $foodbakery_restaurant_id, true, true, false, true, '', false, false)); ?>
                                                            <p class="total-price"><?php esc_html_e('Total', 'foodbakery') ?> <span class="price"><?php echo currency_symbol_possitions_html('<em class="dev-menu-grtotal">' . restaurant_menu_price_calc($get_added_menus, $foodbakery_restaurant_id, true, true, false, true) . '</em>', foodbakery_get_currency_sign()); ?></span></p>
                                                        </div>
                                                    </div>
                                                    <div id="dev-no-menu-orders-list" style="display: <?php echo ($have_menu_orders === false ? 'block' : 'none') ?>;">
                                                        <?php echo '<span class="success-message">' . esc_html__('There are no items in your basket.', 'foodbakery') . '</span>' ?>
                                                    </div>
                                                    <?php do_action('foodbakery_order_area', $foodbakery_restaurant_id); ?>
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

                                                                $foodbakery_restaurant_disable_cash = apply_filters('foodbakery_cash_module_status', $foodbakery_restaurant_disable_cash);

                                                                if ($foodbakery_restaurant_disable_cash == 'no') {
                                                                    ?>

                                                                    <li>
                                                                        <input id="order-cash-payment" type="radio" name="order_payment_method" data-type="cash" />
                                                                        <label for="order-cash-payment">
                                                                            <i class="icon-coins"></i>
                                                                            <?php esc_html_e('Cash', 'foodbakery') ?>
                                                                        </label>
                                                                    </li>
                                                                <?php } ?>
                                                                <li>
                                                                    <input id="order-card-payment" type="radio" checked="checked" name="order_payment_method" data-type="card" />
                                                                    <label for="order-card-payment">
                                                                        <i class="icon-credit-card4"></i>
                                                                        <?php esc_html_e('Card', 'foodbakery') ?>
                                                                    </label>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <?php
                                                    }
                                                    //pre(apply_filters('foodbakery_timezone_convert', date('H:i')), false);
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <div class="input-group date" >
                                                                    <input type="text" name="delivery_date" id="<?php echo apply_filters('foodbakery_datetime_field_id', 'datetimepicker1'); ?>" class="form-control" value="<?php echo apply_filters('foodbakery_timezone_convert', date('d-m-Y H:i'), 'd-m-Y H:i'); //date('d-m-Y H:i');   ?>" placeholder="Select Date and Time"/>
                                                                    <span class="input-group-addon">
                                                                        <span class="icon-event_available"></span>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php
                                                        $openhours_array = array(
                                                            'Thu Jan 19 2023 10:22:00 GMT+0500 (Pakistan Standard Time)'  => array( 'min' => '11:00', 'max' => '12:00'),
                                                            'Fri Jan 13 2023 10:22:00 GMT+0500 (Pakistan Standard Time)'  => array( 'min' => '10:00', 'max' => '11:00'),
                                                        )
                                                        ?>
                                                        
                                                        <script type="text/javascript">
                                                            jQuery(function () {
                                                                jQuery('#datetimepicker1').datetimepicker({
                                                                    format: 'd-m-Y H:i',
                                                                    timepicker: true,
                                                                    minDate: '<?php echo apply_filters('foodbakery_timezone_convert', date('d-m-Y H:i'), 'd-m-Y H:i'); //date('d-m-Y H:i');   ?>',
                                                                    step: 15
                                                                });
                                                            });
                                                        </script>
                                                    </div>
                                                    <a href="javascript:void(0)" class="menu-order-confirm" id="menu-order-confirm" data-rid="<?php echo absint($foodbakery_restaurant_id) ?>"><?php esc_html_e('Confirm Order', 'foodbakery') ?></a>
                                                    <span class="menu-loader"></span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <!-- Row End -->
                            <!-- Start Edit extra modal -->
                            <div id="edit_extra_modal"></div>
                            <!-- End Edit extra modal -->

                        </div>
                        <!-- Container End -->
                    </div>
                    <!-- Page Section End -->
                </div>
            </div>
        </div>
    </div>
</div>
<?php
echo force_balance_tags($extras_modal_boxes);
get_footer();
