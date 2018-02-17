<?php

/**
 * Zt (http://www.zootemplate.com/zo2)
 * A powerful Joomla template framework
 *
 * @link        http://www.zootemplate.com/zo2
 * @link        https://github.com/cleversoft/zo2
 * @author      ZooTemplate <http://zootemplate.com>
 * @copyright   Copyright (c) 2014 CleverSoft (http://cleversoft.co/)
 * @license     GPL v2
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Class exists checking
 */
if (!class_exists('ZtonepageAssets'))
{

    /**
     * @uses This class used for managed ALL asset stuffs
     * @rule
     * All asset stuffs must be save under <core>|<template>/assets directory
     */
    class ZtonepageAssets extends JObject
    {

        /**
         * Singleton instance
         * @var ZtonepageAssets
         */
        public static $instance;

        /**
         * Get instance of ZtonepageAssets
         * @return \ZtonepageAssets
         */
        public static function getInstance()
        {
            if (!isset(self::$instance))
            {
                self::$instance = new ZtonepageAssets();
            }
            if (isset(self::$instance))
            {
                return self::$instance;
            }
        }

        /**
         * Get asset file with relative path
         * @param string $key File location    
         * @return boolean|string
         */
        public function getAssetUrl($key)
        {
            return ZtonepagePath::getInstance()->getUrl($key);
        }

        /**
         *
         * @param type $file
         * @return \ZtonepageAssets
         */
        public function addStyleSheet($key)
        {

            $assetFile = $this->getAssetUrl($key);


            if ($assetFile)
            {
                $doc = JFactory::getDocument();
                $doc->addStyleSheet($assetFile);
            }
        }

        /**
         * @param type $file
         *
         * @return \ZtonepageAssets
         */
        public function addScript($key)
        {
            $assetFile = $this->getAssetUrl($key);

            if ($assetFile)
            {
                $doc = JFactory::getDocument();
                $doc->addScript($assetFile);
            }
        }

        public function addScriptDeclaration($script)
        {
            $doc = JFactory::getDocument();
            $doc->addScriptDeclaration($script);
        }

        public static function import($assets = array())
        {
            foreach ($assets as $asset)
            {
                $ext = JFile::getExt($asset);
                if ($ext == 'css')
                {
                    self::getInstance()->addStyleSheet($asset);
                } else
                {
                    self::getInstance()->addScript($asset);
                }
            }
        }

        public static function loadVendor($name, $files = array())
        {
            foreach ($files as $file)
            {
                // Create key from vendor name & files list
                $key = 'Ztonepage://assets/vendor/' . $name . '/' . $file;
                $ext = JFile::getExt($key);
                if ($ext == 'css')
                {
                    self::getInstance()->addStyleSheet($key);
                } else
                {
                    self::getInstance()->addScript($key);
                }
            }
        }

        /*
        * load assset
        */

        public function init() {
            static $inited = false;
            if ($inited == false) {
                /* Import Zt Assets
                */
                ZtonepageAssets::import(array(
                    'Ztonepage://assets/css/zt.core.css',
                    'Ztonepage://assets/js/zt.core.php',
                    'Ztonepage://assets/js/zt.ui.js',
                    'Ztonepage://assets/js/zt.ajax.js',
                    'Ztonepage://assets/js/zt.joomla.js',
                    'Ztonepage://assets/vendor/form-validator/theme-default.css',
                    'Ztonepage://assets/vendor/form-validator/jquery.form-validator.js',
                    'Ztonepage://assets/js/styles.css'
                ));
                $inited = true;
            }
        }

    }

}    
