<div class="wrap wrap-wpim-settings">

    <?php
    $wpim_settings = get_option('wpim_settings');
    $my_invoices = isset($wpim_settings['my_invoices']) ? $wpim_settings['my_invoices'] : '';
    $my_estimates = isset($wpim_settings['my_estimates']) ? $wpim_settings['my_estimates'] : '';
    $invoice_page = isset($wpim_settings['invoice_page']) ? $wpim_settings['invoice_page'] : '';
    $estimate_page = isset($wpim_settings['estimate_page']) ? $wpim_settings['estimate_page'] : '';
    $approve_estimate_form = isset($wpim_settings['approve_estimate_form']) ? $wpim_settings['approve_estimate_form'] : '';
    $form_unique_id_field = isset($wpim_settings['form_unique_id_field']) ? $wpim_settings['form_unique_id_field'] : '';
    $pdfshift_api_key = isset($wpim_settings['pdfshift_api_key']) ? $wpim_settings['pdfshift_api_key'] : '';
    $select_pdf_size = isset($wpim_settings['select_pdf_size']) ? $wpim_settings['select_pdf_size'] : '';
    ?>

    <h2>WP Invoice Settings Settings</h2>

    <form method="post" action="options.php">
        <?php settings_fields( 'wpim_settings' ); ?>
        <?php do_settings_sections( 'wpim_settings' ); ?> 
        <table class="form-table">
            <tbody>
                <tr>
                    <th>My Invoices Page</th>
                    <td>
                        <?php  
                        $args1 = array(
                            'name' => 'wpim_settings[my_invoices]',
                            'selected' => $my_invoices
                        );
                        wp_dropdown_pages( $args1 );
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>My Estimates Page</th>
                    <td>
                        <?php  
                        $args2 = array(
                            'name' => 'wpim_settings[my_estimates]',
                            'selected' => $my_estimates
                        );
                        wp_dropdown_pages( $args2 );
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>View Invoice Page</th>
                    <td>
                        <?php  
                        $args3 = array(
                            'name' => 'wpim_settings[invoice_page]',
                            'selected' => $invoice_page
                        );
                        wp_dropdown_pages( $args3 );
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>View Estimate Page</th>
                    <td>
                        <?php  
                        $args4 = array(
                            'name' => 'wpim_settings[estimate_page]',
                            'selected' => $estimate_page
                        );
                        wp_dropdown_pages( $args4 );
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Approve Estimate Form</th>
                    <td>
                        <?php  
                        $forms = GFAPI::get_forms();
                        ?>
                        <select name="wpim_settings[approve_estimate_form]">
                            <option>---</option>
                            <?php
                                foreach ($forms as $key => $form) {
                                    echo '<option value="'.$form['id'].'" '.selected( $approve_estimate_form, $form['id'], false ).' >'.$form['title'].'</option>';
                                }
                            ?>
                        </select>
                    </td>
                </tr>
                <?php if ($approve_estimate_form ): ?>
                <tr>
                    <th>Unique ID field</th>
                    <td>
                        <?php  
                        $form = GFAPI::get_form( $approve_estimate_form );
                        ?>
                        <select name="wpim_settings[form_unique_id_field]">
                            <?php
                                foreach ( $form['fields'] as $key => $field ) {
                                    echo '<option value="'.$field['id'].'" '.selected( $form_unique_id_field, $field['id'], false ).' >'.$field['label'].'</option>';
                                }
                            ?>
                        </select>
                        <p class="description">Choose Gravity Form field that holds the unique ID.</p>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <th>
                        PDFShift API Key
                    </th>
                    <td>
                        <input type="text" size="40" name="wpim_settings[pdfshift_api_key]" value="<?= $pdfshift_api_key ?>" />
                    </td>
                </tr>
                <tr>
                    <th>
                        PDF Size
                    </th>
                    <td>
                        <select name="wpim_settings[select_pdf_size]">
                            <option value="A1" <?php selected( $select_pdf_size, 'A1', true ) ?> >A1</option>
                            <option value="A2" <?php selected( $select_pdf_size, 'A2', true ) ?>>A2</option>
                            <option value="A3" <?php selected( $select_pdf_size, 'A3', true ) ?>>A3</option>
                            <option value="A4" <?php selected( $select_pdf_size, 'A4', true ) ?>>A4</option>
                            <option value="A5" <?php selected( $select_pdf_size, 'A5', true ) ?>>A5</option>
                            <option value="Letter" <?php selected( $select_pdf_size, 'Letter', true ) ?>>Letter</option>
                            <option value="HalfLetter" <?php selected( $select_pdf_size, 'HalfLetter', true ) ?>>HalfLetter</option>
                            <option value="Ledger" <?php selected( $select_pdf_size, 'Ledger', true ) ?>>Ledger</option>
                            <option value="Legal" <?php selected( $select_pdf_size, 'Legal', true ) ?>>Legal</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php submit_button(); ?>
    </form>

</div>