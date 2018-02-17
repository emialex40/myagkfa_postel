<?php
/**
 * ------------------------------------------------------------------------
 * JJA Filter Plugin - Virtuemart
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2016 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */

defined('_JEXEC') or die;
?>
{#data}
<div class="item product product-item col">                
	<div data-container="product-grid" class="product-item-info {?thumbnail}{:else} no-image {/thumbnail}">

		<div class="product-item-details">
			
			<h4 class="product-item-name">
				<a href="{url}" class="product-item-link">
					{name|s}
				</a>
			</h4>

			<div class="product-reviews-summary short">
				<div class="rating-summary">
					{?rating}
					<div title="{rating} out of 5" class="rating-result">
						<span style="width:{width_rating}%"></span>
					</div>
					{:else}
					<div title="0%" class="rating-result">
						<span style="width:0%"></span>
					</div>
					{/rating}
				</div>

				<div data-product-id="{id}" data-role="priceBox" class="price-box price-final_price">
					<span class="price-container price-final_price tax weee">
						<span class="price-wrapper " data-price-type="finalPrice" data-price-amount="{price}" id="product-price-{id}">
							<span class="price">{frontend_price|s}</span>    
						</span>
					</span>
				</div>
			</div>
			{?thumbnail}
			<a tabindex="-1" class="product-item-photo" href="{url}">
				<span class="product-image-container">
					<img alt="{name|s}" src="<?php echo JUri::base(true).'/'; ?>{thumbnail}" class="product-image-photo">
				</span>
			</a>
			{/thumbnail}

		</div>

		<div class="product-item-actions {?is_salable}{:else}unavailable{/is_salable}">
			{?is_salable}
			<div class="addtocart-area">
				<form method="post" class="product js-recalculate" action="<?php echo JRoute::_ ('index.php?option=com_virtuemart',false); ?>">
					<div class="addtocart-bar">
						<span class="addtocart-button">
							<input name="addtocart" class="btn btn-default" value="<?php echo JText::_('COM_JAMEGAFILTER_ADD_TO_CART'); ?>" title="<?php echo JText::_('COM_JAMEGAFILTER_ADD_TO_CART'); ?>" type="submit">             
						</span>  

						<a href="{url}" class="btn-detail" title="<?php echo JText::_('COM_JAMEGAFILTER_VIEW_DETAIL'); ?>"><?php echo JText::_('COM_JAMEGAFILTER_VIEW_DETAIL'); ?></a>

						<input name="virtuemart_product_id[]" value="{id}" type="hidden">
						<noscript><input type="hidden" name="task" value="add"/></noscript>
					</div> 
					<input type="hidden" name="option" value="com_virtuemart"/>
					<input type="hidden" name="view" value="cart"/>
					<input type="hidden" name="virtuemart_product_id[]" value="{id}"/>
					<input type="hidden" name="pname" value="{name}"/>
					<input type="hidden" name="pid" value="{id}"/>
				</form>
			</div>
			{:else}
			<div class="stock unavailable"><span><?php echo JText::_('COM_JAMEGAFILTER_OUT_STOCK'); ?></span></div>
			<a class="btn btn-default" href="{url}"><?php echo JText::_('COM_JAMEGAFILTER_VIEW_DETAIL'); ?></a>
			{/is_salable}
		</div>
	</div>
</div>
{/data}