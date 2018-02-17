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
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$input = JFactory::getApplication()->input;
$direction = !empty($this->config->Moduledirection) ? $this->config->Moduledirection : $this->config->direction;
if ($direction == 'vertical')
	$direction='';

if((!empty($this->config->isComponent) && empty($this->config->isModule)) ||
				(empty($this->config->isComponent) && !empty($this->config->isModule)) ) {
	// load virtuemart css, js
	vmJsApi::jPrice();
	vmJsApi::writeJS();

	$model = VmModel::getModel('User');
	$user = $model->getCurrentUser();
	$shopper_groups = $user->shopper_groups;

	$json = file_get_contents(JPATH_SITE.$this->config->json);
	$array = (array) json_decode($json);
	$sefUrl = array();
	foreach ( $array as $value)
	{
		$sefUrl[$value->id] = JROUTE::_($value->url);
	}
}
?>

<?php if(!empty($this->config->isComponent) && empty($this->config->isModule)): ?>
<?php	
	$hasModule = JaMegafilterHelper::hasMegafilterModule(); 
	if($hasModule) {
		$this->config->sticky = 0; 
	} 
?>

<div class="row <?php echo $this->item['type'] ?> <?php echo $direction; ?> ja-megafilter-wrap clearfix layout-2">
	<?php if(!empty($this->config->fullpage) && !$hasModule): ?>
		<div data-mgfilter="vm"  class="<?php echo $direction ?> ja-mg-sidebar sidebar-main col">
			<a href="javascript:void(0)" class="sidebar-toggle">
				<span class="filter-open">
					<i class="fa fa-filter"></i><?php echo JText::_('COM_JAMEGAFILTER_OPEN_FILTER'); ?>
				</span>
				<span class="filter-close">
					<i class="fa fa-close"></i><?php echo JText::_('COM_JAMEGAFILTER_CLOSE_FILTER'); ?>
				</span>
			</a>
			<div class="block ub-layered-navigation-sidebar sidebar-content"></div>
		</div>
	<?php endif; ?>
	<?php 
		if ($hasModule || (empty($this->config->fullpage) && !$hasModule)) {
			$full_width = 'full-width';
		} else {
			$full_width = '';
		}
	?>
	<div class="main-content col <?php echo $full_width ?>">    
	</div>
</div>
<?php else: ?>
	<div data-mgfilter="vm" class="<?php echo $direction; ?> ja-mg-sidebar sidebar-main col">
		<div class="block ub-layered-navigation-sidebar sidebar-content"></div>
		<?php if(empty($this->config->isComponent)): ?>
			<a id="jamegafilter-search-btn" class="btn btn-default " href="javascript:void(0)">Search</a>
		<?php endif;?>
	</div>	
<?php endif; ?>

<?php if((!empty($this->config->isComponent) && empty($this->config->isModule)) ||
				(empty($this->config->isComponent) && !empty($this->config->isModule)) ): ?>

<script type="text/javascript">
<?php if(!empty($this->config->url)): ?>
var filter_url = '<?php echo $this->config->url?>'	;
<?php endif; ?>
var JABaseUrl = '<?php echo JUri::base(true); ?>';
var sefUrl = <?php echo json_encode($sefUrl) ?>;
var shopper_groups = <?php echo json_encode($shopper_groups) ?>;
var maxHeight = 0;
var p = <?php echo json_encode($this->jstemplate); ?>;
for (var key in p) {
	if (p.hasOwnProperty(key)) {
		var compiled = dust.compile(p[key], key);
		dust.loadSource(compiled);
	}
}
jQuery(document).on('afterRender', function (e) {
		jQuery('.product-image-photo').on('load', function() {
			equal_height();
		})
});

jQuery(document).on('jamg-layout-change', function(e){
	var layout = jQuery('.jamg-layout-chooser .active').attr('data-layout');
	switch (layout) {
		case 'grid':
			equal_height();
			break;
		case 'list':
			destroy_equal_height();	
			break;
	}
})

