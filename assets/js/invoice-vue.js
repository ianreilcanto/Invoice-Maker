let jQ = jQuery;
jQ.noConflict();

window.onload = function () {
    if (jQ('#wpinvoicer').length){
        Vue.component('date-picker', {
            template: '<input type="text" v-bind:name="name" autocomplete="off" v-model="dateDef" />',
            props: [ 'dateFormat', 'name', 'dateDef' ],
            mounted: function() {
                var self = this;
                jQ(this.$el).datepicker({
                    dateFormat: this.dateFormat,
                    onSelect: function(date) {
                        self.$emit('input', date);
                    }
                });
                let format = (post_data.user_meta._invoice_date_format) ? post_data.user_meta._invoice_date_format[0] : ''

                if (this.dateDef) {
                    let datef = this.dateDef
                    if (format == 'dd/mm/yy') {
                        let d = datef.split('/')
                        datef = d[1]+'/'+d[0]+'/'+d[2]
                    }
                    jQ(this.$el).datepicker('setDate', new Date(datef));
                }
                
            },
            beforeDestroy: function() {
                jQ(this.$el).datepicker('hide').datepicker('destroy');
            }
        });

        var app = new Vue({
            el: '#wpinvoicer',
            data: {
            title: '',
            name: '',
            type: 'invoice',
            from_name: '',
            from_email: '',
            from_address: '',
            from_phone: '',
            from_business_number: '',
            to_id: '',
            to_name: '',
            to_email: '',
            to_address: '',
            to_phone: '',
            invoice_number: '',
            date: (post_data.post_meta.date) ? post_data.post_meta.date[0] : '',
            terms: '',
            due: (post_data.post_meta.due) ? post_data.post_meta.due[0] : '',
            note: '',
            item_id: '',
            item_desc: '',
            item_details: '',
            item_price: '',
            item_quantity: 1,
            item_amount: '',
            item_tax: false,
            invoice_breakdown: '',
            items: [],
            tax_rate: 'percent',
            tax_value: '',
            discount: 'fixed',
            discount_value: '',
            image_prev: '',
            editing_item: null,
            color: '#333',
            clients: [],
            client_open: false,
            products: [],
            product_open: false,
            date_format: (post_data.user_meta._invoice_date_format) ? post_data.user_meta._invoice_date_format[0] : 'mm/dd/yy',
            currency: 'USD',
            invoice_prefix: '',
            register_trigger: true,
            subscription: '',
            is_logged_in: false,
            thousands_separator: '',
            _invoice_limit_count: 0
            },
            mounted() {
                this.is_logged_in = (post_data.is_logged_in) ? true : false
                this.subscription = post_data.user_subscription_status
                this.title = post_data.title;
                this.type = (post_data.post_meta.type) ? post_data.post_meta.type[0] : 'invoice';

                let _invoice_username = (post_data.user_meta._invoice_username) ? post_data.user_meta._invoice_username[0] : ''
                let _invoice_email = (post_data.user_meta._invoice_email) ? post_data.user_meta._invoice_email[0] : ''
                let _invoice_address = (post_data.user_meta._invoice_address) ? post_data.user_meta._invoice_address[0] : ''
                let _invoice_phone = (post_data.user_meta._invoice_phone) ? post_data.user_meta._invoice_phone[0] : ''
                let _invoice_business_number = (post_data.user_meta._invoice_business_number) ? post_data.user_meta._invoice_business_number[0] : ''
                let _invoice_business_logo = (post_data.user_meta._invoice_business_logo) ? post_data.user_meta._invoice_business_logo[0] : this.image_prev

                let urscur = (post_data.user_meta.currency) ? post_data.user_meta.currency[0] : this.currency
                let cur = (post_data.post_meta.currency) ? post_data.post_meta.currency[0] : urscur;
                this.currency = cur
                this.invoice_prefix = (post_data.user_meta._invoice_prefix) ? post_data.user_meta._invoice_prefix[0] : ''
                this._invoice_limit_count = (post_data.user_meta._invoice_limit_count) ? post_data.user_meta._invoice_limit_count[0] : 5

                this.name = (post_data.post_meta.name) ? post_data.post_meta.name[0] : '';
                this.from_name = (post_data.post_meta.from_name) ? post_data.post_meta.from_name[0] : _invoice_username;
                this.from_email = (post_data.post_meta.from_email) ? post_data.post_meta.from_email[0] : _invoice_email;
                this.from_address = (post_data.post_meta.from_address) ? post_data.post_meta.from_address[0] : _invoice_address;
                this.from_phone = (post_data.post_meta.from_phone) ? post_data.post_meta.from_phone[0] : _invoice_phone;
                this.from_business_number = (post_data.post_meta.from_business_number) ? post_data.post_meta.from_business_number[0] : _invoice_business_number;


                this.to_id = (post_data.post_meta.to_id) ? post_data.post_meta.to_id[0] : '';
                this.to_name = (post_data.post_meta.to_name) ? post_data.post_meta.to_name[0] : '';
                this.to_email = (post_data.post_meta.to_email) ? post_data.post_meta.to_email[0] : '';
                this.to_address = (post_data.post_meta.to_address) ? post_data.post_meta.to_address[0] : '';
                this.to_phone = (post_data.post_meta.to_phone) ? post_data.post_meta.to_phone[0] : '';
                this.invoice_number = (post_data.post_meta.invoice_number) ? post_data.post_meta.invoice_number[0] : this.invoice_prefix;
                
                this.terms = (post_data.post_meta.terms) ? post_data.post_meta.terms[0] : '';

                this.note = post_data.note;
                this.item_desc = (post_data.post_meta.item_desc) ? post_data.post_meta.item_desc[0] : '';
                this.item_details = (post_data.post_meta.item_details) ? post_data.post_meta.item_details[0] : '';
                this.item_price = (post_data.post_meta.item_price) ? post_data.post_meta.item_price[0] : '';
                this.item_quantity = (post_data.post_meta.item_quantity) ? post_data.post_meta.item_quantity[0] : '1';
                this.item_tax = (post_data.post_meta.item_tax) ? post_data.post_meta.item_tax[0] : '';
                this.invoice_breakdown = (post_data.post_meta.invoice_breakdown) ? post_data.post_meta.invoice_breakdown[0] : '';
                this.items = (post_data.post_meta.invoice_breakdown) ? JSON.parse(post_data.post_meta.invoice_breakdown[0]) : [];
                this.tax_rate = (post_data.post_meta.tax_rate) ? post_data.post_meta.tax_rate[0] : 'fixed';
                this.tax_value = (post_data.post_meta.tax_value) ? post_data.post_meta.tax_value[0] : '';
                this.image_prev = (post_data.post_meta.image_prev) ? post_data.post_meta.image_prev[0] : _invoice_business_logo;
                this.color = (post_data.post_meta.color) ? post_data.post_meta.color[0] : this.color;
                this.thousands_separator = (post_data.post_meta.thousands_separator) ? post_data.post_meta.thousands_separator[0] : this.thousands_separator;

            },
            watch: {
                item_price: function() {
                    this.item_amount = this.item_price * this.item_quantity;
                    return this.item_amount.toFixed(2)
                },
                item_quantity: function() {
                    this.item_amount = this.item_price * this.item_quantity;
                }
            },
            computed: {
                subtotal: function() {
                    let total = 0;
                    for ( var i = 0; i < this.items.length; i++ ) {
                        total += this.items[i]['amount']
                    }
                    return total.toFixed(2)
                },
                taxable_subtotal:  function() {
                    let total = 0;
                    for ( var i = 0; i < this.items.length; i++ ) {
                        if (this.items[i]['tax']) {
                            total += this.items[i]['amount']
                        }
                    }

                    return total.toFixed(2)
                },
                tax_computation: function() {
                    if (!this.tax_value)
                        return 0;

                    if (this.tax_rate == 'fixed') {
                        return this.tax_value.toFixed(2)
                    } else {
                        let part = (parseInt(this.tax_value) * parseFloat(this.taxable_subtotal))
                        rate = (parseFloat(part) / 100);

                        return rate.toFixed(2)
                    }
                },
                discount_computation:  function() {
                    if (!this.discount_value)
                        return 0;

                    if (this.discount == 'fixed') {
                        return this.discount_value.toFixed(2)
                    } else {
                        let part = (parseInt(this.discount_value) * parseFloat(this.subtotal))
                        rate = (parseFloat(part) / 100);

                        return rate.toFixed(2)
                    }
                },
                total: function() {

                    if (!this.items.length)
                        return 0;

                    let taxable_total = 0;
                    let untaxable_total = 0;

                    for ( var i = 0; i < this.items.length; i++ ) {
                        if (this.items[i]['tax']) {
                            taxable_total += this.items[i]['amount']
                        } else {
                            untaxable_total += this.items[i]['amount']
                        }
                        
                    }

                    let taxed_value = parseFloat(taxable_total) + parseFloat(this.tax_computation);
                    let subtotal = parseFloat(taxed_value) + parseFloat(untaxable_total);
                    let total = parseFloat(subtotal) - this.discount_computation

                    return total.toFixed(2);
                    
                }
            },
            methods: {
                addItem: function() {

                    if (!this.item_desc || !this.item_price || !this.item_quantity) {
                        alert('Fill required fields');
                        return;
                    }

                    let new_item = {
                        id: this.item_id,
                        description: this.item_desc,
                        details: this.item_details,
                        price: this.item_price,
                        quantity: this.item_quantity,
                        amount: this.item_amount,
                        tax: this.item_tax
                    }

                    if (this.editing_item != null) {
                        this.items[this.editing_item] = new_item
                        this.editing_item = null
                    } else {
                        this.items.push(new_item);
                    }

                    this.invoice_breakdown = JSON.stringify(this.items);

                    this.item_id = ''
                    this.item_desc = ''
                    this.item_details = ''
                    this.item_price = ''
                    this.item_quantity = 1
                    this.item_amount = ''
                    this.item_tax = false
                },
                editItem: function(index) {
                    this.editing_item = index
                    let item = this.items[index]
                    this.item_id = item.id
                    this.item_desc = item.description
                    this.item_details = item.details
                    this.item_price = item.price
                    this.item_quantity = item.quantity
                    this.item_amount = item.amount
                    this.item_tax = item.tax
                },
                deleteItem: function(index) {
                    if (confirm('Are you sure?')) {
                        let item = this.items.splice(index, 1)
                        this.invoice_breakdown = JSON.stringify(this.items);
                    }
                    
                },
                chooseColor (color) {
                    this.color = color;
                },
                uploadPhoto(event) {
                    var file = event.target.files;
                    var data = new FormData();
                    data.append("action", "wpim_file_upload");
                    jQ.each(file, function(key, value) {
                        data.append("wpim_file_upload", value);
                    });

                    let vue = this
                    jQ.ajax({
                        url: post_data.ajax_url,
                        type: 'POST',
                        data: data,
                        cache: false,
                        dataType: 'json',
                        processData: false, // Don't process the files
                        contentType: false, // Set content type to false as jQuery will tell the server its a query string request
                        success: function(data, textStatus, jqXHR) {	
                            if( data.response == "SUCCESS" ){
                                var preview = "";
                                if( data.type === "image/jpg" 
                                    || data.type === "image/png" 
                                    || data.type === "image/gif"
                                    || data.type === "image/jpeg"
                                ) {
                                    vue.image_prev = data.url
                                } else {
                                    preview = data.filename;
                                }
                            } else {
                                alert( data.error );
                            }
        
                        }
                    });
                },
                saveInvoice(event) {

                    var unregister = this.getCookie('unregister');
                    var iu = (unregister) ? false : this.register_trigger
                    iu = (this.is_logged_in) ? false : true

                    if (iu) {

                        if (jQ('body').find('a[href="#opl-login-form"]').length) {
                            
                            if ($j("meta[name='wpim-popup-condition']").length) {
                                $j("meta[name='wpim-popup-condition']").attr("content", 'from-form');
                            } else {
                                $j('head').append('<meta name="wpim-popup-condition" content="from-form">');
                            }

                            jQ('.opl-link').trigger('click');
                            this.register_trigger = false
                            event.preventDefault();
                            return false
                        }

                        event.preventDefault();

                    } else {
                        
                        let post_id = jQ('input[name="post_id"]').val();

                        if (!post_id) {
                            let user_status = post_data.user_subscription_status;

                            if (this.type === 'invoice') {
                                if (user_status.status !== 'active' && user_status.current_invoice_count >= this._invoice_limit_count) {
                                    jQ( '#subscription-modal' ).fadeIn( 500 );
                                    event.preventDefault();
                                    return false;
                                }
                            }
                            else {
                                if (user_status.status !== 'active' && user_status.current_estimate_count >= this._invoice_limit_count) {
                                    jQ( '#subscription-modal' ).fadeIn( 500 );
                                    event.preventDefault();
                                    return false;
                                }
                            }
                        }

                    }

                    if ( this.from_name == '' ||
                        this.from_email == '' ||
                        this.to_name == '' ||
                        this.to_email == '') {
                        event.preventDefault();
                        alert('Fill required fields');
                        return;
                    }

                    if (!this.items.length) {
                        event.preventDefault();
                        alert('No items found on the invoice.')
                        return;
                    }
                    

                },
                getCookie(cname) {
                    var name = cname + "=";
                    var decodedCookie = decodeURIComponent(document.cookie);
                    var ca = decodedCookie.split(';');
                    for(var i = 0; i <ca.length; i++) {
                        var c = ca[i];
                        while (c.charAt(0) == ' ') {
                            c = c.substring(1);
                        }
                        if (c.indexOf(name) == 0) {
                            return c.substring(name.length, c.length);
                        }
                    }
                    return "";
                },
                searchClient: _.debounce(function () {
                    let vue = this
                    jQ.post(
                        post_data.ajax_url,
                        { 
                            data: {
                                's': this.to_name
                            },
                            action : 'get_clients'
                        }, 
                        function( result, textStatus, xhr ) {
                            vue.clients = JSON.parse(result)
                        }
                    ).fail(function() {
                        // console.log('fail')
                    });
                    this.client_open = true
                }, 500),
                selectClient(index) {
                    let client = this.clients[index]
                    this.to_id = client.id
                    this.to_name = client.name
                    this.to_email = client.email
                    this.to_address = client.address
                    this.to_phone = client.phone
                    this.client_open = false
                },
                searchProduct: _.debounce(function () {
                    let vue = this
                    jQ.post(
                        post_data.ajax_url,
                        { 
                            data: {
                                's': this.item_desc
                            },
                            action : 'get_products'
                        }, 
                        function( result, textStatus, xhr ) {
                            vue.products = JSON.parse(result)
                        }
                    ).fail(function() {
                        // console.log('fail')
                    });
                    this.product_open = true
                }, 500),
                selectProduct(index) {
                    let product = this.products[index]
                    this.item_id = product.id
                    this.item_desc = product.name
                    this.item_details = product.description
                    this.item_price = product.price
                    this.product_open = false
                },
                generateInvoice() {
                    jQ('#generate-invoice-form').submit();
                },
                emailInvoice(id) {
                    var innerWidth = jQ( 'html' ).innerWidth();
                    jQ( 'html' ).css( 'overflow', 'hidden' );
                    var hiddenInnerWidth = jQ( 'html' ).innerWidth();
                    jQ( 'html' ).css( 'margin-right', hiddenInnerWidth - innerWidth );

                    // Open the modal
                    jQ( '#wpim-email-content' ).fadeIn( 500 );
                    jQ('input[name="invoice_id"]').val(id);
                    var type = jQ(this).parents('table').data('type');
                    jQ('#inv-type').html(type);
                },
                warningSaveFirst() {
                    jQ( '#warning-save-first-modal' ).fadeIn( 500 );
                }
            }
        });
    }//endif
};