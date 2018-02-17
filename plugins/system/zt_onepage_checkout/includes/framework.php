<?php

/**
 * {$id}
 */
defined('_JEXEC') or die('Restricted access');

if (!class_exists('ZtonepageFramework'))
{

    class ZtonepageFramework
    {

        //can be removed

        public static function getSession($name, $default = null)
        {
            $session = JFactory::getSession();
            return $session->get($name, $default, 'Ztonepage');
        }

        //can be removed
        public static function setSession($name, $value)
        {
            $session = JFactory::getSession();
            return $session->set($name, $value, 'Ztonepage');
        }


        public static function registerExtension($file)
        {
            if (JFile::exists($file))
            {
                $extension = json_decode(file_get_contents($file));
                ZtonepageExtensions::getInstance()->register($extension);
            }
        }

        public static function getExtension($namespace, $flush = false)
        {
            static $extensions;
            if (!isset($extensions[$namespace]) || $flush)
            {
                $className = $namespace . 'Assets';
                $extensions[$namespace] = new $className(ZtonepageExtensions::getInstance()->get($namespace));
            }
            return $extensions[$namespace];
        }

        public static function import($key)
        {
            $path = ZtonepagePath::getInstance();
            $filePath = $path->getPath($key);
            if ($filePath)
            {
                return require_once $filePath;
            }
            return false;
        }

        public static function addStyleSheet($key)
        {
            return ZtonepageAssets::getInstance()->addStyleSheet($key);
        }

        public static function isAjax()
        {
            $input = JFactory::getApplication()->input;
            if ($input->get('zt_cmd') == 'ajax')
            {
                return true;
            }
            return false;
        }

        public static function restart()
        {
            JFactory::getSession()->restart();
        }

    }

}
