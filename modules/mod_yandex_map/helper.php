<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_google_map
 *
 * @copyright   Copyright (C) 2015 Artem Yegorov. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Helper for mod_google_map
 *
 * @package     Joomla.Site
 * @subpackage  mod_yandex_map
 * @since       1.1.0
 */
class ModYandexmapHelper
{
	public static function getParams($id)
	{
		if (isset($id) && ($id > 0))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('params')
			->from('#__modules')
			->where("id={$id}");
			$db->setQuery($query);
			$array =  $db->loadAssoc();
			$fields =  json_decode($array['params']);
			$marker = $fields->marker;
			$marker_en = json_encode($marker);
			$fields->marker = $marker_en;			
		} else {
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('params')
			->from('#__modules')
			->where('module="mod_yandex_map"');
			$db->setQuery($query);
			$array =  $db->loadAssoc();
			$fields =  json_decode($array['params']); 
			$marker = $fields->marker;
			$marker_en = json_encode($marker);
			$fields->marker = $marker_en;
			
		}
			return $fields;
	}
		
}
