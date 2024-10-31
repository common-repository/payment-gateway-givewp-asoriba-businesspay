<?php
/**
 * Class Give_BusinessPay_Gateway
 */
class Give_BusinessPay_Gateway {

	private static $instance;

	/**
	 * Get active object instance
	 *
	 * @since  1.0
	 * @access public
	 * @static
	 *
	 * @return object
	 */
	public static function get_instance() {

		if ( ! self::$instance ) {
			self::$instance = new Give_BusinessPay_Gateway();
		}

		return self::$instance;
	}

	/**
	 * Give_BusinessPay_Gateway constructor.
	 *	
	 */
	public function __construct() {
		$this->setup_constants();
		$this->includes();
		
	}

	/**
	 * Setup plugin constants.	 
	 * @return void
	 */
	private function setup_constants() {

		if ( ! defined( 'GIVEPP_VERSION' ) ) {
			define( 'GIVEPP_VERSION', '1.1.4' );
		}
		if ( ! defined( 'GIVEPP_MIN_GIVE_VERSION' ) ) {
			define( 'GIVEPP_MIN_GIVE_VERSION', '1.8.12' );
		}
		if ( ! defined( 'GIVEPP_PRODUCT_NAME' ) ) {
			define( 'GIVEPP_PRODUCT_NAME', 'Businesspay Gateway' );
		}
		if ( ! defined( 'GIVEPP_BUSINESSPAY_PLUGIN_FILE' ) ) {
			define( 'GIVEPP_BUSINESSPAY_PLUGIN_FILE', __FILE__ );
		}
		if ( ! defined( 'GIVEPP_BUSINESSPAY_PLUGIN_DIR_SSL' ) ) {
			define( 'GIVEPP_BUSINESSPAY_PLUGIN_DIR_SSL', dirname( __FILE__ ) );
		}
		if ( ! defined( 'GIVEPP_BUSINESSPAY_BASENAME' ) ) {
			define( 'GIVEPP_BUSINESSPAY_BASENAME', plugin_basename( __FILE__ ) );
		}
		if ( ! defined( 'GIVEPP_BUSINESSPAY_STORE_API_URL' ) ) {
			define( 'GIVEPP_BUSINESSPAY_STORE_API_URL', 'https://givewp.com' );
		}

	}

	/**
	 * Include required files	 
	 */
	private function includes() {

		require_once GIVEPP_BUSINESSPAY_PLUGIN_DIR_SSL . '/includes/give-businesspay-activation.php';
				
		if ( ! class_exists( 'Give' ) ) {
			return false;
		}
		 require_once GIVEPP_BUSINESSPAY_PLUGIN_DIR_SSL . '/includes/class-give-businesspay.php';
		$this->init();

	}

	/**
	 * Initialize Give businesspay	 
	 */
	private function init() {

		add_action( 'init', array( $this, 'load_textdomain' ) );
		new Give_businesspay_php();	
		return true;
	}

	/**
	 * Load the text domain.	
	 */
	public function load_textdomain() {

		// Set filter for plugin's languages directory
		$lang_dir = dirname( GIVEPP_BUSINESSPAY_BASENAME ) . '/languages/';

		// Traditional WordPress plugin locale filter
		$locale = apply_filters( 'plugin_locale', get_locale(), 'give-businesspay' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'give-businesspay', $locale );

		// Setup paths to current locale file
		$mofile_local  = $lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/givepp/' . $mofile;

		if ( file_exists( $mofile_global ) ) {		
			load_textdomain( 'give-businesspay', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {			
			load_textdomain( 'give-businesspay', $mofile_local );
		} else {
			load_plugin_textdomain( 'give-businesspay', false, $lang_dir );
		}
	}
}
/**
 * Get it Started
 */
function give_load_businesspay_gateway() {
	$GLOBALS['give_businesspay_gateway'] = new Give_BusinessPay_Gateway();	
}
?>