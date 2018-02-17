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
$shipMentModel = $modelVM->getShipments();
$class = 'zt-opc-shipment';
?>

<div id="<?php echo $class; ?>-wrap" class="zt-opc-element">
    <h3 class="<?php echo $class; ?>-title zt-opc-title">
        <div class="zt-opc-step <?php echo $class; ?>-step">3</div>
        <?php echo ZtonepageHelperText::_('SHIPIMENT'); ?>
    </h3>
    <div class="inner-wrap">
        
            <fieldset>
                <?php
                if ($this->found_shipment_method)
                {
                    // if only one Shipment , should be checked by default
                    foreach ($this->shipments_shipment_rates as $shipment_shipment_rates)
                    {
                        if (is_array($shipment_shipment_rates))
                        {
                            foreach ($shipment_shipment_rates as $shipment_shipment_rate)
                            {
                                echo '<div class="vm-shipment-plugin-single">' . $shipment_shipment_rate . '</div>';
                            }
                        }
                    }
                }
                ?>
            </fieldset>
        
    </div>
</div>
