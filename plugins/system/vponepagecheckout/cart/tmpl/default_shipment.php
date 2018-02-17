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
 * $Id: default_shipment.php 105 2017-01-23 08:33:40Z abhishekdas $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;
?>
<?php if($this->found_shipment_method) : ?>
	<form id="proopc-shipment-form">
		<div class="inner-wrap">
			<fieldset>
				<?php foreach ($this->shipments_shipment_rates as $shipment_shipment_rates)
				{
					if(is_array($shipment_shipment_rates))
					{
						foreach ($shipment_shipment_rates as $shipment_shipment_rate)
						{
							echo $shipment_shipment_rate;
							echo '<div class="clear"></div>';
						}
					}
				} ?>
				
				<input type="hidden" name="proopc-savedShipment" id="proopc-savedShipment" value="<?php echo $this->cart->virtuemart_shipmentmethod_id ?>" />
			</fieldset>
		</div>
	</form>
<?php else : ?>
	<div class="proopc-alert-error"><?php echo $this->shipment_not_found_text ?></div>
<?php endif; ?>

