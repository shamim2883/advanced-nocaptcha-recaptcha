<?php
/*
Plugin Name: Advanced noCaptcha & invisible Captcha
Plugin URI: https://www.shamimsplugins.com/contact-us/
Description: Show noCaptcha or invisible captcha in Comment Form (after Comment textarea before submit button), bbPress, Login, Register, Lost Password, Reset Password. Also can implement in any other form easily.
Version: 2.7
Author: Shamim
Author URI: https://www.shamimsplugins.com/contact-us/
Text Domain: advanced-nocaptcha-recaptcha
License: GPLv2 or later
*/
//DEFINE
define('ANR_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
define('ANR_PLUGIN_URL', plugins_url( '/', __FILE__ ) );

require_once('functions.php');


	//ADD ACTIONS
	
	add_action('after_setup_theme', 'anr_include_require_files');
	add_action('plugins_loaded', 'anr_translation');
	//add_action('wp_enqueue_scripts', 'anr_enqueue_scripts');
	add_action('login_enqueue_scripts', 'anr_login_enqueue_scripts');
	
