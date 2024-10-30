<?php
/**
 * Checkout shipping information form
 *
 * This template can be overridden by copying it to yourtheme/iwd-woocommerce-checkout-suite/public/templates/woocommerce/checkout/form-shipping.php.
 *
 * @package IWD_OPC/public/templates
 * @since 1.0.0
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="iwd-opc-shipping-form woocommerce-shipping-fields">
	<?php if ( true === WC()->cart->needs_shipping_address() ) : ?>
        <p id="ship-to-different-address" class="field form-row">
            <input id="ship-to-different-address-checkbox"
                   class="input-checkbox" <?php checked( apply_filters( 'woocommerce_ship_to_different_address_checked', 'shipping' === get_option( 'woocommerce_ship_to_destination' ) ? 1 : 0 ), 1 ); ?>
                   type="checkbox" name="ship_to_different_address" value="1"/>
            <label for="ship-to-different-address-checkbox" class="checkbox">
                <span><?php _e( 'My billing and shipping address are different', 'woocommerce' ); ?></span>
            </label>
        </p>

        <div class="shipping_address">
            <h3 class="iwd-opc-section-title"><?php _e( 'Shipping Address', 'woocommerce' ); ?></h3>

			<?php do_action( 'woocommerce_before_checkout_shipping_form', $checkout ); ?>

            <div class="iwd_opc_shipping_address_form_fields">
                <div class="row">
					<?php
					$fields = $checkout->get_checkout_fields( 'shipping' );

					foreach ( $fields as $key => $field ) {
						// Remove Company Field
						if ( $key == 'shipping_company' ) {
							continue;
						}

						if ( isset( $field['country_field'], $fields[ $field['country_field'] ] ) ) {
							$field['country'] = $checkout->get_value( $field['country_field'] );
						}
						iwd_wc_opc_form_field( $key, $field, $checkout->get_value( $key ) );
					}
					?>
                </div>
            </div>

			<?php do_action( 'woocommerce_after_checkout_shipping_form', $checkout ); ?>

        </div>

	<?php endif; ?>
    <div class="multistep-next">
        <div class="row">
            <div class="col-sm-6 col-md-5">
                <button type="button" class="iwd-opc-btn multistep-btn multistep-billing-btn alt">
					<?php _e( 'Continue to delivery Method', 'woocommerce' ); ?>
                </button>
            </div>
        </div>
    </div>

    <a href="<?php echo get_site_url( null, 'cart' ); ?>"
       class="iwd-opc-payment-methods-form__edit-cart iwd-opc-payment-methods-form__edit-cart--multistep edit-cart-link">
        <i class="fa fa-angle-left" aria-hidden="true"></i>
		<?php _e( 'Back to edit shopping cart', 'woocommerce' ); ?>
    </a>
</div>
