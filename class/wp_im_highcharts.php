<?php
class WP_Invoice_Maker_Highcharts {

    public function __construct() {
        add_shortcode('wpim_yearly_revenue', array( $this, 'yearly_revenue') );
        add_shortcode('wpim_total_unpaid_invoices', array( $this, 'total_unpaid_invoices') );
        add_shortcode('wpim_monthly_revenue', array( $this, 'monthly_revenue') );
        add_shortcode('wpim_paid_unpaid', array( $this, 'paid_unpaid') );
        add_shortcode('wpim_top_products', array( $this, 'top_products') );
        add_shortcode('wpim_top_customer', array( $this, 'top_customer') );
    }

    public function add_highcharts_scripts() {
        if (!wp_script_is( 'highcharts-more', 'enqueued' )) {
            wp_enqueue_script( 'highcharts-more' );
        }
        if (!wp_script_is( 'highcharts', 'enqueued' )) {
            wp_enqueue_script( 'highcharts' );
        }
        if (!wp_script_is( 'highcharts-exporting', 'enqueued' )) {
            wp_enqueue_script( 'highcharts-exporting' );
        }
    }

    public function yearly_revenue() {
        if ( is_user_logged_in() ):
            global $current_user;

            $args = array(
                'post_type' => 'invoice',
                'author' => $current_user->ID,
                'posts_per_page' => '-1',
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'relation' => 'OR',
                        array(
                            'key' => 'type',
                            'value' => 'invoice'
                        ),
                        array(
                            'key' => 'type',
                            'compare' => 'NOT EXISTS'
                        )
                    ),
                    array(
                        'key' => 'status',
                        'value' => 'paid'
                    ),
                )
            );
            $invoices = get_posts($args);

            $yearly_income = 0;
            foreach($invoices as $key =>  $invoice) {
                $date = get_post_meta($invoice->ID, 'date',  true);
                if ($date) {
                    $new_date = explode('/', $date);
                    if ($new_date[2] == date('Y')) {
                        $yearly_income += floatval(get_post_meta($invoice->ID, 'total',  true));
                    }
                }
            }

            echo '<h2 class="wpim-yearly-icome">'. get_user_meta($current_user->ID, '_invoice_currency', true) . ' ' . $yearly_income.'</h2>';

        endif;
    }

    public function total_unpaid_invoices() {
        if ( is_user_logged_in() ):
            global $current_user;

            $args = array(
                'post_type' => 'invoice',
                'author' => $current_user->ID,
                'posts_per_page' => '-1',
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'relation' => 'OR',
                        array(
                            'key' => 'type',
                            'value' => 'invoice'
                        ),
                        array(
                            'key' => 'type',
                            'compare' => 'NOT EXISTS'
                        )
                    ),
                    array(
                        'key' => 'status',
                        'value' => 'unpaid'
                    ),
                )
            );
            $invoices = get_posts($args);

            $unpaid_invoices = 0;
            foreach($invoices as $key =>  $invoice) {
                $unpaid_invoices += floatval(get_post_meta($invoice->ID, 'total',  true));
            }

            echo '<h2 class="wpim-unpaid-invoices">'. get_user_meta($current_user->ID, '_invoice_currency', true) . ' ' . $unpaid_invoices.'</h2>';

        endif;
    }

    public function monthly_revenue() {
        if ( is_user_logged_in() ):
            global $current_user;

            $args = array(
                'post_type' => 'invoice',
                'author' => $current_user->ID,
                'posts_per_page' => '-1',
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'relation' => 'OR',
                        array(
                            'key' => 'type',
                            'value' => 'invoice'
                        ),
                        array(
                            'key' => 'type',
                            'compare' => 'NOT EXISTS'
                        )
                    ),
                    array(
                        'key' => 'status',
                        'value' => 'paid'
                    ),
                )
            );
            $invoices = get_posts($args);

            $default_date_format = get_user_meta($current_user->ID, '_invoice_date_format', true);
            if ($default_date_format == 'mm/dd/yy') {
                $month_index = 0;
            } else {
                $month_index = 1;
            }

            $monthly_income = array();
            foreach($invoices as $key =>  $invoice) {
                $date = get_post_meta($invoice->ID, 'date',  true);
                if ($date) {
                    $new_date = explode('/', $date);
                    if ($new_date[2] == date('Y')) {
                        $monthly_income[$new_date[$month_index]] = isset($monthly_income[$new_date[$month_index]]) ? ($monthly_income[$new_date[$month_index]] + get_post_meta($invoice->ID, 'total',  true)) : get_post_meta($invoice->ID, 'total',  true);
                    }
                }
            }

            $this->add_highcharts_scripts();
            ob_start();
            ?>
            <div id="revenue-container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
            <script type="text/javascript">
                
                var revenue_chart = {
                    chart_args: {
                        "chart": {
                            "type":"pie",
                            "show_labels":true,
                            "renderTo":"revenue-container",
                            "height":400
                        },
                        "title":{
                            "text":"Revenue"
                        },
                        "subtitle":{
                            "text":""
                        },
                        "legend":{
                            "enabled":true
                        },
                        "credits":{"href":"","text":""},
                        "exporting":{"enabled":false},
                        "plotOptions":{
                            "series":{
                                "dataLabels":{
                                    "enabled":true,
                                    "format":"{y:,f}"
                                },
                                "connectNulls":true
                            },
                            "spline":{
                                "dataLabels":{"enabled":true}
                            }
                        },
                        "xAxis":{
                            "categories":["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],"title":{"text":""}
                        },
                        "yAxis":{"title":{"text":"USD"}},
                        "series":[
                            {
                                "type":"spline",
                                "showInLegend":true,
                                "data":[
                                    ["Jan", parseInt("<?= isset($monthly_income['01']) ? $monthly_income['01'] : '' ?>")],
                                    ["Feb", parseInt("<?= isset($monthly_income['02']) ? $monthly_income['02'] : '' ?>")],
                                    ["Mar", parseInt("<?= isset($monthly_income['03']) ? $monthly_income['03'] : '' ?>")],
                                    ["Apr", parseInt("<?= isset($monthly_income['04']) ? $monthly_income['04'] : '' ?>")],
                                    ["May", parseInt("<?= isset($monthly_income['05']) ? $monthly_income['05'] : '' ?>")],
                                    ["Jun", parseInt("<?= isset($monthly_income['06']) ? $monthly_income['06'] : '' ?>")],
                                    ["Jul", parseInt("<?= isset($monthly_income['07']) ? $monthly_income['07'] : '' ?>")],
                                    ["Aug", parseInt("<?= isset($monthly_income['08']) ? $monthly_income['08'] : '' ?>")],
                                    ["Sep", parseInt("<?= isset($monthly_income['09']) ? $monthly_income['09'] : '' ?>")],
                                    ["Oct", parseInt("<?= isset($monthly_income['10']) ? $monthly_income['10'] : '' ?>")],
                                    ["Nov", parseInt("<?= isset($monthly_income['11']) ? $monthly_income['11'] : '' ?>")],
                                    ["Dec", parseInt("<?= isset($monthly_income['12']) ? $monthly_income['12'] : '' ?>")]
                                ]
                            }
                        ],
                        "tooltip":{"pointFormat":"<b>{point.y}</b>"}
                        },
                        post_id: 0,
                        instance: 0	
                    };

                
                (function( $ ) {
                    revenue_chart.render_chart = function( ) {
                        $( '.m-chart' ).trigger({
                            type:     'render_start',
                            post_id:  this.post_id,
                            instance: this.instance
                        });

                        $( '#revenue-container' ).highcharts(
                            this.chart_args,
                            function( chart ) {
                                // Stuff to do after the chart has rendered goes here
                            }
                        );

                        this.chart = $( '#revenue-container' ).highcharts();

                        $( '.m-chart' ).trigger({
                            type:     'render_done',
                            chart:    this.chart,
                            post_id:  this.post_id,
                            instance: this.instance
                        });
                    };

                    $( function() {
                        revenue_chart.render_chart();
                    } );
                })( jQuery );


            </script>
            <?php

            $output = ob_get_contents();
            ob_end_clean();

            echo $output;
        endif;
    }

    public function paid_unpaid() {
        if ( is_user_logged_in() ):
            global $current_user;

            $args = array(
                'post_type' => 'invoice',
                'author' => $current_user->ID,
                'posts_per_page' => '-1',
                'meta_query' => array(
                    'relation' => 'OR',
                    array(
                        'key' => 'type',
                        'value' => 'invoice'
                    ),
                    array(
                        'key' => 'type',
                        'compare' => 'NOT EXISTS'
                    )
                )
            );
            $invoices = get_posts($args);

            $paid_unpaid = array( 'paid' => 0, 'unpaid' => 0 );
            foreach($invoices as $key =>  $invoice) {
                $status = get_post_meta($invoice->ID, 'status',  true);
                if ($status == 'paid') {
                    $paid_unpaid['paid'] += 1;
                }
                else {
                    $paid_unpaid['unpaid'] += 1;
                }
            }

            $this->add_highcharts_scripts();
            ob_start();
            ?>
            <div id="paid-unpaid-container" class="m-chart-container"></div>
            <script type="text/javascript">
                var paid_unpaid_container = {
                    chart_args: {"chart":{"type":"pie","show_labels":true,"renderTo":"m-chart-127","height":400},"title":{"text":"Paid vs Unpaid"},"subtitle":{"text":""},"legend":{"enabled":true},"credits":{"href":"","text":""},"exporting":{"enabled":false},"plotOptions":{"series":{"dataLabels":{"enabled":true,"format":"{y:,f}"},"connectNulls":true},"pie":{"dataLabels":{"enabled":true}}},"xAxis":{"title":{"text":""}},"yAxis":{"title":{"text":""}},"series":[{"type":"pie","showInLegend":true,"data":[["Paid",<?= absint($paid_unpaid['paid']) ?>],["Unpaid",<?= absint($paid_unpaid['unpaid']) ?>]]}],"tooltip":{"pointFormat":"<b>{point.y}</b>"}},
                    post_id: 0,
                    instance: 0	};

                
                (function( $ ) {
                    paid_unpaid_container.render_chart = function( ) {
                        $( '.m-chart' ).trigger({
                            type:     'render_start',
                            post_id:  this.post_id,
                            instance: this.instance
                        });

                        $( '#paid-unpaid-container' ).highcharts(
                            this.chart_args,
                            function( chart ) {
                                // Stuff to do after the chart has rendered goes here
                                
                            }
                        );

                        this.chart = $( '#paid-unpaid-container' ).highcharts();

                        $( '.m-chart' ).trigger({
                            type:     'render_done',
                            chart:    this.chart,
                            post_id:  this.post_id,
                            instance: this.instance
                        });
                    };

                    $( function() {
                        paid_unpaid_container.render_chart();
                    } );
                })( jQuery );
            </script>
            <?php

            $output = ob_get_contents();
            ob_end_clean();

            echo $output;
        endif;
    }

    public function top_products() {
        if ( is_user_logged_in() ):
            global $current_user;

            $args = array(
                'post_type' => 'invoice',
                'author' => $current_user->ID,
                'posts_per_page' => '-1',
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'relation' => 'OR',
                        array(
                            'key' => 'type',
                            'value' => 'invoice'
                        ),
                        array(
                            'key' => 'type',
                            'compare' => 'NOT EXISTS'
                        )
                    ),
                    array(
                        'key' => 'status',
                        'value' => 'paid'
                    ),
                )
            );
            $invoices = get_posts($args);

            $products = array();
            foreach($invoices as $key =>  $invoice) {
                $invoice_breakdown = get_post_meta($invoice->ID, 'invoice_breakdown',  true);
                $invoice_breakdown = json_decode($invoice_breakdown);

                foreach ($invoice_breakdown as $breakdown) {
                    $products[$breakdown->description] = isset($products[$breakdown->description]) ? ($products[$breakdown->description] + $breakdown->amount) : floatval($breakdown->amount);
                }

                
            }

            $this->add_highcharts_scripts();
            ob_start();
            ?>
            <div id="top-product-container" class="m-chart-container"></div>
            <script type="text/javascript">
                var top_products_container = {
                    chart_args: {"chart":{"type":"bar","show_labels":false,"renderTo":"m-chart-132","height":400},"title":{"text":"Top Products"},"subtitle":{"text":""},"legend":{"enabled":false},"credits":{"href":"","text":""},"exporting":{"enabled":false},"plotOptions":{"series":{"dataLabels":{"enabled":false,"format":"{y:,f}"},"connectNulls":true},"bar":{"dataLabels":{"enabled":false}}},"xAxis":{"categories":[
                            <?php
                            foreach ($products as $key => $product) {
                                echo '"'.$key.'",';
                            }    
                            ?>
                        ],"title":{"text":""}},"yAxis":{"title":{"text":""}},"series":[{"type":"bar","showInLegend":true,
                        "data":[
                            <?php
                            $count = 0;
                            foreach ($products as $key => $product_total) {
                                if ($count < 9) {
                                    echo '["'.$key.'",'.$product_total.'],';
                                    $count++;
                                }
                            }    
                            ?>
                        ]}
                        ],"tooltip":{"pointFormat":"<b>{point.y}</b>"}},
                    post_id: 0,
                    instance: 0	};

                
                (function( $ ) {
                    top_products_container.render_chart = function( ) {
                        $( '.m-chart' ).trigger({
                            type:     'render_start',
                            post_id:  this.post_id,
                            instance: this.instance
                        });

                        $( '#top-product-container' ).highcharts(
                            this.chart_args,
                            function( chart ) {
                                // Stuff to do after the chart has rendered goes here
                                
                            }
                        );

                        this.chart = $( '#top-product-container' ).highcharts();

                        $( '.m-chart' ).trigger({
                            type:     'render_done',
                            chart:    this.chart,
                            post_id:  this.post_id,
                            instance: this.instance
                        });
                    };

                    $( function() {
                        top_products_container.render_chart();
                    } );
                })( jQuery );
            </script>
            <?php

            $output = ob_get_contents();
            ob_end_clean();

            echo $output;
        endif;
    }

    public function top_customer() {
        if ( is_user_logged_in() ):
            global $current_user;

            $args = array(
                'post_type' => 'invoice',
                'author' => $current_user->ID,
                'posts_per_page' => '-1',
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'relation' => 'OR',
                        array(
                            'key' => 'type',
                            'value' => 'invoice'
                        ),
                        array(
                            'key' => 'type',
                            'compare' => 'NOT EXISTS'
                        )
                    ),
                    array(
                        'key' => 'status',
                        'value' => 'paid'
                    ),
                )
            );
            $invoices = get_posts($args);

            $customers = array();
            foreach($invoices as $key =>  $invoice) {
                $total = get_post_meta($invoice->ID, 'total',  true);
                $customer_id = get_post_meta($invoice->ID, 'to_id',  true);
                $customer = get_post($customer_id);

                if (!empty($customer)) {
                    $customers[$customer->ID] = isset($customers[$customer->ID]) ? (['customer' => $customer->post_title, 'total' => $customers[$customer->ID]['total'] + $total]) : (['customer' => $customer->post_title, 'total' => $total]);
                }

                
            }

            $this->add_highcharts_scripts();
            ob_start();
            ?>
            <div id="top-customer-container" class="m-chart-container"></div>
            <script type="text/javascript">
                var top_customer_container = {
                    chart_args: {"chart":{"type":"bar","show_labels":false,"renderTo":"m-chart-132","height":400},"title":{"text":"Top Customers"},"subtitle":{"text":""},"legend":{"enabled":false},"credits":{"href":"","text":""},"exporting":{"enabled":false},"plotOptions":{"series":{"dataLabels":{"enabled":false,"format":"{y:,f}"},"connectNulls":true},"bar":{"dataLabels":{"enabled":false}}},"xAxis":{"categories":
                        [
                            <?php
                            foreach ($customers as $key => $customer) {
                                echo '"'.$customer['customer'].'",';
                            }    
                            ?>
                        ],"title":{"text":""}},"yAxis":{"title":{"text":""}},"series":[{"type":"bar","showInLegend":true,
                        "data":[
                            <?php
                            $count = 0;
                            foreach ($customers as $key => $customer) {
                                if ($count < 9) {
                                    echo '["'.$customer['customer'].'",'.$customer['total'].'],';
                                    $count++;
                                }
                            }    
                            ?>
                        ]}
                        ],"tooltip":{"pointFormat":"<b>{point.y}</b>"}},
                    post_id: 0,
                    instance: 0	};

                
                (function( $ ) {
                    top_customer_container.render_chart = function( ) {
                        $( '.m-chart' ).trigger({
                            type:     'render_start',
                            post_id:  this.post_id,
                            instance: this.instance
                        });

                        $( '#top-customer-container' ).highcharts(
                            this.chart_args,
                            function( chart ) {
                                // Stuff to do after the chart has rendered goes here
                                
                            }
                        );

                        this.chart = $( '#top-customer-container' ).highcharts();

                        $( '.m-chart' ).trigger({
                            type:     'render_done',
                            chart:    this.chart,
                            post_id:  this.post_id,
                            instance: this.instance
                        });
                    };

                    $( function() {
                        top_customer_container.render_chart();
                    } );
                })( jQuery );
            </script>
            <?php

            $output = ob_get_contents();
            ob_end_clean();

            echo $output;
        endif;
    }

    
}

new WP_Invoice_Maker_Highcharts();