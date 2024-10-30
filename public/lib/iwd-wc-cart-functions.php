<?php
/**
 * IWD WooCommerce OPC Cart Functions
 *
 * Functions for cart specific things.
 *
 * @package IWD_OPC
 * @since 1.0.0
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get Chosen Shipping Method.
 */
function iwd_wc_opc_chosen_shipping_html() {
	$packages = WC()->shipping->get_packages();

	foreach ( $packages as $i => $package ) {
		$chosen_method = isset( WC()->session->chosen_shipping_methods[ $i ] ) ? WC()->session->chosen_shipping_methods[ $i ] : '';

		wc_get_template(
			'checkout/review-order/chosen-shipping.php', array(
				'available_methods' => $package['rates'],
				/* translators: %d: shipping package number */
				'package_name'      => apply_filters( 'woocommerce_shipping_package_name', ( ( $i + 1 ) > 1 ) ? sprintf( _x( 'Shipping %d', 'shipping packages', 'woocommerce' ), ( $i + 1 ) ) : _x( 'Shipping', 'shipping packages', 'woocommerce' ), $i, $package ),
				'chosen_method'     => $chosen_method,
			)
		);
	}
}

/**
 * Get Shipping Methods.
 */
function iwd_wc_opc_shipping_methods_html() {
	$packages = WC()->shipping->get_packages();
	$first    = true;

	foreach ( $packages as $i => $package ) {
		$chosen_method = isset( WC()->session->chosen_shipping_methods[ $i ] ) ? WC()->session->chosen_shipping_methods[ $i ] : '';
		$product_names = array();

		if ( count( $packages ) > 1 ) {
			foreach ( $package['contents'] as $item_id => $values ) {
				$product_names[ $item_id ] = $values['data']->get_name() . ' &times;' . $values['quantity'];
			}
			$product_names = apply_filters( 'woocommerce_shipping_package_details_array', $product_names, $package );
		}

		wc_get_template(
			'checkout/shipping-methods-list.php', array(
				'package'                  => $package,
				'available_methods'        => $package['rates'],
				'show_package_details'     => count( $packages ) > 1,
				'show_shipping_calculator' => is_cart() && $first,
				'package_details'          => implode( ', ', $product_names ),
				/* translators: %d: shipping package number */
				'package_name'             => apply_filters( 'woocommerce_shipping_package_name', ( ( $i + 1 ) > 1 ) ? sprintf( _x( 'Shipping %d', 'shipping packages', 'woocommerce' ), ( $i + 1 ) ) : _x( 'Shipping', 'shipping packages', 'woocommerce' ), $i, $package ),
				'index'                    => $i,
				'chosen_method'            => $chosen_method,
			)
		);

		$first = false;
	}
}

/**
 * Get a shipping methods full label including price.
 *
 * @param  WC_Shipping_Rate $method Shipping method rate data.
 * @return mixed
 */
function iwd_wc_opc_shipping_method_label( $method ) {
	$label = '';

	if ( $method->cost >= 0 && $method->get_method_id() !== 'free_shipping' ) {
		if ( WC()->cart->display_prices_including_tax() ) {
			$label .= wc_price( $method->cost + $method->get_shipping_tax() );
			if ( $method->get_shipping_tax() > 0 && ! wc_prices_include_tax() ) {
				$label .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
			}
		} else {
			$label .= wc_price( $method->cost );
			if ( $method->get_shipping_tax() > 0 && wc_prices_include_tax() ) {
				$label .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
			}
		}
	}

	$label .= $method->get_label();

	return apply_filters( 'woocommerce_cart_shipping_method_full_label', $label, $method );
}

/**
 * Get a shipping methods price.
 *
 * @param  WC_Shipping_Rate $method Shipping method rate data.
 *
 * @return string
 */
function iwd_wc_cart_totals_shipping_method_price( $method ) {
	if ( $method->cost >= 0 ) {
		if ( WC()->cart->display_prices_including_tax() ) {
			$price = wc_price( $method->cost + $method->get_shipping_tax() );
			if ( $method->get_shipping_tax() > 0 && ! wc_prices_include_tax() ) {
				$price .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
			}
		} else {
			$price = wc_price( $method->cost );
			if ( $method->get_shipping_tax() > 0 && wc_prices_include_tax() ) {
				$price .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
			}
		}
	}

    esc_html_e($price);
}

/**
 * Get a total discount amount.
 *
 * @return string
 */
function iwd_wc_cart_total_discount_amount_html() {
	$total_discount_amount = 0;

	foreach ( WC()->cart->get_coupons() as $code => $coupon ) {
		if ( is_string( $coupon ) ) {
			$coupon = new WC_Coupon( $coupon );
		}

		$amount                = WC()->cart->get_coupon_discount_amount( $coupon->get_code(), WC()->cart->display_cart_ex_tax );
		$total_discount_amount += $amount;
	}
	// Invert Discount value
	$total_discount_amount      = - $total_discount_amount;
	$total_discount_amount_html = wc_price( $total_discount_amount );

    esc_html_e($total_discount_amount_html);
}