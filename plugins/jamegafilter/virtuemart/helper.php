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

class VirtuemartFilterHelper 
{
	function getLangSuffix()
	{
		$vmlang = VmConfig::get('active_languages','', true);
		$lang_sfx = array();

		if ($vmlang) {
			foreach ($vmlang as $tag) {
				$lang_sfx[] = str_replace('-', '_', strtolower($tag));
			}
		} else {
			$frontend_lang = JComponentHelper::getParams('com_languages')->get('site');
			$lang_sfx[] = str_replace('-', '_', strtolower($frontend_lang));
		}
		return $lang_sfx;
	}
	
	function getBaseItem($id, $lang)
	{
		$db = $db = JFactory::getDBO();
		$select = 'SELECT vp.*';
		$from = 'FROM `#__virtuemart_products` as vp';
		$join = '';
		$select .= ', vp_'.$lang.'.product_name';
		$join .= ' LEFT JOIN `#__virtuemart_products_'.$lang.'` as vp_'.$lang.' on vp.virtuemart_product_id = vp_'.$lang.'.virtuemart_product_id';
		
		if ($lang != VmConfig::$vmlang) {
			$select .= ', vp_'.VmConfig::$vmlang.'.product_name as product_name_'.VmConfig::$vmlang;
			$join .= ' LEFT JOIN `#__virtuemart_products_'.VmConfig::$vmlang.'` as vp_'.VmConfig::$vmlang.' on vp.virtuemart_product_id = vp_'.VmConfig::$vmlang.'.virtuemart_product_id';
		}
		
		$where = 'WHERE vp.virtuemart_product_id = ';
		$q = $select.' '.$from.' '.$join.' '.$where.$id;
		$db->setQuery($q);
		$baseItem = $db->loadObject();

		if (!$baseItem->product_name) {
			$baseItem->product_name = $baseItem->{ 'product_name_'.VmConfig::$vmlang} ;
		} 
		return $baseItem;
	}
	
	function getCatInfo($id, $lang, $catsList)
	{
		$db = $db = JFactory::getDBO();
		$q = 'SELECT * FROM `#__virtuemart_product_categories`  WHERE `virtuemart_product_id` = ' . (int) $id;
		$db->setQuery ($q);
		$categoryIds = $db->loadAssocList();
		foreach ($categoryIds as $cat) {
			if (!in_array($cat['virtuemart_category_id'], $catsList)) continue;
			$parentTree = $this->getParentsList($cat['virtuemart_category_id'], $lang);
			$this->attr['cat']['value'][] = $cat['virtuemart_category_id'];
			$nametree = '';
			foreach ($parentTree AS $key => $pt) {
				if ($key) {
					$nametree .= ' &raquo; '.$pt->category_name;
				} else {
					$nametree .= $pt->category_name;
				}
			}
			$this->attr['cat']['frontend_value'][] = $nametree;
		}
		return $this->attr;
	}
	
	function getParentsList($virtuemart_category_id, $lang)
	{
		$db = JFactory::getDBO();
		$menu = JFactory::getApplication()->getMenu();
		$catModel = VmModel::getModel('Category');
		$menuItem = $menu->getActive();
		$menuCatid = (empty($menuItem->query['virtuemart_category_id'])) ? 0 : $menuItem->query['virtuemart_category_id'];
		$parents_id = array_reverse($catModel->getCategoryRecurse($virtuemart_category_id,$menuCatid));
		
		$parents = array();
		foreach ($parents_id as $id ) {
			$select = 'SELECT vc.virtuemart_category_id';
			$from = 'FROM `#__virtuemart_categories` as vc';
			$join = '';
			$select .= ', vc_'.$lang.'.category_name ';
			$join .= ' LEFT JOIN `#__virtuemart_categories_'.$lang.'` as vc_'.$lang.' on vc.virtuemart_category_id = vc_'.$lang.'.virtuemart_category_id';

			if ($lang != VmConfig::$vmlang) 
			{
				$select .= ', vc_'.VmConfig::$vmlang.'.category_name as category_name_'.VmConfig::$vmlang;
				$join .= ' LEFT JOIN `#__virtuemart_categories_'.VmConfig::$vmlang.'` as vc_'.VmConfig::$vmlang.' on vc.virtuemart_category_id = vc_'.VmConfig::$vmlang.'.virtuemart_category_id';
			}
			
			$where = 'WHERE vc.virtuemart_category_id = ';
			$q = $select.' '.$from.' '.$join.' '.$where.(int)$id;
			$db->setQuery($q);
			$obj = $db->loadObject();
			
			if (!$obj->category_name) {
				$obj->category_name = $obj->{ 'category_name_'.VmConfig::$vmlang };
			}
			
			$parents[] = $obj;
		}
		return $parents;
	}
	
