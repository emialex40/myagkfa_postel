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

/**
 * Class exists checking
 */
if (!class_exists('modZTNewsHelper'))
{

    /**
     * Main helper class 
     */
    class modZTNewsHelper
    {

        /**
         * Get source object class
         * @staticvar className $sources
         * @param type $params
         * @return \className
         */
        public static function getSource($params)
        {
            $className = 'ZtNewsSource' . ucfirst($params->get('source'));
            return new $className($params);
        }

        /**
         * Get all items
         * @param type $params
         * @return type
         */
        public static function getItems($params, $groupByCategories = false)
        {
            $key = md5(serialize($params));
            $cache = JFactory::getCache('mod_zt_news', '');

            // Force caching but we also allow config it via params if exists
            $cache->setCaching($params->get('cache',1));

            //clear cache
            if ($params->get('clear_cache',0) and $cache->get($key))
            {
                $cache->clean();
            }

            // Get cached items
            $items = $cache->get($key);

            // If no cached yet than we execute to get items
            if (!$items)
            {
                $items = self::getSource($params)->getItems($groupByCategories);
                // Store it back
                $cache->store($items, $key);
            }
            return $items;
        }

        /**
         * Get all Category
         * @param type $params
         * @return type
         */
        public static function getCategories($params)
        {
            $key = md5(serialize($params));
            $cache = JFactory::getCache('mod_zt_news', '');

            // Force caching but we also allow config it via params if exists
            $cache->setCaching($params->get('cache',1));

            // Get cached items
            $categories = $cache->get($key);

            // If no cached yet than we execute to get categories
            if (!$categories)
            {
                $categories = self::getSource($params)->getCategories();
                // Store it back
                $cache->store($categories, $key);
            }
            return $categories;
        }

        /**
         * @param $src
         * @param $width
         * @param $height
         * @param $params
         * @return mixed|string
         */
        public static function getThumbnailLink($src, $width, $height, $params)
        {
            $src = JPATH_ROOT . '/' . $src;
            if (JFile::exists($src))
            {
                // @todo Should be autoload inside lib
                require_once __DIR__ . '/libraries/imager.php';
                require_once __DIR__ . '/libraries/imager/abstract.php';
                require_once __DIR__ . '/libraries/imager/gd.php';
                require_once __DIR__ . '/libraries/imager/sizer.php';

                $ext = JFile::getExt($src);
                $cacheFile = JPATH_ROOT . '/cache/' . $width . '_' . $height . '_' . md5($src) . '.' . $ext;

                //  @todo Check cache interval and allow cache clearing
                if (!JFile::exists($cacheFile))
                {
                    $imager = new ZtNewsImager('gd');
                    $imager->loadFile($src);
                    $method = $params->get('thumbnail_method', 'resize');
                    if ($method == 'crop')
                    {
                        $imager->crop($width, $height, array('position' => $params->get('thumbnail_crop_position', 'center')));
                    } else
                    {
                        $imager->$method($width, $height);
                    }

                    if ($imager->saveToFile($cacheFile))
                    {
                        return str_replace(JPATH_ROOT, rtrim(JUri::root(), '/'), $cacheFile);
                    }
                } else
                {
                    return str_replace(JPATH_ROOT, rtrim(JUri::root(), '/'), $cacheFile);
                }
            }
            return $src;
        }

    }

}
