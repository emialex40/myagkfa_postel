<?php 
/**
 * @package    ZT VirtueMarter
 * @subpackage ZT VirtueMarter Product Module
 * @author       ZooTemplate.com
 * @link http://www.zootemplate.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');
vmJsApi::jPrice();

?>
<div class="vmgroup<?php echo $params->get('moduleclass_sfx') ?>">

    <?php if ($headerText) : ?>
        <div class="vmheader"><?php echo $headerText ?></div>
    <?php endif; ?>

    <div class="vmproduct<?php echo $params->get('moduleclass_sfx'); ?>">
        <?php foreach ($products as $product) : ?>
            <div style="text-align:center;">
                <div class="spacer">
                    <?php
                    if (!empty($product->images[0]))
                        $image = $product->images[0]->displayMediaThumb('class="featuredProductImage" ', false);
                    else $image = '';
                    echo JHTML::_('link', JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id), $image, array('title' => $product->product_name));
                    echo '<div class="clear"></div>';
                    $url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id); ?>
                    <a href="<?php echo $url ?>"><?php echo $product->product_name ?></a>        <?php    echo '<div class="clear"></div>';
                    // $product->prices is not set when show_prices in config is unchecked
                    if ($showPrice && isset($product->prices)) {
                        if (!empty($product->prices['salesPrice'])) echo $currency->createPriceDiv('salesPrice', '', $product->prices, true);
                        if (!empty($product->prices['salesPriceWithDiscount'])) echo $currency->createPriceDiv('salesPriceWithDiscount', '', $product->prices, true);
                    }
                    if ($showAddtocart)
                        echo ModZtvirtuemarterProductHelper::addtocart($product);
                    ?>
                </div>
            </div>

        <?php endforeach; ?>
        <?php if ($footerText) : ?>
            <div class="vmheader"><?php echo $footerText ?></div>
        <?php endif; ?>
    </div>
</div>