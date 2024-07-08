<?php

class foodbakery_restaurant_api {

    function __construct() {

        add_action('rest_api_init', array($this, 'my_register_route'));
    }

    function my_register_route() {

        register_rest_route('restaurant', 'list', array(
            'methods' => 'GET',
            'callback' => array($this, 'restaurant_list')
                )
        );

        register_rest_route('restaurant', 'menu', array(
            'methods' => 'GET',
            'callback' => array($this, 'restaurant_menu')
                )
        );

        register_rest_route('restaurant', 'featured', array(
            'methods' => 'GET',
            'callback' => array($this, 'restaurants_featured')
                )
        );
        register_rest_route('restaurant', 'detail', array(
            'methods' => 'GET',
            'callback' => array($this, 'restaurant_detail')
                )
        );
        register_rest_route('restaurant', 'add_to_cart', array(
            'methods' => 'GET',
            'callback' => array($this, 'add_to_cart_callback'),
            )
        );
    }

    function restaurants_featured() {

        $today_date = date("Y-m-d");

        $today_date = strtotime($today_date);





        $restaurant_featured_list = array(
            'post_type' => 'restaurants',
            'post_status' => 'publish',
            'posts_per_page' => 6,
            'orderby' => 'title',
            'order' => 'ASC',
            'meta_query' => array(
                array(
                    'key' => 'foodbakery_restaurant_is_featured',
                    'value' => 'on',
                ),
                array(
                    'key' => 'foodbakery_restaurant_expired',
                    'value' => $today_date,
                    'compare' => '>=', // method of comparison 
                )
            )
        );

        $restaurant_featured_list_query = new WP_Query($restaurant_featured_list);



        while ($restaurant_featured_list_query->have_posts()) :
            $restaurant_featured_list_query->the_post();
            $post_id = get_the_ID();

            //$restaurant_list_image = get_post_meta($post_id, 'foodbakery_restaurant_featured_image_id', true);
            $restaurant_list_image = get_post_meta($post_id, 'foodbakery_cover_image', true);
            $restaurant_list_image_data = wp_get_attachment_image_src($restaurant_list_image, 'full');
            $restaurant_list_image_url = $restaurant_list_image_data[0];


            $restaurant_list_array[] = array(
                array(
                    'field_name' => 'restaurant_title',
                    'field_type' => 'text',
                    'data_type' => 'text_display',
                    'color' => '#000000',
                    'data' => get_the_title(),
                    'API_URL' => 'restaurant/detail?restaurant_id=' . $post_id
                ),
                array(
                    'field_name' => 'restaurant_image',
                    'field_type' => 'imageType',
                    'data_type' => 'imageType_display',
                    'data' => $restaurant_list_image_url
                )
            );


        endwhile;

        wp_reset_postdata();

        $restaurant_list_data = array(
            'section' => 'restaurants',
            'fields' => $restaurant_list_array
        );

        return rest_ensure_response($restaurant_list_data);
    }

