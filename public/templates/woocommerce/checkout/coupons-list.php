<?php
/**
 * Checkout coupons list
 *
 * This template can be overridden by copying it to yourtheme/iwd-woocommerce-checkout-suite/public/templates/woocommerce/checkout/coupons-list.php.
 *
 * @package IWD_OPC/public/templates
 * @since 1.0.0
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="iwd_opc_coupons_list_block">
	<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
        <div class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
            <div class="cart_discount_label"><?php wc_cart_totals_coupon_label( $coupon ); ?></div>
            <div class="cart_discount_html"><?php wc_cart_totals_coupon_html( $coupon ); ?></div>
        </div>
	<?php endforeach; ?>
</div>
