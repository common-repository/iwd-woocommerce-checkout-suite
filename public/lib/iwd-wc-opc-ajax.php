<?php
/**
 * IWD WooCommerce OPC Ajax Plugin Class.
 *
 * @package    IWD_OPC
 * @subpackage IWD_OPC/includes
 * @since      1.0.0
 * @version    2.0.0
 */

if ( ! defined( 'IWD_WC_OPC_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'IWD_OPC_AJAX' ) ) {

	/**
	 * Class IWD_OPC_AJAX
	 */
	class IWD_OPC_AJAX extends WC_AJAX {

		/**
		 * Hook in ajax handlers.
		 */
		public static function init() {
			add_action( 'init', array( __CLASS__, 'define_ajax' ), 0 );
			add_action( 'template_redirect', array( __CLASS__, 'do_wc_ajax' ), 0 );
			self::add_ajax_events();
		}

		/**
		 * Get WC Ajax Endpoint.
		 *
		 * @param  string $request Optional.
		 *
		 * @return string
		 */
		public static function get_endpoint( $request = '' ) {
			return esc_url_raw( apply_filters( 'woocommerce_ajax_get_endpoint', add_query_arg( 'wc-ajax', $request, remove_query_arg( array(
				'remove_item',
				'add-to-cart',
				'added-to-cart'
			), home_url( '/', 'relative' ) ) ), $request ) );
		}

		/**
		 * Set WC AJAX constant and headers.
		 */
		public static function define_ajax() {
			if ( ! empty( $_GET['wc-ajax'] ) ) {
				wc_maybe_define_constant( 'DOING_AJAX', true );
				wc_maybe_define_constant( 'WC_DOING_AJAX', true );
				if ( ! WP_DEBUG || ( WP_DEBUG && ! WP_DEBUG_DISPLAY ) ) {
					@ini_set( 'display_errors', 0 ); // Turn off display_errors during AJAX events to prevent malformed JSON.
				}
				$GLOBALS['wpdb']->hide_errors();
			}
		}

		/**
		 * Send headers for WC Ajax Requests.
		 */
		private static function wc_ajax_headers() {
			send_origin_headers();
			@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
			@header( 'X-Robots-Tag: noindex' );
			send_nosniff_header();
			wc_nocache_headers();
			status_header( 200 );
		}

		/**
		 * Check for WC Ajax request and fire action.
		 */
		public static function do_wc_ajax() {
			global $wp_query;

			if ( ! empty( $_GET['wc-ajax'] ) ) {
				$wp_query->set( 'wc-ajax', sanitize_text_field( wp_unslash( $_GET['wc-ajax'] ) ) );
			}

			$action = $wp_query->get( 'wc-ajax' );

			if ( $action ) {
				self::wc_ajax_headers();
				$action = sanitize_text_field( $action );
				do_action( 'wc_ajax_' . $action );
				wp_die();
			}
		}

		/**
		 * Custom IWD OPC Events
		 *
		 * @version 1.0.0
		 */
		public static function add_ajax_events() {
			// woocommerce_EVENT => nopriv
			$ajax_events = array(
				'iwd_opc_update_order_review' => true,
				'iwd_opc_customer_login'      => true
			);
			foreach ( $ajax_events as $ajax_event => $nopriv ) {
				add_action( 'wp_ajax_woocommerce_' . $ajax_event, array( __CLASS__, $ajax_event ) );
				if ( $nopriv ) {
					add_action( 'wp_ajax_nopriv_woocommerce_' . $ajax_event, array( __CLASS__, $ajax_event ) );
					// WC AJAX can be used for frontend ajax requests
					add_action( 'wc_ajax_' . $ajax_event, array( __CLASS__, $ajax_event ) );
				}
			}
		}

		/**
		 * Updated WC Update Order Review
		 *
		 * @since 1.0.0
		 * @version 2.0.0
		 */
		public static function iwd_opc_update_order_review() {
			check_ajax_referer( 'update-order-review', 'security' );

			wc_maybe_define_constant( 'WOOCOMMERCE_CHECKOUT', true );

			if ( WC()->cart->is_empty() && ! is_customize_preview() ) {
				self::update_order_review_expired();
			}

			do_action( 'woocommerce_checkout_update_order_review', $_POST['post_data'] );

			$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );

			if ( isset( $_POST['shipping_method'] ) && is_array( $_POST['shipping_method'] ) ) {
				foreach ( $_POST['shipping_method'] as $i => $value ) {
					$chosen_shipping_methods[ $i ] = wc_clean( $value );
				}
			}

			WC()->session->set( 'chosen_shipping_methods', $chosen_shipping_methods );
			WC()->session->set( 'chosen_payment_method', empty( $_POST['payment_method'] ) ? '' : sanitize_text_field($_POST['payment_method'] ));
			WC()->customer->set_props(
				array(
					'billing_country'   => isset( $_POST['country'] ) ? sanitize_text_field( $_POST['country'] ) : null,
					'billing_state'     => isset( $_POST['state'] ) ? sanitize_text_field( $_POST['state'] ) : null,
					'billing_postcode'  => isset( $_POST['postcode'] ) ? sanitize_text_field( $_POST['postcode'] ) : null,
					'billing_city'      => isset( $_POST['city'] ) ? sanitize_text_field( $_POST['city'] ) : null,
					'billing_address_1' => isset( $_POST['address'] ) ? sanitize_text_field( $_POST['address'] ) : null,
					'billing_address_2' => isset( $_POST['address_2'] ) ? sanitize_text_field( $_POST['address_2'] ) : null,
				)
			);

			if ( wc_ship_to_billing_address_only() ) {
				WC()->customer->set_props(
					array(
						'shipping_country'   => isset( $_POST['country'] ) ? sanitize_text_field( $_POST['country'] ) : null,
						'shipping_state'     => isset( $_POST['state'] ) ? sanitize_text_field( $_POST['state'] ) : null,
						'shipping_postcode'  => isset( $_POST['postcode'] ) ? sanitize_text_field( $_POST['postcode'] ) : null,
						'shipping_city'      => isset( $_POST['city'] ) ? sanitize_text_field( $_POST['city'] ) : null,
						'shipping_address_1' => isset( $_POST['address'] ) ? sanitize_text_field( $_POST['address'] ) : null,
						'shipping_address_2' => isset( $_POST['address_2'] ) ? sanitize_text_field( $_POST['address_2'] ) : null,
					)
				);
			} else {
				WC()->customer->set_props(
					array(
						'shipping_country'   => isset( $_POST['s_country'] ) ? sanitize_text_field( $_POST['s_country'] ) : null,
						'shipping_state'     => isset( $_POST['s_state'] ) ? sanitize_text_field( $_POST['s_state'] ) : null,
						'shipping_postcode'  => isset( $_POST['s_postcode'] ) ? sanitize_text_field( $_POST['s_postcode'] ) : null,
						'shipping_city'      => isset( $_POST['s_city'] ) ? sanitize_text_field( $_POST['s_city'] ) : null,
						'shipping_address_1' => isset( $_POST['s_address'] ) ? sanitize_text_field( $_POST['s_address'] ) : null,
						'shipping_address_2' => isset( $_POST['s_address_2'] ) ? sanitize_text_field( $_POST['s_address_2'] ) : null,
					)
				);
			}

			if ( wc_string_to_bool( $_POST['has_full_address'] ) ) {
				WC()->customer->set_calculated_shipping( true );
			} else {
				WC()->customer->set_calculated_shipping( false );
			}

			WC()->customer->save();
			WC()->cart->calculate_totals();

			// Get order review fragment
			ob_start();
			woocommerce_order_review();
			$woocommerce_order_review = ob_get_clean();

			// Get checkout payment fragment
			ob_start();
			iwd_wc_opc_payment_methods_wrapper();
			$iwd_wc_opc_payment_methods = ob_get_clean();

			// Get shipping methods fragment
			ob_start();
			iwd_wc_opc_shipping_methods();
			$iwd_wc_opc_shipping_methods = ob_get_clean();

			// Get shipping methods fragment
			ob_start();
			iwd_wc_opc_coupons_list();
			$iwd_wc_opc_coupons_list = ob_get_clean();


			// Get sidebar tablet header fragment
			ob_start();
			iwd_wc_opc_sidebar_tablet_header();
			$iwd_wc_opc_sidebar_tablet_header = ob_get_clean();

			// Get messages if reload checkout is not true
			$messages = '';
			if ( ! isset( WC()->session->reload_checkout ) ) {
				ob_start();
				wc_print_notices();
				$messages = ob_get_clean();
			}

			unset( WC()->session->refresh_totals, WC()->session->reload_checkout );

			wp_send_json(
				array(
					'result'    => empty( $messages ) ? 'success' : 'failure',
					'messages'  => $messages,
					'reload'    => isset( WC()->session->reload_checkout ) ? 'true' : 'false',
					'fragments' => apply_filters(
						'woocommerce_update_order_review_fragments', array(
							'.iwd-opc-review-form'                   => $woocommerce_order_review,
							'.iwd-opc-payment-methods-form__wrapper' => $iwd_wc_opc_payment_methods,
							'.iwd-opc-shipping-methods-form'         => $iwd_wc_opc_shipping_methods,
							'.iwd_opc_coupons_list_block'            => $iwd_wc_opc_coupons_list,
							'.iwd-opc-sidebar__tablet-header'        => $iwd_wc_opc_sidebar_tablet_header
						)
					)
				)
			);
		}

		/**
		 * Session has expired.
		 */
		private static function update_order_review_expired() {
			wp_send_json(
				array(
					'fragments' => apply_filters(
						'woocommerce_update_order_review_fragments', array(
							'.woocommerce-checkout' => '<div class="woocommerce-error">' . __( 'Sorry, your session has expired.', 'woocommerce' ) . ' <a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '" class="wc-backward">' . __( 'Return to shop', 'woocommerce' ) . '</a></div>',
						)
					),
				)
			);
		}

		/**
		 * Custom IWD OPC Login Form
		 *
		 * @version 1.0.0
		 */
		function iwd_opc_customer_login() {
			$creds                  = array();
			$creds['user_login']    = $_POST['username'];
			$creds['user_password'] = $_POST['password'];
			$creds['remember']      = true;

			$user = wp_signon( $creds, false );

			if ( is_wp_error( $user ) ) {
				$error   = true;
				$message = __( 'Invalid login or password!', 'woocommerce' );
			} else {
				$error   = false;
				$message = __( 'Success', 'woocommerce' );
			}

			wp_send_json(
				array(
					'error'   => $error,
					'message' => $message
				)
			);
		}

	}

	$iwd_opc_ajax = new IWD_OPC_AJAX();
	$iwd_opc_ajax->init();
}