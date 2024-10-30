<?php
/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/iwd-woocommerce-checkout-suite/public/templates/woocommerce/checkout/review-order.php.
 *
 * @package IWD_OPC/public/templates
 * @since 1.0.0
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="iwd-opc-review-form js-iwd-opc-review-form">
    <div class="iwd-opc-review-form__main-wrapper">
        <div class="iwd-opc-review-products-wrapper">
            <div class="iwd-opc-review-products">
				<?php
				do_action( 'woocommerce_review_order_before_cart_contents' );
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
					$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

					if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
						?>

                        <div class="iwd-opc-review-products__item">
							<?php if ( has_post_thumbnail( $_product->id ) ): ?>
                                <div class="iwd-opc-review-products__item-img">
									<?php
									$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
									echo esc_attr($thumbnail);
									?>
                                </div>
							<?php endif; ?>

                            <div class="iwd-opc-review-products__item-info">
                                <p class="iwd-opc-review-products__item-name"><?php echo esc_attr($_product->get_title()); ?></p>
								<?php
								$attributes = IWD_OPC_Frontend::get_attributes( $_product );
								if ( $attributes ) {
									echo '<p class="iwd-opc-review-products__item-attributes">' . esc_attr($attributes) . '</p>';
								}
								?>
                                <p class="iwd-opc-review-products__item-qty">
                                    (<?php echo apply_filters( 'woocommerce_checkout_cart_item_quantity', $cart_item['quantity'], $cart_item, $cart_item_key ); ?>)
                                </p>
                            </div>

                            <p class="iwd-opc-review-products__item-subtotal"><?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?></p>

                        </div>
						<?php
					}
				}
				do_action( 'woocommerce_review_order_after_cart_contents' );
				?>

            </div>
        </div>

		<?php
		// IWD OPC Default Configurations
		$is_discount_form_enabled = get_option( 'iwd_wc_opc_discount_form_enabled' );
		if ( $is_discount_form_enabled ) {
			iwd_wc_opc_coupon();
		}
		?>

        <div class="iwd-opc-review-totals-wrapper">
            <table class="iwd-opc-review-totals">

                <tr class="iwd-opc-review-totals__subtotal">
                    <td><?php _e( 'Subtotal', 'woocommerce' ); ?></td>
                    <td><?php wc_cart_totals_subtotal_html(); ?></td>
                </tr>

				<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
					<?php iwd_wc_opc_chosen_shipping_html(); ?>
				<?php endif; ?>

				<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
                    <tr class="iwd-opc-review-totals__fee">
                        <td><?php echo esc_html( $fee->name ); ?></td>
                        <td><?php wc_cart_totals_fee_html( $fee ); ?></td>
                    </tr>
				<?php endforeach; ?>

				<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
					<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
						<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
                            <tr class="iwd-opc-review-totals__tax-rate tax_rate_<?php echo sanitize_title( $code ); ?>">
                                <td><?php echo esc_html( $tax->label ); ?></td>
                                <td><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
                            </tr>
						<?php endforeach; ?>
					<?php else : ?>
                        <tr class="iwd-opc-review-totals__fee-tax">
                            <td><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></td>
                            <td><?php wc_cart_totals_taxes_total_html(); ?></td>
                        </tr>
					<?php endif; ?>
				<?php endif; ?>

				<?php if ( WC()->cart->get_coupons() ): ?>
                    <tr class="iwd-opc-review-totals__discount">
                        <td><?php _e( 'Discount', 'woocommerce' ); ?></td>
                        <td><?php iwd_wc_cart_total_discount_amount_html(); ?></td>
                    </tr>
				<?php endif; ?>

				<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

                <tr class="iwd-opc-review-totals__total">
                    <td><?php _e( 'Total to pay', 'woocommerce' ); ?></td>
                    <td><?php wc_cart_totals_order_total_html(); ?></td>
                </tr>

				<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

            </table>
        </div>

		<?php do_action( 'iwd_woocommerce_checkout_payment' ); ?>
    </div>
</div>