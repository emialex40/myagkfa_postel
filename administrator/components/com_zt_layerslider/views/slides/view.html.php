<?php
/**
 * @package ZT LayerSlider
 * @author    ZooTemplate.com
 * @copyright(C) 2015 - ZooTemplate.com
 * @license PHP files are GNU/GPL
 **/

// No direct access.
defined('_JEXEC') or die;

class ZT_LayersliderViewSlides extends JViewLegacy
{

    protected $items;
    protected $pagination;
    protected $state;

    public function display ( $tpl = null )
    {

        $version = new JVersion;
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state      = $this->get('State');

        // check for errors
        if ( count($errors = $this->get('Errors')) ) {
            JError::raiseError(500, implode("\n", $errors));

            return false;
        }

        $this->addToolbar();
        parent::display($tpl);
    }


    protected function addToolbar ()
    {
        $canDo = ZTSliderFunctions::getActions();

        JToolBarHelper::title(JText::_('COM_ZT_LAYERSLIDER_SLIDER_MANAGER'), 'slider.png');

        JToolbarHelper::help('JHELP_ZT_SLIDERS_MANAGER');

        if ( $canDo->get('core.admin') ) {
            JToolBarHelper::preferences('com_zt_layerslider',$height = '550', $width = '875', $alt = 'COM_ZT_LAYERSLIDER_GLOBAL_CONFIGURATION');
            JToolBarHelper::divider();
        }
    }

    protected function getSortFields()
    {
        return array(
            'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
            'a.state' => JText::_('JSTATUS'),
            'a.title' => JText::_('JGLOBAL_TITLE'),
            'access_level' => JText::_('JGRID_HEADING_ACCESS'),
            'language' => JText::_('JGRID_HEADING_LANGUAGE'),
            'a.id' => JText::_('JGRID_HEADING_ID')
        );
    }
}