<?php
JHtml::addIncludePath(JPATH_ROOT.'/components/com_yandex_maps/helpers');
JHtml::stylesheet(JURI::root().'media/com_yandex_maps/js/kladr/jquery.kladr.min.css');
JHtml::stylesheet(JURI::root().'media/com_yandex_maps/js/datetimepicker/jquery.datetimepicker.css');
JHtml::stylesheet(JURI::root().'media/com_yandex_maps/js/dialog/jquery.dialog.css');
JHtml::stylesheet(JURI::root().'media/com_yandex_maps/css/registration.css');
// форма ввода с кладру
JHtml::script(JURI::root().'media/com_yandex_maps/js/kladr/jquery.kladr.min.js');
// всплывашки
JHtml::script(JURI::root().'media/com_yandex_maps/js/dialog/jquery.dialog.js');
// датапикер для формы ввода даты
JHtml::script(JURI::root().'media/com_yandex_maps/js/datetimepicker/jquery.datetimepicker.js');
// маска для телефонов и дат
JHtml::script(JURI::root().'media/com_yandex_maps/js/maskedinput/jquery.mask.min.js');
// сохранение данных формы в браузере
JHtml::script(JURI::root().'media/com_yandex_maps/js/registration.js');
$organization = $this->item;
$params = new JRegistry();
$params->merge(JComponentHelper::getParams('com_yandex_maps'));
$params->loadString(json_encode($this->item->params));
$this->params = $params;
?><div id="xdsoft_registration_organization_save">
<?php  include 'default_organization.php'; ?>
</div>