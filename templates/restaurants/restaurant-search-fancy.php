<?php
/**
 * Jobs Restaurant search box
 *
 */
?>
<!--Element Section Start-->
<!--Foodbakery Element Start-->
<?php
global $foodbakery_post_restaurant_types;
$rand_numb = isset( $atts['rand_numb'] ) ? $atts['rand_numb'] : '0';
$foodbakery_search_result_page = isset( $atts['foodbakery_search_result_page'] ) ? $atts['foodbakery_search_result_page'] : '0';
?>
<div class="main-search fancy bg-holder">
    <form name="foodbakery-restaurant-form" id="frm_restaurant_arg<?php echo intval( $rand_numb ); ?>" action="<?php echo esc_html( $foodbakery_search_result_page ); ?>" >
        <?php
        $listing_type = isset( $_GET['listing_type'] ) ? $_GET['listing_type'] : '';
        $search_title = isset( $_GET['search_title'] ) ? $_GET['search_title'] : '';
        $search_location = isset( $_GET['location'] ) ? $_GET['location'] : '';
        $search_field_cols = apply_filters('foodbakery_search_field_cols', 'col-lg-6 col-md-6 col-sm-5 col-xs-12');
        $show_location_field = isset( $atts['restaurant_search_show_location_field'] )? $atts['restaurant_search_show_location_field'] : 'yes';
        $search_field_cols = ($show_location_field == 'no')? 'col-lg-10 col-md-10 col-sm-5 col-xs-12' : $search_field_cols;
        ?>
        <div class="row">
            <?php do_action('foodbakery_serach_field_before'); ?>
            <div class="<?php echo esc_attr($search_field_cols); ?>">
                <div class="field-holder">
                    <span class="restaurant-element-search-btn"> <i class="icon-search5"></i> </span>
                    <input type="text" placeholder="<?php echo esc_html__('Resturant name', 'foodbakery'); ?>" name="search_title" value="<?php echo esc_html( $search_title ) ?>">
                </div>
            </div>
            <?php if( $show_location_field == 'yes'){ ?>
                <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12">
                    <div class="field-holder">
                        <ul>
                            <?php
                            foodbakery_get_custom_locations_listing_filter( '', '', false, $rand_numb, 'filter', '', '' );
                            ?>
                        </ul>
                    </div>
                </div>
            <?php } ?>
            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
                <div class="field-holder">
                    <input type="submit" value="<?php _e( 'Search', 'foodbakery' ) ?>">
                </div>
            </div>
        </div>

    </form>
</div>
<!--Foodbakery Element End-->