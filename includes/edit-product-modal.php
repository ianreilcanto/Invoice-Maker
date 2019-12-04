<div id="wpim-edit-product-modal" class="oew-modal-wrap" style="display: none;">
    <div class="oew-modal-container">
        <div class="oew-modal-inner">
            <button type="button" class="oew-modal-close oew-modal-close-inside">
                <svg width="14" height="14" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve">
                    <path d="M505.943,6.058c-8.077-8.077-21.172-8.077-29.249,0L6.058,476.693c-8.077,8.077-8.077,21.172,0,29.249
                        C10.096,509.982,15.39,512,20.683,512c5.293,0,10.586-2.019,14.625-6.059L505.943,35.306
                        C514.019,27.23,514.019,14.135,505.943,6.058z"></path>
                    <path d="M505.942,476.694L35.306,6.059c-8.076-8.077-21.172-8.077-29.248,0c-8.077,8.076-8.077,21.171,0,29.248l470.636,470.636
                        c4.038,4.039,9.332,6.058,14.625,6.058c5.293,0,10.587-2.019,14.624-6.057C514.018,497.866,514.018,484.771,505.942,476.694z"></path>
                </svg>
            </button>

            <form id="edit-product-form" method="POST">
                <input type="hidden" name="form_action" value="edit_wpim_product" />
                <input type="hidden" name="product_id" value="<?= $product->ID ?>" />
                <div class="field-group">
                    <label>Product name</label>
                    <input type="text" name="product_name" placeholder="Enter Name" value="<?= $product->post_title ?>" />
                </div>
                <div class="field-group">
                    <label>Price</label>
                    <input type="number" name="_product_price" placeholder="Enter Price" value="<?= get_post_meta($product->ID, '_product_price', true) ?>" style="max-width:unset" />
                </div>
                <div class="field-group">
                    <label>Product Description</label>
                    <textarea rows="4" name="description" placeholder="Enter Description"><?= $product->post_content ?></textarea>
                </div>

                <button class="button" id="save-product">Save</button>
            </form>
        </div>
        <div class="oew-modal-overlay"></div>
    </div>
</div>