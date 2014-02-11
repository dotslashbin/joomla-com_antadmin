<?php defined( '_JEXEC' ) or die ( 'Restricted Access' ); ?>
<div id="ant-voucher-generation-container" style="float: left; margin-top: -20px;">
    <fieldset>
        <legend id="generate-voucher-title">Generate Voucher</legend>
        <form method="post" action="index.php" >
        <div id="voucher-generation-fields-container">
            
            <div>
                <select name="product_id" id="product-list">
                    <?php
                        // IG 3100 Products
                        $prefix = 'IG3100'; 
                    ?>
                    <?php foreach( $this->IG3100Products as $productGroup ): ?>
                    <optgroup label="<?php echo $prefix; ?> <?php echo $productGroup->category_name; ?>">
                        <?php foreach( $productGroup->products as $product ): ?>
                        <option value="<?php echo $product->product_id; ?>"><?php echo $product->product_name; ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <input type="text" id="so_number" name="so_number" value="<?php echo JText::_( 'FORM_LABEL_SO' ); ?>" />
            </div>
            <div>
                <input type="hidden" name="option" value="com_antadmin">
                <input type="hidden" name="task" value="generateVoucher">
                <input type="submit" value="<?php echo JText::_( 'FORM_BUTTON_GENERATE' ); ?>" />
            </div>
            
        </div>
        </form>
        <div style="clear: both;"></div>
        <?php if( !empty( $this->generated_voucher ) ): ?>
        <div id="voucher-generation-result-container">
            <p><?php echo JText::_( 'VOUCHER_MESSAGE_PREFIX' ); ?> <?php echo $this->generated_voucher; ?></p>
        </div>
        <?php endif; ?>
    </fieldset>
</div>
