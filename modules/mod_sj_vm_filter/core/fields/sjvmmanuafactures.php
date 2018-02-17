<?php
/**
 * @package Sj Filter for VirtueMart
 * @version 3.0.1
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2015 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */

defined('_JEXEC') or die;
JFormHelper::loadFieldClass('list');

if (!class_exists('JFormFieldSjVmManuafactures')) {

	class JFormFieldSjVmManuafactures extends JFormFieldList
	{
		protected $categories = null;

		public function getInput()
		{
			if ($this->vm_require()) {
				$categories = $this->getManuafactures();
				if (!count($categories)) {
					$input = '<div style="margin: 5px 0;float: left;font-size: 1.091em;">You have no category to select.</div>';
				} else {
					$input = parent::getInput();
				}
			} else {
				$input = '<div style="margin: 5px 0;float: left;font-size: 1.091em;">Maybe your component (Virtuemart) has been installed incorrectly. <br/>Please sure your component work properly. <br/>If you still get errors, please contact us via our <a href="http://www.smartaddons.com/forum/" target="_blank">forum</a> or <a href="http://www.smartaddons.com/tickets/" target="_blank">ticket system</a></div>';
			}
			return $input;
		}

		protected function vm_require()
		{
			if (!class_exists('VmConfig')) {
				if (file_exists(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php')) {
					require JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php';
				} else {
					$this->error = 'Could not find VmConfig helper';
					return false;
				}
			}
			if (!class_exists('VmModel')) {
				if (defined('JPATH_VM_ADMINISTRATOR') && file_exists(JPATH_VM_ADMINISTRATOR . '/helpers/vmmodel.php')) {
					require JPATH_VM_ADMINISTRATOR . '/helpers/vmmodel.php';
				} else {
					$this->error = 'Could not find VmModel helper';
					return false;
				}
			}
			if (defined('JPATH_VM_ADMINISTRATOR')) {
				JTable::addIncludePath(JPATH_VM_ADMINISTRATOR . '/tables');
			}
			return true;
		}

		protected function getManuafactures()
		{
			if (is_null($this->manuafactures)) {
				$manufacturersModel = VmModel::getModel ('Manufacturer');
				$manufacturers = $manufacturersModel->getManufacturers (true,true,true);
				$this->manuafactures = $manufacturers;
			}
			return $this->manuafactures;
		}

		public function getOptions()
		{
			$options = parent::getOptions();

			// sorted categories
			$manuafactures = $this->getManuafactures();
			if (count($manuafactures)) {
				foreach ($manuafactures as $manuafacture) {
					$value 	= $manuafacture->virtuemart_manufacturer_id;
					$text 	= $manuafacture->mf_name;
					$options[] = JHtml::_('select.option', $value, $text);
				}
			}
			return $options;
		}

	}
}