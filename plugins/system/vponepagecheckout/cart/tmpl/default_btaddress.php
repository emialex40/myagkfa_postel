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
 * $Id: default_btaddress.php 105 2017-01-23 08:33:40Z abhishekdas $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;
?>
<div class="inner-wrap">
	<div class="edit-address">
		<?php if(!empty($this->btFields['fields'])) : ?>
			<form id="EditBTAddres" autocomplete="off">
				<?php 
				foreach($this->btFields['fields'] as $name => $field)
				{
					$toolTip = !empty($field['tooltip']) ? ' class="hover-tootip" title="' . htmlspecialchars($field['tooltip']) . '"' : '';
					
					echo '<div class="'.$field['name'].'-group">';
					echo '<div class="inner">';
					
					if($field['type'] == 'delimiter')
					{
						echo '<h5 id="' . $field['name'] . '_field_delimiter" class="proopc-delimiter">';
						echo '<span' . $toolTip . '>' . JText::_($field['title']) . '</span>';
						echo '</h5>';
					}
					else
					{
						if($field['name'] == 'email')
						{
							$field['name'] = 'bt_' . $field['name'];
							$field['formcode'] = str_replace('id="email_field"', 'id="bt_email_field"', $field['formcode']);
						}
						
						echo '<label class="' . $field['name'] . '_field_lbl" for="' . $field['name'] . '_field">';
						echo '<span' . $toolTip . '>' . JText::_($field['title']) . '</span>';
						echo (strpos($field['formcode'], ' required') || $field['required'])  ? ' <span class="asterisk">*</span>' : '';
						echo '</label>';
						
						if(strpos($field['formcode'], 'vm-chzn-select') !== false)
						{
							echo str_replace('vm-chzn-select', '', $field['formcode']);
						}
						else
						{
							echo $field['formcode'];
						}
					}
					
					echo '</div>';
					echo '</div>';
				} ?>
			</form>
		<?php endif; ?>
	</div>
	<input type="hidden" name="billto" value="<?php echo $this->cart->lists['billTo']; ?>" />
</div>