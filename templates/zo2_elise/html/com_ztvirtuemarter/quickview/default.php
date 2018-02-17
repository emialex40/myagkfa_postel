<?php
/**
 * @package    ZT VirtueMarter
 * @subpackage Components
 * @author       ZooTemplate.com
 * @link http://zootemplate.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

?>
    <div class="productdetails-view productdetails quickview-product">

        <?php // Back To Category Button
        if ($this->product->virtuemart_category_id) :
            $catURL = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $this->product->virtuemart_category_id, FALSE);
            $categoryName = $this->product->category_name;
        else :
            $catURL = JRoute::_('index.php?option=com_virtuemart');
            $categoryName = vmText::_('COM_VIRTUEMART_SHOP_HOME');
        endif;
        ?>

        <?php // afterDisplayTitle Event
        echo $this->product->event->afterDisplayTitle ?>

        <?php
        // Product Edit Link
        echo $this->edit_link;
        // Product Edit Link END
        ?>
        <div class="vm-product-container">
            <div class="vm-product-media-container col-sm-6">
                <div id="gallery_image-zoom-product"  class="owl-carousel owl-theme">
                <?php
                echo $this->loadTemplate('images');
                $count_images = count($this->product->images);
                if ($count_images > 1) :
                    echo $this->loadTemplate('images_additional');
                endif; ?>
                </div>

                <?php
                // event onContentBeforeDisplay
                echo $this->product->event->beforeDisplayContent;

                $sale = isset($this->product->prices['product_override_price']) ? $this->product->prices['product_override_price'] : '';
                $saleClass = ($sale > 0) ? 'product-sale' : '';
                ?>
            </div>

            <div class="vm-product-details-container col-sm-6">
                <div class="spacer-buy-area">

                    <?php // Product Title   ?>
                    <h1><?php echo $this->product->product_name; ?></h1>
                    <?php $prod_quant = $this->product->product_in_stock; ?>
                    <div class="FlexibleStockNumber">Наличие: <?php if ($prod_quant) { echo "<span class='stock'>в наличии $prod_quant шт.</span>"; } else { echo "<span class='stock'>товар временно отсутствует</span>"; } ?></div><br />

                    <?php // Product Title END   ?>
                    <?php
                    echo shopFunctionsF::renderVmSubLayout('rating', array('showRating' => $this->showRating, 'product' => $this->product));

                    if (is_array($this->productDisplayShipments)) :
                        foreach ($this->productDisplayShipments as $productDisplayShipment) :
                            echo $productDisplayShipment . '<br />';
                        endforeach;
                    endif;
                    if (is_array($this->productDisplayPayments)) :
                        foreach ($this->productDisplayPayments as $productDisplayPayment) :
                            echo $productDisplayPayment . '<br />';
                        endforeach;
                    endif;

                    //In case you are not happy using everywhere the same price display fromat, just create your own layout
                    //in override /html/fields and use as first parameter the name of your file
                    echo '<div class="product-price ' . $saleClass . '">' . shopFunctionsF::renderVmSubLayout('prices', array('product' => $this->product, 'currency' => $this->currency)) . '</div>';
                    //echo shopFunctionsF::renderVmSubLayout('prices',array('product'=>$this->product,'currency'=>$this->currency));

                    // Product Short Description
                    if (!empty($this->product->product_s_desc)) : ?>
                        <div class="product-short-description">
                            <?php
                            /** @todo Test if content plugins modify the product description */
                            echo nl2br($this->product->product_s_desc);
                            ?>
                        </div>
                        <?php
                    endif; // Product Short Description END

                    echo shopFunctionsF::renderVmSubLayout('addtocart', array('product' => $this->product));

                    echo shopFunctionsF::renderVmSubLayout('stockhandle', array('product' => $this->product));

                    // Ask a question about this product
                    if (VmConfig::get('ask_question', 0) == 1) :
                        $askquestion_url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&task=askquestion&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id . '&tmpl=component', FALSE);
                        ?>
                        <div class="ask-a-question">
                            <a class="ask-a-question" href="<?php echo $askquestion_url ?>"
                               rel="nofollow"><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_ENQUIRY_LBL') ?></a>
                        </div>
                    <?php endif; ?>

                    <?php
                    // Manufacturer of the Product
                    if (VmConfig::get('show_manufacturers', 1) && !empty($this->product->virtuemart_manufacturer_id)) :
                        echo $this->loadTemplate('manufacturer');
                    endif;
                    ?>
                </div>
                <?php

                echo shopFunctionsF::renderVmSubLayout('customfields', array('product' => $this->product, 'position' => 'ontop'));
                ?>
            </div>
            <div class="clear"></div>
        </div>

    </div>
    <script>
        Virtuemart.container = jQuery('.productdetails-view');
        Virtuemart.containerSelector = '.productdetails-view';
    </script>
<?php if (plgSystemZtvirtuemarter::getZtvirtuemarterSetting()->enable_countdown == '1') : ?>
    <script>
        (function ($) {
            jQuery("#gallery_image-zoom-product").owlCarousel({
                autoPlay: true,
                singleItem:true,
                navigation: true
            });
        })(jQuery)
    </script>
    <?php
endif;
die;
?>