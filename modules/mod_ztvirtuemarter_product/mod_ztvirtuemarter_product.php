<?php
/**
 * @package    ZT VirtueMarter
 * @subpackage ZT VirtueMarter Product Module
 * @author       ZooTemplate.com
 * @link http://zootemplate.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */
defined('_JEXEC') or die('Direct Access to ' . basename(__FILE__) . ' is not allowed.');

if (!class_exists('VmConfig')) require(JPATH_ROOT . '/administrator/components/com_virtuemart/helpers/config.php');

VmConfig::loadConfig();
VmConfig::loadJLang('mod_ztvirtuemarter_product', true);
$jinput = JFactory::getApplication()->input;

// Setting
$maxItems = $params->get('max_items', 2); //maximum number of items to display
$layout = $params->get('layout', 'default');
$categoryId = $params->get('virtuemart_category_id', null); // Display products from this category only
$filterCategory = (bool)$params->get('filter_category', 0); // Filter the category
$displayStyle = $params->get('display_style', "div"); // Display Style
$productsPerRow = $params->get('products_per_row', 1); // Display X products per Row
$showPrice = (bool)$params->get('show_price', 1); // Display the Product Price?
$showAddtocart = (bool)$params->get('show_addtocart', 1); // Display the "Add-to-Cart" Link?
$headerText = $params->get('headerText', ''); // Display a Header Text
$footerText = $params->get('footerText', ''); // Display a footerText
$productGroup = $params->get('product_group', 'featured'); // Display a footerText
$newProductFrom = $params->get('new_product_from', '7');
$mainframe = Jfactory::getApplication();
$virtuemartCurrencyId = $mainframe->getUserStateFromRequest("virtuemart_currency_id", 'virtuemart_currency_id', vRequest::getInt('virtuemart_currency_id', 0));


/* Load  VM fonction */
if (!class_exists('mod_ztvirtuemarter_product')) require('helper.php');

$vendorId = vRequest::getInt('vendorid', 1);

if ($filterCategory) $filterCategory = TRUE;

$productModel = VmModel::getModel('Product');

$products = $productModel->getProductListing($productGroup, $maxItems, $showPrice, true, false, $filterCategory, $categoryId);
$productModel->addImages($products);

$totalProd = count($products);
if (empty($products)) return false;
$currency = CurrencyDisplay::getInstance();

if ($showAddtocart) {
    vmJsApi::jPrice();
    vmJsApi::cssSite();
}
ob_start();

/* Load tmpl default */
require(JModuleHelper::getLayoutPath('mod_ztvirtuemarter_product', $layout));
$output = ob_get_clean();

echo $output;
?>
