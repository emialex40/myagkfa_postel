<?php

/**
 * One Page Checkout For VirtueMart
 *
 * @version     1.0.2
 * @link        http://www.zootemplate.com
 * @author      ZooTemplate
 * @copyright   Copyright (c) 2015 CleverSoft (http://cleversoft.co/)
 * @license     GPL v2
 */

defined('_JEXEC') or die('Restricted access');

// Html For zt onepage checkout

$class = 'zt-opc-';
?>

<div id="<?php echo $class; ?>plugin">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6 col-md-6 span6">
                <?php echo $this->loadTemplate('guest'); ?>
            </div>
            <div class="col-sm-6 col-md-6 span6">
                <?php echo $this->loadTemplate('login'); ?>
            </div>
        </div>
    </div>   
</div>
