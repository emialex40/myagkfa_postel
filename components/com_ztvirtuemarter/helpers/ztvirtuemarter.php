<?php
/**
 * @package    ZT VirtueMarter
 * @subpackage ZT VirtueMarter Components
 * @author       ZooTemplate.com
 * @link http://zootemplate.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');

class ZtvituemarterHelper
{
    /**
     * void
     * @param null
     */
    public static function loadVMLibrary()
    {
        if (!class_exists('VmConfig')) require(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/config.php');
        if (!class_exists('calculationHelper')) require(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/calculationh.php');
        if (!class_exists('CurrencyDisplay')) require(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/currencydisplay.php');
        if (!class_exists('VirtueMartModelVendor')) require(JPATH_ADMINISTRATOR . '/components/com_virtuemart/models/vendor.php');
        if (!class_exists('VmImage')) require(JPATH_ADMINISTRATOR . '/components/com_virtuemart/helpers/image.php');
        if (!class_exists('shopFunctionsF')) require(JPATH_SITE . '/components/com_virtuemart/helpers/shopfunctionsf.php');
        if (!class_exists('calculationHelper')) require(JPATH_COMPONENT_SITE . '/helpers/cart.php');
        if (!class_exists('VirtueMartModelProduct')) {
            JLoader::import('product', JPATH_ADMINISTRATOR . '/components/com_virtuemart/models');
        }
        if (!class_exists('VirtueMartModelRatings')) {
            JLoader::import('ratings', JPATH_ADMINISTRATOR . '/components/com_virtuemart/models');
        }
    }

    /**
     * Class hepler
     * return $itemid
     * @param String $view
     */
    public static function getItemId($view)
    {
        $itemid = '';
        $items = JFactory::getApplication()->getMenu('site')->getItems('component', 'com_ztvirtuemarter');
        foreach ($items as $item) {
            if ($item->query['view'] == $view) {
                $itemid = $item->id;
            }
        }
        return $itemid;
    }
}