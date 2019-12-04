<?php
global $post;
global $current_user;
$invoice = $post;
?>
<!doctype html>
<html class="html" lang="en" itemscope="" itemtype="http://schema.org/WebPage" style="margin: 0 auto;">
    <head>
        <meta charset="UTF-8">
        <title><?= $invoice->post_title ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" id="oceanwp-style-css" href="<?= get_template_directory_uri() ?>/assets/css/style.min.css?ver=1.0" type="text/css" media="all">
        <link rel="stylesheet" id="wpim-style-css" href="<?= WPIM_URL . '/assets/css/styles.css' ?>" type="text/css" media="all">
        <style>
            .preview-container {
                border: 0;
                box-shadow: none;
            }
			
			.content-half {
				padding: 0 0;
			}

            @media screen and (max-width: 900px) {
                .content-half {
                    float: left;
                    width: 50%;
                    clear: none;
                    margin: 0;
                    margin-bottom: 0;
                    overflow: hidden;
                }
            }
        </style>
    </head>
    <body>
        <div id="printable-invoice" class="preview-wrap" style="background-color: transparent;
        padding: 0;">
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
                        <span class="amount"><?= get_post_meta($invoice->ID, 'currency', true); ?> <?= number_format(get_post_meta($invoice->ID, 'total', true), 2, '.', get_post_meta($invoice->ID, 'thousands_separator', true)) ?></span>
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
                        <?php foreach($breakdowns as $key => $breakdown): ?>
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
                        <?php endforeach; ?>
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
    </body>
</html>