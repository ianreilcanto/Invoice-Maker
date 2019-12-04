<div class="wpim-container">
    <div id="wpinvoicer">
        <div class="section-row">

            <form method="POST" @submit="saveInvoice($event)" id="wpim-form">
                <input type="hidden" name="invoice_action" value="save_invoice" />
                <div class="content-half paper-part">
                    <div class="header-info">
                        <div :class="[{'ok-3': !image_prev, 'ok-2': image_prev}, 'content-half']">
                            <div class="inline-field">
                                <label>Type</label>
                                <select name="type" v-model="type" class="input">
                                    <option value="invoice">Invoice</option>
                                    <option value="estimate">Estimate</option>
                                </select>
                            </div>
                            
                        </div>
                        <div :class="[{'ok-3': !image_prev, 'ok-2': image_prev}, 'content-half']">
                            <div class="logo-wrap">
                                <template v-if="image_prev">
                                    <div class="image-preview-wrap">
                                        <a href="#" @click.prevent="image_prev = null" class="close">x</a>
                                        <img v-bind:src="image_prev" />
                                    </div>
                                </template>
                                <template v-else>
                                    <label>Upload Logo</label>
                                    <div class="logo-container">
                                        <input type="file" id="wpim_file_input" @change="uploadPhoto($event)" />
                                    </div>
                                </template>
                                <input type="hidden" v-model="image_prev" name="image_prev" />
                            </div>
                        </div>
                        <div class="content-half ok-3" v-if="!image_prev">
                            <div class="">
                                <label>or Enter Company Name</label>
                                <input type="text" name="name" class="input" v-model="name" />
                            </div>
                            
                        </div>
                    </div>
                    <div class="information-wrap">
                        <div class="content-half ok-2">
                            <label class="info-label"><strong><span v-text="type"></span> From</strong></label>
                            <p>
                                <div class="inline-field">
                                    <label>Name<span class="required">*</span></label>
                                    <input type="text" v-model="from_name" name="from_name" class="input"  />
                                </div>
                            </p>
                            <p>
                                <div class="inline-field">
                                    <label>Email<span class="required">*</span></label>
                                    <input type="text" v-model="from_email" name="from_email" class="input"  />
                                </div>
                            </p>
                            <p>
                                <div class="inline-field">
                                    <label>Address</label>
                                    <input type="text" v-model="from_address" name="from_address" class="input"  />
                                </div>
                            </p>
                            <p>
                                <div class="inline-field">
                                    <label>Phone</label>
                                    <input type="text" v-model="from_phone" name="from_phone" class="input"  />
                                </div>
                            </p>
                            <p>
                                <div class="inline-field">
                                    <label>Business No.</label>
                                    <input type="text" v-model="from_business_number" name="from_business_number" class="input"  />
                                </div>
                            </p>
                        </div>
                        <div class="content-half ok-2 padding-right-dk">
                            <label class="info-label"><strong><span v-text="type"></span> To</strong></label>
                            <div class="is-suggest-block">
                                <div class="inline-field">
                                    <input type="hidden" v-model="to_id" name="to_id" />
                                    <label>Name<span class="required">*</span></label>
                                    <input type="text" v-model="to_name" name="to_name" class="input" autocomplete="off" @keyup="searchClient" @focus="client_open=true"   />
                                    <div class="client-suggestion suggest-div" v-if="clients.length && client_open">
                                        <ul class="suggest-ul">
                                            <template v-for="(client, index) in clients">
                                                <li @click="selectClient(index)">{{client.name}}</li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <p>
                                <div class="inline-field">
                                    <label>Email<span class="required">*</span></label>
                                    <input type="text" v-model="to_email" name="to_email" class="input"  />
                                </div>
                            </p>
                            <p>
                                <div class="inline-field">
                                    <label>Address</label>
                                    <input type="text" v-model="to_address" name="to_address" class="input"  />
                                </div>
                            </p>
                            <p>
                                <div class="inline-field">
                                    <label>Phone</label>
                                    <input type="text" v-model="to_phone" name="to_phone" class="input"  />
                                </div>
                            </p>
                        </div>
                    </div>

                    <hr />

                    <div class="invoice-details">
                        <div class="content-half ok-2">
                            <p>
                                <div class="inline-field">
                                    <label>Number</label>
                                    <input type="text" v-model="invoice_number" name="invoice_number" class="input"  />
                                </div>
                            </p>
                            <p>
                                <div class="inline-field">
                                    <label>Date</label>
                                    <date-picker v-model="date" name="date" :date-format="date_format" :date-def="date" v-once></date-picker>
                                </div>
                            </p>
                        </div>
                        <div class="content-half ok-2">
                            <p>
                                <div class="inline-field">
                                    <label>Terms</label>
                                    <select v-model="terms" name="terms" class="input">
                                        <option value="upon receipt">Upon Receipt</option>
                                        <option value="by date">By Date</option>
                                    </select>
                                </div>
                            </p>
                            <p>
                                <div class="inline-field">
                                    <label>Due</label>
                                    <date-picker v-model="due" name="due" :date-format="date_format"  :date-def="due" v-once></date-picker>
                                </div>
                            </p>
                        </div>
                    </div>

                    <div class="invoice-breakdown">
                        <input type="hidden" name="invoice_breakdown" v-model="invoice_breakdown" value="" />

                        <div class="invoice-breakdown-table-wrapper">
                            <table border="0" class="invoice-breakdown-table">
                                <thead>
                                    <tr>
                                        <th v-bind:style="{ backgroundColor: color}"></th>
                                        <th v-bind:style="{ backgroundColor: color}">Description</th>
                                        <th width="150" v-bind:style="{ backgroundColor: color}">Price</th>
                                        <th width="100" v-bind:style="{ backgroundColor: color}">Qty</th>
                                        <th width="150" v-bind:style="{ backgroundColor: color}">Amount</th>
                                        <th width="50" v-bind:style="{ backgroundColor: color}">Tax</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-if="items.length">
                                        <tr v-for="(item, index) in items" valign="top">
                                            <td width="20">
                                                <a href="#" @click.prevent="editItem(index)" >Edit</a> | <a href="#" @click.prevent="deleteItem(index)" >Delete</a>
                                            </td>
                                            <td>
                                                <p>{{item.description}}</p>
                                                <p>{{item.details}}</p>
                                            </td>
                                            <td>
                                                <p>{{item.price}}</p>
                                            </td>
                                            <td>
                                                <p>{{item.quantity}}</p>
                                            </td>
                                            <td>
                                                <p>{{item.amount}}</p>
                                            </td>
                                            <td>
                                                <p>{{(item.tax) ? 'yes' : 'no'}}</p>
                                            </td>
                                        </tr>
                                    </template>
                                    <template  v-else>
                                        <tr>
                                            <td colspan="6">
                                                <p style="text-align:center;">No Products added.</p>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <table border="0" class="invoice-breakdown-table all-products">
                            <tbody>
                                <tr valign="top" style="border-bottom:0;" class="product-fields-section">
                                    <td>
                                        <div class="is-suggest-block">
                                            <label>Product</label>
                                            <input type="text" v-model="item_desc" placeholder="Enter Product Name" autocomplete="off" @keyup="searchProduct" @focus="product_open=true" class="input"   />
                                            <div class="product-suggestion suggest-div" v-if="products.length && product_open">
                                                <ul class="suggest-ul">
                                                    <template v-for="(product, index) in products">
                                                        <li @click="selectProduct(index)">{{product.name}}</li>
                                                    </template>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p>
                                            <label>Price</label>
                                            <span class="with-symbol-field">
                                                <span class="currency-label" v-text="currency"></span>
                                                    <input type="number" v-model="item_price" placeholder="0.00 *" class="align-right" style="max-width:100%" />
                                                </span>
                                            </span>
                                        </p>
                                    </td>
                                    <td>
                                        <p>
                                            <label>Quantity</label>
                                            <input type="number" v-model="item_quantity"  min="1" step="1" placeholder="1 *" class="align-right" style="max-width:100%" />
                                        </p>
                                    </td>
                                    <td>
                                        <p class="align-right">
                                            <label>Amount</label>
                                            <span class="input" v-text="item_amount"></span>
                                        </p>
                                    </td>
                                    <td>
                                        <p>
                                            <label>Taxable</label>
                                            <input type="checkbox" v-model="item_tax" />
                                        </p>
                                    </td>
                                </tr>
                                <tr  class="product-fields-section">
                                    <td colspan="2">
                                        <p>
                                            <label>Description</label>
                                            <textarea rows="4" class="input" v-model="item_details" placeholder="Additional Details"></textarea>
                                        </p>
                                        <button class="btn button" @click.prevent="addItem">{{(editing_item != null) ? 'Update' : 'Add'}}</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <table border="0" class="invoice-breakdown-table total-table">
                            <tr>
                                <td align="right">
                                    <strong>Tax </strong>
                                    <span class="breakdown-value" v-if="tax_rate == 'fixed'" v-text="currency + ' ' + tax_computation"></span>
                                    <span class="breakdown-value" v-else v-text="tax_computation + '%'"></span>
                                </td>
                            </tr>
                            <tr>
                                <td align="right">
                                    <strong>Discount</strong>
                                    <span class="breakdown-value" v-if="discount == 'fixed'" v-text="currency + ' ' + discount_computation"></span>
                                    <span class="breakdown-value" v-else v-text="discount_computation + '%'"></span>
                                </td>
                            </tr>
                            <tr valign="right">
                                <td align="right">
                                    <strong>Subtotal</strong>
                                    <span class="breakdown-value" v-text="currency + ' ' + (subtotal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, thousands_separator))"></span>
                                    <input type="hidden" name="subtotal" v-model="subtotal" />
                                </td>
                            </tr>
                            <tr>
                                <td align="right">
                                    <strong>Total</strong>
                                    <span class="breakdown-value" v-text="currency + ' ' + (total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, thousands_separator))"></span>
                                    <input type="hidden" name="total" v-model="total" />
                                </td>
                            </tr>
                        </table>
                    </div>

                    <textarea v-model="note" name="note" rows="4" placeholder="Enter Note"></textarea>

                </div>
                <div class="content-half tools-part">

                    <div class="subscription-count" v-if="is_logged_in">
                        <table class="invoice-details-table" v-if="type == 'invoice'">
                            <tbody>
                                <tr>
                                    <td><strong>Account</strong></td>
                                    <td class="align-right"><span v-text="subscription.current_invoice_count"></span>/<span v-if="subscription.status == 'active'">unlimitted</span><span v-else>5</span></td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="invoice-details-table" v-else>
                            <tbody>
                                <tr>
                                    <td><strong>Account</strong></td>
                                    <td class="align-right" v-text="subscription.current_estimate_count">/<span v-if="subscription.status == 'active'">unlimitted</span><span v-else>5</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                
                    <div class="form-actions">
