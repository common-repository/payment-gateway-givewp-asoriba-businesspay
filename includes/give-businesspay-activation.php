<?php
/**
 * Give businesspay Pay Activation Banner
 *
 * Includes and initializes Give activation banner class.
 */
function give_businesspay_activation_banner() {

	// Check for if give plugin activate or not.
	$is_give_active = defined( 'GIVE_PLUGIN_BASENAME' ) ? is_plugin_active( GIVE_PLUGIN_BASENAME ) : false;

	//Check to see if Give is activated, if it isn't deactivate and show a banner
	if ( current_user_can( 'activate_plugins' ) && ! $is_give_active ) {

		add_action( 'admin_notices', 'give_businesspay_activation_notice' );

		//Don't let this plugin activate
		deactivate_plugins( GIVEPP_BUSINESSPAY_BASENAME );

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		return false;

	}

	//Check minimum Give version.
	if ( defined( 'GIVE_VERSION' ) && version_compare( GIVE_VERSION, GIVEPP_MIN_GIVE_VERSION, '<' ) ) {

		add_action( 'admin_notices', 'give_businesspay_min_version_notice' );

		//Don't let this plugin activate.
		deactivate_plugins( GIVEPP_BUSINESSPAY_BASENAME );

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		return false;

	}

	// Check for activation banner inclusion.
	if (
		! class_exists( 'Give_Addon_Activation_Banner' )
		&& file_exists( GIVE_PLUGIN_DIR . 'includes/admin/class-addon-activation-banner.php' )
	) {
		include GIVE_PLUGIN_DIR . 'includes/admin/class-addon-activation-banner.php';
	}

	// Initialize activation welcome banner.
	if ( class_exists( 'Give_Addon_Activation_Banner' ) ) {

		//Only runs on admin
		$args = array(
			'file'              => __FILE__,
			'name'              => __( 'businesspay Pay Gateway', 'give-businesspay' ),
			'version'           => GIVEPP_VERSION,
			'settings_url'      => admin_url( 'edit.php?post_type=give_forms&page=give-settings&tab=gateways' ),
			'documentation_url' => '#',
			'support_url'       => 'https://givewp.com/support/',
			'testing'           => false //Never leave true!
		);

		new Give_Addon_Activation_Banner( $args );
	}

	return false;

}

add_action( 'admin_init', 'give_businesspay_activation_banner' );

/**
 * Notice for No Core Activation
 *
 */
function give_businesspay_activation_notice() {
	echo '<div class="error"><p>' . __( '<strong>Activation Error:</strong> You must have the <a href="https://givewp.com/" target="_blank">Give</a> plugin installed and activated for the businesspay Pay add-on to activate.', 'give-businesspay' ) . '</p></div>';
}


/**
 * Notice for no core activation.
 *
 */
function give_businesspay_min_version_notice() {
	echo '<div class="error"><p>' . sprintf( __( '<strong>Activation Error:</strong> You must have <a href="%s" target="_blank">Give</a> version %s+ for the businesspay Pay add-on to activate.', 'give-businesspay' ), 'https://givewp.com', GIVEPP_MIN_GIVE_VERSION ) . '</p></div>';
}


/**
 * Plugins row action links
 *
 * @return array An array of updated action links.
 */
function givepp_plugin_action_links_ssl( $actions ) {
	$new_actions = array(
		'settings' => sprintf(
			'<a href="%1$s">%2$s</a>',
			admin_url( 'edit.php?post_type=give_forms&page=give-settings&tab=gateways' ),
			esc_html__( 'Settings', 'give-sslcommerz' )
		),
	);

	return array_merge( $new_actions, $actions );
}

add_filter( 'plugin_action_links_' . GIVEPP_BUSINESSPAY_BASENAME, 'givepp_plugin_action_links_ssl' );


/**
 * Plugin row meta links
 * 
 * @param string $plugin_file Path to the plugin file, relative to the plugins directory.
 *
 * @return array
 */
function givepp_businesspay_plugin_row_meta_ssl( $plugin_meta, $plugin_file ) {

	if ( $plugin_file != GIVEPP_BUSINESSPAY_BASENAME ) {
		return $plugin_meta;
	}

	$new_meta_links = array(
		sprintf(
			'<a href="%1$s" target="_blank">%2$s</a>',
			esc_url( add_query_arg( array(
					'utm_source'   => 'plugins-page',
					'utm_medium'   => 'plugin-row',
					'utm_campaign' => 'admin',
				), '' )
			),
			__( 'Documentation', 'give-businesspay' )
		),
		sprintf(
			'<a href="%1$s" target="_blank">%2$s</a>',
			esc_url( add_query_arg( array(
					'utm_source'   => 'plugins-page',
					'utm_medium'   => 'plugin-row',
					'utm_campaign' => 'admin',
				), 'https://givewp.com/addons/' )
			),
			__( 'Add-ons', 'give-businesspay' )
		),
	);

	return array_merge( $plugin_meta, $new_meta_links );
}

add_filter( 'plugin_row_meta', 'givepp_businesspay_plugin_row_meta_ssl', 10, 2 );