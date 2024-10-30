<?php
/**
 * Additional Fields
 *
 * This template can be overridden by copying it to yourtheme/iwd-woocommerce-checkout-suite/public/templates/woocommerce/checkout/comments.php.
 *
 * @package IWD_OPC/public/templates
 * @since 1.0.0
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

// IWD OPC Default Configuration.
$is_order_notes_field_enabled = get_option( 'iwd_wc_opc_order_notes_field_enabled' );
?>

<div class=iwd_opc_additional_fields">
	<?php do_action( 'woocommerce_before_order_notes', $checkout ); ?>

	<?php if ( $is_order_notes_field_enabled ) : ?>

        <div class="fieldset">
			<?php foreach ( $checkout->get_checkout_fields( 'order' ) as $key => $field ) : ?>
				<?php iwd_wc_opc_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
			<?php endforeach; ?>
        </div>

	<?php endif; ?>

	<?php do_action( 'woocommerce_after_order_notes', $checkout ); ?>
</div>