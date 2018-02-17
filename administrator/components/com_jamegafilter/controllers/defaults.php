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

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class JaMegaFilterControllerDefaults extends JControllerAdmin
{
	function getModel($name = 'Default', $prefix = 'JaMegaFilterModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
	
	function export()
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$cid = $input->post->get('cid', array(), 'array');
		foreach ( $cid as $id)
		{
			if ($this->getModel()->exportById($id)) {
				$app->enqueueMessage(JText::_('COM_JAMEGAFILTER_EXPORT_SUCCESS'));
			}
		}
		$this->setRedirect('index.php?option=com_jamegafilter');
	}
	
	function export_all()
	{
		$app = JFactory::getApplication();
		$mode_list = $this->getModel('Defaults','JaMegaFilterModel');
		$items = $mode_list->getItems();
		foreach ( $items as $item)
		{
			if ($this->getModel()->exportById($item->id)) {
				$app->enqueueMessage(JText::_('COM_JAMEGAFILTER_EXPORT_SUCCESS'));
			}
		}
		$this->setRedirect('index.php?option=com_jamegafilter');
	}
}
