<?php
/**
 * @package     CSVI
 * @subpackage  Template
 *
 * @author      Roland Dalmulder <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - 2016 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        http://www.csvimproved.com
 */

defined('_JEXEC') or die;

$class = 'span12';

if ($this->extraHelp)
{
	$class = 'span11';
}

?>
<div class="row-fluid">
	<div class="<?php echo $class; ?>">
		<div class="span2">
			<?php echo $this->loadTemplate('steps'); ?>
		</div>
		<div class="span10">
			<div class="step_explanation">
				<h3><?php echo JText::_('COM_CSVI_WIZARD_STEP_EXPLANATION'); ?></h3>
				<?php echo JText::_('COM_CSVI_TEMPLATE_EXPLAIN_' . $this->action . '_STEP' . $this->step); ?>
			</div>
			<hr />
			<?php echo $this->loadTemplate('step' . $this->step); ?>
		</div>
	</div>
	<?php
		if ($this->extraHelp)
		{
			$layout = new JLayoutFile('csvi.help-arrow');
			echo $layout->render((object) array(new stdClass));
		}
	?>
</div>
<input type="hidden" name="step" value="<?php echo ++$this->step; ?>" />

<script type="text/javascript">
	var token = '<?php echo JSession::getFormToken(); ?>';
	jQuery(document).ready(function ()
	{
		// Turn off the help texts
		jQuery('.help-block, .cron-block').hide();

		// Hide/show the system fields
		Csvi.showFields(jQuery('#jform_use_system_limits').val(), '.system-limit');

		// Export settings
		if ('<?php echo $this->action; ?>' == 'export' && <?php echo $this->item->csvi_template_id ?: 0; ?> > 0)
		{

		}
		// Import settings
		else if ('<?php echo $this->action; ?>' == 'import' && <?php echo ($this->item->csvi_template_id) ? $this->item->csvi_template_id : 0; ?> > 0)
		{
			// Hide/show the image fields
			Csvi.showFields(jQuery('#jform_process_image').val(), '.hidden-image #full_image #thumb_image #watermark_image');
		}
	});

	Joomla.submitbutton = function(task) {
		if (task == 'hidetips')
		{
			if (document.adminForm.task.value == 'hidetips')
			{
				jQuery('.help-block').hide();
				document.adminForm.task.value = '';
			}
			else
			{
				jQuery('.help-block').show();
				document.adminForm.task.value = 'hidetips';
			}

			return false;
		}
		else
		{
			if (document.formvalidator.isValid(document.id('adminForm')))
			{
				Joomla.submitform(task, document.getElementById('adminForm'));
			}
		}
	}
</script>