<button class="btn button" style="width: 100%;display:block; margin:3px">Save <span style="text-transform:uppercase" v-text="type"></span></button>
<a href="#" target="_blank" class="btn button grey" style="width: 100%;display:block; margin:3px" @click.prevent="warningSaveFirst">Preview</a>
<a href="#" class="btn button grey" style="width: 100%;display:block; margin:3px" @click.prevent="warningSaveFirst">Generate PDF</a>
<a href="#" class="btn button grey" style="width: 100%;display:block; margin:3px" @click.prevent="warningSaveFirst">Email Invoice</a>
</div>

                    <hr class="hr-margin" />

                    <div class="color-picker-block">
                        <label class="info-label"><strong>Color</strong></label>
                        <input type="hidden" name="color" v-model="color" />
                        <!-- color -->
                        <ul class="color-list">
                            <li :class="{ active: color === '#333' }">
                                <a href="#" style="background-color:#333" @click.prevent="chooseColor('#333')"></a>
                            </li>
                            <li :class="{ active: color === '#555' }">
                                <a href="#" style="background-color:#555" @click.prevent="chooseColor('#555')"></a>
                            </li>
                            <li :class="{ active: color === '#445a64' }">
                                <a href="#" style="background-color:#445a64" @click.prevent="chooseColor('#445a64')"></a>
                            </li>
                            <li :class="{ active: color === '#5d4037' }">
                                <a href="#" style="background-color:#5d4037" @click.prevent="chooseColor('#5d4037')"></a>
                            </li>
                            <li :class="{ active: color === '#c62828' }">
                                <a href="#" style="background-color:#c62828"  @click.prevent="chooseColor('#c62828')"></a>
                            </li>
                            <li :class="{ active: color === '#d81b60' }">
                                <a href="#" style="background-color:#d81b60"  @click.prevent="chooseColor('#d81b60')"></a>
                            </li>
                            <li :class="{ active: color === '#7b1fa2' }">
                                <a href="#" style="background-color:#7b1fa2"  @click.prevent="chooseColor('#7b1fa2')"></a>
                            </li>
                            <li :class="{ active: color === '#4526a0' }">
                                <a href="#" style="background-color:#4526a0"  @click.prevent="chooseColor('#4526a0')"></a>
                            </li>
                            <li :class="{ active: color === '#1564c0' }">
                                <a href="#" style="background-color:#1564c0"  @click.prevent="chooseColor('#1564c0')"></a>
                            </li>
                            <li :class="{ active: color === '#00695c' }">
                                <a href="#" style="background-color:#00695c"  @click.prevent="chooseColor('#00695c')"></a>
                            </li>
                            <li :class="{ active: color === '#2e7d32' }">
                                <a href="#" style="background-color:#2e7d32"  @click.prevent="chooseColor('#2e7d32')"></a>
                            </li>
                            <li :class="{ active: color === '#558b2f' }">
                                <a href="#" style="background-color:#558b2f" @click.prevent="chooseColor('#558b2f')"></a>
                            </li>
                        </ul>
                    </div>

                    <hr class="hr-margin" />

                    <label class="info-label"><strong> Tax </strong></label>
                    <p>
                        <select v-model="tax_rate" name="tax_rate">
                            <option value="percent">Percent</option>
                            <option value="fixed">Fixed</option>
                        </select>
                    </p>
                    <p>
                        <input v-model="tax_value" name="tax_value" type="text" placeholder="0.00" />
                    </p>

                    <hr class="hr-margin" />

                    <label class="info-label"><strong> Discount </strong></label>
                    <p>
                        <select v-model="discount" name="discount">
                            <option value="percent">Percent</option>
                            <option value="fixed">Fixed</option>
                        </select>
                    </p>
                    <p>
                        <input v-model="discount_value" name="discount_value" type="text" placeholder="0.00" />
                    </p>

                    <hr class="hr-margin" />

                    <label class="info-label"><strong> Currency </strong></label>
                    <p>
                        <select v-model="currency" name="currency">
                            <option value="USD">USD</option>
                            <option value="CAD">CAD</option>
                            <option value="GBP">GBP</option>
                            <option value="EURO">EURO</option>
                            <option value="PHP">PHP</option>
                            <option value="AUD">AUD</option>
                            <option value="JPY">JPY</option>
                        </select>
                    </p>

                    <hr class="hr-margin" />

                    <label class="info-label"><strong> Thousands Separator </strong></label>
                    <p>
                        <select v-model="thousands_separator" name="thousands_separator">
                            <option value="">None</option>
                            <option value=" ">Space( )</option>
                            <option value=",">Comma(,)</option>
                            <option value=".">Dot(.)</option>
                        </select>
                    </p>
                    
                </div>
            </form>

        </div>
    </div>
</div>