<?php
global $current_user;

$test_publishable_key = get_user_meta($current_user->ID, 'test_publishable_key', true);
$test_secrect_key = get_user_meta($current_user->ID, 'test_secrect_key', true);
$live_publishable_key = get_user_meta($current_user->ID, 'live_publishable_key', true);
$live_secrect_key = get_user_meta($current_user->ID, 'live_secrect_key', true);
$enable_test_stripe = get_user_meta($current_user->ID, 'enable_test_stripe', true);

$paypal_sandbox_client_id = get_user_meta($current_user->ID, 'paypal_sandbox_client_id', true);
$paypal_live_client_id = get_user_meta($current_user->ID, 'paypal_live_client_id', true);
$enable_test_paypal = get_user_meta($current_user->ID, 'enable_test_paypal', true);

?>
<form method="POST" id="invoice-user-settings-form">
    <input type="hidden" name="form_action" value="user_payment_settings" />
    <h4>Stripe Settings</h4>
    <div class="field-group">
        <label for="test_publishable_key">Test Publishable Key</label>
        <input type="text" name="test_publishable_key" value="<?= $test_publishable_key ?>" />
    </div>
    <div class="field-group">
        <label for="test_secrect_key">Test Secret Key</label>
        <input type="text" name="test_secrect_key" value="<?= $test_secrect_key ?>" />
    </div>
    <div class="field-group">
        <label for="enable_test_stripe">
            <input type="checkbox" name="enable_test_stripe" value="1" <?php checked( $enable_test_stripe, 1 ); ?> />
            Enable Stripe Test
        </label>
    </div>
    <div class="field-group">
        <label for="live_publishable_key">Live Publishable Key</label>
        <input type="text" name="live_publishable_key" value="<?= $live_publishable_key ?>" />
    </div>
    <div class="field-group">
        <label for="live_secrect_key">Live Secret Key</label>
        <input type="text" name="live_secrect_key" value="<?= $live_secrect_key ?>" />
    </div>
    <br /><br />

    <h4>Paypal Settings</h4>
    <div class="field-group">
        <label for="paypal_sandbox_client_id">Sandbox Client ID</label>
        <input type="text" name="paypal_sandbox_client_id" value="<?= $paypal_sandbox_client_id ?>" />
    </div>
    <div class="field-group">
        <label for="paypal_live_client_id">Live Client ID</label>
        <input type="text" name="paypal_live_client_id" value="<?= $paypal_live_client_id ?>" />
    </div>
    <div class="field-group">
        <label for="enable_test_paypal">
            <input type="checkbox" name="enable_test_paypal" value="1" <?php checked( $enable_test_paypal, 1 ); ?> />
            Enable Paypal Test
        </label>
    </div>

    <input type="submit" value="Save" class="btn-primary button" />
</form>