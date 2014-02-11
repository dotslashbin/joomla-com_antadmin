<?php

    defined('_JEXEC') or die;

    class AntadminViewAntadmin extends JViewLegacy
    {
            protected $form;

            protected $params;

            protected $state;

            protected $user;
            
            const VIEW_SALES                = 'sales'; 
            const VIEW_SUPPORT              = 'support'; 
            const SALES_ADMIN_GROUP_ID      = 10; 
            const SUPPORT_ADMIN_GROUP_ID    = 11; 
            const SUPPORT_MANAGER_GROUP_ID  = 22; 

            /**
             * Method to display the view.
             *
             * @param   string	The template file to include
             * @since   1.5
             */
            public function display( $tpl = null )
            {
                
                $language               = &JFactory::getLanguage(); 
                $application            = &JFactory::getApplication(); 
                $config                 = &JFactory::getConfig(); 
                $extension              = 'com_antadmin'; 
                $base_directory         = JPATH_COMPONENT.'/language'; 
                
                $model                  = $this->getModel(); 
                
                $user                   = &JFactory::getUser();
                $userGroups             = $model->getUserGroups( $user->groups ); 
                
                $dashboard              = $this->getDashboardFromGroup( $userGroups ); 
                
                /**
                 * This is to check if there is a dashboard to be loaded, based 
                 * on the credentials of the user. If ther is, then it will 
                 * proceed, otherwise, it will redirect back to the login 
                 * page prompting a message. 
                 */
                if ( $dashboard == NULL ) { 
                    $application->logout(); 
                    $application->redirect( 'index.php?option=com_antadmin&task=antAdminLogin&relogin=1' ); 
                }
                
                $this->assign( 'dashboard', $dashboard ); 
                
                $generatedVoucher       = &JRequest::getVar( 'voucher_code' ); 
                
                if ( !empty( $generatedVoucher ) ) {
                    $this->assignRef( 'generated_voucher', $generatedVoucher ); 
                }
                
                $tag                    = 'en-BG';
                $reload                 = TRUE; 
                
                $IG3100Products         = $this->getProductByGroup( $config->get( 'PRODUCT_GROUPNAME_IG3100' ) ); 
                
                $this->assignRef( 'IG3100Products', $IG3100Products ); 
                $this->assignRef( 'user', $user ); 
                
                $language->load( $extension, $base_directory, $tag, $reload ); 
                
                parent::display($tpl);
            }
            
            /**
             * Returns a structured array representing a group of products
             * 
             * @author  Joshua Fuentes  <joshua.fuentes@antlabs.com>
             * 
             * @param   string      $groupName          Name of the product group 
             */
            private function getProductByGroup( $groupName ) {
                
                $model = $this->getMOdel(); 
                
                $subGroups  = $model->getSubgroupsFromName( $groupName ); 
                
                foreach( $subGroups as $subgroup ) {
                    
                    $subgroup->products = $model->getProductsForGroup( $subgroup->category_id ); 
                }
                
                return $subGroups;
            }
            
            /**
             * Returns a string that will be used to determine which dashboard 
             * to load for the user
             * 
             * @param       $groups         Array of group names
             * @author Joshua Fuentes <joshua.fuentes@antlabs.com>
             */
            private function getDashboardFromGroup( $groups ) {
                
                $groupIDContainer = array(); 
                
                foreach( $groups as $group ) {
                    $groupIDContainer[] = $group->id; 
                }
                
                $config = &JFactory::getConfig();
                
                // Check for Sales Admin
                if ( in_array( self::SALES_ADMIN_GROUP_ID , $groupIDContainer ) ) {
                    return self::VIEW_SALES; 
                } else if ( in_array( self::SUPPORT_ADMIN_GROUP_ID, $groupIDContainer ) || in_array( self::SUPPORT_MANAGER_GROUP_ID , $groupIDContainer ) ) {
                    return self::VIEW_SUPPORT; 
                } else {
                    return NULL;
                }
            }
    }
