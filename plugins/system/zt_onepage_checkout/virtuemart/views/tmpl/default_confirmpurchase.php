<?php

/**
 * One Page Checkout For VirtueMart
 *
 * @version     1.0.2
 * @link        http://www.zootemplate.com
 * @author      ZooTemplate
 * @copyright   Copyright (c) 2015 CleverSoft (http://cleversoft.co/)
 * @license     GPL v2
 */

defined('_JEXEC') or die('Restricted access');
/**
 *
 */
$modelVM = ZtonepageModelVirtuemart::getInstance();
$shipModel = $modelVM->getShipto();
$shipTo = $shipModel['fields'];

$class = 'zt-opc-purchase';
?>

<div id="<?php echo $class; ?>-wrap" class="zt-opc-element">
    
        <h3 class="<?php echo $class; ?>-title zt-opc-title">
            <div class="zt-opc-step <?php echo $class; ?>-step">6</div><?php echo ZtonepageHelperText::_('CONFIRM_PURCHASE'); ?>
        </h3>
        <div class="inner-wrap">
            <div class="customer-comment-group">
                <label class="comment" for="<?php echo $class; ?>-field"><?php echo ZtonepageHelperText::_('NOTES'); ?></label>
                <textarea class="customer-comment inputbox" rows="3" cols="60" name="customer_note" id="<?php echo $class; ?>-field"></textarea>				
            </div>
            <div class="cart-tos-group">
                <label class="checkbox <?php echo $class; ?>-tos-label <?php echo $class; ?>-row" for="tos">
                    <input type="checkbox" value="1" name="tos" class="terms-of-service"><?php echo ZtonepageHelperText::_('AGREE'); ?>
                    <div class="terms-of-service">                        
                            <label for="tos">
                                    <a href="<?php echo JRoute::_ ('index.php?option=com_virtuemart&view=vendor&layout=tos&virtuemart_vendor_id=1', FALSE) ?>" class="terms-of-service" id="terms-of-service" rel="facebox"
                                       target="_blank">
                                            <span class="vmicon vm2-termsofservice-icon"></span>
                                            <?php echo ZtonepageHelperText::_('CLICK_HERE_TO_READ_TOS') ?>
                                    </a>
                            </label>

                            <div id="full-tos">
                                    <h2><?php echo vmText::_ ('COM_VIRTUEMART_CART_TOS') ?></h2>
                                    <?php echo $this->cart->vendor->vendor_terms_of_service ?>
                                    </div>
                    </div>
                </label>
            </div>
            <div class="<?php echo $class; ?>-row <?php echo $class; ?>-checkout-box">
                <button 
                    id="<?php echo $class; ?>-order-submit" 
                    class="<?php echo $class; ?>-btn btn btn-info" 
                    type="submit"
                    ><?php echo ZtonepageHelperText::_('CONFIRM_PURCHASE'); ?>
                </button>
            </div>
        </div>
   
</div>
