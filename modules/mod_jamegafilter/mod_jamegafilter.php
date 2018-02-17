<?php

/**
 * ------------------------------------------------------------------------
 * JA Megafilter Module
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */
defined('_JEXEC') or die;

JLoader::register('JaMegafilterHelper', JPATH_ADMINISTRATOR . '/components/com_jamegafilter/helper.php');
require_once __DIR__ . '/helper.php';
$helper = new ModJamegafilterHelper();
$helper->display($params);
