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

require_once __DIR__ . '/helper.php';

// Autoloading
JLoader::discover('ZtNews', __DIR__ . '/libraries');
JLoader::discover('ZtNewsSource', __DIR__ . '/libraries/source');
JLoader::discover('ZtNewsHelper', __DIR__ . '/helper');

// @todo Should be load default assets . For each template will load inside template
$doc = JFactory::getDocument();
$doc->addStyleSheet(JUri::root() . 'modules/mod_zt_news/assets/css/default.css'); 