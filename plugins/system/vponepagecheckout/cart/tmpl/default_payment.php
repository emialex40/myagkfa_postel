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
 * $Id: default_payment.php 105 2017-01-23 08:33:40Z abhishekdas $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;
?>
<?php if($this->found_payment_method) : ?>
	<div class="inner-wrap">
		<form id="proopc-payment-form">
			<fieldset>
				<?php foreach($this->paymentplugins_payments as $paymentplugin_payments)
				{
					if(is_array($paymentplugin_payments))
					{
						foreach($paymentplugin_payments as $paymentplugin_payment)
						{
							echo $paymentplugin_payment;
							echo '<div class="clear proopc-method-end"></div>';
						}
					}
				} ?>
			</fieldset>
		</form>
	</div>
<?php else : ?>
	<div class="proopc-alert-error payment"><?php echo $this->payment_not_found_text ?></div>  
<?php endif; ?>