var wWidth = jQuery(window).width();
jQuery( window ).resize(function(e) {
	// detect when scroll with keeping width on touch device
	if (wWidth === jQuery(window).width()) {
		return;
	}
	wWidth = jQuery(window).width();
	destroy_equal_height();
	equal_height();
});

// equal height
function equal_height() {
	var layout = jQuery('.jamg-layout-chooser .active').attr('data-layout') ;
	if (layout === 'list') return; 
	var item = jQuery('.product-item-info');
	var maxHeight = 0;
	item.each(function(){
		var h = jQuery(this).height();
		if( h > maxHeight) {
			maxHeight = h;
		}
	});
	item.height(maxHeight);
}

function destroy_equal_height() {
	jQuery('.product-item-info').css('height','');
}

function bindCallback() {
	setTimeout(function(){
		jQuery('.quantity-controls.quantity-plus, .quantity-controls.quantity-minus').off().unbind().on('click' ,function(){
			$val = jQuery(this).parents('form.product.js-recalculate').find('input.quantity-input').val();
			$val = parseInt($val);
			if (jQuery(this).hasClass('quantity-plus'))
				$val++;
			else $val--;
			if ($val<1) $val=1;
			jQuery(this).parents('form.product.js-recalculate').find('input.quantity-input').val($val);
		});
		if (jQuery('.jamegafilter-wrapper').find('.pagination-wrap').length) {
			jQuery('.jamegafilter-wrapper').removeClass('no-pagination');
		} else {
			jQuery('.jamegafilter-wrapper').addClass('no-pagination');
		}

		if (isMobile.apple.tablet && jQuery('#t3-off-canvas-sidebar').length) {
			jQuery('select').unbind().off().on('touchstart', function() {
					formTouch=true;
					fixedElement.css('position', 'absolute');
					fixedElement.css('top', jQuery(document).scrollTop());
			});
			jQuery('html').unbind().off().on('touchmove', function() {
				if (formTouch==true) {
					fixedElement.css('position', 'fixed');
					fixedElement.css('top', '0');
					formTouch=false;
				}
			});
		}
		initScript();
	}, 100);
	if (jQuery('.items.product-items').find('.item').length == 0) {
		jQuery('.toolbar-amount').each(function(){
			jQuery(this).find('.toolbar-number').first().text(0);
		});
	}
}

function scrolltop() {
	if (!isMobile.phone) jQuery("html, body").stop().animate({ scrollTop: jQuery('div.ja-megafilter-wrap').offset().top }, 400);
}

function afterAutoPage() {
	var autopage =<?php echo $input->getCmd('autopage') ? 'true':'false' ?>;
	if (autopage) {
		Virtuemart.product(jQuery("form.product"))
	}
}

function MegaFilterCallback() {
	Virtuemart.product(jQuery("form.product"));
	bindCallback();
	<?php echo $input->getCmd('scrolltop') ? 'scrolltop();':'' ?>
}


function afterGetData(item) {
//	console.log(item.id)
	item.url = sefUrl[item.id];

	if ( item['shopper_groups'].length !== 0 ) {
		var common = jQuery.grep(shopper_groups, function(element) {
			return jQuery.inArray(element, item['shopper_groups'] ) !== -1;
		});

		if (common.length !== 0 ) {
			return false;
		} else {
			return true;
		}
	} else {
		return false;
	}
}

jQuery(document).ready(function() {
	var UBLNConfig = {};
	UBLNConfig.dataUrl = "<?php echo JUri::base(true).$this->config->json;  ?>";
	UBLNConfig.fields = <?php echo json_encode($this->config->fields); ?>;
	UBLNConfig.sortByOptions = <?php echo json_encode($this->config->sorts); ?>;
	UBLNConfig.defaultSortBy = "position";
	UBLNConfig.productsPerPageAllowed = [<?php echo implode(',', $this->config->paginate); ?>];
	UBLNConfig.autopage = <?php echo $this->config->autopage ? 'true':'false' ?>;
	UBLNConfig.sticky = <?php echo $this->config->sticky ? 'true':'false' ?>;
	UBLN.main(UBLNConfig);
	MegaFilterCallback();
});
</script>
<?php endif;