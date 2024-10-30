<?php
/**
 * IWD WooCommerce OPC Template
 *
 * Overwritten WooCommerce template Functions for the templating system.
 *
 * @package  IWD_OPC\Functions
 * @since 1.0.0
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'iwd_wc_opc_review_order' ) ) {

	/**
	 * Output Shipping Methods for the checkout.
	 */
	function iwd_wc_opc_review_order() {
		wc_get_template( 'checkout/review-order.php' );
	}
}

if ( ! function_exists( 'iwd_wc_opc_shipping_methods' ) ) {

	/**
	 * Output Shipping Methods for the checkout.
	 */
	function iwd_wc_opc_shipping_methods() {
		wc_get_template( 'checkout/shipping-methods.php', array(
			'checkout' => WC()->checkout(),
		) );
	}
}

if ( ! function_exists( 'iwd_wc_opc_payment_methods' ) ) {

	/**
	 * Output Shipping Methods for the checkout.
	 */
	function iwd_wc_opc_payment_methods() {
		wc_get_template( 'checkout/payment-methods-list.php' );
	}
}

if ( ! function_exists( 'iwd_wc_opc_payment_methods_wrapper' ) ) {

	/**
	 * Output Shipping Methods for the checkout.
	 */
	function iwd_wc_opc_payment_methods_wrapper() {
		wc_get_template( 'checkout/payment-methods-list-wrapper.php', array(
			'available_gateways' => WC()->payment_gateways->get_available_payment_gateways(),
		) );
	}
}

if ( ! function_exists( 'iwd_wc_opc_coupon' ) ) {

	/**
	 * Output Shipping Methods for the checkout.
	 */
	function iwd_wc_opc_coupon() {
		wc_get_template( 'checkout/coupon.php' );
	}
}

if ( ! function_exists( 'iwd_wc_opc_coupons_list' ) ) {

	/**
	 * Output Shipping Methods for the checkout.
	 */
	function iwd_wc_opc_coupons_list() {
		wc_get_template( 'checkout/coupons-list.php' );
	}
}

if ( ! function_exists( 'iwd_wc_opc_sidebar_tablet_header' ) ) {

	/**
	 * Output Shipping Methods for the checkout.
	 */
	function iwd_wc_opc_sidebar_tablet_header() {
		wc_get_template( 'checkout/sidebar-tablet-header.php' );
	}
}

if ( ! function_exists( 'iwd_wc_opc_additional_fields' ) ) {

	/**
	 * Output Additional Fields for the checkout.
	 */
	function iwd_wc_opc_additional_fields() {
		wc_get_template( 'checkout/additional-fields.php', array(
			'checkout' => WC()->checkout(),
		) );
	}
}

/** Forms */

