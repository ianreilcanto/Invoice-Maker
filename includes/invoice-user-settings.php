<?php
global $current_user;

$name = get_user_meta($current_user->ID, '_invoice_username', true);
$email = get_user_meta($current_user->ID, '_invoice_email', true);
$address = get_user_meta($current_user->ID, '_invoice_address', true);
$phone = get_user_meta($current_user->ID, '_invoice_phone', true);
$business_number = get_user_meta($current_user->ID, '_invoice_business_number', true);
$business_logo = get_user_meta($current_user->ID, '_invoice_business_logo', true);
$invoice_prefix = get_user_meta($current_user->ID, '_invoice_prefix', true);
$default_currency = get_user_meta($current_user->ID, '_invoice_currency', true);
$default_date_format = get_user_meta($current_user->ID, '_invoice_date_format', true);

?>
<form method="POST" id="invoice-user-settings-form" enctype="multipart/form-data">
    <input type="hidden" name="form_action" value="invoice_user_settings" />
    <div class="field-group">
        <label for="_invoice_username">Name</label>
        <input type="text" name="_invoice_username" value="<?= $name ?>" />
    </div>
    <div class="field-group">
        <label for="_invoice_email">Email</label>
        <input type="text" name="_invoice_email" value="<?= $email ?>" />
    </div>
    <div class="field-group">
        <label for="_invoice_address">Address</label>
        <textarea rows="4" name="_invoice_address"><?= $address ?></textarea>
    </div>
    <div class="field-group">
        <label for="_invoice_phone">Phone</label>
        <input type="text" name="_invoice_phone" value="<?= $phone ?>" />
    </div>
    <div class="field-group">
        <label for="_invoice_business_number">Business Number</label>
        <input type="text" name="_invoice_business_number" value="<?= $business_number ?>" />
    </div>
    <div class="field-group">
        <label for="_invoice_prefix">Invoice Prefix</label>
        <input type="text" name="_invoice_prefix" value="<?= $invoice_prefix ?>" />
    </div>
    <div class="field-group">
        <label for="_invoice_currency">Default Currency</label><br/>
        <label>
            <input type="radio" name="_invoice_currency" value="USD" <?php checked( $default_currency, 'USD' ); ?> />
            USD
        </label><br/>
        <label>
            <input type="radio" name="_invoice_currency" value="CAD" <?php checked( $default_currency, 'CAD' ); ?> />
            CAD
        </label><br/>
        <label>
            <input type="radio" name="_invoice_currency" value="GBP" <?php checked( $default_currency, 'GBP' ); ?> />
            GBP
        </label><br/>
        <label>
            <input type="radio" name="_invoice_currency" value="EURO" <?php checked( $default_currency, 'EURO' ); ?> />
            EURO
        </label><br/>
        <label>
            <input type="radio" name="_invoice_currency" value="PHP" <?php checked( $default_currency, 'PHP' ); ?> />
            PHP
        </label><br/>
        <label>
            <input type="radio" name="_invoice_currency" value="AUD" <?php checked( $default_currency, 'AUD' ); ?> />
            AUD
        </label><br/>
        <label>
            <input type="radio" name="_invoice_currency" value="JPY" <?php checked( $default_currency, 'JPY' ); ?> />
            JPY
        </label>
    </div>
    <div class="field-group">
        <label for="default_date_format">Date Format</label><br/>
        <label>
            <input type="radio" name="_invoice_date_format" value="mm/dd/yy" <?php checked( $default_date_format, 'mm/dd/yy' ); ?> />
            mm/dd/yyyy
        </label><br/>
        <label>
            <input type="radio" name="_invoice_date_format" value="dd/mm/yy" <?php checked( $default_date_format, 'dd/mm/yy' ); ?> />
            dd/mm/yyyy
        </label>
    </div>
    <div class="field-group">
        <label for="_invoice_business_logo">Business Logo</label><br />
        <input type="file" name="_invoice_business_logo"  accept="image/x-png,image/jpeg" />
        <?php 
        if ($business_logo) {
            echo '<img src="'.$business_logo.'" class="business-logo-set" />';
        }?>
    </div>
    <br /><br />
    <input type="submit" value="Save" class="btn-primary button" id="invoice-settings-submit" />
</form>