    function restaurant_list() {

        $today_date = date("Y-m-d");

        $today_date = strtotime($today_date);
        
        $paged  = isset( $_GET['paged'] )? $_GET['paged'] : 1;


        $restaruant_category = isset( $_GET['restaruant_category'] )? $_GET['restaruant_category'] : '';

        $restaurant_list = array(
            'post_type' => 'restaurants',
            'post_status' => 'publish',
            'posts_per_page' => 10,
            'paged' => $paged,
            'orderby' => 'title',
            'order' => 'ASC');

        if ($restaruant_category != '') {
            $restaurant_list['tax_query'] = array(
                array(
                    'taxonomy' => 'restaurant-category',
                    'field' => 'slug',
                    'terms' => $restaruant_category,
                ),
                array(
                    'key' => 'foodbakery_restaurant_expired',
                    'value' => $today_date,
                    'compare' => '>=', // method of comparison 
                )
            );
        }

        $restaurant_list_loop = new WP_Query($restaurant_list);

        while ($restaurant_list_loop->have_posts()) :
            $restaurant_list_loop->the_post();
            $post_id = get_the_ID();

            //$restaurant_list_image = get_post_meta($post_id, 'foodbakery_restaurant_featured_image_id', true);
            $restaurant_list_image = get_post_meta($post_id, 'foodbakery_cover_image', true);
            $restaurant_list_image_data = wp_get_attachment_image_src($restaurant_list_image, 'full');
            $restaurant_list_image_url = $restaurant_list_image_data[0];


            $restaurant_list_array[] = array(
                array(
                    'field_name' => 'restaurant_title',
                    'field_type' => 'text',
                    'data_type' => 'text_display',
                    'color' => '#000000',
                    'data' => get_the_title(),
                    'API_URL' => 'restaurant/detail?restaurant_id=' . $post_id
                ),
                array(
                    'field_name' => 'restaurant_image',
                    'field_type' => 'imageType',
                    'data_type' => 'imageType_display',
                    'data' => $restaurant_list_image_url
                )
            );

        endwhile;

        wp_reset_postdata();
        
        $next_page = $paged+1;
        
        $next_page_api  = 'restaurant/list?paged='.$next_page;
        
        
        $search_form = array(
            array(
                'field_name' => 'restaurant_name',
                'field_type' => 'text',
                'data_type' => 'text',
                'color' => '#efefef',
                'required' => true,
                'data' => '',
                'placeholder' => 'Restaurant Name',
            ),
            array(
                'field_name' => ' guests',
                'field_type' => 'dropdown',
                'data_type' => 'dropdown',
                'color' => '#efefef',
                'data' => '',
                'placeholder' => 'Guests',
                'options' => array(
                    array(
                        'key' => '1',
                        'value' => '1'
                    ),
                    array(
                        'key' => '2',
                        'value' => ''
                    ),
                    array(
                        'key' => '3',
                        'value' => '3'
                    ),
                    array(
                        'key' => '4',
                        'value' => '4'
                    ),
                    array(
                        'key' => '5',
                        'value' => '5'
                    ),
                ),
            ),
            array(
                'field_name' => ' submit',
                'field_type' => 'submit',
                'data_type' => 'button',
                'color' => '#efefef',
                'data' => 'Search',
                'API_URL' => 'restaurant/list',
        ));

        $restaurant_list_data = array(
            'section' => 'restaurants',
            'fields' => $restaurant_list_array,
            'search_fields' => $search_form,
            'next_page_api' => $next_page_api
        );

        return rest_ensure_response($restaurant_list_data);
    }

