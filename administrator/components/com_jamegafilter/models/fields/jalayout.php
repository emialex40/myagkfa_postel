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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');
jimport('joomla.filesystem.folder');

class JFormFieldJalayout extends JFormField {

	protected $type = 'jalayout';

	protected function getInput() {
		$layouts = array();
		$type = $this->getFilterType();
		$templatePath = $this->getTemplatePath($type);
		$paths = array(
				JPATH_PLUGINS . '/jamegafilter/' . $type . '/tmpl'
		);
		if (!empty($templatePath)) {
			$paths[] = $templatePath;
		}
		foreach ($paths as $path) {
			if (JFolder::exists($path)) {
				$files = JFolder::files($path, '\.php');
				if (!empty($files)) {
					foreach ($files as $file) {
						$file = preg_replace('#\.[^.]*$#', '', $file);
						if ($file != 'default') {
							$layouts[] = $file;
						}
					}
				}
			}
		}
		$layouts = array_unique($layouts);
		$html = '<select name="' . $this->name . '">';
		$html .= '<option value="default">default</option>';
		foreach ($layouts as $layout) {
			$selected = ($layout == $this->value) ? 'selected' : '';
			$html .= '<option value="' . $layout . '" ' . $selected . '>' . $layout . '</option>';
		}
		$html .= '</select>';
		return $html;
	}

	function getFilterType() {
		$input = JFactory::getApplication()->input;
		$id = $input->getCmd('id', 0);
		if ($id) {
			$model = new MenusModelItem();
			$menu = $model->getItem($id);
			if (!empty($menu->request['id'])) {
				$q = 'SELECT type from #__jamegafilter where id=' . $menu->request['id'];
				$db = JFactory::getDbo()->setQuery($q);
				$result = $db->loadResult();
				return $result;
			}
		}
		return;
	}

	function getTemplatePath($type) {
		$input = JFactory::getApplication()->input;
		$id = $input->getCmd('id', 0);
		if ($id) {
			$q = 'SELECT template_style_id from #__menu where id=' . $id;
			$db = JFactory::getDbo()->setQuery($q);
			$template_style_id = $db->loadResult();
			if ($template_style_id) {
				$q = 'SELECT template from #__template_styles where id=' . $template_style_id;
				$db = JFactory::getDbo()->setQuery($q);
				$path = $db->loadResult();
			} else {
				$q = 'SELECT template from #__template_styles where client_id = 0 and home = 1';
				$db = JFactory::getDbo()->setQuery($q);
				$path = $db->loadResult();
			}
		}

		if (empty($path)) {
			return;
		} else {
			return JPATH_SITE . '/templates/' . $path . '/html/plg_jamegafilter_' . $type;
		}
	}

}
