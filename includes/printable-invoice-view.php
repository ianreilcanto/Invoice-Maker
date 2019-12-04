<div id="printable-invoice" class="preview-wrap" style="max-width: 850px;margin: 0 auto;">


    <?php
    if (!isset($_GET['render'])):
    ?>
    <div id="title-block">
        <div class="left-align">
            <h2> 
                <?= (get_post_meta($invoice->ID, 'status', true) != 'paid') ?  'Total Due:' : 'Paid:'
                ?>
                <span>
                <?= get_post_meta($invoice->ID, 'currency', true); ?> <?=  get_post_meta($invoice->ID, 'total', true) ?>
                </span>
            </h2>
        </div>
        <div class="right-align">
            <?php
            if (get_post_meta($invoice->ID, 'type', true) == 'invoice'):
            ?>
            <form action="" method="post" target="_blank">
                <input type="hidden" name="invoice_id" value="<?= $invoice->ID ?>" />
                <input type="hidden" name="form_action" value="generate_pdf" />
                <input type="submit" value="Generate PDF" class="button button-action print-invoice-btn" />
            </form>
            <?php
            endif;
            ?>
        </div>
    </div>

    <?php
    if (get_post_meta($invoice->ID, 'type', true) == 'invoice'):
    ?>
    
    <?php
    $enable_test_stripe = get_user_meta($invoice->post_author, 'enable_test_stripe', true);
    if ($enable_test_stripe) {
        $publishable_key = get_user_meta($invoice->post_author, 'test_publishable_key', true);
    } else {
        $publishable_key = get_user_meta($invoice->post_author, 'live_publishable_key', true);
    }
    if ( get_post_meta($invoice->ID, 'status', true) != 'paid' && !empty($publishable_key) ):
    ?>
    <div class="invoice-action-block">

        <h2>Payment Options</h2>

        <?php
        if (get_post_meta($invoice->ID, 'status', true) !== 'paid'):
        ?>

        <?php
            if (!empty($publishable_key)):
        ?>
        <a href="#" class="payment-button" id="pay-width-card">
            <img src="<?= WPIM_URL . '/assets/images/pay-with-cards.png' ?>" class="payment-icon" />
            <label>Credit Card  </label>
        </a>
        <form action="" method="post" style="display:none">
            <input type="hidden" name="invoice_id" value="<?= $invoice->ID ?>" />
            <input type="hidden" name="total_amount" value="<?= number_format(get_post_meta($invoice->ID, 'total', true), 2, '', '') ?>" />
            <script src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                data-key="<?= $publishable_key ?>"
                data-description="Invoice Payment"
                data-amount="<?= number_format(get_post_meta($invoice->ID, 'total', true), 2, '', '') ?>"
                data-locale="auto"></script>
        </form>
        <?php
            endif;
        ?>

        <!-- start paypal -->
        <?php
            $enable_test_paypal = get_user_meta($invoice->post_author, 'enable_test_paypal', true);
            $sandbox = ($enable_test_paypal) ? 'sandbox' : 'production';
            $paypal_sandbox_client_id = get_user_meta($invoice->post_author, 'paypal_sandbox_client_id', true);
            $paypal_live_client_id = get_user_meta($invoice->post_author, 'paypal_live_client_id', true);

            if (!empty($paypal_sandbox_client_id) || !empty($paypal_live_client_id)):
        ?>
        <a href="#" class="payment-button" id="pay-width-paypal">
            <img src="<?= WPIM_URL . '/assets/images/paypal-icon.png' ?>" class="payment-icon" />
            <label>PayPal  </label>
        </a>
        <div id="paypal-button-block">
            <div id="paypal-button-container"></div>
            <script src="https://www.paypalobjects.com/api/checkout.js"></script>
            <script>
                // Render the PayPal button
                paypal.Button.render({
                // Set your environment
                env: '<?= $sandbox ?>', // sandbox | production

                // Specify the style of the button
                style: {
                layout: 'vertical',  // horizontal | vertical
                size:   'medium',    // medium | large | responsive
                shape:  'rect',      // pill | rect
                color:  'gold'       // gold | blue | silver | white | black
                },

                // Specify allowed and disallowed funding sources
                funding: {
                allowed: [
                    paypal.FUNDING.CARD,
                    paypal.FUNDING.CREDIT
                ],
                disallowed: []
                },

                // Enable Pay Now checkout flow (optional)
                commit: true,

                // PayPal Client IDs - replace with your own
                client: {
                sandbox: '<?= $paypal_sandbox_client_id ?>',
                production: '<?= $paypal_live_client_id ?>'
                },

                payment: function (data, actions) {
                return actions.payment.create({
                    payment: {
                    transactions: [
                        {
                        amount: {
                            total: '<?= number_format(get_post_meta($invoice->ID, 'total', true), 2, '', '') ?>',
                            currency: '<?= get_post_meta($invoice->ID, 'currency', true); ?>'
                        }
                        }
                    ]
                    }
                });
                },

                onAuthorize: function (data, actions) {
                return actions.payment.execute()
                    .then(function () {
                        jQuery.post(
                            '<?= admin_url() ?>admin-ajax.php',
                            { 
                                data: {
                                    'invoice_id': <?= $invoice->ID ?>
                                },
                                action : 'paypal_success_payment'
                            }, 
                            function( result, textStatus, xhr ) {
                                var response = JSON.parse(result)
                                if (response.status == 'success') {
                                    window.location.href = response.redirect_uri;
                                }
                            }
                        ).fail(function() {
                            
                        });
            
                    });
                }
                }, '#paypal-button-container');
            </script>
        </div>
        <?php
            endif;
        ?>
        <!-- end paypal -->


        <?php
        endif;
        ?>
    </div>
    <?php
        endif;// endif if status=paid
    endif; // endif if type=invoice
    endif; // endif if isset render
    ?>

    <div class="preview-container">
        <div class="header-info">
            <div class="content-half ok-2">
                <div class="logo-container">
                    <?php
                        if (!get_post_meta($invoice->ID, 'image_prev', true)) {
                            ?>
                            <h1 class="invoice-title name"><?= get_post_meta($invoice->ID, 'name', true) ?></h1>
                            <?php
                        } else {
                            ?>
                            <img src="<?= get_post_meta($invoice->ID, 'image_prev', true) ?>" />
                            <?php
                        }
                    ?>
                </div>
                <h1 class="invoice-title" style="text-transform: capitalize;"><?= get_post_meta($invoice->ID, 'type', true) ?></h1>

                <div class="invoice-details">
                    <table border="0" class="invoice-details-table">
                        <tbody>
                            <tr>
                                <td align="left" class="label">Invoice Number</td>
                                <td><?= get_post_meta($invoice->ID, 'invoice_number', true) ?></td>
                            </tr>
                            <tr>
                                <td align="left" class="label">Invoice Date</td>
                                <td><?= get_post_meta($invoice->ID, 'date', true) ?></td>
                            </tr>
                            <tr>
                                <td align="left" class="label">Invoice Terms</td>
                                <td><?= get_post_meta($invoice->ID, 'terms', true) ?></td>
                            </tr>
                            <tr>
                                <td align="left" class="label">Invoice Due Date</td>
                                <td><?= get_post_meta($invoice->ID, 'due', true) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="amount-due-block">
                    <span class="label">
                    <?= (get_post_meta($invoice->ID, 'status', true) != 'paid') ?  'AMOUNT DUE' : 'AMOUNT PAID' ?>
                    </span> <br />
                    <span class="amount"><?= get_post_meta($invoice->ID, 'currency', true); ?> <?=   number_format(get_post_meta($invoice->ID, 'total', true), 2, '.', get_post_meta($invoice->ID, 'thousands_separator', true)) ?></span>
                </div>
            </div>
            <div class="content-half ok-2" style="padding-left: 65px;">
                <h3 class="recept-name"><?= get_post_meta($invoice->ID, 'from_name', true) ?></h3>
                <p><?= get_post_meta($invoice->ID, 'from_email', true) ?> </p>
                <p><?= get_post_meta($invoice->ID, 'from_address', true) ?> </p>
                <p><?= get_post_meta($invoice->ID, 'from_phone', true) ?> </p>
                <p><?= get_post_meta($invoice->ID, 'from_business_number', true) ?> </p>

                <hr />

                <h3 class="recept-name">Bill To: </h3>
                <p><?= get_post_meta($invoice->ID, 'to_name', true) ?> </p>
                <p><?= get_post_meta($invoice->ID, 'to_email', true) ?> </p>
                <p><?= get_post_meta($invoice->ID, 'to_address', true) ?> </p>
                <p><?= get_post_meta($invoice->ID, 'to_phone', true) ?></p>
            </div>
        </div>

        <div class="invoice-breakdown">
            <?php
                $invoice_breakdown = get_post_meta($invoice->ID, 'invoice_breakdown', true);
                $breakdowns = json_decode($invoice_breakdown);
            ?>
            <table border="0" class="invoice-breakdown-table">
                <thead>
                    <tr>
                    <th style="background-color: <?= get_post_meta($invoice->ID, 'color', true) ?>">Description</th>
                    <th width="100" style="background-color: <?= get_post_meta($invoice->ID, 'color', true) ?>" >Price</th>
                    <th width="100" style="background-color: <?= get_post_meta($invoice->ID, 'color', true) ?>" >Qty</th>
                    <th width="100" style="background-color: <?= get_post_meta($invoice->ID, 'color', true) ?>" >Amount</th>
                    <th width="50" style="background-color: <?= get_post_meta($invoice->ID, 'color', true) ?>" >Tax</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (!empty($breakdowns)):
                    foreach($breakdowns as $key => $breakdown):
                    ?>
                    <tr valign="top">
                        <td>
                            <p><?= $breakdown->description ?></p>
                        </td>
                        <td>
                            <p><?= $breakdown->price ?></p>
                        </td>
                        <td>
                            <p><?= $breakdown->quantity ?></p>
                        </td>
                        <td>
                            <p><?= $breakdown->amount ?></p>
                        </td>
                        <td>
                            <p><?= $breakdown->tax ? 'yes' : 'no' ?></p>
                        </td>
                    </tr>
                    <?php endforeach;endif; ?>
                </tbody>
            </table>
            <table border="0" class="invoice-breakdown-table total-table">
            <tr valign="right">
                <td align="right">Subtotal</td>
                <td width="100"><?= get_post_meta($invoice->ID, 'currency', true); ?> <?= number_format(get_post_meta($invoice->ID, 'subtotal', true), 2, '.', get_post_meta($invoice->ID, 'thousands_separator', true)) ?> </td>
            </tr>
            <tr>
                <td align="right">Tax</td>
                <?php
                $tx_value = (get_post_meta($invoice->ID, 'tax_value', true)) ? get_post_meta($invoice->ID, 'tax_value', true) : 0
                ?>
                <td width="100"><?= get_post_meta($invoice->ID, 'tax_rate', true) == 'fixed' ? get_post_meta($invoice->ID, 'currency', true) : '' ?> <?= $tx_value ?><?= get_post_meta($invoice->ID, 'tax_rate', true) == 'percent' ? '%' : '' ?></td>
            </tr>
            <tr v-if="discount_value">
                <td align="right">Discount</td>
                <?php
                $disc_value = (get_post_meta($invoice->ID, 'discount_value', true)) ? get_post_meta($invoice->ID, 'discount_value', true) : 0
                ?>
                <td width="100"><?= get_post_meta($invoice->ID, 'discount', true) == 'fixed' ? get_post_meta($invoice->ID, 'currency', true) : '' ?> <?= $disc_value ?><?= get_post_meta($invoice->ID, 'discount', true) == 'percent' ? '%' : '' ?></td>
            </tr>
            <tr>
                <td align="right">Total</td>
                <td width="100"><?= get_post_meta($invoice->ID, 'currency', true); ?> <?= number_format(get_post_meta($invoice->ID, 'total', true), 2, '.', get_post_meta($invoice->ID, 'thousands_separator', true)) ?></td>
            </tr>
        </table>
        </div>

        <div class="invoice-note-wrap">
            <p><strong>Note: </strong><?= $invoice->post_content ?></p>
            <!-- <p><?= number_format(get_post_meta($invoice->ID, 'total', true), 2, '', '') ?></p> -->
        </div>
    </div>
</div>

<?php
if (isset($_GET['payment'])):

    if ($_GET['payment'] == 'success') {
        ?>
            <div class="payment-message payment-success">Success! Thank your for your payment</div>
        <?php
    } else {
        ?>
            <div class="payment-message payment-fail">Something went wrong with the payments. Please try another payment.</div>
        <?php
    }

endif;
?>