    function restaurant_detail() {
        $restaurant_id = $_GET ['restaurant_id'];
        $foodbakery_restaurant_title = get_the_title($restaurant_id); //get_post_meta($restaurant_id, 'foodbakery_restaurant_title', true);
        $foodbakery_restaurant_desc = get_post_meta($restaurant_id, 'foodbakery_restaurant_desc', true);
        $foodbakery_restaurant_delivery_time = get_post_meta($restaurant_id, 'foodbakery_delivery_time', true);
        $foodbakery_restaurant_tags = get_post_meta($restaurant_id, 'foodbakery_restaurant_tags', true);
        if (empty($foodbakery_restaurant_tags)) {
            $foodbakery_restaurant_tags_comma = '';
        } else {
            $foodbakery_restaurant_tags_comma = implode(",", $foodbakery_restaurant_tags);
        }

        $foodbakery_restaurant_status = get_post_meta($restaurant_id, 'foodbakery_restaurant_status', true);

        $ratings_data = array(
            'overall_rating' => 0.0,
            'count' => 0,
        );
        $ratings_data = apply_filters('reviews_ratings_data', $ratings_data, $restaurant_id);



        $foodbakery_restaurant_ratings_data = get_post_meta($restaurant_id, 'reviews_ratings_data', true);

        $restaurant_general_info = array(
            array(
                'field_name' => 'foodbakery_restaurant_title',
                'field_type' => 'text',
                'data_type' => 'text_display',
                'color' => '#000000',
                'data' => $foodbakery_restaurant_title,
            ),
            array(
                'field_name' => 'foodbakery_restaurant_desc',
                'field_type' => 'text',
                'data_type' => 'text_display',
                'color' => '#000000',
                'data' => $foodbakery_restaurant_desc,
            ),
            array(
                'field_name' => 'foodbakery_delivery_time',
                'field_type' => 'text',
                'data_type' => 'text_display',
                'color' => '#000000',
                'data' => $foodbakery_restaurant_delivery_time,
                'icon' => 'f21c',
                'icon_color' => '#000000'
            ),
            array(
                'field_name' => 'foodbakery_restaurant_tags',
                'field_type' => 'text',
                'data_type' => 'text_display',
                'color' => '#000000',
                'data' => $foodbakery_restaurant_tags_comma,
                'icon' => 'f02c',
                'icon_color' => '#000000'
            ),
            array(
                'field_name' => 'foodbakery_restaurant_status',
                'field_type' => 'text',
                'data_type' => 'text_display',
                'color' => '#000000',
                'data' => $foodbakery_restaurant_status,
                'icon' => 'f08b',
                'icon_color' => '#000000'
            ),
            array(
                'field_name' => 'reviews_ratings_data',
                'field_type' => 'text',
                'data_type' => 'text_display',
                'color' => '#000000',
                'data' => $ratings_data['overall_rating'],
                'icon' => 'f005',
                'icon_color' => '#000000'
            ),
        );

        $value_restaurant_menu = get_post_meta($restaurant_id, 'foodbakery_menu_items', true);

        foreach ($value_restaurant_menu as $value) {
            $restaurant_menu_image = $value ['menu_item_icon'];
            $restaurant_menu_name = $value['menu_item_title'];
            $restaurant_menu_item_description = $value['menu_item_description'];
            $restaurant_menu_price = $value['menu_item_price'];
            $menu_item_identity = isset( $value['menu_item_identity'] )? $value['menu_item_identity'] : 0;
            $to_get_restaurant_menu[] = array(
                'restaurant_menu_image' => $value ['menu_item_icon'],
                'restaurant_menu_name' => $value['menu_item_title'],
                'restaurant_menu_item_description' => $value['menu_item_description'],
                'restaurant_menu_price' => $value['menu_item_price'],
                'menu_item_identity' => $menu_item_identity,
            );


            $restaurant_menu_image_data = wp_get_attachment_image_src($restaurant_menu_image, 'full');
            $restaurant_menu_image_url = $restaurant_menu_image_data[0];


            $restaurant_menu_array[] = array(
                array(
                    'field_name' => 'menu_item_icon',
                    'field_type' => 'imageType',
                    'data_type' => 'imageType_display',
                    'data' => $restaurant_menu_image_url,
                    'quantity' => 5,
                ),
                array(
                    'field_name' => 'menu_item_title',
                    'field_type' => 'text',
                    'data_type' => 'text_display',
                    'color' => '#efefef',
                    'data' => $restaurant_menu_name,
                ),
                array(
                    'field_name' => 'menu_detail',
                    'field_type' => 'text',
                    'data_type' => 'text_display',
                    'order' => 1,
                    'color' => '#efefef',
                    'data' => $restaurant_menu_item_description,
                ),
                array(
                    'field_name' => 'menu_item_price',
                    'field_type' => 'text',
                    'data_type' => 'text_display',
                    'color' => '#efefef',
                    'data' => $restaurant_menu_price,
                    'API_URL' => 'restaurant/add_to_cart/?restaurant_id='.$restaurant_id.'&menu_item_identity='.$menu_item_identity,
                ),
            );
        }



        $restaurant_rating = new Foodbakery_Reviews();
        $reviews = $restaurant_rating->get_user_reviews_for_post($restaurant_id, 0, 10);
        $restaurant_review_array    = array();

        foreach ($reviews as $reviews_data) {
            if (empty($reviews_data)) {
                continue;
            }
            $restaurant_reviewer_image = $reviews_data['img'];

            $restaurant_reviewer_image_data = wp_get_attachment_image_src($restaurant_reviewer_image, 'full');
            $restaurant_reviewer_image_url = $restaurant_reviewer_image_data[0];

            $reviewer_name = $reviews_data['username'];
            $review_date = $reviews_data['dated'];
            $review_star = $reviews_data['overall_rating'];
            $review_desc = $reviews_data['description'];


            $restaurant_review_array[] = array(
                array(
                    'field_name' => 'username',
                    'field_type' => 'text',
                    'data_type' => 'text_display',
                    'color' => '#efefef',
                    'data' => $reviewer_name,
                    'API_URL' => ''
                ),
                array(
                    'field_name' => 'reviewer_img',
                    'field_type' => 'text',
                    'data_type' => 'text_display',
                    'color' => '#efefef',
                    'data' => $restaurant_reviewer_image_url,
                    'API_URL' => ''
                ),
                array(
                    'field_name' => 'dated',
                    'field_type' => 'text',
                    'data_type' => 'text_display',
                    'color' => '#efefef',
                    'data' => $review_date,
                    'API_URL' => ''
                ),
                array(
                    'field_name' => 'overall_rating',
                    'field_type' => 'text',
                    'data_type' => 'text_display',
                    'color' => '#efefef',
                    'data' => $review_star,
                    'API_URL' => ''
                ),
                array(
                    'field_name' => 'description',
                    'field_type' => 'text',
                    'data_type' => 'text_display',
                    'color' => '#efefef',
                    'data' => $review_desc,
                    'API_URL' => ''
                ),
            );
        }

        $foodbakery_table_booking = array(
            array(
                'field_name' => 'first_name',
                'field_type' => 'text',
                'data_type' => 'text',
                'color' => '#efefef',
                'required' => true,
                'data' => '',
                'placeholder' => 'First Name',
            ),
            array(
                'field_name' => 'last_name',
                'field_type' => 'text',
                'data_type' => 'text',
                'color' => '#efefef',
                'required' => true,
                'data' => '',
                'placeholder' => 'Last Name',
            ),
            array(
                'field_name' => ' email_address',
                'field_type' => 'text',
                'data_type' => 'email',
                'color' => '#efefef',
                'required' => true,
                'data' => '',
                'placeholder' => 'Email Address',
            ),
            array(
                'field_name' => ' guests',
                'field_type' => 'dropdown',
                'data_type' => 'dropdown',
                'color' => '#efefef',
                'data' => '',
                'placeholder' => 'Guests',
                'options' => array(
                    array(
                        'key' => '1',
                        'value' => '1'
                    ),
                    array(
                        'key' => '2',
                        'value' => ''
                    ),
                    array(
                        'key' => '3',
                        'value' => '3'
                    ),
                    array(
                        'key' => '4',
                        'value' => '4'
                    ),
                    array(
                        'key' => '5',
                        'value' => '5'
                    ),
                ),
            ),
            array(
                'field_name' => ' booking_date',
                'field_type' => 'date',
                'data_type' => 'date',
                'color' => '#efefef',
                'required' => true,
                'data' => '',
                'placeholder' => 'Booking Date',
            ),
            array(
                'field_name' => ' time',
                'field_type' => 'time',
                'data_type' => 'time',
                'required' => true,
                'color' => '#efefef',
                'data' => '',
                'placeholder' => '12:00 AM',
            ),
            array(
                'field_name' => ' instruction',
                'field_type' => 'textarea',
                'data_type' => 'textarea',
                'color' => '#efefef',
                'data' => '',
                'placeholder' => 'Your Instruction',
            ),
            array(
                'field_name' => ' submit',
                'field_type' => 'submit',
                'data_type' => 'button',
                'color' => '#efefef',
                'data' => 'Submit',
                'API_URL' => '',
        ));

        $tabs_array = array(
            array(
                'tab_title' => 'Menu',
                'tab_type' => 'listing',
                'tab_data' => array(
                    'fields' => $restaurant_menu_array
                )
            ),
            array(
                'tab_title' => 'Reviews',
                'tab_type' => 'listing',
                'tab_data' => array(
                    'fields' => $restaurant_review_array
                )
            ),
            array(
                'tab_title' => 'table booking',
                'tab_type' => 'form',
                'tab_data' => array(
                    'form_fields' => $foodbakery_table_booking
                )
        ));

        $restaurant_detail = array(
            'section' => 'restaurant_detail',
            'fields' => $restaurant_general_info,
            'tabs' => $tabs_array
        );


        return rest_ensure_response($restaurant_detail);
    }

