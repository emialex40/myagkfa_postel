<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_latest
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<ul class="latestnews<?php echo $moduleclass_sfx; ?>">
<?php foreach ($list as $item) :  ?>
  <?php $images = json_decode($item->images); ?>

  <li itemscope itemtype="https://schema.org/Article">
    <a href="<?php echo $item->link; ?>" itemprop="url">
      <img src="<?php echo htmlspecialchars($images->image_intro); ?>" alt="<?php echo htmlspecialchars($images->image_intro_alt); ?>"/>
      <div class="item-detail">
        <span class="create">
          <time>
            <?php echo  JHtml::_('date', $itemprop->created, JText::_('DATE_FORMAT_LC3')); ?>
          </time>
        </span>
        <span itemprop="name">
          <?php echo $item->title; ?>
        </span>
      </div>
    </a>
  </li>
<?php endforeach; ?>
</ul>
