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
 * $Id: default_cartlist.php 105 2017-01-23 08:33:40Z abhishekdas $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;

$style = $this->params->get('style', 1);

if($style == 1 || $style == 3)
{
	// For style 1 and style 3 layout we need to have a different type of price list layout
	echo $this->loadTemplate('pricelistnarrow');
}
else
{
	// For style 2 and style 4 layout we use the same price list sublayout as first stage.
	// default_pricelist.php layout will always display full cart table when we are in final stage.
	echo $this->loadTemplate('pricelist');
}