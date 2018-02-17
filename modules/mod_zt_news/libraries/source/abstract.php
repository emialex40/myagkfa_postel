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


if (!class_exists('ZtNewsSourceAbstract'))
{

    /**
     * Source abstract class
     */
    abstract class ZtNewsSourceAbstract
    {

        /**
         * Source name
         * @var string
         */
        protected $_source = 'content';

        /**
         * Plugin params
         * @var JRegistry
         */
        protected $_params;

        /**
         * Items table name
         * @var string
         */
        protected $_table_items = '#__content';

        /**
         * Categories table name
         * @var string
         */
        protected $_table_categories = '#__categories';
        protected $_categories;

        /**
         * Contructor
         * @param JRegistry $params
         */
        public function __construct($params)
        {
            $this->_params = $params;
            // Get requested categories
            $this->_categories = $this->_params->get($this->_source . '_cids');
        }

        /**
         * Get array of requested categories
         * @return array
         */
        public function getCategories()
        {
            if ($this->_params->get('get_children', 1))
            {
                // Recursive for children categories
                return $this->_getChildrenCategories($this->_categories);
            } else
            {
                return $this->_categories;
            }
        }

        /**
         * Must be declare in extend class
         */
        abstract protected function _getChildrenCategories($cids);

        /**
         * Get items in categories
         * @return array
         */
        public function getItems($groupByCategories = false)
        {
            $db = JFactory::getDbo();
            $query = $this->_buildQuery();
            $db->setQuery($query);
            $list = $db->loadObjectList();
            $list = $this->_prepareItems($list);
            // Group items by categories
            if ($groupByCategories)
            {
                foreach ($list as $item)
                {
                    $newList[$item->catid]['items'][] = $item;
                    $category = new JObject();
                    $category->title = $item->title;
                    $category->link = $item->cat_link;
                    $newList[$item->catid]['category'] = $category;
                }
                $list = $newList;
            }
            return $list;
        }

        /**
         * Build query to getItems
         * @return string
         */
        protected function _buildQuery()
        {
            $query = ' SELECT a.*, cc.title as cat_title,' .
                    ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,' .
                    ' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug' .
                    ' FROM ' . $this->_table_items . ' AS a' .
                    ' INNER JOIN ' . $this->_table_categories . ' AS cc ON cc.id = a.catid' .
                    ' WHERE ' . $this->_buildWhere() . $this->_buildWhereExtend() .
                    ' AND cc.published = 1' .
                    ' ORDER BY ' . $this->_buildOrder() .
                    ' LIMIT ' . $this->_params->get('max_items', 10);
            return $query;
        }

        /**
         * Basic required WHERE
         * @return string
         */
        protected function _buildWhere()
        {
            
        }

        /**
         * Extend WHERE condition
         * @return string
         */
        protected function _buildWhereExtend()
        {
            $db = JFactory::getDbo();
            $date = JFactory::getDate();
            $now = $date->toSQL();
            $nullDate = $db->getNullDate();
            $user = JFactory::getUser();
            $userId = (int) $user->get('id');
            $where = ' AND( a.publish_up = ' . $db->Quote($nullDate) . ' OR a.publish_up <= ' . $db->Quote($now) . ' ) '
                    . ' AND( a.publish_down = ' . $db->Quote($nullDate) . ' OR a.publish_down >= ' . $db->Quote($now) . ' ) ';
            // Filter by categories
            $categories = $this->getCategories();
            $where .= ' AND ' . $db->quoteName('a') . '.' . $db->quoteName('catid') . ' IN ' . ' ( ' . implode(',', $categories) . ' ) ';
            // User Filter
            switch ($this->_params->get('user_id'))
            {
                case 'by_me':
                    $where .= ' AND ( ' . $db->quoteName('created_by') . ' = ' . (int) $userId . ' OR ' . $db->quoteName('modified_by') . ' = ' . (int) $userId . ')';
                    break;
                case 'not_me':
                    $where .= ' AND ( ' . $db->quoteName('created_by') . ' <> ' . (int) $userId . ' AND ' . $db->quoteName('modified_by') . ' <> ' . (int) $userId . ')';
                    break;
            }
            return $where;
        }

        /**
         * Build ordering query
         * @return string
         */
        protected function _buildOrder()
        {
            // Ordering
            switch ($this->_params->get('ordering'))
            {
                case 'date':
                    $orderby = 'a.created ASC';
                    break;
                case 'rdate':
                    $orderby = 'a.created DESC';
                    break;
                case 'alpha':
                    $orderby = 'a.title';
                    break;
                case 'ralpha':
                    $orderby = 'a.title DESC';
                    break;
                case 'order':
                    $orderby = 'a.ordering';
                    break;
                case 'rorder':
                    $orderby = 'a.ordering DESC';
                    break;
                case 'hits':
                    $orderby = 'a.hits DESC';
                    break;
                case 'rand':
                    $orderby = 'RAND()';
                    break;
                case 'modified':
                    $orderby = 'a.modified DESC';
                    break;
                default:
                    $orderby = 'a.id DESC';
                    break;
            }
            return $orderby;
        }

        /**
         * Prepare item before return items list
         * @param array $list
         * @return array
         */
        protected function _prepareItems($list)
        {
            $items = array();
            foreach ($list as $index => $item)
            {
                $item = $this->_prepareItem($item);
                $item = $this->_prepareItemImages($item);
                $item = $this->_mapFields($item, array());
                $items[$index] = $item;
            }
            return $items;
        }

        /**
         * Field source fields are different than we need map it to standard
         * @param type $item
         * @param type $fields
         * @return type
         */
        public function _mapFields($item, $fields)
        {
            foreach ($fields as $key => $field)
            {
                if (isset($item->$key))
                {
                    $item->$field = $key;
                    unset($item->$key);
                }
            }
            return $item;
        }

        /**
         * Prepare item properties
         */
        abstract protected function _prepareItem($item);

        /**
         * Prepare images for item
         */
        abstract protected function _prepareItemImages($item);
    }

}