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
$class = 'zt-opc-coupon';
$ship['name'] = (isset($ship['name']) ? $ship['name'] : '');
?>
<span class="dotted-line"></span>
<div class="row" id="<?php echo $class; ?>-wrap" id="<?php echo $ship['name']; ?>-group">
    <div class="col-sm-3 col-md-3" style="text-align: right;">
        <lable><?php echo ZtonepageHelperText::_('COUPON'); ?>:</lable>
    </div>
    <div class="col-sm-6 col-md-6">
        <input type="text" class="form-control" name="coupon_code" id="<?php echo $class; ?>-code">
    </div>
    <div class="col-sm-3 col-md-3">
        <button type="button" class="btn btn-default"><?php echo ZtonepageHelperText::_('APPLY'); ?></button>
    </div>
</div>
