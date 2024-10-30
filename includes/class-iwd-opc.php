<?php
/**
 * Main Plugin Class.
 *
 * @since      1.0.0
 * @package    IWD_OPC
 * @subpackage IWD_OPC/includes
 */

if ( ! defined( 'IWD_WC_OPC_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'IWD_OPC' ) ) {
	/**
	 * Class IWD_OPC
	 */
	class IWD_OPC {
		/**
		 * Plugin version.
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $version = IWD_WC_OPC_VERSION;

		/**
		 * Main Admin Instance.
		 *
		 * @var IWD_OPC_Admin
		 * @since 1.0.0
		 */
		public $admin = null;

		/**
		 * Main Frontend Instance.
		 *
		 * @var IWD_OPC_Frontend
		 * @since 1.0.0
		 */
		public $frontend = null;

		/**
		 * Check if the plugin is enabled.
		 *
		 * @var bool|mixed|void
		 * @since 1.0.0
		 */
		public $is_plugin_enabled = false;

		/**
		 * Main Instance.
		 *
		 * @var IWD_OPC
		 * @since 1.0.0
		 */
		protected static $_instance = null;
        /**
         * @var Iwd_Track_Order
         */
        private $track;

        /**
		 * IWD_OPC constructor.
		 */
		public function __construct() {

			$this->is_plugin_enabled = get_option( 'iwd_wc_opc_enabled' );

			/*
			 * Require Main Plugin Files.
			 */
			$require = apply_filters( 'iwd_opc_require_class',
				array(
					'admin'    => array(
						'admin/class-iwd-opc-backend.php',
					),
					'frontend' => array(
						'public/class-iwd-opc-frontend.php'
					),
				)
			);
			$this->_require( $require );
            $this->require_track_system();
			// Initiate Plugin
			add_action( 'init', array( $this, 'init' ) );

			// Add IWD Agency PayPal BN Code
			add_filter( 'woocommerce_paypal_express_checkout_request_body', array($this,'iwd_paypal_express_checkout_request_body_additional_parameters'));
			add_filter( 'woocommerce_paypal_args', array($this,'iwd_paypal_standard_additional_parameters'));
		}

		/**
		 * @return IWD_OPC
		 * @since 1.0.0
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * @param $main_classes
		 *
		 * @since 1.0.0
		 */
		protected function _require( $main_classes ) {
			foreach ( $main_classes as $section => $classes ) {
				foreach ( $classes as $class ) {
					if ( ( 'frontend' == $section && ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) || ( 'admin' == $section && is_admin() ) && file_exists( IWD_WC_OPC_PATH . $class ) ) {
						require_once( IWD_WC_OPC_PATH . $class );
					}
				}
			}
		}

		/**
		 * Initiate Frontend and Backend Logic.
		 *
		 * @since 1.0.0
		 */
		public function init() {
			if ( is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['context'] ) && $_REQUEST['context'] == 'frontend' ) ) {
				$this->admin = new IWD_OPC_Admin();
			} elseif ( $this->is_plugin_enabled ) {
				$this->frontend = new IWD_OPC_Frontend();
			}
            $this->track = new Iwd_Track_Order();
		}

		/**
		 * Overwrite PayPal Button Source Parameter value.
		 *
		 * @param $params
		 *
		 * @return mixed
		 * @since 1.0.0
		 */
		function iwd_paypal_express_checkout_request_body_additional_parameters( $params ) {
			$params['BUTTONSOURCE'] = IWD_AGENCY_PP_BN_CODE;

			return $params;
		}

		/**
		 * Overwrite PayPal BN code.
		 *
		 * @param $paypal_args
		 *
		 * @return mixed
		 * @since 1.0.0
		 */
		public function iwd_paypal_standard_additional_parameters( $paypal_args ) {
			$paypal_args['bn'] = IWD_AGENCY_PP_BN_CODE;

			return $paypal_args;
		}

        public function require_track_system(){
            require_once( IWD_WC_OPC_PATH . 'includes/order/class-track-order.php' );
        }
	}
}