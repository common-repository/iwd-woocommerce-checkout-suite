<?php
/**
 * Checkout coupon form
 *
 * This template can be overridden by copying it to yourtheme/iwd-woocommerce-checkout-suite/public/templates/woocommerce/checkout/form-coupon.php.
 *
 * @package IWD_OPC/public/templates
 * @since 1.0.0
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! wc_coupons_enabled() ) { // @codingStandardsIgnoreLine.
	return;
}

?>
<div class="iwd-opc-discount<?php if ( WC()->cart->get_coupons() ): ?> opened <?php endif; ?>">
    <div class="iwd-opc-discount__title-wrapper">
        <h3 class="iwd-opc-discount__title">
			<?php esc_attr_e( 'Apply a Promo Code', 'woocommerce' ); ?>
        </h3>
    </div>

    <div class="iwd-opc-discount__form">

		<?php iwd_wc_opc_coupons_list(); ?>

        <div class="iwd-opc-discount__form-input-wrapper" data-id="iwd_checkout_coupon_block">
            <div class="iwd-opc-discount__field">
                <input type="text" class="iwd-opc-discount__form-input iwd-opc-input" name="coupon_code"
                       id="coupon_code" placeholder="<?php esc_attr_e( 'Enter Code', 'woocommerce' ); ?>" value="">
            </div>

            <div class="iwd-opc-discount__apply-wrapper">
                <button type="button" class="iwd-opc-btn" name="iwd_apply_discount">
                    <span><?php esc_html_e( 'Apply', 'woocommerce' ); ?></span></button>
            </div>
        </div>
    </div>
</div>
