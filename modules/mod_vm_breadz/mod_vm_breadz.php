<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );
/**
* Cherry's Breadcrumb Module
*
* @package VM Breadz 2.2 - 2012 May
* @copyright Copyright © 2009-2012 Maksym Stefanchuk All rights reserved.
* @license http://www.gnu.org/licenses/gpl.html GNU/GPL
*
* http://www.galt.md
*/

defined('VMLANG') or define('VMLANG', strtolower(JFactory::getLanguage()->getTag()));

require('controller.php');
require('config.php');

/*
$options = array();
$options['add_label'] = $params->get('add_label', null);
$pretext = $params->get('pretext', null);
$pretext_url = $params->get('pretext_url', null);
$options['showx'] = $params->get('showx', null);
$options['currency_sign'] = $params->get('currency_sign', null);
$options['startfrom'] = $params->get('startfrom', null);
breadzConf::setOptions($options);
*/

//echo '<pre style="font-size:13px;">';

$pretext = $params->get('pretext', null);
$pretext_url = $params->get('pretext_url', null);
breadzConf::setOptions($params->toArray());

$breadz = new chpBreadzController();

$categories = $breadz->getCategories();
$manufacturer = $breadz->getManufacturer();
$searchKeyword = $breadz->getSearchKeyword();
$prices = $breadz->getPrices();
$filters = $breadz->getFilters();
$product = $breadz->getProductData();


$elements = array_merge((array)$categories, (array)$manufacturer, (array)$searchKeyword, 
	(array)$prices, (array)$filters, (array)$product);

if ($elements) {
	// prepend pre-text to elements
	if ($pretext) {
		$data = array();
		$data['name'] = $pretext;
		$data['url'] = $pretext_url;
		$data['xurl'] = null;
		
		array_unshift($elements, $data);
	}

	require('writer.php');
	chpBreadzWriter::printBreadcrumbs($elements);
	
	$doc =& JFactory::getDocument();
	$doc->addStyleSheet( JURI::base() .'/modules/mod_vm_breadz/static/style.css' );
}


//var_dump($elements);
//print_r($elements);

?>