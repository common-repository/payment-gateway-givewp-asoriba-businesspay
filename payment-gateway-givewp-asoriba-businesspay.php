<?php
/**
 * Plugin Name: Payment Gateway GiveWP Asoriba BusinessPay
 * Plugin URI: https://www.c-metric.com/
 * Description: Asoriba BusinessPay Payment gateway Add-on for GiveWP, BusinessPay is a Ghanaian Payment Gateway Add-on for the GiveWP plugin.
 * Version:     1.2
 * Author:      cmetric
 * Author URI:  https://www.c-metric.com/
 * Text Domain: give-businesspay
 *
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// include all required files here
require_once('class-businesspay-givewp.php');
require_once('businesspay-givewp-template-installation.php');

add_action( 'plugins_loaded', 'give_load_businesspay_gateway' );
register_activation_hook(  __FILE__ , 'businesspay_installer' );
//Filter to call the custom template When businesspay thank you page call
 add_filter( 'page_template', 'wp_page_template_businesspay' );