<?php
defined('JPATH_BASE') or die;
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
class JFormFieldListIcons extends JFormFieldList{
	protected function getOptions(){
		$options = array();
		$path =  JPATH_ROOT.$this->getAttribute('path',DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_yandex_maps'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'map'.DIRECTORY_SEPARATOR.'tmpl');
		$dir = opendir($path);
		$icons = include JPATH_BASE.'/components/com_yandex_maps/helpers/images.php';
		foreach ($icons as $icon=>$image) {
			if ((preg_match('#cluster#i', $icon) and !$this->getAttribute('icon')) or (!preg_match('#cluster#i', $icon) and $this->getAttribute('icon'))) {
				$options[] = JHtml::_('select.option', $icon, $icon);
			}
		}
		return $options;
	}
	public function getAttribute($attr_name, $default = null){
		if (!empty($this->element[$attr_name])) {
			return $this->element[$attr_name];
		} else {
			return $default;
		}
	}
}
