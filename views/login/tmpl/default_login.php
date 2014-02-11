<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
?>
<style>
    #ant-login-form-container div {
        margin-bottom: 10px;
    }
    
    #ant-login-form-container div input.field {
        height: 30px;
        width: 200px;
    }
    
</style>
<div id="ant-wrapper">
    <p><?php echo JText::_( 'FORM_LOGIN_MESSAGE' ); ?></p>
    
    <form method="post" action="index.php" >
        
    <div id="ant-login-form-container">
        <div>
            <input id="ant-email-field" class="field" type="text" name="email" value="<?php echo JText::_( 'FORM_LOGIN_EMAIL_LABEL' ); ?>" />
        </div>
        <div>
            <input id="ant-real-password-field" class="field" type="password" name="password" value="" style="display: none;" />
            <input id="ant-fake-password-field" class="field" type="text" name="fakepassword" value="<?php echo JText::_( 'FORM_LOGIN_PASSWORD_LABEL' ); ?>"  />
        </div>
        <div>
            <input type="hidden" name="option" value="com_antadmin" />
            <input type="hidden" name="task" value="antAdminLogin" />
            <input type="submit" value="login" />
        </div>
    </div>
        
    </form>
</div>
<script>
    jQuery( document ).ready(
            function() {
                jQuery( '#ant-email-field' ).click(
                        function() {
                            jQuery( this ).val( '' ); 
                        }
                ); 
                    
                jQuery( '#ant-fake-password-field' ).click(
                       function() {
                            jQuery( this ).hide(); 
                            jQuery( '#ant-real-password-field' ).show(); 
                            jQuery( '#ant-real-password-field' ).focus(); 
                       }
                )
                    
                jQuery( '#ant-fake-password-field' ).focus( 
                       function() {
                            jQuery( this ).hide(); 
                            jQuery( '#ant-real-password-field' ).show(); 
                            jQuery( '#ant-real-password-field' ).focus(); 
                       }
                )
            }
    ); 
</script>