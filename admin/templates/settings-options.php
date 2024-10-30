<div class="wrap">
    <h1>IWD Dominate - Checkout Suite</h1>

    <form id="iwd_wc_opc_general_settings" method="post" action="options.php">
		<?php settings_fields( 'iwd_wc_opc_settings' ); ?>
		<?php do_settings_sections( 'iwd_wc_opc_settings' ); ?>

        <h2>General</h2>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Enable Checkout Suite</th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text">
                            <span>Enable Checkout Suite</span>
                        </legend>
                        <label for="iwd_wc_opc_enabled">
                            <input name="iwd_wc_opc_enabled" id="iwd_wc_opc_enabled" type="checkbox" value="1" <?php checked(1, get_option('iwd_wc_opc_enabled'), true); ?>>
                            Check this option to enable plugin features
                        </label>
                    </fieldset>
                </td>
            </tr>
        </table>

        <h2>Default Configurations</h2>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Show Login Form?</th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text">
                            <span>Show Login Form?</span>
                        </legend>
                        <label for="iwd_wc_opc_login_form_enabled">
                            <input name="iwd_wc_opc_login_form_enabled" id="iwd_wc_opc_login_form_enabled" type="checkbox" value="1" <?php checked(1, get_option('iwd_wc_opc_login_form_enabled'), true); ?>>
                            Check this option to show Login form on the Checkout page
                        </label>
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Show Order Notes Field?</th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text">
                            <span>Show Order Notes?</span>
                        </legend>
                        <label for="iwd_wc_opc_order_notes_field_enabled">
                            <input name="iwd_wc_opc_order_notes_field_enabled" id="iwd_wc_opc_order_notes_field_enabled" type="checkbox" value="1" <?php checked(1, get_option('iwd_wc_opc_order_notes_field_enabled'), true); ?>>
                            Check this option to show Order Notes field on the Checkout page
                        </label>
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Show Discount Form?</th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text">
                            <span>Show Discount Form?</span>
                        </legend>
                        <label for="iwd_wc_opc_discount_form_enabled">
                            <input name="iwd_wc_opc_discount_form_enabled" id="iwd_wc_opc_discount_form_enabled" type="checkbox" value="1" <?php checked(1, get_option('iwd_wc_opc_discount_form_enabled'), true); ?>>
                            Check this option to show Discount form on the Checkout page
                        </label>
                    </fieldset>
                </td>
            </tr>
        </table>



        <h2>Design Configurations</h2>
        <table class="form-table design-conf-table">
            <tr valign="top">
                <th scope="row">Design for desktop resolution:</th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text">
                            <span>Design for desktop </span>
                        </legend>
                        <select name="iwd_wc_opc_desktop_design" id="iwd_wc_opc_desktop_design">
                            <?php echo IWD_OPC_Admin::iwd_wc_opc_designs_list('iwd_wc_opc_desktop_design'); ?>
                        </select>
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Design for tablet resolution:</th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text">
                            <span>Design for tablet resolution</span>
                        </legend>
                        <select name="iwd_wc_opc_tablet_design" id="iwd_wc_opc_tablet_design">
		                    <?php echo IWD_OPC_Admin::iwd_wc_opc_designs_list('iwd_wc_opc_tablet_design'); ?>
                        </select>
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Design for mobile resolution:</th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text">
                            <span>Design for mobile resolution</span>
                        </legend>
                        <select name="iwd_wc_opc_mobile_design" id="iwd_wc_opc_mobile_design">
		                    <?php echo IWD_OPC_Admin::iwd_wc_opc_designs_list('iwd_wc_opc_mobile_design'); ?>
                        </select>
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Login before checkout:</th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text">
                            <span>Show modal window with login form?</span>
                        </legend>
                        <label for="iwd_wc_opc_login_before_checkout">
                            <input name="iwd_wc_opc_login_before_checkout" id="iwd_wc_opc_login_before_checkout" type="checkbox" value="1" <?php checked(1, get_option('iwd_wc_opc_login_before_checkout'), true); ?>>
                            Check this option to show modal window with login form on the Checkout page
                        </label>
                    </fieldset>
                </td>
            </tr>
        </table>

		<?php submit_button(); ?>

    </form>
</div>