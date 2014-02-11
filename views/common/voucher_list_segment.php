<?php defined( '_JEXEC' ) or die( 'Restricted Access' ); ?>
<div id="ant-voucher-list-container">
    <div id="tabs">
        <ul>
            <?php if ( $this->dashboard == 'sales' ): ?>
            <li><a href="#tabs-1"><?php echo JText::_( 'TAB_GENERATED_VOUCHER' ); ?></a></li>
            <?php endif; ?>
            <li><a href="#tabs-2"><?php echo JText::_( 'TAB_ONLINE_PURCHASED' ); ?></a></li>
        </ul>
        
        <?php if ( $this->dashboard == 'sales' ): ?>
        <div id="tabs-1">
            <div id="generated-vouchers-container"></div>
        </div>
        <?php endif; ?>
        
        <div id="tabs-2">
            <div id="online-purchased-vouchers"></div> 
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(function() {
        jQuery( "#tabs" ).tabs();
        
        jQuery( '#so_number' ).click(
            function() {
                jQuery( this ).val( '' ); 
            }
        ); 
    });
</script>
<script type="text/javascript">
    jQuery(document).ready(function () {
        <?php if( $this->dashboard == 'sales' ): ?>
            jQuery('#generated-vouchers-container').jtable({
                title: 'Generated Vouchers',
                paging: true, 
                pageSize: 10, 
                sorting: true, 
                actions: {
                   listAction: 'index.php?option=com_antadmin&task=getVouchers&type=generated' 
                }, 
                fields: {
                    transaction_number: {
                        title: 'SO Number'
                    },
                    product_code: {
                        title: 'SKU'
                    }, 
                    price_value: {
                        title: 'Price', 
                        sorting: false
                    }, 
                    date_generated: {
                        title: 'Date Created'
                    }, 
                    created_by: {
                        title: 'Created By'
                    }, 
                    voucher: {
                        title: 'Voucher',
                        sorting: false
                    }, 
                    download_voucher: {
                        title: 'Download Voucher', 
                        sorting: false
                    }, 
                    voucher_status: {
                        title: 'Voucher Status', 
                        sorting: false
                    }
                }, 
                recordsLoaded: function( event, data ) {
                jQuery( '.generated-status-check-button' ).click(
                    function() {
                        var elementID = jQuery( this ).attr('id' ); 
                        
                        var container = elementID.split( '-' ); 
                        
                        checkVoucherStatus( 'generated_codecontainer-' + container[1] ); 
                    }
                ); 
            }
            });


        <?php endif; ?>
        
        jQuery('#online-purchased-vouchers').jtable({
            title: 'Vouchers purchased from online store',
            paging: true, 
            pageSize: 10, 
            sorting: true, 
            actions: {
               listAction: 'index.php?option=com_antadmin&task=getVouchers&type=purchased_online' 
            }, 
            fields: {
                transaction_number: {
                    title: 'Transaction Number',
                    
                },
                product_code: {
                    title: 'SKU',
                },
                price_value: {
                    title: 'Price',
                    sorting: false
                }, 
                date_generated: {
                    title: 'Date Created',
                },                 
                voucher: {
                    title: 'Voucher', 
                    sorting: false
                }, 
                status: {
                    title: 'Status', 
                    sorting: false
                }, 
                download_invoice: {
                    title:  'Invoice PDF', 
                    sorting: false
                },
                voucher_status: {
                    title: 'Voucher Status', 
                    sorting: false
                }
            }, 
            recordsLoaded: function( event, data ) {
                jQuery( '.purchased-status-check-button' ).click(
                    function() {
                        var elementID = jQuery( this ).attr('id' ); 
                        
                        var container = elementID.split( '-' ); 
                        
                        checkVoucherStatus( 'purchased_codecontainer-' + container[1] ); 
                    }
                ); 
            }
        });
        
        // Triggering a click on the first column of the table to allow fetching of data
        jQuery( 'div#generated-vouchers-container > div > table > thead > tr > th:first-child' ).trigger( 'click' ); 
        jQuery( 'div#online-purchased-vouchers > div > table > thead > tr > th:first-child' ).trigger( 'click' ); 
        
        
        function checkVoucherStatus( fieldName ) {

            var voucherCode = jQuery( '#' + fieldName ).val(); 
            
            jQuery.ajax({
                type: "POST",
                url: "voucher-download/check-activation.php",
                data: { voucher_code:voucherCode }
            }).done( function( data ) {
                
                var container = fieldName.split( '-' ); 

                var index = container[1]; 

                var prefixContainer = container[0].split( '_' ); 

                var prefix = prefixContainer[0]; 

                if ( data == 'enable' ) {
                    jQuery( '#' +prefix + '_activationstatus-' +index ).html( 'Activated' )
                } else {
                    jQuery( '#' +prefix + '_activationstatus-' +index ).html( 'Not Activated' )
                }
            });
        }
    });
</script>
