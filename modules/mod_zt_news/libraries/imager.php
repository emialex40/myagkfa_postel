<?php

/**
 * ZT News
 * 
 * @package     Joomla
 * @subpackage  Module
 * @version     2.0.0
 * @author      ZooTemplate 
 * @email       support@zootemplate.com 
 * @link        http://www.zootemplate.com 
 * @copyright   Copyright (c) 2015 ZooTemplate
 * @license     GPL v2
 */
defined('_JEXEC') or die('Restricted access');
/**
 * Class exists checking
 */
if (!class_exists('ZtNewsImager'))
{

    /**
     * Image process class
     */
    class ZtNewsImager
    {

        /**
         *
         * @var object 
         */
        private $_adapter;

        /**
         * 
         * @param string $adapterName
         */
        public function __construct($adapterName)
        {
            $this->_adapter = $this->_createAdapter($adapterName);
        }

        /**
         * Get adapter
         * @param type $adapterName
         * @return \ZtNewsImagerImagick|\ZtNewsImagerGd
         */
        private function _createAdapter($adapterName)
        {
            switch ($adapterName)
            {
                case 'gd':
                    return new ZtNewsImagerGd ();
                case 'imagick':
                    return new ZtNewsImagerImagick ();
                default :
                    return new ZtNewsImagerGd ();
            }
        }

        /**
         * Execute adapter method
         * @param type $name
         * @param type $arguments
         * @return type
         */
        public function __call($name, $arguments)
        {
            if (method_exists($this->_adapter, $name))
            {
                return call_user_func_array(array($this->_adapter, $name), $arguments);
            }
        }

    }

}