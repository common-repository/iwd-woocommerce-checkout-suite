<?php
/**
 * Empty cart page
 *
 *
 * This template can be overridden by copying it to yourtheme/iwd-woocommerce-checkout-suite/public/templates/woocommerce/cart/cart-empty.php.
 *
 * @package IWD_OPC/public/templates
 * @since 1.0.0
 * @version 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

wc_print_notices();

/**
 * @hooked wc_empty_cart_message - 10
 */
do_action( 'woocommerce_cart_is_empty' );
?>

<?php if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
    <div class="iwd-opc-main-wrapper">
        <div class="iwd-opc-empty-cart">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="iwd-opc-empty-cart__title">
							<?php _e( 'Empty Cart', 'woocommerce' ) ?>
                        </h1>
                        <p class="iwd-opc-empty-cart__info">
							<?php _e( 'Looks like there\'s nothing here yet. Add something to your shopping cart to get started.', 'woothemes' ) ?>
                        </p>
                    </div>
                    <div class="col-md-12">
                        <a class="iwd-opc-btn active"
                           href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>"
                           title="Continue Shopping">
							<?php _e( 'Continue Shopping', 'woothemes' ) ?>
                        </a>
                    </div>
                    <div class="col-md-12">
						<?php if ( is_user_logged_in() ) { ?>
                            <a class="iwd-opc-btn"
                               href="<?php echo get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ); ?>"
                               title="<?php _e( 'My Account', 'woothemes' ); ?>"><?php _e( 'My Account', 'woothemes' ); ?></a>
						<?php } else { ?>
                            <a class="iwd-opc-btn"
                               href="<?php echo get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ); ?>"
                               title="<?php _e( 'Login In', 'woothemes' ); ?>"><?php _e( 'Log In', 'woothemes' ); ?></a>
						<?php } ?>
                    </div>
                    <div class="col-md-12 iwd-opc-empty-cart__copyright">
                        <a title="WooCommerce Checkout Suite" target="_blank"
                           href="<?php echo IWD_AGENCY_WC_OPC_PROD_PAGE; ?>">
		                    <?php _e( 'WooCommerce Checkout Suite', 'woothemes' ) ?>
                        </a>
                        <span><?php _e( 'by', 'woothemes' ) ?></span>
                        <a title="IWD Dominate" target="_blank"
                           href="<?php echo IWD_AGENCY_SITE_URL; ?>">
							<?php _e( 'IWD Dominate', 'woothemes' ) ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
