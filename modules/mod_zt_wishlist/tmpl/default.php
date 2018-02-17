<?php
/**
 * @package    ZT VirtueMarter
 * @subpackage ZT VirtueMarter Comparelist Module
 * @author       ZooTemplate.com
 * @link http://zootemplate.com
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die('Restricted access');
JFactory::getLanguage()->load('com_ztvirtuemarter');
$items = JFactory::getApplication()->getMenu('site')->getItems('component', 'com_ztvirtuemarter');
$itemid = '';
foreach ($items as $item) {
    if ($item->query['view'] === 'wishlists') {
        $itemid = $item->id;
    }
}
if (plgSystemZtvirtuemarter::getZtvirtuemarterSetting()->enable_wishlist == '1') :
    ?>
    <div class="ajax-dropdown vmgroup<?php echo $params->get('moduleclass_sfx') ?>" id="mod_wishlists">
        <div class="seldcomp" id="butseldwish">
            <?php if (plgSystemZtvirtuemarter::getZtvirtuemarterSetting()->enable_wishlist == '1') : ?>
                <a class="btn-wishlist"
                   href="<?php echo JRoute::_('index.php?option=com_ztvirtuemarter&view=wishlist&Itemid=' . $itemid . ''); ?>">
                    <i class="fa fa-heart-o hover-dropdown"></i>
                <span><?php if ($user->guest) {
                        echo count($wishlistIds);
                    } else {
                        echo count($allprod['id']);
                    } ?></span>
                </a>
            <?php endif; ?>
        </div>
        <div class="zt-cart-inner">
            <div class="vmproduct">
                <?php
                if (count($prods) > 0) :
                    foreach ($prods as $product) :
                        ?>
                        <div id="wishlists_prod_<?php echo $product->virtuemart_product_id; ?>"
                             class="modwishlistsprod clearfix">
                            <div class="wishlist-product-img">
                                <a href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id); ?>">
                                    <img src="<?php if (!empty($product->file_url_thumb)) {
                                        echo JURI::base() . $product->file_url_thumb;
                                    } else {
                                        echo JURI::base() . 'images/stories/virtuemart/noimage.gif';
                                    } ?>" alt="<?php echo $product->product_name; ?>"
                                         title="<?php echo $product->product_name; ?>"/>
                                </a>
                            </div>
                            <div class="wishlist-product-detail">
                                <div class="name">
                                    <?php echo JHTML::link($product->link, $product->product_name); ?>
                                </div>
                                <div class="remwishlists">
                                    <a class="tooltip-1" title="remove"
                                       onclick="zo2.wishlist.remove('<?php echo $product->virtuemart_product_id; ?>');">
                                        <i class="fa fa-times"></i><?php echo JText::_('REMOVE'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php
                    endforeach;
                else : ?>
                    <div class="not_text wishlists"><?php echo JText::_('YOU_HAVE_NO_PRODUCT_TO_WISHLISTS'); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>