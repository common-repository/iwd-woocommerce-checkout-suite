<?php
/**
 * Log In Checkout form
 *
 * This template can be overridden by copying it to yourtheme/iwd-woocommerce-checkout-suite/public/templates/woocommerce/checkout/form-login.php.
 *
 * @package IWD_OPC/public/templates
 * @since 1.0.0
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Global WP var
global $wp;

if ( is_user_logged_in() ) {
	global $current_user;
	get_currentuserinfo();
}

// IWD OPC Default Configuration.
$is_login_form_enabled = get_option( 'iwd_wc_opc_login_form_enabled' );
?>

<div class="iwd-opc-login-form" id="iwd_opc_login_form">
	<?php if ( ! is_user_logged_in() ): ?>
        <input type="hidden" name="login_redirect" value="<?php echo home_url( $wp->request ) ?>"/>

		<?php if ( $is_login_form_enabled ): ?>
            <p class="iwd-opc-login-form__login-message">
                <a href="#" id="nav-email" class="showlogin iwd_opc_login_toggle">
					<?php _e( 'Log in', 'woocommerce' ); ?>
                </a> <?php _e( 'to your account or continue as a guest and create one after order is placed.', 'woocommerce' ); ?>
            </p>
		<?php endif; ?>

        <div class="row">
            <div class="col-sm-6 col-md-6">
                <p class="form-row form-row-wide validate-required validate-email" id="billing_email_field">
                    <span class="field iwd-opc-field">
                        <input type="email"
                               class="iwd-opc-login-form__login-field iwd-opc-input"
                               name="billing_email"
                               id="billing_email"
                               autocomplete="email username"
                        >
                        <span class="iwd-opc-field__label"><?php _e( 'Email Address *', 'woocommerce' ); ?></span>
                        <span class="iwd-opc-error"><?php _e( 'This field is required', 'woocommerce' ); ?></span>
                    </span>
                </p>
            </div>
        </div>
	<?php else: ?>
        <p class="iwd-opc-login-form__login-message iwd-opc-login-form__login-message--logged">
			<?php _e( 'Hi,', 'woocommerce' ); ?> <a href="<?php echo home_url() . '/my-account/'; ?>"
                                                    id="nav-email"><?php echo esc_attr($current_user->user_email) ; ?>!</a>
        </p>
        <p class="form-row form-row-wide validate-required validate-email" id="billing_email_field"
           style="display: none !important;">
                    <span class="field iwd-opc-field">
                        <input type="email"
                               class="iwd-opc-login-form__login-field iwd-opc-input"
                               name="billing_email"
                               id="billing_email"
                               autocomplete="email username"
                               value="<?php echo esc_attr($current_user->user_email); ?>"
                        >
                        <span class="iwd-opc-field__label"><?php _e( 'Email Address *', 'woocommerce' ); ?></span>
                        <span class="iwd-opc-error"><?php _e( 'This field is required', 'woocommerce' ); ?></span>
                    </span>
        </p>
	<?php endif; ?>

	<?php if ( $is_login_form_enabled && ! is_user_logged_in() ): ?>
        <div id="iwd_opc_login_here_content" style="display:none;">
            <div class="row">
                <div class="col-sm-6 col-md-6">
                    <p class="form-row form-row-wide validate-required" id="password_field">
                        <span class="field iwd-opc-field">
                            <input type="password" class="iwd-opc-input login_form_field" name="password" id="password"
                                   autocomplete="current-password"/>
                            <span class="iwd-opc-field__label"><?php _e( 'Password *', 'woocommerce' ); ?></span>
                            <span class="iwd-opc-error"><?php _e( 'This field is required', 'woocommerce' ); ?></span>
                        </span>
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-6 col-sm-3 col-md-3">
                    <button type="button" id="iwd_login_submit" name="iwd_login_submit"
                            class="iwd-opc-btn"><?php _e( 'Log In', 'woocommerce' ); ?></button>
                </div>
                <div class="col-xs-6 col-sm-3 col-md-3">
                    <button type="button" id="iwd_reset_password" class="iwd-opc-btn"
                            onclick="window.location='<?php echo esc_url( wp_lostpassword_url() ); ?>'"><?php _e( 'Reset', 'woocommerce' ); ?></button>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-md-6">
                    <div id="iwd_opc_login_form_error" class="woocommerce-error" style="display:none"></div>
                </div>
            </div>
        </div>
	<?php endif; ?>
</div>