	function getThumbnail($id, $lang)
	{
		$explode = explode('_', $lang);
		$lang_tag = $explode[0].'-'.strtoupper ($explode[1]);
		
		$db = JFactory::getDbo();
		$select = 'select vpme.*, vm.file_url, vm.file_lang';
		$from = 'from #__virtuemart_product_medias as vpme';
		$join = 'left join #__virtuemart_medias as vm on vpme.virtuemart_media_id = vm.virtuemart_media_id';
		$where = 'where vpme.virtuemart_product_id ='. (int) $id.' and vm.published = 1';
		$order = 'order by ordering asc';
		$q = $select.' '.$from.' '.$join.' '.$where.' '.$order;
		
		$db->setQuery($q);
		$list = $db->loadAssocList();

		if ( !empty($list) ) {
			foreach ($list as $image )
			{
				$file_lang = explode(',', $image['file_lang']);
				if ( empty($file_lang[0]) || in_array($lang_tag, $file_lang)) {
					return	$image['file_url'];
				}
			}
		}

		return 'components/com_virtuemart/assets/images/vmgeneral/noimage.gif';
	}
	
	function getPrice($vm_product)
	{
		if (!class_exists('CurrencyDisplay'))
			require(VMPATH_ADMIN . '/helpers/currencydisplay.php');
		$currency = CurrencyDisplay::getInstance( $vm_product->prices['product_currency']);
		$price = new stdClass();
		$price->pprice = !empty($vm_product->prices['salesPrice']) ? $vm_product->prices['salesPrice'] : 0;
		$priceDisplay = $currency->priceDisplay($price->pprice);
		$symbol = $currency->getSymbol();
		$price->frontend_price = str_replace($symbol, '<span class="currency">'.$symbol.'</span>', $priceDisplay);
		return $price;
	}

	function getCustomFields($virtuemart_product)
	{
		// Simple Custom Field.
		foreach ($virtuemart_product->customfields AS $v) 
		{
			if (empty($v->published)) continue;
			switch ($v->field_type) 
			{
				case 'X':
				case 'Y':
					if (empty($this->attr['ct'.$v->virtuemart_custom_id]['value']))
						$this->attr['ct'.$v->virtuemart_custom_id]['value']='';
					if (empty($this->attr['ct'.$v->virtuemart_custom_id]['frontend_value']))
						$this->attr['ct'.$v->virtuemart_custom_id]['frontend_value']='';
					$this->attr['ct'.$v->virtuemart_custom_id]['value'] .= $v->customfield_value.' ';
					$this->attr['ct'.$v->virtuemart_custom_id]['frontend_value'] .= $v->customfield_value.' ';
					break;

				case 'D':
					$this->attr['ct'.$v->virtuemart_custom_id]['value'][] = strtotime($v->customfield_value);
					$this->attr['ct'.$v->virtuemart_custom_id]['frontend_value'][] = strtotime($v->customfield_value);
					break;

				case 'S':
				case 'B':
					$this->attr['ct'.$v->virtuemart_custom_id]['value'][] = str_replace('+','%20',urlencode($v->customfield_value));
					$this->attr['ct'.$v->virtuemart_custom_id]['frontend_value'][] = $v->customfield_value;
					break;
			}
		}
		return $this->attr;
	}
	
	function getManufacturerInfo($id, $lang)
	{
		$db = JFactory::getDbo();
		$select = 'select vpma.virtuemart_manufacturer_id';
		$from = ' from #__virtuemart_product_manufacturers as vpma';
		$join = ' left join #__virtuemart_manufacturers as vma on vpma.virtuemart_manufacturer_id = vma.virtuemart_manufacturer_id';
		$join .= ' left join #__virtuemart_manufacturercategories as vmac on vpma.virtuemart_manufacturer_id = vma.virtuemart_manufacturer_id';
		
		$select .= ', vma_'.$lang.'.mf_name' ;
		$join .= ' left join #__virtuemart_manufacturers_'.$lang.' as vma_'.$lang.' on vpma.virtuemart_manufacturer_id = vma_'.$lang.'.virtuemart_manufacturer_id';
		if ($lang != VmConfig::$vmlang) 
		{
			$select .= ', vma_'.VmConfig::$vmlang.'.mf_name as mf_name_'.VmConfig::$vmlang ;
			$join .= ' left join #__virtuemart_manufacturers_'.VmConfig::$vmlang.' as vma_'.VmConfig::$vmlang.' on vpma.virtuemart_manufacturer_id = vma_'.VmConfig::$vmlang.'.virtuemart_manufacturer_id';
		}
		
		$where = ' where vpma.virtuemart_product_id = '.$id;
		$where .= ' and vmac.published = 1 and vma.published = 1 group by vpma.virtuemart_manufacturer_id';
		$q = $select.$from.$join.$where;
		$db->setQuery($q);
		$mfList = $db->loadObjectList();

		if ( !empty($mfList) )
		{
			foreach ($mfList as $mf)
			{
				$this->attr['manu']['value'][] = $mf->virtuemart_manufacturer_id;
				$this->attr['manu']['frontend_value'][] = !empty($mf->mf_name) ? $mf->mf_name : $mf->{ 'mf_name_'.VmConfig::$vmlang };
			}
		}
		
		return $this->attr;
	}
	
