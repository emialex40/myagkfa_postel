<?php
/**
 * @package ZT LayerSlider
 * @author    ZooTemplate.com
 * @copyright(C) 2015 - ZooTemplate.com
 * @license PHP files are GNU/GPL
 **/
// No direct access.
defined('_JEXEC') or die;

class ZT_LayersliderController extends JControllerLegacy
{
    /**
     * @var		string	The default view.
     * @since	1.6
     */
    protected $default_view = 'sliders';

    /**
     * Method to display a view.
     *
     * @param	boolean			If true, the view output will be cached
     * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return	JController		This object to support chaining.
     * @since	1.5
     */
    public function display($cachable = false, $urlparams = false)
    {
        // Load the submenu.
        $version = new JVersion();
        $app = JFactory::getApplication();
        if (version_compare($version->getShortVersion(), '3.0.0', '<')) {
            ZTSliderFunctions::addSubmenu($app->input->getCmd('view', 'sliders'));
        }

        parent::display();

        return $this;
    }
}