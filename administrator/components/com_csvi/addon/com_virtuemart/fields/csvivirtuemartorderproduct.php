<?php
/**
 * @package     CSVI
 * @subpackage  VirtueMart
 *
 * @author      Roland Dalmulder <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - 2016 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        http://www.csvimproved.com
 */

defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('CsviForm');

/**
 * Select list form field with order products.
 *
 * @package     CSVI
 * @subpackage  VirtueMart
 * @since       4.0
 */
class JFormFieldCsviVirtuemartOrderProduct extends JFormFieldCsviForm
{
	/**
	 * Type of field
	 *
	 * @var    string
	 * @since  4.0
	 */
	protected $type = 'CsviVirtuemartOrderProduct';

	/**
	 * Select order products.
	 *
	 * @return  array  An array of users.
	 *
	 * @since   4.0
	 */
	protected function getOptions()
	{
		$skus = $this->form->getValue('orderproduct', 'jform');
		$orderProducts = array();

		if (!empty($skus) && !empty($skus[0]))
		{
			foreach ($skus as $pkey => $sku)
			{
				$skus[$pkey] = $this->db->quote($sku);
			}

			$query = $this->db->getQuery(true)
				->select($this->db->quoteName('order_item_sku', 'value') . ',' . $this->db->quoteName('order_item_name', 'text'))
				->from($this->db->quoteName('#__virtuemart_order_items'))
				->where($this->db->quoteName('order_item_sku') . ' IN (' . (implode(',', $skus) . ')'))
				->order($this->db->quoteName('order_item_name'))
				->group($this->db->quoteName('order_item_sku'));

			$this->db->setQuery($query);
			$orderProducts = $this->db->loadObjectList();

			if (empty($orderProducts))
			{
				$orderProducts = array();
			}
		}

		return array_merge(parent::getOptions(), $orderProducts);
	}
}
