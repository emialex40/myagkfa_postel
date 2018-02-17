<?php
/**
 * @package     CSVI
 * @subpackage  Template
 *
 * @author      Roland Dalmulder <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - 2016 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        http://www.csvimproved.com
 */

defined('_JEXEC') or die;

if ($this->action == 'export' && count($this->fields) == 0)
{
	echo JText::_('COM_CSVI_WIZARD_EXPORT_FINALIZE_NO_FIELDS');
}
else
{
	echo JText::_('COM_CSVI_WIZARD_' . $this->action . '_FINALIZE');
}
