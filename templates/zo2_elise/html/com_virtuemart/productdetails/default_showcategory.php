<?php
/**
 *
 * Show the product details page
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers, Valerie Isaksen

 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2012 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default_showcategory.php 8811 2015-03-30 23:11:08Z Milbo $
 */

// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );	
	
	$parent_cat = array();
	$parent_cat[0]['title'] = $this->category->category_name;
	$parent_cat[0]['id'] = $this->category->virtuemart_category_id;


	$children_cat = array();
	if($this->category->haschildren){
		foreach ($this->category->children as $key => $value) {
			$children_cat[$key]['title'] = $value->category_name;
			$children_cat[$key]['id'] = $value->virtuemart_category_id;
		}
	}
	$array_category = array_merge($parent_cat,$children_cat);

	foreach ($array_category as $k => $v) {
		# code...
		echo "<a href='".JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $v['id'], FALSE)."'>".$v['title']."</a>";
		echo "&nbsp;&nbsp;";
	}

 ?>


