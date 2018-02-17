<?php // no direct access
defined('_JEXEC') or die('Restricted access');
$categoryModel->addImages($categories);
$categories_per_row = vmConfig::get('categories_per_row');
$col_width = floor ( 100 / $categories_per_row);
//var_dump($categories);die;
?>

<ul class="vm-categories-wall <?php echo $class_sfx ?>">
  <?php foreach ($categories as $category) : ?>
  <?php
  $caturl = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$category->virtuemart_category_id);
  $catname = $category->category_name ;
  ?>
  <li class="vm-categories-wall-catwrapper floatleft width20">
  	<div class="vm-categories-wall-spacer center">
      <a href="<?php echo $caturl; ?>">
        <?php echo $category->images[0]->displayMediaThumb('class="vm-categories-wall-img"',false) ?>
    		<div class="vm-categories-wall-catname"><?php echo $catname; ?></div>
      </a>
        <div class="num-product">(<?php echo $categoryModel->countProducts($category->virtuemart_category_id); ?> items)</div>
  	</div>
  </li>
  <?php endforeach; ?>
  <li class="clear"></li>
</ul>