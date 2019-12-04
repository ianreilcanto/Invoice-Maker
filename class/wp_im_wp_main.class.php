<?php
use \PDFShift\PDFShift;

class WP_Invoice_Maker {
    private static $instance;

    public static function get_instance()
    {
        if( null == self::$instance ) {
            self::$instance = new WP_Invoice_Maker();
        }

        return self::$instance;
    }

    public function __construct() {
        add_action('admin_init', array( $this, 'register_settings' ));
        add_action('admin_menu', array( $this, 'admin_menus'), 10 );
        add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts' ), 2000 );
        add_action( 'admin_enqueue_scripts', array ($this, 'add_scripts' ) );
        add_action('wp_ajax_wpim_file_upload', array( $this, 'wpim_file_upload') );
        add_action('wp_ajax_nopriv_wpim_file_upload', array( $this, 'wpim_file_upload') );
        add_shortcode('create_invoice', array( $this, 'create_invoice_shortcode') );
        add_shortcode('my_invoices', array( $this, 'my_invoices') );
        add_shortcode('my_estimates', array( $this, 'my_estimates') );
        add_shortcode('wpim_user_settings', array( $this, 'user_settings') );
        add_shortcode('code_view_invoice', array( $this, 'code_view_invoice') );
        add_action('init', array( $this, 'save_user_settings') );
        add_action('wp_ajax_send_invoice', array( $this, 'send_invoice') );
        add_action('wp_ajax_nopriv_send_invoice', array( $this, 'send_invoice') );
        add_action('wp_ajax_edit_product', array( $this, 'edit_product') );
        add_action('wp_ajax_nopriv_edit_product', array( $this, 'edit_product') );
        add_action('init', array( $this, 'save_edit_product') );
        add_action('wp_ajax_edit_client', array( $this, 'edit_client') );
        add_action('wp_ajax_nopriv_edit_client', array( $this, 'edit_client') );
        add_action('init', array( $this, 'save_edit_client') );
        add_action('wp_ajax_update_invoice_status', array( $this, 'update_invoice_status') );
        add_action('wp_ajax_nopriv_update_invoice_status', array( $this, 'update_invoice_status') );
        add_action('init', array( $this, 'extra_modal') );
        add_action('init', array( $this, 'generate_pdf') );
        add_action('wp_print_scripts', array( $this, 'remove_scripts'), 100 );
        add_action('wp', array( $this, 'email_popup' ));
        
    }

    public function register_settings() {
        register_setting( 'wpim_settings', 'wpim_settings', '' );
    }

    public function admin_menus(){
        add_submenu_page ( 'edit.php?post_type=invoice' , 'Settings' , 'Settings' , 'manage_options' , 'wpim-settings' , array( $this , 'wpim_settings_page' ));
    }

    public function wpim_settings_page() {
        include_once(WPIM_PATH_INCLUDES . '/wpim-settings.php');
    }

    public function appointments_menus() {
        
    }

    public function remove_scripts() {
        if( class_exists('Ocean_Popup_Login') ){
            wp_dequeue_script( 'opl-js-script' );
        }
    }

    public function add_scripts() {
        global $post;
        global $current_user;

        if (is_singular('invoice')) {
            $post_id = (!empty($post)) ? $post->ID : get_the_ID();
            $new_post = get_post($post_id);

            $post_title = $new_post->post_title;
            $post_content = $new_post->post_content;
            $post_meta = get_post_meta($post_id);
        } else {

            $post_title = get_the_title($post->ID);
            $generated_title = preg_replace('!(template)!i','',$post_title);//str_replace('template', '', $post_title);
            $clean_title = preg_replace('/\s+/', ' ', $generated_title);

            $post_id = 0;
            $post_title =  (is_home() || is_front_page()) ? '' : $clean_title;
            $post_content = '';
            $post_meta = [];
        }

        if ( is_user_logged_in() ) {
            $user_meta = get_user_meta($current_user->ID, '');
        } else {
            $user_meta = false;
        }

        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_register_style( 'jquery-ui', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css' );
        wp_enqueue_style( 'jquery-ui' );
        wp_enqueue_style( 'wpim-style', WPIM_URL . '/assets/css/styles.css' );

        wp_enqueue_script( 'wpim-lodash', WPIM_URL . '/assets/js/lodash.js', '1.0', true );
        wp_enqueue_script( 'wpim-vue-script', WPIM_URL . '/assets/js/vue.min.js', '1.0', true );

        wp_register_script( 'wpim-script', WPIM_URL . '/assets/js/scripts.js', array('jquery'), '1.0', true );

        $post_data2 = array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'home_url' => home_url()
        );
        
        wp_localize_script( 'wpim-script', 'post_data', $post_data2 );
        wp_enqueue_script( 'wpim-script' );

        wp_register_script( 'invoice-vue', WPIM_URL . '/assets/js/invoice-vue.js', array('jquery'), '1.0', true );

        $post_data = array(
            'post_id' => $post_id,
            'title' => $post_title,
            'note' => $post_content,
            'post_meta' => $post_meta,
            'user_meta' => $user_meta,
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'user_subscription_status' => $this->get_user_subscription_status(),
            'home_url' => home_url(),
            'is_logged_in' => is_user_logged_in(),
        );
        
        wp_localize_script( 'invoice-vue', 'post_data', $post_data );

        if (is_singular('invoice')) {
            wp_enqueue_script( 'invoice-vue' );
        }

        wp_enqueue_script( 'wpim-opl-js-script', WPIM_URL . '/assets/js/ocean-popup.js', array( 'jquery' ), null, true );
    }

