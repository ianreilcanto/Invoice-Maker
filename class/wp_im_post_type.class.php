<?php
class WP_Invoice_Maker_Post_Type {

    public function __construct() {
        add_action('init', array( $this, 'settings_options_init' ));
        add_action( 'pre_get_posts', array( $this, '__set_all_posts_for_author' )  );
        add_filter('single_template', array( $this, 'invoice_single_template' ));
        add_action('add_meta_boxes', array( $this, 'wpim_metaboxes' ));
        add_action('save_post', array($this, 'save_fields' ));
        add_action('init', array($this, 'save_invoice' ));
        add_filter('manage_edit-invoice_columns', array( $this, 'invoice_edit_columns' ));
        add_action('manage_posts_custom_column', array( $this, 'invoice_custom_columns' ), 10, 2);
        add_filter( 'user_search_columns', array( $this, 'filter_function_name'), 10, 3 );
        add_filter( 'posts_search', array( $this, 'db_filter_authors_search' ) );
        add_action( 'user_register', array( $this, 'on_registration_save' ), 10, 1 );

    }

    

    function filter_function_name( $search_columns, $search, $wp_user_query ) {
        // Alter $search_columns to include the fields you want to search on
        $search_columns[] = 'user_url';
        return $search_columns;
    }

    public function settings_options_init() {
        $labels = array(
            'name'               => _x( 'Invoices', 'post type general name', 'btapp' ),
            'singular_name'      => _x( 'Invoice', 'post type singular name', 'btapp' ),
            'menu_name'          => _x( 'Invoices', 'admin menu', 'btapp' ),
            'name_admin_bar'     => _x( 'Invoice', 'add new on admin bar', 'btapp' ),
            'add_new'            => _x( 'Add New', 'invoice', 'btapp' ),
            'add_new_item'       => __( 'Add New Invoice', 'btapp' ),
            'new_item'           => __( 'New Invoice', 'btapp' ),
            'edit_item'          => __( 'Edit Invoice', 'btapp' ),
            'view_item'          => __( 'View Invoice', 'btapp' ),
            'all_items'          => __( 'Invoices', 'btapp' ),
            'search_items'       => __( 'Search Invoices', 'btapp' ),
            'parent_item_colon'  => __( 'Parent Invoices:', 'btapp' ),
            'not_found'          => __( 'No invoices found.', 'btapp' ),
            'not_found_in_trash' => __( 'No invoices found in Trash.', 'btapp' )
        );
        
        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'exclude_from_search'=> true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_nav_menus'  => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'invoice', 'with_front' => false ),
            'map_meta_cap'  => true,
            'capabilities' => array(
                // Meta capabilities
                'edit_post'		 => 'edit_invoice',
                'read_post'		 => 'read_invoice',
                'delete_post'		 => 'delete_invoice',
                'edit_posts'		 => 'edit_invoices',
                'edit_others_posts'	 => 'edit_others_invoices',
                'publish_posts'		 => 'publish_invoices',
                'read_private_posts'	 => 'read_private_invoices',
                'read'                  => 'read',
                'delete_posts'           => 'delete_invoices',
                'delete_private_posts'   => 'delete_private_invoices',
                'delete_published_posts' => 'delete_published_invoices',
                'delete_others_posts'    => 'delete_others_invoices',
                'edit_private_posts'     => 'edit_private_invoices',
                'edit_published_posts'   => 'edit_published_invoices',
                'create_posts'          => 'edit_invoices'
            ),
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => null,
            'menu_icon'          => 'dashicons-media-text',
            'supports'           => array('title', 'editor', 'thumbnail', 'slug', 'author')
        );
        
