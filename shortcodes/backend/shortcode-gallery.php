<?php
/**
 * Shortcode Name : Gallery
 *
 * @package    foodbakery
 */
if (!function_exists('foodbakery_var_page_builder_gallery')) {

    function foodbakery_var_page_builder_gallery($die = 0)
    {
        global $post, $foodbakery_html_fields, $foodbakery_node, $foodbakery_var_html_fields, $foodbakery_var_form_fields, $foodbakery_var_frame_static_text;
        if (function_exists('foodbakery_shortcode_names')) {
            $shortcode_element = '';
            $filter_element = 'filterdrag';
            $shortcode_view = '';
            $foodbakery_output = array();
            $foodbakery_PREFIX = 'gallery';

            $foodbakery_counter = isset($_POST['counter']) ? $_POST['counter'] : '';
            if (isset($_POST['action']) && !isset($_POST['shortcode_element_id'])) {
                $foodbakery_POSTID = '';
                $shortcode_element_id = '';
            } else {
                $foodbakery_POSTID = isset($_POST['POSTID']) ? $_POST['POSTID'] : '';
                $shortcode_element_id = isset($_POST['shortcode_element_id']) ? $_POST['shortcode_element_id'] : '';
                $shortcode_str = stripslashes($shortcode_element_id);
                $parseObject = new ShortcodeParse();
                $foodbakery_output = $parseObject->foodbakery_shortcodes($foodbakery_output, $shortcode_str, true, $foodbakery_PREFIX);
            }
            $defaults = array(
                'gallery_title' => '',
                'gallery_view' => '',
                'foodbakery_images_url' => '',
                'selected_restaurant' => '',
            );
            if (isset($foodbakery_output['0']['atts'])) {
                $atts = $foodbakery_output['0']['atts'];
            } else {
                $atts = array();
            }
            if (isset($foodbakery_output['0']['content'])) {
                $gallery_column_text = $foodbakery_output['0']['content'];
            } else {
                $gallery_column_text = '';
            }
            $gallery_element_size = '100';
            foreach ($defaults as $key => $values) {
                if (isset($atts[$key])) {
                    $$key = $atts[$key];
                } else {
                    $$key = $values;
                }
            }
            $name = 'foodbakery_var_page_builder_gallery';
            $coloumn_class = 'column_' . $gallery_element_size;
            if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') {
                $shortcode_element = 'shortcode_element_class';
                $shortcode_view = 'cs-pbwp-shortcode';
                $filter_element = 'ajax-drag';
                $coloumn_class = '';
            }
            ?>

            <div id="<?php echo esc_attr($name . $foodbakery_counter) ?>_del"
                 class="column  parentdelete <?php echo esc_attr($coloumn_class); ?>
		 <?php echo esc_attr($shortcode_view); ?>" item="gallery"
                 data="<?php echo foodbakery_element_size_data_array_index($gallery_element_size) ?>">
                <?php foodbakery_element_setting($name, $foodbakery_counter, $gallery_element_size) ?>
                <div class="cs-wrapp-class-<?php echo intval($foodbakery_counter) ?>
		     <?php echo esc_attr($shortcode_element); ?>" id="<?php echo esc_attr($name . $foodbakery_counter) ?>"
                     data-shortcode-template="[gallery {{attributes}}]{{content}}[/gallery]"
                     style="display: none;">
                    <div class="cs-heading-area" data-counter="<?php echo esc_attr($foodbakery_counter) ?>">
                        <h5><?php echo esc_html__("Gallery Options", "foodbakery"); ?></h5>
                        <a href="javascript:foodbakery_frame_removeoverlay('<?php echo esc_js($name . $foodbakery_counter) ?>','<?php echo esc_js($filter_element); ?>')"
                           class="cs-btnclose">
                            <i class="icon-times"></i>
                        </a>
                    </div>
                    <div class="cs-pbwp-content">
                        <div class="cs-wrapp-clone cs-shortcode-wrapp">
                            <?php
                            if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') {
                                foodbakery_shortcode_element_size();
                            }

                            $foodbakery_opt_array = array(
                                'name' => esc_html__('Element Title', 'foodbakery'),
                                'desc' => '',
                                'hint_text' => esc_html__("Enter element title here.", "foodbakery"),
                                'echo' => true,
                                'field_params' => array(
                                    'std' => $gallery_title,
                                    'id' => 'gallery_title',
                                    'cust_name' => 'gallery_title[]',
                                    'return' => true,
                                ),
                            );
                            $foodbakery_html_fields->foodbakery_text_field($foodbakery_opt_array);
                            
                            
                            $foodbakery_opt_array = array(
				'std' => esc_url($foodbakery_var_frame_image_url_array),
				'id' => 'frame_image_url',
				'name' => foodbakery_var_theme_text_srt('foodbakery_var_image_field_url'),
				'desc' => '',
				'hint_text' => foodbakery_var_theme_text_srt('foodbakery_var_image_field_url_hint'),
				'echo' => true,
				'array' => true,
				'prefix' => '',
                                'images_ids' => $foodbakery_images_url,
				'field_params' => array(
				    'std' => esc_url($foodbakery_var_frame_image_url_array),
				    'id' => 'frame_image_url',
				    'return' => true,
				    'array' => true,
                                    'multi' => true,
				    'array_txt' => false,
				    'prefix' => '',
				),
			    );
			    $foodbakery_var_html_fields->foodbakery_var_upload_file_field($foodbakery_opt_array);
                            
                            

                           
                            if (function_exists('foodbakery_shortcode_custom_classes_test')) {
                                foodbakery_shortcode_custom_dynamic_classes($gallery_custom_class, $gallery_custom_animation, '', 'gallery');
                            }
                            ?>

                        </div>
                        <?php if (isset($_POST['shortcode_element']) && $_POST['shortcode_element'] == 'shortcode') { ?>
                            <ul class="form-elements insert-bg">
                                <li class="to-field">
                                    <a class="insert-btn cs-main-btn"
                                       onclick="javascript:foodbakery_shortcode_insert_editor('<?php echo str_replace('foodbakery_var_page_builder_', '', $name); ?>', '<?php echo esc_js($name . $foodbakery_counter) ?>', '<?php echo esc_js($filter_element); ?>')"><?php echo foodbakery_var_frame_text_srt('foodbakery_var_insert'); ?></a>
                                </li>
                            </ul>
                            <div id="results-shortocde"></div>
                        <?php } else { ?>

                            <?php
                            $foodbakery_opt_array = array(
                                'std' => 'gallery',
                                'id' => '',
                                'before' => '',
                                'after' => '',
                                'classes' => '',
                                'extra_atr' => '',
                                'cust_id' => 'foodbakery_orderby' . $foodbakery_counter,
                                'cust_name' => 'foodbakery_orderby[]',
                                'required' => false
                            );
                            $foodbakery_var_form_fields->foodbakery_var_form_hidden_render($foodbakery_opt_array);

                            $foodbakery_opt_array = array(
                                'name' => '',
                                'desc' => '',
                                'hint_text' => '',
                                'echo' => true,
                                'field_params' => array(
                                    'std' => 'Save',
                                    'cust_id' => 'gallery_save',
                                    'cust_type' => 'button',
                                    'extra_atr' => 'onclick="javascript:_removerlay(jQuery(this))"',
                                    'classes' => 'cs-foodbakery-admin-btn',
                                    'cust_name' => 'gallery_save',
                                    'return' => true,
                                ),
                            );

                            $foodbakery_var_html_fields->foodbakery_var_text_field($foodbakery_opt_array);
                        }
                        ?>
                    </div>
                </div>
                <script type="text/javascript">

                    popup_over();
                    chosen_selectionbox();

                </script>
            </div>

            <?php
        }
        if ($die <> 1) {
            die();
        }
    }

    add_action('wp_ajax_foodbakery_var_page_builder_gallery', 'foodbakery_var_page_builder_gallery');
}

