<?php

if (!class_exists('anr_captcha_class'))
{
  class anr_captcha_class
  {
	private static $instance;
	
	private static $captcha_count = 0;
	
	public static function init()
		{
			if(!self::$instance instanceof self) {
				self::$instance = new self;
			}
			return self::$instance;
		}
		
	function actions_filters()
		{
			if ( '1' == anr_get_option( 'fep_contact_form' )) {
					add_action ('fepcf_message_form_after_content', array($this, 'form_field'), 99);
					add_action ('fepcf_action_message_before_send', array($this, 'fepcf_verify'));
				}
			
			if ( '1' == anr_get_option( 'login' ) && !defined('XMLRPC_REQUEST')) {
					add_action ('login_form', array($this, 'login_form_field'), 99);
					add_action ('woocommerce_login_form', array($this, 'login_form_field'), 99);
					add_filter ('authenticate', array($this, 'login_verify'), 999, 3 );
					
					add_action ('wp_login', array($this, 'clear_data'), 10, 2);
				}
				
			if ( '1' == anr_get_option( 'wc_checkout' )) {
					add_action( 'woocommerce_after_checkout_validation', array($this, 'wc_checkout_verify') );
					add_action ('woocommerce_checkout_after_order_review', array($this, 'wc_form_field') );
				}
			
			if ( '1' == anr_get_option( 'registration' )) {
					add_action ('register_form', array($this, 'form_field'), 99);
					add_action ('woocommerce_register_form', array($this, 'form_field'), 99);
					add_filter ('registration_errors', array($this, 'registration_verify'), 10, 3 );
					add_filter ('woocommerce_registration_errors', array($this, 'registration_verify'), 10, 3 );
					add_action ('woocommerce_checkout_after_order_review', array($this, 'wc_form_field') );
				}
			
			if ( '1' == anr_get_option( 'ms_user_signup' )) {
					add_action ('signup_extra_fields', array($this, 'ms_form_field'), 99);
					add_filter ('wpmu_validate_user_signup', array($this, 'ms_form_field_verify'));
				}
			
			if ( '1' == anr_get_option( 'lost_password' )) {
					add_action ('lostpassword_form', array($this, 'form_field'), 99);
					add_action ('woocommerce_lostpassword_form', array($this, 'form_field'), 99);
					//add_action ('allow_password_reset', array($this, 'lostpassword_verify'), 10, 2); //lostpassword_post does not return wp_error( prior WP 4.4 )
					add_action('lostpassword_post', array($this, 'lostpassword_verify_44'));
				}
				
			if ( '1' == anr_get_option( 'reset_password' )) {
					add_action ('resetpass_form', array($this, 'form_field'), 99);
					add_action ('woocommerce_resetpassword_form', array($this, 'form_field'), 99);
					add_filter ('validate_password_reset', array($this, 'reset_password_verify'), 10, 2 );
				}
					
			if ( '1' == anr_get_option( 'comment' )) {
					if( ! is_user_logged_in() ) {
						add_action ('comment_form_after_fields', array($this, 'form_field'), 99);
					} else {
						add_filter ('comment_form_field_comment', array($this, 'comment_form_field'), 99 );
					}
					add_filter ('preprocess_comment', array($this, 'comment_verify') );
				}

			if ( function_exists( 'wpcf7_add_form_tag' ) ) {
				wpcf7_add_form_tag('anr_nocaptcha', array($this, 'wpcf7_form_field'), array( 'name-attr' => true ) );
				add_filter('wpcf7_validate_anr_nocaptcha', array($this, 'wpcf7_verify'), 10, 2);
			} elseif( function_exists( 'wpcf7_add_shortcode' ) ){
				wpcf7_add_shortcode('anr_nocaptcha', array($this, 'wpcf7_form_field'), true);
				add_filter('wpcf7_validate_anr_nocaptcha', array($this, 'wpcf7_verify'), 10, 2);
			}
				
			if ( '1' == anr_get_option( 'bb_new' )) {
					add_action ('bbp_theme_before_topic_form_submit_wrapper', array($this, 'form_field'), 99);
					add_action ('bbp_new_topic_pre_extras', array($this, 'bb_new_verify') );
				}
				
			if ( '1' == anr_get_option( 'bb_reply' )) {
					add_action ('bbp_theme_before_reply_form_submit_wrapper', array($this, 'form_field'), 99);
					add_action ('bbp_new_reply_pre_extras', array($this, 'bb_reply_verify'), 10, 2 );
				}
		}
		
	function total_captcha()
	{
		return self::$captcha_count;
	}
	
	function captcha_form_field()
	{
		self::$captcha_count++;
		$no_js		= anr_get_option( 'no_js' );
		$site_key 	= trim(anr_get_option( 'site_key' ));
		$number 	= $this->total_captcha();
		
		$field = '<div class="anr_captcha_field"><div id="anr_captcha_field_' . $number . '"></div></div>';
		
		if ( 1 == $no_js )
			{
			$field .='<noscript>
						  <div>
							<div style="width: 302px; height: 422px; position: relative;">
							  <div style="width: 302px; height: 422px; position: absolute;">
								<iframe src="https://www.google.com/recaptcha/api/fallback?k='. $site_key .'"
										frameborder="0" scrolling="no"
										style="width: 302px; height:422px; border-style: none;">
								</iframe>
							  </div>
							</div>
							<div style="width: 300px; height: 60px; border-style: none;
										   bottom: 12px; left: 25px; margin: 0px; padding: 0px; right: 25px;
										   background: #f9f9f9; border: 1px solid #c1c1c1; border-radius: 3px;">
							  <textarea id="g-recaptcha-response-'.$number.'" name="g-recaptcha-response"
										   class="g-recaptcha-response"
										   style="width: 250px; height: 40px; border: 1px solid #c1c1c1;
												  margin: 10px 25px; padding: 0px; resize: none;" ></textarea>
							</div>
						  </div>
						</noscript>';
				}
				
			return $field;
	}
	
	function footer_script()
	{
		$number = $this->total_captcha();
		static $included = false;
		
		if ( !$number )
			return;
			
		if ( $included )
			return;
		
		$included = true;
		
			$site_key 	= trim(anr_get_option( 'site_key' ));
			$theme		= anr_get_option( 'theme', 'light' );
			$size		= anr_get_option( 'size', 'normal' );
			$language	= trim(anr_get_option( 'language' ));
		
				$lang	= "";
				if ( $language )
					$lang = "&hl=$language";
		
		?>
	<script type="text/javascript">
	  var anr_onloadCallback = function() {
		 var anr_obj = {
			 'sitekey' : '<?php echo esc_js( $site_key ); ?>',
			 'size' : '<?php echo esc_js( $size ); ?>',
		 };
		 <?php if( 'invisible' == $size ){
			 wp_enqueue_script( 'jquery' ); ?>
			 anr_obj.badge = '<?php echo esc_js( anr_get_option( 'badge', 'bottomright' ) ); ?>';
		<?php } else { ?>
			anr_obj.theme = '<?php echo esc_js( $theme ); ?>';
		<?php } ?>
		
	  <?php for ( $num = 1; $num <= $number; $num++ ) { ?>
				var anr_captcha_<?php echo $num; ?>;
				
			<?php if( 'invisible' == $size ){ ?>
				var anr_form<?php echo $num; ?> = jQuery('#anr_captcha_field_<?php echo $num; ?>').closest('form')[0];
				anr_obj.callback = function(){ anr_form<?php echo $num; ?>.submit(); };
				anr_obj["expired-callback"] = function(){ grecaptcha.reset(anr_captcha_<?php echo $num; ?>); };
				
				anr_form<?php echo $num; ?>.onsubmit = function(evt){
					evt.preventDefault();
					//grecaptcha.reset(anr_captcha_<?php echo $num; ?>);
					grecaptcha.execute(anr_captcha_<?php echo $num; ?>);
				};
		   <?php } ?>
			
			anr_captcha_<?php echo $num; ?> = grecaptcha.render('anr_captcha_field_<?php echo $num; ?>', anr_obj );
		<?php } ?>
	  };
	</script>
	<script src="https://www.google.com/recaptcha/api.js?onload=anr_onloadCallback&render=explicit<?php echo esc_js( $lang ); ?>"
		async defer>
	</script>

		<?php 

	}
		
	
	function form_field()
		{
			$loggedin_hide 	= anr_get_option( 'loggedin_hide' ); 
			
			if ( is_user_logged_in() && $loggedin_hide )
				return;
				
			anr_captcha_form_field( true );
			
		}
		
	function post_id(){
		global $wpdb;
		static $post_id;
		
		if( ! absint( anr_get_option( 'failed_login_allow' ) ) ){
			return 0;
		}
		if( is_numeric( $post_id ) ){
			return $post_id;
		}
		$post_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_type = 'anr-post' LIMIT 1");
		
		if( ! $post_id ){
			$wpdb->insert( $wpdb->posts, array( 'post_type' => 'anr-post' ) );
			$post_id = $wpdb->insert_id;
		}
		$post_id = absint( $post_id );
		
		return $post_id;
	}
	
	function show_login_captcha(){
		global $wpdb;
		
		$show_captcha = true;
		$ip = $_SERVER['REMOTE_ADDR'];
		//filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE );
		$count = absint( anr_get_option( 'failed_login_allow' ) );
		$post_id = $this->post_id();
		
		if( $count && $post_id && filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ){
			$user_logins = $wpdb->get_col( $wpdb->prepare( "SELECT meta_value FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s", $post_id, md5( $ip ) ) );
			
			if( count( $user_logins ) < $count && count( array_unique( $user_logins ) ) <= 1 ){
				$show_captcha = false;
			}
		}

		return $show_captcha;
	}
	function login_form_field()
	{	
		if( $this->show_login_captcha() ){
			$this->form_field();
		}
	}
	
	function wc_form_field()
		{
			
			if( ! is_user_logged_in() && 'yes' == get_option( 'woocommerce_enable_signup_and_login_from_checkout', 'yes' ) && '1' == anr_get_option( 'registration' ) ){
				$this->form_field();
				
			} elseif( '1' == anr_get_option( 'wc_checkout' ) ){
				$this->form_field();
			}
			
		}
	
	function ms_form_field( $errors )

		{
			$loggedin_hide 	= anr_get_option( 'loggedin_hide' ); 

			if ( is_user_logged_in() && $loggedin_hide )
				return;

				if ( $errmsg = $errors->get_error_message('anr_error') ) {
					echo '<p class="error">' . $errmsg . '</p>';
				}

				anr_captcha_form_field( true );

		}
		
	function comment_form_field( $defaults )
		{
			$loggedin_hide 	= anr_get_option( 'loggedin_hide' ); 
			
			if ( is_user_logged_in() && $loggedin_hide )
				return $defaults;
				
				$defaults = $defaults . anr_captcha_form_field( false );
				return $defaults;
			
			
		}
		
	function verify()
		{
			
			$loggedin_hide 	= anr_get_option( 'loggedin_hide' ); 
			
			if ( is_user_logged_in() && $loggedin_hide )
				return true;
				
			return anr_verify_captcha();
			
		}
		
	function fepcf_verify ( $errors )
		{
			$error_message = str_replace(__('<strong>ERROR</strong>: ', 'advanced-nocaptcha-recaptcha'), '', anr_get_option( 'error_message' ));
			
			if ( ! $this->verify() ){	
				$errors->add('anr_error', $error_message);
			}
		}
		
	function login_verify ( $user, $username = '', $password = '' )
		{
			global $wpdb;
			
			$show_captcha = $this->show_login_captcha();
			
			if ( ! ( $user instanceof WP_User ) ){
				if( ! $show_captcha && $username && ( $post_id = $this->post_id() ) ){
					if( is_email( $username ) ){
						$user_data = get_user_by( 'email', $username );
						if( $user_data ){
							$username = $user_data->user_login;
						}
					}
					$wpdb->insert( $wpdb->postmeta, array( 'post_id' => $post_id, 'meta_key' => md5( $_SERVER['REMOTE_ADDR'] ), 'meta_value' => $username ), array( '%d', '%s', '%s' ) );
				}
				return $user;
			}
			if ( $show_captcha && ! $this->verify() ) {
				$error_message = anr_get_option( 'error_message' );
				return new WP_Error( 'anr_error', $error_message );
			}
			
			return $user;
		}
	
	function clear_data( $user_login, $user ){
		global $wpdb;
		
		if( $post_id = $this->post_id() ){
			$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE post_id = %d AND ( meta_key = %s OR meta_value = %s )", $post_id, md5( $_SERVER['REMOTE_ADDR'] ), $user_login ) );
		}
	}
		
	function registration_verify (  $errors, $sanitized_user_login, $user_email )
		{
			if ( ! $this->verify() ) {
				$error_message = anr_get_option( 'error_message' );
				$errors->add( 'anr_error', $error_message );
			}
			
			return $errors;
		}
	
	function ms_form_field_verify( $result )

		{
			if ( ! $this->verify() ) {
				$error_message = str_replace(__('<strong>ERROR</strong>: ', 'advanced-nocaptcha-recaptcha'), '', anr_get_option( 'error_message' ));
				$result['errors']->add( 'anr_error', $error_message );
			}

			return $result;
		}
		
	function lostpassword_verify( $result, $user_id )
		{
			if ( ! $this->verify() ) {
				$error_message = anr_get_option( 'error_message' );
				return new WP_Error( 'anr_error', $error_message );
			}
			
			return $result;
		}
		
	function lostpassword_verify_44( $errors )
		{
			if ( ! $this->verify() ) {
				$error_message = anr_get_option( 'error_message' );
				$errors->add('anr_error', $error_message);
			}
		}
		
		
	function reset_password_verify( $errors, $user )
		{
			
			if ( ! $this->verify() ) {
				$error_message = anr_get_option( 'error_message' );
				$errors->add('anr_error', $error_message);
			}
		}
		
	function comment_verify( $commentdata )
		{
			
			if ( ! $this->verify() ) {
				$error_message = anr_get_option( 'error_message' );
				wp_die( $error_message, 200 );
			}
			
			return $commentdata;
		}
		
	function wpcf7_form_field( $tag )
		{
			$loggedin_hide 	= anr_get_option( 'loggedin_hide' ); 
			
			if ( is_user_logged_in() && $loggedin_hide )
				return '';
				
			return anr_captcha_form_field( false ). sprintf( '<span class="wpcf7-form-control-wrap %s"></span>', $tag->name );
		}
		
	function wpcf7_verify( $result, $tag  )
		{
			if ( ! $this->verify() ) {
				$error_message = str_replace(__('<strong>ERROR</strong>: ', 'advanced-nocaptcha-recaptcha'), '', anr_get_option( 'error_message' ));
				$result->invalidate( $tag, $error_message );
			}

		return $result;
	}
		
	function bb_new_verify( $forum_id )
		{
			
			if ( ! $this->verify() ) {
				$error_message = anr_get_option( 'error_message' );
				bbp_add_error('anr_error', $error_message);
			}
		}
		
	function bb_reply_verify( $topic_id, $forum_id )
		{
			
			if ( ! $this->verify() ) {
				$error_message = anr_get_option( 'error_message' );
				bbp_add_error('anr_error', $error_message);
			}
		}
	
	function wc_checkout_verify()
		{
			
			if( ! is_user_logged_in() && 'yes' == get_option( 'woocommerce_enable_signup_and_login_from_checkout', 'yes' ) && '1' == anr_get_option( 'registration' ) ){
				// verification done during ragistration, So no need any more verification
				
			} elseif( ! $this->verify() ){

				$error_message = anr_get_option( 'error_message' );
				wc_add_notice( $error_message, 'error' );
			}
		}

	
  } //END CLASS
} //ENDIF

add_action('init', array(anr_captcha_class::init(), 'actions_filters'));

