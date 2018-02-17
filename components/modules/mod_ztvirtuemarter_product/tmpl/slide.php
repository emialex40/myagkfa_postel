<?php 
/**
 * @package    ZT VirtueMarter
 * @subpackage ZT VirtueMarter Product Module
 * @author       ZooTemplate.com
 * @link http://www.zootemplate.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */
// no direct access
defined ('_JEXEC') or die('Restricted access');
// add javascript for price and cart, need even for quantity buttons, so we need it almost anywhere
vmJsApi::jPrice();
$doc = JFactory::getDocument();
$doc->addScript(JUri::root() . 'modules/mod_ztvirtuemarter_product/assets/js/owl.carousel.min.js');
$doc->addStyleSheet(JUri::root() . 'modules/mod_ztvirtuemarter_product/assets/css/owl_carousel/owl.carousel.css');
$doc->addStyleSheet(JUri::root() . 'modules/mod_ztvirtuemarter_product/assets/css/owl_carousel/owl.theme.css');

$col = 1;
$pwidth = ' width' . floor (100 / $productsPerRow);
if ($productsPerRow > 1) {
    $float = "floatleft";
} else {
    $float = "center";
}
//$display_style = $params->get( 'display_style', "div" );
$number = $params->get ('products_per_row');
?>

<div class="vmgroup <?php echo $params->get ('moduleclass_sfx') ?> product-grid-item" id="slide-product">

    <?php if ($headerText) { ?>
        <div class="vmheader"><?php echo $headerText ?></div>
    <?php
    }
    if ($display_style == "div") {
        ?>
        <ul class="vmproduct<?php echo $params->get ('moduleclass_sfx'); ?> productdetails owl-carousel owl-theme">
            <?php foreach ($products as $product) : ?>
                <li class="item">
                    <?php
                    if (!empty($product->images[0])) {
                        $image = $product->images[0]->displayMediaThumb ('class="featuredProductImage" border="0"', FALSE);
                    } else {
                        $image = '';
                    }
                    echo JHTML::_ ('link', JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id), $image, array('title' => $product->product_name));
                    echo '<div class="clear"></div>';
                    $url = JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' .
                        $product->virtuemart_category_id); ?>
                    <a href="<?php echo $url ?>"><?php echo $product->product_name ?></a>        <?php    echo '<div class="clear"></div>';
                    // $product->prices is not set when show_prices in config is unchecked
                    echo '<div class="product-price' . $saleClass . '">' . shopFunctionsF::renderVmSubLayout('prices', array('product' => $product, 'currency' => $currency)) . '</div>';

                    if ($show_addtocart) {
                        echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$product));
                    }
                    ?>
                </li>
                <?php
                if ($col == $productsPerRow && $productsPerRow && $last) {
                    echo '
        </ul><div class="clear"></div>';
                    $col = 1;
                } else {
                    $col++;
                }
                $last--;
            endforeach; ?>
        </ul>
        <br style='clear:both;'/>

    <?php
    } else {
        $last = count ($products) - 1;
        ?>




        <div class="vmproduct <?php echo $params->get ('moduleclass_sfx'); ?> productdetails owl-carousel owl-theme">

            <?php foreach ($products as $product) { ?>
                <div class="products">
                    <div class="item">
                        <div class="spacer">
                            <div class="vm-product-media-container zt-product-content ">
                                <?php
                                if (!empty($product->images[0])) {
                                    $image = $product->images[0]->displayMediaThumb ('class="featuredProductImage" border="0"', FALSE);
                                } else {
                                    $image = '';
                                }
                                echo JHTML::_ ('link', JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id), $image, array('title' => $product->product_name));
                                echo '<div class="clear"></div>';
                                $url = JRoute::_ ('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' .
                                    $product->virtuemart_category_id); ?>

                                <?php echo plgSystemZtvirtuemarter::addQuickviewButton($product);?>
                                <?php

                                $createddate = $product->created_on;
                                $sale = $product->prices['product_override_price'];
                                $timeCreateddate = strtotime($createddate);
                                $date = strtotime("now");

                                $htmlLabel = '';
                                $saleClass = '';
                                if ($sale > 0) {
                                    $saleClass = ' product-sale';
                                }

                                if ($date - $createddate <= 3) {
                                    $htmlLabel .= '<div class="label-product label-new">New</div>';
                                }
                                if ($sale > 0) {
                                    $htmlLabel .= '<div class="label-product label-sale">Sale</div>';
                                }

                                echo $htmlLabel;

                                ?>
                            </div>
                            <div class="content">
                                <h3 class="product-name">
                                    <a href="<?php echo $url ?>"><?php echo $product->product_name ?></a>
                                </h3>
                                <?php
                                $ratingModel = VmModel::getModel('ratings');
                                $product->showRating = $ratingModel->showRating($product->virtuemart_product_id);
                                if ($product->showRating) {
                                    $rating = $ratingModel->getRatingByProduct($product->virtuemart_product_id);
                                    if (!empty($rating)) {
                                        $r = $rating->rating;
                                    } else {
                                        $r = 0;
                                    }
                                    $maxrating = VmConfig::get('vm_maximum_rating_scale', 5);
                                    $ratingwidth = ($r * 100) / $maxrating;
                                    $rateStar = '';
                                    $rateStar .= '<div class="comare_rating">';
                                    $rateStar .= '<div class="rating">';
                                    $rateStar .= '<span class="vote">';
                                    $rateStar .= '<span title="" class="vmicon ratingbox" style="display:inline-block;">';
                                    $rateStar .= '<span class="stars-orange" style="width:' . $ratingwidth . '%">';
                                    $rateStar .= '</span>';
                                    $rateStar .= '</span>';
                                    $rateStar .= '</span>';
                                    $rateStar .= '</div>';
                                    $rateStar .= '</div>';
                                    echo $rateStar;
                                }
                                ?>
                                <div class="coundown">
                                    <?php plgSystemZtvirtuemarter::getCountdown($product);?>
                                </div>

                                <?php    echo '<div class="clear"></div>';

                                echo '<div class="product-price' . $saleClass . '">' . shopFunctionsF::renderVmSubLayout('prices', array('product' => $product, 'currency' => $currency)) . '</div>';


                                ?>
                            </div>
                            <div class="product_hover add-to-link">

                                <?php
                                if ($showAddtocart) {
                                    $oder = 0;
                                    for ($i = 0; $i < strlen($url); $i++) {
                                        if ($url[$i] == '/') {
                                            $oder = $i;
                                        }
                                    }
                                    $abc = substr($url, $oder);
                                    $urlLink = str_replace($abc, "", $url);
                                    echo ModZtvirtuemarterProductHelper::addtocart($product);
                                }
                                ?>

                                <?php plgSystemZtvirtuemarter::addWishlistButton($product); ?>
                                <?php plgSystemZtvirtuemarter::addCompareButton($product); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                if ($col == $productsPerRow && $productsPerRow && $col < $totalProd) {
                    //echo "    </div>";
                    $col = 1;
                } else {
                    $col++;
                }
            } ?>
        </div>

    <?php
    }
    if ($footerText) : ?>
        <div class="vmfooter<?php echo $params->get ('moduleclass_sfx') ?>">
            <?php echo $footerText ?>
        </div>
    <?php endif; ?>
</div>

<script>
    jQuery(window).load(function() {
        var owl = jQuery("#slide-product .productdetails");
        owl.owlCarousel({
            autoPlay: true,
            items : <?php echo $number;?>,
            nav : true,
            dots : false,
            smartSpeed : 500,
            responsive:{
                0:{
                    items:1,
                    nav:true
                },
                600:{
                    items:3,
                    nav:false
                },
                1000:{
                    items:5,
                    nav:true,
                    loop:false
                }
            }
        });
    });
</script>