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
//No direct to access this file.
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.folder');

class JaMegafilterHelper
{
	static function getSupportedComponentList() {
		$path = JPATH_PLUGINS.'/system/jamegafilter/';
		if (JFolder::exists(JPATH_PLUGINS.'/jamegafilter/')) {
			$path = JPATH_PLUGINS.'/jamegafilter/';
		}
		$folders = JFolder::folders($path);
		return $folders;
	}
	
	static function getComponentStatus($component)
	{
		$db = JFactory::getDbo();
		$q = 'select enabled from #__extensions where element = "'.$component.'"';
        $db->setQuery($q);
        $status = $db->loadResult();
        if($status) {
            return true;
        } else {
            return false;
        }
	}
	
	static function hasMegafilterModule() {
		$template = JFactory::getApplication()->getTemplate();
		$file = JPATH_SITE . '/templates/' . $template . '/templateDetails.xml';
		$xml = simplexml_load_file($file); 	
		$positions = array();
		foreach	($xml->positions->children() as $p) {
			$positions[] = (string) $p;
		}
		
		$modules = JModuleHelper::getModuleList();
		$i = 0;
		foreach ($modules as $module) {
			if ($module->module === 'mod_jamegafilter' && $module->menuid > 0 ) {
				$i++;
			}
		}
		return $i;
	}
}