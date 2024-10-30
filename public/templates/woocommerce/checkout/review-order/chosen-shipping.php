<?php
/**
 * Show Chosen Shipping Method in Order Review
 *
 * In 2.1 we show methods per package. This allows for multiple methods per order if so desired.
 *
 * This template can be overridden by copying it to yourtheme/iwd-woocommerce-checkout-suite/public/templates/woocommerce/checkout/review-order/cart-shipping.php.
 *
 * @package IWD_OPC/public/templates
 * @since 1.0.0
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<tr class="iwd-opc-review-totals__shipping">
    <td><?php echo wp_kses_post( $package_name ); ?></td>
    <td>
		<?php if ( count( $available_methods ) ) : ?>
			<?php foreach ( $available_methods as $method ) : ?>
				<?php if ( checked( $method->id, $chosen_method, false ) ): ?>
					<?php echo iwd_wc_cart_totals_shipping_method_price( $method ) ?>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>
    </td>
</tr>