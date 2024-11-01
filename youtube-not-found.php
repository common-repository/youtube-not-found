<?php
/*
Plugin Name: Youtube Not Found
Plugin URI: http://www.mindstien.com
Description: Sharing youtube videos on your site is easy but keeping them upto date is really not practical job. This plugin will help you find invalid youtube videos (Videos you have posted months or years ago but deleted/removed from youtube) and will automatically email to admin of the site.
Version: 2.1
Author: Chirag Gadara (Mindstien Technologies)
Author URI: http://www.mindstien.com
*/
//error_reporting(E_ALL & ~E_NOTICE);
global $youtube_debug_mode;
$youtube_debug_mode = false; // do not enable this, this is for plugin developers only....
require_once 'classes/sunrise.class.php';



register_activation_hook( __FILE__, 'ytnf_activated' );
register_deactivation_hook(__FILE__, 'ytnf_de_activated');
add_action('ytnf_daily_event','ytnf_daily_event_func');

function ytnf_de_activated()
{
	wp_clear_scheduled_hook('ytnf_daily_event');
}

function ytnf_activated()
{
	$to = get_bloginfo('admin_email');
	$subject = "Youtube Not Found Plugin Activated on ".get_bloginfo('name');
	$message = "<p>Thankyou for using me. Now I will keep testing your site every few days and will inform you as soon as invalid youtube video is found.</p> <p>Thanks<br>Youtube Not Found Plugin.</p>";
	add_filter( 'wp_mail_content_type', 'set_html_content_type' );
	wp_mail( $to, $subject, $message, $headers, $attachments );
	remove_filter( 'wp_mail_content_type', 'set_html_content_type' );			
	
	wp_schedule_event( time(), 'daily', 'ytnf_daily_event');
}

$ytnf = new Sunrise_Plugin_Framework;

	$ytnf->add_settings_page( array(
		'parent' => 'options-general.php',
		'page_title' => $ytnf->name,
		'menu_title' => $ytnf->name,
		'capability' => 'manage_options',
		'settings_link' => true
	) );

	// Include plugin actions
	require_once 'inc/core.php';
	//require_once 'inc/shortcodes.php';
	//require_once 'inc/meta_boxes.php';

	// Make plugin meta translatable
	__( 'Author Name', $ytnf->textdomain );
	__( 'Vladimir Anokhin', $ytnf->textdomain );
	__( 'Plugin description', $ytnf->textdomain );

	// Destroy plugin instance
	unset( $ytnf );
?>