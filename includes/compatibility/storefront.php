<?php
/**
 * IWD WC OPC compatibility with Storefront theme.
 *
 * @since      1.0.0
 * @version    2.1.0
 * @package    IWD_OPC
 * @subpackage IWD_OPC/includes/compatibility
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( ! function_exists( 'iwd_wc_opc_storefront_compatibility' ) ){
	/**
	 * Add Storefront style configurations to IWD OPC
	 *
	 * @since 1.0.0
	 */
	function iwd_wc_opc_storefront_compatibility() {
		/**
		 * Get necessary Storefront theme mods.
		 */
		$storefront_theme_mods = array(
			'accent_color'                => get_theme_mod( 'storefront_accent_color' ),
			'button_background_color'     => get_theme_mod( 'storefront_button_background_color' ),
			'button_text_color'           => get_theme_mod( 'storefront_button_text_color' )
		);


		$css = '
			.iwd-opc-btn,
			.iwd-opc-sidebar.open .iwd-opc-sidebar__tablet-header {
				background-color: ' . $storefront_theme_mods['button_background_color'] . ';
				color: ' .  $storefront_theme_mods['button_text_color'] . ';
			}
			
			.iwd-opc-discount__title::after {
				color: ' . $storefront_theme_mods['button_background_color'] . ';
			}
			
			.wc-gateway-ppec-cancel {
				background-color: ' . $storefront_theme_mods['button_background_color'] . ';
				color: ' .  $storefront_theme_mods['button_text_color'] . ' !important;
			}
			
			.iwd-opc-sidebar.open .iwd-opc-sidebar__tablet-header p:last-child {
				color: ' .  $storefront_theme_mods['button_text_color'] . ';
			}
			
			.iwd-opc-input:focus ,
			.address-field .iwd-opc-field--select .input-text:focus,
			textarea#order_comments:focus {
				border-color: ' . $storefront_theme_mods['accent_color'] . ';
			}
			
			.iwd-opc a,
			.iwd-opc-review-form__tablet-header h3 {
				color: ' . $storefront_theme_mods['accent_color'] . ';
			}
			
			.iwd-opc .shipping-methods-list__item.checked > label,
			.iwd-opc .payment-methods-list__item.checked {
				border-color: ' . $storefront_theme_mods['accent_color'] . ';
			}
		
			
			.iwd-opc .shipping-methods-list__item.checked > label::before,
			.iwd-opc .payment-methods-list__item.checked > label::before,
		    .iwd-opc .woocommerce-SavedPaymentMethods li input:checked + label::before,
	        .iwd-opc .iwd-opc-review-totals__discount td {
				color: ' . $storefront_theme_mods['accent_color'] . ';
			}
			
			
			.iwd-opc .select2-container--default .select2-selection--single:focus,
			.iwd-opc .wc-stripe-elements-field.focused,
			.iwd-opc .sq-input.sq-input--focus,
			.iwd-opc .iwd_option.checked {
				border-color: ' . $storefront_theme_mods['accent_color'] . ';
			}
			
			.iwd-opc-empty-cart a.iwd-opc-btn {
				border-color: ' . $storefront_theme_mods['button_background_color'] . ';
				color: ' .  $storefront_theme_mods['button_background_color'] . ';
			}
			
			.iwd-opc-empty-cart a.iwd-opc-btn:hover,
			.iwd-opc-empty-cart a.iwd-opc-btn.active {
				border-color: ' . $storefront_theme_mods['accent_color'] . ';
				color: ' .  $storefront_theme_mods['accent_color'] . ';
			}
			 
			.iwd-opc-empty-cart__copyright > a{
				color: ' .  $storefront_theme_mods['accent_color'] . ';
			}
			
		';

		wp_add_inline_style( 'storefront-style', $css );
	}
}

add_action( 'wp_enqueue_scripts', 'iwd_wc_opc_storefront_compatibility', 130 );