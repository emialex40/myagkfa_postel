<?php
/**
 *---------------------------------------------------------------------------------------
 * @package      VP One Page Checkout - Joomla! System Plugin
 * @subpackage   For VirtueMart 3+
 *---------------------------------------------------------------------------------------
 * @copyright    Copyright (C) 2012-2017 VirtuePlanet Services LLP. All rights reserved.
 * @license      GNU General Public License version 2 or later; see LICENSE.txt
 * @authors      Abhishek Das
 * @email        info@virtueplanet.com
 * @link         https://www.virtueplanet.com
 *---------------------------------------------------------------------------------------
 * $Revision: 105 $
 * $LastChangedDate: 2017-01-23 14:03:40 +0530 (Mon, 23 Jan 2017) $
 * $Id: order_done.php 105 2017-01-23 08:33:40Z abhishekdas $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;

echo '<div class="vm-wrap vm-order-done">';

if($this->display_title)
{
	echo '<h3>' . vmText::_('COM_VIRTUEMART_CART_ORDERDONE_THANK_YOU') . '</h3>';
}

// Everything here is displayed by payment method plugin.
// It is exactly same as standard VirtueMart order done layout. We just need to print it as it is.
echo $this->html;

if(vRequest::getBool('display_loginform', true) && !JFactory::getUser()->guest && class_exists('shopFunctionsF'))
{
	echo shopFunctionsF::getLoginForm();
}

echo '</div>';