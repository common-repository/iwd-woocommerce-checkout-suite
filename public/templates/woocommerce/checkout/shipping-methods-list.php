<?php
/**
 * Shipping Methods Display
 *
 * In 2.1 we show methods per package. This allows for multiple methods per order if so desired.
 *
 * This template can be overridden by copying it to yourtheme/iwd-woocommerce-checkout-suite/public/templates/woocommerce/checkout/shipping-methods-list.php.
 *
 * @package IWD_OPC/public/templates
 * @since 1.0.0
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="iwd-opc-shipping-methods-form" id="nav-shipping-method">
    <h3 class="iwd-opc-section-title"><?php _e( 'Delivery Method', 'woocommerce' ); ?></h3>

	<?php if ( count( $available_methods ) ) : ?>
        <div class="shipping-methods-list row">
			<?php foreach ( $available_methods as $method ) : ?>
                <div class="shipping-methods-list__item col-sm-4 col-md-4 <?php if ( checked( $method->id, $chosen_method, false ) ): ?> checked <?php endif; ?>">
					<?php
					printf( '<input type="radio" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method input-radio" %4$s />
                            <label for="shipping_method_%1$d_%2$s" >%5$s</label>',
						$index, sanitize_title( $method->id ), esc_attr( $method->id ), checked( $method->id, $chosen_method, false ), iwd_wc_opc_shipping_method_label( $method ) );

					do_action( 'woocommerce_after_shipping_rate', $method, $index );
					?>
                </div>
			<?php endforeach; ?>
        </div>
	<?php elseif ( WC()->customer->has_calculated_shipping() ) : ?>
		<?php
		if ( is_cart() ) {
			echo apply_filters( 'woocommerce_cart_no_shipping_available_html', wpautop( __( 'There are no shipping methods available. Please ensure that your address has been entered correctly, or contact us if you need any help.', 'woocommerce' ) ) );
		} else {
			echo apply_filters( 'woocommerce_no_shipping_available_html', wpautop( __( 'There are no shipping methods available. Please ensure that your address has been entered correctly, or contact us if you need any help.', 'woocommerce' ) ) );
		}
		?>
	<?php elseif ( ! is_cart() ) : ?>
		<?php echo wpautop( __( 'Enter your full address to see shipping costs.', 'woocommerce' ) ); ?>
	<?php endif; ?>
	<?php if ( $show_package_details ) : ?>
        <div class="field form-row">
            <p class="shipping_package_details"><?php echo esc_html( $package_details ); ?></p>
        </div>
	<?php endif; ?>

    <div class="multistep-next">
        <div class="row">
            <div class="col-sm-6 col-md-4">
                <button type="button" class="iwd-opc-btn multistep-btn multistep-delivery-btn alt">
					<?php _e( 'Continue to Payment', 'woocommerce' ); ?>
                </button>
            </div>
        </div>
        <a href="#" class="iwd-opc-shipping-methods-form__back edit-cart-link"><i class="fa fa-angle-left"
                                                                                  aria-hidden="true"></i><?php _e( 'Back to edit shipping address', 'woocommerce' ); ?>
        </a>
    </div>

	<?php iwd_wc_opc_additional_fields(); ?>
</div>
