<?php
/**
 * Checkout Billing Form
 *
 * This template can be overridden by copying it to yourtheme/iwd-woocommerce-checkout-suite/public/templates/woocommerce/checkout/form-billing.php.
 *
 * @package IWD_OPC/public/templates
 * @since 1.0.0
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/** @global WC_Checkout $checkout */
?>

<div class="iwd-opc-billing-form">
	<?php if ( wc_ship_to_billing_address_only() && WC()->cart->needs_shipping() ) : ?>

        <h3 class="iwd-opc-section-title"><?php _e( 'Billing &amp; Shipping', 'woocommerce' ); ?></h3>

	<?php else : ?>

        <h3 class="iwd-opc-section-title"><?php _e( 'Billing Address', 'woocommerce' ); ?></h3>

	<?php endif; ?>

	<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

    <div class="row">
		<?php
		$fields = $checkout->get_checkout_fields( 'billing' );

		foreach ( $fields as $key => $field ) {
			// Remove Email and Company Fields
			if ( $key == 'billing_email' || $key == 'billing_company' ) {
				continue;
			}

			if ( isset( $field['country_field'], $fields[ $field['country_field'] ] ) ) {
				$field['country'] = $checkout->get_value( $field['country_field'] );
			}

			iwd_wc_opc_form_field( $key, $field, $checkout->get_value( $key ) );
		}
		?>
    </div>

	<?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>
	<?php if ( ( ! is_user_logged_in() && $checkout->is_registration_enabled() ) ): ?>
        <div class="iwd_opc_account_fields">
			<?php if ( ! $checkout->is_registration_required() ) : ?>

                <p class="form-row field create-account">
                    <input class="input-checkbox"
                           id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true ) ?>
                           type="checkbox" name="createaccount" value="1"/>
                    <label for="createaccount" class="checkbox">
                        <span><?php _e( 'Create an account?', 'woocommerce' ); ?></span>
                    </label>
                </p>

			<?php endif; ?>

			<?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

			<?php if ( $checkout->get_checkout_fields( 'account' ) ) : ?>

                <div class="create-account">
					<?php foreach ( $checkout->get_checkout_fields( 'account' ) as $key => $field ) : ?>
						<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
					<?php endforeach; ?>
                </div>

			<?php endif; ?>

			<?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>
        </div>
	<?php endif; ?>
</div>
