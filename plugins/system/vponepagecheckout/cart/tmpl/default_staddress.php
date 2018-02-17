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
 * $Id: default_staddress.php 105 2017-01-23 08:33:40Z abhishekdas $
 * --------------------------------------------------------------------------------------
*/
defined('_JEXEC') or die;

if($this->juser->guest)
{
	unset($this->stFields['fields']['address_type_name']);
}
?>

<!--<div id="proopc-st-address">
	<div class="inner-wrap">
		<div class="edit-address" style="display: block;">
			<form id="EditSTAddres" autocomplete="off">
					<div class="shipto_address_1-group">
				        <div class="inner">
				            <label class="shipto_address_1" for="shipto_address_1_field">
				            <span class="hover-tootip" data-tiptext="Адрес">Адрес </span><span style="color: #f00;">*</span>
				            </label>
				            <input class="required" required="required" type="text" id="shipto_address_1_field" name="shipto_address_1" size="30" value="" maxlength="64" style="width: 266px;">
				        </div>
				    </div>
				    <input type="hidden" name="shipto_virtuemart_userinfo_id" id="shipto_virtuemart_userinfo_id" value="0">
				</form>
		</div>
	</div>
</div>-->




<script>

jQuery(document).ready(function(){
    jQuery("#proopc-shipment-form").click(function(){
        if(jQuery("#shipment_id_1").attr("checked") != "checked") {
            jQuery(".proopc-st-address").css("display","none");     
        }
        else {
            jQuery(".proopc-st-address").css("display","block");
        }
    });
});
</script>

<!--<div id="proopc-st-address">
	<div class="inner-wrap">
		<label for="STsameAsBT" class="st-same-checkbox">
			<input type="checkbox" name="STsameAsBT" id="STsameAsBT" <?php echo $this->cart->STsameAsBT == 1 ? 'checked="checked"' : ''; ?> onclick="return ProOPC.setst(this);" />
			<?php echo JText::_('COM_VIRTUEMART_USER_FORM_ST_SAME_AS_BT') ?>
		</label>
		<div class="edit-address<?php echo ($this->cart->STsameAsBT == 1) ? '' : ' soft-show'; ?>">
			<?php if(!empty($this->stFields['fields'])) : ?>
				<form id="EditSTAddres" autocomplete="off">
					<?php 
					if($this->selectSTName && !$this->juser->guest)
					{
						echo '<div class="proopc-select-st-group">';
						echo '<div class="inner">';
						echo '<label class="proopc-select-st_field" for="proopc-select-st">' . JText::_('PLG_VPONEPAGECHECKOUT_SELECT_ADDRESS') . '</label>';
						echo $this->selectSTName;
						echo '</div>';
						echo '</div>';
					}
					
					foreach($this->stFields['fields'] as $field)
					{
						$toolTip = !empty($field['tooltip']) ? ' class="hover-tootip" title="' . htmlspecialchars($field['tooltip']) . '"' : '';
						
						echo '<div class="' . $field['name'] . '-group">';
						echo '<div class="inner">';
						
						echo '<label class="' . $field['name'] . '" for="' . $field['name'] . '_field">';
						echo '<span' . $toolTip . '>' . JText::_($field['title']) . '</span>';
						echo (strpos($field['formcode'], ' required') || $field['required']) ? ' <span class="asterisk">*</span>' : '';
						echo '</label>';
						
						if(strpos($field['formcode'], 'vm-chzn-select') !== false)
						{
							echo str_replace('vm-chzn-select', '', $field['formcode']);
						}
						else
						{
							echo $field['formcode'];
						}
						
						echo '</div>';
						echo '</div>';
					} ?>
					<input type="hidden" name="shipto_virtuemart_userinfo_id" id="shipto_virtuemart_userinfo_id" value="<?php echo $this->cart->selected_shipto ?>" />
				</form>
			<?php endif; ?>
		</div>
	</div>
</div>-->
