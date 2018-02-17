<?php

/*
 * @package VirtueMart for Joomla
 * @subpackage payment
 * @author www.net2pay.ru
 * @copyright Copyright (C) 2017 www.net2pay.ru
 * @license GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

class netpayInstallerScript {
    private $minimum_php_release = '5.3.0';
    private $usekey = 0;
    private $install_extension = array();
    private $install_folders = array(
    	'plugins/vmpayment/netpay'
    );
    private $install_files = array(
    );
    private $old_folders = array(
    );
    private $old_files = array(
    );
    private $name;
    private $scriptfile;
    private $element;

    private function setVar($parent) {
        $manifest = $parent->get('manifest');
        $this->name = (string) $manifest->name;
        $this->scriptfile = (string) $manifest->scriptfile;
        $this->element = substr($this->scriptfile, 0, -4);
        $this->version = (string) $manifest->version;
    }

    private function updateDataBase($type = 'install') {
        $db = JFactory::getDbo();
        $db->setQuery("SELECT extension_id FROM `#__extensions` WHERE type='netpay'");
        $payment_id = $db->loadResult();
        if (!$payment_id && $type == 'install') {
	    	$query = <<< SQL
INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`) VALUES('Net Pay Payment', 'plugin', 'netpay', 'vmpayment', 0, 1, 1, 0, '{"legacy":true,"name":"Net Pay Payment","type":"plugin","creationDate":"May 2014","author":"Net Pay","copyright":"Net Pay","authorEmail":"","authorUrl":"http:\\/\\/net2pay.ru","version":"1.0.0","description":"\\u043f\\u043b\\u0430\\u0433\\u0438\\u043d \\u043f\\u043b\\u0430\\u0442\\u0451\\u0436\\u043d\\u043e\\u0439 \\u0441\\u0438\\u0441\\u0442\\u0435\\u043c\\u044b Net Pay","group":""}', '{}', '', '', 0, '0000-00-00 00:00:00', 0, 0);
SQL;
            $db->setQuery($query);
            $db->execute();
        } 
        elseif ($payment_id && $type == 'uninstall') {
            $query = "DELETE FROM `#__extensions` WHERE extension_id=" . $payment_id;
            $db->setQuery($query);
            $db->execute();
        }
    }

    function preflight($type, $parent) {
        $this->setVar($parent);
        $error = 0;
        $app = JFactory::getApplication();
        if (version_compare(phpversion(), $this->minimum_php_release, '<')) {
            $app->enqueueMessage($this->name . ' requires PHP ' . $this->minimum_php_release . ' or later version!', 'error');
            $error = 1;
        }
        if ($this->usekey && !extension_loaded('bcmath')) {
            $app->enqueueMessage($this->name . ' requires requires PHP BCMath extension!', 'error');
            $error = 1;
        }
        if ($error) {
            $app->enqueueMessage('The installation was canceled', 'error');
            return false;
        }
    }

    function install($parent) {
        
    }

    function update($parent) {
        
    }

    function postflight($type, $parent) {
        $installer = new JInstaller;
        $install_folder = JPATH_ROOT . '/tmp/' . $this->element;
        foreach ($this->install_extension as $extension) {
            if ($extension['type'] == 'plugin') {
                $folder = 'plugins/' . $extension['folder'] . '/' . $extension['element'];
            } else {
                $folder = 'modules/' . $extension['element'];
            }
            if ($extension['checkversion'] && file_exists(JPATH_ROOT . '/' . $folder . '/' . $extension['element'] . '.xml')) {
                $oldXML = JFactory::getXML(JPATH_ROOT . '/' . $folder . '/' . $extension['element'] . '.xml');
                $xml = JFactory::getXML($install_folder . '/' . $folder . '/' . $extension['element'] . '.xml');
                if (version_compare(trim($xml->version), trim($oldXML->version), '<')) {
                    continue;
                }
            }
            $installer->install($install_folder . '/' . $folder);
            if ($extension['enabled']) {
                $t_extension = JTable::getInstance('Extension');
                $extension_id = $t_extension->find(array('type' => $extension['type'], 'element' => $extension['element'], 'folder' => $extension['folder']));
                if ($extension_id) {
                    $t_extension->load($extension_id);
                    $t_extension->enabled = 1;
                    $t_extension->store();
                }
            }
        }
        if (file_exists($install_folder)) {
            @JFolder::delete($install_folder);
        }

        $extension_root = $parent->getParent()->getPath('extension_root');
        $extension_source = $parent->getParent()->getPath('source');
        @JFile::copy($extension_source . '/' . $this->scriptfile, $extension_root . '/' . $this->scriptfile);

        $this->updateDataBase('install');

        foreach ($this->old_folders as $folder) {
            if (file_exists(JPATH_ROOT . '/' . $folder)) {
                @JFolder::delete(JPATH_ROOT . '/' . $folder);
            }
        }

        foreach ($this->old_files as $file) {
            if (file_exists(JPATH_ROOT . '/' . $file)) {
                @JFile::delete(JPATH_ROOT . '/' . $file);
            }
        }

        $manifest = $parent->getParent()->getManifest();
        $parent->getParent()->setRedirectURL('index.php?option=com_virtuemart&controller=addons');
    }

    function uninstall($parent) {
        $this->setVar($parent);
        $installer = new JInstaller;
        foreach ($this->install_extension as $extension) {
            $extension_id = JTable::getInstance('Extension')->find(array('type' => $extension['type'], 'element' => $extension['element'], 'folder' => $extension['folder']));
            if ($extension_id) {
                $installer->uninstall($extension['type'], $extension_id);
            }
        }

        foreach ($this->install_folders as $folder) {
            if (file_exists(JPATH_ROOT . '/' . $folder)) {
                @JFolder::delete(JPATH_ROOT . '/' . $folder);
            }
        }

        foreach ($this->install_files as $file) {
            if (file_exists(JPATH_ROOT . '/' . $file)) {
                @JFile::delete(JPATH_ROOT . '/' . $file);
            }
        }

        $this->updateDataBase('uninstall');

        if (file_exists($parent->getParent()->getPath('extension_root') . '/' . $this->scriptfile)) {
            @JFile::delete($parent->getParent()->getPath('extension_root') . '/' . $this->scriptfile);
        }
    }

}

if (JFactory::getApplication()->input->getCmd('option') == 'com_virtuemart') {
    $extension_id = JTable::getInstance('Extension')->find(array('type' => 'file', 'element' => $row->alias));
    if ($extension_id) {
        JInstaller::getInstance()->uninstall('file', $extension_id);
    }
}
