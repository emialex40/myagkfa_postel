<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class chpBreadzController {
	
	public $ptid = null;
	private $baseurl;
	private $mid;
	private $keyword;
	private $low_price;
	private $high_price;

	private $ptFields = array();
	private $cfFields = array();

	private $appliedFiltersURL = '';
	private $appliedPricesURL = '';

	//private $parameters = array();
	//private $applied_filters = array();
	
	function __construct() {
		$cid = JRequest::getVar('virtuemart_category_id', null);
		$mid = JRequest::getVar('virtuemart_manufacturer_id', null);
		$keyword = JRequest::getVar('keyword', null);
		$pid = JRequest::getVar('virtuemart_product_id', null);
		$ptid = JRequest::getVar('ptid', null);
		$itemid = JRequest::getVar('Itemid', null);
		
		$s = 'index.php?option=com_virtuemart&view=category';
		if ($itemid) $s .= '&Itemid='. $itemid;
		if ($cid) $s .= '&virtuemart_category_id='. $cid;
		//if ($mid) $s .= '&virtuemart_manufacturer_id='. $mid;
		if ($ptid) $this->baseurl .= '&ptid='. $ptid;

		$this->baseurl = $s;

		if ($mid) $this->mid = '&virtuemart_manufacturer_id='. $mid;
		if ($keyword) $this->keyword = '&keyword='. urlencode($keyword);

		$this->getAppliedPrices();
		$this->getProductTypeFields();
		$this->getCustomFields();
		
	}

	
	public function getCategories() {
	
		$cid = JRequest::getVar('virtuemart_category_id', null);
		if (!$cid) return;
	
		require_once( JPATH_BASE . '/administrator/components/com_virtuemart/models/category.php' );

		$category_model = new VirtueMartModelCategory();
		$categories = $category_model->getParentsList($cid);
		
		if (!$categories) return;
		
		$baseurl = 'index.php?option=com_virtuemart&view=category&virtuemart_category_id=';
	//	if ($this->mid) $baseurl .= '&virtuemart_manufacturer_id='. $this->mid;
		$data = array();
		
		foreach ($categories as $category) {
			$d = array();
			$d['name'] = $category->category_name;
			$d['url'] = $baseurl . $category->virtuemart_category_id;
			$d['xurl'] = '';
			
			$data[] = $d;
		}
		
		//var_dump($data);
		
		return $data;
	}


	public function getManufacturer() {
		
		$mid = JRequest::getVar('virtuemart_manufacturer_id', null);

		if (!$mid) return;

		$name = '';
		if (breadzConf::option('add_label')) $name .= '<span class="breadz-label">'. breadzConf::option('manufacturer_label') .':</span> ';

		$db =& JFactory::getDBO();
		$q = "SELECT `mf_name` FROM `#__virtuemart_manufacturers_". VMLANG ."` WHERE `virtuemart_manufacturer_id`=". $mid;
		$db->setQuery($q);
		$name .= $db->loadResult();

		$d = array();
		$d['name'] = $name;
		$d['url'] = '';

		$xurl = $this->baseurl;
		if ($this->keyword) $xurl .= $this->keyword;
		$xurl .= $this->getAppliedFiltersUrl();
		$xurl .= $this->appliedPricesURL;

		$d['xurl'] = $xurl;

		return array($d);
	}


	public function getSearchKeyword() {

		$keyword = JRequest::getVar('keyword', null);
		if (!$keyword) return;

		$d = array();
		$name = '';
		if (breadzConf::option('add_label')) $name .= '<span class="breadz-label">'. breadzConf::option('keyword_label') .':</span> ';
		$name .= $keyword;
		$d['name'] = $name;
		$d['url'] = '';

		$xurl = $this->baseurl;
		if ($this->mid) $xurl .= $this->mid;
		$xurl .= $this->getAppliedFiltersUrl();
		$xurl .= $this->appliedPricesURL;

		$d['xurl'] = $xurl;

		return array($d);
	}

	
	public function getPrices() {
		$view = JRequest::getVar('view', null);
		
		if ($view == 'productdetails' && isset($_SESSION['applied_prices'])) {
			return $_SESSION['applied_prices'];
		}
		
		//$low_price = JRequest::getVar('low-price', null);
		//$high_price = JRequest::getVar('high-price', null);

		$lp = $this->low_price;
		$hp = $this->high_price;
		
		if (!$lp && !$hp) {
			$_SESSION['applied_prices'] = array();
			return;
		}
		
		
	//	$url = '';
		if ($lp && !$hp) {
			$name = breadzConf::option('currency_sign') . $lp . JText::_('BR_AND_ABOVE');
	//		$url = 'low-price='. $lp;
		}
		else if (!$lp && $hp) {
			$name = breadzConf::option('currency_sign') . $hp . JText::_('BR_AND_UNDER');
	//		$url = 'high-price='. $hp;
		}
		else {
			$name = breadzConf::option('currency_sign') . $lp . JText::_('BR_TO') . breadzConf::option('currency_sign') . $hp;
	//		$url = 'low-price='. $lp .'&high-price='. $hp;
		}
		
		
		$data = array();
		$d = array();
		$d['name'] = '';
		if (breadzConf::option('add_label')) $d['name'] .= '<span class="breadz-label">'. breadzConf::option('price_label') .':</span> ';
		$d['name'] .= $name;
		//$d['url'] = $this->baseurl .'&'. $url;
		$d['url'] = '';

		$xurl = $this->baseurl;
		if ($this->mid) $xurl .= $this->mid;
		if ($this->keyword) $xurl .= $this->keyword;
		$filters_url = $this->getAppliedFiltersUrl();
		if ($filters_url) $xurl .= $filters_url;
		
		$d['xurl'] = $xurl;
		
		$data[] = $d;
		
		if ($view == 'category') $_SESSION['applied_prices'] = $data;
		
		//var_dump($filters_url);
		//var_dump($data);
		
		return $data;
	}

	
	public function getFilters() {

		$view = JRequest::getVar('view', null);

		if ($view == 'productdetails' && isset($_SESSION['applied_filters'])) {
			return $_SESSION['applied_filters'];
		}

		$pt = $this->getProductTypeFilters();
		$cf = $this->getCustomFieldFilters();

		$appliedFilters = array_merge($pt, $cf);

		//var_dump($appliedFilters);

		if ($view == 'category') $_SESSION['applied_filters'] = $appliedFilters;


		return $appliedFilters;
	}


	private function getProductTypeFilters() {

		//$lp = $this->low_price;
		//$hp = $this->high_price;
		
		$range_separator = ':';

		$data = array();

		foreach ($this->ptFields as $field) {
			$is_range = strpos($field['value'], $range_separator);
			if ($is_range !== false) {
				$v = explode($range_separator, $field['value']);
				if ($v[0] && !$v[1]) {
					$title = $v[0] . JText::_('BR_AND_ABOVE');
				} 
				else if (!$v[0] && $v[1]) {
					$title = $v[1] . JText::_('BR_AND_UNDER');
				}
				else {
					$title = $v[0] . JText::_('BR_TO') . $v[1];
				}
				
			} else {
				$title = str_replace('|', ', ', $field['value']);
			}

			$d = array();
			$d['name'] = '';
			if (breadzConf::option('add_label')) $d['name'] .= '<span class="breadz-label">'. $field['title'] . ':</span> ';
			$d['name'] .= $title;
			if ($field['unit']) $d['name'] .= ' '. $field['unit'];
			
			$d['url'] = '';
			$d['xurl'] = $this->baseurl;
			$xurl = $this->getFilterRemoveUrl($field['name']);
			if ($xurl) $d['xurl'] .= '&'. $xurl;
			//if ($lp) $d['xurl'] .= '&low-price='. $lp;
			//if ($hp) $d['xurl'] .= '&high-price='. $hp;
			$d['xurl'] .= $this->appliedPricesURL;
			
			
			$data[] = $d;
		}

		
		//var_dump($data);
		
		return $data;
	}


	private function getCustomFieldFilters() {
		//$lp = $this->low_price;
		//$hp = $this->high_price;
		
		$data = array();

		foreach ($this->cfFields as $field) {
			
			$title = str_replace('|', ', ', $field['value']);
			

			$d = array();
			$d['name'] = '';
			if (breadzConf::option('add_label')) $d['name'] .= '<span class="breadz-label">'. $field['title'] . ':</span> ';
			$d['name'] .= $title;
			
			$d['url'] = '';
			$d['xurl'] = $this->baseurl;
			$xurl = $this->getFilterRemoveUrl($field['name']);
			if ($xurl) $d['xurl'] .= '&'. $xurl;
			//if ($lp) $d['xurl'] .= '&low-price='. $lp;
			//if ($hp) $d['xurl'] .= '&high-price='. $hp;
			$d['xurl'] .= $this->appliedPricesURL;
			if ($this->mid) $d['xurl'] .= $this->mid;
			if ($this->keyword) $d['xurl'] .= $this->keyword;
			
			
			$data[] = $d;
		}

		
		//var_dump($data);
		
		return $data;
	}


	private function getAppliedPrices() {
		$this->low_price = $this->validatePriceValue(JRequest::getVar('low-price', 0));
		$this->high_price = $this->validatePriceValue(JRequest::getVar('high-price', 0));

		$url = '';
		if ($this->low_price) $url .= '&low-price='. $this->low_price;
		if ($this->high_price) $url .= '&high-price='. $this->high_price;
		$this->appliedPricesURL = $url;
	}


	private function validatePriceValue($price) {
		// not empty
		if (empty($price)) return false;
		// not -X or 0
		if ($price <= 0) return false;
		// change , with .
		$price = str_replace(',','.',$price);
		if (!is_numeric($price)) return false;
		// remove leading/trailing zeros
		$price += 0;
		
		return $price;
	}

	
	public function getProductData() {
		$pid = JRequest::getVar('virtuemart_product_id', null);
		if (!$pid) return;
		
		$d = array();
		$data = array();
		$d['name'] = $this->getProductName($pid);
		$d['url'] = '';
		$d['xurl'] = '';
		
		$data[] = $d;
		
		return $data;
	}

	
	private function getProductName($pid) {
		$db=& JFactory::getDBO();
		$q="SELECT `product_name` FROM `#__virtuemart_products_". VMLANG ."` WHERE `virtuemart_product_id`=$pid";
		$db->setQuery($q);
		return $db->loadResult();
	}


	private function getProductTypeFields() {
		
		$db =& JFactory::getDBO();

		$q = "SHOW TABLES LIKE '%_vm_product_type_parameter%'";
		$db->setQuery($q);
		$tableExists = $db->loadResult();

		if (!$tableExists) return;

		$q = "SELECT `parameter_name`, `parameter_label`, `parameter_unit` FROM `#__vm_product_type_parameter`";
		$db->setQuery($q);
		$fields = $db->loadObjectList();


		$data = array();
		foreach ($fields as $field) {
			$get = JRequest::getVar($field->parameter_name, null);
			if ($get) {
				//$data[$field] = $get;
				$data[] = array("name" => $field->parameter_name, "title" => $field->parameter_label, "value" => $get, 
					"unit" => $field->parameter_unit);
			}
		}

		if ($data) $this->ptFields = $data;


		//var_dump($data);
	}


	private function getCustomFields() {

		$db =& JFactory::getDBO();

		$q = "SELECT `custom_title`, `custom_field_desc` FROM `#__virtuemart_customs` WHERE `custom_parent_id` ".
			"IN (SELECT `virtuemart_custom_id` FROM `#__virtuemart_customs` ".
			"WHERE `custom_parent_id`=0 AND `field_type`='P')";
		
		$db->setQuery($q);
		$fields = $db->loadObjectList();

		$data = array();
		foreach ($fields as $field) {
			$cfName = $this->healCustomFieldName($field->custom_title);
			$get = JRequest::getVar($cfName, null);
			if ($get) {
				//$data[$field] = $get;
				$data[] = array("name" => $field->custom_title, "title" => $field->custom_field_desc, "value" => $get);
			}
		}

		if ($data) $this->cfFields = $data;

		//var_dump($data);
	}


	private function healCustomFieldName($cfName) {
		if (function_exists('mb_strtolower')) {
			$cfName = mb_strtolower($cfName);
		} else {
			$cfName = strtolower($cfName);
		}

		$forbiddenChars = array(" ");
		$healedCFName = str_replace($forbiddenChars, "_", $cfName);

 		return $healedCFName;
	}

	
	private function getFilterRemoveUrl($name) {

		$filters = array();
		foreach ($this->ptFields as $field) {
			if ($field['name'] != $name) $filters[] = $field['name'] ."=". urlencode($field['value']);
		}

		foreach ($this->cfFields as $field) {
			if ($field['name'] != $name) $filters[] = $field['name'] ."=". urlencode($field['value']);
		}		

		$url = implode('&', $filters);


		return $url;
	}
	

	private function getAppliedFiltersUrl() {

		if ($this->appliedFiltersURL) return $this->appliedFiltersURL;
		
		//$filters = array();
		$url = '';
		foreach ($this->ptFields as $field) {
			//$filters[] = $field['name'] ."=". urlencode($field['value']);
			$url .= '&'. $field['name'] ."=". urlencode($field['value']);
		}

		foreach ($this->cfFields as $field) {
			//$filters[] = $field['name'] ."=". urlencode($field['value']);
			$url .= '&'. $field['name'] ."=". urlencode($field['value']);
		}		

		//$url = '&'. implode('&', $filters);

		$this->appliedFiltersURL = $url;

		return $url;
	}

}


?>