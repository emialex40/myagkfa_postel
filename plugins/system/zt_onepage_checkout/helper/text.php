<?php

/**
 * One Page Checkout For VirtueMart
 *
 * @version     1.0.2
 * @link        http://www.zootemplate.com
 * @author      ZooTemplate
 * @copyright   Copyright (c) 2015 CleverSoft (http://cleversoft.co/)
 * @license     GPL v2
 */

defined('_JEXEC') or die('Restricted access');

if (!class_exists('ZtonepageHelperText'))
{

    class ZtonepageHelperText
    {

        public static function _($text)
        {
            $extension = ZtonepageFramework::getExtension('Ztonepage');
            $text = $extension->text . $text;
            return JText::_($text);
        }

    }
}
