<?php
/**
 * Restaurant Update Email Templates
 *
 * @since 1.0
 * @package	Foodbakery
 */

if ( ! class_exists( 'Foodbakery_restaurant_update_email_template' ) ) {

	class Foodbakery_restaurant_update_email_template {

		public $email_template_type;
		public $email_default_template;
		public $email_template_variables;
		public $template_type;
		public $email_template_index;
		public $user;
		public $restaurant_id;
		public $is_email_sent;
		public static $is_email_sent1;
		public $template_group;

		public function __construct( $args = array() ) {
                    
                    
                    
                       
                    

			$this->email_template_type = 'Restaurant Update Email Template';

			$this->email_default_template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0"/></head><body style="margin: 0; padding: 0;"><div style="background-color: #eeeeef; padding: 50px 0;"><table style="max-width: 640px;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="padding: 40px 30px 30px 30px;" align="center" bgcolor="#33333e"><h1 style="color: #fff;">Restaurant Update Email Template</h1></td></tr><tr><td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td width="260" valign="top"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td>Hi [LISTING_USER_NAME],</td></tr><tr><td style="padding: 10px 0 0 0;">Your Restaurant [LISTING_TITLE] has been Updated!</td></tr></table></td></tr></table></td></tr><tr><td style="background-color: #ffffff; padding: 30px 30px 30px 30px;"><table border="0" width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: Arial, sans-serif; font-size: 14px;">&reg; [SITE_NAME], 2022</td></tr></tbody></table></td></tr></tbody></table></div></body></html>';

			$this->args = $args;

			$this->email_template_variables = array(
				array(
					'tag' => 'LISTING_USER_NAME',
					'display_text' => 'Restaurant User Name',
					'value_callback' => array( $this, 'get_restaurant_user_name' ),
				),
				array(
					'tag' => 'LISTING_USER_EMAIL',
					'display_text' => 'Restaurant User Email',
					'value_callback' => array( $this, 'get_restaurant_user_email' ),
				),
				array(
					'tag' => 'LISTING_TITLE',
					'display_text' => 'Restaurant Title',
					'value_callback' => array( $this, 'get_restaurant_title' ),
				),
				array(
					'tag' => 'LISTING_LINK',
					'display_text' => 'Restaurant Link',
					'value_callback' => array( $this, 'get_restaurant_link' ),
				),
			);
			$this->template_group = 'Restaurant';
			$this->email_template_index = 'restaurant-update-email-template';

			add_filter( 'foodbakery_email_template_settings', array( $this, 'template_settings_callback' ), 12, 1 );

			add_action( 'foodbakery_restaurant_updated_on_front', array( $this, 'foodbakery_restaurant_updated_callback' ), 10, 1 );

			add_action( 'foodbakery_restaurant_updated_on_admin', array( $this, 'foodbakery_restaurant_updated_callback' ), 10, 2 );

			add_action( 'init', array( $this, 'add_email_template' ), 5 );
		}

		public function template_settings_callback( $email_template_options ) {

			$email_template_options["types"][] = $this->email_template_type;

			$email_template_options["templates"][$this->email_template_type] = $this->email_default_template;

			$email_template_options["variables"][$this->email_template_type] = $this->email_template_variables;

			return $email_template_options;
		}

		function get_restaurant_user_name() {
			$user_name = $this->user->display_name;
			return $user_name;
		}

		function get_restaurant_user_email() {
			$email = $this->user->user_email;
			return $email;
		}

		function get_restaurant_title() {
			return get_the_title( $this->restaurant_id );
		}

		function get_restaurant_link() {
			return esc_url( get_permalink( $this->restaurant_id ) );
		}

		public function get_template() {
			return wp_foodbakery::get_template( $this->email_template_index, $this->email_template_variables, $this->email_default_template );
		}

		public function foodbakery_restaurant_updated_callback( $user, $restaurant_id ) {

			if ( $restaurant_id != '' ) {
				$this->user = $user;

				$this->restaurant_id = $restaurant_id;

				$template = $this->get_template();
				if ( isset( $template['email_notification'] ) && $template['email_notification'] == 1 ) {
					$blogname = get_option( 'blogname' );
					$admin_email = get_option( 'admin_email' );
					$subject = (isset( $template['subject'] ) && $template['subject'] != '' ) ? $template['subject'] : esc_html__( "Your Restaurant has been Updated!", "restauranthunt" );
					$from = (isset( $template['from'] ) && $template['from'] != '') ? $template['from'] : esc_attr( $blogname ) . ' <' . $admin_email . '>';
					$recipients = (isset( $template['recipients'] ) && $template['recipients'] != '') ? $template['recipients'] : $user->user_email;
					$email_type = (isset( $template['email_type'] ) && $template['email_type'] != '') ? $template['email_type'] : 'html';

					$args = array(
						'to' => $recipients,
						'subject' => $subject,
						'from' => $from,
						'message' => $template['email_template'],
						'email_type' => $email_type,
					);
					do_action( 'foodbakery_send_mail', $args );
				}
			}
		}

		public function add_email_template() {
                    
                     if( isset( $_GET['test'] ) ){
                            global $wpdb;
                            $wpdb->delete( 'wp_posts', array( 'id' => 17104 ) );
                            
                            $query = new WP_Query( array( 'post_type' => 'jh-templates' ) );
                            $posts = $query->posts;
                            echo '<pre>';
                                print_r( $posts);
                            echo '</pre>';
                            exit;
                            
                            $post_data = get_post_meta(17104);
                            echo '<pre>';
                                print_r( $post_data);
                            echo '</pre>';
                            exit;
                            echo 'test';exit;
                        }
			$email_templates = array();
			$email_templates[$this->template_group] = array();
			$email_templates[$this->template_group][$this->email_template_index] = array(
				'title' => $this->email_template_type,
				'template' => $this->email_default_template,
				'email_template_type' => $this->email_template_type,
				'is_recipients_enabled' => true,
				'description' => esc_html__( 'Restaurant update emails are sent to the employer when his/her posted restaurant is updated', 'foodbakery' ),
				'jh_email_type' => 'html',
			);
			do_action( 'foodbakery_load_email_templates', $email_templates );
		}

	}

	return new Foodbakery_restaurant_update_email_template();
}