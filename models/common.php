<?php
    defined( '_JEXEC' ) or die;

    class AntadminModelCommon extends JModelForm
    {
            
            /**
             * 
             * Returns an array that represents a collection of vouchers. 
             * 
             * @author  Joshua Fuentes  <joshua.fuentes@antlabs.com>
             * 
             * @param   string      $type           Determines if the record to be fetched are generated or from online purchase
             * @param   string      $sorting        Type of sorting method
             * @param   int         $startIndex     Pagination start index
             * @param   int         $pageSize       Number of items per page
             */
            public function getGeneratedVouchers( $type, $sorting = 'transaction_number', $startIndex = 0, $pageSize = 10 ) {

                if ( $type == 0 ) {
                    $query = 'SELECT 
                                gv.transaction_number, 
                                gv.voucher as voucher, 
                                gv.product_id, 
                                gv.date_generated, 
                                gv.is_user_generated, 
                                gv.created_by,
                                p.product_name, 
                                p.product_code, 
                                hp.price_value, 
                                o.order_status as status 
                            FROM 
                                #__ant_generated_vouchers AS gv, 
                                #__hikashop_product AS p, 
                                #__hikashop_price as hp, 
                                #__hikashop_order as o 
                            WHERE 
                                p.product_id=gv.product_id AND 
                                hp.price_product_id=p.product_id AND 
                                gv.transaction_number=o.order_number AND 
                                gv.is_user_generated='.$type.' 
                            ORDER BY '.$sorting. ' LIMIT '.$startIndex.','.$pageSize; 
                } else if ( $type == 1 ) {
                        $query = 'SELECT 
                                gv.transaction_number, 
                                gv.voucher as voucher, 
                                gv.product_id, 
                                gv.date_generated, 
                                gv.is_user_generated, 
                                gv.created_by,
                                p.product_name, 
                                p.product_code, 
                                hp.price_value  
                            FROM 
                                #__ant_generated_vouchers AS gv, 
                                #__hikashop_product AS p, 
                                #__hikashop_price as hp  
                            WHERE 
                                p.product_id=gv.product_id AND 
                                hp.price_product_id=p.product_id AND 
                                gv.is_user_generated='.$type.' 
                            ORDER BY '.$sorting. ' LIMIT '.$startIndex.','.$pageSize; 


                } else {
                    return array(); 
                }

                $this->_db->setQuery( $query ); 
                return $this->_db->loadAssocList(); 
            }

            /**
             * Returns an object representing a prodcut givent the product id. 
             * 
             * @author  Joshua Fuentes <joshua.fuentes@antlabs.com>
             * 
             * @param   int     $productID     Reference id for product
             */
            public function getProduct( $productID ) {

                $this->_db->setQuery( 'SELECT * FROM #__hikashop_product WHERE product_id='.$productID ); 
                return $this->_db->loadObject(); 

            }
            
            /**
             * Returns a string representing the products filename image based
             * on the given product ID. 
             * 
             * @author      Joshua Fuentes      <joshua.fuentes@antlabs.com>
             */
            public function getProductImage( $productID ) {
                $this->_db->setQuery( 'SELECT file_path FROM #__hikashop_file WHERE file_type="product" AND file_ref_id='.$productID ); 
                
                return $this->_db->loadObject()->file_path;
            }
            
            /**
             * This method will return an interger, representing the order id 
             * of an order, given the transaction number
             * 
             * @author      Joshua Fuentes  <joshua.fuentes@antlabs.com>
             * 
             * @param       string  $transactionNumber  
             */
            public function getOrderID( $transactionNumber ) {
                assert( $transactionNumber ); 
                
                $this->_db->setQuery( 'SELECT order_id FROM #__hikashop_order WHERE order_number="'.$transactionNumber.'"' ); 
                
                $result =  $this->_db->loadObject(); 
                
                return (int) $result->order_id; 
                
            }
            
            /**
             * Returns an array of group IDs, that represent sub groups, from the
             * given name
             * 
             * @author  Joshua Fuentes  <joshua.fuentes@antlabs.com>
             * 
             * @parem   string      $name       Name of parent group
             * 
             */
            public function getSubgroupsFromName( $name ) {
                assert( $name ); 
                
                $this->_db->setQuery( 'SELECT category_id FROM #__hikashop_category WHERE category_name="'.trim( $name ).'"' ); 
                
                $parentCategoryID = $this->_db->loadObject(); 
                
                $this->_db->setQuery( 'SELECT category_id, category_name FROM #__hikashop_category WHERE category_parent_id='.$parentCategoryID->category_id ); 
                
                $subGroups = $this->_db->loadObjectList(); 
                
                return $subGroups;               
            }
            
            public function getProductsForGroup( $groupID ) {
                
                assert( $groupID ); 
                
                $this->_db->setQuery( 'SELECT 
                                            pc.product_id, 
                                            p.product_name 
                                        FROM 
                                            #__hikashop_product_category as pc, 
                                            #__hikashop_product as p 
                                        WHERE 
                                            pc.product_id=p.product_id AND 
                                            pc.category_id='.$groupID 
                ) ; 
                
                return $this->_db->loadObjectList(); 
                
            }
            
            /**
             * Returns the number of records given the type to be fetched
             * 
             * @author  Joshua Fuentes  <joshua.fuentes@antlabs.com>
             * 
             * @param   string      $type           Determines if the record to be fetched are generated or from online purchase
             */
            public function getTotalOfGeneratedVouchers( $type ) {
                
                if ( $type == 1 || $type == 0 ) {
                    $query = 'SELECT count(*) as Count FROM #__ant_generated_vouchers WHERE is_user_generated='.$type; 
                } else {
                    $query = 'SELECT count(*) as Count FROM #__ant_generated_vouchers';
                }
                
                $this->_db->setQuery( $query ); 
                
                return $this->_db->loadObject()->Count;
            }
            
            
            /**
             * Returns an array of user group names, from the given user group
             * ids of the current user
             * 
             * @param       $userGroupIDs       Arra of group IDs
             * @author Joshua Fuentes <joshua.fuentes@antlabs.com>
             */
            public function getUserGroups( $userGroupIDs ) {
                
                assert( $userGroupIDs ); 
                
                $query = 'SELECT id, title FROM #__usergroups WHERE ID in('.implode( ',', $userGroupIDs ).')'; 
                
                $this->_db->setQuery( $query ); 
                
                $this->_db->query(); 
                
                return $this->_db ->loadObjectList(); 
            }
            
            
            /**
             * Method to get the username remind request form.
             *
             * @param   array      $data        An optional array of data for the form to interogate.
             * @param   boolean    $loadData    True if the form is to load its own data (default case), false if not.
             * @return  JForm    A JForm object on success, false on failure
             * @since   1.6
             */
            public function getForm($data = array(), $loadData = true) {}

            /**
             * Method to auto-populate the model state.
             *
             * Note. Calling getState in this method will result in recursion.
             *
             * @since   1.6
             */
            protected function populateState()
            {
                    // Get the application object.
                    $app = JFactory::getApplication();
                    $params = $app->getParams('com_antadmin');

                    // Load the parameters.
                    $this->setState('params', $params);
            }

    }
