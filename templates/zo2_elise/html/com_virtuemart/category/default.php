<?php
/**
 *
 * Show the products in a category
 *
 * @package    VirtueMart
 * @subpackage
 * @author RolandD
 * @author Max Milbers
 * @todo add pagination
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default.php 9017 2015-10-14 10:44:34Z Milbo $
 */

defined ('_JEXEC') or die('Restricted access');

?> <div class="category-view"> <?php
$js = "
jQuery(document).ready(function () {
	jQuery('.orderlistcontainer').hover(
		function() { jQuery(this).find('.orderlist').stop().show()},
		function() { jQuery(this).find('.orderlist').stop().hide()}
	)
});
";
vmJsApi::addJScript('vm.hover',$js);

if (empty($this->keyword) and !empty($this->category)) {
	?>
	
<h1><?php echo vmText::_($this->category->category_name); ?></h1>
	
<?php
}

// Show child categories
if (VmConfig::get ('showCategory', 1) and empty($this->keyword)) {
	if (!empty($this->category->haschildren)) {
		echo ShopFunctionsF::renderVmSubLayout('categories',array('categories'=>$this->category->children));
	}
}

if($this->showproducts){
?>

<?php
$desc = explode('<hr id="system-readmore" />', $this->category->category_description);
if($desc[1]){ ?>
    <div class="category_description description_top"><?php echo $desc[0]; ?></div>                    
<?php } else { ?>
<div class="category_description">    
    <?php echo $this->category->category_description; ?>    
</div>
<?php } ?>

<div class="browse-view" id="windy-show-shop">
<?php

//if (!empty($this->keyword)) {
	//id taken in the view.html.php could be modified
?>
<div class="filter-shop">

	<?php $category_id  = vRequest::getInt ('virtuemart_category_id', 0); ?>
	<h3><?php echo $this->keyword; ?></h3>

<?php  //} ?>

<?php // Show child categories
	?>

	<div class="row orderby-displaynumber-windy">
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 orderby">
			<?php echo $this->orderByList['orderby']; ?>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 datalayout">
			<div class="page-view" id="windy-chose-view-style">
				<span class="page-view-item list-layout" data-layout="list-layout">
					<i class="cs clever-icon-list"></i>
				</span>
				<span class="page-view-item grid-layout active-mode-preview" data-layout="grid-layout">
					<i class="cs clever-icon-grid"></i>
				</span>
				<span class="getResultsCounter"><?php echo $this->vmPagination->getResultsCounter ();?></span>
			</div>
		</div>
	</div>
</div>

	<?php
	if (!empty($this->products)) {
	$products = array();
	$products[0] = $this->products;
	echo shopFunctionsF::renderVmSubLayout($this->productsLayout,array('products'=>$products,'currency'=>$this->currency,'products_per_row'=>$this->perRow,'showRating'=>$this->showRating));

	?>
<!--Pagination-->
<div class="vm-pagination vm-pagination-bottom"><?php echo $this->vmPagination->getPagesLinks (); ?>
	<span class="vm-page-counter"><?php echo $this->vmPagination->getPagesCounter (); ?></span>
</div>
<!--end Pagination-->

<?php
} elseif (!empty($this->keyword)) {
	echo vmText::_ ('COM_VIRTUEMART_NO_RESULT') . ($this->keyword ? ' : (' . $this->keyword . ')' : '');
}
?>
</div>

<?php } ?>
<?php if($desc[1]){ ?>
        <div class="category_description description_bottom"><?php echo $desc[1]; ?></div>
<?php } ?>

</div>


<?php
$j = "Virtuemart.container = jQuery('.category-view');
Virtuemart.containerSelector = '.category-view';";

vmJsApi::addJScript('ajaxContent',$j);
?>
<!-- end browse-view -->