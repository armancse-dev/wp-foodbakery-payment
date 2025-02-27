<?php
/**
 * Review Added Email Template.
 *
 * @since 1.0
 * @package	Foodbakery
 */

if ( ! class_exists( 'Foodbakery_review_added_email_template' ) ) {

	class Foodbakery_review_added_email_template {

		public $email_template_type;
		public $email_default_template;
		public $email_template_variables;
		public $template_type;
		public $email_template_index;
		public $user;
		public $review_id;
		public $is_email_sent;
		public static $is_email_sent1;
		public $template_group;

		public function __construct() {
			$this->user = array();
			$this->email_template_type = 'Review Added';

			$this->email_default_template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0"/></head><body style="margin: 0; padding: 0;"><div style="background-color: #eeeeef; padding: 50px 0;"><table style="max-width: 640px;" border="0" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td style="padding: 40px 30px 30px 30px;" align="center" bgcolor="#33333e"><h1 style="color: #fff;">Review Added</h1></td></tr><tr><td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td width="260" valign="top"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td>Hello! A Review has been added to your post "[REVIEW_POST_TITLE]".</td></tr></table></td></tr></table></td></tr><tr><td style="background-color: #ffffff; padding: 30px 30px 30px 30px;"><table border="0" width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td style="font-family: Arial, sans-serif; font-size: 14px;">&reg; [SITE_NAME], 2022</td></tr></tbody></table></td></tr></tbody></table></div></body></html>';

			$this->email_template_variables = array(
				array(
					'tag' => 'REVIEW_USER_NAME',
					'display_text' => 'Review User',
					'value_callback' => array( $this, 'get_review_added_user_name' ),
				),
				array(
					'tag' => 'REVIEW_DESCRIPTION',
					'display_text' => 'Review Description',
					'value_callback' => array( $this, 'get_review_added_description' ),
				),
				array(
					'tag' => 'REVIEW_RATING_SUMMARY',
					'display_text' => 'Review Rating Summary',
					'value_callback' => array( $this, 'get_review_added_rating_summary' ),
				),
				array(
					'tag' => 'REVIEW_OVERALL_RATING',
					'display_text' => 'Review Overall Rating',
					'value_callback' => array( $this, 'get_review_added_overall_rating' ),
				),
				array(
					'tag' => 'REVIEW_USER_EMAIL',
					'display_text' => 'Review User Email',
					'value_callback' => array( $this, 'get_review_added_email' ),
				),
				array(
					'tag' => 'REVIEW_POST_TITLE',
					'display_text' => 'Review Post Title',
					'value_callback' => array( $this, 'get_review_added_post_title' ),
				),
			);
			$this->template_group = 'Review';

			$this->email_template_index = 'review-added-template';
			add_action( 'init', array( $this, 'add_email_template' ), 5 );
			add_filter( 'foodbakery_email_template_settings', array( $this, 'template_settings_callback' ), 12, 1 );
			add_action( 'foodbakery_review_added_email', array( $this, 'foodbakery_review_added_email_callback' ), 10, 2 );
		}

		public function foodbakery_review_added_email_callback( $user = '', $review_id = '' ) {

			$this->user = $user;
			$this->review_id = $review_id;
                        $reciever_email = '';
			$template = $this->get_template();
                        $publisher_email        = '';
                        $post_slug      = get_post_meta( $review_id, 'post_id', true );
                        
                        $get_post = array(
				'name' => $post_slug,
				'post_type' => 'restaurants',
			);
			$post_obj = get_posts( $get_post );
			/*
			 * usiing required post id get that restaurant type
			 */
			$post_id = '';
			if ( isset( $post_obj[0]->ID ) && $post_obj[0]->ID != '' ) {
				$post_id = $post_obj[0]->ID;
			}
                        
                        $publisher_id   = get_post_meta( $post_id, 'foodbakery_restaurant_username', true );
                        $user_id        = get_post_meta( $post_id, 'foodbakery_restaurant_publisher', true );

                        if( isset( $publisher_id ) && $publisher_id != '' ){
                            $publisher_email = get_post_meta( $publisher_id, 'foodbakery_email_address', true );
                        }
                        if( isset( $user_id ) && $user_id != '' ){
                            $user_info = get_userdata( $user_id );
                            $user_email = is_object($user_info) ? $user_info->user_email : '';
                        }

                        $reciever_email = ( $publisher_email != '')? $publisher_email: $user_email;
			// checking email notification is enable/disable
			if ( isset( $template['email_notification'] ) && $template['email_notification'] == 1 ) {

				$blogname = get_option( 'blogname' );
				$admin_email = get_option( 'admin_email' );
				// getting template fields
				$subject = (isset( $template['subject'] ) && $template['subject'] != '' ) ? $template['subject'] : esc_html__( 'Review Added', 'foodbakery' );
				$from = (isset( $template['from'] ) && $template['from'] != '') ? $template['from'] : esc_attr( $blogname ) . ' <' . $admin_email . '>';
				$recipients = (isset( $template['recipients'] ) && $template['recipients'] != '') ? $template['recipients'].','.$reciever_email : $reciever_email;
				$email_type = (isset( $template['email_type'] ) && $template['email_type'] != '') ? $template['email_type'] : 'html';
                                
				$args = array(
					'to' => $recipients,
					'subject' => $subject,
					'from' => $from,
					'message' => $template['email_template'],
					'email_type' => $email_type,
					'class_obj' => $this,
				);
				
				do_action( 'foodbakery_send_mail', $args );
				Foodbakery_review_added_email_template::$is_email_sent1 = $this->is_email_sent;
			}
		}

		public function add_email_template() {
			$email_templates = array();
			$email_templates[$this->template_group] = array();
			$email_templates[$this->template_group][$this->email_template_index] = array(
				'title' => $this->email_template_type,
				'template' => $this->email_default_template,
				'email_template_type' => $this->email_template_type,
				'is_recipients_enabled' => TRUE,
				'description' => esc_html__( 'This template is used to send email when new review added.', 'foodbakery' ),
				'jh_email_type' => 'html',
			);
			do_action( 'foodbakery_load_email_templates', $email_templates );
		}

		public function template_settings_callback( $email_template_options ) {

			$email_template_options["types"][] = $this->email_template_type;

			$email_template_options["templates"][$this->email_template_type] = $this->email_default_template;

			$email_template_options["variables"][$this->email_template_type] = $this->email_template_variables;

			return $email_template_options;
		}

		public function get_template() {
			return wp_foodbakery::get_template( $this->email_template_index, $this->email_template_variables, $this->email_default_template );
		}

		function get_review_added_user_name() {
			$user_name = $this->user->display_name;
			return $user_name;
		}

		function get_review_added_description() {
			$description = get_post_field( 'post_content', $this->review_id );
			return $description;
		}

		function get_review_added_email() {
			$email = $this->user->user_email;
			return $email;
		}
		
		function get_review_added_post_title() {
			$output = '';
			$post_slug = get_post_meta( $this->review_id, 'post_id', true );

			$args = array(
				'name' => $post_slug,
				'post_type' => 'restaurants',
				'post_status' => 'publish',
				'numberposts' => 1
			);
			$restaurants = get_posts( $args );
			$output = $post_slug;
                        $post_link  = get_permalink( $restaurants[0]->ID );
			// If restaurant found.
			if (0 < count( $restaurants ) ) {
				$output = '<a href="' . $post_link . '">' . $restaurants[0]->post_title. '</a>';
			}
			return $output;
		}

		function get_review_added_overall_rating() {
			$overall_rting = get_post_meta( $this->review_id, 'overall_rating', true );
			return ( $overall_rting  . '/5' );
		}

		function get_review_added_rating_summary() {
			$output = ' ';
			$ratings = get_post_meta( $this->review_id, 'ratings', true );
			if ( is_array( $ratings ) && $ratings != '' ) {
				foreach ( $ratings as $key => $rating ) {
					$output .= $key . ': ' . $rating . '/5<br>';
				}
			}
			return $output;
		}

	}

	new Foodbakery_review_added_email_template();
}
