<?php
/**
 * @package ZT LayerSlider
 * @author    ZooTemplate.com
 * @copyright(C) 2015 - ZooTemplate.com
 * @license PHP files are GNU/GPL
 **/

// No direct access.
defined('_JEXEC') or die;


class JFormFieldImagesource extends JFormField
{

	protected $type = 'Imagesource';


	protected static $initialised = false;


	protected function getInput()
	{

	    $asset = JFactory::getApplication()->input->get('option');


		$html = array();

		$html[] = '';

		$directory = (string) $this->element['directory'];

		if ($this->value && file_exists(JPATH_ROOT . '/' . $this->value))
		{
			$folder = explode('/', $this->value);
			$folder = array_diff_assoc($folder, explode('/', JComponentHelper::getParams('com_media')->get('image_path', 'images')));
			array_pop($folder);
			$folder = implode('/', $folder);
		}
		elseif (file_exists(JPATH_ROOT . '/' . JComponentHelper::getParams('com_media')->get('image_path', 'images') . '/' . $directory))
		{
			$folder = $directory;
		}
		else
		{
			$folder = '';
		}

        JHtml::_('bootstrap.tooltip');

        $html[] = '<a id="button_change_image_source" class="modal btn disabled" style="display: none" title="' . JText::_('Change Image') . '"' . ' href="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=' . $asset . '&amp;fieldid=' . $this->id . '&amp;folder=' . $folder . '"'
            . ' rel="{handler: \'iframe\', size: {x: 800, y: 500}, onClose: UniteLayersRev.changeImageSource}">';
        $html[] = JText::_('Change Background Image') . '</a>';
        $html[] = '<input type="hidden" name="image_source_temp" value=""/>';

		return implode("\n", $html);
	}
}
