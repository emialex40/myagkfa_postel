<?php
/**
 * ------------------------------------------------------------------------
 * JA Megafilter Component
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2016 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');

require_once JPATH_COMPONENT.'/assets/asset.php';
JFactory::getDocument()->addScriptDeclaration("
	Joomla.submitbutton = function(task)
	{
		if (task == 'default.cancel' || document.formvalidator.isValid(document.getElementById('item-form'))) {
			Joomla.submitform(task, document.getElementById('item-form'));
		}
	};
");
$app = JFactory::getApplication();
$typeLists = $this->typeLists;
$item = $this->item;
?>

<form action="<?php echo JRoute::_('index.php?option=com_jamegafilter&view=default&layout=edit&id=' . (int) $this->item->id); ?>"
		method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="form-horizontal">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_JAMEGAFILTER_DETAILS'); ?></legend>
			<div class="row-fluid">
				<div class="">
					<div class="control-group">
						<div class="control-label"><?php echo JText::_('COM_JAMEGAFILTER_COMPONENT'); ?></div>
						<div class="controls">
							<select onChange="window.location.href='<?php echo JRoute::_('index.php?option=com_jamegafilter&layout=edit&id=' . (int) $this->item->id); ?>&view=default&type='+this.value;" id="jform_jatype" name="jform[jatype]">
								<option value="blank"><?php echo JText::_('JSELECT'); ?></option>
								<?php
								if (!empty($typeLists)):
									foreach ($typeLists AS $l):
										echo '<option '.($item->type == $l ? ' selected="selected" ' : '').' value="'.$l.'" >'.ucfirst($l).'</option>';
									endforeach;
								endif;
								?>
							</select>
						</div>
					</div>
					<?php if ($item->type != 'blank'): ?>
					<div class="control-group">
						<div class="control-label"><?php echo JText::_('COM_JAMEGAFILTER_PUBLISHED'); ?></div>
						<div class="controls">
							<fieldset id="jform_params_menu_text" class="btn-group btn-group-yesno radio">
								<input type="radio" id="jform_params_menu_text0" name="jform[published]" value="1" <?php echo ($item->published==1 ? ' checked="checked"' : '') ?> />
								<label for="jform_params_menu_text0" class="btn"><?php echo JText::_('JYES'); ?></label>
								<input type="radio" id="jform_params_menu_text1" name="jform[published]" value="0" <?php echo ($item->published==0 ? ' checked="checked"' : '') ?> />
								<label for="jform_params_menu_text1" class="btn"><?php echo JText::_('JNO'); ?></label>
							</fieldset>
						</div>
					</div>
					<div class="control-group">
						<div class="control-label">
							<label for="jform_title">
								<?php echo JText::_('COM_JAMEGAFILTER_TITLE') ?>
								<span class="star">&nbsp;*</span>
							</label>
						</div>
						<div class="controls">
							<input id="jform_title" type="text" class="inputbox" value="<?php echo $item->title; ?>" name="jform[title]" required />
						</div>
					</div>
					<?php endif; ?>
					<?php if (empty($this->checkComponent) && $item->type != 'blank'): ?>
						<?php echo $app->enqueueMessage(JText::sprintf('COM_JAMEGAFILTER_COMPONENT_NOT_FOUND', ucfirst($item->type)), 'error'); ?>
					<?php endif; ?>
					<?php
					if (!empty($this->form) && !empty($this->checkComponent) && $item->type != 'blank'):
						foreach ($this->form->getFieldset('base') as $field): ?>
							<div class="control-group">
									<div class="control-label">
										<?php echo $this->form->getLabel($field->fieldname,$field->group); ?>
									</div>
									<div class="controls">
										<?php echo $this->form->getInput($field->fieldname,$field->group,(!empty($item->params[$field->fieldname]) ? $item->params[$field->fieldname] : false)); ?>
									</div>
							</div>
						<?php endforeach; ?>
						<?php foreach ($this->form->getFieldset('filterfields') as $field):?>
							<div class="control-group">
								<div class="">
									<?php echo $this->form->getInput($field->fieldname,$field->group,(!empty($item->params[$field->fieldname]) ? $item->params[$field->fieldname] : false)); ?>
								</div>
							</div>
						<?php endforeach; ?>
					<?php else:?>
						<?php if ($item->type != 'blank'): ?>
							<?php echo $app->enqueueMessage(JText::_('COM_JAMEGAFILTER_FORM_NOT_FOUND'), 'error'); ?>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</div>
		</fieldset>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" value="<?php echo $item->id; ?>" name="jform[id]" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<script>
jQuery( function($) {
	$( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
	$( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
	$( "#tabs" ).tabs().find( ".ui-tabs-nav" ).sortable({
		handle: ".icon-menu",
		axis: "y",
		start: function (e, ui) {
			drag_i= $(ui.item[0]).index();

		},
		stop: function(e, ui) {
			drop_i = $(ui.item[0]).index()
			if (drop_i !== drag_i) {
				helper = $('<div>', {
					class:'ui-tabs-panel'
				})
				helper.insertAfter($('.ui-tabs-panel:last'))
				moved = $($('.ui-tabs-panel')[drag_i]).detach()
				moved.insertBefore($($('.ui-tabs-panel')[drop_i]))
				helper.remove()
			}
			$( "#tabs" ).tabs( "refresh" );
		}
	});

	$( "tbody" ).sortable({
		axis: "y",
		handle: ".icon-menu"
	});
});

function publish_item(e) {
	var input = jQuery(e).next(), 
			ele = jQuery(e),
			span
	
	switch ( input.val()) {
		case '0':
			input.val(1)
			span = ele.find('span')
			span.removeClass('icon-unpublish')
			span.addClass('icon-publish')
			break
		case '1':
			input.val(0)
			span = ele.find('span')
			span.removeClass('icon-publish')
			span.addClass('icon-unpublish')
			break;
	}
}

</script>
