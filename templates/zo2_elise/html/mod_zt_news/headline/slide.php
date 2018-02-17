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
// Slice had items
$listItems = array_slice($slide, $numberIntroItems);
?>
<div class="zt-category headline">
    <div class="row">
        <?php $index = 0; ?>
        <?php foreach ($slide as $key => $item) : ?>         
            <?php if ($index < $numberIntroItems) : ?>
                <div class="col-md-6 zt-item head">
                    <?php if($showTitleCategory):?>
                        <div class="link-category">
                            <a href="<?php echo $item->cat_link; ?>" alt="<?php echo $item->cat_title; ?>"><?php echo $item->cat_title; ?></a>
                        </div> 
                    <?php endif;?>
                    <!-- Head Thumbnail -->
                    <?php if($isImage):?>
                        <div class="post-thumnail">
                            <a href="<?php echo $item->link; ?>" title="">
                                <?php if (!empty($item->thumb)) : ?>
                                    <img class="thumbnail" 
                                         src="<?php echo $item->thumb; ?>" 
                                         alt="<?php echo $item->title; ?>"
                                         title="<?php echo $item->title; ?>"
                                         />
                                <?php endif; ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="zt-article_content">
                        <!-- Item title -->
                        <?php if ($showTitle) : ?>
                            <h3>
                                <a href="<?php echo $item->link; ?>" title="">
                                    <?php echo $item->title; ?>
                                </a>
                            </h3>
                        <?php endif; ?>
                        <!-- Created -->
                        <?php if ($showCreated) : ?>                            
                            <span class="created">
                                <?php echo JHTML::_('date', $item->created, JText::_('DATE_FORMAT_LC3')); ?> - <?php
                                echo $item->hits;
                                echo JText::_(' Views');
                                ?>
                            </span>
                        <?php endif; ?>
                        <!-- Intro text -->
                        <?php if ($showIntro && $item->introtext != false) : ?>
                            <div class="zt-introtext"><?php echo ($item->introtext); ?></div>
                        <?php endif; ?> 
                        <!-- Readmore -->
                        <?php if ($showReadmore) : ?>                     
                            <p class="zt-news-readmore">
                                <a class="readmore" href="<?php echo $item->link; ?>"><?php echo JTEXT::_('READ MORE'); ?></a>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>      
            <?php $index++; ?>
        <?php endforeach; ?>

        <div class="col-md-6">
            <div class="row">
                <?php foreach ($listItems as $key => $item) : ?>
                    <div class="col-md-6 zt-item">
                        <?php if($showTitleCategory):?>
                            <div class="link-category">
                                <a href="<?php echo $item->cat_link; ?>" alt="<?php echo $item->cat_title; ?>"><?php echo $item->cat_title; ?></a>
                            </div> 
                        <?php endif;?>
                        <?php if($showImageOnList):?>
                            <div class="post-thumnail">                      
                                <a href="<?php echo $item->link; ?>" title="">
                                        <!-- List thumbnail -->
                                    <?php if (!empty($item->subThumb)) : ?>
                                        <img class="thumbnail" 
                                             src="<?php echo $item->subThumb; ?>" 
                                             alt="<?php echo $item->title; ?>"
                                             title="<?php echo $item->title; ?>"
                                             />
                                     <?php endif; ?>
                                </a>
                            </div>
                        <?php endif;?>
                        <div class="zt-article_content">
                            <?php if ($showTitle) : ?>
                                <h3>
                                    <a href="<?php echo $item->link; ?>" title="">
                                        <?php echo $item->title; ?>
                                    </a>
                                </h3>
                            <?php endif; ?>
                            <!-- Created -->
                            <?php if ($showCreated) : ?>                            
                                <span class="created">
                                    <?php echo JHTML::_('date', $item->created, JText::_('DATE_FORMAT_LC3')); ?> - <?php
                                    echo $item->hits;
                                    echo JText::_('MOD_ZTNEWS_VIEWS');
                                    ?>
                                </span>
                            <?php endif; ?>
                            <!-- Intro text -->
                            <?php if ($showIntroList && $item->introtext != false) : ?>
                                <div class="zt-introtext"><?php echo substr($item->introtext, 0, 90); ?></div>
                            <?php endif; ?> 
                            <!-- Readmore -->
                            <?php if ($showReadmore) : ?>                     
                                <p class="zt-news-readmore">
                                    <a class="readmore" href="<?php echo $item->link; ?>"><?php echo JTEXT::_('MOD_ZTNEWS_READMORE'); ?></a>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>