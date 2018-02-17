<?php

defined('_JEXEC') or die;

$wrapperClass = "";

$glval = JComponentHelper::getParams('com_zt_layerslider');

?>
<script>
    rev_lang.root = '<?php echo JUri::root()."administrator/index.php?option=com_zt_layerslider" ;?>';
</script>
<?php
$waitstyle = '';
if(isset($_REQUEST['update_shop'])){
    $waitstyle = 'display:block';
}
?>
<div id="j-main-container">

<div id="waitaminute" style="<?php echo $waitstyle; ?>">
    <div class="waitaminute-message"><i class="eg-icon-emo-coffee"></i><br><?php echo JText::_('COM_ZT_LAYERSLIDER_PLEASE_WAIT');//_e("Please Wait...", 'revslider'); ?></div>
</div>


<script type="text/javascript">
    var g_urlAjaxShowImage = "<?php echo JRoute::_('index.php?option=com_zt_layerslider&task=sliders.ajaximage') ; ?>";
    var g_urlAjaxActions = "<?php echo JRoute::_('index.php?option=com_zt_layerslider&task=slider.preview') ;  ?>";
    var g_revslider_url = "<?php echo JPATH_ROOT . '/administrator/components/com_zt_layerslider'; ?>";
    var g_settingsObj = {};

    var global_grid_sizes = {
        'desktop': '<?php echo $glval->get('width', 1230); ?>',
        'notebook': '<?php echo $glval->get('width_notebook', 1230); ?>',
        'tablet': '<?php echo $glval->get('width_tablet', 992); ?>',
        'mobile': '<?php echo $glval->get('width_mobile', 480); ?>'
    };
</script>

<div id="div_debug"></div>

<div class='unite_error_message' id="error_message" style="display:none;"></div>

<div class='unite_success_message' id="success_message" style="display:none;"></div>

<div id="viewWrapper" class="view_wrapper<?php echo $wrapperClass; ?>">
    <?php echo $this->loadTemplate('header'); ?>
</div>

<div id="divColorPicker" style="display:none;"></div>

    <?php include_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/system/dialog_video.php'); ?>

<div class="tp-plugin-version">
    <span style="margin-right:15px">&copy; All rights reserved, <a href="http://zootemplate.com" target="_blank">ZooTemplate</a>  ver. 2.0.0</span>
</div>
<?php
/*
<script type="text/javascript">
	<span class="rs-shop">SHOP</span>
	jQuery(document).ready(function(){
		jQuery('.rs-shop').click(function(){

		});
	});
</script>
*/
?>
<div id="rs-shop-overview">
</div>

<div id="rs-preview-wrapper" style="display: none;">
    <div id="rs-preview-wrapper-inner">
        <div id="rs-preview-info">
            <div class="rs-preview-toolbar">
                <a class="rs-close-preview"><i class="eg-icon-cancel"></i></a>
            </div>

            <div data-type="desktop" class="rs-preview-device_selector_prev rs-preview-ds-desktop selected"></div>
            <div data-type="notebook" class="rs-preview-device_selector_prev rs-preview-ds-notebook"></div>
            <div data-type="tablet" class="rs-preview-device_selector_prev rs-preview-ds-tablet"></div>
            <div data-type="mobile" class="rs-preview-device_selector_prev rs-preview-ds-mobile"></div>

        </div>
        <div class="rs-frame-preview-wrapper">
            <iframe id="rs-frame-preview" name="rs-frame-preview"></iframe>
        </div>
    </div>
</div>
<form id="rs-preview-form" name="rs-preview-form" action="<?php echo JRoute::_('index.php?option=com_zt_layerslider') ?>" target="rs-frame-preview" method="post">
    <input type="hidden" name="task" value="slider.previewslider">
    <input type="hidden" name="option" value="com_zt_layerslider">

    <!-- SPECIFIC FOR SLIDE PREVIEW -->
    <input type="hidden" name="data" value="" id="preview-slide-data">

    <!-- SPECIFIC FOR SLIDER PREVIEW -->
    <input type="hidden" id="preview_sliderid" name="sliderid" value="">
    <input type="hidden" id="preview_slider_markup" name="only_markup" value="">
</form>


<div id="dialog_preview_sliders" class="dialog_preview_sliders" title="Preview Slider" style="display:none;">
    <iframe id="frame_preview_slider" name="frame_preview_slider" style="width: 100%;"></iframe>
</div>

<script type="text/javascript">
    <?php
//    $validated = get_option('revslider-valid', 'false');
    ?>
//    rs_plugin_validated = <?php //echo ($validated == 'true') ? 'true' : 'false'; ?>//;

    jQuery('body').on('click','.rs-preview-device_selector_prev', function() {
        var btn = jQuery(this);
        jQuery('.rs-preview-device_selector_prev.selected').removeClass("selected");
        btn.addClass("selected");

        var w = parseInt(global_grid_sizes[btn.data("type")],0);
        if (w>1450) w = 1450;
        jQuery('#rs-preview-wrapper-inner').css({maxWidth:w+"px"});

    });

    jQuery(window).resize(function() {
        var ww = jQuery(window).width();
        if (global_grid_sizes)
            jQuery.each(global_grid_sizes,function(key,val) {
                if (ww<=parseInt(val,0)) {
                    jQuery('.rs-preview-device_selector_prev.selected').removeClass("selected");
                    jQuery('.rs-preview-device_selector_prev[data-type="'+key+'"]').addClass("selected");
                }
            })
    })

    /* SHOW A WAIT FOR PROGRESS */
    function showWaitAMinute(obj) {
        var wm = jQuery('#waitaminute');
        // SHOW AND HIDE WITH DELAY
        if (obj.delay!=undefined) {

            punchgs.TweenLite.to(wm,0.3,{autoAlpha:1,ease:punchgs.Power3.easeInOut});
            punchgs.TweenLite.set(wm,{display:"block"});

            setTimeout(function() {
                punchgs.TweenLite.to(wm,0.3,{autoAlpha:0,ease:punchgs.Power3.easeInOut,onComplete:function() {
                    punchgs.TweenLite.set(wm,{display:"block"});
                }});
            },obj.delay)
        }

        // SHOW IT
        if (obj.fadeIn != undefined) {
            punchgs.TweenLite.to(wm,obj.fadeIn/1000,{autoAlpha:1,ease:punchgs.Power3.easeInOut});
            punchgs.TweenLite.set(wm,{display:"block"});
        }

        // HIDE IT
        if (obj.fadeOut != undefined) {

            punchgs.TweenLite.to(wm,obj.fadeOut/1000,{autoAlpha:0,ease:punchgs.Power3.easeInOut,onComplete:function() {
                punchgs.TweenLite.set(wm,{display:"block"});
            }});
        }

        // CHANGE TEXT
        if (obj.text != undefined) {
            switch (obj.text) {
                case "progress1":

                    break;
                default:
                    wm.html('<div class="waitaminute-message"><i class="eg-icon-emo-coffee"></i><br>'+obj.text+'</div>');
                    break;
            }
        }
    }
</script>
</div>
<script>
    //remove conflic button between bootstrap and jquery
    jQuery.fn.bootstrapBtn = jQuery.fn.button.noConflict();
</script>