    function restaurant_menu() {


        $restaurant_id = $_GET ['restaurant_id'];
        $value_restaurant_menu = get_post_meta($restaurant_id, 'foodbakery_menu_items', true);

        foreach ($value_restaurant_menu as $value) {
            $restaurant_menu_name = $value['menu_item_title'];
            $restaurant_menu_price = $value['menu_item_price'];
            $to_get_restaurant_menu[] = array(
                'restaurant_menu_name' => $value['menu_item_title'],
                'restaurant_menu_price' => $value['menu_item_price'],
            );

            $restaurant_menu_array[] = array(
                array(
                    'field_name' => 'menu_item_title',
                    'field_type' => 'text',
                    'data_type' => 'text_display',
                    'color' => '#efefef',
                    'data' => $restaurant_menu_name,
                    'API_URL' => ''
                ),
                array(
                    'field_name' => 'menu_item_price',
                    'field_type' => 'text',
                    'data_type' => 'text_display',
                    'color' => '#efefef',
                    'data' => $restaurant_menu_price,
                    'API_URL' => ''
                ),
            );
        }

        $restaurant_menu = array(
            'section' => 'restaurant_menu',
            'fields' => $restaurant_menu_array
        );


        return rest_ensure_response($restaurant_menu);
    }
    
    public function add_to_cart_callback() {
        $restaurant_id = isset( $_GET['restaurant_id'] )? $_GET['restaurant_id'] : 0;
        $menu_item_identity = isset( $_GET['menu_item_identity'] )? $_GET['menu_item_identity'] : 0;
        update_option('add_to_cart_callback', json_encode($_GET));
        
        $response_data = array(
            'response_type' => 'flash_msg',
            'success'   => true,
            'message'   => 'Item added to cart'
        );
        return rest_ensure_response($response_data);
        
    }

}

new foodbakery_restaurant_api();
?>