	public function getFeatured($virtuemart_product) {
		$this->attr['featured']['value'][] = (string) $virtuemart_product->product_special;
		if ($virtuemart_product->product_special) {
			$this->attr['featured']['frontend_value'][] = JText::_('COM_JAMEGAFILTER_ONLY_FEATURED');
		} else {
			$this->attr['featured']['frontend_value'][] = JText::_('COM_JAMEGAFILTER_NOT_FEATURED');
		}
		return $this->attr;
	}

	function getItem($id, $lang, $catsList)
	{
		// because of virtuemart cache and support multi language
		$baseItem = $this->getBaseItem($id, $lang);
		$item = new stdClass();
		
		if ( VmConfig::get('stockhandle') == 'none' || VmConfig::get('stockhandle') == 'risetime' || $baseItem->product_in_stock != 0 ) 
			$item->is_salable = 1;
		else if($baseItem->product_in_stock == 0 && (VmConfig::get('stockhandle') == 'disableit' || VmConfig::get('stockhandle') == 'disableit_children') )
			return '';
			
		$item->id = $baseItem->virtuemart_product_id;
		$item->name = $baseItem->product_name;
		$item->thumbnail = $this->getThumbnail($id, $lang);
		$item->product_weight = !empty($baseItem->product_weight) ? (float) $baseItem->product_weight : 0;
		$item->product_length = !empty($baseItem->product_length) ? (float) $baseItem->product_length : 0;
		$item->product_width = !empty($baseItem->product_width) ? (float) $baseItem->product_width : 0;
		$item->product_height = !empty($baseItem->product_height) ? (float) $baseItem->product_height : 0;

		$item->created_date = strtotime($baseItem->created_on);
		$item->modified_date = strtotime($baseItem->modified_on);

		$rateModel = VmModel::getModel('Ratings');
		$rating = $rateModel->getRatingByProduct($id);
		$item->rating = (!empty($rating) ? ($rating->rating) : 0);
		$item->width_rating = $item->rating*20;
		
		$productModel = VmModel::getModel('Product');
		$virtuemart_product = $productModel->getProduct($id, false, true, true, 1, 0);
		
		$item->shopper_groups = $virtuemart_product->shoppergroups ? $virtuemart_product->shoppergroups: array();
		$price = $this->getPrice($virtuemart_product);
		$item->pprice = $price->pprice;
		$item->frontend_price = $price->frontend_price;
		
		
		// special field.
		$this->attr = array();
		$this->getCustomFields($virtuemart_product);
		$this->getCatInfo($id, $lang, $catsList);
		$this->getManufacturerInfo($id, $lang);
		$this->getFeatured($virtuemart_product);
		$item->attr = $this->attr;

		if (!empty($item->jacat)) 
			$rootCat = (int) substr($item->jacat['category']['value'][0], 3);
		else 
			$rootCat = 0;
		$item->url = 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $id.'&virtuemart_category_id=' . $rootCat;
		
		return $item;
	}
	
	public function getListId($catIds)
	{
		$db = JFactory::getDbo();
		$select = 'select vpc.virtuemart_product_id';
		$from = ' from #__virtuemart_product_categories as vpc';
		$join = ' left join #__virtuemart_products as vp on vpc.virtuemart_product_id = vp.virtuemart_product_id';
		$where = ' where vpc.virtuemart_category_id in ('.  implode(',', $catIds).')';

		$where .= ' and vp.published = 1';
		$group = ' group by vpc.virtuemart_product_id order by vpc.virtuemart_product_id desc';
		$q = $select.$from.$join.$where.$group;
		$db->setQuery($q);
		$listID = $db->loadColumn();
		return $listID;
	}
	
	public function getItemList($catid, $lang)
	{
		$itemList = new stdCLass();
		$catModel = VmModel::getModel('Category');
		$childCats = $catModel->getCategoryTree($catid);
		$catsList = array($catid);
		if (!empty($childCats)) {
			foreach ($childCats AS $child) {
				$catsList[] = $child->virtuemart_category_id;
			}
		}
		
		$itemIdList = $this->getListId ( $catsList );
		foreach ($itemIdList as $id) {
			$property = 'item_'.$id;
			$item = $this->getItem($id, $lang, $catsList);
			if( !empty($item))
				$itemList->{ $property } = $item;
			else
				continue;
		}
		
		return $itemList;
	}
	
	function getFilterItems($catid)
	{
		VmConfig::set ('llimit_init_BE',10000);
		$filterItems = array();
		$lang_sfx = $this->getLangSuffix();
		foreach ($lang_sfx AS $lang) {
			$filterItems[$lang] = $this->getItemList($catid, $lang);
		}
		return $filterItems;
	}
	
}