    public function get_user_subscription_status($author_id = 0) {
        global $current_user;
        $status = rcp_get_status();

        $author_id = ($author_id) ? $author_id : $current_user->ID;

        $invoice_args = array(
            'post_type' => 'invoice',
            'author' => $author_id,
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
        $invoices = new WP_Query($invoice_args);

        $estimates_args = array(
            'post_type' => 'invoice',
            'author' => $author_id,
            'posts_per_page' => '-1',
            'meta_query' => array(
                array(
                    'key' => 'type',
                    'value' => 'estimate'
                )
            )
        );
        $estimates = new WP_Query($estimates_args);


        if ($status == 'active') {
            return array(
                'status'                    => $status,
                'invoice_limit'             => 'unlimited',
                'current_invoice_count'     => $invoices->post_count,
                'current_estimate_count'    => $estimates->post_count
            );
        }

        return  array(
            'status' => $status,
            'invoice_limit' => '5',
            'current_invoice_count'     => $invoices->post_count,
            'current_estimate_count'    => $estimates->post_count
        );

    }

    function wpim_file_upload() {
        $file_data = isset( $_FILES ) ? $_FILES : array();
        $response = array();

        $uploaded_file = wp_handle_upload( $file_data['wpim_file_upload'], array( 'test_form' => false ) );

        if( $uploaded_file && ! isset( $uploaded_file['error'] ) ) {
            $response['response'] = "SUCCESS";
            $response['filename'] = basename( $uploaded_file['url'] );
            $response['url'] = $uploaded_file['url'];
            $response['type'] = $uploaded_file['type'];
        } else {
            $response['response'] = "ERROR";
            $response['error'] = $uploaded_file['error'];
        }
        
        echo json_encode( $response );
        die();
    }

    public function create_invoice_shortcode() {
        global $post;
        wp_enqueue_script( 'invoice-vue' );
        ob_start();

        include_once(WPIM_PATH_INCLUDES . '/generate-invoice.php');

        $output = ob_get_contents();
        ob_end_clean();

        echo $output;
    }

    public function my_invoices() {
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

            $wpim_settings = get_option('wpim_settings');

            echo '<table data-type="invoice" class="wpim-datatable">';
                echo '<thead>';
                    echo '<tr>';
                        echo '<th>UUID</th>';
                        echo '<th>To</th>';
                        echo '<th>Date</th>';
                        echo '<th>Total</th>';
                        echo '<th>Status</th>';
                        echo '<th>Actions</th>';
                    echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                    foreach ($invoices as $invoice) {
                        $invoice_page = isset($wpim_settings['invoice_page']) ? $wpim_settings['invoice_page'] : '';
                        $link = get_permalink($invoice_page) . '?uniid=' . get_post_meta($invoice->ID, 'unique_key', true);

                        $title_r = ($invoice->post_title) ? $invoice->post_title : get_post_meta($invoice->ID, 'unique_key', true);

                        echo '<tr>';
                            echo '<td><a href="'.$link.'">'.$title_r.'</a></td>';
                            echo '<td>'. get_post_meta($invoice->ID, 'to_name', true) .'</td>';
                            echo '<td>'. get_post_meta($invoice->ID, 'date', true) .'</td>';
                            echo '<td>'. get_post_meta($invoice->ID, 'total', true) .'</td>';
                            echo '<td>
                                    <select class="invoice-status-dropdown" data-id="'.$invoice->ID.'">';
                                    echo    '<option value="unpaid" '.selected( 'unpaid', get_post_meta($invoice->ID, 'status', true) ).'>Unpaid</option>';
                                    echo    '<option value="paid" '.selected( 'paid', get_post_meta($invoice->ID, 'status', true) ).'>Paid</option>';
                                    echo    '<option value="void" '.selected( 'void', get_post_meta($invoice->ID, 'status', true) ).'>Void</option>';
                            echo    '</select>    
                            </td>';
                            echo '<td>
                                <a href="'.get_permalink($invoice->ID).'">Edit</a> | 
                                <a href="#" data-id='.$invoice->ID.' class="send-invoice">Send</a> |
                                <a href="'.$link.'&render=preview" target="_blank">Preview</a> |
                                <a href="#" data-id='.$invoice->ID.' class="myinv-send-invoice">PDF</a>
                            </td>';
                        echo '</tr>';
                    }
                echo '</tbody>';
            echo '</table>';
            
            add_action('wp_footer', array( $this, 'invoice_mail_modal' ), 100 );
            add_action('wp_footer', function(){
                echo '<form action="" method="post" target="_blank" id="generate-invoice-form">
                    <input type="hidden" name="invoice_id" value="" />
                    <input type="hidden" name="form_action" value="generate_pdf" />
                    <input type="submit" value="Generate PDF" class="button button-action print-invoice-btn" style="display:none;" />
                </form>';
            });
            wp_enqueue_style( 'wpim-datatable-style', 'https://cdn.datatables.net/v/dt/dt-1.10.18/datatables.min.css' );
            wp_enqueue_script( 'wpim-datatable-script', 'https://cdn.datatables.net/v/bs-3.3.7/dt-1.10.18/datatables.min.js', '1.0', true );

        else:
            echo '<pre>Not Logged In.</pre>';
        endif;
    }

    public function my_estimates() {
        if ( is_user_logged_in() ):
            global $current_user;
            $args = array(
                'post_type' => 'invoice',
                'author' => $current_user->ID,
                'posts_per_page' => '-1',
                'meta_query' => array(
                    array(
                        'key' => 'type',
                        'value' => 'estimate'
                    )
                )
            );
            $invoices = get_posts($args);

            $wpim_settings = get_option('wpim_settings');

            echo '<table data-type="estimate" class="wpim-datatable">';
                echo '<thead>';
                    echo '<tr>';
                        echo '<th>UUID</th>';
                        echo '<th>To</th>';
                        echo '<th>Date</th>';
                        echo '<th>Total</th>';
                        echo '<th>Status</th>';
                        echo '<th>Actions</th>';
                    echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                    foreach ($invoices as $invoice) {
                        $invoice_page = isset($wpim_settings['estimate_page']) ? $wpim_settings['estimate_page'] : '';
                        $link = get_permalink($invoice_page) . '?uniid=' . get_post_meta($invoice->ID, 'unique_key', true);

                        $title_r = ($invoice->post_title) ? $invoice->post_title : get_post_meta($invoice->ID, 'unique_key', true);

                        echo '<tr>';
                            echo '<td><a href="'.$link.'">'.$title_r.'</a></td>';
                            echo '<td>'. get_post_meta($invoice->ID, 'to_name', true) .'</td>';
                            echo '<td>'. get_post_meta($invoice->ID, 'date', true) .'</td>';
                            echo '<td>'. get_post_meta($invoice->ID, 'total', true) .'</td>';
                            echo '<td>
                                    <select class="invoice-status-dropdown" data-id="'.$invoice->ID.'">';
                                    echo    '<option value="unpaid" '.selected( 'unpaid', get_post_meta($invoice->ID, 'status', true) ).'>Unpaid</option>';
                                    echo    '<option value="paid" '.selected( 'paid', get_post_meta($invoice->ID, 'status', true) ).'>Paid</option>';
                            echo    '</select>    
                            </td>';
                            echo '<td>
                                <a href="'.get_permalink($invoice->ID).'">Edit</a> | 
                                <a href="#" data-id='.$invoice->ID.' class="send-invoice">Send</a>
                            </td>';
                        echo '</tr>';
                    }
                echo '</tbody>';
            echo '</table>';

            add_action('wp_footer', array( $this, 'invoice_mail_modal' ) );
            wp_enqueue_style( 'wpim-datatable-style', 'https://cdn.datatables.net/v/dt/dt-1.10.18/datatables.min.css' );
            wp_enqueue_script( 'wpim-datatable-script', 'https://cdn.datatables.net/v/bs-3.3.7/dt-1.10.18/datatables.min.js', '1.0', true );
        else:
            echo '<pre>Not Logged In.</pre>';
        endif;
    }

    public function code_view_invoice() {
        ob_start();
        global $current_user;

        $unique_id = isset($_GET['uniid']) ?  $_GET['uniid'] : '';

        $invoice = get_posts(array(
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
        
        if ($invoice) {
            $invoice = $invoice[0];
            include_once(WPIM_PATH_INCLUDES . '/printable-invoice-view.php');
        } else {
            echo '<p>Cannot find invoice.</p>';
        }
        

        $output = ob_get_contents();
        ob_end_clean();

        echo $output;
    }

    public function email_popup() {
        if (is_singular(['invoice'])) {
            add_action('wp_footer', function() {
                include_once(WPIM_PATH_INCLUDES . '/email-content.php');
            }, 100 );
        }
    }


    public function user_settings() {
        ob_start();

        if ( is_user_logged_in() ) {
            include_once(WPIM_PATH_INCLUDES . '/invoice-user-settings.php');
        } else {
            echo '<p>Not Logged In.</p>';
        }

        

        $output = ob_get_contents();
        ob_end_clean();

        echo $output;
    }

    public function save_user_settings() {
        global $current_user;
        if (isset($_POST['form_action']) && $_POST['form_action'] == 'invoice_user_settings'):
            require_once( ABSPATH . 'wp-admin/includes/file.php' );

            update_user_meta($current_user->ID, '_invoice_username', $_POST['_invoice_username'], '');
            update_user_meta($current_user->ID, '_invoice_email', $_POST['_invoice_email'], '');
            update_user_meta($current_user->ID, '_invoice_address', $_POST['_invoice_address'], '');
            update_user_meta($current_user->ID, '_invoice_phone', $_POST['_invoice_phone'], '');
            update_user_meta($current_user->ID, '_invoice_business_number', $_POST['_invoice_business_number'], '');
            update_user_meta($current_user->ID, '_invoice_prefix', $_POST['_invoice_prefix'], '');
            update_user_meta($current_user->ID, '_invoice_currency', $_POST['_invoice_currency'], '');
            update_user_meta($current_user->ID, '_invoice_date_format', $_POST['_invoice_date_format'], '');
            



            if ( $_FILES['_invoice_business_logo']) {
                $file = $_FILES['_invoice_business_logo'];
                $type = $file['type'];
                if ($type == 'image/png' || $type == 'image/jpeg') {
                    $business_logo = wp_handle_upload( $file, array( 'test_form' => false ) );
                    update_user_meta($current_user->ID, '_invoice_business_logo', $business_logo['url'], '');
                }    
            }
        endif;
        
    }

    public function send_invoice() {
        if (isset($_POST['data'])):
            
            $invoice_id = $_POST['data']['invoice_id'];
            $invoice = get_post($invoice_id);

            if (!empty($invoice)):

                $wpim_settings = get_option('wpim_settings');
                $invoice_page = isset($wpim_settings['invoice_page']) ? $wpim_settings['invoice_page'] : '';
                $estimate_page = isset($wpim_settings['estimate_page']) ? $wpim_settings['estimate_page'] : '';

                $link = '';
                $type = get_post_meta($invoice->ID, 'type', true);
                if ($type == 'invoice') {
                    $link = get_permalink($invoice_page) . '?uniid=' . get_post_meta($invoice->ID, 'unique_key', true);
                } else {
                    $link = get_permalink($estimate_page) . '?uniid=' . get_post_meta($invoice->ID, 'unique_key', true);
                }

                $subject = $_POST['data']['subject'];
                $body = $_POST['data']['body'];
                $link_word = ($_POST['data']['link_word']) ? $_POST['data']['link_word'] : $type;
                $body = str_replace('{invoicelink}', '<a href="'.$link.'">'.$link_word.'</a>',  $body);

                wp_mail(
                    get_post_meta($invoice->ID, 'to_email', true),
                    $subject,
                    $body,
                    array('Content-Type: text/html;')
                );
                
                die();
            endif;

        endif;
    }

    public function edit_product() {
        if (isset($_POST['data'])):

            $product_id = $_POST['data']['product_id'];
            $product = get_post($product_id);

            if ( !empty($product) ):
                ob_start();

                include_once(WPIM_PATH_INCLUDES . '/edit-product-modal.php');

                $output = ob_get_contents();
                ob_end_clean();

                echo $output;
                die();
            endif;
        endif;
    }

    public function save_edit_product() {
        if (isset($_POST['form_action']) && $_POST['form_action'] == 'edit_wpim_product'):
            

            $product_data = array(
                'ID'           => $_POST['product_id'],
                'post_title'   => $_POST['product_name'],
                'post_content' => $_POST['description'],
                'post_type'    => 'wpimx_product',
                'post_status'  =>  'publish',
                'meta_input'   => array(
                    '_product_price' => $_POST['_product_price']
                ),
            );
          
            wp_insert_post( $product_data );

        endif;
        
    }

    public function edit_client() {
        if (isset($_POST['data'])):

            $client_id = $_POST['data']['client_id'];
            $client = get_post($client_id);

            if ( !empty($client) ):
                ob_start();

                include_once(WPIM_PATH_INCLUDES . '/edit-client-modal.php');

                $output = ob_get_contents();
                ob_end_clean();

                echo $output;
                die();
            endif;
        endif;
    }

    public function save_edit_client() {
        if (isset($_POST['form_action']) && $_POST['form_action'] == 'edit_wpim_client'):
            

            $client_data = array(
                'ID'           => $_POST['client_id'],
                'post_title'   => $_POST['business_name'],
                'post_type'    => 'wpimx_client',
                'post_status'  =>  'publish',
                'meta_input'   => array(
                    '_client_name' => $_POST['client_name'],
                    '_client_address' => $_POST['_client_address'],
                    '_client_phone' => $_POST['_client_phone'],
                    '_client_email' => $_POST['_client_email'],
                    '_client_website' => $_POST['_client_website']
                ),
            );
          
            wp_insert_post( $client_data );

        endif;
        
    }

    public function update_invoice_status() {
        if (isset($_POST['data'])):

            $invoice_id = $_POST['data']['invoice_id'];
            $status = $_POST['data']['status'];

            if ( !empty($invoice_id) ):
                update_post_meta( $invoice_id, 'status',  $status);

                echo 'success';
                die();
            endif;
        endif;
    }

    public function extra_modal() {
        $status = $this->get_user_subscription_status();

        if ($status['status'] !== 'active') {
            add_action('wp_footer', function() {
                ob_start();

                include_once(WPIM_PATH_INCLUDES . '/subscribe-modal.php');

                $output = ob_get_contents();
                ob_end_clean();

                echo $output;
            });
        }

        add_action('wp_footer', function() {
            ob_start();

            include_once(WPIM_PATH_INCLUDES . '/save-first-warning-model.php');

            $output = ob_get_contents();
            ob_end_clean();

            echo $output;
        });
    }



    public function generate_pdf() {
        if (isset($_POST['form_action']) && $_POST['form_action'] == 'generate_pdf'):
            $invoice_id = $_POST['invoice_id'];
            $wpim_settings = get_option('wpim_settings');

            $upload_dir = wp_upload_dir();
            $wpim_upload_folder = $upload_dir['basedir'] . '/wpim-generated-pdf';
            if (!file_exists($wpim_upload_folder)) {
                mkdir($wpim_upload_folder, 0777, true);
            }
            
            $pdf_render_link = get_permalink($invoice_id) . '?rendering=pdf';
            $page_size = ($wpim_settings['select_pdf_size']) ? $wpim_settings['select_pdf_size'] : 'A4';
            $pdfshift_api_key = ($wpim_settings['pdfshift_api_key']) ? $wpim_settings['pdfshift_api_key'] : '';
            $file_name = get_post_meta($invoice_id, 'unique_key', true) . '.pdf';
            $local_file = $wpim_upload_folder . '/' . $file_name;

            PDFShift::setApiKey($pdfshift_api_key);
            PDFShift::convertTo($pdf_render_link, null, $local_file);

            wp_redirect($upload_dir['baseurl'] . '/wpim-generated-pdf/' . $file_name);
            
        endif;
    }
}
