<?php
/**
 * Plugin Name: Dominate Checkout Suite (Open-Source)
 * Plugin URI: https://www.dominate.co/woocommerce
 * Description: Checkout Suite Connector
 * Author: Dominate
 * Version: 2.1.1
 * Author URI: https://www.dominate.co
 * WC requires at least: 2.6.0
 * WC tested up to: 5.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'plugins_loaded', 'iwd_opc_init' );

if ( ! function_exists( 'iwd_opc_init' ) ) {
	/**
	 * Initializer.
	 *
	 * @since 1.0.0
	 * @version 2.1.0
	 */
	function iwd_opc_init() {
		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'woocommerce_iwd_missing_wc_notice' );
		} else {
			/**
			 * Define plugin minimums and constants.
			 */
			define( 'IWD_WC_OPC_VERSION', '2.1.0' );
			define( 'IWD_WC_OPC_FILE', __FILE__ );
			define( 'IWD_WC_OPC_PATH', plugin_dir_path( __FILE__ ) );
			define( 'IWD_WC_OPC_URL', plugins_url( '/', __FILE__ ) );
			define( 'IWD_WC_OPC_ADMIN_ASSETS_URL', IWD_WC_OPC_URL . 'admin/assets/' );
			define( 'IWD_WC_OPC_PUBLIC_ASSETS_URL', IWD_WC_OPC_URL . 'public/assets/' );
			define( 'IWD_WC_OPC_ADMIN_TEMPLATES_PATH', IWD_WC_OPC_PATH . 'admin/templates/' );
			define( 'IWD_WC_OPC_PUBLIC_TEMPLATES_PATH', IWD_WC_OPC_PATH . 'public/templates/' );
			define( 'IWD_WC_OPC_PUBLIC_WC_TEMPLATE_PATH', IWD_WC_OPC_PATH . 'public/templates/woocommerce/' );
			define( 'IWD_AGENCY_PP_BN_CODE', 'InteriorWebDesignWoo_SP' );
			define( 'IWD_AGENCY_SITE_URL', 'https://www.iwdagency.com/' );
			define( 'IWD_AGENCY_WC_OPC_PROD_PAGE', 'https://www.iwdagency.com/extensions/woocommerce-one-page-checkout.html' );

			/**
			 * Instance main plugin class.
			 */
			IWD_OPC();
		}
	}
}

if ( ! function_exists( 'IWD_OPC' ) ) {
	/**
	 * Return the main instance of IWD_OPC class.
	 *
	 * @return IWD_OPC
	 * @since 1.0.0
	 */
	function IWD_OPC() {
		// Load required classes and functions.
		require_once( IWD_WC_OPC_PATH . 'includes/class-iwd-opc.php' );

		return IWD_OPC::instance();
	}
}

/**
 * Print WooCommerce fallback notice.
 *
 * @since 1.0.0
 */
function woocommerce_iwd_missing_wc_notice() {
	echo '<div class="error"><p>' . sprintf( __( 'IWD WooCommerce One Page Checkout requires WooCommerce to be installed and active. You can download %s here.', 'iwd-woocommerce-checkout-suite' ), '<a href="https://woocommerce.com/woocommerce/" target="_blank">WooCommerce</a>' ) . '</p></div>';
}