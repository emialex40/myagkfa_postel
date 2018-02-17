<?php
/**
 *
 * Show the product details page
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers, Eugen Stranz, Max Galt
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2014 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default.php 9185 2016-02-25 13:51:01Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/* Let's see if we found the product */
if (empty($this->product)) {
	echo vmText::_('COM_VIRTUEMART_PRODUCT_NOT_FOUND');
	echo '<br /><br />  ' . $this->continue_link_html;
	return;
}

echo shopFunctionsF::renderVmSubLayout('askrecomjs',array('product'=>$this->product));

if(vRequest::getInt('print',false)){ ?>
<body onload="javascript:print();">
<?php } ?>
<script type="text/javascript">
  jQuery('.lft-content').hide();
  jQuery('#zo2-position-25').parent().hide();
  jQuery('#zo2-component').parent().removeClass('col-md-9 col-sm-9').addClass('col-md-12 col-sm-12');
</script>

<div class="row" id="windy-product-detail">
	<div class="col-xs-12 col-sm-5 col-md-5 col-lg-5 windy-product-detail-gallery">
		<?php
			echo $this->loadTemplate('images');
			?>
			<?php
				$count_images = count ($this->product->images);
				if ($count_images > 1) {
					echo $this->loadTemplate('images_additional');
				}

				// event onContentBeforeDisplay
				echo $this->product->event->beforeDisplayContent; 
        $sale = $this->product->prices['product_override_price'];
        $saleClass = '';
        if ($sale > 0) {
            $saleClass = 'product-sale';
        }
      ?>
	</div>
	<div class="col-xs-12 col-sm-7 col-md-7 col-lg-7 windy-product-detail-info">
		<div class="infor">
      <div class="detail-product-direct">
        <div class="windy-prodcut-detai-nav">
          <?php  if (VmConfig::get('product_navigation', 1)) : // Product Navigation ?>
                <div class="product-neighbours">
                <?php if (!empty($this->product->neighbours ['previous'][0])) : ?>                  
              <a href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['previous'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id, FALSE); ?>" class="#"><i class="fa fa-angle-left"></i></a>
                <?php endif; ?>
                <?php if (!empty($this->product->neighbours ['next'][0])) : ?>
                <a class="#" href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['next'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id, FALSE); ?>" title="#"><i class="fa fa-angle-right"></i></a>
                <?php endif; ?>
                </div>
          <?php endif; // Product Navigation END ?>
        </div>
      </div>
			<div class="detail-product-title">
				<?php // Product Title   ?>
					<h1 itemprop="name"><?php echo $this->product->product_name ?></h1>
				<?php // Product Title END   ?>		
			</div>
    	<div class="clear"></div>

			<div class="raiting">					
				<?php echo shopFunctionsF::renderVmSubLayout('raiting-prodcut-detail',array('showRating'=>$this->showRating,'product'=>$this->product)); ?>
			</div>
		</div>
		<div class="price">					
			<div class="list-price">
				<?php echo shopFunctionsF::renderVmSubLayout('prices',array('product'=>$this->product,'currency'=>$this->currency)); ?>
			</div>
			<div class="">
			    <?php $prod_quant = $this->product->product_in_stock; ?>
				<p><b>Наличие:</b> <?php if ($prod_quant) { echo "<span class='stock'>в наличии $prod_quant шт.</span>"; } else { echo "<span class='stock'>товар временно отсутствует</span>"; } ?></p>
			</div>
		</div><br />

		<?php if(!isset($this->product->product_s_desc)) { echo '<div class="description-detail">'; } ?>
			<?php if(isset($this->product->product_s_desc)): ?>
				<p><?php echo nl2br($this->product->product_s_desc); ?></p>
			<?php else: ?>
				<p>Нет описания</p>
			<?php endif; ?>
		<?php if(!isset($this->product->product_s_desc)) { echo '</div>'; } ?>

		<div class="windy-addtocart clearfix">					
			<?php echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$this->product)); ?>
		</div>

		<div class="detail-bottons">
			<ul>
				<li><?php plgSystemZtvirtuemarter::addWishlistButton($product); ?></li>
				<li><?php plgSystemZtvirtuemarter::addCompareButton($product); ?></li>
			</ul>
		</div>
	</div>
</div>

