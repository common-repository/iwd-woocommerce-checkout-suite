<?php
/**
 * Payment Methods Block Wrapper
 *
 * This template can be overridden by copying it to yourtheme/iwd-woocommerce-checkout-suite/public/templates/woocommerce/checkout/payment-methods-list-wrapper.php.
 *
 * @package IWD_OPC/public/templates
 * @since 2.0.0
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;
$paymentMethodsCounter = 0;
?>

<div class="iwd-opc-payment-methods-form__wrapper">
	<?php if ( WC()->cart->needs_payment() ) : ?>
		<?php if ( ! empty( $available_gateways ) ): ?>
            <h3 class="iwd-opc-section-title"><?php _e( 'Payment Method', 'woocommerce' ); ?></h3>

            <div class="payment-methods-list">

				<?php foreach ( $available_gateways as $gateway ): ?>
					<?php
					if ( $paymentMethodsCounter % 2 === 0 ) {
						echo "<div class='row'>";
					}
					?>
                    <div class="col-sm-6 col-md-6">
                        <div class="payment-methods-list__item wc_payment_method payment_method_<?php echo esc_attr($gateway->id); ?>">
                            <input id="payment_method_<?php echo esc_attr($gateway->id); ?>" type="radio" class="input-radio"
                                   name="payment_method"
                                   value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?>
                                   data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>"/>

                            <label for="payment_method_<?php echo esc_attr($gateway->id); ?>">
                                <span class="title"><?php echo esc_attr( $gateway->get_title()); ?></span>
                                <span class="icon"></span> <!-- TODO:: icon -->
                            </label>

							<?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
                                <div class="payment_box payment_method_<?php echo esc_attr($gateway->id); ?>"
								     <?php if ( ! $gateway->chosen ) : ?>style="display:none;"<?php endif; ?>>
									<?php echo esc_attr( $gateway->payment_fields()); ?>
                                </div>
							<?php endif; ?>
                        </div>
                    </div>
					<?php
					if ( $paymentMethodsCounter % 2 !== 0 || $paymentMethodsCounter >= count( $available_gateways ) - 1 ) {
						echo "</div>";
					}

					$paymentMethodsCounter ++;
					?>
				<?php endforeach; ?>

            </div>
		<?php else: ?>
            <p><?php echo apply_filters( 'woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? esc_html__( 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) : esc_html__( 'Please fill in your details above to see available payment methods.', 'woocommerce' ) ); ?></p>
		<?php endif; ?>
	<?php endif; ?>
</div>