<?php
/**
 * Plugin Name: WP Invoice Maker
 * Description: Custom plugin to let user make a invoices
 * Version:     1.0
 * Author:      Ian Reil Canto
 * Text Domain: wpim
 */

define( 'WPIM_PATH', dirname( __FILE__ ) );
define( 'WPIM_PATH_CLASS', dirname( __FILE__ ) . '/class' );
define( 'WPIM_PATH_INCLUDES', dirname( __FILE__ ) . '/includes' );
define( 'WPIM_FOLDER', basename( WPIM_PATH ) );
define( 'WPIM_URL', plugins_url() . '/' . WPIM_FOLDER );
define( 'WPIM_URL_INCLUDES', WPIM_URL . '/includes' );
define( 'WPIM_NAME', 'Appointments WP - User' );
define( 'WPIM_VERSION', '1.0' );

if(!class_exists('WP_Invoice_Maker')):

    register_activation_hook( __FILE__, 'wp_im_activation' );
    function wp_im_activation(){
        
    }

    register_deactivation_hook( __FILE__, 'wp_im_deactivation' );
    function wp_im_deactivation(){
        // deactivation block
    }

    add_action( 'admin_init', 'wp_im_plugin_activate' );
    function wp_im_plugin_activate(){
        // deactivation logic
    }

    add_action( 'admin_init', 'add_custom_caps');
    function add_custom_caps() {
        // gets the subscriber role
        $all_roles = array('administrator', 'editor', 'subscriber'); 

        foreach ($all_roles as $role_str) {
            $role = get_role( $role_str );
            
            $role->add_cap( 'edit_invoice' );
            $role->add_cap( 'read_invoice' );
            $role->add_cap( 'delete_invoice' );
            $role->add_cap( 'edit_invoices' );
            $role->add_cap( 'edit_others_invoices' );
            $role->add_cap( 'publish_invoices' );
            $role->add_cap( 'read_private_invoices' );
            $role->add_cap( 'read' );
            $role->add_cap( 'delete_invoices' );
            $role->add_cap( 'delete_private_invoices' );
            $role->add_cap( 'delete_published_invoices' );
            $role->add_cap( 'delete_others_invoices' );
            $role->add_cap( 'edit_private_invoices' );
            $role->add_cap( 'edit_published_invoices' );
            $role->add_cap( 'edit_invoices' );
        }
        
    }
    
    include_once(WPIM_PATH.'/vendor/autoload.php');
    
    // include classes
    include_once(WPIM_PATH_CLASS.'/wp_im_wp_main.class.php');
    include_once(WPIM_PATH_CLASS.'/wp_im_post_type.class.php');
    include_once(WPIM_PATH_CLASS.'/wp_im_payments.class.php');
    include_once(WPIM_PATH_CLASS.'/wp_im_gravity_forms.php');
    include_once(WPIM_PATH_CLASS.'/wp_im_highcharts.php');

    add_action( 'plugins_loaded', array( 'WP_Invoice_Maker', 'get_instance' ) );

endif;
