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
$billModel = $modelVM->getBillTo();
if (is_object($billModel)) {
    $billTo = $billModel->fields;
    $billTo = json_decode(json_encode($billTo),true);    
}else {
    $billTo = $billModel['fields'];
}

$class = 'zt-opc-billto';
?>

<div id="<?php echo $class; ?>-wrap" class="zt-opc-element">
    <h3 class="<?php echo $class; ?>-title zt-opc-title">
        <div class="zt-opc-step <?php echo $class; ?>-step">1</div><?php echo ZtonepageHelperText::_('BILL_TO'); ?>
    </h3>

    <!-- @todo Show sort information here -->

    <!-- BT address -->
    <div class="inner-wrap">
        <span class="label label-info"><?php echo JFactory::getUser()->email; ?></span>
        <p></p>
        <!-- @todo Show Edit button to expand below form -->
        <div class="edit-address billto" >

            <?php foreach ($billTo as $bill) : ?>
                <?php if(!in_array($bill['name'], array('username', 'password', 'password2', 'delimiter_userinfo', 'agreed'))): ?>
                        <div id="<?php echo $bill['name']; ?>-group" class="form-group">
                            <div class="inner">
                                <label for="<?php echo $bill['name']; ?>_field" class="<?php echo $bill['name']; ?>"><?php echo $bill['title']; ?> <?php echo ($bill['required'] == 1) ? '<span class="required">*</span>' : ''; ?></label>
                                <?php echo $bill['formcode']; ?>
                            </div>
                        </div>
                <?php endif; ?>
            <?php endforeach; ?>
            <fieldset>
                <input type="hidden" name="address_type" value="BT">
            </fieldset>
            
        </div>
        <?php if (JFactory::getUser()->guest) : ?>
        <label class="<?php echo $class; ?>-extend" id="<?php echo $class; ?>-extend-toogle" for="<?php echo $class; ?>-extend-input">
            <input type="checkbox" id="<?php echo $class; ?>-extend-input" name="<?php echo $class; ?>-extend-input" onClick="jQuery('#zt-opc-billto-checkout-later').toggle();">
            Create an account for later use
        </label>
        <div class="edit-address billto" id="<?php echo $class; ?>-checkout-later" style="display:none;">
            <?php foreach ($billTo as $bill) : ?>
                <?php if(in_array($bill['name'], array('username', 'password', 'password2'))): ?>
            
                        <div id="<?php echo $bill['name']; ?>-group" class="form-group">
                            <div class="inner">
                                <label for="<?php echo $bill['name']; ?>_field" class="<?php echo $bill['name']; ?>"><?php echo $bill['title']; ?> <?php echo ($bill['required'] == 1) ? '<span class="required">*</span>' : ''; ?></label>
                                <?php echo $bill['formcode']; ?>
                            </div>
                        </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
