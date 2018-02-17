<?php

/**
 * ------------------------------------------------------------------------
 * JA Megafilter Module
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */
defined('_JEXEC') or die('Restricted access');

class ModJamegafilterHelper {

	public $config;
	public $jstemplate;
	public $url;

	function display($params) {
		$app = JFactory::getApplication();
		
		$isComInstalled = JComponentHelper::isInstalled('com_jamegafilter');
		if (!$isComInstalled) {
			$app->enqueueMessage(JText::_('MOD_JAMEGAFILTER_COMPONENT_IS_NOT_INSTALLED'), 'error');
			return;
		}

		$menu = JFactory::getApplication()->getMenu();
		$item = $menu->getItem($params->get('filter', 0));
		$query = $item->query;
		if (empty($query['id'])) {
			$app->enqueueMessage(JText::_('MOD_JAMEGAFILTER_PLEASE_CHOOSE_FILTER_MENU'), 'error');
			return;
		}
		
		$q = 'SELECT * FROM #__jamegafilter WHERE id=' . $query['id'];
		$db = JFactory::getDbo()->setQuery($q);
		$page = $db->loadObject();
		if (empty($page)) {
			$app->enqueueMessage(JText::_('MOD_JAMEGAFILTER_FILTER_PAGE_IS_NOT_EXIST'), 'error');
			return;
		}

		$isPluginEnabled = JPluginHelper::isEnabled('jamegafilter', $page->type);
		if (!$isPluginEnabled) {
			$app->enqueueMessage(JText::_('MOD_JAMEGAFILTER_FILTER_PLUGIN_IS_NOT_ENABLED_OR_INSTALLED'), 'error');
			return;
		}
		
		$num = JaMegafilterHelper::hasMegafilterModule();
		if ($num > 1) {
			$app->enqueueMessage(JText::_('MOD_JAMEGAFILTER_FILTER_EACH_PAGE_MUST_HAS_MAXIMUM_ONE_MEGAFILTER_MODULE'), 'error');
			return;
		}
		
		$this->loadAssets();

		$input = $app->input;
		$input->set('jalayout', $query['jalayout']);

		require_once JPATH_SITE . '/components/com_jamegafilter/views/default/view.html.php';
		$view = new JaMegaFilterViewDefault();
		$view->_addCss($page->type);
		$view->_addLayoutPath($page->type);

		$this->config = $view->_getFilterConfig((array) $page);
		$this->config->isModule = true;
		$this->config->Moduledirection = $params->get('direction', null);
		$this->config->url = JROUTE::_('index.php?Itemid=' . $params->get('filter', 0));
		$this->jstemplate = $view->_loadJsTemplate();

		if ($page->type !== 'blank') {
			JPluginHelper::importPlugin('jamegafilter');
			$dispatcher = JEventDispatcher::getInstance();
			$dispatcher->trigger('onBeforeDisplay' . ucfirst($page->type) . 'Items', array($this->jstemplate, $this->config, (array) $page));
		}
	}

	function loadAssets() {
		require_once __DIR__ . '/assets/assets.php';
	}
}
