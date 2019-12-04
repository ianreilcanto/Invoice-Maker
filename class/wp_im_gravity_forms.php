<?php
class WP_Invoice_Maker_Gravity_Form {

    public function __construct() {
        add_action('gform_after_submission', array($this, 'process_estimate'));
    }

    public function process_estimate($entry) {

        $wpim_settings = get_option('wpim_settings');
        $invoice_page = isset($wpim_settings['invoice_page']) ? $wpim_settings['invoice_page'] : '';
        $estimate_page = isset($wpim_settings['estimate_page']) ? $wpim_settings['estimate_page'] : '';
        $approve_estimate_form = isset($wpim_settings['approve_estimate_form']) ? $wpim_settings['approve_estimate_form'] : '';
        $form_unique_id_field = isset($wpim_settings['form_unique_id_field']) ? $wpim_settings['form_unique_id_field'] : '';

        if ($entry['form_id'] !== $approve_estimate_form)
            return;

        $unique_id = rgar( $entry, $form_unique_id_field);

        if ( !$unique_id )
            return;

        $estimate = get_posts(array(
                'post_type' => 'invoice',
                'status' => 'publish',
                'posts_per_page' => 1,
                'meta_query' => array(
                    array(
                        'key' => 'unique_key',
                        'value' => $unique_id
                    )
                )
            )
        );

        if (empty($estimate))
            return;

        $estimate = $estimate[0];
        update_post_meta( $invoice_id, 'status',  'accepted');
        wp_redirect( get_permalink($invoice_page) . '?uniid=' . $unique_id );
    }


}
new WP_Invoice_Maker_Gravity_Form;