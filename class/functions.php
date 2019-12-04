<?php

function get_my_invoices_count() {
    global $current_user;

    $args = array(
        'post_type' => 'invoice',
        'author' => $current_user->ID,
        'posts_per_page' => '-1',
        'year' => date('Y'),
        'monthnum' => date('n'),
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

    $invoices = new WP_Query($args);

    return $invoices->post_count;
}

function get_my_estimates_count() {
    global $current_user;
    
    $args = array(
        'post_type' => 'invoice',
        'author' => $current_user->ID,
        'posts_per_page' => '-1',
        'year' => date('Y'),
        'monthnum' => date('n'),
        'meta_query' => array(
            array(
                'key' => 'type',
                'value' => 'estimate'
            )
        )
    );

    $invoices = new WP_Query($args);

    return $invoices->post_count;
}