/**
 *
 *  IWD WooCommerce OPC Script
 *
 *  @package 1.0.1
 *  */

jQuery(
    function ($) {
        "use strict";

        // wc_checkout_params is required to continue, ensure the object exists.
        if (typeof wc_checkout_params === 'undefined') {
            return false;
        }

        $.blockUI.defaults.overlayCSS.cursor = 'default';

        var wc_checkout_form = {
            updateTimer: false,
            dirtyInput: false,
            selectedPaymentMethod: false,
            canplace: false,
            xhr: false,
            $order_review: $( '#order_review' ),
            $checkout_form: $( 'form.checkout' ),
            init: function () {
                $( document.body ).bind( 'update_checkout', this.update_checkout );
                $( document.body ).bind( 'init_checkout', this.init_checkout );

                // Payment methods.
                this.$checkout_form.on( 'click', 'input[name="payment_method"]', this.payment_method_selected );

                if ($( document.body ).hasClass( 'woocommerce-order-pay' )) {
                    this.$order_review.on( 'click', 'input[name="payment_method"]', this.payment_method_selected );
                }

                // Prevent HTML5 validation which can conflict.
                this.$checkout_form.attr( 'novalidate', 'novalidate' );

                // Form submission.
                // this.$checkout_form.on('submit', this.submit_form);.
                this.$checkout_form.on( 'click', '#place_order_iwd', this.submit );

                // Inline validation.
                this.$checkout_form.on( 'input validate change', '.iwd-opc-input, .input-text, select, input:checkbox', this.validate_field );

                // Manual trigger.
                this.$checkout_form.on( 'update', this.trigger_update_checkout );

                // Inputs/selects which update totals.
                this.$checkout_form.on( 'change', 'select.shipping_method, input[name^="shipping_method"], #ship-to-different-address input, .update_totals_on_change select, .update_totals_on_change input[type="radio"], .update_totals_on_change input[type="checkbox"]', this.trigger_update_checkout );
                this.$checkout_form.on( 'change', '.address-field select', this.input_changed );
                this.$checkout_form.on( 'change', '.address-field input.iwd-opc-input, .update_totals_on_change input.iwd-opc-input', this.maybe_input_changed );
                this.$checkout_form.on( 'keydown', '.address-field input.iwd-opc-input, .update_totals_on_change input.iwd-opc-input', this.queue_update_checkout );

                // Address fields.
                this.$checkout_form.on( 'change', '#ship-to-different-address input', this.ship_to_different_address );

                // Trigger events.
                this.$checkout_form.find( '#ship-to-different-address input' ).change();
                this.init_payment_methods();

                // Textareas Autoresizer.
                this.textarea_autoresize();

                this.$checkout_form.on( 'click', '.iwd_opc_login_toggle', this.toggle_login_form );

                // Update on page load.
                if (wc_checkout_params.is_checkout === '1') {
                    $( document.body ).trigger( 'init_checkout' );
                }
                if (wc_checkout_params.option_guest_checkout === 'yes') {
                    $( 'input#createaccount' ).change( this.toggle_create_account ).change();
                }
            },
            init_payment_methods: function () {
                var $payment_methods = $( 'form.woocommerce-checkout' ).find( 'input[name="payment_method"]' );

                // If there was a previously selected method, check that one.
                if (wc_checkout_form.selectedPaymentMethod) {
                    var selectedPaymentMethodBlock = $( '#' + wc_checkout_form.selectedPaymentMethod );
                    selectedPaymentMethodBlock.prop( 'checked', true );
                    selectedPaymentMethodBlock.closest( '.payment-methods-list__item.wc_payment_method' ).addClass( 'checked' );
                }

                // If there are none selected, select the first.
                if (0 === $payment_methods.filter( ':checked' ).length) {
                    $payment_methods.eq( 0 ).prop( 'checked', true );
                    $payment_methods.eq( 0 ).closest( '.payment-methods-list__item.wc_payment_method' ).addClass( 'checked' );
                }

                if ($payment_methods.length > 1) {

                    // Hide open descriptions.
                    $( 'div.payment_box' ).filter( ':visible' ).toggle( 0 );
                }

                // Trigger click event for selected method.
                $payment_methods.filter( ':checked' ).eq( 0 ).trigger( 'click' );
            },
            get_payment_method: function () {
                return wc_checkout_form.$checkout_form.find( 'input[name="payment_method"]:checked' ).val();
            },
            payment_method_selected: function (e) {
                e.stopPropagation();

                if ($( '.payment-methods-list input.input-radio' ).length > 1) {
                    var target_payment_box = $( 'div.payment_box.' + $( this ).attr( 'ID' ) ),
                        is_checked         = $( this ).is( ':checked' );

                    if (is_checked && ! target_payment_box.is( ':visible' )) {
                        $( 'div.payment_box' ).filter( ':visible' ).toggle( 0 );
                        $( '.payment-methods-list__item.wc_payment_method' ).removeClass( 'checked' );

                        if (is_checked) {
                            target_payment_box.toggle( 0 );
                            target_payment_box.closest( '.payment-methods-list__item.wc_payment_method' ).addClass( 'checked' );
                        }
                    }
                } else {
                    $( 'div.payment_box' ).show();
                }

                if ($( this ).data( 'order_button_text' )) {
                    $( '.place-order-btn, .multistep-place-btn' ).text( $( this ).data( 'order_button_text' ) );
                } else {
                    $( '.place-order-btn, .multistep-place-btn' ).text( $( '[name="iwd_opc_place_order"]' ).data( 'value' ) );
                }

                var selectedPaymentMethod = $( '.woocommerce-checkout input[name="payment_method"]:checked' ).attr( 'id' );

                if (selectedPaymentMethod !== wc_checkout_form.selectedPaymentMethod) {
                    $( document.body ).trigger( 'payment_method_selected' );
                }

                wc_checkout_form.selectedPaymentMethod = selectedPaymentMethod;
                if (wc_checkout_form.selectedPaymentMethod == 'payment_method_ppcp-gateway') {
                    $( '.place-order-button-wrapper' ).hide();
                } else {
                    $( '.place-order-button-wrapper' ).show();
                }
            },
            toggle_create_account: function () {
                $( 'div.create-account' ).hide();

                if ($( this ).is( ':checked' )) {
                    // Ensure password is not pre-populated.
                    $( '#account_password' ).val( '' ).change();
                    $( 'div.create-account' ).toggle();
                }
            },
            toggle_login_form: function () {
                $( '#iwd_opc_login_here_content' ).toggle();
            },

            textarea_autoresize: function () {
                $( 'textarea' ).each(
                    function () {
                        this.setAttribute( 'style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;' );
                    }
                ).on(
                    'input',
                    function () {
                        this.style.height = '37px';
                        this.style.height = (this.scrollHeight) + 'px';
                    }
                );
            },
            init_checkout: function () {
                $( '#billing_country, #shipping_country, .country_to_state' ).change();
                $( document.body ).trigger( 'update_checkout' );
            },
            maybe_input_changed: function (e) {
                if (wc_checkout_form.dirtyInput) {
                    wc_checkout_form.input_changed( e );
                }
            },
            input_changed: function (e) {
                wc_checkout_form.dirtyInput = e.target;
                wc_checkout_form.maybe_update_checkout();
            },
            queue_update_checkout: function (e) {
                var code = e.keyCode || e.which || 0;

                if (code === 9) {
                    return true;
                }

                wc_checkout_form.dirtyInput = this;
                wc_checkout_form.reset_update_checkout_timer();
                wc_checkout_form.updateTimer = setTimeout( wc_checkout_form.maybe_update_checkout, '1000' );
            },
            trigger_update_checkout: function () {
                wc_checkout_form.reset_update_checkout_timer();
                wc_checkout_form.dirtyInput = false;
                $( document.body ).trigger( 'update_checkout' );
            },
            maybe_update_checkout: function () {
                var update_totals = true;

                if ($( wc_checkout_form.dirtyInput ).length) {
                    var $required_inputs = $( wc_checkout_form.dirtyInput ).closest( 'div.iwd-opc-billing-form' ).find( '.address-field.validate-required' );

                    if ($required_inputs.length) {
                        $required_inputs.each(
                            function () {
                                if ($( this ).find( 'input.iwd-opc-input' ).val() === '') {
                                    update_totals = false;
                                }
                            }
                        );
                    }
                }
                if (update_totals) {
                    wc_checkout_form.trigger_update_checkout();
                }
            },
            ship_to_different_address: function () {
                $( 'div.shipping_address' ).hide();
                if ($( this ).is( ':checked' )) {
                    $( 'div.shipping_address' ).toggle();
                }
            },
            reset_update_checkout_timer: function () {
                clearTimeout( wc_checkout_form.updateTimer );
            },
            is_valid_json: function (raw_json) {
                try {
                    var json = $.parseJSON( raw_json );

                    return (json && 'object' === typeof json);
                } catch (e) {
                    return false;
                }
            },
            validate_field: function (e) {
                var $this             = $( this ),
                    $parent           = $this.closest( '.form-row' ),
                    validated         = true,
                    validate_required = $parent.is( '.validate-required' ),
                    validate_email    = $parent.is( '.validate-email' ),
                    event_type        = e.type;

                if ('input' === event_type) {
                    $parent.removeClass( 'woocommerce-invalid woocommerce-invalid-required-field woocommerce-invalid-email woocommerce-validated' );
                }

                if ('validate' === event_type || 'change' === event_type) {

                    if (validate_required) {
                        if ('checkbox' === $this.attr( 'type' ) && ! $this.is( ':checked' )) {
                            $parent.removeClass( 'woocommerce-validated' ).addClass( 'woocommerce-invalid woocommerce-invalid-required-field' );
                            validated = false;
                        } else if ($this.val() === '') {
                            $parent.removeClass( 'woocommerce-validated' ).addClass( 'woocommerce-invalid woocommerce-invalid-required-field' );
                            validated = false;
                        }
                    }

                    if (validate_email) {
                        if ($this.val()) {
                            /* https://stackoverflow.com/questions/2855865/jquery-validate-e-mail-address-regex */
                            var pattern = new RegExp( /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i );

                            if ( ! pattern.test( $this.val() )) {
                                $parent.removeClass( 'woocommerce-validated' ).addClass( 'woocommerce-invalid woocommerce-invalid-email' );
                                validated = false;
                            }
                        }
                    }

                    if (validated) {
                        $parent.removeClass( 'woocommerce-invalid woocommerce-invalid-required-field woocommerce-invalid-email' ).addClass( 'woocommerce-validated' );
                    }
                }
            },
            update_checkout: function (event, args) {
                // Small timeout to prevent multiple requests when several fields update at the same time.
                wc_checkout_form.reset_update_checkout_timer();
                wc_checkout_form.updateTimer = setTimeout( wc_checkout_form.update_checkout_action, '5', args );
            },
            update_checkout_action: function (args) {
                if (wc_checkout_form.xhr) {
                    wc_checkout_form.xhr.abort();
                }

                if ($( 'form.checkout' ).length === 0) {
                    return;
                }

                args = typeof args !== 'undefined' ? args : {
                    update_shipping_method: true
                };

                var country          = $( '#billing_country' ).val(),
                    state            = $( '#billing_state' ).val(),
                    postcode         = $( 'input#billing_postcode' ).val(),
                    city             = $( '#billing_city' ).val(),
                    address          = $( 'input#billing_address_1' ).val(),
                    address_2        = $( 'input#billing_address_2' ).val(),
                    s_country        = country,
                    s_state          = state,
                    s_postcode       = postcode,
                    s_city           = city,
                    s_address        = address,
                    s_address_2      = address_2,
                    $required_inputs = $( wc_checkout_form.$checkout_form ).find( '.address-field.validate-required:visible' ),
                    has_full_address = true;

                if ($required_inputs.length) {
                    $required_inputs.each(
                        function () {
                            if ($( this ).find( ':input' ).val() === '') {
                                has_full_address = false;
                            }
                        }
                    );
                }

                if ($( '#ship-to-different-address' ).find( 'input' ).is( ':checked' )) {
                    s_country   = $( '#shipping_country' ).val();
                    s_state     = $( '#shipping_state' ).val();
                    s_postcode  = $( 'input#shipping_postcode' ).val();
                    s_city      = $( '#shipping_city' ).val();
                    s_address   = $( 'input#shipping_address_1' ).val();
                    s_address_2 = $( 'input#shipping_address_2' ).val();
                }

                var data = {
                    security: wc_checkout_params.update_order_review_nonce,
                    payment_method: wc_checkout_form.get_payment_method(),
                    country: country,
                    state: state,
                    postcode: postcode,
                    city: city,
                    address: address,
                    address_2: address_2,
                    s_country: s_country,
                    s_state: s_state,
                    s_postcode: s_postcode,
                    s_city: s_city,
                    s_address: s_address,
                    s_address_2: s_address_2,
                    has_full_address: has_full_address,
                    post_data: $( 'form.checkout' ).serialize()
                };

                if (false !== args.update_shipping_method) {
                    var shipping_methods = {};

                    $( 'select.shipping_method, input[name^="shipping_method"][type="radio"]:checked, input[name^="shipping_method"][type="hidden"]' ).each(
                        function () {
                            shipping_methods[$( this ).data( 'index' )] = $( this ).val();
                        }
                    );

                    data.shipping_method = shipping_methods;
                }

                $( '.iwd-opc-payment-methods-form__wrapper, .iwd-opc-shipping-methods-form, .iwd-opc-discount ' ).block(
                    {
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    }
                );

                wc_checkout_form.xhr = $.ajax(
                    {
                        type: 'POST',
                        url: wc_checkout_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'iwd_opc_update_order_review' ),
                        data: data,
                        success: function (data) {

                            // Reload the page if requested.
                            if (true === data.reload) {
                                window.location.reload();
                                return;
                            }

                            // Remove any notices added previously.
                            $( '.woocommerce-NoticeGroup-updateOrderReview' ).remove();

                            var termsCheckBoxChecked = $( '#terms' ).prop( 'checked' );

                            // Save payment details to a temporary object.
                            var paymentDetails = {};
                            $( '.payment_box :input' ).each(
                                function () {
                                    var ID = $( this ).attr( 'id' );

                                    if (ID) {
                                        if ($.inArray( $( this ).attr( 'type' ), ['checkbox', 'radio'] ) !== -1) {
                                            paymentDetails[ID] = $( this ).prop( 'checked' );
                                        } else {
                                            paymentDetails[ID] = $( this ).val();
                                        }
                                    }
                                }
                            );

                            // Always update the fragments.
                            if (data && data.fragments) {
                                $.each(
                                    data.fragments,
                                    function (key, value) {
                                        $( key ).replaceWith( value );
                                        $( key ).unblock();

                                        if (key === '.iwd-opc-review-form') {
                                            if ( $.cookie( 'coupon_opened' ) ) {
                                                $( '.iwd-opc-sidebar .iwd-opc-discount' ).addClass( 'opened' );
                                                $.removeCookie( 'coupon_opened' );
                                            }

                                            if ($.cookie( 'coupon_code_message' )) {
                                                $( '.iwd-opc-discount__form' ).prepend( $.cookie( 'coupon_code_message' ) );
                                                $.removeCookie( 'coupon_code_message' );
                                                updateSidebarSticky();
                                                /* Remove message after 5s */
                                                setTimeout(
                                                    function () {
                                                        $( '.iwd-opc-discount__form' ).find( '.woocommerce-message, .woocommerce-error' ).remove();
                                                        updateSidebarSticky();
                                                    },
                                                    5000
                                                );
                                            }

                                            if ($( '#mobile-promo .iwd-opc-discount' ).length) {
                                                $( '#mobile-promo .iwd-opc-discount' ).unblock();
                                            }

                                            positionForOpenedSummaryBar();
                                        }

                                        if (key === '.iwd-opc-shipping-methods-form') {
                                            $( 'input[id^=shipping_method_]' ).on(
                                                'change',
                                                function () {
                                                    var shippingMethodName = $( this )
                                                        .next( 'label' )
                                                        .clone()    // clone the element.
                                                        .children() // select all the children.
                                                        .remove()   // remove all the children.
                                                        .end()      // again go back to selected element.
                                                        .text();    // get only text.

                                                    var shippingMethodPrice = $( this )
                                                        .next( 'label' )
                                                        .find( '.woocommerce-Price-amount.amount' )
                                                        .text();

                                                    $( '.anchors__item-shipping-method' ).html( shippingMethodName );
                                                    $( '.anchors__item-shipping-price' ).html( shippingMethodPrice );
                                                }
                                            );

                                            $( '.multistep-delivery-btn' ).on(
                                                'click',
                                                function () {
                                                    $( this ).closest( '.iwd-opc-main-wrapper' ).removeClass( 'step-2' ).addClass( 'step-3' );
                                                    copyPromoAndPlaceOrderOnMobile();
                                                    hideEmailAnchor();
                                                }
                                            );

                                            $( '.iwd-opc-shipping-methods-form__back' ).on(
                                                'click',
                                                function (e) {
                                                    e.preventDefault();
                                                    $( this ).closest( '.iwd-opc-main-wrapper' ).removeClass( 'step-2' ).addClass( 'step-1' );
                                                    hideEmailAnchor();
                                                }
                                            );
                                        }

                                        if (key === '.iwd-opc-sidebar__tablet-header') {
                                            // Sticky.
                                            var scrollTop = 0;

                                            $( '.js-summary-tablet-header' ).on(
                                                'click',
                                                function () {
                                                    var parent = $( this ).closest( '.js-iwd-opc-sidebar' );

                                                    if ( ! parent.hasClass( 'open' )) {
                                                        scrollTop = $( document ).scrollTop();
                                                    }

                                                    if (parent.hasClass( 'open' )) {
                                                        $( 'body' ).css( 'position', 'static' );
                                                        $( document ).scrollTop( scrollTop );
                                                        parent.removeClass( 'open' );
                                                        parent.attr( 'style', '' );
                                                    } else {
                                                        $( 'body' ).css( 'position', 'fixed' )
                                                            .css( 'left', 0 )
                                                            .css( 'right', 0 )
                                                            .css( 'height', 'auto' );
                                                        parent.addClass( 'open' );

                                                        var offsetTop = getHeaderHeight();
                                                        offsetTop    += getAdminBarHeight();
                                                        parent.css( 'top', offsetTop + 'px' );
                                                    }

                                                    positionForOpenedSummaryBar();
                                                }
                                            );
                                        }

                                        $( '.iwd-opc-payment-methods-form__back' ).on(
                                            'click',
                                            function (e) {
                                                e.preventDefault();
                                                var step = $( '.is-virtual' ).length ? 'step-1' : 'step-2';
                                                $( this ).closest( '.iwd-opc-main-wrapper' ).removeClass( 'step-3' ).addClass( step );
                                                hideEmailAnchor();
                                            }
                                        );
                                    }
                                );
                            }

                            // Recheck the terms and conditions box, if needediwd_opc_login_form.
                            if (termsCheckBoxChecked) {
                                $( '#terms' ).prop( 'checked', true );
                            }

                            // Fill in the payment details if possible without overwriting data if set.
                            if ( ! $.isEmptyObject( paymentDetails )) {
                                $( '.payment_box :input' ).each(
                                    function () {
                                        var ID = $( this ).attr( 'id' );

                                        if (ID) {
                                            if ($.inArray( $( this ).attr( 'type' ), ['checkbox', 'radio'] ) !== -1) {
                                                $( this ).prop( 'checked', paymentDetails[ID] ).change();
                                            } else if (0 === $( this ).val().length) {
                                                $( this ).val( paymentDetails[ID] ).change();
                                            }
                                        }
                                    }
                                );
                            }

                            // Check for error.
                            if ('failure' === data.result) {

                                var $form = $( 'form.checkout' );

                                // Remove notices from all sources.
                                // $( '.woocommerce-error, .woocommerce-message' ).remove();.

                                // Add new errors returned by this event.
                                if (data.messages) {
                                    $form.prepend( '<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-updateOrderReview">' + data.messages + '</div>' );
                                } else {
                                    $form.prepend( data );
                                }

                                // Lose focus for all fields.
                                $form.find( '.iwd-opc-input, select, input:checkbox' ).trigger( 'validate' ).blur();

                                wc_checkout_form.scroll_to_notices();
                            }

                            // Re-init methods.
                            wc_checkout_form.init_payment_methods();

                            // Clear PayPal Quick buttons buttons before update.
                            $( '#woo_pp_ec_button_checkout > div[id^="xcomponent-paypal-button"]' ).empty();

                            // Fire updated_checkout event.
                            $( document.body ).trigger( 'updated_checkout', [data] );
                        }

                    }
                );

                updateSidebarSticky();
            },
            listener: function (){
                document.querySelector( '#place_order' ).addEventListener(
                    'click',
                    () => {wc_checkout_form.canplace = true;$( 'form.checkout' ).submit();
                    }
                )
            },
            submit_form: function (event){
                event.preventDefault();
            },
            submit: function (event) {
                event.preventDefault();
                wc_checkout_form.reset_update_checkout_timer();
                var $form = $( 'form.checkout' );
                if (wc_checkout_form.selectedPaymentMethod == "payment_method_ppcp-credit-card-gateway") {
                    wc_checkout_form.listener();
                    if (wc_checkout_form.canplace === false) {
                        $( '#ppcp-hosted-fields button' ).click();
                        return false;
                    } else {
                        wc_checkout_form.$checkout_form.submit();
                    }

                }
                $form.find( '#password_field' ).removeClass( 'validate-required' );

                if ($form.is( '.processing' )) {
                    return false;
                }

                // Trigger a handler to let gateways manipulate the checkout if needed.
                if ($form.triggerHandler( 'checkout_place_order' ) !== false && $form.triggerHandler( 'checkout_place_order_' + wc_checkout_form.get_payment_method() ) !== false) {

                    $form.addClass( 'processing' );

                    var form_data = $form.data();

                    if (1 !== form_data['blockUI.isBlocked']) {
                        $form.block(
                            {
                                message: null,
                                overlayCSS: {
                                    background: '#fff',
                                    opacity: 0.6
                                }
                            }
                        );
                    }

                    // ajaxSetup is global, but we use it to ensure JSON is valid once returned.
                    $.ajaxSetup(
                        {
                            dataFilter: function (raw_response, dataType) {
                                // We only want to work with JSON.
                                if ('json' !== dataType) {
                                    return raw_response;
                                }

                                if (wc_checkout_form.is_valid_json( raw_response )) {
                                    return raw_response;
                                } else {
                                    // Attempt to fix the malformed JSON.
                                    var maybe_valid_json = raw_response.match( /{"result.*}/ );

                                    if (null === maybe_valid_json) {
                                        console.log( 'Unable to fix malformed JSON' );
                                    } else if (wc_checkout_form.is_valid_json( maybe_valid_json[0] )) {
                                        console.log( 'Fixed malformed JSON. Original:' );
                                        console.log( raw_response );
                                        raw_response = maybe_valid_json[0];
                                    } else {
                                        console.log( 'Unable to fix malformed JSON' );
                                    }
                                }

                                return raw_response;
                            }
                        }
                    );

                    $.ajax(
                        {
                            type: 'POST',
                            url: 'http://110e5ae7c4.nxcli.net/opc' + wc_checkout_params.checkout_url,
                            data: $form.serialize(),
                            dataType: 'json',
                            success: function (result) {
                                try {
                                    if ('success' === result.result) {
                                        if (-1 === result.redirect.indexOf( 'https://' ) || -1 === result.redirect.indexOf( 'http://' )) {
                                            window.location = result.redirect;
                                        } else {
                                            window.location = decodeURI( result.redirect );
                                        }
                                    } else if ('failure' === result.result) {
                                        throw 'Result failure';
                                    } else {
                                        throw 'Invalid response';
                                    }
                                } catch (err) {
                                    // Reload page.
                                    if (true === result.reload) {
                                        window.location.reload();
                                        return;
                                    }

                                    // Trigger update in case we need a fresh nonce.
                                    if (true === result.refresh) {
                                        $( document.body ).trigger( 'update_checkout' );
                                    }

                                    // Add new errors.
                                    if (result.messages) {
                                        wc_checkout_form.submit_error( result.messages );
                                    } else {
                                        wc_checkout_form.submit_error( '<div class="woocommerce-error">' + wc_checkout_params.i18n_checkout_error + '</div>' );
                                    }
                                }
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                wc_checkout_form.submit_error( '<div class="woocommerce-error">' + errorThrown + '</div>' );
                            }
                        }
                    );
                }

                return false;
            },
            submit_error: function (error_message) {
                $( '.woocommerce-NoticeGroup-checkout, .woocommerce-error, .woocommerce-message' ).remove();
                $( '.iwd-opc-main' ).prepend( '<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout">' + error_message + '</div>' );
                wc_checkout_form.$checkout_form.removeClass( 'processing' ).unblock();
                wc_checkout_form.$checkout_form.find( '.iwd-opc-input, .input-text, select, input:checkbox' ).trigger( 'validate' ).blur();
                wc_checkout_form.scroll_to_notices();
                $( document.body ).trigger( 'checkout_error' );
            },
            scroll_to_notices: function () {
                var scrollElement = $( '.woocommerce-NoticeGroup-updateOrderReview, .woocommerce-NoticeGroup-checkout' );

                if ( ! scrollElement.length) {
                    scrollElement = $( '.form.checkout' );
                }
                $.scroll_to_notices( scrollElement );
            }
        };

        var wc_checkout_coupons = {
            init: function () {
                $( document.body ).on( 'click', '.iwd-opc-sidebar .iwd-opc-discount__title-wrapper', this.show_coupon_form );
                $( document.body ).on( 'click', '.woocommerce-remove-coupon', this.remove_coupon );
                $( document.body ).on( 'click', '[name="iwd_apply_discount"]', this.submit );
            },

            show_coupon_form: function () {
                $( '.iwd-opc-sidebar .iwd-opc-discount' ).toggleClass( 'opened' );
                // $('.iwd-opc-sidebar .iwd-opc-discount__form-input').focus();.
                if ($( '.iwd-opc-sidebar-wrapper' ).data( 'stickySidebar' )) {
                    $( '.iwd-opc-sidebar-wrapper' ).data( 'stickySidebar' ).updateSticky();
                }
            },

            submit: function (e) {
                e.preventDefault();

                var $couponBlock = $( e.target ).closest( '[data-id="iwd_checkout_coupon_block"]' );

                if ($couponBlock.closest( '.iwd-opc-discount' ).is( '.processing' )) {
                    return false;
                }

                $couponBlock.closest( '.iwd-opc-discount' ).addClass( 'processing' ).block(
                    {
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    }
                );

                $( '.iwd-opc-review-totals-wrapper' ).addClass( 'processing' ).block(
                    {
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    }
                );

                var data = {
                    security: wc_checkout_params.apply_coupon_nonce,
                    coupon_code: $couponBlock.find( 'input[name="coupon_code"]' ).val()
                };

                /* Set cookie only for form in sidebar */
                if ( ! $( e.target ).closest( '#mobile-promo' ).length) {
                    $.cookie( 'coupon_opened', true );
                }

                $.ajax(
                    {
                        type: 'POST',
                        url: wc_checkout_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'apply_coupon' ),
                        data: data,
                        success: function (code) {
                            $( '.woocommerce-error, .woocommerce-message' ).remove();
                            $couponBlock.closest( '.iwd-opc-discount' ).removeClass( 'processing' ).unblock();
                            $( '.iwd-opc-review-totals-wrapper' ).removeClass( 'processing' ).unblock();

                            if (code) {
                                $.cookie( 'coupon_code_message', code );
                                $( document.body ).trigger( 'update_checkout', {update_shipping_method: false} );
                            }

                            updateSidebarSticky();
                        },
                        dataType: 'html'
                    }
                );

                return false;
            },

            remove_coupon: function (e) {
                e.preventDefault();

                var container = $( this ).parents( '.woocommerce-checkout-review-order' ),
                    coupon    = $( this ).data( 'coupon' );

                $( '.iwd-opc-review-totals-wrapper' ).addClass( 'processing' ).block(
                    {
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    }
                );

                var data = {
                    security: wc_checkout_params.remove_coupon_nonce,
                    coupon: coupon
                };

                /* Set cookie only for form in sidebar */
                if ( ! $( e.target ).closest( '#mobile-promo' ).length) {
                    $.cookie( 'coupon_opened', true );
                }

                $.ajax(
                    {
                        type: 'POST',
                        url: wc_checkout_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'remove_coupon' ),
                        data: data,
                        success: function (code) {
                            $( '.woocommerce-error, .woocommerce-message' ).remove();
                            $( '.iwd-opc-review-totals-wrapper' ).removeClass( 'processing' ).unblock();

                            if (code) {
                                $.cookie( 'coupon_code_message', code );

                                $( document.body ).trigger( 'update_checkout', {update_shipping_method: false} );

                                // Remove coupon code from coupon field.
                                $( 'form.checkout_coupon' ).find( 'input[name="coupon_code"]' ).val( '' );
                            }
                        },
                        error: function (jqXHR) {
                            if (wc_checkout_params.debug_mode) {
                                /* jshint devel: true */
                                console.log( jqXHR.responseText );
                            }
                        },
                        dataType: 'html'
                    }
                );
            }
        };

        var wc_checkout_login_form = {
            init: function () {
                $( document.body ).on( 'click', '[name="iwd_login_submit"]', this.submit );

                $( document.body ).on( 'click', 'a.showlogin', this.show_login_form );
            },
            submit: function () {
                var container      = $( this ).closest( '.iwd-opc-login-form' ),
                    username       = container.find( 'input[type=email]' ),
                    password       = container.find( 'input[type=password]' ),
                    login_redirect = container.find( 'input[name=login_redirect]' );

                if ( ! password.closest( '.form-row' ).is( '.validate-required' )) {
                    password.closest( '.form-row' ).addClass( 'validate-required' );
                }

                if ( ! wc_checkout_login_form.validate_login_fields( container )) {
                    return;
                }

                container.addClass( 'processing' ).block(
                    {
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    }
                );

                var data = {
                    'username': username.val(),
                    'password': password.val(),
                };

                $.post(
                    {
                        url: wc_checkout_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'iwd_opc_customer_login' ),
                        data: data,
                        success: function (data) {
                            if (data['error'] === true) {
                                container.unblock();
                                container.find( '#iwd_opc_login_form_error' ).html( data['message'] ).show();
                                var inputs = $( '.iwd-opc-field input, .iwd-opc-field--select input' );

                                $( inputs ).each(
                                    function (index, element) {
                                        if ($( element ) && ! $( element ).is( ":hidden" )) {
                                            if ($( element ).val() && $( element ).val().length) {
                                                $( element ).next().addClass( 'filed' );
                                            } else {
                                                $( element ).next().removeClass( 'filed' );
                                            }
                                        }
                                    }
                                );
                            } else {
                                document.location.href = login_redirect.val();
                            }
                        }
                    }
                );
            },
            validate_login_fields: function (container) {
                var $login_fields = container.find( 'input.iwd-opc-login-form__login-field' ),
                    validated     = true;

                $login_fields.each(
                    function(){
                        var $this             = $( this ),
                            $parent           = $this.closest( '.form-row' ),
                            validate_required = $parent.is( '.validate-required' );

                        if (validate_required) {
                            if ($this.val() === '') {
                                $parent.removeClass( 'woocommerce-validated' ).addClass( 'woocommerce-invalid woocommerce-invalid-required-field' );
                                validated = false;
                            }
                        }

                        if (validated) {
                            $parent.removeClass( 'woocommerce-invalid woocommerce-invalid-required-field woocommerce-invalid-email' ).addClass( 'woocommerce-validated' );
                        }
                    }
                );

                return validated;
            },
            show_login_form: function () {
                $( 'form.login, form.woocommerce-form--login' ).toggle();
                return false;
            }
        };

        var wc_terms_toggle = {
            init: function () {
                $( document.body ).on( 'click', 'a.woocommerce-terms-and-conditions-link', this.toggle_terms );
            },

            toggle_terms: function () {
                if ($( '.woocommerce-terms-and-conditions' ).length) {
                    $( '.woocommerce-terms-and-conditions' ).toggle(
                        function () {
                            var link_toggle = $( '.woocommerce-terms-and-conditions-link' );

                            if ($( '.woocommerce-terms-and-conditions' ).is( ':visible' )) {
                                link_toggle.addClass( 'woocommerce-terms-and-conditions-link--open' );
                                link_toggle.removeClass( 'woocommerce-terms-and-conditions-link--closed' );
                            } else {
                                link_toggle.removeClass( 'woocommerce-terms-and-conditions-link--open' );
                                link_toggle.addClass( 'woocommerce-terms-and-conditions-link--closed' );
                            }
                        }
                    );

                    return false;
                }
            }
        };

        wc_checkout_form.init();
        wc_checkout_coupons.init();
        wc_checkout_login_form.init();
        wc_terms_toggle.init();

        /*=========================== REDESIGN ===========================*/
        var isMobile            = $( window ).width() <= 767,
            isTablet            = $( window ).width() >= 768 && $( window ).width() < 992,
            isDesktop           = $( window ).width() >= 992,
            isDesktopMultistep  = $( '.desktop-multistep' ).length,
            isTabletMultistep   = $( '.tablet-multistep' ).length,
            isMobileMultistep   = $( '.mobile-multistep' ).length,
            isLoggedIn          = $( '.loggedIn' ).length,
            mainWrapper         = $( '.iwd-opc-main-wrapper' ),
            sidebarSticky       = $( '.iwd-opc-sidebar-wrapper' ),
            headerHeight        = $( '#page > header' ).outerHeight(),
            sidebar             = $( ".js-iwd-opc-sidebar" ),
            inputs              = $( '.iwd-opc-field input, .iwd-opc-field--select input' ),
            anchorsDataProvider = "#billing";

        /* Pull up labels for input */
        function toggleInput(element) {
            if ($( element ) && ! $( element ).is( ":hidden" )) {
                if ($( element ).val() && $( element ).val().length) {
                    $( element ).next().addClass( 'filed' );
                } else {
                    $( element ).next().removeClass( 'filed' );
                }
            }
        }

        /* Get header height */
        function getHeaderHeight () {
            return $( '#page > header' ).length ? $( '#page > header' ).outerHeight() : 0;
        }

        /* Get adminbar height if exist*/
        function getAdminBarHeight() {
            return $( '#wpadminbar' ).length && $( '#wpadminbar' ).is( ":visible" ) ? $( '#wpadminbar' ).outerHeight() : 0;
        }

        /* Calculate position for sidebar on tablet/mobile */
        function positionForOpenedSummaryBar() {
            if (isTablet || isMobile) {
                var reviewFormHeight = $( '.site-header' ).outerHeight() + $( '.js-summary-tablet-header' ).outerHeight() + $( '.iwd-opc-sidebar__tablet-subtitle' ).outerHeight() + 40;
                reviewFormHeight    += getAdminBarHeight();
                $( '.js-iwd-opc-review-form' ).css( 'max-height', 'calc(100vh - ' + reviewFormHeight + 'px)' );
                $( '.js-iwd-opc-review-form' ).find( '[name="coupon_code"]' ).focus();
            }
        }

        /* Sticky sidebar init */
        function setStickySidebar() {
            sidebarSticky.stickySidebar(
                {
                    topSpacing: $( '#wpadminbar' ).length && $( '#wpadminbar' ).is( ":visible" ) ? $( '#wpadminbar' ).outerHeight() : 0,
                    bottomSpacing: 100,
                    containerSelector: '#iwd_opc'
                }
            );
        }

        /* Copy place order button and coupon block form  from sidebar*/
        function copyPromoAndPlaceOrderOnMobile() {
            if (isMobile || isTablet) {
                var sidebarPromo = $( '.iwd-opc-sidebar .iwd-opc-discount' ),
                    mainPromo    = $( "#mobile-promo" ),
                    placeOrder   = $( '.iwd-opc-sidebar .iwd-opc-place-order-wrapper' );

                if ( ! mainPromo.find( '.iwd-opc-discount' ).length) {
                    sidebarPromo.clone().appendTo( '#mobile-promo' );
                    mainPromo.find( '.iwd-opc-discount' ).addClass( 'opened' );
                    mainPromo.find( '#coupon_code' ).attr( 'id', mainPromo.find( '#coupon_code' ).attr( 'id' ) + '2' );
                }

                if ( ! $( "#mobile-place-order .iwd-opc-place-order-wrapper" ).length) {
                    placeOrder.clone().appendTo( '#mobile-place-order' );
                    placeOrder.find( '#woocommerce-process-checkout-nonce' ).remove();
                }
            }
        }

        /* Get fullname for customer */
        function getFullName() {
            var firstName = $( anchorsDataProvider + "_first_name" ).length ? $( anchorsDataProvider + "_first_name" ).val() : '',
                lastName  = $( anchorsDataProvider + "_last_name" ).length ? $( anchorsDataProvider + "_last_name" ).val() : '',
                separator = firstName.length === 0 || lastName.length === 0 ? '' : ' ';

            return firstName + separator + lastName;
        }

        /* Init anchors section */
        function anchorsInit() {
            if ($( "*[class^='anchors']" ).length === 0) {
                return;
            }

            /* Email */
            if ( ! $( '.anchors__item-email' ).html().length) {
                var email = $( anchorsDataProvider + '_email' ).val();
                $( '.anchors__item-email' ).html( email );
            }

            /* Fullname */
            $( '.anchors__item-customer' ).html( getFullName() );

            /* Address */
            var address2 = $( anchorsDataProvider + '_address_2' ).length ? $( anchorsDataProvider + '_address_2' ).val() : '';
            $( '.anchors__item-apt' ).html( address2 );

            var address1 = $( anchorsDataProvider + '_address_1' ).length ? $( anchorsDataProvider + '_address_1' ).val() : '';
            $( '.anchors__item-street-name' ).html( address1 );

            var city = $( anchorsDataProvider + '_city' ).length ? $( anchorsDataProvider + '_city' ).val() : '';
            $( '.anchors__item-city' ).html( city );

            var code = '';
            if ($( anchorsDataProvider + '_state' ).length && $( anchorsDataProvider + '_state' ).is( "select" )) {
                code = $( anchorsDataProvider + '_state' ).select2( 'data' )[0]['id'];
            } else {
                code = $( anchorsDataProvider + '_state' ).length ? $( anchorsDataProvider + '_state' ).val() : '';
            }

            $( '.anchors__item-state' ).html( code );

            var postCode = $( anchorsDataProvider + '_postcode' ).length ? $( anchorsDataProvider + '_postcode' ).val() : '';
            $( '.anchors__item-postcode' ).html( postCode );

        }

        /* Set event listeners for anchors */
        function setAnchorsDataEvents() {
            $( anchorsDataProvider + '_email' ).on(
                'change',
                function () {
                    $( '.anchors__item-email' ).html( $( this ).val() );
                }
            );

            $( anchorsDataProvider + "_first_name, " + anchorsDataProvider + "_last_name" ).on(
                'change',
                function () {
                    /* Fullname */
                    $( '.anchors__item-customer' ).html( getFullName() );
                }
            );

            $( anchorsDataProvider + '_address_2' ).on(
                'change',
                function () {
                    $( '.anchors__item-apt' ).html( $( this ).val() );
                }
            );

            $( anchorsDataProvider + '_address_1' ).on(
                'change',
                function () {
                    $( '.anchors__item-street-name' ).html( $( this ).val() );
                }
            );

            $( anchorsDataProvider + '_city' ).on(
                'change',
                function () {
                    $( '.anchors__item-city' ).html( $( this ).val() + ', ' );
                }
            );

            if ($( anchorsDataProvider + '_state' ).is( "select" )) {
                $( anchorsDataProvider + '_state' ).on(
                    'select2:select',
                    function () {
                        var code = $( anchorsDataProvider + '_state' ).select2( 'data' )[0]['id'];
                        $( '.anchors__item-state' ).html( code );
                    }
                );
            } else {
                $( anchorsDataProvider + '_state' ).on(
                    'change',
                    function () {
                        var code = $( anchorsDataProvider + '_state' ).val();
                        $( '.anchors__item-state' ).html( code );
                    }
                );
            }

            $( anchorsDataProvider + '_postcode' ).on(
                'change',
                function () {
                    $( '.anchors__item-postcode' ).html( $( this ).val() );
                }
            );
        }

        /* Delete event listeners for anchors */
        function deleteAnchorsEvents() {
            $( anchorsDataProvider + "_first_name, " + anchorsDataProvider + "_last_name" ).off();
            $( anchorsDataProvider + '_address_2' ).off();
            $( anchorsDataProvider + '_address_1' ).off();
            $( anchorsDataProvider + '_city' ).off();
            $( anchorsDataProvider + '_state' ).off();
            $( anchorsDataProvider + '_postcode' ).off();
        }

        /* Init first step for multistep template */
        function initStep() {
            if ((isDesktop && isDesktopMultistep) || (isTablet && isTabletMultistep) || (isMobile && isMobileMultistep)) {
                mainWrapper.addClass( 'step-1' );
            } else {
                mainWrapper.removeClass(
                    function (index, className) {
                        return (className.match( /(^|\s)step-\d+/g ) || []).join( ' ' );
                    }
                );
            }
        }

        /* Update position for sidebar */
        function updateSidebarSticky() {
            if (sidebarSticky && sidebarSticky.data( 'stickySidebar' )) {
                sidebarSticky.data( 'stickySidebar' ).updateSticky();
            }
        }

        /* Make sidebar not sticky */
        function destroySidebarSticky() {
            if (sidebarSticky && sidebarSticky.data( 'stickySidebar' )) {
                sidebarSticky.data( 'stickySidebar' ).destroy();
            }
        }

        /* Get IOS version */
        function iOSversion() {
            if (/iP(hone|od|ad)/.test( navigator.platform )) {
                // supports iOS 2.0 and later: <http://bit.ly/TJjs1V>.
                var v = (navigator.appVersion).match( /OS (\d+)_(\d+)_?(\d+)?/ );
                return [parseInt( v[1], 10 ), parseInt( v[2], 10 ), parseInt( v[3] || 0, 10 )];
            }
        }

        /* Hide email anchor if user is logged in */
        function hideEmailAnchor() {
            if (isLoggedIn && ((isDesktop && isDesktopMultistep) || (isTablet && isTabletMultistep) || (isMobile && isMobileMultistep)) && $( '.iwd-opc-main-wrapper' ).hasClass( 'step-1' )) {
                $( '.anchors' ).hide();
            } else {
                $( '.anchors' ).show();
            }
        }

        positionForOpenedSummaryBar();
        /* Remove invalid class on page load if state are not selected */
        $( '.form-row.form-row-wide.address-field.validate-required.validate-state' ).removeClass( 'woocommerce-invalid woocommerce-invalid-required-field' );

        /* Init inputs if we have some of information after page loaded*/
        $( inputs ).each(
            function (index, element) {
                toggleInput( element );
            }
        );

        /* Add event listeners for inputs */
        $( inputs ).each(
            function (index, element) {
                $( element ).on(
                    'change keyup focusin',
                    function () {
                        toggleInput( element );
                    }
                );
            }
        );

        $( '.iwd-opc-field__label' ).on(
            'click',
            function () {
                $( this ).prev( 'input' ).focus();
            }
        );

        /* Catch autofill event */
        $( 'input' ).on(
            'animationstart',
            function (e) {
                var target        = e.target;
                var animationName = e.originalEvent.animationName;

                if (animationName === 'onAutoFillStart') {
                    $( target ).next().addClass( 'filed' );
                    if ($( target ).attr( 'name' ) === 'billing_email' && $( '.anchors__item-email' ).length) {
                        $( '.anchors__item-email' ).html( $( target ).val() );
                    }
                }
            }
        );

        /* Set sticky sidebar only for desktop */
        if (isDesktop) {
            setStickySidebar();
        }

        copyPromoAndPlaceOrderOnMobile();

        $( window ).on(
            'resize',
            function () {
                isMobile  = $( window ).width() <= 767;
                isTablet  = $( window ).width() >= 768 && $( window ).width() < 992;
                isDesktop = $( window ).width() >= 992;

                positionForOpenedSummaryBar();
                copyPromoAndPlaceOrderOnMobile();
                hideEmailAnchor();

                headerHeight = getHeaderHeight() + getAdminBarHeight();

                /* When header height changed - change position for sidebar */
                $( '.js-iwd-opc-sidebar.open' ).css( 'top', headerHeight + 'px' );

                if (isDesktop) {
                    $( 'body' ).css( 'position', 'static' );
                    sidebar.css( 'top', 0 );
                    sidebar.removeClass( 'open' );
                    setStickySidebar();
                    updateSidebarSticky();

                    if ( ! mainWrapper.hasClass( 'desktop-multistep' )) {
                        mainWrapper.removeClass(
                            function (index, className) {
                                return (className.match( /(^|\s)step-\d+/g ) || []).join( ' ' );
                            }
                        );
                    } else {
                        mainWrapper.addClass( 'step-1' );
                    }
                }

                if (isTablet) {
                    $( 'body' ).css( 'position', 'static' );
                    if ( ! mainWrapper.hasClass( 'tablet-multistep' )) {
                        mainWrapper.removeClass(
                            function (index, className) {
                                return (className.match( /(^|\s)step-\d+/g ) || []).join( ' ' );
                            }
                        );
                    } else {
                        mainWrapper.removeClass(
                            function (index, className) {
                                return (className.match( /(^|\s)step-\d+/g ) || []).join( ' ' );
                            }
                        );

                        mainWrapper.addClass( 'step-1' );
                    }
                }

                if (isMobile || isTablet) {
                    destroySidebarSticky();

                    if (sidebar.hasClass( 'open' )) {
                        var offsetTop = getHeaderHeight();
                        offsetTop    += getAdminBarHeight();
                        sidebar.css( 'top', offsetTop + 'px' );
                    } else {
                        sidebar.attr( 'style', '' );
                    }

                    $( '#mobile-promo .iwd-opc-discount__title-wrapper' ).off();
                }

                if (isMobile) {
                    if ( ! mainWrapper.hasClass( 'mobile-multistep' )) {
                        mainWrapper.removeClass(
                            function (index, className) {
                                return (className.match( /(^|\s)step-\d+/g ) || []).join( ' ' );
                            }
                        );
                    } else {
                        mainWrapper.addClass( 'step-1' );
                    }
                }
            }
        );

        /* Pull up label for state input when it has text type */
        $( '.country_select' ).on(
            'select2:select',
            function () {
                $( '[id^=billing_state][type=text], [id^=shipping_state][type=text]' ).each(
                    function () {
                        toggleInput( $( this ) );
                        $( this ).on(
                            'focusout',
                            function () {
                                toggleInput( $( this ) );
                            }
                        );
                    }
                );

            }
        );

        /* Order for this 2 functions is necessary */
        initStep();
        hideEmailAnchor();

        /* If very first step with login form isn't exist - init anchors */
        if ( ! $( '.js-login-before-checkout' ).length) {
            anchorsInit();
            setAnchorsDataEvents( anchorsDataProvider );
        }

        /* When billing and shipping address different - reinit anchors and trigger pull up labels event */
        $( '#ship-to-different-address-checkbox' ).on(
            'change',
            function () {
                var checked = $( this ).prop( 'checked' );
                deleteAnchorsEvents();

                if (checked) {
                    anchorsDataProvider = '#shipping';
                } else {
                    anchorsDataProvider = '#billing';
                }

                setTimeout(
                    function () {
                        $( document.body ).trigger( 'country_to_state_changed' );
                        setAnchorsDataEvents();

                        var inputs = $( '.iwd-opc-field input, .iwd-opc-field--select input' );

                        $( inputs ).each(
                            function (index, element) {
                                toggleInput( element );
                            }
                        );

                        $( inputs ).each(
                            function (index, element) {
                                $( element ).on(
                                    'change keyup',
                                    function () {
                                        toggleInput( element );
                                    }
                                );
                            }
                        );
                    },
                    300
                );
            }
        );

        /* Get shipping name and price */
        var currentShippingMethod = $( 'input[id^=shipping_method_][checked]' );
        var shippingMethodName    = currentShippingMethod
            .next( 'label' )
            .clone()    // clone the element.
            .children() // select all the children.
            .remove()   // remove all the children.
            .end()      // again go back to selected element.
            .text();    // get only text.

        var shippingMethodPrice = currentShippingMethod
            .next( 'label' )
            .find( '.woocommerce-Price-amount.amount' )
            .text();

        $( '.anchors__item-shipping-method' ).html( shippingMethodName );
        $( '.anchors__item-shipping-price' ).html( shippingMethodPrice );

        /* Anchors for multistep design */
        if (isDesktopMultistep || isMobileMultistep || isTabletMultistep) {
            $( '.anchors__link' ).on(
                'click',
                function (e) {
                    e.preventDefault();

                    if ((isDesktop && isDesktopMultistep) || (isTablet && isTabletMultistep) || (isMobile && isMobileMultistep)) {
                        var target = $( this ).data( 'target' );
                        mainWrapper.removeClass(
                            function (index, className) {
                                return (className.match( /(^|\s)step-\d+/g ) || []).join( ' ' );
                            }
                        );

                        if (target === 'step-1') {
                            mainWrapper.addClass( 'step-1' );
                        } else if (target === 'step-2') {
                            mainWrapper.addClass( 'step-2' );
                        } else if (target === 'step-3') {
                            mainWrapper.addClass( 'step-3' );
                        }

                        hideEmailAnchor();
                    }

                    var href      = $( this ).attr( 'href' ).substr( 1 );
                    var scrollTop = 0;

                    if ($( '#wpadminbar' ).length && $( '#wpadminbar' ).is( ":visible" )) {
                        scrollTop += $( '#wpadminbar' ).outerHeight();
                    }

                    setTimeout(
                        function () {
                            $( 'html, body' ).animate(
                                {
                                    scrollTop: $( '[id="' + href + '"]' ).offset().top - scrollTop
                                },
                                500
                            );

                            if (href === 'nav-email') {
                                $( '#billing_email' ).focus();
                            }
                        },
                        200
                    );
                }
            );
        }

        /* Change anchors with code from billing state input */
        $( '#billing_country' ).on(
            'select2:select',
            function () {
                if ($( '#billing_state' ).is( "select" )) {
                    $( '#billing_state' ).on(
                        'select2:select',
                        function () {
                            var code = $( anchorsDataProvider + '_state' ).select2( 'data' )[0].id;
                            $( '.anchors__item-state' ).html( code );
                        }
                    );
                } else {
                    $( '#billing_state' ).on(
                        'change',
                        function () {
                            var code = $( anchorsDataProvider + '_state' ).val();
                            $( '.anchors__item-state' ).html( code );
                        }
                    );
                }
            }
        );

        /* Change anchors with code from shipping state input */
        $( '#shipping_country' ).on(
            'select2:select',
            function () {
                if ($( '#shipping_state' ).is( "select" )) {
                    $( '#shipping_state' ).on(
                        'select2:select',
                        function () {
                            var code = $( anchorsDataProvider + '_state' ).select2( 'data' )[0].id;
                            $( '.anchors__item-state' ).html( code );
                        }
                    );
                } else {
                    $( '#shipping_state' ).on(
                        'change',
                        function () {
                            var code = $( anchorsDataProvider + '_state' ).val();
                            $( '.anchors__item-state' ).html( code );
                        }
                    );
                }
            }
        );

        /* Smart buttons for login popup */
        $( '#checkout_as_guest' ).on(
            'click',
            function () {
                var smartBtns = $( '#iwd_smart_btns' ).clone();
                $( '#iwd_smart_btns' ).remove();
                $( '.js-login-before-checkout' ).css( 'display', 'none' );
                $( document.body ).trigger( 'wc_fragments_loaded' );
                $( '#iwd_opc' ).css( 'display', 'block' );
                $( '#iwd_opc' ).css( 'visibility', 'hidden' );
                var inputs = $( '.iwd-opc-field input, .iwd-opc-field--select input' );

                setTimeout(
                    function () {
                        $( document.body ).trigger( 'country_to_state_changed' );
                        $( '#iwd_opc' ).css( 'visibility', 'visible' );

                        $( inputs ).each(
                            function (index, element) {
                                toggleInput( element );
                                if (isDesktop) {
                                    setStickySidebar();
                                }
                                anchorsInit();
                                setAnchorsDataEvents( anchorsDataProvider );
                            }
                        );

                        smartBtns.insertAfter( '.iwd-opc-main-top__title--mobile' ).addClass( 'pull-right' );
                        $( document.body ).trigger( 'wc_fragments_loaded' );
                        copyPromoAndPlaceOrderOnMobile();
                    },
                    1
                );

            }
        );

        /* Init next step button on first step */
        $( '.multistep-billing-btn' ).on(
            'click',
            function () {
                var forms                    = $( '.iwd-opc-billing-form, .iwd-opc-login-form' );
                var differentShippingAddress = $( '#ship-to-different-address-checkbox' ).prop( 'checked' );

                if (differentShippingAddress) {
                    forms = $( '.iwd-opc-billing-form, .iwd-opc-login-form, .iwd-opc-shipping-form' );
                }

                wc_checkout_form.$checkout_form.find( '.iwd-opc-input, .input-text,  select, input:checkbox' ).trigger( 'validate' ).blur();

                var errors = forms.find( '.woocommerce-invalid-required-field:not("#password_field")' ).length;

                var step = $( '.is-virtual' ).length ? 'step-3' : 'step-2';

                if ( ! errors) {
                    $( this ).closest( '.iwd-opc-main-wrapper' ).removeClass( 'step-1' ).addClass( step );
                }

                hideEmailAnchor();
            }
        );

        /* Fix for IOS 10 */
        $( '#mobile-promo [name="coupon_code"]' ).on(
            'focusin',
            function () {
                if (iOSversion()[0] && iOSversion()[0] === 10 &&
                    ((isDesktop && ! isDesktopMultistep) || (isTablet && ! isTabletMultistep) || (isMobile && ! isMobileMultistep))
                ) {
                    $( '.iwd-opc-main' ).css( 'margin-bottom', '300px' );
                }

                this.scrollIntoView( true );
            }
        );

        /* Fix for IOS 10 */
        $( '#mobile-promo [name="coupon_code"]' ).on(
            'focusout',
            function () {
                if (iOSversion()[0] && iOSversion()[0] === 10 &&
                    ((isDesktop && ! isDesktopMultistep) || (isTablet && ! isTabletMultistep) || (isMobile && ! isMobileMultistep))
                ) {
                    $( '.iwd-opc-main' ).css( 'margin-bottom', '0' );
                }
            }
        );
    }
);
