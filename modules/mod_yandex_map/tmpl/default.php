<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_google_map
 *
 * @copyright   Copyright (C) 2015 Artem Yegorov. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<script type="text/javascript">
var yandex_module_id= <?php if ($module->id!==null){echo $module->id;} else { echo "0";};?>;
var yandex_module_ids = yandex_module_ids || [];
yandex_module_ids.push(yandex_module_id);
var y_cords = y_cords || [];
y_cords[yandex_module_id] = y_cords[yandex_module_id] || <?php echo $fields->marker ; ?>;
var y_zoom = y_zoom || [];
y_zoom[yandex_module_id]  = y_zoom[yandex_module_id] || <?php echo (isset($fields->zoom) ? $fields->zoom : '16');?>;
var y_mapType = y_mapType || [];
y_mapType[yandex_module_id]  = y_mapType[yandex_module_id] || "yandex#" + "<?php echo (isset($fields->type) ? $fields->type : 'map'); ?>";
var y_preset = y_preset || [];
y_preset[yandex_module_id] = y_preset[yandex_module_id] || "islands#" + "<?php echo (isset($fields->icontype) ? $fields->icontype : 'dotIcon');?>";
</script>
<script type="text/javascript" src="modules/mod_yandex_map/js/joomly_map.js"></script>
<div class="joomly-map" style="max-width: <?php echo (isset($fields->width) ? $fields->width : 600)."px"; ?>;height: <?php echo (isset($fields->height) ? $fields->height : 400)."px"; ?>;
margin-left: <?php echo isset($fields->margin) ? $fields->margin : "none"; ?>;margin-right: <?php echo isset($fields->margin) ? $fields->margin : "none"; ?>;">
	<div id="map<?php if ($module->id !==null){echo $module->id;};?>" class="joomly-ymap"></div>
</div>	
