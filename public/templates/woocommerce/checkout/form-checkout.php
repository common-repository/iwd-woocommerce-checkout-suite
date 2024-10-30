<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/iwd-woocommerce-checkout-suite/public/templates/woocommerce/checkout/form-checkout.php.
 *
 * @package IWD_OPC/public/templates
 * @since 1.0.0
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Print WooCommerce Notices
wc_print_notices();

do_action( 'woocommerce_before_checkout_form', $checkout );


// If checkout registration is disabled and not logged in, the user cannot checkout
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) );

	return;
}

// Check if Quote is Virtual
$isVirtual      = false;
$isVirtualClass = '';
if ( ! WC()->cart->needs_shipping() ) {
	$isVirtual      = true;
	$isVirtualClass = 'is-virtual ';
}

if ( is_user_logged_in() ) {
	global $current_user;
	get_currentuserinfo();
}

$step = get_option( 'iwd_wc_opc_desktop_design' ) === 'multistep'
        || get_option( 'iwd_wc_opc_tablet_design' ) === 'multistep'
        || get_option( 'iwd_wc_opc_mobile_design' ) === 'multistep'
	? 'step-1'
	: '';

$paypalCheckoutCompleted      = WC()->session->get( 'paypal' ) !== null ? WC()->session->get( 'paypal' )->checkout_completed : false;
$paypalCheckoutCompletedClass = $paypalCheckoutCompleted ? 'paypal-checkout-completed ' : '';

$desktopDesign = get_option( 'iwd_wc_opc_desktop_design' ) === 'multistep' && ! $paypalCheckoutCompleted ? 'desktop-multistep ' : '';
$tabletDesign  = get_option( 'iwd_wc_opc_tablet_design' ) === 'multistep' && ! $paypalCheckoutCompleted ? 'tablet-multistep ' : '';
$mobileDesign  = get_option( 'iwd_wc_opc_mobile_design' ) === 'multistep' && ! $paypalCheckoutCompleted ? 'mobile-multistep ' : '';

$isLoggedIn = is_user_logged_in() ? 'loggedIn' : '';

