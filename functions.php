<?php


function anr_get_option( $option, $default = '', $section = 'anr_admin_options' ) {
	
    if ( is_multisite() ) {
		$same_settings = apply_filters( 'anr_same_settings_for_all_sites', false );
	} else {
		$same_settings = false;
	}
	if ( $same_settings ) {
		$options = get_site_option( $section );
	} else {
		$options = get_option( $section );
	}

    if ( isset( $options[$option] ) ) {
        return $options[$option];
    }

    return $default;
}

function anr_update_option( $options, $value = '', $section = 'anr_admin_options' ) {
	
	if( $options && ! is_array( $options ) ){
		$options = array(
			$options => $value,
		);
	}
	if( ! is_array( $options ) )
	return false;
	
	if ( is_multisite() ) {
		$same_settings = apply_filters( 'anr_same_settings_for_all_sites', false );
	} else {
		$same_settings = false;
	}
	if ( $same_settings ) {
		update_site_option( $section, wp_parse_args( $options, get_site_option( $section ) ) );
	} else {
		update_option( $section, wp_parse_args( $options, get_option( $section ) ) );
	}

    return true;
}
	
function anr_translation()
	{
	//SETUP TEXT DOMAIN FOR TRANSLATIONS
	load_plugin_textdomain('advanced-nocaptcha-recaptcha', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

//Not used since version 2.1	
function anr_enqueue_scripts()
    {
		$language	= trim(anr_get_option( 'language' ));
		
		$lang	= "";
		if ( $language )
			$lang = "?hl=$language";
			
		wp_register_script( 'anr-google-recaptcha-script', "https://www.google.com/recaptcha/api.js$lang", array(), '2.0', true );
		
	}
	
function anr_login_enqueue_scripts()
    {
		$remove_css	= trim(anr_get_option( 'remove_css' ));
		
		if ( !$remove_css )
		wp_enqueue_style( 'anr-login-style', ANR_PLUGIN_URL . 'style/style.css' );
		
	}
	
function anr_include_require_files() 
	{
	if ( is_admin() ) 
		{
			$fep_files = array(
							'admin' => 'admin/anr-admin-class.php'
							);
										
		} else {
			$fep_files = array(
							'main' => 'anr-captcha-class.php'
							);
				}
					
	$fep_files = apply_filters('anr_include_files', $fep_files );
	
	foreach ( $fep_files as $fep_file ) {
	require_once ( $fep_file );
		}
	}
add_action('wp_footer', 'anr_wp_footer');
add_action('login_footer', 'anr_wp_footer');

function anr_wp_footer()
{
	anr_captcha_class::init()->footer_script();
}

add_action( 'anr_captcha_form_field', function(){ anr_captcha_form_field( true ); } );
add_shortcode( 'anr-captcha', 'anr_captcha_form_field' );

function anr_captcha_form_field( $echo = false )
	{
		
		if ( $echo ) {
			echo anr_captcha_class::init()->captcha_form_field();
		} else {
			return anr_captcha_class::init()->captcha_form_field();
		}
		
	}
	
function anr_verify_captcha( $response = false )
	{
		$secre_key 	= trim(anr_get_option( 'secret_key' )); 
		$remoteip = $_SERVER["REMOTE_ADDR"];
		
		if ( !$secre_key ) //if $secre_key is not set
			return true;
		
		if( false === $response )
			$response = isset( $_POST['g-recaptcha-response'] ) ? $_POST['g-recaptcha-response'] : '';
		
		if ( !$response || !$remoteip )
			return false;
		
		$url = "https://www.google.com/recaptcha/api/siteverify";

		// make a POST request to the Google reCAPTCHA Server
		$request = wp_remote_post( $url, array( 'timeout' => 10, 'body' => array( 'secret' => $secre_key, 'response' => $response, 'remoteip' => $remoteip ) ) );

		if ( is_wp_error( $request ) )
   			return false;

		// get the request response body
		$request_body = wp_remote_retrieve_body( $request );
			if ( !$request_body )
				return false;

		$result = json_decode( $request_body, true );
		 if ( isset($result['success']) && true == $result['success'] )
		 	return true;

		return false;
	}
	
add_filter('shake_error_codes', 'anr_add_shake_error_codes' );

function anr_add_shake_error_codes( $shake_error_codes )
	{
		$shake_error_codes[] = 'anr_error';
		
		return $shake_error_codes;
	}
	
