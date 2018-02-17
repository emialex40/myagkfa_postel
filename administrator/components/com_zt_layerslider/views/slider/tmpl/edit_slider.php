<?php
defined('_JEXEC') or die;
?>
<input type="hidden" id="sliderid" value="<?php echo $this->item->id; ?>" />

<?php
$this->is_edit = true;
echo $this->loadTemplate('slider_main_options');
?>

<script type="text/javascript">
	var g_jsonTaxWithCats = <?php echo $this->jsonTaxWithCats;?>;

	jQuery(document).ready(function(){			
		ZtSliderAdmin.initEditSliderView();
	});
</script>