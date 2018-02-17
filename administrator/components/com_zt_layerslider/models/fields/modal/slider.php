<?php
/**
 * @package ZT LayerSlider
 * @author    ZooTemplate.com
 * @copyright(C) 2015 - ZooTemplate.com
 * @license PHP files are GNU/GPL
 **/

// No direct access.
defined('_JEXEC') or die;

class JFormFieldModal_Slider extends JFormField
{

    protected $type = 'Modal_Slider';


    protected function getInput ()
    {

        JHtml::_('behavior.modal', 'a.modal');

        // Build the script.
        $script   = array();
        $script[] = '	function jSelectSlider_' . $this->id . '(id, title) {';
        $script[] = '		document.id("' . $this->id . '_id").value = id;';
        $script[] = '		document.id("' . $this->id . '_name").value = title;';
        $script[] = '		SqueezeBox.close();';
        $script[] = '	}';

        // Add the script to the document head.
        JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));


        // Setup variables for display.
        $html = array();
        $link = 'index.php?option=com_zt_layerslider&amp;view=sliders&amp;layout=modal&amp;tmpl=component&amp;function=jSelectSlider_' . $this->id;

        $db = JFactory::getDBO();
        $db->setQuery('SELECT title' . ' FROM #__zt_layerslider' . ' WHERE id = ' . (int)$this->value);
        $title = $db->loadResult();

        if ( $error = $db->getErrorMsg() ) {
            JError::raiseWarning(500, $error);
        }

        if (empty($title))
        {
            $title = JText::_('COM_ZT_LAYERSLIDER_SELECT_AN_SLIDER');
        }
        $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

        // The current user display field.
        $html[] = '<span class="input-append">';
        $html[] = '<input type="text" class="input-medium" id="'.$this->id.'_name" value="'.$title.'" disabled="disabled" size="35" /><a class="modal btn" title="'.JText::_('COM_CONTENT_CHANGE_ARTICLE').'"  href="'.$link.'&amp;'.JSession::getFormToken().'=1" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-file"></i> '.JText::_('JSELECT').'</a>';
        $html[] = '</span>';

        // The active article id field.
        if (0 == (int) $this->value)
        {
            $value = '';
        }
        else
        {
            $value = (int) $this->value;
        }

        // class='required' for client side validation
        $class = '';
        if ($this->required)
        {
            $class = ' class="required modal-value"';
        }

        $html[] = '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';

        return implode("\n", $html);

    }
}