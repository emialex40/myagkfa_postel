<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_yandex_map
 *
 * @copyright   Copyright (C) 2015 Artem Yegorov. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the weblinks functions only once
require_once __DIR__ . '/helper.php';

$fields = ModYandexmapHelper::getParams($module->id);
$document	= JFactory::getDocument();
$lang = JFactory::getLanguage();

$document->addScript("https://api-maps.yandex.ru/2.1/?lang=ru_RU");
$document->addStyleSheet("modules/mod_yandex_map/css/map.css");

require JModuleHelper::getLayoutPath('mod_yandex_map', $params->get('layout', 'default'));
