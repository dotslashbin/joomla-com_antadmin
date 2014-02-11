<?php
        /**
         * antadmin.php - ANTlabs Admin Component
         * 
         * @author Joshua Fuentes <joshua.fuentes@antlabs.com>
         * copyright Copyright (C) 1999-2013 ANTlabs - Advanced Network Technology Laboratories Pte. Ltd. All Rights Reserved.
         * license   GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
         * website   www.antlabs.com
         */

        defined( '_JEXEC' ) or die( 'Restricted Access' ); 
        
        jimport( 'joomla.applcation.component.controller' ); 
      
        class AntadminController extends JControllerLegacy {
            
            const MEDIA_PATH   = 'media/com_hikashop/upload/thumbnails/100x100/'; 
            
            /**
             * This method will log process the admin logging in 
             * 
             * @author      Joshua Fuentes      <joshua.fuentes@antlabs.com>
             */
            public function antAdminLogin() {
                
                jimport( 'joomla.user.authentication' );
                
                $application    = &JFactory::getApplication(); 
                
                $username       = &JRequest::getVar( 'email' ); 
                $password       = &JRequest::getVar( 'password' ); 
                $relogin        = &JRequest::getVar( 'relogin' ); 

                /**
                 * This will check the $relogin value,  which indicates if the 
                 * reidrection to this page is for relogin, or for  first 
                 * login
                 */
                if ( $relogin == '1' ) {
                    $application->redirect( 'index.php?option=com_antadmin', JText::_( 'RELOGIN_MESSAGE' ), 'message' ); 
                }
                $credentials    = array( 'username' => $username, 'password' => $password ); 
                
                $auhtentication = &JAuthentication::getInstance(); 
                
                $response       = $auhtentication->authenticate( $credentials, array() ); 
                
                if ($response->status != JAuthentication::STATUS_SUCCESS ) {
                    $application->redirect( 'index.php?option=com_antadmin', JText::_( 'FAILED_LOGIN_MESSAGE' ), 'error' ); 
                } else {
                    $siteApplication = &JFactory::getApplication( 'site' ); 
                    $siteApplication->login( $credentials, array() ); 
                    $application->redirect( 'index.php?option=com_antadmin' ); 
                }
                
            }

            /**
             * Default method to run
             */
            public function display( $cacheable = FALSE, $urlParams = FALSE ) {
                
                $document       = JFactory::getDocument(); 
                
                // Jquery UI Libraries
                $document->addStyleSheet( 'http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css' ); 
                $document->addScript( 'http://code.jquery.com/ui/1.10.3/jquery-ui.js' ); 
                
                // JTable
                $document->addStyleSheet( 'jtable/themes/basic/jtable_basic.min.css' ); 
                $document->addScript( 'jtable/jquery.jtable.min.js' ); 
                
                $viewFormat     = $document->getType(); 
                $user           = &JFactory::getUser();
                
                if ( empty( $user->id ) ) {
                    $viewToLoad = 'login'; 
                } else {
                    $viewToLoad = 'antadmin'; 
                }
                
                $model          = $this->getModel( 'common' ); 
                
                $view           = $this->getView( $viewToLoad, $viewFormat );                 
                $view->setModel( $model, TRUE ); 
                $view->document = $document; 
                $view->display(); 
            }
            
            /**
             * This method is the processor to generate voucher for the sales
             * admin. 
             * 
             * @author      Joshua Fuentes      <joshua.fuentes@antlabs.com>
             */
            public function generateVoucher() {
                
                // Fetching post variables
                $application    = &JFactory::getApplication(); 
                $SONumber       = &JRequest::getVar( 'so_number' ); 
                $productID      = &JRequest::getVar( 'product_id' ); 
                $model          = $this->getModel( 'common' );
                $user           = &JFactory::getUser(); 
                
                // Validate if there is an SO number entered
                if ( empty( $SONumber ) || $SONumber == JText::_( 'FORM_LABEL_SO' ) ) {
                    $application->redirect( 'index.php?option=com_antadmin', JText::_( 'BLANK_SO_ERROR_MESSAGE' ) , 'error' );
                }

                $product        = $model->getProduct( $productID );

                // Includes the helper
                JLoader::register( 'ANTVoucherHelper', 'helpers/ant_voucherhelpers.php' ); 
                $voucherHelper = new ANTVoucherHelper();

                $generatedVoucher = $voucherHelper->generateVoucher( 1, $product->product_code, $SONumber ); 

                $voucherCode = $generatedVoucher[0]->voucher_code; 

                $voucherHelper->saveGeneratedVouchers( $SONumber, $productID, $generatedVoucher, 1, $user->id ); 

                $application->redirect( 'index.php?option=com_antadmin&voucher_code='.$voucherCode );

            }
            
            /**
             * This will return a markup that will build a download voucher
             * link
             * 
             * @author      Joshua Fuentes      <joshua.fuentes@antlabs.com>
             */
            private function getDownloadVoucherLink( $record ) {
                
                $model      = $this->getModel( 'common' ); 
                
                $product    = $model->getProduct( $record[ 'product_id' ] ); 
                
                $voucher    = json_decode( $record[ 'voucher_object' ] ); 
                
                $productImage = $model->getProductImage( $record[ 'product_id' ] ); 
                
                $HTML       = ''; 
                
                $HTML       .= '<form method="post" action="voucher-download/sales-generated.php">'; 
                $HTML       .=      '<input type="hidden" name="transaction_number" value="'.$record[ 'transaction_number' ].'" />'; 
                $HTML       .=      '<input type="hidden" name="voucher_code" value="'.$voucher->voucher_code.'" />'; 
                $HTML       .=      '<input type="hidden" name="sku" value="'.$product->product_code.'" />'; 
                $HTML       .=      '<input type="hidden" name="price" value="'.$record[ 'price_value' ].'" />'; 
                $HTML       .=      '<input type="hidden" name="product_name" value="'.$product->product_name.'" />'; 
                $HTML       .=      '<input type="hidden" name="product_description" value="'.$product->product_description.'" />'; 
                $HTML       .=      '<input type="hidden" name="product_image" value="'.self::MEDIA_PATH.$productImage.'" />'; 
                $HTML       .=      '<input type="hidden" name="preprequisite" value="'.$product->prerequisite.'" />'; 
                $HTML       .=      '<input type="hidden" name="terms_conditions" value="'.$product->fineprint.'" />'; 
                $HTML       .=      '<input type="hidden" name="valid_from" value="'.$voucher->valid_from.'" />'; 
                $HTML       .=      '<input type="hidden" name="valid_until" value="'.$voucher->valid_until.'" />'; 
                $HTML       .=      '<input type="hidden" name="date_generated" value="'.$record[ 'date_generated' ].'" />'; 
                $HTML       .=      '<input type="submit" name="submit" class="button" value="'.JText::_( 'DOWNLOAD_LABEL' ).'" />'; 
                $HTML       .= '</form>'; 
                
                return $HTML; 
            }
            
            /**
             * This will return a string that is an HTML markup for a form. The
             * form will submit values to invoice-download, to give the user 
             * the feature of downloading a PDF version of the invoice
             * 
             * @author  Joshua Fuentes <joshua.fuentes@antlabs.com>
             * 
             * @param   string      $transaction_number     Transaction Number
             */
            private function getInvoiceDownloadForm( $transactionNumber ) {
                
                assert( $transactionNumber ); 
                
                include_once( JPATH_ADMINISTRATOR.'/components/com_hikashop/helpers/helper.php' ); 
                
                JLoader::register( 'ANTVoucherHelper', 'helpers/ant_voucherhelpers.php' ); 
                $voucherHelper = new ANTVoucherHelper(); 
                
                $orderClass = hikashop_get('class.order');
                
                $model  = $this->getModel( 'common' ); 
                $orderID = $model->getOrderID( $transactionNumber ); 
                $order = $orderClass->loadFullOrder( $orderID, FALSE, FALSE );
                
                $voucherHelper->populateProductDescriptions( $order ); 
                $voucherHelper->populateCurrencyName( $order ); 
                $voucherHelper->populateCBValues( $order ); 
                
                $strippedDownOrderObject = $voucherHelper->getStrippedDownOrderForPrinting( $order ); 
                
                $invoiceDownloadForm = '<form method="post" action="invoice-download/index.php">'; 
                $invoiceDownloadForm .=     '<input type="hidden" name="order_object" value="'.urlencode( json_encode( $strippedDownOrderObject ) ).'" />'; 
                $invoiceDownloadForm .=     '<input type="submit" value="DOWNLOAD" class="button" />'; 
                $invoiceDownloadForm .= '</form>'; 
                
                return $invoiceDownloadForm; 
            }
            
            /**
             * Return an array of the same records to be displayed, with the 
             * additional structures
             * 
             * @author  Joshua Fuentes  <joshua.fuentes@antlabs.com>
             * 
             * @param   Array   $records    Array of records derrived from DB
             */
            private function getStructuredRecords( $records ) {
                assert( $records ); 
                
                $structuredRecords = array(); 
                for( $iterator = 0; $iterator < count( $records ); $iterator++ ) {
                    $record = $records[ $iterator ]; 

                    $record[ 'voucher_object' ] = $record[ 'voucher' ]; 
                    
                    $voucherObject = json_decode( $record[ 'voucher' ] );
                    
                    // Build the voucher code to the expected array element
                    $record[ 'voucher' ] = $voucherObject->voucher_code; 
                    
                    // Capitalize the status
                    $record[ 'status' ] = ucwords( $record[ 'status' ] ); 
                    
                    // Set price value format
                    $record[ 'price_value' ] = number_format((float)$record[ 'price_value' ] , 2, '.', '').' (USD)';
                    
                    // Fetches download buttons that will enable downloading of 
                    // either voucher, or invoice. 
                    if ( $record[ 'is_user_generated' ] == 0 ) {
                        $record[ 'download_invoice' ] = $this->getInvoiceDownloadForm( $record[ 'transaction_number' ] ); 
                    } else {
                        $record[ 'download_voucher' ] =  $this->getDownloadVoucherLink( $record ); 
                    }
                    
                    // Fetches the name of the user who generated  the voucher
                    if ( $record[ 'created_by' ] > 0 ) {
                        $userWhoCreated = &JFactory::getUser( (int) $record[ 'created_by' ] ); 
                        $userName = ($userWhoCreated->name); 
                        $record[ 'created_by' ] = $userName; 
                    }
                    
                    // Fetches voucher status markup
                    $record[ 'voucher_status' ] = $this->getVoucherStatusForm( $record, $iterator ); 
                    
                    $structuredRecords[] = $record; 
                }
                
                return $structuredRecords; 
            }
            
            /**
             * This method will return a json string that represents the list 
             * of vouchers, depending on what parameters were passed through 
             * $_POST
             * 
             * @author Joshua Fuentes <joshua.fuentes@antlabs.com>
             */
            public function getVouchers() {
                
                // Fetching posted variables
                $pageSize           = &JRequest::getVar( 'jtPageSize' ); 
                $sorting            = &JRequest::getVar( 'jtSorting' ); 
                $startIndex         = &JRequest::getVar( 'jtStartIndex' ); 
                $type               = &JRequest::getVar( 'type' ); 
                
                // Initializations of models
                $model              = $this->getModel( 'common' ); 
                
                $typeValue          = ( $type == 'generated' )? 1:0; 
                
                $numberOfRecords    = $model->getTotalOfGeneratedVouchers( $typeValue ); 
                
                if ( $numberOfRecords > 0 ) {
                    $vouchers           = $model->getGeneratedVouchers( $typeValue, $sorting, $startIndex, $pageSize ); 
                } else {
                    $vouchers           = array(); 
                }
                
                // Rendering JSON object for result
                $tableResult = array(); 
                $tableResult['Result'] = "OK"; 
                $tableResult['Records'] = $this->getStructuredRecords( $vouchers ); 
                $tableResult['TotalRecordCount'] = $numberOfRecords;
                
                print json_encode( $tableResult ); 
                
                exit(); 
            }
            
            /**
             * Returns a markup that will build the "check" button to show the 
             * status of a voucher
             * @param type $record
             * @param type $index
             * @return string
             */
            private function getVoucherStatusForm( $record, $index ) {
                
                $classification = ( $record[ 'is_user_generated' ] == 0 )? 'purchased':'generated'; 
                
                $HTML = ''; 
                
                $HTML .= '<div id="'.$classification.'_activationstatus-'.$index.'">'; 
                $HTML .=    '<input type="hidden" id="'.$classification.'_codecontainer-'.$index.'" value="'.$record[ 'voucher' ].'" />'; 
                $HTML .=    '<input type="button" class="button '.$classification.'-status-check-button" id="'.$classification.'_statuscheck-'.$index.'" value="check" />'; 
                $HTML .= '</div>'; 
                
                return $HTML; 
            }
        }
?>