if ( ! function_exists( 'iwd_wc_opc_form_field' ) ) {

	/**
	 * Outputs a checkout/address form field.
	 *
	 * @param string $key Key.
	 * @param mixed $args Arguments.
	 * @param string $value (default: null).
	 *
	 * @return string
	 */
	function iwd_wc_opc_form_field( $key, $args, $value = null ) {
		$defaults = array(
			'type'              => 'text',
			'label'             => '',
			'description'       => '',
			'placeholder'       => '',
			'maxlength'         => false,
			'required'          => false,
			'autocomplete'      => false,
			'id'                => $key,
			'class'             => array(),
			'label_class'       => array(),
			'input_class'       => array(),
			'return'            => false,
			'options'           => array(),
			'custom_attributes' => array(),
			'validate'          => array(),
			'default'           => '',
			'autofocus'         => '',
			'priority'          => '',
		);

		$args = wp_parse_args( $args, $defaults );
		$args = apply_filters( 'woocommerce_form_field_args', $args, $key, $value );


		$col = 'col-md-';
		switch ( $key ) {
			case 'billing_first_name':
			case 'billing_last_name':
			case 'shipping_first_name':
			case 'shipping_last_name':
				$col .= '6';
				break;
			case 'billing_address_1':
			case 'shipping_address_1':
				$col .= '8';
				break;
			case 'billing_address_2':
			case 'shipping_address_2':
				$col .= '4';
				break;
			case 'billing_country':
			case 'billing_state':
			case 'billing_city':
			case 'billing_postcode':
			case 'billing_phone':
			case 'shipping_country':
			case 'shipping_state':
			case 'shipping_city':
			case 'shipping_postcode':
			case 'shipping_phone':
				$col .= '4';
				break;
			default:
				$col .= '6';
		}

		$col .= ' col-sm-6';

		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required        = '&nbsp;*';
		}

		if ( is_string( $args['label_class'] ) ) {
			$args['label_class'] = array( $args['label_class'] );
		}

		if ( empty( $args['label'] ) ) {
			$args['label'] = $args['placeholder'];
		}

		if ( is_null( $value ) ) {
			$value = $args['default'];
		}

		// Custom attribute handling.
		$custom_attributes         = array();
		$args['custom_attributes'] = array_filter( (array) $args['custom_attributes'], 'strlen' );

		if ( $args['maxlength'] ) {
			$args['custom_attributes']['maxlength'] = absint( $args['maxlength'] );
		}

		if ( ! empty( $args['autocomplete'] ) ) {
			$args['custom_attributes']['autocomplete'] = $args['autocomplete'];
		}

		if ( true === $args['autofocus'] ) {
			$args['custom_attributes']['autofocus'] = 'autofocus';
		}

		if ( $args['description'] ) {
			$args['custom_attributes']['aria-describedby'] = $args['id'] . '-description';
		}

		if ( ! empty( $args['custom_attributes'] ) && is_array( $args['custom_attributes'] ) ) {
			foreach ( $args['custom_attributes'] as $attribute => $attribute_value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}

		if ( ! empty( $args['validate'] ) ) {
			foreach ( $args['validate'] as $validate ) {
				$args['class'][] = 'validate-' . $validate;
			}
		}

		$field           = '';
		$label_id        = $args['id'];
		$sort            = $args['priority'] ? $args['priority'] : '';
		$field_container = '<p class="form-row %1$s %4$s" id="%2$s" data-priority="' . esc_attr( $sort ) . '">%3$s</p>';

		switch ( $args['type'] ) {
			case 'country':
				$countries = 'shipping_country' === $key ? WC()->countries->get_shipping_countries() : WC()->countries->get_allowed_countries();
				if ( 1 === count( $countries ) ) {

					$field .= '<strong>' . current( array_values( $countries ) ) . '</strong>';

					$field .= '<input type="hidden" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="' . current( array_keys( $countries ) ) . '" ' . implode( ' ', $custom_attributes ) . ' class="country_to_state" readonly="readonly" />';

				} else {

					$field = '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="country_to_state country_select ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . '><option value="">' . esc_html__( 'Select a country&hellip;', 'woocommerce' ) . '</option>';

					foreach ( $countries as $ckey => $cvalue ) {
						$field .= '<option value="' . esc_attr( $ckey ) . '" ' . selected( $value, $ckey, false ) . '>' . $cvalue . '</option>';
					}

					$field .= '</select>';

					$field .= '<noscript><button type="submit" name="woocommerce_checkout_update_totals" value="' . esc_attr__( 'Update country', 'woocommerce' ) . '">' . esc_html__( 'Update country', 'woocommerce' ) . '</button></noscript>';

				}

				break;
			case 'state':
				/* Get country this state field is representing */
				$for_country = isset( $args['country'] ) ? $args['country'] : WC()->checkout->get_value( 'billing_state' === $key ? 'billing_country' : 'shipping_country' );
				$states      = WC()->countries->get_states( $for_country );

				if ( is_array( $states ) && empty( $states ) ) {

					$field_container = '<p class="form-row %1$s" id="%2$s" style="display: none">%3$s</p>';

					$field .= '<input type="hidden" class="hidden" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="" ' . implode( ' ', $custom_attributes ) . ' placeholder="' . esc_attr( $args['label'] ) . $required . '" readonly="readonly" />';

				} elseif ( ! is_null( $for_country ) && is_array( $states ) ) {

					$field .= '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="state_select ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . ' data-placeholder="' . esc_attr( $args['label'] ) . $required . '">
						<option value="">' . esc_html__( 'Select a state&hellip;', 'woocommerce' ) . '</option>';

					foreach ( $states as $ckey => $cvalue ) {
						$field .= '<option value="' . esc_attr( $ckey ) . '" ' . selected( $value, $ckey, false ) . '>' . $cvalue . '</option>';
					}

					$field .= '</select>'
					          . '<span class="iwd-opc-field__label">' . esc_attr( $args['label'] ) . $required . '</span>';

				} else {

					$field .= '<input type="text" class="iwd-opc-input ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" value="' . esc_attr( $value ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" ' . implode( ' ', $custom_attributes ) . ' />'
					          . '<span class="iwd-opc-field__label">' . esc_attr( $args['label'] ) . $required . '</span>';

				}

				break;
			case 'textarea':
				$field .= '<textarea name="' . esc_attr( $key ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['label'] ) . $required . '" ' . ( empty( $args['custom_attributes']['rows'] ) ? ' rows="2"' : '' ) . ( empty( $args['custom_attributes']['cols'] ) ? ' cols="5"' : '' ) . implode( ' ', $custom_attributes ) . '>' . esc_textarea( $value ) . '</textarea>';

				break;
			case 'checkbox':
				$field = '<label class="checkbox ' . implode( ' ', $args['label_class'] ) . '" ' . implode( ' ', $custom_attributes ) . '>
						<input type="' . esc_attr( $args['type'] ) . '" class="input-checkbox ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="1" ' . checked( $value, 1, false ) . ' /> ' . $args['label'] . $required . '</label>';

				break;
			case 'text':
			case 'password':
			case 'datetime':
			case 'datetime-local':
			case 'date':
			case 'month':
			case 'time':
			case 'week':
			case 'number':
			case 'email':
			case 'url':
			case 'tel':
				$field .= '<input type="' . esc_attr( $args['type'] ) . '" class="iwd-opc-input ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '"  value="' . esc_attr( $value ) . '" ' . implode( ' ', $custom_attributes ) . ' />';

				if ( $key === 'shipping_phone' ) {
					$field .= '<span class="iwd-opc-field__label">' . 'Phone' . $required . '</span>';
				} else {
					$field .= '<span class="iwd-opc-field__label">' . esc_attr( $args['label'] ) . $required . '</span>';
				}


				break;
			case 'select':
				$field   = '';
				$options = '';

				if ( ! empty( $args['options'] ) ) {
					foreach ( $args['options'] as $option_key => $option_text ) {
						if ( '' === $option_key ) {
							// If we have a blank option, select2 needs a placeholder.
							if ( empty( $args['label'] ) ) {
								$args['label'] = $option_text ? $option_text : __( 'Choose an option', 'woocommerce' );
							}
							$custom_attributes[] = 'data-allow_clear="true"';
						}
						$options .= '<option value="' . esc_attr( $option_key ) . '" ' . selected( $value, $option_key, false ) . '>' . esc_attr( $option_text ) . '</option>';
					}

					$field .= '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="select ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . ' data-placeholder="' . esc_attr( $args['label'] ) . $required . '">
							' . $options . '
						</select>';
				}

				break;
			case 'radio':
				$label_id = current( array_keys( $args['options'] ) );

				if ( ! empty( $args['options'] ) ) {
					foreach ( $args['options'] as $option_key => $option_text ) {
						$field .= '<input type="radio" class="input-radio ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" value="' . esc_attr( $option_key ) . '" name="' . esc_attr( $key ) . '" ' . implode( ' ', $custom_attributes ) . ' id="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '"' . checked( $value, $option_key, false ) . ' />';
						$field .= '<label for="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '" class="radio ' . implode( ' ', $args['label_class'] ) . '">' . $option_text . '</label>';
					}
				}

				break;
		}

		if ( ! empty( $field ) ) {
			$field_html     = '';
			$classForSelect = $args['type'] === 'country' || $args['type'] === 'state' ? ' iwd-opc-field--select' : '';

			$field_html .= '<span class="iwd-opc-field' . $classForSelect . '">' . $field;

			if ( $args['description'] ) {
				$field_html .= '<span class="description" id="' . esc_attr( $args['id'] ) . '-description" aria-hidden="true">' . wp_kses_post( $args['description'] ) . '</span>';
			}

			$field_html .= '<span class="iwd-opc-error">This field is required</span></span>';

			$container_class = esc_attr( implode( ' ', $args['class'] ) );
			$container_id    = esc_attr( $args['id'] ) . '_field';
			$field           = sprintf( $field_container, $container_class, $container_id, $field_html, $col );
		}

		/**
		 * Filter by type.
		 */
		$field = apply_filters( 'woocommerce_form_field_' . $args['type'], $field, $key, $args, $value );

		/**
		 * General filter on form fields.
		 */
		$field = apply_filters( 'woocommerce_form_field', $field, $key, $args, $value );

		if ( $args['return'] ) {
			return $field;
		} else {
            esc_html_e($field); // WPCS: XSS ok.
		}
	}
}