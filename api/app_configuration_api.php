<?php

class App_Configuration_Api{

    function __construct() {

        add_action( 'rest_api_init', array($this, 'my_register_route') );

    }

    function my_register_route() {
        

        register_rest_route( 'info', 'theme_settings', array(
            'methods' => 'GET',
            'callback' => array($this,'theme_settings')
            )
        );


    }


function theme_settings() {
    global $foodbakery_var_options;
    $to_get_themes_settings= array(
        'foodbakery_var_custom_logo'=>$foodbakery_var_options['foodbakery_var_custom_logo'],
        'foodbakery_var_layout'=>$foodbakery_var_options['foodbakery_var_layout'],
        'foodbakery_var_bg_image'=>$foodbakery_var_options['foodbakery_var_bg_image'],
        'foodbakery_var_sub_header_sub_hdng'=>$foodbakery_var_options['foodbakery_var_sub_header_sub_hdng'],
        'foodbakery_var_copy_right'=>$foodbakery_var_options['foodbakery_var_copy_right'],
        'foodbakery_var_post_title_color'=>$foodbakery_var_options['foodbakery_var_post_title_color'],
        'foodbakery_var_flickr_key'=>$foodbakery_var_options['foodbakery_var_flickr_key']
        );
    return rest_ensure_response($to_get_themes_settings);
} 
}
new App_Configuration_Api();

?>