</div></div></div></div>
<div class="product-desc"><div class="container"><div class="row">
<div class="container-desc">
	<div id="zt_tabs" class="tabs">
        <ul class="nav nav-tabs" role="tablist" id="myTab">
            <li class="active"><a href="#tab1" role="tab" data-toggle="tab"><?php echo 'Описание' ;?></a></li>
            <li class=""><a href="#tab3" role="tab" data-toggle="tab"><?php echo 'Оплата и доставка' ;?></a></li>
            <li class=""><a href="#tab2" role="tab" data-toggle="tab"><?php echo 'Отзывы'; ?></a></li>
        </ul>        
	    <div class="tab-content">
	        <div class="tab-pane active " id="tab1">  
	           	<?php
					// Product Description
					if (!empty($this->product->product_desc)) {
					    ?>
				        <div class="product-description">
					<?php /** @todo Test if content plugins modify the product description */ ?>
				    	<span class="title"><?php //echo vmText::_('COM_VIRTUEMART_PRODUCT_DESC_TITLE') ?></span>
					<?php echo $this->product->product_desc; ?>
				        </div>
					<?php
			    } ?>
	        </div>
	         <div class="tab-pane " id="tab2">
	         	<?php echo $this->loadTemplate('reviews');?>
	        </div>
	        <div class="tab-pane " id="tab3">
	            <h3>Оплата</h3>
                <p>Оплата заказа производится через интернет непосредственно после оформления заказа. Мы принимаем следующие виды платежей:</p>
                <ul>
                <li>Яндекс.Деньги</li>
                <li>Webmoney</li>
                <li>Кошелек QIWI</li>
                <li>Альфа-Клик</li>
                <li>Сбербанк Онлайн</li>
                <li>Наличными через терминал</li>
                <li>Банковская карта</li>
                <li>Счёт для безналичной оплаты</li>
                <p>При выборе счёта для безналичной оплаты: после оформления заказа с вами свяжется оператор и выставит вам счет.</p>
                <h3>Доставка</h3>
                <p>Длительность доставки - от 1-2 рабочих дней по Москве и до 10 рабочих дней в другие города России в зависимости от удаленности вашего населенного пункта.</p><br>
                <p>При сумме заказа свыше 5 000 руб. - доставка БЕСПЛАТНАЯ.</p><br>
                <p>При сумме заказа менее 5 000 р.:</p>
                <p>Стоимость доставки курьером по Москве и во все регионы России — 400 рублей. При оформлении заказа выберите это вариант доставки, и по указанному вами адресу будет осуществлена доставка. Курьерские службы обязательно будут информировать вас о ходе доставки.</p><br>
                <p>Возможен самовывоз в городе клиента: на ваш выбор либо пункты самовывоза курьерских компаний, либо постаматы. Стоимость — 250 рублей. Если при оформлении заказа вы выбираете этот вариант, то с вами свяжется наш оператор для уточнения удобного для вас адреса забора заказа.</p>
	        </div>
	    </div>         
	</div><!--/zt_tabs-->
</div>
</div></div></div>

<div class="row" style="margin: 15px;" id="windy-product-detail-bottom">
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4"></div>
    <!--<div class="windy-addtocart col-xs-12 col-sm-12 col-md-4 col-lg-4 windy-product-detail-info">					
    			<?php //echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$this->product)); ?>
    </div>-->
    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4"></div>
</div>

<div class="cat-detail">
	<div class="container">
		<div class="row">
			<p>
				<b>Категория :</b> <?php
				if (VmConfig::get('showCategory', 1)) {
					echo $this->loadTemplate('showcategory');
				}
				?>
			</p>
		</div>
	</div>
</div>

<div class="related_products">
	<div class="container">
		<div class="row">
			<h2 class="title-related_products">Сопутствующие товары</h2>
			<?php echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'related_products','class'=> 'product-related-products-windy','customTitle' => true )); ?>
		</div>
	</div>
</div>

