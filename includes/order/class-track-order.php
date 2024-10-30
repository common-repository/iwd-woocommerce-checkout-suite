<?php

class Iwd_Track_Order{

    const ORDER_ID = 'order_id';
    const SHIPPING_METHOD_TITLE = 'shipping_method_title';
    const SHIPPING_METHOD_CODE = 'shipping_method_code';
    const PAYMENT_METHOD_TITLE = 'payment_method_title';
    const PAYMENT_METHOD_CODE = 'payment_method_code';
    const CURRENCY = 'currency';
    const TOTAL = 'total';
    const STORE_URL = 'store_url';
    const PLATFORM = 'platform_name_version';
    const ORDER_STATUS = 'order_status';
    const CUSTOMER_SESSION_ID = 'customer_session_id';
    const CUSTOMER_EMAIL = 'customer_email';
    const GUEST_CUSTOMER = 'guest_customer';
    const PAYMENT_GATEWAY_MODE = 'payment_gateway_mode';
    const BN_CODE = 'bn_code';
    const PAGE_LAYOUT = 'page_layout';
    const COUPON_CODE = 'coupon_used';
    const TRACK_SYSTEM_URL = 'https://38d21773a4.nxcli.net/orders/save'; //todo: remove later

    public function __construct()
    {
        add_action('woocommerce_thankyou', array( $this, 'track_orders' ));
    }

    /**
     * @param $order_id
     * @return bool|WP_Error
     */
    public function track_orders($order_id )
    {

        $order = wc_get_order( $order_id );
        $shipping_code = '';
        foreach ($order->get_data()['shipping_lines'] as $item){
            $shipping_code = $item->get_method_id();
        }

        $body = array(
            self::ORDER_ID              => $order_id,
            self::SHIPPING_METHOD_TITLE => $order->get_shipping_method(),
            self::SHIPPING_METHOD_CODE  => $shipping_code,
            self::PAYMENT_METHOD_TITLE  => $order->get_payment_method_title(),
            self::PAYMENT_METHOD_CODE   => $order->get_payment_method(),
            self::CURRENCY              => $order->get_order_currency(),
            self::TOTAL                 => $order->get_total(),
            self::STORE_URL             => get_bloginfo('wpurl'),
            self::PLATFORM              => 'Woo OPC',
            self::ORDER_STATUS          => $order->get_status(),
            self::CUSTOMER_SESSION_ID   => WC()->session->get_customer_id(),
            self::CUSTOMER_EMAIL        => $order->get_billing_email(),
            self::GUEST_CUSTOMER        => is_user_logged_in() ? 'NO' : 'YES',
            self::PAYMENT_GATEWAY_MODE  => ' null ',
            self::BN_CODE               => ' null ',
            self::PAGE_LAYOUT           => get_option( 'iwd_wc_opc_desktop_design' ),
            self::COUPON_CODE           => empty($order->get_coupons()) ? 'NO' : 'YES',
        );

        wp_safe_remote_post(
            self::TRACK_SYSTEM_URL,
            $this->buildRequest( $body )
        );

        return true;
    }

    /**
     * Build request
     *
     * @param array $body
     * @return array
     */
    private function buildRequest( array $body ) {
        return array(
            'method'      => 'POST',
            'body'        => $body,
            'timeout'     => 70,
            'httpversion' => '1.1',
        );

    }

}