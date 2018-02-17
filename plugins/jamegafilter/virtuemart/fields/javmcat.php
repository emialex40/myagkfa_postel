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
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldJavmcat extends JFormField
{
	protected $type = 'Javmcat';

	protected function getInput()
	{
		$value = 0;
		if (!empty($this->value)) {
			$value = $this->value;
		}

		$catModel = VmModel::getModel('Category');
		$items = $catModel->getCategoryTree(0, 0, true, '', '', 500);
		
		$html = '';
		$html = '<select name="'.$this->name.'">';
		$html .= '<option value="0">'.JText::_('COM_JAMEGAFILTER_ALL_CATEGORY').'</option>';
		foreach ($items AS $item) {
			$html .= '<option '.($value == $item->virtuemart_category_id ? ' selected="selected" ' : '').' value="'.$item->virtuemart_category_id.'">'.str_repeat('.&nbsp;&nbsp;', ($item->level-1)).'|_.&nbsp;'.$item->category_name.'</option>';
		}
		$html.='</select>';
		return $html;
	}
}