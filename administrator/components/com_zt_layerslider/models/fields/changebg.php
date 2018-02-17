<?php
/**
 * @package ZT LayerSlider
 * @author    ZooTemplate.com
 * @copyright(C) 2015 - ZooTemplate.com
 * @license PHP files are GNU/GPL
 **/

// No direct access.
defined('_JEXEC') or die;


class JFormFieldChangebg extends JFormField
{

	protected $type = 'Changebg';


	protected static $initialised = false;


	protected function getInput()
	{

	    $asset = JFactory::getApplication()->input->get('option');

		if (!self::$initialised)
		{

			// Build the script.
			$script = array();

            //update setting input

			$script[] = '	function refreshValue() {';
			$script[] = '		var url = "'.JUri::root().'" + document.id("'.$this->id.'").value;';
			$script[] = '			if (url) {';
            $script[] = '				jQuery("#divLayers").css("background-image","url("+url+")");';
            $script[] = '				jQuery("#jform_image_url").val(url);';
			$script[] = '			}';
			$script[] = '	}';


			JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

			self::$initialised = true;
		}

		$html = array();

		// The text field.
		$html[] = '<div id="change_bgimage" class="input-prepend input-append">';
//
//        if ($this->value && file_exists(JPATH_ROOT . '/' . $this->value))
//        {
//            $src = JURI::root() . $this->value;
//        }
//        else
//        {
//            $src = '';
//        }


		$html[] = '	<input type="hidden" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
			. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' . ' />';

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
        $version = new JVersion();
        if (version_compare($version->getShortVersion(), '3.0.0', '<')) {
            JHtml::_('behavior.tooltip');
        } else {
            JHtml::_('bootstrap.tooltip');
        }
        $html[] = '<a class="modal btn botton" title="' . JText::_('JLIB_FORM_BUTTON_SELECT') . '"' . ' href="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=' . $asset . '&amp;fieldid=' . $this->id . '&amp;folder=' . $folder . '"'
            . ' rel="{handler: \'iframe\', size: {x: 800, y: 500}, onClose: refreshValue}">';
        $html[] = JText::_('Change Background Image') . '</a>';


		$html[] = '</div>';

		return implode("\n", $html);
	}
}
