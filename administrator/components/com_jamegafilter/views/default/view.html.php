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

class JaMegaFilterViewDefault extends JViewLegacy
{

	protected $form = null;


	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		JPluginHelper::importPlugin('jamegafilter');
		// Get the Data
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			$app->enqueueMessages(implode('<br />', $errors), 'message');

			return false;
		}

		// Set the toolbar
		$this->addToolBar();
		
		$jinput = JFactory::getApplication()->input;
		$this->type = $jinput->get('type', 0);
		$this->title = $jinput->get('title', '');
		$this->published = $jinput->get('published', 0);
		if (!empty($this->type))
			$this->item->type = $this->type;
		if ($this->item->type == NULL) $this->item->type = 'blank';
		if (!empty($this->published))
			$this->item->published = $this->published;
		if (!empty($this->title))
			$this->item->title = $this->title;
		
		$this->typeLists = JaMegafilterHelper::getSupportedComponentList();
		$this->checkComponent = JaMegafilterHelper::getComponentStatus('com_'.$this->item->type);
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
		$input = JFactory::getApplication()->input;

		// Hide Joomla Administrator Main menu
		$input->set('hidemainmenu', true);

		$isNew = ($this->item->id == 0);

		if ($isNew)
		{
			$title = JText::_('COM_JAMEGAFILTER_NEW');
		}
		else
		{
			$title = JText::_('COM_JAMEGAFILTER_EDIT');
		}

		JToolBarHelper::title($title, 'default');
		JToolBarHelper::apply('default.jaapply');
		JToolBarHelper::save('default.jasave');
		JToolBarHelper::cancel(
			'default.cancel',
			$isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE'
		);
	}
}