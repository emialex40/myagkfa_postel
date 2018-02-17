<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2015 ThemePunch
 */

 
defined( '_JEXEC') or die();

$this->is_edit = false;

echo $this->loadTemplate('slider_main_options');
?>

<script type="text/javascript">
	var g_jsonTaxWithCats = <?php echo $this->jsonTaxWithCats?>;

	jQuery(document).ready(function(){
		ZtSliderAdmin.initAddSliderView();
	});
</script>

