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
 * $Id: default_style1.php 105 2017-01-23 08:33:40Z abhishekdas $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;

$checkout_step = 1;
?>
<div id="proopc-system-message"></div>
<div class="proopc-finalpage<?php echo $this->params->get('reload', 0) ? ' proopc-reload' : ''; ?>">
	<div class="proopc-row">
		<h1 class="cart-page-title">
			<?php echo JText::_ ('COM_VIRTUEMART_CART_TITLE'); ?>&nbsp;<span class="septa">/</span>&nbsp;<span><?php echo JText::sprintf('COM_VIRTUEMART_CART_X_PRODUCTS', '<span id="proopc-cart-totalqty">' . $this->productsCount . '</span>'); ?></span>
		</h1> 
	</div>
	<div class="proopc-row">
		<div class="proopc-login-message-cont">
			<?php if($this->juser->guest && !$this->params->get('only_guest', 0)) : ?>
				<a href="#goback" data-vpopc="redirect" data-vphref="<?php echo JRoute::_('index.php?option=com_virtuemart&view=cart&ctask=goback', $this->useXHTML, $this->useSSL) ?>" class="proopc-goback-link"><?php echo JText::_('JLOGIN') . '/' . JText::_('JREGISTER') ?></a>
			<?php elseif(!$this->juser->guest) : ?>
				<?php echo $this->loadTemplate('logout'); ?>
			<?php endif; ?>
		</div>	
		<?php if(!empty($this->continue_link)) : ?>
			<div class="proopc-continue-link">
				<a href="<?php echo $this->continue_link ?>"><?php echo vmText::_('COM_VIRTUEMART_CONTINUE_SHOPPING') ?></a>
			</div>
		<?php endif; ?>
	</div>
	<div class="proopc-column3">
	    <div class="proopc-cartlist">
			<h3 class="proopc-process-title"><?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : ''; ?><?php echo JText::_('COM_VIRTUEMART_CART_OVERVIEW')?></h3>
			<form id="proopc-carttable-form">
				<div id="proopc-pricelist">
					<?php echo $this->loadTemplate ('cartlist'); ?>
				</div>
				<input type="hidden" name="ctask" value="updateproduct" />
			</form>
		</div>
	</div>
	<div class="proopc-column3">
		<div class="proopc-bt-address">
			<h3 class="proopc-process-title">
				<?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : ''; ?><?php echo JText::_('COM_VIRTUEMART_USER_FORM_BILLTO_LBL') ?>
			</h3>
			<?php echo $this->loadTemplate('btaddress'); ?>
		</div>
		<div class="proopc-shipments">
			<h3 class="proopc-process-title">
				<?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : ''; ?><?php echo JText::_('COM_VIRTUEMART_CART_SHIPPING')?>
			</h3>
			<div id="proopc-shipments">
				<?php echo $this->loadTemplate ('shipment'); ?>
			</div>
			<div class="samov" style="margin: 15px;">При выборе самовывоза: после оформления заказа с вами свяжется оператор для уточнения адреса забора заказа.</div>
		</div>
		<!--<div class="proopc-st-address">
			<h3 class="proopc-process-title">
				<?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step"></div>' : ''; ?><?php echo JText::_('COM_VIRTUEMART_USER_FORM_SHIPTO_LBL') ?>
			</h3>
			<?php //echo $this->loadTemplate ('staddress'); ?>
		</div>-->
	</div>
	<div class="proopc-column3 last">
	    <div class="proopc-payments">
			<h3 class="proopc-process-title">
				<?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : ''; ?><?php echo JText::_('COM_VIRTUEMART_CART_PAYMENT')?>
			</h3>
			<div id="proopc-payments">
				<?php echo $this->loadTemplate ('payment'); ?>
			</div>
			<div class="beznal" style="margin: 15px;">При выборе счёта для безналичной оплаты: после оформления заказа с вами свяжется оператор и выставит вам счет.</div>
		</div>
	    <?php if (VmConfig::get('coupons_enable')) : ?>
			<div class="proopc-coupon">
				<h3 class="proopc-process-title">
					<?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : ''; ?><?php echo JText::_('COM_VIRTUEMART_COUPON_DISCOUNT')?>
				</h3>
				<div id="proopc-coupon">
					<?php echo $this->loadTemplate('coupon'); ?>
				</div>
			</div>
		<?php endif; ?>
		<?php if($this->params->get('handlerbund_compliant', 0)) : ?>
			<div class="proopc-additional-info">
				<h3 class="proopc-process-title">
					<?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : '' ?><?php echo JText::_('PLG_VPONEPAGECHECKOUT_ADDITIONAL_INFO')?>
				</h3>
				<div id="proopc-additional-info">
					<?php echo $this->loadTemplate ('cartform'); ?>
				</div>
			</div>
		<?php endif; ?>
	    
		<div class="proopc-confirm-order">
			<!--<h3 class="proopc-process-title"><?php echo $this->params->get('oncheckout_show_steps', 1) ? '<div class="proopc-step">'.($checkout_step++).'</div>' : ''; ?><?php echo JText::_('COM_VIRTUEMART_ORDER_CONFIRM_MNU')?></h3>-->
			<div id="proopc-confirm-order">
				<?php echo $this->loadTemplate ('confirm'); ?>
			</div>
			<?php echo $this->loadTemplate('advertisement'); ?>
		</div>
	</div>
</div>