        register_post_type( 'invoice' , $args );
        
    }

    function __set_all_posts_for_author( $query ) {
        if (is_admin()):
            $user = wp_get_current_user();
            $role = ( array ) $user->roles;
            if ( $role[0] == 'subscriber' ) {
                $current_user = wp_get_current_user();
                $query->set( 'author', $current_user->ID );
            }
        endif;
    }

    public function invoice_single_template($single) {
        global $post;

        if ($post->post_type == 'invoice') {
            if ( file_exists( WPIM_PATH . '/single-invoice.php' ) ) {
                return WPIM_PATH . '/single-invoice.php';
            }
        }

        return $single;
    }
    
    public function wpim_metaboxes() {
        add_meta_box( 'wpim_invoice_info', __( 'Invoice Details', 'wpim' ), array( $this, 'wpim_appointent_details_callback'), 'invoice', 'normal' );

        add_meta_box( 'wpim_invoice_info', __( 'Invoice Preview', 'wpim' ), array( $this, 'wpim_preview_metabox'), 'invoice', 'side' );
    }

    public function wpim_appointent_details_callback() {
        global $post;
        include_once(WPIM_PATH_INCLUDES . '/invoice-meta-data.php');
    }

    public function wpim_preview_metabox() {
        global $post;
        ?>
            <label><strong>Unique Key</strong></label>
            <span style="display:block;margin-top:8px"><?= get_post_meta($post->ID, 'unique_key', true) ?></span>
            <br />
            <?php
            $wpim_settings = get_option('wpim_settings');
            $invoice_page = isset($wpim_settings['invoice_page']) ? $wpim_settings['invoice_page'] : '';
            $link = get_permalink($invoice_page) . '?uniid=' . get_post_meta($post->ID, 'unique_key', true);
            ?>
            <a href="<?= $link ?>" class="button">View Invoice</a>
        <?php
    }

    public function save_fields($post_id) {
        // Avoid autosaves
        if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        $slug = 'invoice';
        if ( ! isset( $_POST['post_type'] ) || $slug != $_POST['post_type'] ) {
            return;
        }
        
        if ( isset( $_POST['from_name']  ) )
            update_post_meta( $post_id, 'from_name',  $_POST['from_name']);

        if ( isset( $_POST['from_email']  ) ) 
            update_post_meta( $post_id, 'from_email',  $_POST['from_email']);
        
        if ( isset( $_POST['from_address']  ) ) 
            update_post_meta( $post_id, 'from_address',  $_POST['from_address']);
        
        if ( isset( $_POST['from_phone']  ) ) 
            update_post_meta( $post_id, 'from_phone',  $_POST['from_phone']);
        
        if ( isset( $_POST['from_business_number']  ) ) 
            update_post_meta( $post_id, 'from_business_number',  $_POST['from_business_number']);

        if ( isset( $_POST['to_name']  ) ) 
            update_post_meta( $post_id, 'to_name',  $_POST['to_name']);

        if ( isset( $_POST['to_email']  ) ) 
            update_post_meta( $post_id, 'to_email',  $_POST['to_email']);

        if ( isset( $_POST['to_address']  ) ) 
            update_post_meta( $post_id, 'to_address',  $_POST['to_address']);

        if ( isset( $_POST['to_phone']  ) ) 
            update_post_meta( $post_id, 'to_phone',  $_POST['to_phone']);

        if ( isset( $_POST['invoice_number']  ) ) 
            update_post_meta( $post_id, 'invoice_number',  $_POST['invoice_number']);

        if ( isset( $_POST['date']  ) ) 
            update_post_meta( $post_id, 'date',  $_POST['date']);

        if ( isset( $_POST['terms']  ) ) 
            update_post_meta( $post_id, 'terms',  $_POST['terms']);

        if ( isset( $_POST['due']  ) ) 
            update_post_meta( $post_id, 'due',  $_POST['due']);

        if ( isset( $_POST['invoice_breakdown']  ) ) 
            update_post_meta( $post_id, 'invoice_breakdown',  $_POST['invoice_breakdown']);

        if ( isset( $_POST['tax_rate']  ) ) 
            update_post_meta( $post_id, 'tax_rate',  $_POST['tax_rate']);

        if ( isset( $_POST['tax_value']  ) ) 
            update_post_meta( $post_id, 'tax_value',  $_POST['tax_value']);

        if ( isset( $_POST['image_prev']  ) ) 
            update_post_meta( $post_id, 'image_prev',  $_POST['image_prev']);

        if ( isset( $_POST['color']  ) ) 
            update_post_meta( $post_id, 'color',  $_POST['color']);
            

    }

    public function save_invoice() {
        global $current_user;
        if (isset($_POST['invoice_action']) && $_POST['invoice_action'] == 'save_invoice'):


            $title_uuid = md5(uniqid(rand(), true));
            if ($_POST['post_id']) {
                $title_uuid = get_post_meta($_POST['post_id'], 'unique_key',  true);
            }

            $invoice_data = array(
                'ID'           => $_POST['post_id'],
                'post_title'   => $title_uuid,
                'post_content' => ($_POST['note']) ? : '',
                'post_type'    => 'invoice',
                'post_status'  =>  'publish',
                'meta_input'   => array(
                    'name' => $_POST['name'],
                    'from_name' => $_POST['from_name'],
                    'from_email' => $_POST['from_email'],
                    'from_address' => $_POST['from_address'],
                    'from_phone' => $_POST['from_phone'],
                    'from_business_number' => $_POST['from_business_number'],
                    'to_id' => $_POST['to_id'],
                    'to_name' => $_POST['to_name'],
                    'to_email' => $_POST['to_email'],
                    'to_address' => $_POST['to_address'],
                    'to_phone' => $_POST['to_phone'],
                    'invoice_number' => $_POST['invoice_number'],
                    'date' => $_POST['date'],
                    'terms' => $_POST['terms'],
                    'due' => $_POST['due'],
                    'invoice_breakdown' => $_POST['invoice_breakdown'],
                    'tax_rate' => $_POST['tax_rate'],
                    'tax_value' => $_POST['tax_value'],
                    'discount' => $_POST['discount'],
                    'discount_value' => $_POST['discount_value'],
                    'image_prev' => $_POST['image_prev'],
                    'color' => $_POST['color'],
                    'type' => $_POST['type'],
                    'subtotal' => $_POST['subtotal'],
                    'total' => $_POST['total'],
                    'currency' => $_POST['currency'],
                    'thousands_separator' => $_POST['thousands_separator']
                ),
            );

            if (empty(get_post_meta($_POST['post_id'], 'status',  true))) {
                $invoice_data['meta_input']['status'] = ($_POST['type'] == 'invoice') ? 'unpaid' : 'pending';
            }

            $invoice_data['meta_input']['unique_key'] = $title_uuid;
          
            // Update the post into the database
            $id = wp_insert_post( $invoice_data );

            if (!is_user_logged_in()) {
                set_transient( 'pm_invoice_id', $id );
            }

            if(!isset($_POST['post_id'])) {
                update_user_meta($current_user->ID, 'last_invoice_made', current_time('mysql'));
            }

            wp_redirect(get_permalink($id));
            
        endif;
    }

    /*******************************************************************
    Manage Columns
    ********************************************************************/
    public function invoice_edit_columns($columns) {
        $newcolumns = array(
            'cb' => '<input type="checkbox" />',
            'title' => 'Title',
            'type' => 'Type',
            'uuid' => 'uuid',
            'invoice-number' => esc_html__('Invoice Number', 'wpim'),
            'from' => esc_html__('From', 'wpim'),
            'to' => esc_html__('To', 'wpim')
        );
        
        $columns = array_merge($newcolumns, $columns);
        
        return $columns;
    }

    public function invoice_custom_columns($column) {
        global $post;
        
        switch ($column) {
            case 'invoice-number':
                echo get_post_meta($post->ID, 'invoice_number', TRUE);
            break;
            case 'type':
                echo get_post_meta($post->ID, 'type', TRUE);
            break;
            case 'uuid':
                $wpim_settings = get_option('wpim_settings');
                $invoice_page = isset($wpim_settings['invoice_page']) ? $wpim_settings['invoice_page'] : '';
                $link = get_permalink($invoice_page) . '?uniid=' . get_post_meta($post->ID, 'unique_key', true);
                echo '<a href="'.$link.'" target="_blank">'.get_post_meta($post->ID, 'unique_key', TRUE).'</a>';
            break;
            case 'from':
                echo get_post_meta($post->ID, 'from_email', TRUE);
            break;
            case 'to':
                echo get_post_meta($post->ID, 'to_email', TRUE);
            break;
        }
    }
    
    public function db_filter_authors_search( $posts_search ) {

        // Don't modify the query at all if we're not on the search template
        // or if the LIKE is empty
        if ( !is_search() || empty( $posts_search ) )
            return $posts_search;

        global $wpdb;
        // Get all of the users of the blog and see if the search query matches either
        // the display name or the user login
        add_filter( 'pre_user_query', array($this, 'db_filter_user_query') );
        $search = sanitize_text_field( get_query_var( 's' ) );
        $args = array(
            'count_total' => false,
            'search' => sprintf( '*%s*', $search ),
            'search_fields' => array(
                'display_name',
                'user_login',
            ),
            'fields' => 'ID',
        );
        $matching_users = get_users( $args );
        remove_filter( 'pre_user_query', array($this, 'db_filter_user_query') );
        // Don't modify the query if there aren't any matching users
        if ( empty( $matching_users ) )
            return $posts_search;
        // Take a slightly different approach than core where we want all of the posts from these authors
        $posts_search = str_replace( ')))', ")) OR ( {$wpdb->posts}.post_author IN (" . implode( ',', array_map( 'absint', $matching_users ) ) . ")))", $posts_search );
        return $posts_search;
    }

    /**
     * Modify get_users() to search display_name instead of user_nicename
     */
    public function db_filter_user_query( &$user_query ) {

        if ( is_object( $user_query ) )
            $user_query->query_where = str_replace( "user_nicename LIKE", "display_name LIKE", $user_query->query_where );
        return $user_query;
    }

    public function on_registration_save( $user_id ) {
        // create 5 invoices limit on user register
        update_user_meta($user_id, '_invoice_limit_count', 5);
    
    }
    
}

new WP_Invoice_Maker_Post_Type();