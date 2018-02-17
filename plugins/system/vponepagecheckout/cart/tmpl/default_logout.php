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
 * $Id: default_logout.php 105 2017-01-23 08:33:40Z abhishekdas $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;
?>
<?php if(!$this->juser->guest) : ?>
	<form action="<?php echo JRoute::_('index.php?option=com_users&task=user.logout') ?>" method="post" name="logout" id="form-logout">
		<div class="proopc-loggedin-user">
			<?php echo vmText::sprintf('COM_VIRTUEMART_WELCOME_USER', $this->juser->name); ?>&nbsp;<b class="caret"></b>
		</div>
		<div class="proopc-logout-cont hide">
			<div class="proopc_arrow_box">
				<div class="proopc-arrow-inner">
					<button type="submit" class="proopc-btn <?php echo $this->btn_class_1 ?>"><?php echo JText::_( 'JLOGOUT'); ?></button>
				</div>
			</div>
		</div>
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.logout" />
		<?php echo JHtml::_('form.token') ?>
		<input type="hidden" name="return" value="<?php echo base64_encode('index.php?option=com_virtuemart&view=cart') ?>" />
	</form>
<?php endif ?>