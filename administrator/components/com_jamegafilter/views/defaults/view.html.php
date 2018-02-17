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

class JaMegaFilterViewDefaults extends JViewLegacy
{
	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		// Get data from the model
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			$app->enqueueMessages(implode('<br />', $errors), 'message');

			return false;
		}

		// Set the toolbar
		$this->addToolBar();

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolBar()
	{
		JToolBarHelper::title(JText::_('COM_JAMEGAFILTER_MANAGER_DEFAULTS'));
		JToolBarHelper::addNew('default.add');
		JToolBarHelper::editList('default.edit');
		JToolBarHelper::deleteList('', 'defaults.delete');
 		JToolBarHelper::custom('defaults.export', 'database', '', JTEXT::_('COM_JAMEGAFILTER_EXPORT'));
		JToolBarHelper::custom('defaults.export_all', 'pending', '', JTEXT::_('COM_JAMEGAFILTER_EXPORT_ALL'), false);
	}
}