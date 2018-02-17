<?php
/**
 * ------------------------------------------------------------------------
 * JA Megafilter Component
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

class JaMegaFilterViewDefault extends JViewLegacy {

	public $_layout_path = array();
	public $_css_path = array();

	function display($tpl = null) {
		$app = JFactory::getApplication();
		$this->item = $this->get('Item');

		if (empty($this->item)) {
			$app->enqueueMessage(JText::_('COM_JAMEGAFILTER_UNDEFINED_MENU_ID'), 'error');
			return;
		}

		if (empty($this->item['published'])) {
			$app->enqueueMessage(JText::_('COM_JAMEGAFILTER_ITEM_UNPUBLISHED'), 'error');
			return;
		}

		if (!JaMegaFilterHelper::getComponentStatus('com_' . $this->item['type'])) {
			$app->enqueueMessage(JText::_('COM_JAMEGAFILTER_COMPONENT_NOT_FOUND'), 'error');
			return;
		}

		$jatype = $this->item['type'];

		$this->_addCss($jatype);

		$this->_addLayoutPath($jatype);

		$this->jstemplate = $this->_loadJsTemplate();

		$filter_config = $this->_getFilterConfig($this->item);

		if ($jatype === 'blank') {
			parent::display($tpl);
		} else {
			JPluginHelper::importPlugin('jamegafilter');
			$dispatcher = JEventDispatcher::getInstance();
			$dispatcher->trigger('onBeforeDisplay' . ucfirst($jatype) . 'Items', array($this->jstemplate, $filter_config, $this->item ));
		}
	}

	function _getFilterConfig($item) {
		$config = new stdClass();
		$jinput = JFactory::getApplication()->input;
		$itp = $jinput->get('itemperrow', 3, 'INT');
		$column = $jinput->get('itempercol', 5, 'INT');
		$itp = $itp*$column;
		$paginate = array($itp, $itp +($column*1), $itp +($column*2), $itp +($column*3), $itp +($column*4));

		$params = json_decode($item['params']);
		$fields = array();
		$sorts = array();
		$sorts[] = array('field' => 'id', 'title' => JText::_('JPOSITION'));

		if (!empty($params->filterfields)) {
			foreach ((array) $params->filterfields as $filters) {
				foreach ((array) $filters as $filter) {
					if (!$filter->published)
						continue;

					if ($filter->sort)
						$sorts[] = array(
								'field' => $filter->field,
								'title' => $filter->title
						);

					$fields[] = array(
							'type' => $filter->type,
							'title' => $filter->title,
							'field' => $filter->field,
							'frontend_field' => str_replace('.value', '.frontend_value', $filter->field));
				}
			}
		}

		$sorts[] = array('field'=>'created_date', 'title'=> JText::_('COM_JAMEGAFILTER_CREATED_DATE'));
		$sorts[] = array('field'=>'modified_date', 'title'=> JText::_('COM_JAMEGAFILTER_MODIFIED_DATE'));

		$langs = JFactory::getLanguage()->getKnownLanguages();
		$lang_tag = JFactory::getLanguage()->getTag();
		$lang_suffix = str_replace('-', '_', strtolower($lang_tag));

		$json = JPATH_ROOT . '/media/com_jamegafilter/' . $lang_suffix . '/' . $item['id'] . '.json';
		if (file_exists($json)) {
			$config->json = '/media/com_jamegafilter/' . $lang_suffix . '/' . $item['id'] . '.json';
		} else {
			foreach ($langs as $lang ) {
				$alter_suffix = str_replace('-', '_', strtolower($lang['tag']));
				$alter_json = JPATH_ROOT . '/media/com_jamegafilter/' . $alter_suffix . '/' . $item['id'] . '.json';
				if ($lang['tag'] != $lang_tag && file_exists($alter_json)) {
					$config->json = '/media/com_jamegafilter/' . $alter_suffix . '/' . $item['id'] . '.json';
					break;
				}
			}
		}

		$option = $jinput->get('option');
		if (!empty($option) && $option === 'com_jamegafilter') {
			$config->isComponent = true;
		}
		
	
		$config->fullpage = $jinput->get('fullpage', 1);
		$config->autopage = $jinput->get('autopage',0);
		$config->sticky = $jinput->get('sticky',0);
		$config->paginate = $paginate;
		$config->sorts = $sorts;
		$config->fields = $fields;
		$config->direction = $jinput->get('direction','vertical');

		return $config;
	}

	function _loadJsTemplate() {
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$template_names = array();

		$layouts_path = JPATH_SITE . '/components/com_jamegafilter/layouts';
		$filter_path = $layouts_path . '/filter';

		$base_files = JFolder::files($layouts_path);
		foreach ($base_files as $base) {
			$template_names[] = JFile::stripExt($base);
		}

		$filter_files = JFolder::files($filter_path);
		foreach ($filter_files as $filter) {
			$template_names[] = JFile::stripExt($filter);
		}

		$jstemplate = new stdClass();
		foreach ($template_names as $name) {
			$jstemplate->{ $name } = $this->_loadLayout($name);
		}

		return $jstemplate;
	}

	function _addLayoutPath($jatype) {
		$app = JFactory::getApplication();
		
		$input = $app->input;

		$jalayout = $input->get('jalayout', 'default');

		$layouts_path = JPATH_SITE . '/components/com_jamegafilter/layouts';

		$filter_path = $layouts_path . '/filter';

		$template_path = JPATH_THEMES . '/' . $app->getTemplate() . '/html/layouts/jamegafilter/' . $jatype . '/' . $jalayout;

		$filter_template_path = $template_path . '/filter';

		$plugin_path_default = JPATH_PLUGINS . '/jamegafilter/' . $jatype . '/layouts/default';

		$filter_plugin_path_default = $plugin_path_default . '/filter';
		
		$plugin_path = JPATH_PLUGINS . '/jamegafilter/' . $jatype . '/layouts/' . $jalayout;

		$filter_plugin_path = $plugin_path . '/filter';

		// add template path
		array_unshift($this->_layout_path, $filter_path);

		array_unshift($this->_layout_path, $layouts_path);
		
		array_unshift($this->_layout_path, $filter_plugin_path_default);
		
		array_unshift($this->_layout_path, $plugin_path_default);

		array_unshift($this->_layout_path, $filter_plugin_path);

		array_unshift($this->_layout_path, $plugin_path);

		array_unshift($this->_layout_path, $filter_template_path);

		array_unshift($this->_layout_path, $template_path);

		return;
	}

	function _loadLayout($name) {
		// Clear prior output
		$this->_output = null;

		// Load the template script
		jimport('joomla.filesystem.path');

		$filename = preg_replace('/[^A-Z0-9_\.-]/i', '', $name);

		$file = JPath::find($this->_layout_path, $filename . '.php');

		if ($file != false) {

			ob_start();

			include $file;

			$this->_output = ob_get_contents();
			ob_end_clean();

			return $this->_output;
		} else {
			throw new Exception(JText::sprintf('JLIB_APPLICATION_ERROR_LAYOUTFILE_NOT_FOUND', $name . '.php'), 500);
		}
	}

	function _addCss($jatype) {
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();

		if (file_exists(JPATH_SITE . '/components/com_jamegafilter/assets/css/style.css')) {
			$doc->addStyleSheet(JURI::root(true) . '/components/com_jamegafilter/assets/css/style.css');
		}

		if (file_exists(JPATH_PLUGINS . '/jamegafilter/' . $jatype . '/assets/css/style.css')) {
			$doc->addStyleSheet(JURI::root(true) . '/plugins/jamegafilter/' . $jatype . '/assets/css/style.css');
		}
		
		if (file_exists(JPATH_THEMES . '/' . $app->getTemplate() . '/css/jamegafilter.css')) {
			$doc->addStyleSheet('templates/' . $app->getTemplate()  . '/css/jamegafilter.css');
		}
	}

}
