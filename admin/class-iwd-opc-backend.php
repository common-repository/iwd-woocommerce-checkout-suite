<?php
/**
 * Admin Plugin Class.
 *
 * @since      1.0.0
 * @package    IWD_OPC
 * @subpackage IWD_OPC/admin
 */

if ( ! defined( 'IWD_WC_OPC_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'IWD_OPC_Admin' ) ) {
	/**
	 * Class IWD_OPC_Admin
	 */
	class IWD_OPC_Admin {
		/**
		 * Admin Panel.
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_panel = 'iwd_wc_panel';

		/**
		 * Admin Panel Page.
		 *
		 * @var string
		 * @since 1.0.0
		 */
		protected $_panel_page = 'iwd_wc_opc_panel';

		/**
		 * IWD_OPC_Admin constructor.
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
			add_action( 'admin_init', array( $this, 'options_update' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			add_filter( 'plugin_action_links_' . plugin_basename( IWD_WC_OPC_PATH . '/' . basename( IWD_WC_OPC_FILE ) ), array(
				$this,
				'action_links'
			) );
		}

		/**
		 * Add Admin Action Links.
		 *
		 * @param $links
		 *
		 * @return array
		 * @since 1.0.0
		 */
		public function action_links( $links ) {
			$links[] = sprintf( '<a href="%s">%s</a>', admin_url( "admin.php?page={$this->_panel_page}" ), _x( 'Settings', 'Action links', 'iwd-woocommerce-product-vendors' ) );

			return $links;
		}

		/**
		 * Add Admin Menu Page & Subpage.
		 *
		 * @since 1.0.0
		 */
		public function add_admin_menu() {

			add_menu_page( 'IWD Dominate - Checkout Suite',
				'IWD Dominate',
				'manage_options',
				$this->_panel,
				array( $this, 'create_admin_page' ),
				IWD_WC_OPC_ADMIN_ASSETS_URL . '/images/iwd-icon.png',
				62.32 );

			add_submenu_page(
				$this->_panel,
				'IWD Dominate - Checkout Suite',
				'Checkout Suite',
				'manage_options',
				$this->_panel_page,
				array( $this, 'create_admin_page' )
			);

			$this->remove_duplicated_submenu();
		}

		/**
		 * Create Admin Page with Plugin Settings.
		 *
		 * @since 1.0.0
		 */
		public function create_admin_page() {
			include( IWD_WC_OPC_ADMIN_TEMPLATES_PATH . '/settings-options.php' );
		}

		/**
		 * Remove duplicated submenu links.
		 *
		 * @since 1.0.0
		 */
		public function remove_duplicated_submenu() {
			remove_submenu_page( 'iwd_wc_panel', 'iwd_wc_panel' );
		}

		/**
		 * Update Plugin Options.
		 *
		 * @since 1.0.0
		 */
		public function options_update() {
			register_setting( 'iwd_wc_opc_settings', 'iwd_wc_opc_enabled' );
			register_setting( 'iwd_wc_opc_settings', 'iwd_wc_opc_login_form_enabled' );
			register_setting( 'iwd_wc_opc_settings', 'iwd_wc_opc_order_notes_field_enabled' );
			register_setting( 'iwd_wc_opc_settings', 'iwd_wc_opc_discount_form_enabled' );

			/* Design configuration */
			register_setting( 'iwd_wc_opc_settings', 'iwd_wc_opc_desktop_design' );
			register_setting( 'iwd_wc_opc_settings', 'iwd_wc_opc_tablet_design' );
			register_setting( 'iwd_wc_opc_settings', 'iwd_wc_opc_mobile_design' );
			register_setting( 'iwd_wc_opc_settings', 'iwd_wc_opc_login_before_checkout' );
		}

		/**
		 * Register and enqueue styles and scripts for Admin Panel.
		 *
		 * @since 1.0
		 * @return void
		 */
		public function enqueue_scripts() {
			// Load scripts and styles only on plugin page.
			if ( $this->_panel_page != 'iwd_wc_opc_panel' ) {
				return;
			}
			// Add Admin Page Styles.
			wp_enqueue_style( 'iwd-opc-admin-css', IWD_WC_OPC_ADMIN_ASSETS_URL . 'css/iwd-opc-admin.css' );
		}

		/**
		 * Checkout design variations list.
		 *
		 * @param $type
		 * @since 2.0
		 * @return string
		 */
		public static function iwd_wc_opc_designs_list( $type ) {
			$designs = [
				[
					'label' => 'Onepage design',
					'value' => 'onepage'
				],
				[
					'label' => 'Multistep design',
					'value' => 'multistep'
				],
			];

			$options = "";

			foreach ( $designs as $design ) {
				$options .= '<option value="' . $design['value'] . '" '
				            . selected( $design['value'], get_option( $type ), false ) . '>'
				            . $design['label']
				            . '</option>';
			}

			return $options;
		}
	}
}