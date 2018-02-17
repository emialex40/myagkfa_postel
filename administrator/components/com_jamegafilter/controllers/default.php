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

class JaMegaFilterControllerDefault extends JControllerForm
{
	function saveobj() {
		$model = $this->getModel();
		return $model->saveobj();
	}
	
	function jaapply() {
		$obj = $this->saveobj();
		$app = JFactory::getApplication();
		$app->enqueueMessage(JText::_('COM_JAMEGAFILTER_SAVE_SUCCESS'));
		$this->setRedirect('index.php?option=com_jamegafilter&view=default&layout=edit&id='.$obj->id);
	}
	
	function jasave() {
		$this->saveobj();
		$app = JFactory::getApplication();
		$app->enqueueMessage(JText::_('COM_JAMEGAFILTER_SAVE_SUCCESS'));
		$this->setRedirect('index.php?option=com_jamegafilter');
	}
}
