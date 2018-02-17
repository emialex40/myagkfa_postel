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
$class = 'zt-opc-cart';
?>

<div id="<?php echo $class; ?>-wrap" class="zt-opc-element">

    <h3 class="<?php echo $class; ?>-title zt-opc-title">
        <div class="zt-opc-step <?php echo $class; ?>-step">5</div><?php echo ZtonepageHelperText::_('CART'); ?>
    </h3>
    <!-- @todo Show sort information here -->

    <!-- BT address -->
    <div class="inner-wrap" style="padding:0px;">
        <?php echo $this->loadTemplate("cartsummary"); ?>
    </div>
    <?php echo $this->loadTemplate("coupon"); ?>
</div>