<div class="productdetails-view productdetails" style="display: none;" >

    <?php
    // Product Navigation
    if (VmConfig::get('product_navigation', 1)) {
	?>
      <div class="product-neighbours">
	    <?php
	    if (!empty($this->product->neighbours ['previous'][0])) {
		$prev_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['previous'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id, FALSE);
		echo JHtml::_('link', $prev_link, $this->product->neighbours ['previous'][0]
			['product_name'], array('rel'=>'prev', 'class' => 'previous-page','data-dynamic-update' => '1'));
	    }
	    if (!empty($this->product->neighbours ['next'][0])) {
		$next_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['next'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id, FALSE);
		echo JHtml::_('link', $next_link, $this->product->neighbours ['next'][0] ['product_name'], array('rel'=>'next','class' => 'next-page','data-dynamic-update' => '1'));
	    }
	    ?>
    	<div class="clear"></div>
        </div>
    <?php } // Product Navigation END
    ?>

	<?php // Back To Category Button
	if ($this->product->virtuemart_category_id) {
		$catURL =  JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$this->product->virtuemart_category_id, FALSE);
		$categoryName = vmText::_($this->product->category_name) ;
	} else {
		$catURL =  JRoute::_('index.php?option=com_virtuemart');
		$categoryName = vmText::_('COM_VIRTUEMART_SHOP_HOME') ;
	}
	?>
	<div class="back-to-category">
    	<a href="<?php echo $catURL ?>" class="product-details" title="<?php echo $categoryName ?>"><?php echo vmText::sprintf('COM_VIRTUEMART_CATEGORY_BACK_TO',$categoryName) ?></a>
	</div>

    <?php // Product Title   ?>
    <h1 itemprop="name"><?php echo $this->product->product_name ?></h1>
    <?php // Product Title END   ?>

    <?php // afterDisplayTitle Event
    echo $this->product->event->afterDisplayTitle ?>

    <?php
    // Product Edit Link
    echo $this->edit_link;
    // Product Edit Link END
    ?>

    <?php
    // PDF - Print - Email Icon
    if (VmConfig::get('show_emailfriend') || VmConfig::get('show_printicon') || VmConfig::get('pdf_icon')) {
	?>
        <div class="icons">
	    <?php

	    $link = 'index.php?tmpl=component&option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->virtuemart_product_id;

		echo $this->linkIcon($link . '&format=pdf', 'COM_VIRTUEMART_PDF', 'pdf_button', 'pdf_icon', false);
	    //echo $this->linkIcon($link . '&print=1', 'COM_VIRTUEMART_PRINT', 'printButton', 'show_printicon');
		echo $this->linkIcon($link . '&print=1', 'COM_VIRTUEMART_PRINT', 'printButton', 'show_printicon',false,true,false,'class="printModal"');
		$MailLink = 'index.php?option=com_virtuemart&view=productdetails&task=recommend&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id . '&tmpl=component';
	    echo $this->linkIcon($MailLink, 'COM_VIRTUEMART_EMAIL', 'emailButton', 'show_emailfriend', false,true,false,'class="recommened-to-friend"');
	    ?>
    	<div class="clear"></div>
        </div>
    <?php } // PDF - Print - Email Icon END
    ?>

    <?php
    // Product Short Description
    if (!empty($this->product->product_s_desc)) {
	?>
        <div class="product-short-description">
	    <?php
	    /** @todo Test if content plugins modify the product description */
	    echo nl2br($this->product->product_s_desc);
	    ?>
        </div>
	<?php
    } // Product Short Description END

	echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'ontop'));
    ?>

    <div class="vm-product-container">
	<div class="vm-product-media-container">
<?php
echo $this->loadTemplate('images');
?>
	</div>

	<div class="vm-product-details-container">
	    <div class="spacer-buy-area">

		<?php
		// TODO in Multi-Vendor not needed at the moment and just would lead to confusion
		/* $link = JRoute::_('index2.php?option=com_virtuemart&view=virtuemart&task=vendorinfo&virtuemart_vendor_id='.$this->product->virtuemart_vendor_id);
		  $text = vmText::_('COM_VIRTUEMART_VENDOR_FORM_INFO_LBL');
		  echo '<span class="bold">'. vmText::_('COM_VIRTUEMART_PRODUCT_DETAILS_VENDOR_LBL'). '</span>'; ?><a class="modal" href="<?php echo $link ?>"><?php echo $text ?></a><br />
		 */
		?>

		<?php
		echo shopFunctionsF::renderVmSubLayout('rating',array('showRating'=>$this->showRating,'product'=>$this->product));

		if (is_array($this->productDisplayShipments)) {
		    foreach ($this->productDisplayShipments as $productDisplayShipment) {
			echo $productDisplayShipment . '<br />';
		    }
		}
		if (is_array($this->productDisplayPayments)) {
		    foreach ($this->productDisplayPayments as $productDisplayPayment) {
			echo $productDisplayPayment . '<br />';
		    }
		}

		//In case you are not happy using everywhere the same price display fromat, just create your own layout
		//in override /html/fields and use as first parameter the name of your file
		echo shopFunctionsF::renderVmSubLayout('prices',array('product'=>$this->product,'currency'=>$this->currency));
		?> <div class="clear"></div><?php
    
		echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$this->product));

		echo shopFunctionsF::renderVmSubLayout('stockhandle',array('product'=>$this->product));

		// Ask a question about this product
		if (VmConfig::get('ask_question', 0) == 1) {
			$askquestion_url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&task=askquestion&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id . '&tmpl=component', FALSE);
			?>
			<div class="ask-a-question">
				<a class="ask-a-question" href="<?php echo $askquestion_url ?>" rel="nofollow" ><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_ENQUIRY_LBL') ?></a>
			</div>
		<?php
		}
		?>

		<?php
		// Manufacturer of the Product
		if (VmConfig::get('show_manufacturers', 1) && !empty($this->product->virtuemart_manufacturer_id)) {
		    echo $this->loadTemplate('manufacturer');
		}
		?>

	    </div>
	</div>
	<div class="clear"></div>


    </div>
