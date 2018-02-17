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
$payMentModel = $modelVM->getPayments();
$class = 'zt-opc-payment';
?>

<div id="<?php echo $class; ?>-wrap" class="zt-opc-element">
    <h3 class="<?php echo $class; ?>-title zt-opc-title">
        <div class="zt-opc-step <?php echo $class; ?>-step">4</div>
        <?php echo ZtonepageHelperText::_('PAYMENT'); ?>
    </h3>
    <div class="inner-wrap">
        <fieldset>
            <?php
            if ($this->found_payment_method)
            {


                echo '<fieldset class="vm-payment-shipment-select vm-payment-select">';
                foreach ($this->paymentplugins_payments as $paymentplugin_payments)
                {
                    if (is_array($paymentplugin_payments))
                    {
                        foreach ($paymentplugin_payments as $paymentplugin_payment)
                        {
                            echo '<div class="vm-payment-plugin-single">' . $paymentplugin_payment . '</div>';
                        }
                    }
                }
                echo '</fieldset>';
            } else
            {
                echo '<h1>' . $this->payment_not_found_text . '</h1>';
            }
            ?>
        </fieldset>
    </div>
</div>
