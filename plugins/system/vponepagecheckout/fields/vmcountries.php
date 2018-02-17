<?php
/*--------------------------------------------------------------------------------------------------------
# VP One Page Checkout - Joomla! System Plugin for VirtueMart 3
----------------------------------------------------------------------------------------------------------
# Copyright:     Copyright (C) 2012-2017 VirtuePlanet Services LLP. All Rights Reserved.
# License:       GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
# Author:        Abhishek Das
# Email:         info@virtueplanet.com
# Websites:      https://www.virtueplanet.com
----------------------------------------------------------------------------------------------------------
$Revision: 105 $
$LastChangedDate: 2017-01-23 14:03:40 +0530 (Mon, 23 Jan 2017) $
$Id: vmcountries.php 105 2017-01-23 08:33:40Z abhishekdas $
----------------------------------------------------------------------------------------------------------*/
defined('JPATH_PLATFORM') or die;
JFormHelper::loadFieldClass('list');

if (!class_exists('VmConfig'))
{
	$config = JPath::clean(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php');
	if(file_exists($config)) require($config);
}

class JFormFieldVMCountries extends JFormFieldList
{
	protected $type = 'VMCountries';
	
	protected static $vmcountries = null;

	protected function getOptions()
	{
		if(!class_exists('VmConfig'))
		{
			JFactory::getApplication()->enqueueMessage('VirtueMart 3 Component not found in your site.', 'error');
			return array();
		}
		
		if(self::$vmcountries === null)
		{
			$db = JFactory::getDBO();
			$query = $db->getQuery(true)
			            ->select('`virtuemart_country_id` AS value, `country_name` AS text')
			            ->from('`#__virtuemart_countries`')
			            ->where('published = 1');
			if(version_compare(JVERSION, '3.0.0', 'ge'))
			{
				$query->clear('limit');
			}
			$db->setQuery($query);
			self::$vmcountries = $db->loadObjectList();
		}
		$options = array();
		foreach(self::$vmcountries as $country)
		{
			$options[] = JHtml::_('select.option', (int) $country->value, $country->text);
		}
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}