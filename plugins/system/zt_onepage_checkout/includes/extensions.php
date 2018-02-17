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
if (!class_exists('ZtonepageExtensions')) {

    class ZtonepageExtensions {

        /**
         * Singleton instance
         * @var ZtExtension
         */
        public static $instance;

        /**
         *
         * @var array
         */
        private $_extensions;

        /**
         * Get instance of ZtExtension
         * @return \ZtExtension
         */
        public static function getInstance() {
            if (!isset(self::$instance)) {
                self::$instance = new ZtonepageExtensions();
            }
            if (isset(self::$instance)) {
                return self::$instance;
            }
        }

        /**
         * 
         * @param type $namespace
         * @return boolean
         */
        public function get($namespace) {
            if (empty($this->_extensions[$namespace])) {
                return false;
            } else {
                return $this->_extensions[$namespace];
            }
        }

        /**
         * 
         * @param type $name
         * @param type $extension
         */
        public function set($extension) {
            $this->_extensions[$extension->namespace] = $extension;
        }

        /**
         * 
         * @param type $name
         * @param type $namespace
         * @param boolean $isAdmin
         */
        public function register($extension) {
            $this->_registerExtension($extension);
            $this->_registerPaths($extension);
            $this->flush();
        }

        /**
         * 
         * @param type $name
         * @param type $namespace
         * @param type $isAdmin
         */
        private function _registerExtension($extension) {

            $this->set($extension);
        }

        private function _registerPaths($extension) {

            if ($extension->admin == 1) {
                $path['root'][] = JPATH_ADMINISTRATOR;
            } else {
                $path['root'][] = JPATH_SITE;
            }

            switch ($extension->extension->type) {
                case 'module':
                    $path['root'] = 'modules' . DIRECTORY_SEPARATOR . 'mod_' . $extension->extension->name;
                    $path['template'][] = 'mod_' . $extension->extension->name;
                    break;
                case 'plugin':
                    $path['root'][] = 'plugins' . DIRECTORY_SEPARATOR . $extension->extension->group . DIRECTORY_SEPARATOR . $extension->extension->name;
                    $path['template'][] = 'plg_' . $extension->extension->group . '_' . $extension->extension->name;
                    break;
                case 'components':
                    $path['root'][] = 'components' . DIRECTORY_SEPARATOR . $extension->extension->name;
                    $path['template'][] = 'com_' . $extension->extension->name;
                    break;
            }

            $paths[] = implode(DIRECTORY_SEPARATOR, $path['root']);
            $paths[] = implode(DIRECTORY_SEPARATOR, $path['root']) . DIRECTORY_SEPARATOR . 'local';
            $paths[] = implode(DIRECTORY_SEPARATOR, $path['root']) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . JFactory::getApplication()->getTemplate() . DIRECTORY_SEPARATOR . 'html';
            foreach ($paths as $path) {

                $registeredPaths[] = $path;
            }
            $extension->paths = $registeredPaths;
            $this->set($extension);
        }

        public function flush() {
            ZtonepageFramework::setSession('extensions', $this->_extensions);
        }

    }

}    