if (!function_exists('foodbakery_save_page_builder_data_gallery_callback')) {

    /**
     * Save data for gallery shortcode.
     *
     * @param    array $args
     * @return    array
     */
    function foodbakery_save_page_builder_data_gallery_callback($args)
    {
        //pre($args);
        
        $foodbakery_images_url_data = isset( $args['data']['foodbakery_images_url'] )? $args['data']['foodbakery_images_url'] : '';
        if( !empty( $foodbakery_images_url_data ) ){
            $foodbakery_images_url_data = implode(',', $foodbakery_images_url_data);
        }
        //pre($foodbakery_images_url_data);
        //$foodbakery_images_url_data = stripslashes($foodbakery_images_url_data);
        //$foodbakery_images_url_data = serialize($foodbakery_images_url_data);
        //pre($foodbakery_images_url_data);
        
        $shortcode_data = '';
        $data = $args['data'];
        $counters = $args['counters'];
        $widget_type = $args['widget_type'];
        $column = $args['column'];
        if ($widget_type == "gallery" || $widget_type == "cs_gallery") {
            $foodbakery_bareber_gallery = '';

            $page_element_size = $data['gallery_element_size'][$counters['foodbakery_global_counter_gallery']];
            $current_element_size = $data['gallery_element_size'][$counters['foodbakery_global_counter_gallery']];

            if (isset($data['foodbakery_widget_element_num'][$counters['foodbakery_counter']]) && $data['foodbakery_widget_element_num'][$counters['foodbakery_counter']] == 'shortcode') {
                $shortcode_str = stripslashes(($data['shortcode']['gallery'][$counters['foodbakery_shortcode_counter_gallery']]));

                $element_settings = 'gallery_element_size="' . $current_element_size . '"';
                $reg = '/gallery_element_size="(\d+)"/s';
                $shortcode_str = preg_replace($reg, $element_settings, $shortcode_str);
                $shortcode_data .= $shortcode_str;

                $counters['foodbakery_shortcode_counter_gallery']++;
            } else {
                $element_settings = 'gallery_element_size="' . htmlspecialchars($data['gallery_element_size'][$counters['foodbakery_global_counter_gallery']]) . '"';
                $foodbakery_bareber_gallery = '[gallery ' . $element_settings . ' ';
                if (isset($data['gallery_title'][$counters['foodbakery_counter_gallery']]) && $data['gallery_title'][$counters['foodbakery_counter_gallery']] != '') {
                    $foodbakery_bareber_gallery .= 'gallery_title="' . htmlspecialchars($data['gallery_title'][$counters['foodbakery_counter_gallery']], ENT_QUOTES) . '" ';
                }
                
                if (isset($data['selected_restaurant'][$counters['foodbakery_counter_gallery']]) && $data['selected_restaurant'][$counters['foodbakery_counter_gallery']] != '') {
                    $foodbakery_bareber_gallery .= 'selected_restaurant="' . htmlspecialchars($data['selected_restaurant'][$counters['foodbakery_counter_gallery']], ENT_QUOTES) . '" ';
                }
                
                if (isset($data['gallery_view'][$counters['foodbakery_counter_gallery']]) && $data['gallery_view'][$counters['foodbakery_counter_gallery']] != '') {
                    $foodbakery_bareber_gallery .= 'gallery_view="' . htmlspecialchars($data['gallery_view'][$counters['foodbakery_counter_gallery']], ENT_QUOTES) . '" ';
                }
                
                
                if (isset($data['foodbakery_images_url'][$counters['foodbakery_counter_gallery']]) && $data['foodbakery_images_url'][$counters['foodbakery_counter_gallery']] != '') {
                    $foodbakery_bareber_gallery .= 'foodbakery_images_url="' . htmlspecialchars($foodbakery_images_url_data, ENT_QUOTES) . '" ';
                }
                
                

                $foodbakery_bareber_gallery .= ']';
                if (isset($data['gallery_column_text'][$counters['foodbakery_counter_gallery']]) && $data['gallery_column_text'][$counters['foodbakery_counter_gallery']] != '') {
                    $foodbakery_bareber_gallery .= htmlspecialchars($data['gallery_column_text'][$counters['foodbakery_counter_gallery']], ENT_QUOTES) . ' ';
                }
                $foodbakery_bareber_gallery .= '[/gallery]';

                $shortcode_data .= $foodbakery_bareber_gallery;
                
                
                //pre($shortcode_data);
                
                $counters['foodbakery_counter_gallery']++;
            }
            $counters['foodbakery_global_counter_gallery']++;
        }
        
        //pre($foodbakery_bareber_gallery);
        //pre($args);
        return array(
            'data' => $data,
            'counters' => $counters,
            'widget_type' => $widget_type,
            'column' => $shortcode_data,
        );
    }

    add_filter('foodbakery_save_page_builder_data_gallery', 'foodbakery_save_page_builder_data_gallery_callback');
}

