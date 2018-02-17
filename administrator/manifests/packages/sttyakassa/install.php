<?php
defined ( '_JEXEC' ) or die ();

class Pkg_sttyakassaInstallerScript {

	public function install($parent) {
		return true;
	}

	public function discover_install($parent) {
		return self::install($parent);
	}

	public function update($parent) {
		return self::install($parent);
	}

	public function uninstall($parent) {
		return true;
	}

	public function preflight($type, $parent) {
//		$manifest = $parent->getParent()->getManifest();
		return true;
	}

	public function makeRoute($uri) {
		return JRoute::_($uri, false);
	}

	public function postflight($type, $parent) {
		$cache = JFactory::getCache();
		$cache->clean('_system');

		// Remove all compiled files from APC cache.
		if (function_exists('apc_clear_cache')) {
			@apc_clear_cache();
		}
		if($type=='update' || $type=="install") {
			if(empty($this->_db)){
				$this->_db = JFactory::getDBO();
			}
			$query="CREATE TABLE IF NOT EXISTS `#__stt_active` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `option` varchar(64) NOT NULL DEFAULT '',
  `order` varchar(255) NOT NULL DEFAULT '',
  `domen` varchar(1000) NOT NULL DEFAULT '',
  `activecode` varchar(4000) NOT NULL DEFAULT '',
  `md5` varchar(255) NOT NULL DEFAULT '',
  `created` datetime, 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
			$this->_db->setQuery($query);
			$this->_db->query();
			require_once JPATH_ROOT.'/libraries/stt/sttlh.php';
			$res = sttlicensehelper::getActive('pkg_sttyakassa', sttlicensehelper::getOrdernumFromManifest($parent->get('manifest')));
			if($res!='ok') {
				throw new RuntimeException($res);
			}
		} 
		if ($type == 'uninstall') return true;

		$this->enablePlugin('vmpayment', 'sttyakassa'); // прописано в plugin= в манифесте: <filename plugin="sttyakassa">sttyakassa.php</filename>
		return true;
	}

	function enablePlugin($group, $element) {
		$plugin = JTable::getInstance('extension');
		if (!$plugin->load(array('type'=>'plugin', 'folder'=>$group, 'element'=>$element))) {
			return false;
		}
		$plugin->enabled = 1;
		return $plugin->store();
	}
}
