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
 * $Id: default_advertisement.php 105 2017-01-23 08:33:40Z abhishekdas $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;
?>
<?php if(!empty($this->checkoutAdvertise) && $this->params->get('checkout_advertisement', 1)) : ?>
	<div id="proopc-advertise-box">
		<?php foreach($this->checkoutAdvertise as $checkoutAdvertise) : ?>
			<div class="checkout-advertise">
				<?php echo $checkoutAdvertise; ?>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>