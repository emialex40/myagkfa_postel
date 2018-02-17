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

?>
<ul class="nav nav-list">
	<?php
	for ($i = 1; $i < 6; $i++)
	{
		if ($this->step == $i)
		{
			$active = 'active-step';
			$arrow = '<i class="icon-chevron-right"></i>';
		}
		else
		{
			$active = 'inactive-step';
			$arrow = '';
		}

		?>
		<li class="<?php echo $active; ?>">
			<?php echo $arrow; echo JText::_('COM_CSVI_TEMPLATE_STEP' . $i); ?>
		</li>
		<?php
	}

	?>
</ul>
