<table border="0" class="invoice-meta-table">
    <tbody>
        <tr>
            <td>
                <h3>From</h3>
                <table class="form-table">
                    <tbody>
                        <tr class="form-field term-name-wrap">
                            <th scope="row"><label for="from_name">Name</label></th>
                            <td>
                                <input name="from_name" id="from_name" type="text" value="<?= get_post_meta($post->ID, 'from_name', TRUE) ?>" size="40" aria-required="true">
                            </td>
                        </tr>
                        <tr class="form-field term-name-wrap">
                            <th scope="row"><label for="from_email">Email</label></th>
                            <td>
                                <input name="from_email" id="from_email" type="text" value="<?= get_post_meta($post->ID, 'from_email', TRUE) ?>" size="40" aria-required="true">
                            </td>
                        </tr>
                        <tr class="form-field term-name-wrap">
                            <th scope="row"><label for="from_address">Address</label></th>
                            <td>
                                <input name="from_address" id="from_address" type="text" value="<?= get_post_meta($post->ID, 'from_address', TRUE) ?>" size="40" aria-required="true">
                            </td>
                        </tr>
                        <tr class="form-field term-name-wrap">
                            <th scope="row"><label for="from_phone">Phone</label></th>
                            <td>
                                <input name="from_phone" id="from_phone" type="text" value="<?= get_post_meta($post->ID, 'from_phone', TRUE) ?>" size="40" aria-required="true">
                            </td>
                        </tr>
                        <tr class="form-field term-name-wrap">
                            <th scope="row"><label for="from_business_number">Business Number</label></th>
                            <td>
                                <input name="from_business_number" id="from_business_number" type="text" value="<?= get_post_meta($post->ID, 'from_business_number', TRUE) ?>" size="40" aria-required="true">
                            </td>
                        </tr>
                        
                    </tbody>
                </table>
            </td>
            <td>
                <h3>To</h3>
                <table class="form-table">
                    <tbody>
                    <tr class="form-field term-name-wrap">
                            <th scope="row"><label for="to_name">Name</label></th>
                            <td>
                                <input name="to_name" id="to_name" type="text" value="<?= get_post_meta($post->ID, 'to_name', TRUE) ?>" size="40" aria-required="true">
                            </td>
                        </tr>
                        <tr class="form-field term-name-wrap">
                            <th scope="row"><label for="to_email">Email</label></th>
                            <td>
                                <input name="to_email" id="to_email" type="text" value="<?= get_post_meta($post->ID, 'to_email', TRUE) ?>" size="40" aria-required="true">
                            </td>
                        </tr>
                        <tr class="form-field term-name-wrap">
                            <th scope="row"><label for="to_address">Address</label></th>
                            <td>
                                <input name="to_address" id="to_address" type="text" value="<?= get_post_meta($post->ID, 'to_address', TRUE) ?>" size="40" aria-required="true">
                            </td>
                        </tr>
                        <tr class="form-field term-name-wrap">
                            <th scope="row"><label for="to_phone">Phone</label></th>
                            <td>
                                <input name="to_phone" id="to_phone" type="text" value="<?= get_post_meta($post->ID, 'to_phone', TRUE) ?>" size="40" aria-required="true">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
<hr />
<table class="form-table">
    <tbody>
    <tr class="form-field term-name-wrap">
            <th scope="row"><label for="invoice_number">Invoice Number</label></th>
            <td>
                <input name="invoice_number" id="invoice_number" type="text" value="<?= get_post_meta($post->ID, 'invoice_number', TRUE) ?>" size="40" aria-required="true">
            </td>
        </tr>
        <tr class="form-field term-name-wrap">
            <th scope="row"><label for="date">Date</label></th>
            <td>
                <input name="date" id="date" type="text" value="<?= get_post_meta($post->ID, 'date', TRUE) ?>" size="40" aria-required="true">
            </td>
        </tr>
        <tr class="form-field term-name-wrap">
            <th scope="row"><label for="terms">Terms</label></th>
            <td>
                <input name="terms" id="terms" type="text" value="<?= get_post_meta($post->ID, 'terms', TRUE) ?>" size="40" aria-required="true">
            </td>
        </tr>
        <tr class="form-field term-name-wrap">
            <th scope="row"><label for="due">Due</label></th>
            <td>
                <input name="due" id="due" type="text" value="<?= get_post_meta($post->ID, 'due', TRUE) ?>" size="40" aria-required="true">
            </td>
        </tr>
    </tbody>
</table>