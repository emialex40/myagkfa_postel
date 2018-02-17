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

class JFormFieldFilterfields extends JFormFieldJaMegafilter_filterfields
{
	protected $type = 'filterfields';

	function getFieldGroups()
	{
		$class_methods = get_class_methods($this);
		$fl_array = preg_grep('/getJaMegafilterField(.*?)/', $class_methods);
		
		$fieldgroups = array();
		foreach ($fl_array as $value) {
			$array_key = strtolower( substr($value, 20) );
			$fieldgroups[$array_key] = $this->{ $value }();
		}
		return $fieldgroups;
	}
	
	function getJaMegafilterFieldBaseField()
	{
		$basefield = array(
			array(
				"published"=>0,
				"sort" => 0,
				"field"=> "name",
				"title"=>JText::_("COM_JAMEGAFILTER_TITLE"),
				"name"=>JText::_("COM_JAMEGAFILTER_TITLE"),
				"filter_type"=>array("value")
			),

			array(
				"published"=>0,
				"sort" => 0,
				"field"=> "pprice",
				"title"=>JText::_("COM_JAMEGAFILTER_PRICE"),
				"name"=>JText::_("COM_JAMEGAFILTER_PRICE"),
				"filter_type"=>array("range")
			),

			array(
				"published"=>0,
				"sort" => 0,	
				"field"=> "attr.cat.value",
				"title"=>JText::_("COM_JAMEGAFILTER_CATEGORY"),
				"name"=>JText::_("COM_JAMEGAFILTER_CATEGORY"),
				"filter_type"=>array("single", "dropdown", "multiple")
			),

			array(
				"published"=>0,
				"sort" => 0,	
				"field"=> "attr.manu.value",
				"title"=>JText::_("COM_JAMEGAFILTER_MANUFACTURER"),
				"name"=>JText::_("COM_JAMEGAFILTER_MANUFACTURER"),
				"filter_type"=>array("single", "dropdown", "multiple")
			),	

			array(
				"published"=>0,
				"sort" => 0,	
				"field"=> "rating",
				"title"=>JText::_("COM_JAMEGAFILTER_RATING"),
				"name"=>JText::_("COM_JAMEGAFILTER_RATING"),
				"filter_type"=>array("range")
			),
			array(
					"published" => 0,
					"sort" => 0,
					"field" => "attr.featured.value",
					"title" => JText::_("COM_JAMEGAFILTER_FEATURED"),
					"name" => JText::_("COM_JAMEGAFILTER_FEATURED"),
					"filter_type" => array("single", "dropdown", "multiple")
			),
			array(
				"published"=>0,
				"sort" => 0,	
				"field"=> "product_height",
				"title"=>JText::_("COM_JAMEGAFILTER_HEIGHT"),
				"name"=>JText::_("COM_JAMEGAFILTER_HEIGHT"),
				"filter_type"=>array("range")
			),

			array(
				"published"=>0,
				"sort" => 0,	
				"field"=> "product_width",
				"title"=>JText::_("COM_JAMEGAFILTER_WIDTH"),
				"name"=>JText::_("COM_JAMEGAFILTER_WIDTH"),
				"filter_type"=>array("range")
			),		

		);
		return $basefield;
	}

	function getJaMegafilterFieldCustomField()
	{
		$supported_field = array(
			'S' => array( "single", "dropdown", "multiple", "color" ), 
			'B' => array( "single", "dropdown", "multiple", "color" ), 
			'D' => array( "date" ), 
			'X' => array( "value" ), 
			'Y' => array( "value" )
			);
		$customfield = array();
		$q = 'SELECT * FROM #__virtuemart_customs WHERE published=1';
		$db = JFactory::getDbo()->setQuery($q);
		$fields = $db->loadObjectList();

		foreach ($fields as $field) 
		{
			if (in_array($field->field_type, array_keys ($supported_field) )) 
			{
				$customfield[] = array(
					"published" => 0,
					"sort" => 0,	
					"field" => 'attr.ct'.$field->virtuemart_custom_id.'.value',
					"title" => $field->custom_title,
					"name" => $field->custom_title,
					"filter_type" => $supported_field[$field->field_type]
					);
			}
		}

		return $customfield;
	}
}