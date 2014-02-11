<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die ( 'Restricted Access' );
?>
<div style="margin-bottom: 20px; text-align: right; float: right; ">
    <div>
        <?php echo JText::_( 'USER_LABEL_PREFIX' ).$this->user->name; ?>
    </div>
    <div>
        <a href="index.php?option=com_comprofiler&task=logout" class="button" ><?php echo JText::_( 'LOGOUT_LABEL' ); ?></a>
    </div>
</div>
<?php 
    if ( $this->dashboard == 'sales' ) {
        include_once( JPATH_COMPONENT.'/views/common/voucher_generation_segment.php' ); 
    }
?>
<div style="clear: both;"></div>
<?php include_once( JPATH_COMPONENT.'/views/common/voucher_list_segment.php' ); ?>