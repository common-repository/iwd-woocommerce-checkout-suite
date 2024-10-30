<?php
/**
 * Payment Methods List
 *
 * This template can be overridden by copying it to yourtheme/iwd-woocommerce-checkout-suite/public/templates/woocommerce/checkout/payment-method.php.
 *
 * @package IWD_OPC/public/templates
 * @since 1.0.0
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;
$paymentMethodsCounter = 0;

$isVirtual = false;
if ( ! WC()->cart->needs_shipping() ) {
	$isVirtual = true;
}

?>
<div id="payment_method" class="iwd-opc-payment-methods-form" data-title="Payment Method">

	<?php iwd_wc_opc_payment_methods_wrapper() ?>

    <div class="multistep-next">
        <div class="row">
            <div class="col-sm-6 col-md-6">
                <button type="submit" class="iwd-opc-btn multistep-btn multistep-place-btn alt">
                    Place order
                </button>
            </div>
        </div>
    </div>

	<?php if ( $isVirtual ): ?>
		<?php iwd_wc_opc_additional_fields(); ?>
	<?php endif; ?>

    <div id="mobile-promo"></div>
    <div id="mobile-place-order"></div>

    <a href="#" class="iwd-opc-payment-methods-form__back edit-cart-link">
        <i class="fa fa-angle-left" aria-hidden="true"></i><?php _e( 'Back to edit delivery method', 'woocommerce' ); ?>
    </a>

</div>
