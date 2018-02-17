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

if (is_object($shipModel)) {
    $shipTo = $shipModel->fields;
    $shipTo = json_decode(json_encode($shipTo),true);    
}else {
    $shipTo = $shipModel['fields'];
}

$class = 'zt-opc-shipto';
?>

<div id="<?php echo $class; ?>-wrap" class="zt-opc-element">
    <h3 class="<?php echo $class; ?>-title zt-opc-title">
        <div class="zt-opc-step <?php echo $class; ?>-step">2</div><?php echo ZtonepageHelperText::_('SHIP_TO'); ?>
    </h3>
    <div class="inner-wrap">
        <label class="<?php echo $class; ?>-extend" id="<?php echo $class; ?>-extend-toogle" for="<?php echo $class; ?>-extend-input">
            <input 
                type="checkbox" checked="checked" id="<?php echo $class; ?>-extend-input" name="<?php echo $class; ?>-extend-input" onClick="jQuery('#zt-opc-shipto-wrap .edit-address').toggle();">
            Use for the shipto same as billto address</label>
        <div class="edit-address" style="display:none;">
            <?php foreach ($shipTo as $ship) : ?>
                <div id="<?php echo $ship['name']; ?>-group" class="form-group">
                    <div class="inner">
                        <label for="<?php echo $ship['name']; ?>_field" class="<?php echo $ship['name']; ?>"><?php echo $ship['title']; ?> <?php echo ($ship['required'] == 1) ? '<span class="required">*</span>' : ''; ?></label>
                        <?php echo $ship['formcode']; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <fieldset>
                <input type="hidden" name="address_type" value="ST">
            </fieldset>
        </div>
    </div>
</div>
