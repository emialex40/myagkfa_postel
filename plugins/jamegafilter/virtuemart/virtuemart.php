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

// no direct access
defined('_JEXEC') or die ('Restricted access');
if (!class_exists( 'VmConfig' ) && file_exists(JPATH_ROOT.'/administrator/components/com_virtuemart/helpers/config.php')) {
	require_once(JPATH_ROOT.'/administrator/components/com_virtuemart/helpers/config.php');
    $vmconfig = VmConfig::loadConfig();
}

// Initiate class to hold plugin events
class plgJamegafilterVirtuemart extends JPlugin {

	// Some params
	var $pluginName = 'jamegafiltervirtuemart';
	var $pluginNameHumanReadable = 'JA Megafilter VirtueMart Plugin';

	function __construct( & $subject, $params) {
		parent::__construct($subject, $params);
	}
	
	function onAfterSaveVirtuemartItems($item) {
		require_once (__DIR__.'/helper.php');
		$helper = new VirtuemartFilterHelper();
		$params = $item->params;
		$objectList = $helper->getFilterItems($params['javmcat']);
		return $objectList;
	}
	
	function onBeforeDisplayVirtuemartItems( $jstemplate, $filter_config, $item )
	{
		$this->jstemplate = $jstemplate;
		$this->config = $filter_config;
		$this->item = $item;
		$input = JFactory::getApplication()->input;
		$jalayout = $input->get('jalayout', 'default');
		$path = JPluginHelper::getLayoutPath('jamegafilter', 'virtuemart', $jalayout);
		
		ob_start();
		include $path;
		$output = ob_get_contents();
		ob_end_clean();
		echo $output;
	}
}