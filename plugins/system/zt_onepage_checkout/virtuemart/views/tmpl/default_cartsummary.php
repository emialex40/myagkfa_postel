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

$class = 'zt-opc-cart';
$cart = VirtueMartCart::getCart(true);
$cart->prepareVendor();
$cart->prepareCartData();
$this->cart = $cart;
?>
<fieldset class="vm-fieldset-pricelist">
    <table class="cart-summary">
        <!-- Begin Heading -->
        <thead style="background-color: #F7F6F6;">
            <tr>
                <th align="left" class="col-name"><?php echo vmText::_('COM_VIRTUEMART_CART_NAME') ?></th>
                <th align="left" class="col-name"><?php echo vmText::_('COM_VIRTUEMART_CART_PRICE') ?></th>
                <th align="left" class="col-qty"><?php echo vmText::_('COM_VIRTUEMART_CART_QUANTITY') ?></th>                
                <th align="left" class="col-qty"><?php echo vmText::_('Tax') ?></th>                
                <th align="left" class="col-qty"><?php echo vmText::_('Discount') ?></th>                
                <th align="left" class="col-total" colspan="2"><?php echo vmText::_('COM_VIRTUEMART_CART_TOTAL') ?></th>

            </tr>
        </thead>
        <!-- End heading -->
        <?php
        $i = 1;

        foreach ($this->cart->products as $pKey => $prow)
        {
            ?>           
            <?php
            $model = ZtonepageModelVirtuemart::getInstance();
            $media = $model->getMedia($prow->virtuemart_product_id);
            ?>
            <tbody>
                <tr valign="top" class="sectiontableentry<?php echo $i ?>">
            <input type="hidden" name="cartpos[]" value="<?php echo $pKey ?>">
            <!-- Name -->
            <td class="col-name">
                <div class="<?php echo $class; ?>-product-image">
                    <?php if ($media) : ?>

                        <div class="p-info-inner">
                            <img class="img-reponsive" alt="" style="  width: 64px;"
                                 src="<?php echo $media->imageUrl; ?>">
                        </div>

                    <?php endif; ?>                   
                </div>
                <?php
                echo JHtml::link($prow->url, $prow->product_name);
                ?>
                <br />
                <span style="  color: #736A6A;
                      font-weight: bold;"><?php echo vmText::_('COM_VIRTUEMART_CART_SKU') ?>
                    : <?php echo $prow->product_sku ?></span><br/>

            </td>
            <!-- price -->
            <td>
                <?php
                if (VmConfig::get('checkout_show_origprice', 1) && $prow->prices['discountedPriceWithoutTax'] != $prow->prices['priceWithoutTax'])
                {
                    echo '<span class="line-through">' . $this->currencyDisplay->createPriceDiv('basePriceVariant', '', $prow->prices, TRUE, FALSE) . '</span><br />';
                }

                if ($prow->prices['discountedPriceWithoutTax'])
                {
                    echo $this->currencyDisplay->createPriceDiv('discountedPriceWithoutTax', '', $prow->prices, FALSE, FALSE);
                } else
                {
                    echo $this->currencyDisplay->createPriceDiv('basePriceVariant', '', $prow->prices, FALSE, FALSE);
                }
                ?>
            </td>
            <!-- Quantity -->
            <td class="col-qty"><?php
                if ($prow->step_order_level)
                    $step = $prow->step_order_level;
                else
                    $step = 1;
                if ($step == 0)
                    $step = 1;
                ?>
                <div class="add-padding">
                    <?php
                    if ($prow->step_order_level)
                        $step = $prow->step_order_level;
                    else
                        $step = 1;
                    if ($step == 0)
                        $step = 1;
                    ?>
                    <input type="text"
                           title="<?php echo vmText::_('COM_VIRTUEMART_CART_UPDATE') ?>"
                           class="quantity-input js-recalculate"
                           id="zt-opc-shoppingcart-pid-<?php echo $pKey; ?>"
                           size="3" maxlength="4" name="quantity[<?php echo $pKey; ?>]"
                           value="<?php echo $prow->quantity ?>"/>

                    <button type="button" class="vm2-add_quantity_cart" title="<?php echo vmText::_('COM_VIRTUEMART_CART_UPDATE') ?>" onClick="zt.onepagecheckout.updateCartQuantity(<?php echo $pKey; ?>);"><i class="fa fa-refresh"></i></button>
                    <button type="button" class="vm2-remove_from_cart" title="<?php echo vmText::_('COM_VIRTUEMART_CART_DELETE') ?>" onClick="zt.onepagecheckout.removeCartItem(<?php echo $pKey; ?>);"><i class="fa fa-close"></i></button>
                </div>                
            </td>
            <!-- Tax -->
            <td class="col-total nowrap">                
                <?php echo($prow->prices['subtotal_tax_amount']); ?>                
            </td>
            <!-- Discount -->
            <td class="col-total nowrap">                
                <?php echo($prow->prices['subtotal_discount']); ?>                
            </td>
            <!-- total -->
            
            <td colspan="2">
                <?php echo $this->currencyDisplay->getSymbol() . ($prow->prices['subtotal_with_tax']) ; ?>
            </td>
            </tr>
            </tbody>


            <?php
            $i = ($i == 1) ? 2 : 1;
        }
        ?>
        <!--Begin of SubTotal, Tax, Shipment, Coupon Discount and Total listing -->
        <?php
        if (VmConfig::get('show_tax'))
        {
            $colspan = 3;
        } else
        {
            $colspan = 2;
        }
        ?>
        <tbody class="opc-price">
            <tr class="sectiontableentry1 price-nomal">
                <td class="col-left"><?php echo vmText::_('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_PRICES_TOTAL'); ?></td>               
                <!-- Discount -->
                <td colspan="4">
                    <?php echo $this->cart->cartPrices['discountAmount']; ?>
                </td>
                <!-- Total -->
        
                <td colspan="2">
                    <?php echo $this->currencyDisplay->getSymbol() .  $this->currencyDisplay->formatNumber($this->cart->cartPrices['salesPrice']); ?>
                </td>                
            </tr>
        </tbody>


        <?php
        foreach ($this->cart->cartData['DBTaxRulesBill'] as $rule)
        {
            ?>
            <tbody>
                <tr class="sectiontableentry<?php echo $i ?>">
                    <td colspan="1"><?php echo $rule['calc_name'] ?> </td>

                    <?php
                    if (VmConfig::get('show_tax'))
                    {
                        ?>
                        <td></td>
                    <?php } ?>
                    <td><?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], FALSE); ?></td>
                    <td><?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], FALSE); ?> </td>
                </tr>
            </tbody>
            <?php
            if ($i)
            {
                $i = 1;
            } else
            {
                $i = 0;
            }
        }
        ?>

        <?php
        foreach ($this->cart->cartData['taxRulesBill'] as $rule)
        {
            ?>
            <tbody>
                <tr class="sectiontableentry<?php echo $i ?>">
                    <td colspan="1"><?php echo $rule['calc_name'] ?> </td>
                    <?php
                    if (VmConfig::get('show_tax'))
                    {
                        ?>
                        <td><?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], FALSE); ?> </td>
                    <?php } ?>
                    <td><?php ?> </td>
                    <td><?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], FALSE); ?> </td>
                </tr>
            </tbody>
            <?php
            if ($i)
            {
                $i = 1;
            } else
            {
                $i = 0;
            }
        }

        foreach ($this->cart->cartData['DATaxRulesBill'] as $rule)
        {
            ?>
            <tbody>
                <tr class="sectiontableentry<?php echo $i ?>">
                    <td colspan="1"><?php echo $rule['calc_name'] ?> </td>

                    <?php
                    if (VmConfig::get('show_tax'))
                    {
                        ?>
                        <td></td>

                    <?php } ?>
                    <td><?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], FALSE); ?>  </td>
                    <td><?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], FALSE); ?> </td>
                </tr>
            </tbody>
            <?php
            if ($i)
            {
                $i = 1;
            } else
            {
                $i = 0;
            }
        }
        ?>

        <?php
        if (VmConfig::get('oncheckout_opc', true) or ! VmConfig::get('oncheckout_show_steps', false) or ( !VmConfig::get('oncheckout_opc', true) and VmConfig::get('oncheckout_show_steps', false) and ! empty($this->cart->virtuemart_shipmentmethod_id))
        )
        {
            ?>
            <tbody>
                <tr class="sectiontableentry1">
                    <?php
                    if (!$this->cart->automaticSelectedShipment)
                    {
                        ?>
                        <td colspan="5">                            
                            <?php
                            echo '<span class="pull-left">' . $this->cart->cartData['shipmentName'] . '</span>';
                            ?>

                        <td colspan="2"><?php echo $this->currencyDisplay->createPriceDiv('salesPriceShipment', '', $this->cart->cartPrices['salesPriceShipment'], FALSE); ?> </td>
                        <?php
                    } else
                    {
                        ?>
                        <td colspan="4" class="col-left">
                            
                            <?php echo $this->cart->cartData['shipmentName']; ?>
                            
                        </td>
                        <?php
                        if (VmConfig::get('show_tax'))
                        {
                            ?>
                            <td><?php echo "<span  class='priceColor2'>" . $this->currencyDisplay->createPriceDiv('shipmentTax', '', $this->cart->cartPrices['shipmentTax'], FALSE) . "</span>"; ?> </td>
                        <?php } ?>                        
                        <td colspan="2"><?php echo $this->currencyDisplay->createPriceDiv('salesPriceShipment', '', $this->cart->cartPrices['salesPriceShipment'], FALSE); ?> </td>
                    <?php } ?>



                </tr>
            </tbody>
        <?php } ?>
        <?php
        if ($this->cart->pricesUnformatted['salesPrice'] > 0.0 and ( VmConfig::get('oncheckout_opc', true) or ! VmConfig::get('oncheckout_show_steps', false) or ( (!VmConfig::get('oncheckout_opc', true) and VmConfig::get('oncheckout_show_steps', false)) and ! empty($this->cart->virtuemart_paymentmethod_id))
                )
        )
        {
            ?>
            <tbody>               
                <tr class="sectiontableentry1">
                    <?php
                    if (!$this->cart->automaticSelectedPayment)
                    {
                        ?>
                        <td colspan="5" class="col-left">
                            <?php
                            echo $this->cart->cartData['paymentName'] . '<br/>';
                            ?>


                        <td colspan="2"><?php echo $this->currencyDisplay->createPriceDiv('salesPricePayment', '', $this->cart->cartPrices['salesPricePayment'], FALSE); ?> </td>
                        </td>


                        <?php
                    } else
                    {
                        ?>
                        <td colspan="7" class="col-left">
                            
                            <?php echo $this->cart->cartData['paymentName']; ?>
                            <?php
                            if (VmConfig::get('show_tax'))
                            {
                                ?>
                                <?php echo "<span  class='priceColor2'>" . $this->currencyDisplay->createPriceDiv('paymentTax', '', $this->cart->cartPrices['paymentTax'], FALSE) . "</span>"; ?>
                            <?php } ?>
                            <span class="pull-right"><?php echo $this->currencyDisplay->createPriceDiv('salesPricePayment', '', $this->cart->cartPrices['salesPricePayment'], FALSE); ?></span>
                            
                        </td>
                        <?php if ($this->cart->cartPrices['salesPricePayment'] > 0) echo $this->currencyDisplay->createPriceDiv('salesPricePayment', '', $this->cart->cartPrices['salesPricePayment'], FALSE); ?>
                    <?php } ?>
                </tr>
            </tbody>
        <?php } ?>
        <tbody>
            <tr class="sectiontableentry2">
                <td colspan="5"><?php echo vmText::_('COM_VIRTUEMART_CART_TOTAL') ?>:</td>
                <td colspan="2">
                    <strong style="text-align: right" class="PricesalesPrice"><span class="PricesalesPrice"><?php echo $this->currencyDisplay->createPriceDiv('billTotal', '', $this->cart->cartPrices['billTotal'], FALSE); ?></span></strong>
                </td>
            </tr>
        </tbody>
        <?php
        if ($this->totalInPaymentCurrency)
        {
            ?>
            <tbody>
                <tr class="sectiontableentry2">
                    <td><?php echo vmText::_('COM_VIRTUEMART_CART_TOTAL_PAYMENT') ?>:</td>

                    <?php
                    if (VmConfig::get('show_tax'))
                    {
                        ?>
                        <td></td>
                    <?php } ?>
                    <td></td>
                    <td><strong><?php echo $this->totalInPaymentCurrency; ?></strong></td>
                </tr>
            </tbody>
            <?php
        }
        ?>

    </table>
</fieldset>
