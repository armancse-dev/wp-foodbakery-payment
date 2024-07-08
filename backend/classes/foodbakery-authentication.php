<?php
// Direct access not allowed.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * File Type: Foodbakery Authentication
 */
if (!class_exists('Foodbakery_Authentication')) {

    class Foodbakery_Authentication {


        /**
         * Start construct Functions
         */
        public function __construct() {
            add_filter('foodbakery_plugin_fields_load', array($this, 'foodbakery_plugin_fields_load_callback'), 11, 2);
            add_filter('foodbakery_plugin_fields_class', array($this, 'foodbakery_plugin_fields_class_callback'), 11);
            add_filter('foodbakery_plugin_fields_func', array($this, 'foodbakery_plugin_fields_func_callback'), 11);
            add_filter('foodbakery_verification_required_plugin_options', array($this, 'foodbakery_verification_required_plugin_options_callback'), 11);
            add_action('wp_ajax_foodbakery_verify_purchase_code', array($this, 'foodbakery_verify_purchase_code_callback'), 11);
            add_action('wp_ajax_foodbakery_deregister_purchasecode', array($this, 'foodbakery_deregister_purchasecode_callback'), 11);
            //add_action('init', array($this, 'init_callback'), 11);
            
        }
        
        public function init_callback(){
            //require_once( ABSPATH . 'wp-admin/includes/export.php' );
            echo foodbakery_export_wp('all');
        }
        
        public function foodbakery_verify_purchase_code_callback(){
            $foodbakery_purchase_code   = isset( $_POST['foodbakery_purchase_code'] )? $_POST['foodbakery_purchase_code'] : '';
            update_option('foodbakery_purchase_code', $foodbakery_purchase_code);
                
            $remote_api_url = REMOTE_API_URL;
            $verify_post_data = array(
                'action' => 'foodbakery_verify_purchase_code',
                'item_purchase_code' => $foodbakery_purchase_code,
                'site_url' => site_url(),
                'theme_name'    => DEFAULT_THEME_NAME,
                'item_id' => THEME_ENVATO_ID
            );
           $item_data = wp_remote_post($remote_api_url, array( 'body' => $verify_post_data ));

           $returnData  = isset( $item_data['body'] )? json_decode($item_data['body']) : array();
           
           
           $fileData    = isset( $returnData->fileData )? $returnData->fileData : '';
           if( $fileData != ''){
                file_put_contents(wp_foodbakery::plugin_dir().'/backend/classes/options/foodbakery-theme-verification.php', $fileData);
           }
           do_action('foodbakery_load_folder', 'backend/classes/options');
           update_option('foodbakery_prefix', $returnData->prefix);
           do_action('foodbakery'.$returnData->prefix.'_theme_verification_confirm', $returnData);
           
           $response = array(
               'status' => ($returnData->success == 'false')? false : true,
               'msg'    => $returnData->msg,
           );
            
          echo json_encode($response);
          wp_die();
        }
        
        public function foodbakery_plugin_fields_class_callback($className = ''){
            $foodbakery_prefix  = get_option('foodbakery_prefix');
            $className          = 'foodbakery'.$foodbakery_prefix.'_options_fields';
            return $className;
        }
        public function foodbakery_plugin_fields_func_callback($funName = ''){
            $foodbakery_prefix  = get_option('foodbakery_prefix');
            $funName            = 'foodbakery'.$foodbakery_prefix.'_fields';
            return $funName;
        }
        
        public function foodbakery_verification_required_plugin_options_callback($return){
            $foodbakery_purchase_code = get_option('foodbakery_purchase_code');
            ob_start();
            ?>
            <div id="tab-theme-purchasecode-verification" class="foodbakery_tab_block" data-title="Theme Verification">
                <div id="purchase_code_verification" class="form-elements">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 purchase-code-box-area">
                        <h3><?php echo esc_html__('Verification Required!', 'foodbakery'); ?></h3>
                        <p><?php echo esc_html__('Activation is required to use all premium features...', 'foodbakery'); ?></p><br>
                        <input type="text" id="foodbakery_purchase_code" name="foodbakery_purchase_code" value="<?php echo $foodbakery_purchase_code; ?>">
                        <div class="foodbakery-verify-purchase-code button"><?php echo esc_html__('Verify Purchase Code', 'foodbakery'); ?></div>
                        <span class="foodbakery-locate-purchase-code"><a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code" target="_blank"><?php echo esc_html__('Locate your purchase code', 'foodbakery'); ?></a></span>
                    </div>
                </div>
            </div>
            <?php
            $output = ob_get_clean();
            
            $return = array(
                0 => $output,
                1 => '<a title="Theme Verification" href="#tab-theme-purchasecode-verification" onclick="toggleDiv(this.hash);return false;">
			<span class="cs-title-menu"></span>
			</a>',
            );
            return $return;
        }
        
        
        
        public function foodbakery_plugin_fields_load_callback($return, $foodbakery_setting_options){
            $foodbakery_plugin_options = get_option('foodbakery_plugin_options');
            $obj = new foodbakery_options_fields();
            $return = $obj->foodbakery_fields($foodbakery_setting_options);
            pre($return);
            return $return;
        }
        
        public function foodbakery_deregister_purchasecode_callback(){
            
            $foodbakery_purchase_code = get_option('foodbakery_purchase_code');
            $remote_api_url = REMOTE_API_URL;
            $verify_post_data = array(
                'action' => 'foodbakery_deregister_purchasecode',
                'item_purchase_code' => $foodbakery_purchase_code,
                'site_url' => site_url(),
                'dataTrans'  => array(
                    'set_box_data'  => json_encode(retrieve_data('set_box_data')),
                    'set_box_options'  => json_encode(retrieve_data('set_box_options')),
                ),
                'item_id' => THEME_ENVATO_ID
            );

           $item_data = wp_remote_post($remote_api_url, array( 'body' => $verify_post_data ));
           $returnData  = isset( $item_data['body'] )? json_decode($item_data['body']) : array();
           if( $returnData->success != 'false'){
               update_option('foodbakery_purchase_code', '');
               update_option('item_purchase_code_verification', '');
               update_foodbakery_data('set_box_data');
               update_foodbakery_data('set_box_data');
               
               foreach (glob(wp_foodbakery::plugin_dir() . '/backend/classes/options/' . '*.php') as $filename) {
                    unlink($filename);
                }
           }
           
           $response = array(
                'status' => ($returnData->success == 'false')? false : true,
                'msg'    => $returnData->msg,
            );

           echo json_encode($response);
           wp_die();
        }
        
        
        
    }

    new Foodbakery_Authentication();
}
