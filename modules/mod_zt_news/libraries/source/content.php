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
 * Make sure this class is not declared
 */
if (!class_exists('ZtNewsSourceContent'))
{
    // com_content route
    if (is_file(JPATH_SITE . '/components/com_content/helpers/route.php'))
    {
        require_once(JPATH_SITE . '/components/com_content/helpers/route.php');
    }

    /**
     * Joomla content source
     */
    class ZtNewsSourceContent extends ZtNewsSourceAbstract
    {

        protected $_source = 'content';
        protected $_table_items = '#__content';
        protected $_table_categories = '#__categories';

        /**
         * Basic required WHERE
         * @return string
         */
        protected function _buildWhere()
        {
            return ' `a`.`state` = 1';
        }

        /**
         * 
         * @param object $item
         * @return object
         */
        protected function _prepareItem($item)
        {
            $item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
            $item->introtext = JHtml::_('string.truncate', $item->introtext, $this->_params->get('intro_length', 200));
            $item->cat_link = JRoute::_(ContentHelperRoute::getCategoryRoute($item->catid));
            return $item;
        }

        /**
         * 
         * @param object $item
         * @return object
         */
        protected function _prepareItemImages($item)
        {
            $images = json_decode($item->images);
            if ($images)
            {
                if ($images->image_intro)
                {
                    $item->thumb = modZTNewsHelper::getThumbnailLink($images->image_intro, $this->_params->get('thumb_main_width'), $this->_params->get('thumb_main_height'), $this->_params);
                    $item->subThumb = modZTNewsHelper::getThumbnailLink($images->image_intro, $this->_params->get('thumb_list_width'), $this->_params->get('thumb_list_height'), $this->_params);
                } else if ($images->image_fulltext)
                {
                    $item->thumb = modZTNewsHelper::getThumbnailLink($images->image_fulltext, $this->_params->get('thumb_main_width'), $this->_params->get('thumb_main_height'), $this->_params);
                    $item->subThumb = modZTNewsHelper::getThumbnailLink($images->image_fulltext, $this->_params->get('thumb_list_width'), $this->_params->get('thumb_list_height'), $this->_params);
                }
            }
            return $item;
        }

        /**
         * Recursive to get all children categories of joomla article
         */
        protected function _getChildrenCategories($cids)
        {
            $db = JFactory::getDBO();
            $query = ' SELECT ' . $db->quoteName('id')
                    . ' FROM ' . $db->quoteName('#__categories')
                    . ' WHERE ' . $db->quoteName('parent_id') . 'IN ( ' . implode(',', $cids) . ' ) '
                    . ' AND ' . $db->quoteName('published') . ' = 1 '
                    . ' AND ' . $db->quoteName('parent_id') . ' != 0 '
                    . ' ORDER BY ' . $db->quoteName('id')
            ;
            $db->setQuery($query);

            $rows = $db->loadColumn();
            $this->_categories = array_merge($this->_categories, $rows);
            if (count($rows) > 0)
            {
                $this->_getChildrenCategories($rows);
            }
            $this->_categories = array_unique($this->_categories);
            return $this->_categories;
        }

    }

}