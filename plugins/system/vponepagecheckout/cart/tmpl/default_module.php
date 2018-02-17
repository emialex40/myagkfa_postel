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
 * $Id: default_module.php 105 2017-01-23 08:33:40Z abhishekdas $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;

$modules = $this->getCartModules();
$count = count($modules);
$i = 0;
?>
<?php if($count > 0) : ?>
	<div class="proopc-cart-modules">
		<?php foreach($modules as $module) : ?>
			<?php if(!empty($module->moduleHtml)) : ?>
				<?php $i++; ?>
				<div class="proopc-row">
					<div class="cart-promo-mod<?php echo ($i == $count) ? ' last' : ''; ?>">
						<?php if($module->showtitle) : ?>
							<h3><?php echo $module->title ?></h3>
						<?php endif; ?>
						<div class="proopc-cart-module">
							<?php echo $module->moduleHtml; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
<?php endif; ?>