<?php
/**
 * Sidebar header for tablet view.
 *
 * This template can be overridden by copying it to yourtheme/iwd-woocommerce-checkout-suite/public/templates/woocommerce/checkout/sidebar-tablet-header.php.
 *
 * @package IWD_OPC/public/templates
 * @since 2.0.0
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="iwd-opc-sidebar__tablet-header js-summary-tablet-header">
    <p><?php _e( 'View Order Summary', 'woocommerce' ); ?> -
        <span class="iwd-opc-sidebar__tablet-header-total"><?php wc_cart_totals_order_total_html(); ?></span>
    </p>
    <p><?php _e( 'Back to Checkout', 'woocommerce' ); ?></p>
</div>