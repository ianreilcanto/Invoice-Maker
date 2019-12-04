<?php
class WP_Invoice_Maker_Payments {

    public function __construct() {
        add_action( 'init', array( $this, 'process_stripe_payment') );
        add_shortcode('wpim_user_payment_settings', array( $this, 'user_payment_settings') );
        add_action( 'init', array( $this, 'save_user_payment_settings') );
        add_action('wp_ajax_paypal_success_payment', array( $this, 'paypal_success_payment') );
        add_action('wp_ajax_nopriv_paypal_success_payment', array( $this, 'paypal_success_payment') );
    }

    public function user_payment_settings() {
        ob_start();

        if ( is_user_logged_in() ) {
            include_once(WPIM_PATH_INCLUDES . '/user-payment-settings.php');
        } else {
            echo '<p>Not Logged In.</p>';
        }

        

        $output = ob_get_contents();
        ob_end_clean();

        echo $output;
    }

    public function save_user_payment_settings() {
        global $current_user;

        if (isset($_POST['form_action']) && $_POST['form_action'] == 'user_payment_settings'):

            update_user_meta($current_user->ID, 'test_publishable_key', $_POST['test_publishable_key'], '');
            update_user_meta($current_user->ID, 'test_secrect_key', $_POST['test_secrect_key'], '');
            update_user_meta($current_user->ID, 'live_publishable_key', $_POST['live_publishable_key'], '');
            update_user_meta($current_user->ID, 'live_secrect_key', $_POST['live_secrect_key'], '');

            if (isset($_POST['enable_test_stripe']))
                update_user_meta($current_user->ID, 'enable_test_stripe', 1, '');
            else
                update_user_meta($current_user->ID, 'enable_test_stripe', 0, '');

            update_user_meta($current_user->ID, 'paypal_sandbox_client_id', $_POST['paypal_sandbox_client_id'], '');
            update_user_meta($current_user->ID, 'paypal_live_client_id', $_POST['paypal_live_client_id'], '');

            if (isset($_POST['enable_test_paypal']))
                update_user_meta($current_user->ID, 'enable_test_paypal', 1, '');
            else
                update_user_meta($current_user->ID, 'enable_test_paypal', 0, '');

        endif;
    }

    public function process_stripe_payment() {

        if (isset($_POST['stripeToken']) && isset($_POST['stripeEmail'])):

            $invoice = get_post($_POST['invoice_id']);
            $wpim_settings = get_option('wpim_settings');
            $invoice_page = isset($wpim_settings['invoice_page']) ? $wpim_settings['invoice_page'] : '';
            $link = get_permalink($invoice_page) . '?uniid=' . get_post_meta($invoice->ID, 'unique_key', true);

            try {

                

                if (!empty($invoice)):

                    $enable_test_stripe = get_user_meta($invoice->post_author, 'enable_test_stripe', true);
                    if ($enable_test_stripe) {
                        $secrect_key = get_user_meta($invoice->post_author, 'test_secrect_key', true);
                    } else {
                        $secrect_key = get_user_meta($invoice->post_author, 'live_secrect_key', true);
                    }
                    
                    \Stripe\Stripe::setApiKey($secrect_key);
        
                    $token  = $_POST['stripeToken'];
                    $email  = $_POST['stripeEmail'];
        
                    $customer = \Stripe\Customer::create([
                        'email' => $email,
                        'source'  => $token,
                    ]);
        
                    $charge = \Stripe\Charge::create([
                        'customer' => $customer->id,
                        'amount'   => $_POST['total_amount'],
                        'currency' => 'usd',
                        'receipt_email' => $email
                    ]);

                    if ($charge->status === 'succeeded') {
                        $invoice_id = $_POST['invoice_id'];
                        if ($invoice_id) {
                            update_post_meta( $invoice_id, 'status',  'paid');

                            wp_redirect($link .'&payment=success');
                            exit;
                        }
                    }
                endif;

            } catch (Exception $e) {
                wp_redirect($link .'&payment=fail');
            }

        endif;
    }

    public function paypal_success_payment() {
        if (isset($_POST['data'])):

            $invoice_id = $_POST['data']['invoice_id'];
            update_post_meta( $invoice_id, 'status',  'paid');

            $wpim_settings = get_option('wpim_settings');
            $invoice_page = isset($wpim_settings['invoice_page']) ? $wpim_settings['invoice_page'] : '';
            $link = get_permalink($invoice_page) . '?uniid=' . get_post_meta($invoice_id, 'unique_key', true);

            $data = array(
                'status' => 'success',
                'redirect_uri' => $link .'&payment=success'
            );

            echo json_encode($data);
            die();

        endif;
    }

}

new WP_Invoice_Maker_Payments;