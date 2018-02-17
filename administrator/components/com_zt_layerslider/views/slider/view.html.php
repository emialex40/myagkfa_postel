<?php
/**
 * @package ZT LayerSlider
 * @author    ZooTemplate.com
 * @copyright(C) 2015 - ZooTemplate.com
 * @license PHP files are GNU/GPL
 **/
defined('_JEXEC') or die;


class ZT_LayersliderViewSlider extends JViewLegacy
{
//    protected $form;
    protected $item;
    protected $state;
    protected $canDo;

    /**
     * Display the view
     */
    public function display($tpl = null)
    {

        $version = new JVersion;
        // Initialiase variables.
//        $this->form		= $this->get('Form');
        $this->item		= $this->get('Item');
        $this->state	= $this->get('State');
        $this->canDo	= ZTSliderFunctions::getActions($this->state->get('slider.id'));

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        $this->addToolbar();

        parent::display($tpl);
    }


    protected function addToolbar()
    {
        $canDo = ZTSliderFunctions::getActions();

        JToolBarHelper::title(JText::_('COM_ZT_LAYERSLIDER_SLIDER_MANAGER'), 'slider.png');

        JToolbarHelper::help('JHELP_ZT_SLIDERS_MANAGER');

        if ( $canDo->get('core.admin') ) {
            JToolBarHelper::preferences('com_zt_layerslider',$height = '550', $width = '875', $alt = 'COM_ZT_LAYERSLIDER_GLOBAL_CONFIGURATION');
            JToolBarHelper::divider();
        }


    }
}