if (!function_exists('foodbakery_load_shortcode_counters_gallery_callback')) {

    /**
     * Populate gallery shortcode counter variables.
     *
     * @param    array $counters
     * @return    array
     */
    function foodbakery_load_shortcode_counters_gallery_callback($counters)
    {
        $counters['foodbakery_global_counter_gallery'] = 0;
        $counters['foodbakery_shortcode_counter_gallery'] = 0;
        $counters['foodbakery_counter_gallery'] = 0;
        return $counters;
    }

    add_filter('foodbakery_load_shortcode_counters', 'foodbakery_load_shortcode_counters_gallery_callback');
}


if (!function_exists('foodbakery_element_list_populate_gallery_callback')) {

    /**
     * Populate gallery shortcode strings list.
     *
     * @param    array $counters
     * @return    array
     */
    function foodbakery_element_list_populate_gallery_callback($element_list)
    {
        $element_list['gallery'] = 'Foodbakery Gallery';
        return $element_list;
    }

    add_filter('foodbakery_element_list_populate', 'foodbakery_element_list_populate_gallery_callback');
}

if (!function_exists('foodbakery_shortcode_names_list_populate_gallery_callback')) {

    /**
     * Populate gallery shortcode names list.
     *
     * @param    array $counters
     * @return    array
     */
    function foodbakery_shortcode_names_list_populate_gallery_callback($shortcode_array)
    {
        $shortcode_array['gallery'] = array(
            'title' => 'FB: Gallery',
            'name' => 'gallery',
            'icon' => 'icon-food',
            'categories' => 'typography',
        );

        return $shortcode_array;
    }

    //add_filter('foodbakery_shortcode_names_list_populate', 'foodbakery_shortcode_names_list_populate_gallery_callback');
}