?>

    <style type="text/css">
        @media screen and (max-width: 767px) {
            html {
                margin-top: 0 !important
            }
        }
    </style>

    <div class="iwd-opc-main-wrapper <?php echo esc_attr($desktopDesign . $tabletDesign . $mobileDesign . $isVirtualClass . $paypalCheckoutCompletedClass . $isLoggedIn); ?>">
        <form name="checkout" method="post" id="iwd_opc"
              class="checkout woocommerce-checkout iwd-opc"
              action="<?php echo esc_url( wc_get_checkout_url() ); ?>"
              enctype="multipart/form-data"
              style="display:
          <?php echo ! is_user_logged_in() && get_option( 'iwd_wc_opc_login_before_checkout' ) && ! $paypalCheckoutCompleted
			      ? 'none'
			      : 'block';
		      ?>">

            <div class="container-fluid">
                <div class="row">
                    <div class="iwd-opc-main col-md-8">

                        <div class="row">
                            <div class="col-md-6 col-sm-6">
                                <ul class="iwd-opc-breadcrumbs">
                                    <li class="iwd-opc-breadcrumbs__item active">
                                        <a href="#"
                                           class="iwd-opc-breadcrumbs__link"><?php _e( 'Billing', 'woocommerce' ); ?></a>
                                    </li>
                                    <li class="iwd-opc-breadcrumbs__item">
                                        <a href="#"
                                           class="iwd-opc-breadcrumbs__link"><?php _e( 'Delivery', 'woocommerce' ); ?></a>
                                    </li>
                                    <li class="iwd-opc-breadcrumbs__item">
                                        <a href="#"
                                           class="iwd-opc-breadcrumbs__link"><?php _e( 'Pay &amp; Review', 'woocommerce' ); ?></a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-4 col-md-offset-2 col-sm-4 col-sm-offset-2"><span
                                        class="secured-checkout"><?php _e( 'Secured checkout', 'woocommerce' ); ?></span>
                            </div>
                        </div>

                        <div class="iwd-opc-main-top" id="iwd_opc_top">
                            <h1 class="iwd-opc-main-top__title pull-left"><?php _e( 'Secure Checkout', 'woocommerce' ); ?></h1>
                            <h1 class="iwd-opc-main-top__title iwd-opc-main-top__title--mobile"><?php _e( 'Checkout', 'woocommerce' ); ?></h1>

							<?php if ( ( is_plugin_active( 'woocommerce-gateway-paypal-express-checkout/woocommerce-gateway-paypal-express-checkout.php' ) && ! get_option( 'iwd_wc_opc_login_before_checkout' ) )
							           || is_user_logged_in()
							): ?>
                                <div class="iwd-opc-smart-buttons widget_shopping_cart pull-right" id="iwd_smart_btns">
                                    <div id="woo_pp_ec_button_mini_cart"></div>
                                </div>
							<?php endif; ?>

                            <div class="iwd-opc-clear"></div>

							<?php if ( ! $paypalCheckoutCompleted ): ?>
                                <div class="iwd-opc-main-top__login">
									<?php wc_get_template( 'checkout/form-email-login.php' ); ?>
                                </div>
							<?php endif; ?>
                        </div>

                        <!-- Billing and Shipping fields -->
						<?php if ( $checkout->get_checkout_fields() ) : ?>
							<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
							<?php do_action( 'woocommerce_checkout_billing' ); ?>
							<?php do_action( 'woocommerce_checkout_shipping' ); ?>
							<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
						<?php endif; ?>

                        <!-- Shipping methods -->
						<?php if ( ! $isVirtual ): ?>
							<?php iwd_wc_opc_shipping_methods(); ?>
						<?php endif; ?>

                        <!-- Payment fields -->
						<?php iwd_wc_opc_payment_methods() ?>


                        <div id="mobile-promo"></div>
                        <div id="mobile-place-order"></div>

                        <div class="row">
                            <div class="col-sm-8 col-md-8">
                                <div class="anchors">
                                    <ul class="anchors__list">
                                        <li class="anchors__item">
                                            <div class="anchors__item-title">Email
                                                <?php if (get_option( 'iwd_wc_opc_desktop_design' ) == 'multistep') : ?>
                                                    <a class="anchors__link" href="#nav-email" data-target="step-1"><?php _e( 'Edit', 'woocommerce' ); ?></a>
                                                <?php endif; ?>
                                            </div>
                                            <div class="anchors__item-value">
                                            <span class="anchors__item-email">
                                                <?php
                                                esc_html_e($current_user->user_email ?: '');
                                                ?>
                                            </span>
                                            </div>
                                        </li>
                                        <li class="anchors__item">
                                            <div class="anchors__item-title"><?php _e( 'Ship to', 'woocommerce' ); ?>
                                                <?php if (get_option( 'iwd_wc_opc_desktop_design' ) == 'multistep') : ?>
                                                <a class="anchors__link" href="#ship-to-different-address" data-target="step-1"><?php _e( 'Edit', 'woocommerce' ); ?></a>
                                                <?php endif; ?>
                                            </div>
                                            <div class="anchors__item-value">
                                                <div class="anchors__item-customer">
													<?php
													$firstName = get_user_meta( $current_user->ID, 'first_name' )[0] ?: '';
													$lastName  = get_user_meta( $current_user->ID, 'last_name' )[0] ?: '';
													$separator = $firstName && $lastName ? ' ' : '';

                                                    esc_html_e( $firstName . $separator . $lastName );
													?>
                                                </div>
                                                <div class="anchors__item-street-wrapper">
                                                <span class="anchors__item-apt">
                                                    <?php esc_html_e(get_user_meta( $current_user->ID, 'shipping_address_2' )[0] ?: ''); ?>
                                                </span>
                                                    <span class="anchors__item-street-name">
                                                    <?php esc_html_e( get_user_meta( $current_user->ID, 'shipping_address_1' )[0] ?: ''); ?>
                                                </span>
                                                </div>
                                                <div>
                                                <span class="anchors__item-city">
                                                    <?php
                                                    esc_html_e( get_user_meta( $current_user->ID, 'shipping_city' )[0]
	                                                    ? get_user_meta( $current_user->ID, 'shipping_city' )[0] . ', '
	                                                    : '');
                                                    ?>
                                                </span>
                                                    <span class="anchors__item-state">
                                                    <?php esc_html_e( get_user_meta( $current_user->ID, 'shipping_state' )[0] ?: ''); ?>
                                                </span>
                                                    <span class="anchors__item-postcode">
                                                    <?php esc_html_e( get_user_meta( $current_user->ID, 'shipping_postcode' )[0] ?: ''); ?>
                                                </span>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="anchors__item">
                                            <div class="anchors__item-title"><?php _e( 'Method', 'woocommerce' ); ?>
                                                <?php if (get_option( 'iwd_wc_opc_desktop_design' ) == 'multistep') : ?>
                                                <a class="anchors__link" href="#nav-shipping-method" data-target="step-2"><?php _e( 'Edit', 'woocommerce' ); ?></a>
                                                <?php endif; ?>
                                            </div>
                                            <div class="anchors__item-value">
                                                <div class="anchors__item-shipping-method"></div>
                                                <div class="anchors__item-shipping-price"></div>
                                            </div>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                        </div>

                        <a href="<?php echo get_site_url( null, 'cart' ); ?>"
                           class="edit-cart-link iwd-opc-payment-methods-form__edit-cart">
                            <i class="fa fa-angle-left" aria-hidden="true"></i>
							<?php _e( 'Back to edit shopping cart', 'woocommerce' ); ?>
                        </a>
                    </div>

                    <div class="iwd-opc-sidebar col-md-4 js-iwd-opc-sidebar">
                        <div class="iwd-opc-sidebar-wrapper">
                            <h3 class="iwd-opc-sidebar__tablet-subtitle"><?php _e( 'Order Summary', 'woocommerce' ); ?></h3>
                            <div class="iwd-opc-sidebar__desktop-header">
                                <h3><?php _e( 'Order Summary', 'woocommerce' ); ?></h3>
                                <a href="<?php echo get_site_url( null, 'cart' ); ?>"
                                   class="iwd-opc-sidebar__edit-cart edit-cart-link"><?php _e( 'Edit cart', 'woocommerce' ); ?></a>
                            </div>

							<?php iwd_wc_opc_review_order(); ?>

							<?php iwd_wc_opc_sidebar_tablet_header(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>

		<?php if ( ! is_user_logged_in() && get_option( 'iwd_wc_opc_login_before_checkout' ) && ! $paypalCheckoutCompleted ): ?>
            <div class="login-before-checkout iwd-opc-login-form js-login-before-checkout">
                <div class="login-before-checkout__wrapper">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <h1><?php _e( 'How would you like to check out?', 'woocommerce' ); ?></h1>
                            </div>
                            <div class="col-sm-8 col-sm-offset-2 col-md-offset-0 col-md-5">
                                <input type="hidden" name="login_redirect"
                                       value="<?php echo home_url( $wp->request ) . '/checkout/' ?>"/>
                                <div class="login-before-checkout__inputs-wrapper">
                                    <h2 class="login-before-checkout__have-account">
										<?php _e( 'Already have an account? Login to checkout under your account.', 'woocommerce' ); ?>
                                    </h2>
                                    <div class="login-before-checkout__inputs js-login-before-checkout-inputs">
                                        <p class="form-row form-row-wide validate-required validate-email"
                                           id="billing_email_field2">
                                        <span class="field iwd-opc-field">
                                            <input type="email"
                                                   class="iwd-opc-login-form__login-field iwd-opc-input"
                                                   name="billing_email"
                                                   id="billing_email2"
                                                   autocomplete="email username">
                                            <span class="iwd-opc-field__label"><?php _e( 'Email Address *', 'woocommerce' ); ?></span>
                                            <span class="iwd-opc-error"><?php _e( 'This field is required', 'woocommerce' ); ?></span>
                                        </span>
                                        </p>
                                        <p class="form-row form-row-wide validate-required" id="password_field">
                                        <span class="field iwd-opc-field">
                                            <input type="password" class="iwd-opc-input login_form_field"
                                                   name="password" id="password2" autocomplete="current-password"/>
                                            <span class="iwd-opc-field__label"><?php _e( 'Password *', 'woocommerce' ); ?></span>
                                            <span class="iwd-opc-error"><?php _e( 'This field is required', 'woocommerce' ); ?></span>
                                        </span>
                                        </p>
                                    </div>
                                    <button type="button" id="iwd_login_submit" name="iwd_login_submit"
                                            class="iwd-opc-btn alt iwd-login-submit"><?php _e( 'Login & checkout', 'woocommerce' ); ?></button>
                                    <button type="button"
                                            class="iwd-opc-btn alt iwd-login-submit iwd-login-submit--mobile-toggle"><?php _e( 'Login & checkout', 'woocommerce' ); ?></button>
                                    <div id="iwd_opc_login_form_error" class="woocommerce-error"
                                         style="display:none"></div>
                                </div>
                            </div>

                            <div class="col-md-2 hidden-xs hidden-sm">
                                <div class="login-before-checkout__separator"></div>
                            </div>
                            <p class="login-before-checkout__mobile-title"><?php _e( 'or checkout quickly as a guest and', 'woocommerce' ); ?> </br><?php _e( 'create an account later.', 'woocommerce' ); ?></p>

                            <div class="col-sm-8 col-sm-offset-2 col-md-offset-0 col-md-5">
                                <div class="login-before-checkout__guest-wrapper">
                                    <h2 class="login-before-checkout__guest-title">
										<?php _e( 'Checkout quickly as a guest and create an account later.', 'woocommerce' ); ?>
                                    </h2>
                                    <button type="button" id="checkout_as_guest"
                                            class="iwd-opc-btn js-checkout-as-guest iwd-checkout-as-guest-btn"><?php _e( 'Checkout as guest', 'woocommerce' ); ?></button>
                                    <button type="button"
                                            class="iwd-opc-btn js-checkout-as-guest iwd-checkout-as-guest-btn iwd-checkout-as-guest-btn--mobile-toggle"><?php _e( 'Checkout as guest', 'woocommerce' ); ?></button>
									<?php if ( is_plugin_active( 'woocommerce-gateway-paypal-express-checkout/woocommerce-gateway-paypal-express-checkout.php' ) ): ?>
                                        <p class="or-express-text"><?php _e( 'or express checkout', 'woocommerce' ); ?></p>
                                        <div class="login-before-checkout__express js-login-before-checkout">
                                            <div class="iwd-opc-smart-buttons widget_shopping_cart" id="iwd_smart_btns">
                                                <div id="woo_pp_ec_button_mini_cart"></div>
                                            </div>
                                        </div>
									<?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		<?php endif; ?>
    </div>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>