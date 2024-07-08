<?php
/**
 * File Type: Searchs Shortcode Frontend
 */
if (!class_exists('Foodbakery_Shortcode_Gallery_front')) {

    class Foodbakery_Shortcode_Gallery_front {

        /**
         * Constant variables
         */
        var $PREFIX = 'gallery';

        /**
         * Start construct Functions
         */
        public function __construct() {
            add_shortcode($this->PREFIX, array($this, 'foodbakery_gallery_shortcode_callback'));
            add_action('foodbakery_gallery_shortcode', array($this, 'foodbakery_gallery_shortcode_callback'), 10, 2);
        }

        /*
         * Shortcode View on Frontend
         */
        
        public function foodbakery_gallery_shortcode_callback($atts, $content = "") {
            global $column_container, $foodbakery_form_fields_frontend, $foodbakery_plugin_options, $current_user;

            $foodbakery_images_ids = isset($atts['foodbakery_images_url']) ? $atts['foodbakery_images_url'] : 0;
            $foodbakery_images_ids_array = array();
            if ($foodbakery_images_ids != '') {
                $foodbakery_images_ids_array = explode(',', $foodbakery_images_ids);
            }
            ?>

            <?php if (!empty($foodbakery_images_ids_array)) { ?>

                        <div class="banner-gallery" style="min-height: 850px;">
                                                <ul>
                                                    <?php foreach ($foodbakery_images_ids_array as $foodbakery_images_id) { 
                                                        
                                                        $foodbakery_gallery_image_src = wp_get_attachment_image_src($foodbakery_images_id, 'small');
                                                        $foodbakery_gallery_image_src = isset($foodbakery_gallery_image_src[0]) ? $foodbakery_gallery_image_src[0] : '';
                                                        
                                                        
                                                        $colors_array = array( '54b13380', 'b133a480', '8fb13380', '0c62f180', 'b98a1280', '2f302c80', 'ff010180');
                                                        $overlay_color = $colors_array[array_rand($colors_array, 1)];
                                                        
                                                        
                                                        $slider_delay = array( 2000, 8000, 12000, 16000);
                                                        $slider_delay_number = $slider_delay[array_rand($slider_delay, 1)];
                                                        
                                                        
                                                        
                                                        $gallery_image1 = $foodbakery_images_ids_array[array_rand($foodbakery_images_ids_array, 1)];
                                                        $gallery_image2 = $foodbakery_images_ids_array[array_rand($foodbakery_images_ids_array, 1)];
                                                        $gallery_image3 = $foodbakery_images_ids_array[array_rand($foodbakery_images_ids_array, 1)];
                                                        
                                                        
                                                        $gallery_image1_src = wp_get_attachment_image_src($gallery_image1, 'small');
                                                        $gallery_image1_src = isset($gallery_image1_src[0]) ? $gallery_image1_src[0] : '';
                                                        
                                                        $gallery_image2_src = wp_get_attachment_image_src($gallery_image2, 'small');
                                                        $gallery_image2_src = isset($gallery_image2_src[0]) ? $gallery_image2_src[0] : '';
                                                        
                                                        $gallery_image3_src = wp_get_attachment_image_src($gallery_image3, 'small');
                                                        $gallery_image3_src = isset($gallery_image3_src[0]) ? $gallery_image3_src[0] : '';
                                                        
                                                        ?>
                                                        
                                                        <li>
                                                            <div class="swiper-container">
                                                                <div class="swiper-wrapper">
                                                                    <div class="img-holder swiper-slide" data-timeout="<?php echo esc_attr($slider_delay_number); ?>">
                                                                        <div class="overlay-image" style="background-color:#<?php echo $colors_array[array_rand($colors_array, 1)]; ?>;"></div>
                                                                        <a href="#"><img src="<?php echo esc_url($foodbakery_gallery_image_src); ?>" alt="#"></a>
                                                                    </div> 
                                                                    <div class="img-holder swiper-slide" data-timeout="<?php echo esc_attr($slider_delay_number); ?>">
                                                                        <div class="overlay-image" style="background-color:#<?php echo $colors_array[array_rand($colors_array, 1)]; ?>;"></div>
                                                                        <a href="#"><img src="<?php echo esc_url($gallery_image2_src); ?>" alt="#"></a>
                                                                    </div>
                                                                    <div class="img-holder swiper-slide" data-timeout="<?php echo esc_attr($slider_delay_number); ?>">
                                                                        <div class="overlay-image" style="background-color:#<?php echo $colors_array[array_rand($colors_array, 1)]; ?>;"></div>
                                                                        <a href="#"><img src="<?php echo esc_url($gallery_image2_src); ?>" alt="#"></a>
                                                                    </div>
                                                                    <div class="img-holder swiper-slide" data-timeout="<?php echo esc_attr($slider_delay_number); ?>">
                                                                        <div class="overlay-image" style="background-color:#<?php echo $colors_array[array_rand($colors_array, 1)]; ?>;"></div>
                                                                        <a href="#"><img src="<?php echo esc_url($gallery_image3_src); ?>" alt="#"></a>
                                                                    </div>   
                                                                </div>
                                                            </div>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                <?php
            }
        }

        public function foodbakery_gallery_shortcode_callback_bk($atts, $content = "") {
            global $column_container, $foodbakery_form_fields_frontend, $foodbakery_plugin_options, $current_user;

            $foodbakery_images_ids = isset($atts['foodbakery_images_url']) ? $atts['foodbakery_images_url'] : 0;
            $foodbakery_images_ids_array = array();
            if ($foodbakery_images_ids != '') {
                $foodbakery_images_ids_array = explode(',', $foodbakery_images_ids);
            }
            ?>

            <?php if (!empty($foodbakery_images_ids_array)) { ?>

                <div class="page-section banner-section fb_background_slider nopadding cs-nomargin" style="min-height: 850px;">
                    <div class="wide">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="video-gallery-holder">
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <div class="banner-gallery">
                                                <ul>
                                                    <?php foreach ($foodbakery_images_ids_array as $foodbakery_images_id) { 
                                                        
                                                        $foodbakery_gallery_image_src = wp_get_attachment_image_src($foodbakery_images_id, 'small');
                                                        $foodbakery_gallery_image_src = isset($foodbakery_gallery_image_src[0]) ? $foodbakery_gallery_image_src[0] : '';
                                                        
                                                        
                                                        $colors_array = array( '54b13380', 'b133a480', '8fb13380', '0c62f180', 'b98a1280', '2f302c80', 'ff010180');
                                                        $overlay_color = $colors_array[array_rand($colors_array, 1)];
                                                        
                                                        
                                                        $slider_delay = array( 2000, 8000, 12000, 16000);
                                                        $slider_delay_number = $slider_delay[array_rand($slider_delay, 1)];
                                                        
                                                        
                                                        
                                                        $gallery_image1 = $foodbakery_images_ids_array[array_rand($foodbakery_images_ids_array, 1)];
                                                        $gallery_image2 = $foodbakery_images_ids_array[array_rand($foodbakery_images_ids_array, 1)];
                                                        $gallery_image3 = $foodbakery_images_ids_array[array_rand($foodbakery_images_ids_array, 1)];
                                                        
                                                        
                                                        $gallery_image1_src = wp_get_attachment_image_src($gallery_image1, 'small');
                                                        $gallery_image1_src = isset($gallery_image1_src[0]) ? $gallery_image1_src[0] : '';
                                                        
                                                        $gallery_image2_src = wp_get_attachment_image_src($gallery_image2, 'small');
                                                        $gallery_image2_src = isset($gallery_image2_src[0]) ? $gallery_image2_src[0] : '';
                                                        
                                                        $gallery_image3_src = wp_get_attachment_image_src($gallery_image3, 'small');
                                                        $gallery_image3_src = isset($gallery_image3_src[0]) ? $gallery_image3_src[0] : '';
                                                        
                                                        ?>
                                                        
                                                        <li>
                                                            <div class="swiper-container">
                                                                <div class="swiper-wrapper">
                                                                    <div class="img-holder swiper-slide" data-timeout="<?php echo esc_attr($slider_delay_number); ?>">
                                                                        <div class="overlay-image" style="background-color:#<?php echo $colors_array[array_rand($colors_array, 1)]; ?>;"></div>
                                                                        <a href="#"><img src="<?php echo esc_url($foodbakery_gallery_image_src); ?>" alt="#"></a>
                                                                    </div> 
                                                                    <div class="img-holder swiper-slide" data-timeout="<?php echo esc_attr($slider_delay_number); ?>">
                                                                        <div class="overlay-image" style="background-color:#<?php echo $colors_array[array_rand($colors_array, 1)]; ?>;"></div>
                                                                        <a href="#"><img src="<?php echo esc_url($gallery_image2_src); ?>" alt="#"></a>
                                                                    </div>
                                                                    <div class="img-holder swiper-slide" data-timeout="<?php echo esc_attr($slider_delay_number); ?>">
                                                                        <div class="overlay-image" style="background-color:#<?php echo $colors_array[array_rand($colors_array, 1)]; ?>;"></div>
                                                                        <a href="#"><img src="<?php echo esc_url($gallery_image2_src); ?>" alt="#"></a>
                                                                    </div>
                                                                    <div class="img-holder swiper-slide" data-timeout="<?php echo esc_attr($slider_delay_number); ?>">
                                                                        <div class="overlay-image" style="background-color:#<?php echo $colors_array[array_rand($colors_array, 1)]; ?>;"></div>
                                                                        <a href="#"><img src="<?php echo esc_url($gallery_image3_src); ?>" alt="#"></a>
                                                                    </div>   
                                                                </div>
                                                            </div>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        }

    }

    global $foodbakery_shortcode_restaurant_search_front;
    $foodbakery_shortcode_restaurant_search_front = new Foodbakery_Shortcode_Gallery_front();
}
