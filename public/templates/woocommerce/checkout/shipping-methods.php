<?php
/**
 * Shipping Methods Block
 *
 * This template can be overridden by copying it to yourtheme/iwd-woocommerce-checkout-suite/public/templates/woocommerce/checkout/shipping-methods.php.
 *
 * @package IWD_OPC/public/templates
 * @since 1.0.0
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;
?>

<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

	<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>

	<?php iwd_wc_opc_shipping_methods_html(); ?>

	<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>

<?php endif; ?>