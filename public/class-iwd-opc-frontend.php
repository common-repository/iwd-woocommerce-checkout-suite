<?php
/**
 * Frontend Plugin Class.
 *
 * @since      1.0.0
 * @version    2.1.0
 * @package    IWD_OPC
 * @subpackage IWD_OPC/public
 */

if ( ! defined( 'IWD_WC_OPC_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'IWD_OPC_Frontend' ) ) {
	/**
	 * Class IWD_OPC_Frontend.
	 */
	class IWD_OPC_Frontend {
		/**
		 * IWD_OPC_Frontend constructor.
		 */
		public function __construct() {
			// Load Plugin Libs
			include_once( 'lib/iwd-wc-opc-ajax.php' );
			include_once( 'lib/iwd-wc-cart-functions.php' );
			include_once( 'lib/iwd-wc-template-functions.php' );

			// Storefront WooCommerce Theme compatibility
			if ( class_exists( 'Storefront_WooCommerce' ) ) {
				include_once( IWD_WC_OPC_PATH . 'includes/compatibility/storefront.php' );
			}

			// Load Plugin Scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );
			add_action( 'woocommerce_checkout_update_order_review', array(
				$this,
				'action_woocommerce_checkout_update_order_review'
			), 10, 2 );

			// Rewrite Checkout Templates
			add_filter( 'woocommerce_locate_template', array( $this, 'intercept_wc_template' ), 10, 3 );

			if(self::version_check('3.5.1')) {
				// Change billing fields order
				add_filter('woocommerce_billing_fields', array( $this, 'billing_fields_order' ), 10, 1);

				// Change shipping fields order
				add_filter('woocommerce_shipping_fields', array( $this, 'shipping_fields_order' ), 10, 1);
			} else {
				// Change billing fields order
				add_filter('woocommerce_checkout_fields', array( $this, 'fields_order' ), 10, 1);

				/*// Change shipping fields order
				add_filter('woocommerce_checkout_fields', array( $this, 'shipping_fields_order' ), 10, 1);*/
			}

			remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
			remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10 );
			remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );

			add_action( 'iwd_woocommerce_checkout_payment', 'woocommerce_checkout_payment', 10 );
		}

		/**
		 * Register and enqueue styles and scripts for Admin Panel.
		 *
		 * @since 1.0
		 * @return void
		 */
		public function enqueue_scripts() {
			// Load scripts and styles only on Checkout & Cart pages.
			if ( ! is_checkout() && ! is_cart() ) {
				return;
			}

			// Add Bootstrap 3
			wp_register_style( 'iwd-wc-opc-bootstrap', IWD_WC_OPC_PUBLIC_ASSETS_URL . 'css/lib/bootstrap.min.css');
			wp_enqueue_style( 'iwd-wc-opc-bootstrap' );

			// Add IWD OPC Global Styles
			wp_register_style( 'iwd-wc-opc-redesign', IWD_WC_OPC_PUBLIC_ASSETS_URL . 'css/iwd-wc-opc-redesign.css', array());
			wp_enqueue_style( 'iwd-wc-opc-redesign' );

			// Add IWD OPC Responsive Styles
			wp_register_style( 'iwd-wc-opc-responsive', IWD_WC_OPC_PUBLIC_ASSETS_URL . 'css/iwd-wc-opc-responsive.css', array());
			wp_enqueue_style( 'iwd-wc-opc-responsive' );

			// Load scripts and styles only on Checkout page.
			if ( ! is_checkout() ) {
				return;
			}

			// Add IWD OPC Header Styles.
			wp_register_style( 'iwd-wc-opc-layout', IWD_WC_OPC_PUBLIC_ASSETS_URL . 'css/iwd-wc-opc-layout.css', array(), IWD_WC_OPC_VERSION );
			wp_enqueue_style( 'iwd-wc-opc-layout' );

			// Add FontAwesome Styles.
			wp_enqueue_style( 'font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css' );
			wp_enqueue_style( 'font-awesome' );

			/* Load Scripts */
			wp_register_script( 'wc-sticky-sidebar', IWD_WC_OPC_PUBLIC_ASSETS_URL . 'js/jquery.sticky-sidebar.js', array( 'jquery', 'woocommerce', 'wc-country-select', 'wc-address-i18n' ), IWD_WC_OPC_VERSION, true );
			wp_enqueue_script( 'wc-sticky-sidebar' );

			/* Add interface for using cookies */
			wp_deregister_script( 'wc-checkout-js-cookie' );
			wp_register_script( 'wc-checkout-js-cookie', IWD_WC_OPC_PUBLIC_ASSETS_URL . 'js/jquery.cookie.js', array( 'jquery', 'woocommerce', 'wc-country-select', 'wc-address-i18n', 'wc-sticky-sidebar' ), IWD_WC_OPC_VERSION, true );
			wp_enqueue_script( 'wc-checkout-js-cookie' );

			/* Main script */
			wp_deregister_script( 'wc-checkout' );
			wp_register_script( 'wc-checkout', IWD_WC_OPC_PUBLIC_ASSETS_URL . 'js/iwd-wc-opc.js', array( 'jquery', 'woocommerce', 'wc-country-select', 'wc-address-i18n', 'wc-sticky-sidebar' ), IWD_WC_OPC_VERSION, true );
			wp_enqueue_script( 'wc-checkout' );
		}

		/**
		 * Rewrite WooCommerce standard templates with Custom IWD OPC templates.
		 *
		 * @param $template
		 * @param $template_name
		 * @param $template_path
		 *
		 * @return string
		 * @since 1.0.0
		 */
		function intercept_wc_template( $template, $template_name, $template_path ) {
			global $woocommerce;
			$_template = $template;

			if ( ! $template_path ) {
				$template_path = $woocommerce->template_url;
			}
			$plugin_path = IWD_WC_OPC_PUBLIC_WC_TEMPLATE_PATH;

			// Look within passed path within the theme.
			$template = locate_template(
				array(
					$template_path . $template_name,
					$template_name
				)
			);

			// Get the template from this plugin, if it exists.
			if ( ! $template && file_exists( $plugin_path . $template_name ) ) {
				$template = $plugin_path . $template_name;
			}

			// Use default template.
			if ( ! $template ) {
				$template = $_template;
			}

			return $template;
		}

		/**
		 * Change billing and shipping fields order
		 *
		 * @since 2.0
		 * @param $address_fields
		 * @return mixed
		 */
		public function fields_order($address_fields) {
			$orderBilling = array(
				"billing_email",
				"billing_first_name",
				"billing_last_name",
				"billing_address_1",
				"billing_address_2",
				"billing_country",
				"billing_state",
				"billing_city",
				"billing_postcode",
				"billing_phone"
			);

			$orderShipping = array(
				"shipping_first_name",
				"shipping_last_name",
				"shipping_address_1",
				"shipping_address_2",
				"shipping_country",
				"shipping_state",
				"shipping_city",
				"shipping_postcode",
				"shipping_phone"
			);

			/* Billing fields */
			$ordered_fields = array();

			foreach ( $orderBilling as $field ) {
				$ordered_fields[ $field ] = $address_fields["billing"][ $field ];
			}

			$fields["billing"] = $ordered_fields;

			/* Shipping fields */
			$ordered_fields = array();

			foreach ( $orderShipping as $field ) {
				$ordered_fields[ $field ] = $address_fields["shipping"][ $field ];
			}

			$fields["shipping"] = $ordered_fields;

			return $fields;
		}

		/**
		 * Change billing fields order for WC version >= 3.5.1
		 *
		 * @since 2.0
		 * @param $address_fields
		 * @return mixed
		 */
		public function billing_fields_order($address_fields) {
			$address_fields['billing_email']['priority'] = 1;
			$address_fields['billing_first_name']['priority'] = 2;
			$address_fields['billing_last_name']['priority'] = 3;
			$address_fields['billing_address_1']['priority'] = 4;
			$address_fields['billing_address_2']['priority'] = 5;
			$address_fields['billing_country']['priority'] = 6;
			$address_fields['billing_state']['priority'] = 7;
			$address_fields['billing_city']['priority'] = 8;
			$address_fields['billing_postcode']['priority'] = 9;
			$address_fields['billing_phone']['priority'] = 10;

			return $address_fields;
		}

		/**
		 * Change shipping fields order for WC version >= 3.5.1
		 *
		 * @since 2.0
		 * @param $address_fields
		 * @return mixed
		 */
		public function shipping_fields_order($address_fields) {
			$address_fields['shipping_first_name']['priority'] = 1;
			$address_fields['shipping_last_name']['priority']  = 2;
			$address_fields['shipping_address_1']['priority']  = 3;
			$address_fields['shipping_address_2']['priority']  = 4;
			$address_fields['shipping_country']['priority']    = 5;
			$address_fields['shipping_state']['priority']      = 6;
			$address_fields['shipping_city']['priority']       = 7;
			$address_fields['shipping_postcode']['priority']   = 8;
			$address_fields['shipping_phone']['priority']      = 9;

			return $address_fields;
		}

		/**
		 * Get product attributes
		 *
		 * @since 2.0
		 * @version 2.1.0
		 * @param $product
		 * @return array|bool|string
		 */
		public static function get_attributes($product) {
			$attributes = $product->get_attributes();
			$out = [];

			if ( ! $attributes ) {
				return false;
			}

			if ( is_a( $product, 'WC_Product_Variation' ) ) {
				$out = implode(', ', $attributes);
				return $out;
			} else {
				foreach ( $attributes as $attribute ) {
					// skip variations
					if ( ! is_a( $attribute, 'WC_Product_Attribute' ) ) {
						continue;
					}
					if ( $attribute->get_variation() ) {
						continue;
					}
					// honor the visibility setting
					if ( ! $attribute->get_visible() ) {
						continue;
					}

					$name = $attribute->get_name();

					$value = wc_get_product_terms( $product->id, $name)[0];

					if (is_object($value)) {
						$value = $value->name;
					}

					array_push($out, $value);
				}
			}

			$out = implode(', ', $out);

			return $out;
		}

		/**
		 * Get version of WooCommerce
		 *
		 * @since 2.0
		 * @param string $version
		 * @return bool
		 */
		public static function version_check( $version = '3.5.0' ) {
			global $woocommerce;

			return version_compare( $woocommerce->version, $version, ">=" );
		}
	}
}
