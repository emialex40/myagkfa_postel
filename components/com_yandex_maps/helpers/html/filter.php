<?php 
defined('_JEXEC') or die; ob_start();
$dispatcher = JEventDispatcher::getInstance();
JPluginHelper::importPlugin('yandexmapssource');
JPluginHelper::importPlugin('system');
$filters = array();
$dispatcher->trigger('generateFilter', array(&$filters, $map));
$id = uniqid();
?>
<div style="height:<?php echo $map->settings->get('filter_height', 210);?>px;margin-top:-<?php echo round($map->settings->get('filter_height', 210)/2);?>px;<?php echo $map->settings->get('filter_extended_style', 210);?>" class="xdsoft_filter <?php echo $map->settings->get('filter_style', 1) ? 'xdsoft_filter_vertical' : 'xdsoft_filter_horizontal'?> xdsoft_filter_<?php echo $map->settings->get('show_category_filter', 0);?>">
	<div class="xdsoft_filter_wrapper">
        <?php if ($map->settings->get('show_label_filter', 1) and $map->settings->get('label_category_filter')) {?>
        <h4><?php echo $map->settings->get('label_category_filter');?></h4>
        <?php } ?>
        <div class="xdsoft_filter_items" style="height:<?php echo $map->settings->get('filter_height', 210) - 50;?>px;">
            <?php foreach ($filters as $filter) { ?>
            <div class="xdsoft_filter_item">
                <?php echo $filter?>
            </div>
            <?php } ?>
        </div>
        <?php if (in_array($map->settings->get('show_category_filter', 0), array(1, 2, 3, 4))) {?>
            <a href="#" id="toggleFilter<?php echo $id?>" class="hide_panel"><span>Скрыть панель</span></a>
        <?php } ?>
    </div>
</div>
<script>
(function($){
	$('.xdsoft_filter #toggleFilter<?php echo $id?>').click(function(){
		$(this).closest('.xdsoft_filter').toggleClass('filterhide');
		return false;
	});
}(window.XDjQuery || window.jQ || window.jQuery))
</script>
<?php 
return ob_get_clean();