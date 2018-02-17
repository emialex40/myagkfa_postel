<?php
/**
 * @package ZT LayerSlider
 * @author    ZooTemplate.com
 * @copyright(C) 2015 - ZooTemplate.com
 * @license PHP files are GNU/GPL
 **/
// no direct access
defined('_JEXEC') or die;

abstract class JHtmlZT_LayerSlider
{
    protected static $loaded = array();


    public static function jquery($noConflict = true, $debug = null)
    {
        // Only load once
        if (!empty(self::$loaded[__METHOD__]))
        {
            return;
        }

        // If no debugging value is set, use the configuration setting
        if ($debug === null)
        {
            $config = JFactory::getConfig();
            $debug  = (boolean) $config->get('debug');
        }

        JHtml::_('script', 'layerslider/jquery.min.js', false, true, false, false, $debug);

        // Check if we are loading in noConflict
        if ($noConflict)
        {
            JHtml::_('script', 'layerslider/jquery-noconflict.js', false, true, false, false, false);
        }

        self::$loaded[__METHOD__] = true;

        return;
    }


}