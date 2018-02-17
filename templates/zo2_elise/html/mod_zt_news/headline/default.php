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

require __DIR__ . '/init.php';
?>
<div id="zt-headline" class="wrapper">
    <div class="">
        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
            <!-- Indicators -->
            <ol class="carousel-indicators">
                <?php foreach ($list as $key => $slide) : ?>       
                    <li data-target="#carousel-example-generic" data-slide-to="<?php echo $key; ?>" class="<?php echo ($key == 0) ? 'active' : ''; ?>"></li>
                <?php endforeach; ?>
            </ol>
            <!-- Wrapper for slides -->
            <div class="carousel-inner" role="listbox">
                <?php foreach ($list as $key => $slide) : ?>    
                    <div class="item <?php echo ($key == 0) ? 'active' : ''; ?>">
                        <?php require __DIR__ . '/slide.php'; ?>
                    </div>
                <?php endforeach; ?>               
            </div>
            <!-- Controls -->
            <div class="owl-buttons">
                <a class="carousel-control control-left" href="#carousel-example-generic" role="button" data-slide="prev">
                    <i class="fa fa-angle-left"></i>
                    <span class="sr-only"><?php echo JText::_('MOD_ZTNEWS_PREVIOUS');?></span>
                </a>
                <a class="carousel-control control-right" href="#carousel-example-generic" role="button" data-slide="next">
                    <i class="fa fa-angle-right"></i>
                    <span class="sr-only"><?php echo JText::_('MOD_ZTNEWS_NEXT');?></span>
                </a>
            </div>
        </div>
    </div>
</div>