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
     
    if ( !defined( 'DS' ) ) define( 'DS', DIRECTORY_SEPARATOR ); 
     
    $document = JFactory::getDocument(); 
     
    $document->addStyleSheet( 'components/com_antadmin/assets/css/antadmin.css' );
     
     // Require helper file
    JLoader::register('AntadminHelper', dirname(__FILE__) . DS . 'helpers' . DS . 'antadmin.php');

    // Get an instance of the controller prefixed by Antcontract
    $controller = JControllerLegacy::getInstance( 'Antadmin' );

    // Perform the request task
    $controller->execute(JRequest::getCmd( 'task' ));

    // Redirect if set by the controller
    $controller->redirect();
?>
