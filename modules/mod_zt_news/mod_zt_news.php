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

jimport( 'joomla.application.module.helper' );

// Rerequire bootstrap file for init
require_once(dirname(__FILE__) . '/bootstrap.php');

// Variables
$showTitle = $params->get('show_title', 1);
$showCreated = $params->get('show_date', 1);
$showIntro = $params->get('show_intro', 1);
$showReadmore = $params->get('show_readmore', 0);
$showIntroList = $params->get('show_intro_list', 1);
$numberIntroItems = (int) $params->get('number_intro_items', 1);
$numberLinkItems = $params->get('number_link_items', 4);
$thumbMainWidth = $params->get('thumb_main_width');
$thumbMainHeight = $params->get('thumb_main_height');
$thumbListWidth = $params->get('thumb_list_width');
$thumbListHeight = $params->get('thumb_list_height');
$showTitleCategory = $params->get('showtitlecat', 1);
$showCreatedOnList = $params->get('show_date_list', 1);
$showTitleOnList = $params->get('show_title_list', 1);
$clearCache = $params->get('clear_cache', 0);
$isImage = $params->get('is_image', 1);
$showImageOnList = $params->get('is_image_list', 1);
$showSubCategory = $params->get('is_subcat', 1);
$breakpoint = $params->get('breakpoint');
$columns = $params->get('columns');
$intro_legnth= $params->get('intro_length');

// Source
$source = $params->get('source');

// Render
$templateType = $params->get('template_type');

// Get items
$items = modZTNewsHelper::getItems($params);

// Should we also cache rendered layout
require JModuleHelper::getLayoutPath('mod_zt_news', $params->get('template_type') . '/default');