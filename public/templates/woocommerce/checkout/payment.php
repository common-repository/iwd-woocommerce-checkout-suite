<?php
/**
 * Payment Methods Block
 *
 * This template can be overridden by copying it to yourtheme/iwd-woocommerce-checkout-suite/public/templates/woocommerce/checkout/payment.php.
 *
 * @package IWD_OPC/public/templates
 * @since 1.0.0
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_ajax() ) {
	do_action( 'woocommerce_review_order_before_payment' );
}
?>

    <div class="iwd-opc-place-order-wrapper">
        <div class="iwd_opc_place_order form-row">
            <noscript>
				<?php esc_html_e( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the <em>Update Totals</em> button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce' ); ?>
                <br/>
                <button type="submit" class="button alt" name="woocommerce_checkout_update_totals"
                        value="<?php esc_attr_e( 'Update totals', 'woocommerce' ); ?>"><?php esc_html_e( 'Update totals', 'woocommerce' ); ?></button>
            </noscript>

			<?php wc_get_template( 'checkout/terms.php' ); ?>

			<?php do_action( 'woocommerce_review_order_before_submit' ); ?>
            <style>
                .wc-credit-card-form{
                    color:#000000;
                    background: white;
                }
                .wc-credit-card-form input{
                    border-bottom: 1px solid #000000;
                }
            </style>

            <div id="ppc-button"></div>

            <div class="place-order-button-wrapper">
				<?php echo apply_filters( 'woocommerce_order_button_html', '<button id="place_order_iwd" type="submit" class="iwd-opc-btn place-order-btn alt" name="iwd_opc_place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); ?>
            </div>

            <div style="display: none;">
                <button type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="Place order" data-value="Place order" ">Place order NEW</button>
                <?php do_action( 'woocommerce_review_order_after_submit' ); ?>
            </div>




			<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
        </div>
    </div>
<?php
if ( ! is_ajax() ) {
	do_action( 'woocommerce_review_order_after_payment' );
}
