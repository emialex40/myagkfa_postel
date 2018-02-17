<?php // no direct access
/**
 * @package    ZT VirtueMarter
 * @subpackage ZT VirtueMarter Product Module
 * @author       ZooTemplate.com
 * @link http://zootemplate.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */
defined ('_JEXEC') or die('Restricted access');
// add javascript for price and cart, need even for quantity buttons, so we need it almost anywhere
$doc = JFactory::getDocument();
$doc->addScript(JURI::root() . 'modules/mod_ztvirtuemarter_product/assets/js/owl.carousel.min.js');
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

<div class="vmgroup <?php echo $params->get ('moduleclass_sfx') ?>" id="slide-product-<?php echo $module->id; ?>">

	<?php if ($headerText) { ?>
	<div class="vmheader"><?php echo $headerText ?></div>
	<?php
}
	if ($display_style == "div") {
		?>
		<ul class="vmproduct <?php echo $params->get ('moduleclass_sfx'); ?> productdetails" id="slide-list-vmproduct-<?php echo $module->id; ?>">
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
				echo '<div class="' . $saleClass . '">' . shopFunctionsF::renderVmSubLayout('prices', array('product' => $product, 'currency' => $currency));

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

		


		<div class="vmproduct productdetails" id="slide-vmproduct-<?php echo $module->id; ?>">

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
						</div>
						<h3 class="product-name">
							<a href="<?php echo $url ?>"><?php echo $product->product_name ?></a>        
						</h3>
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
						<?php    echo '<div class="clear"></div>';

						 echo '<div class="' . $saleClass . '">' . shopFunctionsF::renderVmSubLayout('prices', array('product' => $product, 'currency' => $currency)) . '</div>';
					    
					 
					?>
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
	                </div>
				</div>
			</div>
		</div>
			<?php
			if ($col == $productsPerRow && $productsPerRow && $col < $totalProd) {
				//echo "	</div>";
				$col = 1;
			} else {
				$col++;
			}
		} ?>
		</div>
		<div class="clear"></div>

		<?php
	}
	if ($footerText) : ?>
		<div class="vmfooter<?php echo $params->get ('moduleclass_sfx') ?>">
			<?php echo $footerText ?>
		</div>
		<?php endif; ?>
</div>

<script>
    jQuery(document).ready(function(){
        var owl = jQuery('#slide-vmproduct-<?php echo $module->id; ?>, #slide-list-vmproduct-<?php echo $module->id; ?>');
        owl.owlCarousel({
            autoPlay: 50000,
            items : <?php echo $number;?>,
            navigation : true,
            pagination : false,
            slideSpeed : 500
        });
    });
</script>