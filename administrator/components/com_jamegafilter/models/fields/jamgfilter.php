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

jimport('joomla.form.formfield');

class JFormFieldJamgfilter extends JFormField
{
	protected $type = 'Jamgfilter';
	
	protected function getInput()
	{
		$value = 0;
		if (!empty($this->value)) {
			$value = $this->value;
		}
		$html = '';
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			  ->from($db->quoteName('#__jamegafilter'))
			  ->where('published=1');
		$db->setQuery($query);
		$items = $db->loadObjectList();
		$html = '<select onchange="Joomla.submitbutton(\'item.apply\');" name="'.$this->name.'">';
		$html .= '<option value="0">'.JText::_('JSELECT').'</option>';
		foreach ($items AS $item) {
			$html .= '<option '.($value == $item->id ? ' selected="selected" ' : '').' value="'.$item->id.'">'.$item->title.'</option>';
		}
		$html.='</select>';
		return $html;
	}
	
}