<?php
	$count_images = count ($this->product->images);
	if ($count_images > 1) {
		echo $this->loadTemplate('images_additional');
	}

	// event onContentBeforeDisplay
	echo $this->product->event->beforeDisplayContent; ?>

	<?php
	//echo ($this->product->product_in_stock - $this->product->product_ordered);
	// Product Description
	if (!empty($this->product->product_desc)) {
	    ?>
        <div class="product-description" >
	<?php /** @todo Test if content plugins modify the product description */ ?>
    	<span class="title"><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_DESC_TITLE') ?></span>
	<?php echo $this->product->product_desc; ?>
        </div>
	<?php
    } // Product Description END

	echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'normal'));

    // Product Packaging
    $product_packaging = '';
    if ($this->product->product_box) {
	?>
        <div class="product-box">
	    <?php
	        echo vmText::_('COM_VIRTUEMART_PRODUCT_UNITS_IN_BOX') .$this->product->product_box;
	    ?>
        </div>
    <?php } // Product Packaging END ?>

    <?php
	echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'onbot'));

  // echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'related_products','class'=> 'product-related-products','customTitle' => true ));

	shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'related_categories','class'=> 'product-related-categories'));

	?>

<?php // onContentAfterDisplay event
echo $this->product->event->afterDisplayContent;

echo $this->loadTemplate('reviews');

// Show child categories
if (VmConfig::get('showCategory', 1)) {
	echo $this->loadTemplate('showcategory');
}

$j = 'jQuery(document).ready(function($) {
	Virtuemart.product(jQuery("form.product"));

	$("form.js-recalculate").each(function(){
		if ($(this).find(".product-fields").length && !$(this).find(".no-vm-bind").length) {
			var id= $(this).find(\'input[name="virtuemart_product_id[]"]\').val();
			Virtuemart.setproducttype($(this),id);

		}
	});
});';
//vmJsApi::addJScript('recalcReady',$j);

/** GALT
 * Notice for Template Developers!
 * Templates must set a Virtuemart.container variable as it takes part in
 * dynamic content update.
 * This variable points to a topmost element that holds other content.
 */
$j = "Virtuemart.container = jQuery('.productdetails-view');
Virtuemart.containerSelector = '.productdetails-view';";

vmJsApi::addJScript('ajaxContent',$j);

if(VmConfig::get ('jdynupdate', TRUE)){
	$j = "jQuery(document).ready(function($) {
	Virtuemart.stopVmLoading();
	var msg = '';
	jQuery('a[data-dynamic-update=\"1\"]').off('click', Virtuemart.startVmLoading).on('click', {msg:msg}, Virtuemart.startVmLoading);
	jQuery('[data-dynamic-update=\"1\"]').off('change', Virtuemart.startVmLoading).on('change', {msg:msg}, Virtuemart.startVmLoading);
});";

	vmJsApi::addJScript('vmPreloader',$j);
}

echo vmJsApi::writeJS();

if ($this->product->prices['salesPrice'] > 0) {
  echo shopFunctionsF::renderVmSubLayout('snippets',array('product'=>$this->product, 'currency'=>$this->currency, 'showRating'=>$this->showRating));
}

?>
</div>



