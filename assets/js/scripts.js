jQuery(function($){
    $('.send-invoice').on('click', function(e){
        e.preventDefault();

        var innerWidth = $( 'html' ).innerWidth();
        $( 'html' ).css( 'overflow', 'hidden' );
        var hiddenInnerWidth = $( 'html' ).innerWidth();
        $( 'html' ).css( 'margin-right', hiddenInnerWidth - innerWidth );

        // Open the modal
        $( '#wpim-email-content' ).fadeIn( 500 );
        var id = $(this).data('id');
        $('input[name="invoice_id"]').val(id);
        var type = $(this).parents('table').data('type');
        $('#inv-type').html(type);
    })

    $( '.oew-modal-close, .oew-modal-overlay' ).live( 'click', function() {
        $( 'html' ).css( {
            'overflow': '',
            'margin-right': '' 
        } );

        // Close the modal
        $( this ).closest( '.oew-modal-wrap' ).fadeOut( 500 );
    } );

    $('#send-invoice-email').on('click', function(e){
        e.preventDefault();

        var self = this
        $(this).text('Sending..');
        $(this).prop("disabled", true);

        var id = $('input[name="invoice_id"]').val();
        var subject = $('input[name="subject"]').val();
        var link_word = $('input[name="link_word"]').val();
        var body = $('textarea[name="body"]').val();

        $.post(
            post_data.ajax_url,
            { 
                data: {
                    'invoice_id': id,
                    'subject': subject,
                    'body': body,
                    'link_word' : link_word
                },
                action : 'send_invoice'
            }, 
            function( result, textStatus, xhr ) {
                $(self).text('Sent');
                $(self).removeProp('disabled');
                $( '#wpim-email-content' ).fadeOut( 500 );
            }
        ).fail(function() {
            $(self).text('Send');
        });
    })

    $('.edit-product').on('click', function(e){
        e.preventDefault();

        var innerWidth = $( 'html' ).innerWidth();
        $( 'html' ).css( 'overflow', 'hidden' );
        var hiddenInnerWidth = $( 'html' ).innerWidth();
        $( 'html' ).css( 'margin-right', hiddenInnerWidth - innerWidth );

        var id = $(this).data('id');

        $.post(
            post_data.ajax_url,
            { 
                data: {
                    'product_id': id
                },
                action : 'edit_product'
            }, 
            function( result, textStatus, xhr ) {
                $('body').find('#wpim-edit-product-modal').remove();
                $('body').append(result);
                $( '#wpim-edit-product-modal' ).fadeIn( 500 );
            }
        ).fail(function() {
            
        });
        
    })

    $('.edit-client').on('click', function(e){
        e.preventDefault();

        var innerWidth = $( 'html' ).innerWidth();
        $( 'html' ).css( 'overflow', 'hidden' );
        var hiddenInnerWidth = $( 'html' ).innerWidth();
        $( 'html' ).css( 'margin-right', hiddenInnerWidth - innerWidth );

        var id = $(this).data('id');

        $.post(
            post_data.ajax_url,
            { 
                data: {
                    'client_id': id
                },
                action : 'edit_client'
            }, 
            function( result, textStatus, xhr ) {
                $('body').find('#wpim-edit-client-modal').remove();
                $('body').append(result);
                $( '#wpim-edit-client-modal' ).fadeIn( 500 );
            }
        ).fail(function() {
            
        });
        
    })

    $('.invoice-status-dropdown').on('change', function(e){
        var id = $(this).data('id');
        var status = $(this).val();

        $.post(
            post_data.ajax_url,
            { 
                data: {
                    'invoice_id': id,
                    'status': status
                },
                action : 'update_invoice_status'
            }, 
            function( result, textStatus, xhr ) {
                // $('body').find('#wpim-edit-client-modal').remove();
                // $('body').append(result);
                // $( '#wpim-edit-client-modal' ).fadeIn( 500 );
            }
        ).fail(function() {
            
        });
    });

    $('#print-single-invoice').on('click', function(e){
        var content = document.getElementById('printable-invoice').innerHTML;
        var mywindow = window.open('', 'Print', 'height=600,width=800');

        mywindow.document.write('<!DOCTYPE html><html><head><title>Print</title>');
        mywindow.document.write('<link rel="stylesheet" id="oceanwp-style-css" href="'+post_data.home_url+'/wp-content/themes/oceanwp/assets/css/style.min.css" type="text/css" media="all">');
        mywindow.document.write('<link rel="stylesheet" type="text/css" href="'+post_data.home_url+'/wp-content/plugins/wp-invoice-maker/assets/css/styles.css">');
        mywindow.document.write(`<style type="text/css">
            .printable-header th {
                background-color: `+ this.color +` !important;
            }
        </style>`);
        mywindow.document.write('</head><body >');
        mywindow.document.write(content);
        mywindow.document.write('</body></html>');

        mywindow.document.close();
        mywindow.focus()
        mywindow.print();
        // mywindow.close();
        return true;
    });

    $('#pay-width-paypal').on('click', function(e){
        e.preventDefault();
        $('#paypal-button-block').slideToggle();
    });

    $('.opl-close-button').on('click', function(e) {
        var now = new Date();
        var time = now.getTime();
        time += 3600 * 1000;
        now.setTime(time);
        document.cookie = 'unregister=1; expires=' + now.toUTCString() + '; path=/';
    });

    $('#pay-width-card').on('click', function(e) {
        e.preventDefault();
        $('.stripe-button-el').trigger('click');
    });

    $('.myinv-send-invoice').on('click', function(e){
        e.preventDefault();
        var id = $(this).data('id');
        $('#generate-invoice-form input[name="invoice_id"').val(id);
        $('#generate-invoice-form').submit();
    })

    if ($('.wpim-datatable').length) {
        $('.wpim-datatable').DataTable({
            "pageLength": 20,
            "bLengthChange": false,
            "bFilter": true,
            "bInfo": false,
            "bAutoWidth": false,
            "responsive": true
        });
    }
})