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

/**
 * Class exists checking
 */
if (!class_exists('plgSystemZt_onepage_checkout'))
{

    /**
     * Zoo Framework entrypoint plugin
     */
    class plgSystemZt_onepage_checkout extends JPlugin
    {

        public function __construct($subject, array $config)
        {
            $lang = JFactory::getLanguage();
            $extension = 'plg_system_zt_onepage_checkout';
            $base_dir = JPATH_SITE;
            $language_tag = 'en-GB';
            $reload = true;
            $lang->load($extension, JPATH_ADMINISTRATOR);
            $lang->load($extension, __DIR__.'/../');
            parent::__construct($subject, $config);
        }

        public function onAfterInitialise() {
            if (!defined('FOF_INCLUDED'))
            {
                include_once JPATH_ROOT . '/libraries/include.php';
            }

            $this->loadLibrariesFiles();

            //reg autoloadZtPsr2 to _autoload
            spl_autoload_register(array($this, 'autoloadZtPsr2'));

//            $this->loadAssets();
            if (JFactory::getApplication()->isSite()){
                ZtonepageAssets::getInstance()->loadVendor('font-awesome', array(
                    'css/font-awesome.css'
                ));
            }
        }

        /**
         * We'll catch before request dispatched to component
         * @link https://docs.joomla.org/Plugin/Events/System#onAfterRoute
         */
        public function onAfterRoute()
        {
            // Asked for Zt Framework
            // Register this plugin
            ZtonepageFramework::registerExtension(__DIR__ . '/extension.json');
            // Check to hook into Virtuemart Cartpage
            if (ZtonepageHelperVirtuemart::isVirtuemart())
            {
                if ($this->params->get('assets_bs3', 1))
                {
                    ZtonepageAssets::getInstance()->loadVendor('bootstrap', array(
                        'css/bootstrap.css',
                        'js/bootstrap.js'
                    ));
                }

                // Init this plugin
                $extension = ZtonepageFramework::getExtension('Ztonepage');
                $extension->init();
                // Init virtuemart
                ZtonepageHelperVirtuemart::initVirtueMart();
                // Override with our custom view
                ZtonepageHelperVirtuemart::overrideView();
                //
                ZtonepageHelperVirtuemart::is3rdAround();
            }
        }

        public function onAfterDispatch() {
            $input = JFactory::getApplication()->input;
            $ztCommand = $input->getCmd('zt_cmd');
            if ($ztCommand)
            {
                switch ($ztCommand)
                {
                    case 'ajax':
                        $class = $input->get('zt_namespace') . 'HelperAjax';
                        $task = $input->get('zt_task');
                        if (class_exists($class))
                        {
                            call_user_func(array($class, $task));
                            $ajax = ZtonepageHelperAjax::getInstance();
                            $ajax->response();
                        }
                        break;
                }
            }
        }

        /**
         *
         * @param string $className
         */
        public static function autoloadZtPsr2($className)
        {
            if (substr($className, 0, 1) != 'J' && substr($className, 0, 3) != 'FOF')
            {
                $path = ZtonepagePath::getInstance();
                $filePath = $path->getPathByClassname($className);
                if ($filePath)
                {
                    return require_once $filePath;
                }
            }
            return false;
        }

        /*
         * load necessary files to start
         */
        public function loadLibrariesFiles() {
            require_once __DIR__ . '/includes/path.php';
            require_once __DIR__ . '/assets/assets.php';
            require_once __DIR__ . '/includes/extensions.php';
            require_once __DIR__ . '/includes/framework.php';
            require_once __DIR__ . '/includes/html.php';

            if (JFactory::getApplication()->input->get('zt_debug') == 1) {
                ZtonepageFramework::restart();
            }
            /* Register Zt autoloading by Psr2 */
            ZtonepageFramework::registerExtension(__DIR__ . '/extension.json');

            /* Include JS Framework core */
            JHtml::_('bootstrap.framework');

            /* Extension init */
            $extension = ZtonepageFramework::getExtension('Ztonepage');
            $extension->init();

        }

    }

}
