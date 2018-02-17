<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2015 ThemePunch
 */


defined('_JEXEC') or die;

$operations = new ZTSliderOperations();
$rs_nav = new ZTSliderNavigation();

$arrValues = JComponentHelper::getParams('com_zt_layerslider');

$arr_navigations = $rs_nav->get_all_navigations(true, false, $this->item->attribs);

$transitions = $operations->getArrTransition();

$_width = (isset($arrValues['width'])) ? $arrValues['width'] : 1240;
$_width_notebook = (isset($arrValues['width_notebook'])) ? $arrValues['width_notebook'] : 1024;
$_width_tablet = (isset($arrValues['width_tablet'])) ? $arrValues['width_tablet'] : 778;
$_width_mobile = (isset($arrValues['width_mobile'])) ? $arrValues['width_mobile'] : 480;


if(!isset($this->is_edit)) $this->is_edit = false;
if(!isset($linksEditSlides)) $linksEditSlides = !empty($this->item->id) ? JRoute::_('index.php?option=com_zt_layerslider&view=slides&id=new&slider_id='.$this->item->id,false) : JRoute::_('index.php?option=com_zt_layerslider&view=slider&layout=edit',false);
?>
<script>
	var thickboxL10n = {};
	thickboxL10n.next = '<?php echo JText::_("COM_ZT_LAYERSLIDER_NEXT")."&gt;" ?>';
	thickboxL10n.prev ='<?php echo "&lt;".JText::_("COM_ZT_LAYERSLIDER_PREVIOUS")?>';
	thickboxL10n.image = '<?php echo JText::_('COM_ZT_LAYERSLIDER_IMAGE') ?>';
	thickboxL10n.of =  '<?php echo JText::_('COM_ZT_LAYERSLIDER_OF') ?>';
	thickboxL10n.close= '<?php echo JText::_('COM_ZT_LAYERSLIDER_CLOSE') ?>';
	thickboxL10n.noiframes = '<?php echo JText::_('COM_ZT_LAYERSLIDER_FEATURE_REQUIRES_INLINE_FRAMES') ?>';
	thickboxL10n.loadingAnimation = '<?php  echo JUri::root().'administrator/components/com_zt_layerslider/assets/images/loadingAnimation.gif' ?>';
	jQuery.getScript('<?php echo JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/js/thickbox.js' ?>');
</script>
<script>
	jQuery(document).ready(function(){function r(r){var i;return r=r.replace(/ /g,""),r.match(/rgba\(\d+\,\d+\,\d+\,([^\)]+)\)/)?(i=100*parseFloat(r.match(/rgba\(\d+\,\d+\,\d+\,([^\)]+)\)/)[1]).toFixed(2),i=parseInt(i)):i=100,i}function i(r,i,e,t){var n,o,c;n=i.data("a8cIris"),o=i.data("wpWpColorPicker"),n._color._alpha=r,c=n._color.toString(),i.val(c),o.toggler.css({"background-color":c}),t&&a(r,e),i.wpColorPicker("color",c)}function a(r,i){i.slider("value",r),i.find(".ui-slider-handle").text(r.toString())}Color.prototype.toString=function(r){if("no-alpha"==r)return this.toCSS("rgba","1").replace(/\s+/g,"");if(1>this._alpha)return this.toCSS("rgba",this._alpha).replace(/\s+/g,"");var i=parseInt(this._color,10).toString(16);if(this.error)return"";if(i.length<6)for(var a=6-i.length-1;a>=0;a--)i="0"+i;return"#"+i},jQuery.fn.alphaColorPicker=function(){return this.each(function(){var e,t,n,o,c,l,s,d,u,p,f;e=jQuery(this),e.wrap('<div class="alpha-color-picker-wrap"></div>'),n=e.attr("data-palette")||"true",o=e.attr("data-show-opacity")||"true",c=e.attr("data-default-color")||"",l=-1!==n.indexOf("|")?n.split("|"):"false"==n?!1:!0,t=e.val().replace(/\s+/g,""),""==t&&(t=c),s={change:function(i,a){try{jQuery(this).closest('.placeholder-single-wrapper').find('.placeholder-checkbox').attr('checked','checked');drawToolBarPreview();} catch(e) {};var t,n,o,l;t=e.attr("data-customize-setting-link"),n=e.wpColorPicker("color"),c==n&&(o=r(n),u.find(".ui-slider-handle").text(o)),"undefined"!=typeof wp.customize&&wp.customize(t,function(r){r.set(n)}),l=d.find(".transparency"),l.css("background-color",a.color.toString("no-alpha"))},palettes:l},e.wpColorPicker(s),d=e.parents(".wp-picker-container:first"),jQuery('<div class="alpha-color-picker-container"><div class="min-click-zone click-zone"></div><div class="max-click-zone click-zone"></div><div class="alpha-slider"></div><div class="transparency"></div></div>').appendTo(d.find(".wp-picker-holder")),u=d.find(".alpha-slider"),p=r(t),f={create:function(r,i){var a=jQuery(this).slider("value");jQuery(this).find(".ui-slider-handle").text(a),jQuery(this).siblings(".transparency ").css("background-color",t)},value:p,range:"max",step:1,min:0,max:100,animate:300},u.slider(f),"true"==o&&u.find(".ui-slider-handle").addClass("show-opacity"),d.find(".min-click-zone").on("click",function(){i(0,e,u,!0)}),d.find(".max-click-zone").on("click",function(){i(100,e,u,!0)}),d.find(".iris-palette").on("click",function(){var i,t;i=jQuery(this).css("background-color"),t=r(i),a(t,u),100!=t&&(i=i.replace(/[^,]+(?=\))/,(t/100).toFixed(2))),e.wpColorPicker("color",i)}),d.find(".button.wp-picker-default").on("click",function(){var i=r(c);a(i,u)}),e.on("input",function(){var i=jQuery(this).val(),e=r(i);a(e,u)}),u.slider().on("slide",function(r,a){var t=parseFloat(a.value)/100;i(t,e,u,!1),jQuery(this).find(".ui-slider-handle").text(a.value)})})}});
</script>

<div class="wrap settings_wrap">
	<div class="clear_both"></div>

<!--	<div class="title_line" style="margin-bottom:0px !important">-->
<!--		<div id="icon-options-general" class="icon32"></div>-->
<!--		<div class="view_title">--><?php //echo ($this->is_edit) ? JText::_("COM_ZT_LAYERSLIDER_EDIT_SLIDER") : JText::_("COM_ZT_LAYERSLIDER_NEW_SLIDER"); ?><!--</div>-->
<!--		<a href="--><?php //echo RevSliderGlobals::LINK_HELP_SLIDER;?><!--" class="button-secondary float_right mtop_10 mleft_10" target="_blank">--><?php //JText::_("Help"); ?><!--</a>-->
<!--		<div class="tp-clearfix"></div>-->
<!--	</div>-->

	<div class="rs_breadcrumbs">
		<div class="rs-breadcrumbs-wrapper">
			<a class='breadcrumb-button' href='<?php echo JRoute::_('index.php?option=com_zt_layerslider&view=sliders',false); ?>'><i class="eg-icon-th-large"></i><?php echo  JText::_("All Sliders"); ?></a>
			<a class='breadcrumb-button selected' href="#"><i class="eg-icon-cog"></i><?php echo  JText::_('Slider Settings');?></a>
			<a class='breadcrumb-button' href="<?php echo $linksEditSlides; ?>"><i class="eg-icon-pencil-2"></i><?php echo  JText::_('Slide Editor'); ?></a>
			<div class="tp-clearfix"></div>
		</div>
		<div class="tp-clearfix"></div>
		<div class="rs-mini-toolbar">
			<div class="rs-mini-toolbar-button rs-toolbar-savebtn">
				<a class='button-primary revgreen' href='javascript:void(0)' id="button_save_slider_t" ><i class="rs-icon-save-light" style="display: inline-block;vertical-align: middle;width: 18px;height: 20px;background-repeat: no-repeat;margin-right:10px;margin-left:2px;"></i><span class="mini-toolbar-text"><?php echo  JText::_("Save Settings");?></span></a>
				<span id="loader_update_t" class="loader_round" style="display:none;background-color:#27AE60 !important; color:#fff;padding: 5px 5px 6px 25px;margin-right: 5px;"><?php echo  JText::_("updating...");?> </span>
				<span id="update_slider_success_t" class="success_message"></span>
			</div>
			
			<?php
		if (isset($linksEditSlides)) {
			?>
			<div class="rs-mini-toolbar-button rs-toolbar-slides">
				<a class="button-primary revblue" href="<?php echo $linksEditSlides;?>"  id="link_edit_slides_t"><i class="revicon-pencil-1"></i><span class="mini-toolbar-text"><?php echo  JText::_("Edit Slides");?></span></a>
			</div>
				<?php
		}
		?>
			<div class="rs-mini-toolbar-button  rs-toolbar-preview">
				<a class="button-primary revgray" href="javascript:void(0)"  id="button_preview_slider_t" ><i class="revicon-search-1"></i><span class="mini-toolbar-text"><?php echo  JText::_("Preview");?></span></a>
			</div>
			<div class="rs-mini-toolbar-button  rs-toolbar-delete">
				<a class='button-primary revred' id="button_delete_slider_t"  href='javascript:void(0)' ><i class="revicon-trash"></i><span class="mini-toolbar-text"><?php echo  JText::_("Delete Slider");?></span></a>
			</div>
			<div class="rs-mini-toolbar-button  rs-toolbar-close">
				<a class='button-primary revyellow' id="button_close_slider_edit_t" href='<?php echo JRoute::_('index.php?option=com_zt_layerslider&view=sliders',false);?>'><i class="eg-icon-th-large"></i><span class="mini-toolbar-text"><?php echo  JText::_("All Sliders");?></span></a>
			</div>
		</div>
	</div>
	<script>
		jQuery(document).ready(function() {
			jQuery('.rs-mini-toolbar-button').hover(function() {
				var btn=jQuery(this),
					txt = btn.find('.mini-toolbar-text');
				punchgs.TweenLite.to(txt,0.2,{width:"100px",ease:punchgs.Linear.easeNone,overwrite:"all"});
				punchgs.TweenLite.to(txt,0.1,{autoAlpha:1,ease:punchgs.Linear.easeNone,delay:0.1,overwrite:"opacity"});
			}, function() {
				var btn=jQuery(this),
					txt = btn.find('.mini-toolbar-text');
				punchgs.TweenLite.to(txt,0.2,{autoAlpha:0,width:"0px",ease:punchgs.Linear.easeNone,overwrite:"all"});
			});
			var mtb = jQuery('.rs-mini-toolbar'),
				mtbo = mtb.offset().top;
			jQuery(document).on("scroll",function() {

				if (mtbo-jQuery(window).scrollTop()<35)
					mtb.addClass("sticky");
				else
					mtb.removeClass("sticky");

			});
			jQuery(document).on('keydown', function(event) {
				 if (event.ctrlKey || event.metaKey) {
			        switch (String.fromCharCode(event.which).toLowerCase()) {
			        	case 's':
			            	event.preventDefault();
			            	jQuery('#button_save_slider_t').click();
			            break;
			         }
			    }
			});
		});
	</script>


	<div class="settings_panel">
		<div class="settings_panel_left settings_wrapper">
			<form name="form_slider_main" id="form_slider_main" onkeypress="return event.keyCode != 13;">
				
				<input type="hidden" name="hero_active" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'hero_active', -1); ?>" />
				
				<!-- CONTENT SOURCE -->
				<div class="setting_box">
					<h3><span class="setting-step-number">1</span><span><?php echo  JText::_("Content Source")?></span></h3>
					<div class="inside" style="padding:0px;">
						<div class="source-selector-wrapper">
							<?php $source_type = ZTSliderFunctions::getVal($this->arrFieldsParams, 'source_type', 'gallery');?>
							<span class="rs-source-selector selected">
								<span class="rs-source-image rssi-default"></span>
								<input type="radio" id="source_type_3" value="gallery" name="source_type" <?php echo ZTSliderFunctions::checked($source_type, 'gallery');?> />
								<span class="rs-source-label"><?php echo  JText::_('Default Slider');?></span>
							</span>
							<span class="tp-clearfix"></span>
						</div>

						<script>
						   jQuery(document).on("ready", function() {
						   		 function rsSelectorFun() {
						   		 	jQuery('.rs-source-selector').removeClass("selected");
						   		 	jQuery('.source-selector-wrapper input:checked').closest(".rs-source-selector").addClass("selected");
						   		 }
						   		jQuery('.source-selector-wrapper input').change(rsSelectorFun);
						   		rsSelectorFun();

								/*jQuery('.rs-coming-soon').click(function(){
									alert('<?php echo  JText::_('Coming Soon!'); ?>');
								});*/
						   })
						</script>
					</div>
				</div>



				<!-- SLIDER TITLE AND SHORTCODE -->
				<div class="setting_box" style="background:#fff">
					<h3><span class="setting-step-number">2</span><span><?php echo  JText::_("Slider Title & ShortCode")?></span></h3>
					<div class="inside">
						<div class="slidertitlebox">

							<span class="one-third-container">
								<input placeholder='<?php echo  JText::_("Enter your Slider Name here")?>' type="text" class='regular-text' id="title" name="title" value="<?php echo (!empty($this->item->title)) ? $this->item->title : '' ;?>"/>
								<i class="input-edit-icon"></i>
								<span class="description"><?php echo  JText::_("The title of the slider, example: Slider 1")?></span>
							</span>

							<span class="one-third-container">
								<input placeholder='<?php echo  JText::_("Enter your Slider Alias here")?>' type="text" class='regular-text' id="alias" name="alias" value="<?php echo (!empty($this->item->alias)) ? $this->item->alias :'';?>"/>
								<i class="input-edit-icon"></i>
								<span class="description"><?php echo  JText::_("The alias for embedding your slider, example: slider1")?></span>
							</span>

							<span class="one-third-container">
								<input type="text" class='regular-text code' readonly='readonly' id="shortcode" name="shortcode" value="" />
								<i class="input-shortcode-icon"></i>
								<span class="description"><?php echo  JText::_("Place the shortcode where you want to show the slider")?></span>
							</span>
						</div>


					</div>
				</div>


				<!-- THE SLIDE TYPE CHOOSER -->
				<div class="setting_box">
					<?php
					$slider_type = ZTSliderFunctions::getVal($this->arrFieldsParams, 'slider-type', 'standard');
					?>
					<h3><span class="setting-step-number">3</span><span><?php echo  JText::_("Select a Slider Type");?></span></h3>
					<div class="rs-slidetypeselector">

						<div data-mode="standardpreset" class="rs-slidertype<?php echo ($slider_type == 'standard') ? ' selected' : '';?>">
							<span class="rs-preset-image standardslider"></span>
							<span class="rs-preset-label"><?php echo  JText::_("Standard Slider");?></span>
							<input style="display: none;" type="radio" name="slider-type" value="standard" <?php echo ZTSliderFunctions::checked($slider_type, 'standard');?> />
						</div>
						<div data-mode="heropreset" class="rs-slidertype<?php echo ($slider_type == 'hero') ? ' selected' : '';?>">
							<span class="rs-preset-image heroscene"></span>
							<span class="rs-preset-label"><?php echo  JText::_("Hero Scene");?></span>
							<input style="display: none;" type="radio" name="slider-type" value="hero" <?php echo ZTSliderFunctions::checked($slider_type, 'hero');?> />
						</div>
						<div data-mode="carouselpreset" class="rs-slidertype<?php echo ($slider_type == 'carousel') ? ' selected' : '';?>">
							<span class="rs-preset-image carouselslider"></span>
							<span class="rs-preset-label"><?php echo  JText::_("Carousel Slider");?></span>
							<input style="display: none;" type="radio" name="slider-type" value="carousel" <?php echo ZTSliderFunctions::checked($slider_type, 'carousel');?> />
						</div>
					</div>
					<span class="preset-splitter readytoopen"><?php echo  JText::_("Load a Preset from this Slider Type");?><i class="eg-icon-down-open"></i></span>
					<div id="preset-selector-wrapper" class="preset-selector-wrapper" style="display:none">
						<div id="preselect-horiz-wrapper" class="preselect-horiz-wrapper">
							<?php
							$presets = ZTSliderOperations::get_preset_settings($this->item->attribs);
							
							echo '<span class="rs-preset-selector rs-do-nothing standardpreset heropreset carouselpreset" id="rs-add-new-settings-preset">';
							echo '<span class="rs-preset-image" style="background-image: url('.JUri::root().'/administrator/components/com_zt_layerslider/assets/images/mainoptions/add_preset.png);"></span>';
							echo '	<span class="rs-preset-label">' . JText::_('Save Current Settings as Preset') . '</span>';
							echo '</span>';
							
							echo '<script type="text/javascript">';
							//$pjs = ZTSliderFunctions::jsonEncodeForClientSide($pjs);
							$pjs = ZTSliderFunctions::jsonEncodeForClientSide($presets);
							echo 'var revslider_presets = jQuery.parseJSON(' . $pjs . ');';
							echo '</script>';
							?>
							<span class="tp-clearfix"></span>
						</div>
					 </div>
					 <script type="text/javascript">
					 	jQuery("document").ready(function() {

					 		jQuery('.preset-splitter').click(function() {
					 			jQuery('#preset-selector-wrapper').slideDown(200);
					 			jQuery(this).removeClass('readytoopen');
					 		})
							var preset_template_container = UniteLayersRev.wptemplate( "rs-preset-container" );
							
							function rs_reset_preset_html(){
								
								jQuery('.rs-preset-entry').remove();
								
								for(var key in revslider_presets){
									
									if (typeof(revslider_presets[key]['settings']['preset']) === 'undefined') {
										revslider_presets[key]['settings']['preset'] = 'standardpreset';
									}
									
									var data = {};
									data['key'] = key;
									data['name'] = revslider_presets[key]['settings']['name'];
									data['type'] = revslider_presets[key]['settings']['preset'];
									data['img'] = (typeof(revslider_presets[key]['settings']['image']) !== 'undefined') ? revslider_presets[key]['settings']['image'] : '';
									data['class'] = (typeof(revslider_presets[key]['settings']['class']) !== 'undefined') ? revslider_presets[key]['settings']['class'] : '';
									data['custom'] = (typeof(revslider_presets[key]['settings']['custom']) !== 'undefined' && revslider_presets[key]['settings']['custom'] == true) ? true : false;
									
									var content = preset_template_container(data);
									
									jQuery('#rs-add-new-settings-preset').before(content);
								}
								
								jQuery('.rs-slidertype.selected').click(); //show only for current active type
							}
							rs_reset_preset_html();
							
					 		function updateSliderPresets() {
					 			var bt = jQuery('.rs-slidertype.selected'),
					 				sw  =jQuery('#preselect-horiz-wrapper'),
					 				swp = jQuery('#preset-selector-wrapper'),
					 				mode = bt.data('mode'),
					 				prewi = (swp.width()-2)/4;
					 				if (prewi<200) prewi = ((swp.width()-1)/3);

					 				preitems = jQuery('.rs-preset-selector.'+mode);

					 			jQuery('.rs-preset-selector').removeClass("selected").hide().css({width:prewi+"px"});
								preitems.show();

								
								//if (preitems.length<7) {
									sw.css({position:"relative",height:"auto",width:"100%"});
									swp.css({position:"relative",height:"auto"});


								//} else {
									
								//	sw.css({position:"absolute",height:"400px",width:(prewi*Math.ceil(preitems.length/2))+"px"});
								//	swp.css({position:"relative",height:"400px"});
								//}
								jQuery('.preset-selector-wrapper').perfectScrollbar('update');
								
								switch(mode) {
									case "standardpreset":											
										jQuery('.dontshowonhero').show();
										jQuery('.dontshowonstandard').hide();
									break;								
									case "carouselpreset":
										jQuery('.dontshowonhero').show();
										jQuery('.dontshowonstandard').show();
									break;
									case "heropreset":
										jQuery('.dontshowonhero').hide();
									break;
								}

					 		}
							
					 		jQuery('body').on("click",'.rs-slidertype',function() {
								var bt = jQuery(this);
								jQuery('.rs-slidertype').removeClass("selected");
								bt.addClass("selected").find('input[name="slider-type"]').attr('checked', 'checked');
								updateSliderPresets();
							});

					 		jQuery('.preset-selector-wrapper').perfectScrollbar({});
					 		updateSliderPresets();
					 		jQuery(window).resize(updateSliderPresets);

							
							jQuery('body').on('mouseover', '.rs-preset-selector', function(){
								jQuery(this).find('.rev-remove-preset').show();
								jQuery(this).find('.rev-update-preset').show();
							});
							jQuery('body').on('mouseleave', '.rs-preset-selector', function(){
								jQuery(this).find('.rev-remove-preset').hide();
								jQuery(this).find('.rev-update-preset').hide();
							});
							
							var googlef_template_container = UniteLayersRev.wptemplate( "rs-preset-googlefont" );
							
							jQuery('body').on('click', '.rs-preset-selector', function(){
								if(typeof(jQuery(this).attr('id')) == 'undefined' || jQuery(this).hasClass('rs-do-nothing')) return false;
								var preset_id = jQuery(this).attr('id').replace('rs-preset-' , '');

								showWaitAMinute({fadeIn:300,text:rev_lang.preset_loaded});

								if(typeof(revslider_presets[preset_id]) !== 'undefined'){
									
									for(var key in revslider_presets[preset_id]['values']){
										var entry = jQuery('[name="'+key+'"]');

										if(key == 'google_font'){
											jQuery('#rs-google-fonts').html('');
											
											for(var gfk in revslider_presets[preset_id]['values'][key]){
												jQuery('#rs-google-fonts').append(googlef_template_container({'value':revslider_presets[preset_id]['values'][key][gfk]}));
											}
											
										}

										if(entry.length == 0) continue;

										switch(entry.prop('tagName').toLowerCase()){
											case 'input':
												switch(entry.attr('type')){
													case 'radio':
														jQuery('[name="'+key+'"][value="'+revslider_presets[preset_id]['values'][key]+'"]').click();
													break;
													case 'checkbox':
														if(revslider_presets[preset_id]['values'][key] == 'on')
															entry.attr('checked', true);
														else
															entry.attr('checked', false);

														ZtSliderSettings.onoffStatus(entry);
													break;
													default:
														entry.val(revslider_presets[preset_id]['values'][key]);
													break;
												}
											break;
											case 'select':
												jQuery('[name="'+key+'"] option[value="'+revslider_presets[preset_id]['values'][key]+'"]').attr('selected', true);
											break;
											default:
												switch(key){
													case 'custom_css':
														if(typeof rev_cm_custom_css !== 'undefined')
															rev_cm_custom_css.setValue(UniteAdminRev.stripslashes(revslider_presets[preset_id]['values'][key]));
													break;
													case 'custom_javascript':
														if(typeof rev_cm_custom_js !== 'undefined')
															rev_cm_custom_js.setValue(UniteAdminRev.stripslashes(revslider_presets[preset_id]['values'][key]));
													break;
													default:
														jQuery('[name="'+key+'"]').val(revslider_presets[preset_id]['values'][key]);
													break;
												}
											break;
										}

										entry.change(); //trigger change call for elements to hide/show dependencies
									}
									
									if(typeof rev_cm_custom_css !== 'undefined') rev_cm_custom_css.refresh();
									if(typeof rev_cm_custom_js !== 'undefined') rev_cm_custom_js.refresh();
								}

								setTimeout('showWaitAMinute({fadeOut:300})',400);
							});

							
							function get_preset_params(){
								var params = ZtSliderSettings.getSettingsObject('form_slider_params');
								delete params.action;
								delete params['0'];
								
								var ecsn = (jQuery('input[name="enable_custom_size_notebook"]').is(':checked')) ? 'on' : 'off';
								var ecst = (jQuery('input[name="enable_custom_size_tablet"]').is(':checked')) ? 'on' : 'off';
								var ecsi = (jQuery('input[name="enable_custom_size_iphone"]').is(':checked')) ? 'on' : 'off';
								var mof = (jQuery('input[name="main_overflow_hidden"]').is(':checked')) ? 'on' : 'off';
								var ah = (jQuery('input[name="auto_height"]').is(':checked')) ? 'on' : 'off';
								
								var params2 = {
									slider_type: jQuery('input[name="slider_type"]:checked').val(),
									width: jQuery('input[name="width"]').val(),
									width_notebook: jQuery('input[name="width_notebook"]').val(),
									width_tablet: jQuery('input[name="width_tablet"]').val(),
									width_mobile: jQuery('input[name="width_mobile"]').val(),
									height: jQuery('input[name="height"]').val(),
									height_notebook: jQuery('input[name="height_notebook"]').val(),
									height_tablet: jQuery('input[name="height_tablet"]').val(),
									height_mobile: jQuery('input[name="height_mobile"]').val(),
									enable_custom_size_notebook: ecsn,
									enable_custom_size_tablet: ecst,
									enable_custom_size_iphone: ecsi,
									
									main_overflow_hidden: mof,
									auto_height: ah,
									min_height: jQuery('input[name="min_height"]').val(),
								};
								
								if(typeof rev_cm_custom_js !== 'undefined')
									params2.custom_javascript = rev_cm_custom_js.getValue();
								
								if(typeof rev_cm_custom_css !== 'undefined')
									params2.custom_css = rev_cm_custom_css.getValue();
								
								jQuery.extend(params, params2);
								
								
								return params;
								
							}
							
							
							jQuery('body').on('click', '.rev-update-preset', function(){
								if(confirm(rev_lang.update_preset)){
									
									var pr_id = jQuery(this).closest('.rs-preset-entry').attr('id').replace('rs-preset-', '');
									
									if(typeof(revslider_presets[pr_id]) == 'undefined') alert(rev_lang.preset_not_found);
									
									var params = get_preset_params();
									
									var update_preset = {name: revslider_presets[pr_id]['settings']['name'], values: params};
									
									UniteAdminRev.ajaxRequest('slider.updatepreset', update_preset, function(response){
										if(response.success == true){
											//refresh presets
											revslider_presets = response.data;
											
											rs_reset_preset_html();
										}
									});
								}
								
								return false;
							});

							
							jQuery('#rs-add-new-settings-preset').click(function(){
								jQuery('input[name="rs-preset-name"]').val('');
								jQuery('input[name="rs-preset-image-id"]').val('');
								jQuery('#rs-preset-img-wrapper').css('background-image', '');
								
								jQuery('#dialog-rs-add-new-setting-presets').dialog({
									modal:true,
									resizable:false,
									minWidth:400,
									minHeight:300,
									closeOnEscape:true,
									buttons:{
										'<?php echo  JText::_('Save Settings'); ?>':function(){
											var preset_name = UniteAdminRev.sanitize_input(jQuery('input[name="rs-preset-name"]').val());
											var preset_img = jQuery('input[name="rs-preset-image-id"]').val();
											jQuery('input[name="rs-preset-name"]').val(preset_name);
											
											if(preset_name == '') return false;
											
											for(var key in revslider_presets){
												if(revslider_presets[key]['settings']['name'] == preset_name){
													alert(rev_lang.preset_name_already_exists);
													return false;
												}
											}
											var c_type = jQuery('.rs-slidertype.selected').data('mode');
											
											var params = get_preset_params();
											
											var new_preset = {
												settings: {'class':'', image:preset_img, name:preset_name, preset:c_type},
												values: params
											};
										
											
											//add new preset to the list
											UniteAdminRev.ajaxRequest('slider.addnewpreset', new_preset, function(response){
												if(response.success == true){
													//refresh presets
													revslider_presets = response.data;
													
													rs_reset_preset_html();
												}
												
												jQuery('#dialog-rs-add-new-setting-presets').dialog('close');
											});
											
										}
									}
								});
								
								
							});

							jQuery('input[name="rs-button-select-img"]').click(function(){
								jQuery('#rs-preset-img-wrapper').css('background-image', '');
								jQuery('input[name="rs-preset-image-id"]').val('');
								
								UniteAdminRev.openAddImageDialog(rev_lang.select_image,function(urlImage,imageID,width,height){
									var data = {url_image:urlImage,image_id:imageID,img_width:width,img_height:height};
									
									jQuery('input[name="rs-preset-image-id"]').val(data.image_id);
									
									jQuery('#rs-preset-img-wrapper').css('background-image', ' url('+data.url_image+')');
									
									var mw = 200;
									var mh = height / width * 200;
									
									jQuery('#rs-preset-img-wrapper').css('width', mw+'px');
									jQuery('#rs-preset-img-wrapper').css('height', mh+'px');
									jQuery('#rs-preset-img-wrapper').css('background-size', 'cover');
								});

							});
							
							jQuery('body').on('click', '.rev-remove-preset', function(){
								if(confirm(rev_lang.delete_preset)){
									
									var pr_id = jQuery(this).closest('.rs-preset-entry').attr('id').replace('rs-preset-', '');
									
									if(typeof(revslider_presets[pr_id]) == 'undefined') alert(rev_lang.preset_not_found);
									
									UniteAdminRev.ajaxRequest('slider.removepreset', {name: revslider_presets[pr_id]['settings']['name']}, function(response){
										revslider_presets = response.data;
										
										rs_reset_preset_html();
									});
								}
								
								return false;
							});
							
					 	});
					 </script>
				</div>

				<!-- SLIDE LAYOUT -->

				<?php
				$width_notebook = ZTSliderFunctions::getVal($this->arrFieldsParams, "width_notebook", $_width_notebook);
				$height_notebook = ZTSliderFunctions::getVal($this->arrFieldsParams, "height_notebook");
				if (intval($height_notebook) == 0) {
					$height_notebook = 768;
				}

				$width = ZTSliderFunctions::getVal($this->arrFieldsParams, "width", $_width);
				$height = ZTSliderFunctions::getVal($this->arrFieldsParams, "height", 868);

				$width_tablet = ZTSliderFunctions::getVal($this->arrFieldsParams, "width_tablet", $_width_tablet);
				if (intval($width_tablet) == 0) {
					$width_tablet = $_width_tablet;
				}

				$height_tablet = ZTSliderFunctions::getVal($this->arrFieldsParams, "height_tablet");
				if (intval($height_tablet) == 0) {
					$height_tablet = 960;
				}
				$width_mobile = ZTSliderFunctions::getVal($this->arrFieldsParams, "width_mobile", $_width_mobile);

				if (intval($width_mobile) == 0) {
					$width_mobile = $_width_mobile;
				}

				$height_mobile = ZTSliderFunctions::getVal($this->arrFieldsParams, "height_mobile");
				if (intval($height_mobile) == 0) {
					$height_mobile = 720;
				}

				$advanced_sizes = ZTSliderFunctions::getVal($this->arrFieldsParams, "advanced-responsive-sizes", 'false');
				$advanced_sizes = ZTSliderFunctions::strToBool($advanced_sizes);

				$sliderType = ZTSliderFunctions::getVal($this->arrFieldsParams, "slider_type");

				?>

				<div class="setting_box" id="rs-slider-layout-cont">
					<h3><span class="setting-step-number">4</span><span><?php echo  JText::_("Slide Layout");?></span></h3>
					<div class="inside" style="padding:0px">

						<?php $slider_type = ZTSliderFunctions::getVal($this->arrFieldsParams, 'slider_type', 'fullwidth');?>
						<div style="background:#eee">
							<div class="rs-slidesize-selector" >

								<div class="rs-slidersize">
									<span class="rs-size-image autosized"></span>
									<span class="rs-preset-label"><?php echo  JText::_('Auto');?></span>
									<input type="radio" id="slider_type_1" value="auto" name="slider_type" <?php echo ZTSliderFunctions::checked($slider_type, 'auto');?> />
								</div>
								<div class="rs-slidersize">
									<span class="rs-size-image fullwidthsized"></span>
									<span class="rs-preset-label"><?php echo  JText::_('Full-Width');?></span>
									<input type="radio" id="slider_type_2" value="fullwidth" name="slider_type" <?php echo ZTSliderFunctions::checked($slider_type, 'fullwidth');?> />
								</div>
								<div class="rs-slidersize selected">
									<span class="rs-size-image fullscreensized"></span>
									<span class="rs-preset-label"><?php echo  JText::_('Full-Screen');?></span>
									<input type="radio" id="slider_type_3" style="margin-left:20px" value="fullscreen" name="slider_type" <?php echo ZTSliderFunctions::checked($slider_type, 'fullscreen');?> />
								</div>
								<div style="clear:both;float:none"></div>
							</div>
						</div>

						<div id="layout-preshow">
							<div class="rsp-desktop-view rsp-view-cell">
								<div class="rsp-view-header">
									<?php echo  JText::_('Desktop Large');?> <span class="rsp-cell-dimension"><?php echo  JText::_('Max');?></span>
								</div>
								<div class="rsp-present-area">
									<div class="rsp-device-imac">
										<div class="rsp-imac-topbar"></div>
										<div class="rsp-bg"></div>
										<div class="rsp-browser">
											<div class="rsp-slide-bg" data-width="140" data-height="70" data-fullwidth="188" data-faheight="70" data-fixwidth="140" data-fixheight="70" data-gmaxw = "140" data-lscale = "1">
												<div class="rsp-grid">
													<div class="rsp-dotted-line-hr-left"></div>
													<div class="rsp-dotted-line-hr-right"></div>
													<div class="rsp-dotted-line-vr-top"></div>
													<div class="rsp-dotted-line-vr-bottom"></div>
													<div class="rsp-layer"><?php echo  JText::_('Layer');?></div>
												</div>
											</div>
										</div>
									</div>
									<div class="rsp-device-imac-bg"></div>
								</div>
								<div class="slide-size-wrapper">
									<span class="rs-preset-label"><?php echo  JText::_('Layer Grid Size');?></span>
									<span class="relpos"><input id="width" name="width" type="text" style="width:120px" class="textbox-small" value="<?php echo $width;?>"><span class="pxfill">px</span></span>
									<span class="rs-preset-label label-multiple">x</span>
									<span class="relpos"><input id="height" name="height" type="text" style="width:120px" class="textbox-small" value="<?php echo $height;?>"><span class="pxfill">px</span></span>
									<span class="tp-clearfix" style="margin-bottom:15px"></span>
									<span class="description"><?php echo  JText::_('Specify a layer grid size above');?></span>
									<span class="description" style="padding:20px 20px 0px; box-sizing:border-box;-moz-box-sizing:border-box;"><?php echo  JText::_('Slider is always Linear Responsive till next Defined Grid size has been hit.');?></span>
								</div>
							</div>
							<div class="rsp-macbook-view rsp-view-cell">
								<div class="rsp-view-header">
									<?php echo  JText::_('Notebook');?> <span class="rsp-cell-dimension"><?php echo $_width_notebook;?>px</span>
								</div>
								<div class="rsp-present-area">
									<div class="rsp-device-macbook">
										<div class="rsp-macbook-topbar"></div>
										<div class="rsp-bg"></div>
										<div class="rsp-browser">
											<div class="rsp-slide-bg" data-width="140" data-height="60" data-fullwidth="160" data-faheight="60" data-fixwidth="140" data-fixheight="60" data-gmaxw = "140" data-lscale = "0.8">
												<div class="rsp-grid">
													<div class="rsp-dotted-line-hr-left"></div>
													<div class="rsp-dotted-line-hr-right"></div>
													<div class="rsp-dotted-line-vr-top"></div>
													<div class="rsp-dotted-line-vr-bottom"></div>
													<div class="rsp-layer"><?php echo  JText::_('Layer');?></div>
												</div>
											</div>
										</div>
									</div>
									<div class="rsp-device-macbook-bg"></div>
								</div>
								<div class="slide-size-wrapper">
									<span class="rs-preset-label"><?php echo  JText::_('Layer Grid Size');?></span>
									<span class="rs-width-height-wrapper">
										<span class="relpos"><input name="width_notebook" type="text" style="width:120px" class="textbox-small" value="<?php echo $width_notebook;?>"><span class="pxfill">px</span></span>
										<span class="rs-preset-label label-multiple">x</span>
										<span class="relpos"><input name="height_notebook" type="text" style="width:120px" class="textbox-small" value="<?php echo $height_notebook;?>"><span class="pxfill">px</span></span>
									</span>
									<span class="rs-width-height-alternative" style="display:none">
										<span class="rs-preset-label"><?php echo  JText::_('Auto Sizes');?></span>
									</span>
									<span class="tp-clearfix" style="margin-bottom:15px"></span>
									<span class="rs-preset-label" style="display:inline-block; margin-right:15px;"><?php echo  JText::_('Custom Grid Size');?></span>
									<span style="text-align:left">
										<input type="checkbox"  class="tp-moderncheckbox" id="enable_custom_size_notebook" name="enable_custom_size_notebook" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, 'enable_custom_size_notebook', 'off'), "on");?>>
									</span>
									<span class="description" style="padding:0px 20px 0px; box-sizing:border-box;-moz-box-sizing:border-box;"><?php echo  JText::_('<br>If not defined, the next bigger Layer Grid Size is the basic of Linear Responsive calculations.');?></span>
								</div>
							</div>
							<div class="rsp-tablet-view rsp-view-cell">
								<div class="rsp-view-header">
									<?php echo  JText::_('Tablet');?> <span class="rsp-cell-dimension"><?php echo $_width_tablet;?>px</span>
								</div>
								<div class="rsp-present-area">
									<div class="rsp-device-ipad">
										<div class="rsp-bg"></div>
										<div class="rsp-browser">
											<div class="rsp-slide-bg" data-width="126" data-height="60" data-fullwidth="138" data-faheight="130" data-fixwidth="140" data-fixheight="70" data-gmaxw = "126" data-lscale = "0.7">
												<div class="rsp-grid">
													<div class="rsp-dotted-line-hr-left"></div>
													<div class="rsp-dotted-line-hr-right"></div>
													<div class="rsp-dotted-line-vr-top"></div>
													<div class="rsp-dotted-line-vr-bottom"></div>
													<div class="rsp-layer"><?php echo  JText::_('Layer');?></div>
												</div>
											</div>
										</div>
									</div>
									<div class="rsp-device-ipad-bg"></div>
								</div>
								<div class="slide-size-wrapper">
									<span class="rs-preset-label"><?php echo  JText::_('Layer Grid Size');?></span>
									<span class="rs-width-height-wrapper">
										<span class="relpos"><input name="width_tablet" type="text" style="width:120px" class="textbox-small" value="<?php echo $width_tablet;?>"><span class="pxfill">px</span></span>
										<span class="rs-preset-label label-multiple">x</span>
										<span class="relpos"><input name="height_tablet" type="text" style="width:120px" class="textbox-small" value="<?php echo $height_tablet;?>"><span class="pxfill">px</span></span>
									</span>
									<span class="rs-width-height-alternative" style="display:none">
										<span class="rs-preset-label"><?php echo  JText::_('Auto Sizes');?></span>
									</span>
									<span class="tp-clearfix" style="margin-bottom:15px"></span>
									<span class="rs-preset-label" style="display:inline-block; margin-right:15px;"><?php echo  JText::_('Custom Grid Size');?></span>
									<span style="text-align:left">
										<input type="checkbox"  class="tp-moderncheckbox" id="enable_custom_size_tablet" name="enable_custom_size_tablet" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, 'enable_custom_size_tablet', 'off'), "on");?>>
									</span>
									<span class="description" style="padding:0px 20px 0px; box-sizing:border-box;-moz-box-sizing:border-box;"><?php echo  JText::_('<br>If not defined, the next bigger Layer Grid Size is the basic of Linear Responsive calculations.');?></span>
								</div>

							</div>
							<div class="rsp-mobile-view rsp-view-cell">
								<div class="rsp-view-header">
									<?php echo  JText::_('Mobile');?> <span class="rsp-cell-dimension"><?php echo $_width_mobile;?>px</span>
								</div>
								<div class="rsp-present-area">
									<div class="rsp-device-iphone">
										<div class="rsp-bg"></div>
										<div class="rsp-browser">
											<div class="rsp-slide-bg" data-width="70" data-height="40" data-fullwidth="80" data-faheight="100" data-fixwidth="140" data-fixheight="70" data-gmaxw = "70" data-lscale = "0.4">
												<div class="rsp-grid">
													<div class="rsp-dotted-line-hr-left"></div>
													<div class="rsp-dotted-line-hr-right"></div>
													<div class="rsp-dotted-line-vr-top"></div>
													<div class="rsp-dotted-line-vr-bottom"></div>
													<div class="rsp-layer"><?php echo  JText::_('Layer');?></div>
												</div>
											</div>
										</div>
									</div>

									<div class="rsp-device-iphone-bg"></div>
								</div>
								<div class="slide-size-wrapper">
									<span class="rs-preset-label"><?php echo  JText::_('Layer Grid Size');?></span>
									<span class="rs-width-height-wrapper">
										<span class="relpos"><input name="width_mobile" type="text" style="width:120px" class="textbox-small" value="<?php echo $width_mobile;?>"><span class="pxfill">px</span></span>
										<span class="rs-preset-label label-multiple">x</span>
										<span class="relpos"><input name="height_mobile" type="text" style="width:120px" class="textbox-small" value="<?php echo $height_mobile;?>"><span class="pxfill">px</span></span>
									</span>
									<span class="rs-width-height-alternative" style="display:none">
										<span class="rs-preset-label"><?php echo  JText::_('Auto Sizes');?></span>
									</span>
									<span class="tp-clearfix" style="margin-bottom:15px"></span>
									<span class="rs-preset-label" style="display:inline-block; margin-right:15px;"><?php echo  JText::_('Custom Grid Size');?></span>
									<span style="text-align:left">
										<input type="checkbox"  class="tp-moderncheckbox" id="enable_custom_size_iphone" name="enable_custom_size_iphone" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, 'enable_custom_size_iphone', 'off'), "on");?>>
									</span>
									<span class="description" style="padding:0px 20px 0px; box-sizing:border-box;-moz-box-sizing:border-box;"><?php echo  JText::_('<br>If not defined, the next bigger Layer Grid Size is the basic of Linear Responsive calculations.');?></span>
								</div>
							</div>
							<div style="clear:both;float:none"></div>
						</div>
						<div class="buttonarea" id="removethisbuttonarea">
							<a class="button-primary revblue" id="show_advanced_navigation" original-title=""><i class="revicon-cog"></i><?php echo  JText::_("Show Advanced Size Options");?></a>
						</div>

						<!-- VISUAL ADVANCED SIZING -->
						<div class="inside" id="visual-sizing" style="display:none; padding:25px 20px;">
							<div id="fullscreen-advanced-sizing">
								<span class="one-half-container" style="vertical-align:top">
									
									<span class="rs-preset-label noopacity "><?php echo  JText::_("Minimal Height of Slider (Optional)");?></span>
									<span style="clear:both;float:none; height:25px;display:block"></span>
								
									<span style="text-align:left; display:none;">
										<span class="rs-preset-label noopacity " style="display:inline-block;margin-right:20px"><?php echo  JText::_("FullScreen Align Force");?> </span>
										<input type="checkbox"  class="tp-moderncheckbox withlabel" id="full_screen_align_force" name="full_screen_align_force" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, 'full_screen_align_force', 'off'), "on");?>>
										<span class="description"><?php echo  JText::_("Layers align within the full slider instead of the layer grid.");?></span>
									</span>
									
									<span class="slidertitlebox limitedtablebox">
										<span class="one-half-container">
											<input placeholder="<?php echo  JText::_("Min. Height");?>" type="text" class="text-sidebar" id="fullscreen_min_height" name="fullscreen_min_height" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "fullscreen_min_height", "");?>">
											<i class="input-edit-icon"></i>
											<span class="description"><?php echo  JText::_("The minimum height of the Slider in FullScreen mode.");?></span>
										</span>
									</span>
									<span style="clear:both;float:none; height:25px;display:block"></span>

									<span style="text-align:left; padding:0px 20px;">
										<span class="rs-preset-label noopacity" style="display:inline-block;margin-right:20px"><?php echo  JText::_("Disable Force FullWidth");?> </span>
										<input type="checkbox"  class="tp-moderncheckbox " id="autowidth_force" name="autowidth_force" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, 'autowidth_force', 'off'), "on");?>>
										<span class="description" style="padding:0px 20px;"><?php echo  JText::_("Disable the FullWidth Force function, and allow to float the Fullheight slider horizontal.");?></span>
									</span>

								</span>

								<span class="one-half-container" style="vertical-align:top">
									<span class="rs-preset-label noopacity "><?php echo  JText::_("Increase/Decrease Fullscreen Height (Optional)");?></span>
									<span style="clear:both;float:none; height:25px;display:block"></span>

									<span class="slidertitlebox limitedtablebox">
										<span class="one-full-container">
											<input placeholder="<?php echo  JText::_("Containers");?>" type="text" class="text-sidebar" id="fullscreen_offset_container" name="fullscreen_offset_container" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "fullscreen_offset_container", "");?>">
											<i class="input-edit-icon"></i>
											<span class="description"><?php echo  JText::_("Example: #header or .header, .footer, #somecontainer | Height of Slider will be decreased with the height of these Containers to fit perfect in the screen.");?></span>
										</span>
									</span>
									<span style="clear:both;float:none; height:25px;display:block"></span>
									<span class="slidertitlebox limitedtablebox">
										<span class="one-full-container">
											<input placeholder="<?php echo  JText::_("PX or %");?>" type="text" class="text-sidebar" id="fullscreen_offset_size" name="fullscreen_offset_size" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "fullscreen_offset_size", "");?>">
											<i class="input-edit-icon"></i>
											<span class="description"><?php echo  JText::_("Decrease/Increase height of Slider. Can be used with px and %. Positive/Negative values allowed. Example: 40px or 10%");?></span>
										</span>
									</span>
									<span style="clear:both;float:none; height:25px;display:block"></span>
									
								</span>
							</div>
							<div id="normal-advanced-sizing">

								<div class="slidertitlebox limitedtablebox" style="width:100%; max-width:100%">
									<span class="one-third-container" style="vertical-align:top">
										<span class="rs-preset-label noopacity" style="margin-top:12px;display:inline-block;margin-right:20px" ><?php echo  JText::_("Overflow Hidden");?> </span>
										<input type="checkbox"  class="tp-moderncheckbox" id="main_overflow_hidden" name="main_overflow_hidden" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, 'main_overflow_hidden', 'off'), "on");?>>
										<div style="clear:both;float:none; height:25px"></div>
										<span class="description"><?php echo  JText::_("Adds overflow:hidden to the slider wrapping container which will hide / cut any overlapping elements. Mostly used in Carousel Sliders.");?></span>
									</span>

									<span class="one-third-container" style="vertical-align:top">
										<span class="rs-preset-label noopacity" style="margin-top:12px;display:inline-block;margin-right:20px" ><?php echo  JText::_("Respect Aspect Ratio");?> </span>
										<input type="checkbox"  class="tp-moderncheckbox" id="auto_height" name="auto_height" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, 'auto_height', 'off'), "on");?>>
										<div style="clear:both;float:none; height:25px"></div>
										<span class="description"><?php echo  JText::_("It will keep aspect ratio and ignore max height of Layer Grid by upscaling. Layer Area will be vertical centered.");?></span>
									</span>
									
									<span class="one-third-container" style="vertical-align:top">
										<input placeholder="<?php echo  JText::_("Min. Heigh (Optional)");?>" type="text" class="text-sidebar" style="line-height:26px" id="min_height" name="min_height" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "min_height", "");?>">
										<i class="input-edit-icon"></i>
										<span class="description"><?php echo  JText::_("The minimum height of the Slider in FullWidth or Auto mode.");?></span>
										<span class="rs-show-on-auto">
											<input placeholder="<?php echo  JText::_("Max. Width (Optional)");?>" type="text" class="text-sidebar" style="padding:11px 45px 11px 15px; margin-top: 20px; line-height:26px" id="max_width" name="max_width" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "max_width", "");?>">
											<i class="input-edit-icon" style="top: 99px;"></i>
											<span class="description"><?php echo  JText::_("The maximum width of the Slider in Auto mode.");?></span>
										</span>
									</span>
								</div>
							</div>
							
						</div>		<!-- / VISUAL ADVANCED SIZING -->

						<script>
							jQuery("document").ready(function() {

								jQuery('#show_advanced_navigation').click(function() {
									var a = jQuery('#visual-sizing');
									a.slideDown(200);
									jQuery('#removethisbuttonarea').remove();
								})

								jQuery('#show_advanced_navigation').click();

								jQuery('input[name="slider_type"]').on("change",function() {
									var s_fs = jQuery('#slider_type_3').attr("checked")==="checked";
									if (s_fs) {
										jQuery('#normal-advanced-sizing').hide();
										jQuery('#fullscreen-advanced-sizing').show();
									} else {
										jQuery('#normal-advanced-sizing').show();
										jQuery('#fullscreen-advanced-sizing').hide();
									}
									
									var s_fs = jQuery('#slider_type_1').attr("checked")==="checked";
									if (s_fs) {
										jQuery('.rs-show-on-auto').show();
									}else{
										jQuery('.rs-show-on-auto').hide();
									}
								});

								jQuery('#slider_type_3').change();

								function get_preview_resp_sizes() {
									var s = new Object();

										s.n  = jQuery('#enable_custom_size_notebook').attr('checked')==="checked";
										s.t  = jQuery('#enable_custom_size_tablet').attr('checked')==="checked";
										s.m  = jQuery('#enable_custom_size_iphone').attr('checked')==="checked";

										

										s.w_d = jQuery('input[name="width"]');
										s.w_n = s.n ? jQuery('input[name="width_notebook"]') : s.w_d;
										s.w_t = s.t ? jQuery('input[name="width_tablet"]') : s.n ? s.w_n : s.w_d;
										s.w_m = s.m ? jQuery('input[name="width_mobile"]') : s.t ? s.w_t : s.n ? s.w_n : s.w_d;

										s.h_d = jQuery('input[name="height"]');
										s.h_n = s.n ? jQuery('input[name="height_notebook"]') : s.h_d;
										s.h_t = s.t ? jQuery('input[name="height_tablet"]') : s.n ? s.h_n : s.h_d;
										s.h_m = s.m ? jQuery('input[name="height_mobile"]') : s.t ? s.h_t : s.n ? s.h_n : s.h_d;
									return s;
								}

								function draw_responsive_previews(s) {
									var  d_gw = (s.w_d.val() / 1400) * 150;
									//jQuery('.rsp-desktop-view .rsp-grid').css({width})

								}

								function setLayoutDesign(element,bw,bh,sw,sh,bm,ww,mm,fh,tt) {
									var o = new Object();
									o.mtp = 1;

									if (bw>sw) {
										bh = bh *  sw/bw;
										o.mtp = sw/bw;
										bw = sw;
									}
									if (bh>sh) {
										bw = bw *  sh/bh;
										o.mtp = sh/bh;
										bh = sh;
									}

									o.tt = tt;

									o.left = o.right = (2 + ((1 - ( bw / sw))*(sw/10))/2) * bm,
									o.height =   (bh/10)*bm;
									o.mt = (((sh/10) - o.height)/20)*bm;

									if (fh===1)
										o.gridtop = (((sh*bm/10) - o.height)/2);
									else {
										o.gridtop = 0;

										if (jQuery('#auto_height').attr("checked")==="checked")
											o.gridtop = Math.abs((o.height - (o.height * (sw/bw))))/2;
									}

									punchgs.TweenLite.to(element.find('.rsp-grid'),0.3,{top:o.gridtop,height:o.height,left:mm,ease:punchgs.Power3.easeInOut});
									punchgs.TweenLite.to(element.find('.rsp-dotted-line-hr-left'),0.3,{left:o.left,ease:punchgs.Power3.easeInOut});
									punchgs.TweenLite.to(element.find('.rsp-dotted-line-hr-right'),0.3,{right:o.right,ease:punchgs.Power3.easeInOut});
									if (fh===1) {
										o.height = "100%";
										o.mt = 0;
										o.tt = 0;
									}
									else

									if (jQuery('#auto_height').attr("checked")==="checked")
										o.height = o.height * (sw/bw);


									punchgs.TweenLite.to(element.find('.rsp-slide-bg'),0.3,{top:o.tt,width:ww,left:0-mm,marginTop:o.mt, height:o.height,ease:punchgs.Power3.easeInOut});
									punchgs.TweenLite.to(element.find('.rsp-layer'),0.3,{fontSize:o.mtp*14, paddingTop:o.mtp*3, paddingBottom:o.mtp*3, paddingLeft:o.mtp*5,paddingRight:o.mtp*5, lineHeight:o.mtp*14+"px" ,ease:punchgs.Power3.easeInOut });

								}
								function readLayoutValues(goon) {
									var s = get_preview_resp_sizes(),
										o = new Object();
									if (goon===1)
										jQuery('.slide-size-wrapper .tp-moderncheckbox').change();

									if (jQuery('#slider_type_2').attr("checked")==="checked" || jQuery('#slider_type_3').attr("checked")==="checked") {
										o.dw = 187; o.dm = 23;
										o.nw = 160; o.nm = 10;
										o.tw = 140; o.tm = 7;
										o.mw = 80; o.mm = 5;
									} else {
										o.dw = 140; o.dm = 0;
										o.nw = 140; o.nm = 0;
										o.tw = 126; o.tm = 0;
										o.mw = 71; o.mm = 0;
									}

									if (jQuery('#slider_type_3').attr("checked")==="checked") {
										o.dh = 1;
										o.nh = 1;
										o.th = 1;
										o.mh = 1;
									} else {
										o.dh = 0;
										o.nh = 0;
										o.th = 0;
										o.mh = 0;
									}

									
									setLayoutDesign(jQuery('.rsp-device-imac'), s.w_d.val(), s.h_d.val(), 1400,900,1,o.dw,o.dm,o.dh,0);
									setLayoutDesign(jQuery('.rsp-device-macbook'), s.w_n.val(), s.h_n.val(), 1200,770,1.166,o.nw,o.nm,o.nh,0);
									setLayoutDesign(jQuery('.rsp-device-ipad'), s.w_t.val(), s.h_t.val(), 768,1024,1.78,o.tw,o.tm,o.th,6);
									setLayoutDesign(jQuery('.rsp-device-iphone'), s.w_m.val(), s.h_m.val(), 640,1136,1.25,o.mw,o.mm,o.mh,0);
								}

								jQuery('.slide-size-wrapper .tp-moderncheckbox').on("change",function() {
										var bt = jQuery(this),
											bp = bt.closest('.slide-size-wrapper'),
											cw = bp.find('.rs-width-height-alternative'),
											aw = bp.find('.rs-width-height-wrapper'),
											s = get_preview_resp_sizes();


										if (bt.attr('checked')==="checked") {
											if (bt.data('oldstatus')==="unchecked" || bt.data('oldstatus') ===undefined) {
												bp.removeClass("disabled").find('input[type="text"]').removeAttr("disabled");
												aw.show();
												cw.hide();
												bp.find('input[type="text"]').each(function() {
													var inp = jQuery(this);
													if (inp.data('oldval')!==undefined) inp.val(inp.data('oldval'));
												});

											}
											bt.data('oldstatus',"checked");
										} else {
											if (bt.data('oldstatus')==="checked" || bt.data('oldstatus') ===undefined) {
												bp.addClass("disabled").find('input[type="text"]').attr("disabled","disabled");
												aw.hide();
												cw.show();												
												bp.find('input[type="text"]').each(function() {
													var inp = jQuery(this);
													inp.data('oldval',inp.val());
												});
											}
											bt.data('oldstatus',"unchecked");
										}

										// CHECK DISABLE VALUES AND INHERIT THEM
										/*if (!s.n) {
											s.w_n.val(s.w_d.val());
											s.h_n.val(s.h_d.val());
										}
										if (!s.t) {
											s.w_t.val(s.w_n.val());
											s.h_t.val(s.h_n.val());
										}
										if (!s.m) {
											s.w_m.val(s.w_t.val());
											s.h_m.val(s.h_t.val());
										}*/

										readLayoutValues(0);

								});

								jQuery('.slide-size-wrapper .tp-moderncheckbox').change();
								readLayoutValues();

								jQuery('input[name="slider_type"], #auto_height, #width, #height, input[name="width_notebook"], input[name="height_notebook"], input[name="width_tablet"], input[name="height_tablet"], input[name="width_mobile"], input[name="height_mobile"]').on("change",function() {
									readLayoutValues(1);
								});
							});
						</script>
						<!-- FALLBACK SETTINGS -->
						<p style="display:none">
							<?php $force_full_width = ZTSliderFunctions::getVal($this->arrFieldsParams, 'force_full_width', 'off');?>
							<span class="rev-new-label"><?php echo  JText::_('Force Full Width:');?></span>
							<input type="checkbox" class="tp-moderncheckbox " id="force_full_width" name="force_full_width" data-unchecked="off" <?php echo ZTSliderFunctions::checked($force_full_width, 'on');?>>

						</p>

						<script>
						   jQuery(document).on("ready", function() {
						   		 function rsSelectorFun(firsttime) {

						   		 	jQuery('.rs-slidersize').removeClass("selected");
						   		 	jQuery('.rs-slidesize-selector input:checked').closest(".rs-slidersize").addClass("selected");


						   		 	// IF AUTO IS SELECTED AND FULLSCREEN IS FORCED (FALL BACK) THAN SELECT FULLWIDTH !
						   		 	if (firsttime===1) {

							   		 	if (jQuery('#force_full_width').attr('checked')==="checked" && jQuery('#slider_type_1').attr('checked')==="checked") {
							   		 		jQuery('#slider_type_1').removeAttr('checked').change();
							   		 		jQuery('#slider_type_2').attr('checked',"checked").change();
							   		 	}

							   		 	if (jQuery('#force_full_width').attr('checked')!=="checked" && jQuery('#slider_type_2').attr('checked')==="checked") {
							   		 		jQuery('#slider_type_2').removeAttr('checked').change();
							   		 		jQuery('#slider_type_1').attr('checked',"checked").change();
							   		 	}
							   		 }

						   		 	// FORCE FULLWIDTH ON FULLWIDTH AND FULLSCREEN
						   		 	if (jQuery('#slider_type_2').attr('checked')==="checked" || jQuery('#slider_type_3').attr('checked')==="checked")
						   		 		jQuery('#force_full_width').attr('checked',"checked").change();
						   		 	else
										jQuery('#force_full_width').removeAttr('checked').change();



						   		 }
						   		jQuery('.rs-slidesize-selector input').change(rsSelectorFun);
						   		jQuery('#force_full_width').change();
						   		rsSelectorFun(1);
						   })
						</script>
						<div style="float:none; clear:both"></div>
					</div>
				</div>

				<div class="setting_box">
					<h3><span class="setting-step-number">5</span><span><?php echo  JText::_("Customize, Build & Implement");?></span></h3>
					<div class="inside" style="padding:35px 20px">
						<div class="slidertitlebox breakdownonmobile">
							<span class="one-third-container" style="text-align:center">
								<img style="width:100%; max-width:325px;" src="<?php echo JUri::root();?>administrator/components/com_zt_layerslider/assets/images/mainoptions/mini-customizeslide.jpg">
								<span class="cbi-title"><?php echo  JText::_("Advanced Settings");?></span>
								<span class="description" style="text-align:center;min-height:60px;"><?php echo  JText::_("Go for further customization using the advanced settings on the right of this configuration page.");?></span>
								<div style="float:none; clear:both; height:20px;display:block;"></div>
								<a class="button-primary revblue" href="#form_slider_params"><i class="revicon-cog"></i><?php echo  JText::_("Scroll to Options");?></a>
							</span>

							<span class="one-third-container" style="text-align:center">
								<img style="width:100%;max-width:325px;" src="<?php echo JUri::root();?>administrator/components/com_zt_layerslider/assets/images/mainoptions/mini-editslide.jpg">
								<span class="cbi-title"><?php echo  JText::_("Start Building Slides");?></span>
								<span class="description" style="text-align:center;min-height:60px;"><?php echo  JText::_("Our drag and drop editor will make creating slide content an absolut breeze. This is where the magic happens!");?></span>
								<div style="float:none; clear:both; height:20px;"></div>
								<?php
if (isset($linksEditSlides)) {
	?>
									<a class="button-primary revblue" href="<?php echo $linksEditSlides;?>"  id="link_edit_slides"><i class="revicon-pencil-1"></i><?php echo  JText::_("Edit Slides");?> </a>
									<?php
}
?>
							</span>

							<span class="one-third-container" style="text-align:center">
								<img style="width:100%;max-width:325px;" src="<?php echo JUri::root();?>administrator/components/com_zt_layerslider/assets/images/mainoptions/mini-implement.jpg"><span class="description"><?php echo  JText::_("");?></span>
								<span class="cbi-title"><?php echo  JText::_("Implement your Slider");?></span>
								<span class="description" style="text-align:center;min-height:60px;"><?php echo  JText::_("There are several ways to add your slider to your wordpress post / page / etc.");?></span>
								<div style="float:none; clear:both; height:20px;"></div>
								
								<span class="button-primary revblue rs-embed-slider"><i class="eg-icon-plus-circled"></i><?php echo  JText::_("Embed Slider");?> </span>
								
							</span>
						</div>
					</div>
					<div class="buttonarea" style="background-color:#eee; text-align:center">
						<a style="width:125px" class='button-primary revgreen' href='javascript:void(0)' id="button_save_slider" ><i class="rs-rp-accordion-icon rs-icon-save-light" style="display: inline-block;vertical-align: middle;width: 18px;height: 20px;margin-right:5px;background-repeat: no-repeat;"></i><?php echo  JText::_("Save Settings");?></a>
						<span id="loader_update" class="loader_round" style="display:none;background-color:#27AE60 !important; color:#fff;padding: 4px 5px 5px 25px;margin-right: 5px;"><?php echo  JText::_("updating...");?> </span>
						<span id="update_slider_success" class="success_message"></span>
						<a style="width:125px" class='button-primary revred' id="button_delete_slider" href='javascript:void(0)' ><i class="revicon-trash"></i><?php echo  JText::_("Delete Slider");?></a>
						<a style="width:125px" class='button-primary revyellow' id="button_close_slider_edit"  href='<?php echo JRoute::_('index.php?option=com_zt_layerslider&view=sliders',false)?>' ><i class="eg-icon-th-large"></i><?php echo  JText::_("All Sliders");?></a>
						<a style="width:125px" class="button-primary revgray" href="javascript:void(0)"  id="button_preview_slider" title="<?php echo  JText::_("Preview Slider");?>"><i class="revicon-search-1"></i><?php echo  JText::_("Preview");?></a>
					</div>
				</div>

				<!-- END OF THE HTML MARKUP FOR THE MAIN SETTINGS -->
			</form>

			<script>
				jQuery('body').on('click', '.rs-embed-slider', function(){			
					var use_alias = jQuery('#alias').val();
					
					jQuery('.rs-dialog-embed-slider').find('.rs-example-alias').text(use_alias);
					jQuery('.rs-dialog-embed-slider').find('.rs-example-alias-1').text('[rev_slider alias="'+use_alias+'"]');
					
					jQuery('.rs-dialog-embed-slider').dialog({
						modal: true,
						resizable:false,
						minWidth:750,
						minHeight:300,
						closeOnEscape:true
					});
				});
			</script>

			<div class="divide20"></div>

			<?php
			if ($this->is_edit) {
				$custom_css = '';
				$custom_js = '';
				if (!empty($this->item->id)) {
					$custom_css = @stripslashes($this->item->attribs['custom_css']);
					$custom_js = @stripslashes($this->item->attribs['custom_javascript']);
				}
				
				$hidebox = '';
				if(!JFactory::getUser()->authorise('core.admin') ){
					$hidebox = 'display: none;';
				}
				?>
				<div class="setting_box" id="css-javascript-customs" style="max-width:100%;position:relative;overflow:hidden;<?php echo $hidebox; ?>">
					<h3><span class="setting-step-number">6</span><span><?php echo  JText::_("Custom CSS / Javascript");?></span></h3>
					<div class="inside" id="codemirror-wrapper">

						<span class="cbi-title"><?php echo  JText::_("Custom CSS")?></span>
						<textarea name="custom_css" id="rs_custom_css"><?php echo $custom_css;?></textarea>
						<div class="divide20"></div>

						<span class="cbi-title"><?php echo  JText::_("Custom JavaScript")?></span>
						<textarea name="custom_javascript" id="rs_custom_javascript"><?php echo $custom_js;?></textarea>
						<div class="divide20"></div>

					</div>
				</div>

				<script type="text/javascript">
					rev_cm_custom_css = null;
					rev_cm_custom_js = null;
					
					jQuery(document).ready(function(){
						rev_cm_custom_css = CodeMirror.fromTextArea(document.getElementById("rs_custom_css"), {
							onChange: function(){ },
							lineNumbers: true,
							mode: 'css',
							lineWrapping:true											
						});

						rev_cm_custom_js = CodeMirror.fromTextArea(document.getElementById("rs_custom_javascript"), {
							onChange: function(){ },
							lineNumbers: true,
							mode: 'text/html',
							lineWrapping:true													
						});


						jQuery('.rs-cm-refresh').click(function(){
							rev_cm_custom_css.refresh();
							rev_cm_custom_js.refresh();
						});

						var hlLineC = rev_cm_custom_css.setLineClass(0, "activeline"),
							hlLineJ = rev_cm_custom_js.setLineClass(0, "activeline");


					});
				</script>

				<?php
			}
			?>
		</div>


		<div class="settings_panel_right">
			<script type="text/javascript">
				function drawToolBarPreview() {

					 var tslideprev = jQuery('.toolbar-sliderpreview'),
						 tslider = jQuery('.toolbar-slider'),
						 tslider_image = jQuery('.toolbar-slider-image'),
						 tprogress = jQuery('.toolbar-progressbar'),
						 tdot = jQuery('.toolbar-dottedoverlay'),
						 tthumbs = jQuery('.toolbar-navigation-thumbs'),
						 ttabs = jQuery('.toolbar-navigation-tabs'),
						 tbuls = jQuery('.toolbar-navigation-bullets'),
						 tla = jQuery('.toolbar-navigation-left'),
						 tra = jQuery('.toolbar-navigation-right');


					// DRAW SHADOWS
					jQuery('.shadowTypes').css({display:"none"});
					tslideprev.removeClass("tp-shadow1").removeClass("tp-shadow2").removeClass("tp-shadow3").removeClass("tp-shadow4").removeClass("tp-shadow5").removeClass("tp-shadow6");

					// MAKE ddd_IF NEEDED
					if (jQuery('#ddd_parallax').attr('checked') && jQuery('#use_parallax').attr('checked')) {
						punchgs.TweenLite.to(tslideprev,0.5,{transformPerspective:800, rotationY:30,rotationX:10,scale:0.8});
						if (jQuery('#ddd_parallax_shadow').attr('checked')) {
							tslideprev.css({boxShadow:"0 45px 100px rgba(0, 0, 0, 0.4)"})
						} else {
							tslideprev.css({boxShadow:"none"})
						}
					} else {
						punchgs.TweenLite.to(tslideprev,0.5,{transformPerspective:800, rotationY:0,rotationX:0,scale:1});						
						tslideprev.addClass('tp-shadow'+jQuery('#shadow_type').val());
						tslideprev.css({boxShadow:"none"})
					}
					





					// DRAW PADDING
					tslideprev.css({padding:jQuery('#padding').val()+"px"});

					// DRAWING BACKGROUND IMAGE OR COLOR
					if (jQuery('#show_background_image').attr("checked")==="checked")	// DRAWING BACKGROUND IMAGE
						tslider.css({background:"url("+jQuery('#background_image').val()+")",
										backgroundSize:jQuery('#bg_fit').val(),
										backgroundPosition:jQuery('#bg_repeat').val(),
										backgroundRepeat:jQuery('#bg_position').val(),
									});
					else
						tslider.css({background:jQuery('#background_color').val()});	// DRAW BACKGROUND COLOR


					// DRAWING PROGRESS BAR
					var progope = parseInt(jQuery('#progress_opa').val(),0),
						progheight = parseInt(jQuery('#progress_height').val(),0);

					progope = jQuery.isNumeric(progope) ? progope/100 : 0.15;
					progheight = jQuery.isNumeric(progheight) ? progheight : 5;

					switch (jQuery('#show_timerbar').val()) {
						case "top":
							punchgs.TweenLite.set(tprogress,{backgroundColor:jQuery('#progressbar_color').val(),top:"0px",bottom:"auto", height:progheight+"px", opacity:progope});
						break;
						case "bottom":
							punchgs.TweenLite.set(tprogress,{backgroundColor:jQuery('#progressbar_color').val(),bottom:"0px",top:"auto", height:progheight+"px", opacity:progope});
						break;
					}
					if (jQuery('#enable_progressbar').attr('checked')==="checked")
							punchgs.TweenLite.set(tprogress,{display:"block"});
					else
							punchgs.TweenLite.set(tprogress,{display:"none"});


					function removeClasses(obj,cs) {
						var classes = cs.split(",");
						if (classes)
							jQuery.each(classes,function(index,c) {
								obj.removeClass("tbn-"+c);
							});
					}

					jQuery('.toolbar-sliderpreview').removeClass("outer-left").removeClass("outer-right").removeClass("outer-top").removeClass("outer-bottom").removeClass("inner");

					// SHOW / HIDE ARROWS
					if (jQuery('#enable_arrows').attr("checked")!=="checked") {
						tla.hide();
						tra.hide();
					} else {
						tla.show();
						tra.show();
						removeClasses(tla,"left,right,center,top,bottom,middle");
						removeClasses(tra,"left,right,center,top,bottom,middle");

						// LEFT ARROW
						var hor = jQuery('#leftarrow_align_hor option:selected').val(),
							ver = jQuery('#leftarrow_align_vert option:selected').val();
						ver = ver === "center" ? "middle" : ver;
						tla.addClass("tbn-"+hor);
						tla.addClass("tbn-"+ver);
						var ml = Math.ceil(parseInt(jQuery('#leftarrow_offset_hor').val(),0)/4),
							mt = Math.ceil(parseInt(jQuery('#leftarrow_offset_vert').val(),0)/4);

						if (hor==="right")
							tla.css({marginRight:ml+"px",marginLeft:"0px"});
						else
							tla.css({marginRight:"0px",marginLeft:ml+"px"});

						if (ver==="bottom")
							tla.css({marginBottom:mt+"px",marginTop:"0px"});
						else
							tla.css({marginBottom:"0px",marginTop:mt+"px"});


						// RIGHT ARROW
						hor = jQuery('#rightarrow_align_hor option:selected').val();
						ver = jQuery('#rightarrow_align_vert option:selected').val();
						ver = ver === "center" ? "middle" : ver;
						tra.addClass("tbn-"+hor);
						tra.addClass("tbn-"+ver);
						ml = Math.ceil(parseInt(jQuery('#rightarrow_offset_hor').val(),0)/4),
						mt = Math.ceil(parseInt(jQuery('#rightarrow_offset_vert').val(),0)/4);
						if (hor==="right")
							tra.css({marginRight:ml+"px",marginLeft:"0px"});
						else
							tra.css({marginRight:"0px",marginLeft:ml+"px"});

						if (ver==="bottom")
							tra.css({marginBottom:mt+"px",marginTop:"0px"});
						else
							tra.css({marginBottom:"0px",marginTop:mt+"px"});

					}


					// SHOW HIDE BULLETS
					if (jQuery('#enable_bullets').attr("checked")!=="checked") {
						tbuls.hide();
					} else {
						tbuls.show();
						removeClasses(tbuls,"left,right,center,top,bottom,middle,vertical,horizontal,inner,outer-left,outer-right,outer-top,outer-bottom");

						hor = jQuery('#bullets_align_hor option:selected').val();
						ver = jQuery('#bullets_align_vert option:selected').val();
						ver = ver === "center" ? "middle" : ver;
						tbuls.addClass("tbn-"+hor);
						tbuls.addClass("tbn-"+ver);

						ml = Math.ceil(parseInt(jQuery('#bullets_offset_hor').val(),0)/4),
						mt = Math.ceil(parseInt(jQuery('#bullets_offset_vert').val(),0)/4);
						if (hor==="right")
							tbuls.css({marginRight:ml+"px",marginLeft:"0px"});
						else
							tbuls.css({marginRight:"0px",marginLeft:ml+"px"});

						if (ver==="bottom")
							tbuls.css({marginBottom:mt+"px",marginTop:"0px"});
						else
							tbuls.css({marginBottom:"0px",marginTop:mt+"px"});

						tbuls.addClass("tbn-"+jQuery('#bullets_direction option:selected').val());
					}

					// SHOW HIDE THUMBNAILS
					if (jQuery('#enable_thumbnails').attr("checked")!=="checked") {
						tthumbs.hide();
					} else {
						tthumbs.show();
						removeClasses(tthumbs,"left,right,center,top,bottom,middle,vertical,horizontal,inner,outer,spanned,outer-left,outer-right,outer-top,outer-bottom");
						tthumbs.addClass("tbn-"+jQuery('#thumbnails_align_hor option:selected').val());
						var v = jQuery('#thumbnails_align_vert option:selected').val() === "center" ? "middle" : jQuery('#thumbnails_align_vert option:selected').val();
						tthumbs.addClass("tbn-"+v);
						tthumbs.addClass("tbn-"+jQuery('#thumbnail_direction option:selected').val());
						if (jQuery('#span_thumbnails_wrapper').attr("checked")==="checked")
								tthumbs.addClass("tbn-spanned");
						jQuery('.toolbar-navigation-thumbs-bg').css({background:jQuery('#thumbnails_wrapper_color').val(), opacity:jQuery('#thumbnails_wrapper_opacity').val()/100});

						jQuery('.toolbar-sliderpreview').addClass(jQuery('#thumbnails_inner_outer option:selected').val())
						tthumbs.addClass("tbn-"+jQuery('#thumbnails_inner_outer option:selected').val())

					}

					// SHOW HIDE TABS
					if (jQuery('#enable_tabs').attr("checked")!=="checked") {
						ttabs.hide();
					} else {
						ttabs.show();
						removeClasses(ttabs,"left,right,center,top,bottom,middle,vertical,horizontal,inner,outer,spanned,outer-left,outer-right,outer-top,outer-bottom");
						ttabs.addClass("tbn-"+jQuery('#tabs_align_hor option:selected').val());
						var v = jQuery('#tabs_align_vert option:selected').val() === "center" ? "middle" : jQuery('#tabs_align_vert option:selected').val();
						ttabs.addClass("tbn-"+v);
						ttabs.addClass("tbn-"+jQuery('#tabs_direction option:selected').val());
						if (jQuery('#span_tabs_wrapper').attr("checked")==="checked")
								ttabs.addClass("tbn-spanned");
						jQuery('.toolbar-navigation-tabs-bg').css({background:jQuery('#tabs_wrapper_color').val(),opacity:jQuery('#tabs_wrapper_opacity').val()/100});
						jQuery('.toolbar-sliderpreview').addClass(jQuery('#tabs_inner_outer option:selected').val());
						ttabs.addClass("tbn-"+jQuery('#tabs_inner_outer option:selected').val());
					}

					// DRAWING DOTTED OVERLAY
					tdot.removeClass("twoxtwo").removeClass("twoxtwowhite").removeClass("threexthree").removeClass("threexthreewhite");
					tdot.addClass(jQuery('#background_dotted_overlay').val());
				}
				jQuery(document).ready(function(){
					ZtSliderAdmin.initEditSlideView();
				});
			</script>


				<!-- ALL SETTINGS -->
				<div class="settings_wrapper closeallothers" id="form_slider_params_wrap">
					<form name="form_slider_params" id="form_slider_params" onkeypress="return event.keyCode != 13;">

						<!-- GENERAL SETTINGS -->

						<div class="setting_box">
							<h3 class="box_closed"><i class="rs-rp-accordion-icon eg-icon-cog-alt"></i>
								<div class="setting_box-arrow"></div>
								<span><?php echo  JText::_("General Settings");?></span>
							</h3>

							<div class="inside" style="display:none;">

								<ul class="main-options-small-tabs" style="display:inline-block; ">
									<li data-content="#general-slideshow" class="selected"><?php echo  JText::_('Slideshow');?></li>
									<li data-content="#general-defaults" class=""><?php echo  JText::_('Defaults');?></li>
									<li data-content="#general-progressbar" class="dontshowonhero"><?php echo  JText::_('Progress Bar');?></li>
									<li data-content="#general-firstslide" class="dontshowonhero"><?php echo  JText::_('1st Slide');?></li>
									<li data-content="#general-misc"><?php echo  JText::_('Misc.');?></li>

								</ul>

								<!-- GENERAL MISC. -->
								<div id="general-misc" style="display:none">
									
									<div class="dontshowonhero">
										<!-- NEXT SLIDE ON FOCUS -->
										<span id="label_next_slide_on_window_focus" class="label" origtitle="<?php echo  JText::_("Call next slide when inactive browser tab is focused again. Use this for avoid dissorted layers and broken timeouts after bluring the browser tab.");?>"><?php echo  JText::_("Next Slide on Focus");?> </span>
										<input type="checkbox"  class="tp-moderncheckbox withlabel" id="next_slide_on_window_focus" name="next_slide_on_window_focus" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, 'next_slide_on_window_focus', 'off'), "on");?>>
										<div class="clearfix"></div>
									</div>
									
									<div>
										<!-- BLUR ON FOCUS -->
										<span id="label_disable_focus_listener" class="label" origtitle="<?php echo  JText::_("This will disable the blur/focus behavior of the browser.");?>"><?php echo  JText::_("Disable Blur/Focus behavior");?> </span>
										<input type="checkbox"  class="tp-moderncheckbox withlabel" id="disable_focus_listener" name="disable_focus_listener" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, 'disable_focus_listener', 'off'), "on");?>>
										<div class="clearfix"></div>
									</div>
								</div><!-- end of GENERAL MISC -->

								<!-- GENERAL DEFAULTS -->
								<div id="general-defaults" style="display:none">
									<!-- DEFAULT LAYER SELECTION -->
									<?php $layer_selection = ZTSliderFunctions::getVal($this->arrFieldsParams, 'def-layer_selection', 'off'); ?>									
									<span id="def-layer_selection" origtitle="<?php echo  JText::_("Default Layer Selection on Frontend enabled or disabled");?>" class="label"><?php echo  JText::_('Layers Selectable:');?></span>
									<input type="checkbox" class="tp-moderncheckbox withlabel" id="def-layer_selection" name="def-layer_selection" data-unchecked="off" <?php echo ZTSliderFunctions::checked($layer_selection, 'on');?>>
									
									<!-- SLIDER ID -->
									<span class="label" id="label_slider_id" origtitle="<?php echo  JText::_("Set a specific ID to the Slider, if empty, there will be a default one written");?>"><?php echo  JText::_("Slider ID");?> </span>
									<input type="text"  class="text-sidebar withlabel" id="slider_id" name="slider_id" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'slider_id', '');?>">
									<div class="clearfix"></div>

									<!-- DELAY -->
									<span class="label" id="label_delay" origtitle="<?php echo  JText::_("The time one slide stays on the screen in Milliseconds. This is a Default Global value. Can be adjusted slide to slide also in the slide editor.");?>"><?php echo  JText::_("Default Slide Duration");?> </span>
									<input type="text"  class="text-sidebar withlabel" id="delay" name="delay" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'delay', '9000');?>">
									 <span ><?php echo  JText::_("ms");?></span>
									<div class="clearfix"></div>

									<!-- Initialisation Delay -->
									<span id="label_start_js_after_delay" class="label" origtitle="<?php echo  JText::_("Sets a delay before the Slider gets initialized");?>"><?php echo  JText::_("Initialization Delay");?> </span>
									<input type="text"  class="text-sidebar withlabel" id="start_js_after_delay" name="start_js_after_delay" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'start_js_after_delay', '0');?>">
									<span ><?php echo  JText::_("ms");?></span>
									<div class="clear"></div>
									<div id="reset-to-default-inputs">
										<input type="checkbox" class="rs-ingore-save rs-reset-slide-setting" name="reset-slide_transition" /> <span id="label_def-slide_transition" origtitle="<?php echo  JText::_("Default transition by creating a new slide.");?>" class="label"><?php echo  JText::_('Transitions');?></span>
										<select id="def-slide_transition" name="def-slide_transition" style="max-width:105px" class="withlabel">
											<?php
											$def_trans = ZTSliderFunctions::getVal($this->arrFieldsParams, 'def-slide_transition', 'fade');
											foreach ($transitions as $handle => $name) {
												$not = (strpos($handle, 'notselectable') !== false) ? ' disabled="disabled"' : '';
												$sel = ($def_trans == $handle) ? ' selected="selected"' : '';
												echo '<option value="' . $handle . '"' . $not . $sel . '>' . $name . '</option>';
											}
											?>
										</select>
										<div class="clear"></div>

										<?php $def_trans_dur = ZTSliderFunctions::getVal($this->arrFieldsParams, 'def-transition_duration', '300');?>
										<input type="checkbox" class="rs-ingore-save rs-reset-slide-setting" name="reset-transition_duration" /> <span id="label_def-transition_duration" origtitle="<?php echo  JText::_("Default transition duration by creating a new slide.");?>" class="label" origtitle=""><?php echo  JText::_('Animation Duration');?></span>
										<input type="text" class="text-sidebar withlabel" id="def-transition_duration" name="def-transition_duration" value="<?php echo $def_trans_dur;?>">
										<span><?php echo  JText::_('ms');?></span>
										<div class="clear"></div>

										<?php
										$img_sizes = ZTSliderFunctions::get_all_image_sizes();
										$bg_image_size = ZTSliderFunctions::getVal($this->arrFieldsParams, 'def-image_source_type', 'full');
										?>
										<input type="checkbox" class="rs-ingore-save rs-reset-slide-setting" name="reset-image_source_type" /> <span id="label_def-image_source_type" class="label" origtitle="<?php echo  JText::_("Default main image source size by creating a new slide.");?>" ><?php echo  JText::_('Image Source Size');?></span>
										<select name="def-image_source_type">
											<?php
											foreach ($img_sizes as $imghandle => $imgSize) {
												$sel = ($bg_image_size == $imghandle) ? ' selected="selected"' : '';
												echo '<option value="' . $imghandle . '"' . $sel . '>' . $imgSize . '</option>';
											}
											?>
										</select>
										<div class="clear"></div>

										<?php
										$bgFit = ZTSliderFunctions::getVal($this->arrFieldsParams, 'def-background_fit', 'cover');
										$bgFitX = ZTSliderFunctions::getVal($this->arrFieldsParams, 'def-bg_fit_x', '100');
										$bgFitY = ZTSliderFunctions::getVal($this->arrFieldsParams, 'def-bg_fit_y', '100');
										?>
										<input type="checkbox" class="rs-ingore-save rs-reset-slide-setting" name="reset-background_fit" /> <span id="label_def-background_fit" origtitle="<?php echo  JText::_("Default background size by creating a new slide.");?>"  class="label"><?php echo  JText::_('Background Fit');?></span>
										<select id="def-background_fit" name="def-background_fit" style="max-width: 105px;" class="withlabel">
											<option value="cover"<?php echo ZTSliderFunctions::selected($bgFit, 'cover');?>>cover</option>
											<option value="contain"<?php echo ZTSliderFunctions::selected($bgFit, 'contain');?>>contain</option>
											<option value="percentage"<?php echo ZTSliderFunctions::selected($bgFit, 'percentage');?>>(%, %)</option>
											<option value="normal"<?php echo ZTSliderFunctions::selected($bgFit, 'normal');?>>normal</option>
										</select>
										<input type="text" name="def-bg_fit_x" style="<?php if ($bgFit != 'percentage') {
											echo 'display: none; ';
										}
										?> width:60px;margin-right:10px" value="<?php echo $bgFitX;?>" />
										<input type="text" name="def-bg_fit_y" style="<?php if ($bgFit != 'percentage') {
											echo 'display: none; ';
										}
										?> width:60px;margin-right:10px"  value="<?php echo $bgFitY;?>" />
										<div class="clear"></div>

										<?php
										$bgPosition = ZTSliderFunctions::getVal($this->arrFieldsParams, 'def-bg_position', 'center center');
										$bgPositionX = ZTSliderFunctions::getVal($this->arrFieldsParams, 'def-bg_position_x', '0');
										$bgPositionY = ZTSliderFunctions::getVal($this->arrFieldsParams, 'def-bg_position_y', '0');
										?>
										<input type="checkbox" class="rs-ingore-save rs-reset-slide-setting" name="reset-bg_position" /> <span id="label_slide_bg_position" origtitle="<?php echo  JText::_("Default background position by creating a new slide.");?>" class="label"><?php echo  JText::_('Background Position');?></span>
										<select name="def-bg_position" id="slide_bg_position" class="withlabel">
											<option value="center top"<?php echo ZTSliderFunctions::selected($bgPosition, 'center top');?>>center top</option>
											<option value="center right"<?php echo ZTSliderFunctions::selected($bgPosition, 'center right');?>>center right</option>
											<option value="center bottom"<?php echo ZTSliderFunctions::selected($bgPosition, 'center bottom');?>>center bottom</option>
											<option value="center center"<?php echo ZTSliderFunctions::selected($bgPosition, 'center center');?>>center center</option>
											<option value="left top"<?php echo ZTSliderFunctions::selected($bgPosition, 'left top');?>>left top</option>
											<option value="left center"<?php echo ZTSliderFunctions::selected($bgPosition, 'left center');?>>left center</option>
											<option value="left bottom"<?php echo ZTSliderFunctions::selected($bgPosition, 'left bottom');?>>left bottom</option>
											<option value="right top"<?php echo ZTSliderFunctions::selected($bgPosition, 'right top');?>>right top</option>
											<option value="right center"<?php echo ZTSliderFunctions::selected($bgPosition, 'right center');?>>right center</option>
											<option value="right bottom"<?php echo ZTSliderFunctions::selected($bgPosition, 'right bottom');?>>right bottom</option>
										</select>
										<input type="text" name="def-bg_position_x" style="<?php if ($bgPosition != 'percentage') {
											echo 'display: none;';
										}
										?>width:60px;margin-right:10px" value="<?php echo $bgPositionX;?>" />
										<input type="text" name="def-bg_position_y" style="<?php if ($bgPosition != 'percentage') { 
											echo 'display: none;';
										}
										?>width:60px;margin-right:10px" value="<?php echo $bgPositionY;?>" />
										<div class="clear"></div>
										<?php
										$bgRepeat = ZTSliderFunctions::getVal($this->arrFieldsParams, 'def-bg_repeat', 'no-repeat');
										?>
										<input type="checkbox" class="rs-ingore-save rs-reset-slide-setting" name="reset-bg_repeat" />
										<span id="slide_bg_repeat" origtitle="<?php echo  JText::_("Default background repeat by creating a new slide.");?>"  class="label"><?php echo  JText::_('Background Repeat');?></span>
										<select name="def-bg_repeat" id="slide_bg_repeat" style="margin-right:20px">
											<option value="no-repeat"<?php echo ZTSliderFunctions::selected($bgRepeat, 'no-repeat');?>>no-repeat</option>
											<option value="repeat"<?php echo ZTSliderFunctions::selected($bgRepeat, 'repeat');?>>repeat</option>
											<option value="repeat-x"<?php echo ZTSliderFunctions::selected($bgRepeat, 'repeat-x');?>>repeat-x</option>
											<option value="repeat-y"<?php echo ZTSliderFunctions::selected($bgRepeat, 'repeat-y');?>>repeat-y</option>
										</select>
										<div class="clear"></div>

										<?php $kenburn_effect = ZTSliderFunctions::getVal($this->arrFieldsParams, 'def-kenburn_effect', 'off'); ?>
										<input type="checkbox" class="rs-ingore-save rs-reset-slide-setting" name="reset-kenburn_effect" />
										<span id="label_def-kenburn_effect" origtitle="<?php echo  JText::_("Default Ken/Burn setting by creating a new slide.");?>" class="label"><?php echo  JText::_('Ken Burns / Pan Zoom:');?></span>
										<input type="checkbox" class="tp-moderncheckbox withlabel" id="def-kenburn_effect" name="def-kenburn_effect" data-unchecked="off" <?php echo ZTSliderFunctions::checked($kenburn_effect, 'on');?>>

										<div class="clear"></div>
										<div id="def-kenburns-wrapper" <?php if ($kenburn_effect == 'off') {
										echo 'style="display: none;"';
										}
										?>>

										<?php $kb_start_fit = ZTSliderFunctions::getVal($this->arrFieldsParams, 'def-kb_start_fit', '100');?>
										<input type="checkbox" class="rs-ingore-save rs-reset-slide-setting" name="reset-kb_start_fit" />
										<span id="label_kb_start_fit" class="label"><?php echo  JText::_('Start Fit: (in %):');?></span>
										<input type="text" name="def-kb_start_fit" value="<?php echo intval($kb_start_fit);?>" />
										<div class="clear"></div>

										<?php $kb_easing = ZTSliderFunctions::getVal($this->arrFieldsParams, 'def-kb_easing', 'Linear.easeNone');?>
										<input type="checkbox" class="rs-ingore-save rs-reset-slide-setting" name="reset-kb_easing" /> <span id="label_kb_easing" class="label"><?php echo  JText::_('Easing:');?></span>
										<select name="def-kb_easing">
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Linear.easeNone');?> value="Linear.easeNone">Linear.easeNone</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Power0.easeIn');?> value="Power0.easeIn">Power0.easeIn  (linear)</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Power0.easeInOut');?> value="Power0.easeInOut">Power0.easeInOut  (linear)</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Power0.easeOut');?> value="Power0.easeOut">Power0.easeOut  (linear)</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Power1.easeIn');?> value="Power1.easeIn">Power1.easeIn</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Power1.easeInOut');?> value="Power1.easeInOut">Power1.easeInOut</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Power1.easeOut');?> value="Power1.easeOut">Power1.easeOut</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Power2.easeIn');?> value="Power2.easeIn">Power2.easeIn</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Power2.easeInOut');?> value="Power2.easeInOut">Power2.easeInOut</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Power2.easeOut');?> value="Power2.easeOut">Power2.easeOut</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Power3.easeIn');?> value="Power3.easeIn">Power3.easeIn</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Power3.easeInOut');?> value="Power3.easeInOut">Power3.easeInOut</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Power3.easeOut');?> value="Power3.easeOut">Power3.easeOut</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Power4.easeIn');?> value="Power4.easeIn">Power4.easeIn</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Power4.easeInOut');?> value="Power4.easeInOut">Power4.easeInOut</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Power4.easeOut');?> value="Power4.easeOut">Power4.easeOut</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Back.easeIn');?> value="Back.easeIn">Back.easeIn</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Back.easeInOut');?> value="Back.easeInOut">Back.easeInOut</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Back.easeOut');?> value="Back.easeOut">Back.easeOut</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Bounce.easeIn');?> value="Bounce.easeIn">Bounce.easeIn</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Bounce.easeInOut');?> value="Bounce.easeInOut">Bounce.easeInOut</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Bounce.easeOut');?> value="Bounce.easeOut">Bounce.easeOut</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Circ.easeIn');?> value="Circ.easeIn">Circ.easeIn</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Circ.easeInOut');?> value="Circ.easeInOut">Circ.easeInOut</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Circ.easeOut');?> value="Circ.easeOut">Circ.easeOut</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Elastic.easeIn');?> value="Elastic.easeIn">Elastic.easeIn</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Elastic.easeInOut');?> value="Elastic.easeInOut">Elastic.easeInOut</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Elastic.easeOut');?> value="Elastic.easeOut">Elastic.easeOut</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Expo.easeIn');?> value="Expo.easeIn">Expo.easeIn</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Expo.easeInOut');?> value="Expo.easeInOut">Expo.easeInOut</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Expo.easeOut');?> value="Expo.easeOut">Expo.easeOut</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Sine.easeIn');?> value="Sine.easeIn">Sine.easeIn</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Sine.easeInOut');?> value="Sine.easeInOut">Sine.easeInOut</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'Sine.easeOut');?> value="Sine.easeOut">Sine.easeOut</option>
											<option <?php echo ZTSliderFunctions::selected($kb_easing, 'SlowMo.ease');?> value="SlowMo.ease">SlowMo.ease</option>
										</select>
										<div class="clear"></div>

										<?php /*$bgEndPosition = ZTSliderFunctions::getVal($this->arrFieldsParams, 'def-bg_end_position', 'center top');?>
										<input type="checkbox" class="rs-ingore-save rs-reset-slide-setting" name="reset-bg_end_position" /> <span id="label_bg_end_position" class="label"><?php echo  JText::_('End Position:');?></span>
										<select name="def-bg_end_position" id="slide_bg_end_position">
											<option <?php echo ZTSliderFunctions::selected($bgEndPosition, 'center top');?> value="center top">center top</option>
											<option <?php echo ZTSliderFunctions::selected($bgEndPosition, 'center right');?> value="center right">center right</option>
											<option <?php echo ZTSliderFunctions::selected($bgEndPosition, 'center bottom');?> value="center bottom">center bottom</option>
											<option <?php echo ZTSliderFunctions::selected($bgEndPosition, 'center center');?> value="center center">center center</option>
											<option <?php echo ZTSliderFunctions::selected($bgEndPosition, 'left top');?> value="left top">left top</option>
											<option <?php echo ZTSliderFunctions::selected($bgEndPosition, 'left center');?> value="left center">left center</option>
											<option <?php echo ZTSliderFunctions::selected($bgEndPosition, 'left bottom');?> value="left bottom">left bottom</option>
											<option <?php echo ZTSliderFunctions::selected($bgEndPosition, 'right top');?> value="right top">right top</option>
											<option <?php echo ZTSliderFunctions::selected($bgEndPosition, 'right center');?> value="right center">right center</option>
											<option <?php echo ZTSliderFunctions::selected($bgEndPosition, 'right bottom');?> value="right bottom">right bottom</option>

										</select>
										<?php
										$bgEndPositionX = ZTSliderFunctions::getVal($this->arrFieldsParams, 'def-bg_end_position_x', '0');
										$bgEndPositionY = ZTSliderFunctions::getVal($this->arrFieldsParams, 'def-bg_end_position_y', '0');
										?>
										<input type="text" name="def-bg_end_position_x" style="<?php if ($bgEndPosition != 'percentage') {
											echo ' display: none;';
										}
										?>width:60px;margin-right:10px" value="<?php echo $bgEndPositionX;?>" />
										<input type="text" name="def-bg_end_position_y" style="<?php if ($bgEndPosition != 'percentage') {
											echo ' display: none;';
										}
										?>width:60px;margin-right:10px" value="<?php echo $bgEndPositionY;?>" />
										<div class="clear"></div>
										*/
										?>
										
										<?php $kb_end_fit = ZTSliderFunctions::getVal($this->arrFieldsParams, 'def-kb_end_fit', '100');?>
										<input type="checkbox" class="rs-ingore-save rs-reset-slide-setting" name="reset-kb_end_fit" /> <span id="label_kb_end_fit" class="label"><?php echo  JText::_('End Fit: (in %):');?></span>
										<input type="text" name="def-kb_end_fit" value="<?php echo intval($kb_end_fit);?>" />
										<div class="clear"></div>
										
										<?php $kb_start_offset_x = ZTSliderFunctions::getVal($this->arrFieldsParams, 'def-kb_start_offset_x', '0');?>
										<input type="checkbox" class="rs-ingore-save rs-reset-slide-setting" name="reset-kb_start_offset_x" /> <span id="label_kb_end_fit" class="label"><?php echo  JText::_('Start Offset X:');?></span>
										<input type="text" name="def-kb_start_offset_x" value="<?php echo intval($kb_start_offset_x);?>" />
										<div class="clear"></div>
										
										<?php $kb_start_offset_y = ZTSliderFunctions::getVal($this->arrFieldsParams, 'def-kb_start_offset_y', '0');?>
										<input type="checkbox" class="rs-ingore-save rs-reset-slide-setting" name="reset-kb_start_offset_y" /> <span id="label_kb_end_fit" class="label"><?php echo  JText::_('Start Offset Y:');?></span>
										<input type="text" name="def-kb_start_offset_y" value="<?php echo intval($kb_start_offset_y);?>" />
										<div class="clear"></div>
										
										<?php $kb_end_offset_x = ZTSliderFunctions::getVal($this->arrFieldsParams, 'def-kb_end_offset_x', '0');?>
										<input type="checkbox" class="rs-ingore-save rs-reset-slide-setting" name="reset-kb_end_offset_x" /> <span id="label_kb_end_fit" class="label"><?php echo  JText::_('End Offset X:');?></span>
										<input type="text" name="def-kb_end_offset_x" value="<?php echo intval($kb_end_offset_x);?>" />
										<div class="clear"></div>
										
										<?php $kb_end_offset_y = ZTSliderFunctions::getVal($this->arrFieldsParams, 'def-kb_end_offset_y', '0');?>
										<input type="checkbox" class="rs-ingore-save rs-reset-slide-setting" name="reset-kb_end_offset_y" /> <span id="label_kb_end_fit" class="label"><?php echo  JText::_('End Offset Y:');?></span>
										<input type="text" name="def-kb_end_offset_y" value="<?php echo intval($kb_end_offset_y);?>" />
										<div class="clear"></div>
										
										<?php $kb_start_rotate = ZTSliderFunctions::getVal($this->arrFieldsParams, 'def-kb_start_rotate', '0');?>
										<input type="checkbox" class="rs-ingore-save rs-reset-slide-setting" name="reset-kb_start_rotate" /> <span id="label_kb_end_fit" class="label"><?php echo  JText::_('Start Rotate:');?></span>
										<input type="text" name="def-kb_start_rotate" value="<?php echo intval($kb_start_rotate);?>" />
										<div class="clear"></div>
										
										<?php $kb_end_rotate = ZTSliderFunctions::getVal($this->arrFieldsParams, 'def-kb_end_rotate', '0');?>
										<input type="checkbox" class="rs-ingore-save rs-reset-slide-setting" name="reset-kb_end_rotate" /> <span id="label_kb_end_fit" class="label"><?php echo  JText::_('End Rotate:');?></span>
										<input type="text" name="def-kb_end_rotate" value="<?php echo intval($kb_end_rotate);?>" />
										<div class="clear"></div>
										
										<?php $kb_duration = ZTSliderFunctions::getVal($this->arrFieldsParams, 'def-kb_duration', '10000');?>
										<input type="checkbox" class="rs-ingore-save rs-reset-slide-setting" name="reset-kb_duration" /> <span id="label_kb_duration" class="label"><?php echo  JText::_('Duration (in ms):');?></span>
										<input type="text" name="def-kb_duration" value="<?php echo intval($kb_duration);?>" />
										<div class="clear"></div>
									</div>
									<span class="overwrite-arrow"></span>
									<input type="button" id="reset_slide_button" value="<?php echo  JText::_('Overwrite Selected Settings on all Slides');?>" class="button-primary revblue" origtitle="">
									<div class="clear"></div>
								</div>


							</div> <!-- END OF GENERAL DEFAULTS -->

								<!-- GENERAL FIRST SLIDE -->
								<div id="general-firstslide" style="display:none">
									<span id="label_start_with_slide_enable" class="label" origtitle="<?php echo  JText::_("Activate Alternative 1st Slide.");?>"><?php echo  JText::_("Activate Alt. 1st Slide");?></span>
									<input type="checkbox" class="tp-moderncheckbox withlabel" id="start_with_slide_enable" name="start_with_slide_enable" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "start_with_slide_enable", "off"), "on");?>>
									<div class="clear"></div>
									
									<?php $start_with_slide = intval(ZTSliderFunctions::getVal($this->arrFieldsParams, 'start_with_slide', '1'));?>
									<div id="start_with_slide_row">
										<span id="label_start_with_slide" class="label" origtitle="<?php echo  JText::_("Start from a different slide instead of the first slide. I.e. good for preview / edit mode.");?>"><?php echo  JText::_("Alternative 1st Slide");?> </span>
										<input type="text" class="text-sidebar withlabel" id="start_with_slide" name="start_with_slide" value="<?php echo $start_with_slide; ?>">
										<div class="clear"></div>
									</div>
									
									<span id="label_first_transition_active" class="label" origtitle="<?php echo  JText::_("If active, it will overwrite the first slide transition. Use it to get special transition for the first slide.");?>"><?php echo  JText::_("First Transition Active");?> </span>
									<input type="checkbox" class="tp-moderncheckbox withlabel" id="first_transition_active" name="first_transition_active" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "first_transition_active", "off"), "on");?>>
									<div class="clear"></div>

									<div id="first_transition_row" class="withsublabels">
										<span id="label_first_transition_type" class="label" origtitle="<?php echo  JText::_("First slide transition type");?>"><?php echo  JText::_("Transition Type");?> </span>
										<select id="first_transition_type" name="first_transition_type" style="max-width:100px"  class="withlabel">
											<?php
											$transitions = $operations->getArrTransition();
											$ftt = ZTSliderFunctions::getVal($this->arrFieldsParams, 'first_transition_type', 'fade');
											foreach ($transitions as $handle => $name) {
												$not = (strpos($handle, 'notselectable') !== false) ? ' disabled="disabled"' : '';
												$sel = ($handle == $ftt) ? ' selected="selected"' : '';
												echo '<option value="' . $handle . '"' . $not . $sel . '>' . $name . '</option>';
											}
											?>
										</select>
										<div class="clear"></div>

										<span id="label_first_transition_duration" class="label" origtitle="<?php echo  JText::_("First slide transition duration.");?>"><?php echo  JText::_("Transition Duration");?> </span>
										<input type="text" class="text-sidebar withlabel" id="first_transition_duration" name="first_transition_duration" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "first_transition_duration", "300");?>">
										<span><?php echo  JText::_("ms");?></span>
										<div class="clear"></div>


										<span id="label_first_transition_slot_amount" class="label" origtitle="<?php echo  JText::_("The number of slots or boxes the slide is divided into.");?>"><?php echo  JText::_("Transition Slot Amount");?> </span>
										<input type="text" class="text-sidebar withlabel"  id="first_transition_slot_amount" name="first_transition_slot_amount" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "first_transition_slot_amount", "7");?>">
										<span><?php echo  JText::_("ms");?></span>
										<div class="clear"></div>
									</div>
								</div><!-- END OF GENERAL FIRST SLIDE -->

								<!-- GENERAL SLIDE SHOW -->
								<div id="general-slideshow" style="display:block;">
									<div class="dontshowonhero">
										<!-- Stop Slider on Hover -->
										<span id="label_stop_on_hover" class="label" origtitle="<?php echo  JText::_("Stops the Timer when mouse is hovering the slider.");?>"><?php echo  JText::_("Stop Slide On Hover");?></span>
										<input type="checkbox" class="tp-moderncheckbox withlabel" id="stop_on_hover" name="stop_on_hover" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, 'stop_on_hover', 'off'), "on");?>>
										<div class="clear"></div>

										<!-- Stop Slider -->
									   	<span class="label label-with-subsection" id="label_stop_slider" origtitle="<?php echo  JText::_("Stops the slideshow after the predefined loop amount at the predefined slide.");?>"><?php echo  JText::_("Stop Slider After ...");?> </span>
										<input type="checkbox" class="tp-moderncheckbox withlabel"  id="stop_slider" name="stop_slider" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, 'stop_slider', 'off'), 'on');?>>
										<div class="clear"></div>

										<div id="stopoptionsofslider" class="withsublabels">
											<!-- Stop After loops -->
											<span class="label " id="label_stop_after_loops" origtitle="<?php echo  JText::_("Stops the slider after certain amount of loops. 0 related to the first loop.");?>"><?php echo  JText::_("Amount of Loops");?> </span>
											<input type="text" class="text-sidebar withlabel"   id="stop_after_loops" name="stop_after_loops" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'stop_after_loops', '0');?>">
											<div class="clear"></div>

											<!-- Stop At Slide -->
											<span class="label" id="label_stop_at_slide" origtitle="<?php echo  JText::_("Stops the slider at the given slide");?>"><?php echo  JText::_("At Slide");?> </span>
											<input type="text"  class="text-sidebar withlabel"  id="stop_at_slide" name="stop_at_slide" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'stop_at_slide', '2');?>">
											<div class="clear"></div>
										</div>

										<!-- SHUFFLE -->
										<span id="label_shuffle" class="label" origtitle="<?php echo  JText::_("Randomize the order of the slides at every Page reload.");?>"><?php echo  JText::_("Shuffle / Random Mode");?> </span>
										<input type="checkbox"  class="tp-moderncheckbox withlabel" id="shuffle" name="shuffle" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, 'shuffle', 'off'), "on");?>>
										<div class="clearfix"></div>

										<!-- Loop Single Slide -->
										<span class="label" id="label_loop_slide" origtitle="<?php echo  JText::_("If only one Slide is in the Slider, you can choose wether the Slide should loop or if it should stop. If only one Slide exist, slide will be duplicated !");?>"><?php echo  JText::_("Loop Single Slide");?> </span>
										<input type="checkbox" class="tp-moderncheckbox withlabel"  id="loop_slide" name="loop_slide" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, 'loop_slide', 'off'), "on");?>>
										<div class="clear"></div>
									</div>

									<!-- ViewPort Slider -->
								   	<span class="label label-with-subsection" origtitle="<?php echo  JText::_("Allow to stop the Slider out of viewport.");?>"><?php echo  JText::_("Stop Slider Out of ViewPort");?> </span>
									<input type="checkbox" class="tp-moderncheckbox withlabel"  id="label_viewport" name="label_viewport" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, 'label_viewport', 'off'), 'on');?>>
									<div class="clear"></div>

									<div id="viewportoptionsofslider" class="withsublabels" <?php if(ZTSliderFunctions::getVal($this->arrFieldsParams, 'label_viewport', 'off') == 'off') echo 'style="display: none;"'; ?>>
										<span class="label"><?php echo  JText::_('Out Of ViewPort:');?></span>
										<select name="viewport_start">
											<option value="wait" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'viewport_start', 'wait'), "wait");?>><?php echo  JText::_("Wait");?></option>
											<option value="pause" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'viewport_start', 'wait'), "pause");?>><?php echo  JText::_("Pause");?></option>
										</select>
										<div class="clear"></div>

										<span class="label" origmedia="show" origtitle="<?php echo  JText::_("Min. Size of Slider must be in Viewport before slide starts again.");?>"><?php echo  JText::_("Area out of ViewPort:");?> </span>
										<input type="text" class="text-sidebar withlabel"  id="viewport_area" name="viewport_area" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'viewport_area', '80');?>">
										<span><?php echo  JText::_("%");?></span>
										
									</div>
									
									<!-- wait for revstart -->
								   	<span class="label label-with-subsection text-selectable" origtitle="<?php echo  JText::_("Wait for the revstart method to be called before playing.");?>"><?php echo  JText::_("Wait for ");?>revapi<?php echo ($this->is_edit) ? $this->item->id : ''; ?>.revstart() </span>
									<input type="checkbox" class="tp-moderncheckbox withlabel"  id="waitforinit" name="waitforinit" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, 'waitforinit', 'off'), 'on');?>>
									<div class="clear"></div>
								</div><!-- END OF GENERAL SLIDE SHOW -->

								<!-- GENERAL PROGRESSBAR -->
								<div id="general-progressbar" style="display:none">

									<span class="label" id="label_enable_progressbar" origmedia='show' origtitle="<?php echo  JText::_("Enable / disable progress var");?>"><?php echo  JText::_("Progress Bar Active");?> </span>
									<input type="checkbox" class="tp-moderncheckbox withlabel" id="enable_progressbar" name="enable_progressbar" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "enable_progressbar", "off"), "on");?>>
									<div id="progressbar_settings">
										<!-- Show Progressbar -->
										<span class="label" id="label_show_timerbar" origmedia="show" origtitle="<?php echo  JText::_("Position of the progress bar.");?>"><?php echo  JText::_("Progress Bar Position");?> </span>
										<select id="show_timerbar" name="show_timerbar" class="withlabel" >
											<option value="top" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'show_timerbar', 'top'), "top");?>><?php echo  JText::_("Top");?></option>
											<option value="bottom" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'show_timerbar', 'top'), "bottom");?>><?php echo  JText::_("Bottom");?></option>
										</select>
										<div class="clear"></div>

										<!-- Progress Bar Height -->
										<span class="label" id="label_progress_height" origmedia="show" origtitle="<?php echo  JText::_("The height of the progress bar");?>"><?php echo  JText::_("Progress Bar Heigth");?> </span>
										<input type="text" class="text-sidebar withlabel"  id="progress_height" name="progress_height" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'progress_height', '5');?>">
										<span><?php echo  JText::_("px");?></span>
										<div class="clear"></div>

										 <!-- Progress Bar Opacity -->
										<span class="label" id="label_progress_opa" origmedia="show" origtitle="<?php echo  JText::_("The opacity of the progress bar <br>(0 == Transparent, 100 = Solid color, 50 = 50% opacity etc...)");?>"><?php echo  JText::_("Progress Bar Opacity");?> </span>
										<input type="text" class="text-sidebar withlabel"  id="progress_opa" name="progress_opa" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'progress_opa', '15');?>">
										<span><?php echo  JText::_("%");?></span>
										<div class="clear"></div>

										<!-- Progress Bar Color -->
										<span class="label" id="label_progressbar_color" origmedia="show" origtitle="<?php echo  JText::_("Color of the progress bar.");?>"><?php echo  JText::_("Progress Bar Color");?> </span>
										<input type="text" class="my-color-field rs-layer-input-field tipsy_enabled_top withlabel" title="<?php echo  JText::_("Font Color");?>"  id="progressbar_color" name="progressbar_color" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'progressbar_color', '#000000');?>" />
										<div class="clear"></div>
									</div>
								</div><!-- END OF GENERAL PROGRESSBAR -->
							</div>

							<script>
								jQuery('#stop_slider').on("change",function() {
										var sbi = jQuery(this);
										if (sbi.attr("checked") === "checked") {
											jQuery('#stopoptionsofslider').show();
										} else {
											jQuery('#stopoptionsofslider').hide();
										}
										drawToolBarPreview();
								});

								jQuery('#label_viewport').on("change",function() {
										var sbi = jQuery(this);
										if (sbi.attr("checked") === "checked") {
											jQuery('#viewportoptionsofslider').show();
										} else {
											jQuery('#viewportoptionsofslider').hide();
										}
										drawToolBarPreview();
								});


								jQuery('#enable_progressbar').on("change",function() {
									var sbi = jQuery(this);
									if (sbi.attr('checked')!=="checked")
										jQuery('#progressbar_settings').hide();
									else
										jQuery('#progressbar_settings').show();
									drawToolBarPreview();
								});

								jQuery('#progress_height').on("keyup",drawToolBarPreview);
								jQuery('#progress_opa').on("keyup",drawToolBarPreview);

								jQuery('#enable_progressbar').change();
								jQuery('#stop_slider').change();
								jQuery('#show_timerbar').change();

								// ALTERNATIVE FIRST SLIDE
								jQuery('#first_transition_active').on("change",function() {
										var sbi = jQuery(this);

										if (sbi.attr("checked") === "checked") {
											jQuery('#first_transition_row').show();

										} else {
											jQuery('#first_transition_row').hide();
										}
								});
								jQuery('#first_transition_active').change();
							</script>
						</div><!-- END OF GENERAL SETTINGS -->

						<!-- LAYOUT VISAL SETTINGS -->
						<div class="setting_box">
							<h3 class="box_closed"><i class="rs-rp-accordion-icon eg-icon-droplet"></i>
								<div class="setting_box-arrow"></div>
								<span><?php echo  JText::_("Layout & Visual");?></span>
							</h3>

							<div class="inside" style="display:none">
								<ul class="main-options-small-tabs" style="display:inline-block; ">
									<li data-content="#visual-appearance" class="selected"><?php echo  JText::_('Appearance');?></li>
									<!--<li data-content="#visual-sizing"><?php echo  JText::_('Sizing');?></li>									-->
									<li data-content="#visual-spinner"><?php echo  JText::_('Spinner');?></li>
									<li data-content="#visual-mobile"><?php echo  JText::_('Mobile');?></li>
									<li data-content="#visual-position"><?php echo  JText::_('Position');?></li>
								</ul>

								<!-- VISUAL Mobile -->
								<div id="visual-mobile" style="display:none">
									<span class="label" id="label_disable_on_mobile" origtitle="<?php echo  JText::_("If this is enabled, the slider will not be loaded on mobile devices.");?>"><?php echo  JText::_("Disable Slider on Mobile");?></span>
									<input type="checkbox" class="tp-moderncheckbox withlabel" id="disable_on_mobile" name="disable_on_mobile" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "disable_on_mobile", "off"), "on");?>>
									<div class="clear"></div>


									<span class="label" id="label_disable_kenburns_on_mobile"  origtitle="<?php echo  JText::_("This will disable KenBurns on mobile devices to save performance");?>"><?php echo  JText::_("Disable KenBurn On Mobile");?></span>
									<input type="checkbox" class="tp-moderncheckbox withlabel" id="disable_kenburns_on_mobile" name="disable_kenburns_on_mobile" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "disable_kenburns_on_mobile", "off"), "on");?>>
									<div class="clear"></div>

									<h4><?php echo  JText::_("Hide Element Under Width:");?></h4>

									<span class="label" id="label_hide_slider_under"  origtitle="<?php echo  JText::_("Hide the slider under the defined slider width. Value 0 will disable the function.");?>"><?php echo  JText::_("Slider");?></span>
									<input type="text" class="text-sidebar withlabel" id="hide_slider_under" name="hide_slider_under" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "hide_slider_under", "0");?>">
									<span><?php echo  JText::_("px");?></span>
									<div class="clear"></div>


									<span class="label" id="label_hide_defined_layers_under" style="font-size:12px" origtitle="<?php echo  JText::_("Hide the selected layers (set layers hide under in slide editor) under the defined slider width. Value 0 will disable the function.");?>"><?php echo  JText::_("Predefined Layers");?></span>
									<input type="text" class="text-sidebar withlabel" id="hide_defined_layers_under" name="hide_defined_layers_under" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "hide_defined_layers_under", "0");?>">
									<span><?php echo  JText::_("px");?></span>
									<div class="clear"></div>

									<span class="label" id="label_hide_all_layers_under"  origtitle="<?php echo  JText::_("Hide all layers under the defined slider width. Value 0 will disable the function.");?>"><?php echo  JText::_("All Layers");?></span>
									<input type="text" class="text-sidebar withlabel" id="hide_all_layers_under" name="hide_all_layers_under" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "hide_all_layers_under", "0");?>">
									<span><?php echo  JText::_("px");?></span>
									<div class="clear"></div>

								</div><!-- VISUAL MOBILE -->


								<!-- VISUAL APPEARANCE -->
								<div id="visual-appearance" style="display:block">
									<div class="hide_on_ddd_parallax">
										<span class="label " id="label_shadow_type" origmedia='show' origtitle="<?php echo  JText::_("The Shadow display underneath the banner.");?>"><?php echo  JText::_("Shadow Type");?> </span>
										<select id="shadow_type"  class="withlabel" name="shadow_type">
											<option value="0" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'shadow_type', '0'), "0");?>><?php echo  JText::_("No Shadow");?></option>
											<option value="1" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'shadow_type', '0'), "1");?>>1</option>
											<option value="2" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'shadow_type', '0'), "2");?>>2</option>
											<option value="3" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'shadow_type', '0'), "3");?>>3</option>
											<option value="4" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'shadow_type', '0'), "4");?>>4</option>
											<option value="5" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'shadow_type', '0'), "5");?>>5</option>
											<option value="6" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'shadow_type', '0'), "6");?>>6</option>
										</select>
										<div class="clear"></div>
									</div>
									<span class="label" id="label_background_dotted_overlay" origmedia="show" origtitle="<?php echo  JText::_("Show a dotted overlay over the slides.");?>"><?php echo  JText::_("Dotted Overlay Size");?> </span>
									<select id="background_dotted_overlay" name="background_dotted_overlay" class="withlabel">
										<option value="none" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'background_dotted_overlay', 'none'), "none");?>><?php echo  JText::_("none");?></option>
										<option value="twoxtwo" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'background_dotted_overlay', 'none'), "twoxtwo");?>><?php echo  JText::_("2 x 2 Black");?></option>
										<option value="twoxtwowhite" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'background_dotted_overlay', 'none'), "twoxtwowhite");?>><?php echo  JText::_("2 x 2 White");?></option>
										<option value="threexthree" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'background_dotted_overlay', 'none'), "threexthree");?>><?php echo  JText::_("3 x 3 Black");?></option>
										<option value="threexthreewhite" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'background_dotted_overlay', 'none'), "threexthreewhite");?>><?php echo  JText::_("3 x 3 White");?></option>
										</select>
									<div class="clear"></div>

									<h4><?php echo  JText::_("Slider Background");?></h4>
									<span class="label" id="label_background_color" origmedia="showbg" origtitle="<?php echo  JText::_("General background color for slider. Clear value to get transparent slider container.");?>"><?php echo  JText::_("Background color");?> </span>
									<input type="text"  class="my-color-field rs-layer-input-field tipsy_enabled_top withlabel" title="<?php echo  JText::_("Font Color");?>"  id="background_color" name="background_color" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'background_color', 'transparent');?>" />
									<div class="clear"></div>

									<span class="label" id="label_padding"  origmedia="showbg" origtitle="<?php echo  JText::_("Padding around the slider. Together with background color shows as slider border.");?>"><?php echo  JText::_("Padding as Border");?> </span>
									<input type="text" class="text-sidebar withlabel"  id="padding" name="padding" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'padding', '0');?>">
									<div class="clear"></div>

									<span class="label" id="label_show_background_image"  origmedia="showbg" origtitle="<?php echo  JText::_("Use a general background image instead of general background color.");?>"><?php echo  JText::_("Show Background Image");?> </span>
									<input type="checkbox"  class="tp-moderncheckbox withlabel" id="show_background_image" name="show_background_image" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, 'show_background_image', 'off'), "on");?>>
									<div class="clear"></div>

									<div id="background_settings" class="withsublabels">
										<span class="label" id="label_background_image" origmedia="showbg" origtitle="<?php echo  JText::_("The source of the general background image.");?>"><?php echo  JText::_("Background Image Url");?> </span>
										<input type="text" class="text-sidebar-long withlabel" style="width: 104px;" id="background_image" name="background_image" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'background_image', '');?>"> <a href="javascript:void(0)" class="button-image-select-bg-img button-primary revblue"><?php echo  JText::_('Set'); ?></a>
										<div class="clear"></div>

										<span class="label" id="label_bg_fit" origmedia="showbg" origtitle="<?php echo  JText::_("General background image size. Cover - always fill the container, cuts overlays. Contain- always fits image into slider.");?>"><?php echo  JText::_("Background Fit");?> </span>
										<select id="bg_fit"  name="bg_fit" class="withlabel">
											<option value="cover" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'bg_fit', 'cover'), "cover");?>><?php echo  JText::_("cover");?></option>
											<option value="contain" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'bg_fit', 'cover'), "contain");?>><?php echo  JText::_("contain");?></option>
											<option value="normal" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'bg_fit', 'cover'), "normal");?>><?php echo  JText::_("normal");?></option>
										</select>
										<div class="clear"></div>

										<span class="label" id="label_bg_repeat" origmedia="showbg" origtitle="<?php echo  JText::_("General background image repeat attitude. Used for tiled images.");?>"><?php echo  JText::_("Background Repeat");?> </span>
										<select id="bg_repeat" name="bg_repeat" class="withlabel">
											<option value="no-repeat" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'bg_repeat', 'no-repeat'), "no-repeat");?>><?php echo  JText::_("no-repeat");?></option>
											<option value="repeat" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'bg_repeat', 'no-repeat'), "repeat");?>><?php echo  JText::_("repeat");?></option>
											<option value="repeat-x" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'bg_repeat', 'no-repeat'), "repeat-x");?>><?php echo  JText::_("repeat-x");?></option>
											<option value="repeat-y" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'bg_repeat', 'no-repeat'), "repeat-y");?>><?php echo  JText::_("repeat-y");?></option>
										</select>
										<div class="clear"></div>

										<span class="label" id="label_bg_position" origmedia="showbg" origtitle="<?php echo  JText::_("General background image position.  i.e. center center to always center vertical and horizontal the image in the slider background.");?>"><?php echo  JText::_("Background Position");?> </span>
										<select id="bg_position" name="bg_position" class="withlabel">
											<option value="center top" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'bg_position', 'center center'), "center top");?>><?php echo  JText::_("center top");?></option>
											<option value="center right" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'bg_position', 'center center'), "center right");?>><?php echo  JText::_("center right");?></option>
											<option value="center bottom" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'bg_position', 'center center'), "center bottom");?>><?php echo  JText::_("center bottom");?></option>
											<option value="center center" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'bg_position', 'center center'), "center center");?>><?php echo  JText::_("center center");?></option>
											<option value="left top" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'bg_position', 'center center'), "left top");?>><?php echo  JText::_("left top");?></option>
											<option value="left center" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'bg_position', 'center center'), "left center");?>><?php echo  JText::_("left center");?></option>
											<option value="left bottom" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'bg_position', 'center center'), "left bottom");?>><?php echo  JText::_("left bottom");?></option>
											<option value="right top" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'bg_position', 'center center'), "right top");?>><?php echo  JText::_("right top");?></option>
											<option value="right center" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'bg_position', 'center center'), "right center");?>><?php echo  JText::_("right center");?></option>
											<option value="right bottom" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'bg_position', 'center center'), "right bottom");?>><?php echo  JText::_("right bottom");?></option>
										</select>
										<div class="clear"></div>
									</div>
								</div>	<!-- / VISUAL APPEARANCE -->



								<!-- VISUAL POSITION -->
								<div id="visual-position" style="display:none;">
									<span class="label" id="label_position" origtitle="<?php echo  JText::_("The position of the slider within the parrent container. (float:left or float:right or with margin:0px auto;). We recomment do use always CENTER, since the slider will auto fill and grow with the wrapping container. Set any border,padding, floating etc. to the wrapping container where the slider embeded instead of using left/right here !");?>"><?php echo  JText::_("Position on the page");?> </span>
									<select id="position" class="withlabel"  name="position">
										<option value="left" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'position', 'center'), "left");?>><?php echo  JText::_("Left");?></option>
										<option value="center" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'position', 'center'), "center");?>><?php echo  JText::_("Center");?></option>
										<option value="right" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'position', 'center'), "right");?>><?php echo  JText::_("Right");?></option>
									</select>
									<div class="clear"></div>


									<span class="label" id="label_margin_top" origtitle="<?php echo  JText::_("The top margin of the slider wrapper div");?>"><?php echo  JText::_("Margin Top");?> </span>
									<input type="text" class="text-sidebar withlabel"   id="margin_top" name="margin_top" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'margin_top', '0');?>">
									<span><?php echo  JText::_("px");?></span>
									<div class="clear"></div>


									<span class="label" id="label_margin_bottom" origtitle="<?php echo  JText::_("The bottom margin of the slider wrapper div");?>"><?php echo  JText::_("Margin Bottom");?> </span>
									<input type="text" class="text-sidebar withlabel" id="margin_bottom" name="margin_bottom" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'margin_bottom', '0');?>">
									<span><?php echo  JText::_("px");?></span>
									<div class="clear"></div>

									<div id="leftrightmargins">
									   <span class="label" id="label_margin_left" origtitle="<?php echo  JText::_("The left margin of the slider wrapper div");?>"><?php echo  JText::_("Margin Left");?> </span>
									   <input type="text" class="text-sidebar withlabel" id="margin_left" name="margin_left" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'margin_left', '0');?>">
									   <span><?php echo  JText::_("px");?></span>
									   <div class="clear"></div>

									   <span class="label" id="label_margin_right" origtitle="<?php echo  JText::_("The right margin of the slider wrapper div");?>"><?php echo  JText::_("Margin Right");?> </span>
									   <input type="text" class="text-sidebar withlabel"  id="margin_right" name="margin_right" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'margin_right', '0');?>">
									   <span><?php echo  JText::_("px");?></span>
									   <div class="clear"></div>
									 </div>
								</div> <!-- / VISUAL POSITION -->

								<!-- VISUAL SPINNER -->
								<div id="visual-spinner"  style="display:none;">
								   <div id="spinner_preview"><div class="tp-loader tp-demo spinner2" style="background-color: rgb(255, 255, 255);"><div class="dot1"></div><div class="dot2"></div><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>
								   <div style="height:15px;width:100%;"></div>
									<span class="label" id="label_use_spinner" origtitle="<?php echo  JText::_("Select a Spinner for your Slider");?>"><?php echo  JText::_("Choose Spinner");?> </span>
									<select id="use_spinner" name="use_spinner" class="withlabel">
										<option value="-1" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "use_spinner", "0"), "-1");?>><?php echo  JText::_("Off");?></option>
										<option value="0" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "use_spinner", "0"), "0");?>><?php echo  JText::_("0");?></option>
										<option value="1" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "use_spinner", "0"), "1");?>><?php echo  JText::_("1");?></option>
										<option value="2" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "use_spinner", "0"), "2");?>><?php echo  JText::_("2");?></option>
										<option value="3" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "use_spinner", "0"), "3");?>><?php echo  JText::_("3");?></option>
										<option value="4" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "use_spinner", "0"), "4");?>><?php echo  JText::_("4");?></option>
										<option value="5" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "use_spinner", "0"), "5");?>><?php echo  JText::_("5");?></option>
									</select>
									<div class="clear"></div>
									<div id="spinner_color_row">
										<span id="label_spinner_color" class="label" origtitle="<?php echo  JText::_("The Color the Spinner will be shown in");?>"><?php echo  JText::_("Spinner Color");?> </span>
										<input type="text" class="my-color-field rs-layer-input-field tipsy_enabled_top withlabel" title="<?php echo  JText::_("Font Color");?>"  id="spinner_color" name="spinner_color" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "spinner_color", "#FFFFFF");?>" />
									   </div>
									<div class="clear"></div>
								</div>	<!-- / VISUAL SPINNER -->

							</div>

							<script type="text/javascript">
								jQuery(document).on("ready",function() {
									/**
									* set shadow type
									*/
									// SHADOW TYPES
									jQuery("#shadow_type").change(function() {
										var sel = jQuery(this).val();

										drawToolBarPreview();
									});

									// BACKGROUND IMAGE SCRIPT
									jQuery('#show_background_image').on("change",function() {
											var sbi = jQuery(this);
											if (sbi.attr("checked") === "checked") {
												jQuery('#background_settings').show();
											} else {
												jQuery('#background_settings').hide();
											}
									});
									jQuery('#show_background_image').change();
									jQuery('#padding').change(drawToolBarPreview).on("keyup",drawToolBarPreview);
									jQuery('#background_dotted_overlay').change(drawToolBarPreview);

									// POSITION SCRIPT
									jQuery('#position').on("change",function() {
										var sbi = jQuery(this);
										switch (jQuery(this).val()) {
											case "left":
											case "right":
												jQuery('#leftrightmargins').show();
											break;
											case "center":
												jQuery('#leftrightmargins').hide();
											break;
										}
										drawToolBarPreview();
									});
									jQuery('#position').change();

									// SPINNER SCRIPT
									 jQuery('#use_spinner').on("change",function() {
											switch (jQuery(this).val()) {
												case "-1":
												case "0":
												case "5":
												   jQuery('#spinner_color_row').hide();
												break;
												default:
													jQuery('#spinner_color_row').show();
												break;
											}
									   });
									   jQuery('#use_spinner').change();

									 // TAB CHANGES
									 jQuery('.main-options-small-tabs').find('li').click(function() {
									 	var li = jQuery(this),
									 		ul = li.closest('.main-options-small-tabs'),
									 		ref = li.data('content');

										jQuery(ul.find('.selected').data('content')).hide();
									 	ul.find('.selected').removeClass("selected");

									 	jQuery(ref).show();
									 	li.addClass("selected");

									 	if (ref=='#navigation-arrows' || ref=='#navigation-bullets' || ref=='#navigation-tabs'|| ref=='#navigation-thumbnails')									 		
									 		jQuery('#navigation-miniimagedimensions').show();
									 	else
									 		if (!jQuery('#navigation-settings-wrapper>h3').hasClass("box_closed")) 
									 			jQuery('#navigation-miniimagedimensions').hide();

									 })
								});
							</script>
						</div> <!-- END OF LAYOUT VISUAL SETTINGS -->

						<!-- NAVIGATION SETTINGS -->
						<div class="setting_box dontshowonhero" id="navigation-settings-wrapper">
							<h3 class="box_closed"><i class="rs-rp-accordion-icon eg-icon-flickr"></i>
								<div class="setting_box-arrow"></div>
								<span><?php echo  JText::_('Navigation');?></span>
							</h3>

							<div class="inside" style="display:none;">
								<ul class="main-options-small-tabs" style="display:inline-block; ">
									<li data-content="#navigation-arrows" class="selected"><?php echo  JText::_('Arrows');?></li>
									<li data-content="#navigation-bullets"><?php echo  JText::_('Bullets');?></li>
									<li data-content="#navigation-tabs"><?php echo  JText::_('Tabs');?></li>
									<li data-content="#navigation-thumbnails"><?php echo  JText::_('Thumbs');?></li>
									<li data-content="#navigation-touch"><?php echo  JText::_('Touch');?></li>
									<li data-content="#navigation-keyboard"><?php echo  JText::_('Misc.');?></li>
								</ul>

								<!-- NAVIGATION ARROWS -->
								<div id="navigation-arrows">
									<span class="label" id="label_enable_arrows"  origtitle="<?php echo  JText::_("Enable / Disable Arrows");?>"><?php echo  JText::_("Enable Arrows");?> </span>
									<input type="checkbox" class="tp-moderncheckbox withlabel" id="enable_arrows" name="enable_arrows" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "enable_arrows", "off"), "on");?>>

									<div id="nav_arrows_subs">
										<span class="label" id="label_rtl_arrows"  origtitle="<?php echo  JText::_("Change Direction of Arrow Functions for RTL Support");?>"><?php echo  JText::_("RTL Direction");?> </span>
										<input type="checkbox" class="tp-moderncheckbox withlabel" id="rtl_arrows" name="rtl_arrows" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "rtl_arrows", "off"), "on");?>>
										<span class="label triggernavstyle" id="label_navigation_arrow_style" origtitle="<?php echo  JText::_("Look of the navigation Arrows");?>"><?php echo  JText::_("Arrows Style");?></span>
										<select id="navigation_arrow_style" name="navigation_arrow_style" class=" withlabel triggernavstyle">
											<option value="" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'navigation_arrow_style', 'round'), ''); ?>><?php echo  JText::_('No Style'); ?></option>
											<?php
											if (!empty($arr_navigations)) {
												$mav = ZTSliderFunctions::getVal($this->arrFieldsParams, 'navigation_arrow_style', 'round');
												foreach ($arr_navigations as $cur_nav) {
													if (isset($cur_nav['markup']['arrows'])) {
														?>
														<option value="<?php echo $cur_nav['handle'];?>" <?php echo ZTSliderFunctions::selected($mav, $cur_nav['handle']);?>><?php echo $cur_nav['name'];?></option>
														<?php
													}
												}
											}
											?>
										</select>
										<div class="clear"></div>
										<span class="label triggernavstyle" id="label_navigation_arrows_preset" origtitle="<?php echo  JText::_("Preset");?>"><?php echo  JText::_("Preset");?></span>
										<select id="navigation_arrows_preset" name="navigation_arrows_preset" class="withlabel" data-startvalue="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'navigation_arrows_preset', 'default'); ?>">
											<option class="never" value="default" selected="selected"><?php echo  JText::_('Default'); ?></option>
											<option class="never" value="custom"><?php echo  JText::_('Custom'); ?></option>
										</select>
										<div class="clear"></div>
										
										<div data-navtype="arrows" class="toggle-custom-navigation-style-wrapper">
											<div class="toggle-custom-navigation-style triggernavstyle"><?php echo  JText::_("Toggle Custom Navigation Styles");?></div>
											<div class="toggle-custom-navigation-styletarget navigation_arrow_placeholder triggernavstyle" style="display:none">												
											</div>		
										</div>
										<div class="clear"></div>

										<h4><?php echo  JText::_("Visibility");?></h4>
										<span class="label" id="label_arrows_always_on" origtitle="<?php echo  JText::_("Enable to make arrows always visible. Disable to hide arrows after the defined time.");?>"><?php echo  JText::_("Always Show ");?></span>
										<select id="arrows_always_on" name="arrows_always_on" class=" withlabel showhidewhat_truefalse" data-showhidetarget="hide_after_arrow">
											<option value="false" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'arrows_always_on', 'false'), "false");?>><?php echo  JText::_("Yes");?></option>
											<option value="true" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'arrows_always_on', 'false'), "true");?>><?php echo  JText::_("No");?></option>
										</select>
										<div class="clear"></div>

										<div id="hide_after_arrow">
											<span class="label" id="label_hide_arrows" origtitle="<?php echo  JText::_("Time after the Arrows will be hidden(Default: 200 ms)");?>"><?php echo  JText::_("Hide After");?></span>
											<input type="text" class="text-sidebar withlabel" id="hide_arrows" name="hide_arrows" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'hide_arrows', '200');?>">
											<span><?php echo  JText::_("ms");?></span>
											<div class="clear"></div>

											<span class="label" id="label_hide_arrows_mobile" origtitle="<?php echo  JText::_("Time after the Arrows will be hidden on Mobile(Default: 1200 ms)");?>"><?php echo  JText::_("Hide After on Mobile");?></span>
											<input type="text" class="text-sidebar withlabel" id="hide_arrows_mobile" name="hide_arrows_mobile" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'hide_arrows_mobile', '1200');?>">
											<span><?php echo  JText::_("ms");?></span>
											<div class="clear"></div>
										</div>

										<span class="label" id="label_hide_arrows_on_mobile"  origtitle="<?php echo  JText::_("Force Hide Navigation Arrows under width");?>"><?php echo  JText::_("Hide Under");?> </span>
										<input type="checkbox" class="tp-moderncheckbox withlabel showhidewhat_truefalse" data-showhidetarget="hide_under_arrow" id="hide_arrows_on_mobile" name="hide_arrows_on_mobile" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "hide_arrows_on_mobile", "off"), "on");?>>
										<div class="clear"></div>

										<div id="hide_under_arrow" class="withsublabels">
											<span id="label_arrows_under_hidden" class="label" origtitle="<?php echo  JText::_("If browser size goes below this value, then Navigation Arrows are hidden.");?>"><?php echo  JText::_("Width");?> </span>
											<input type="text" class="text-sidebar withlabel" id="arrows_under_hidden" name="arrows_under_hidden" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'arrows_under_hidden', '0');?>">
											<span><?php echo  JText::_("px");?></span>
											<div class="clear"></div>
										</div>

										<span class="label" id="label_hide_arrows_over"  origtitle="<?php echo  JText::_("Force Hide Navigation over width");?>"><?php echo  JText::_("Hide Over");?> </span>
										<input type="checkbox" class="tp-moderncheckbox withlabel showhidewhat_truefalse" data-showhidetarget="hide_over_arrow" id="hide_arrows_over" name="hide_arrows_over" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "hide_arrows_over", "off"), "on");?>>
										<div class="clear"></div>

										<div id="hide_over_arrow" class="withsublabels">
											<span id="label_arrows_over_hidden" class="label" origtitle="<?php echo  JText::_("If browser size goes over this value, then Navigation Arrows are hidden.");?>"><?php echo  JText::_("Width");?> </span>
											<input type="text" class="text-sidebar withlabel" id="arrows_over_hidden" name="arrows_over_hidden" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'arrows_over_hidden', '0');?>">
											<span><?php echo  JText::_("px");?></span>
											<div class="clear"></div>
										</div>

										<h4><?php echo  JText::_("Left Arrow Position");?></h4>

										<span class="label" id="label_leftarrow_align_hor" origtitle="<?php echo  JText::_("Horizontal position of the left arrow.");?>"><?php echo  JText::_("Horizontal Align");?></span>
										<select id="leftarrow_align_hor" name="leftarrow_align_hor" class="withlabel">
											<option value="left" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "leftarrow_align_hor", "left"), "left");?>><?php echo  JText::_("Left");?></option>
											<option value="center" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "leftarrow_align_hor", "left"), "center");?>><?php echo  JText::_("Center");?></option>
											<option value="right" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "leftarrow_align_hor", "left"), "right");?>><?php echo  JText::_("Right");?></option>
										</select>
										<div class="clear"></div>


										<span class="label" id="label_leftarrow_align_vert" origtitle="<?php echo  JText::_("Vertical position of the left arrow.");?>"><?php echo  JText::_("Vertical Align");?> </span>
										<select id="leftarrow_align_vert" name="leftarrow_align_vert" class="withlabel">
											<option value="top" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "leftarrow_align_vert", "center"), "top");?>><?php echo  JText::_("Top");?></option>
											<option value="center" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "leftarrow_align_vert", "center"), "center");?>><?php echo  JText::_("Center");?></option>
											<option value="bottom" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "leftarrow_align_vert", "center"), "bottom");?>><?php echo  JText::_("Bottom");?></option>
										</select>
										<div class="clear"></div>

										<span id="label_leftarrow_offset_hor" class="label" origtitle="<?php echo  JText::_("Offset from current horizontal position of of left arrow.");?>"><?php echo  JText::_("Horizontal Offset");?> </span>
										<input type="text" class="text-sidebar withlabel" id="leftarrow_offset_hor" name="leftarrow_offset_hor" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'leftarrow_offset_hor', '20');?>">
										<span><?php echo  JText::_("px");?></span>
										<div class="clear"></div>

										<span id="label_leftarrow_offset_vert" class="label" origtitle="<?php echo  JText::_("Offset from current vertical position of of left arrow.");?>"><?php echo  JText::_("Vertical Offset");?> </span>
										<input type="text" class="text-sidebar withlabel" id="leftarrow_offset_vert" name="leftarrow_offset_vert" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "leftarrow_offset_vert", "0");?>">
										<span><?php echo  JText::_("px");?></span>
										<div class="clear"></div>
										
										<span class="label" id="label_leftarrow_position" origtitle="<?php echo  JText::_("Position the Left Arrow to Slider or Layer Grid");?>"><?php echo  JText::_("Aligned by");?></span>
										<select id="leftarrow_position" name="leftarrow_position" class="withlabel">
											<option value="slider" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "leftarrow_position", "slider"), "slider");?>><?php echo  JText::_("Slider");?></option>
											<option value="grid" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "leftarrow_position", "slider"), "grid");?>><?php echo  JText::_("Layer Grid");?></option>
										</select>
										<div class="clear"></div>

										<h4><?php echo  JText::_("Right Arrow Position");?></h4>
										<span class="label" id="label_rightarrow_align_hor" origtitle="<?php echo  JText::_("Horizontal position of the right arrow.");?>"><?php echo  JText::_("Horizontal Align");?> </span>
										<select id="rightarrow_align_hor" name="rightarrow_align_hor" class="withlabel">
											<option value="left" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "rightarrow_align_hor", "right"), "left");?>><?php echo  JText::_("Left");?></option>
											<option value="center" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "rightarrow_align_hor", "right"), "center");?>><?php echo  JText::_("Center");?></option>
											<option value="right" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "rightarrow_align_hor", "right"), "right");?>><?php echo  JText::_("Right");?></option>
										</select>

										<div class="clear"></div>


										<span id="label_rightarrow_align_vert" class="label" origtitle="<?php echo  JText::_("Vertical position of the right arrow.");?>"><?php echo  JText::_("Vertical Align");?> </span>
										<select id="rightarrow_align_vert" name="rightarrow_align_vert" class="withlabel">
											<option value="top" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "rightarrow_align_vert", "center"), "top");?>><?php echo  JText::_("Top");?></option>
											<option value="center" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "rightarrow_align_vert", "center"), "center");?>><?php echo  JText::_("Center");?></option>
											<option value="bottom" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "rightarrow_align_vert", "center"), "bottom");?>><?php echo  JText::_("Bottom");?></option>
										</select>

										<div class="clear"></div>


										<span id="label_rightarrow_offset_hor" class="label" origtitle="<?php echo  JText::_("Offset from current horizontal position of of right arrow.");?>"><?php echo  JText::_("Horizontal Offset");?> </span>
										<input type="text" class="text-sidebar withlabel" id="rightarrow_offset_hor" name="rightarrow_offset_hor" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "rightarrow_offset_hor", "20");?>">
										<span><?php echo  JText::_("px");?></span>
										<div class="clear"></div>


										<span id="label_rightarrow_offset_vert" class="label" origtitle="<?php echo  JText::_("Offset from current vertical position of of right arrow.");?>"><?php echo  JText::_("Vertical Offset");?></span>
										<input type="text" class="text-sidebar withlabel" id="rightarrow_offset_vert" name="rightarrow_offset_vert" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "rightarrow_offset_vert", "0");?>">
										<span><?php echo  JText::_("px");?></span>
										<div class="clear"></div>
										
										<span class="label" id="label_rightarrow_position" origtitle="<?php echo  JText::_("Position the Right Arrow to Slider or Layer Grid");?>"><?php echo  JText::_("Aligned by");?></span>
										<select id="rightarrow_position" name="rightarrow_position" class="withlabel">
											<option value="slider" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "rightarrow_position", "slider"), "slider");?>><?php echo  JText::_("Slider");?></option>
											<option value="grid" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "rightarrow_position", "slider"), "grid");?>><?php echo  JText::_("Layer Grid");?></option>
										</select>
										<div class="clear"></div>
									</div>
								</div><!-- END OF NAVIGATION ARROWS -->
								
								<!-- NAVIGATION BULLETS -->
								<div id="navigation-bullets" style="display:none;">

									<span class="label" id="label_enable_bullets"  origtitle="<?php echo  JText::_("Enable / Disable Bullets");?>"><?php echo  JText::_("Enable Bullets");?> </span>
									<input type="checkbox" class="tp-moderncheckbox withlabel" id="enable_bullets" name="enable_bullets" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "enable_bullets", "off"), "on");?>>

									<div id="nav_bullets_subs">
										<span class="label" id="label_rtl_bullets"  origtitle="<?php echo  JText::_("Change Direction of Bullet Functions for RTL Support");?>"><?php echo  JText::_("RTL Direction");?> </span>
										<input type="checkbox" class="tp-moderncheckbox withlabel" id="rtl_bullets" name="rtl_bullets" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "rtl_bullets", "off"), "on");?>>

										<span class="label triggernavstyle" id="label_navigation_bullets_style" origtitle="<?php echo  JText::_("Look of the Bullets");?>"><?php echo  JText::_("Bullet Style");?></span>
										<select id="navigation_bullets_style" name="navigation_bullets_style" class="triggernavstyle withlabel">
											<option value="" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'navigation_bullets_style', 'round'), ''); ?>><?php echo  JText::_('No Style'); ?></option>
											<?php
											if (!empty($arr_navigations)) {
												foreach ($arr_navigations as $cur_nav) {
													if (isset($cur_nav['markup']['bullets'])) {
														?>
														<option value="<?php echo $cur_nav['handle'];?>" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'navigation_bullets_style', 'round'), $cur_nav['handle']);?>><?php echo $cur_nav['name'];?></option>
														<?php
													}
												}
											}
											?>
										</select>
										<div class="clear"></div>
										<span class="label triggernavstyle" id="label_navigation_bullets_preset" origtitle="<?php echo  JText::_("Preset");?>"><?php echo  JText::_("Preset");?></span>
										<select id="navigation_bullets_preset" name="navigation_bullets_preset" class="triggernavstyle withlabel" data-startvalue="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'navigation_bullets_preset', 'default'); ?>">
											<option class="never" value="default" selected="selected"><?php echo  JText::_('Default'); ?></option>
											<option class="never" value="custom"><?php echo  JText::_('Custom'); ?></option>
										</select>
										<div class="clear"></div>
										<div data-navtype="bullets" class="toggle-custom-navigation-style-wrapper">
											<div class="toggle-custom-navigation-style"><?php echo  JText::_("Toggle Custom Navigation Styles");?></div>
											<div class="toggle-custom-navigation-styletarget navigation_bullets_placeholder">
											</div>
										</div>
										<div class="clear"></div>
										
										<span class="label" id="label_bullets_space" origtitle="<?php echo  JText::_("Space between the bullets.");?>"><?php echo  JText::_("Space");?></span>
										<input type="text" class="text-sidebar withlabel" id="bullets_space" name="bullets_space" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'bullets_space', '5');?>">
										<span><?php echo  JText::_("px");?></span>
										<div class="clear"></div>


										<span class="label" id="label_bullets_direction" origtitle="<?php echo  JText::_("Direction of the Bullets. Vertical or Horizontal.");?>"><?php echo  JText::_("Direction");?></span>
										<select id="bullets_direction" name="bullets_direction" class=" withlabel">
											<option value="horizontal" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'bullets_direction', 'horizontal'), "horizontal");?>><?php echo  JText::_("Horizontal");?></option>
											<option value="vertical" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'bullets_direction', 'horizontal'), "vertical");?>><?php echo  JText::_("Vertical");?></option>
										</select>
										<div class="clear"></div>


										<h4><?php echo  JText::_("Visibility");?></h4>

										<span class="label" id="label_bullets_always_on" origtitle="<?php echo  JText::_("Enable to make bullets always visible. Disable to hide bullets after the defined time.");?>"><?php echo  JText::_("Always Show");?></span>
										<select id="bullets_always_on" name="bullets_always_on" class=" withlabel showhidewhat_truefalse" data-showhidetarget="hide_after_bullets">
											<option value="false" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'bullets_always_on', 'false'), "false");?>><?php echo  JText::_("Yes");?></option>
											<option value="true" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'bullets_always_on', 'false'), "true");?>><?php echo  JText::_("No");?></option>
										</select>
										<div class="clear"></div>
										<div id="hide_after_bullets">
											<span class="label" id="label_hide_bullets" origtitle="<?php echo  JText::_("Time after that the bullets will be hidden(Default: 200 ms)");?>"><?php echo  JText::_("Hide After");?></span>
											<input type="text" class="text-sidebar withlabel" id="hide_bullets" name="hide_bullets" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'hide_bullets', '200');?>">
											<span><?php echo  JText::_("ms");?></span>
											<div class="clear"></div>

											<span class="label" id="label_hide_bullets_mobile" origtitle="<?php echo  JText::_("Time after the bullets will be hidden on Mobile (Default: 1200 ms)");?>"><?php echo  JText::_("Hide After on Mobile");?></span>
											<input type="text" class="text-sidebar withlabel" id="hide_bullets_mobile" name="hide_bullets_mobile" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'hide_bullets_mobile', '1200');?>">
											<span><?php echo  JText::_("ms");?></span>
											<div class="clear"></div>
										</div>

										<span class="label" id="label_hide_bullets_on_mobile"  origtitle="<?php echo  JText::_("Force Hide Navigation Bullets under width");?>"><?php echo  JText::_("Hide under Width");?> </span>
										<input type="checkbox" class="tp-moderncheckbox withlabel showhidewhat_truefalse" data-showhidetarget="hide_under_bullet" id="hide_bullets_on_mobile" name="hide_bullets_on_mobile" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "hide_bullets_on_mobile", "off"), "on");?>>
										<div class="clear"></div>

										<div id="hide_under_bullet" class="withsublabels">
											<span id="label_bullets_under_hidden" class="label" origtitle="<?php echo  JText::_("If browser size goes below this value, then Navigation bullets are hidden.");?>"><?php echo  JText::_("Width");?> </span>
											<input type="text" class="text-sidebar withlabel" id="bullets_under_hidden" name="bullets_under_hidden" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'bullets_under_hidden', '0');?>">
											<span><?php echo  JText::_("px");?></span>
											<div class="clear"></div>
										</div>

										<span class="label" id="label_hide_bullets_over"  origtitle="<?php echo  JText::_("Force Hide Navigation Bullets over width");?>"><?php echo  JText::_("Hide over Width");?> </span>
										<input type="checkbox" class="tp-moderncheckbox withlabel showhidewhat_truefalse" data-showhidetarget="hide_over_bullet" id="hide_bullets_over" name="hide_bullets_over" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "hide_bullets_over", "off"), "on");?>>
										<div class="clear"></div>

										<div id="hide_over_bullet" class="withsublabels">
											<span id="label_bullets_over_hidden" class="label" origtitle="<?php echo  JText::_("If browser size goes below this value, then Navigation bullets are hidden.");?>"><?php echo  JText::_("Width");?> </span>
											<input type="text" class="text-sidebar withlabel" id="bullets_over_hidden" name="bullets_over_hidden" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'bullets_over_hidden', '0');?>">
											<span><?php echo  JText::_("px");?></span>
											<div class="clear"></div>
										</div>

										<h4><?php echo  JText::_("Position");?></h4>
										<span class="label" id="label_bullets_align_hor" origtitle="<?php echo  JText::_("Horizontal position of bullets ");?>"><?php echo  JText::_("Horizontal Align");?></span>
										<select id="bullets_align_hor" name="bullets_align_hor" class=" withlabel">
											<option value="left" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'bullets_align_hor', 'center'), "left");?>><?php echo  JText::_("Left");?></option>
											<option value="center" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'bullets_align_hor', 'center'), "center");?>><?php echo  JText::_("Center");?></option>
											<option value="right" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'bullets_align_hor', 'center'), "right");?>><?php echo  JText::_("Right");?></option>
										</select>
										<div class="clear"></div>

										<span class="label" id="label_bullets_align_vert" origtitle="<?php echo  JText::_("Vertical positions of bullets ");?>"><?php echo  JText::_("Vertical Align");?></span>
										<select id="bullets_align_vert" name="bullets_align_vert" class="withlabel">
											<option value="top" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'bullets_align_vert', 'bottom'), "top");?>><?php echo  JText::_("Top");?></option>
											<option value="center" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'bullets_align_vert', 'bottom'), "center");?>><?php echo  JText::_("Center");?></option>
											<option value="bottom" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'bullets_align_vert', 'bottom'), "bottom");?>><?php echo  JText::_("Bottom");?></option>
										</select>
										<div class="clear"></div>

										<span class="label" id="label_bullets_offset_hor" origtitle="<?php echo  JText::_("Offset from current horizontal position.");?>"><?php echo  JText::_("Horizontal Offset");?></span>
										<input type="text" class="text-sidebar withlabel" id="bullets_offset_hor" name="bullets_offset_hor" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'bullets_offset_hor', '0');?>">
										<span><?php echo  JText::_("px");?></span>
										<div class="clear"></div>


										<span class="label" id="label_bullets_offset_vert" origtitle="<?php echo  JText::_("Offset from current Vertical  position.");?>"><?php echo  JText::_("Vertical Offset");?></span>
										<input type="text" class="text-sidebar withlabel" id="bullets_offset_vert" name="bullets_offset_vert" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'bullets_offset_vert', '20');?>">
										<span><?php echo  JText::_("px");?></span>
										<div class="clear"></div>
										
										<span class="label" id="label_bullets_position" origtitle="<?php echo  JText::_("Position the Bullets to Slider or Layer Grid");?>"><?php echo  JText::_("Aligned by");?></span>
										<select id="bullets_position" name="bullets_position" class="withlabel">
											<option value="slider" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "bullets_position", "slider"), "slider");?>><?php echo  JText::_("Slider");?></option>
											<option value="grid" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "bullets_position", "slider"), "grid");?>><?php echo  JText::_("Layer Grid");?></option>
										</select>
										<div class="clear"></div>
										
									</div>
								</div><!-- END OF NAVIGATION BULLETS -->


								<!-- NAVIGATION THUMBNAILS -->
								<div id="navigation-thumbnails" style="display:none;">
									<span class="label" id="label_enable_thumbnails"  origtitle="<?php echo  JText::_("Enable / Disable Thumbnails");?>"><?php echo  JText::_("Enable Thumbnails");?> </span>
									<input type="checkbox" class="tp-moderncheckbox withlabel" id="enable_thumbnails" name="enable_thumbnails" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "enable_thumbnails", "off"), "on");?>>

									<div id="nav_thumbnails_subs">
										<span class="label" id="label_rtl_thumbnails"  origtitle="<?php echo  JText::_("Change Direction of thumbnail Functions for RTL Support");?>"><?php echo  JText::_("RTL Direction");?> </span>
										<input type="checkbox" class="tp-moderncheckbox withlabel" id="rtl_thumbnails" name="rtl_thumbnails" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "rtl_thumbnails", "off"), "on");?>>

										<h4><?php echo  JText::_("Wrapper Container");?></h4>

										<span class="label" id="label_thumbnails_padding"  origtitle="<?php echo  JText::_("The wrapper div padding of thumbnails");?>"><?php echo  JText::_("Wrapper Padding");?> </span>
										<input type="text" class="text-sidebar withlabel"  id="thumbnails_padding" name="thumbnails_padding" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'thumbnails_padding', '5');?>">
										<div class="clear"></div>

										<span class="label" id="label_span_thumbnails_wrapper"  origtitle="<?php echo  JText::_("Span wrapper to full width or full height based on the direction selected");?>"><?php echo  JText::_("Span Wrapper");?> </span>
										<input type="checkbox" class="tp-moderncheckbox withlabel" id="span_thumbnails_wrapper" name="span_thumbnails_wrapper" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "span_thumbnails_wrapper", "off"), "on");?>>


										<span class="label" id="label_thumbnails_wrapper_color"  origtitle="<?php echo  JText::_("Thumbnails wrapper background color. For transparent leave empty.");?>"><?php echo  JText::_("Wrapper color");?> </span>
										<input type="text"  class="my-color-field rs-layer-input-field tipsy_enabled_top withlabel" title="<?php echo  JText::_("Wrapper Color");?>"  id="thumbnails_wrapper_color" name="thumbnails_wrapper_color" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'thumbnails_wrapper_color', 'transparent');?>" />
										<div class="clear"></div>

										<span class="label" id="label_thumbnails_wrapper_opacity" origtitle="<?php echo  JText::_("Opacity of the Wrapper container. 0 - transparent, 50 - 50% opacity...");?>"><?php echo  JText::_("Wrapper Opacity");?></span>
										<input type="text" class="text-sidebar withlabel" id="thumbnails_wrapper_opacity" name="thumbnails_wrapper_opacity" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'thumbnails_wrapper_opacity', '100');?>">
										<div class="clear"></div>


										<h4><?php echo  JText::_("Thumbnails");?></h4>

										<span class="label triggernavstyle" id="label_thumbnails_style" origtitle="<?php echo  JText::_("Style of the thumbnails.");?>"><?php echo  JText::_("Thumbnails Style");?></span>
										<select id="thumbnails_style" name="thumbnails_style" class="triggernavstyle withlabel">
											<option value="" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'thumbnails_style', 'round'), ''); ?>><?php echo  JText::_('No Style'); ?></option>
											<?php
											if (!empty($arr_navigations)) {
												foreach ($arr_navigations as $cur_nav) {
													if (isset($cur_nav['markup']['thumbs'])) {
														?>
														<option value="<?php echo $cur_nav['handle'];?>" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'thumbnails_style', 'round'), $cur_nav['handle']);?>><?php echo $cur_nav['name'];?></option>
														<?php
													}
												}
											}
											?>
										</select>
										<div class="clear"></div>
										<span class="label triggernavstyle" id="label_navigation_thumbs_preset" origtitle="<?php echo  JText::_("Preset");?>"><?php echo  JText::_("Preset");?></span>
										<select id="navigation_thumbs_preset" name="navigation_thumbs_preset" class="withlabel triggernavstyle" data-startvalue="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'navigation_thumbs_preset', 'default'); ?>">
											<option class="never" value="default" selected="selected"><?php echo  JText::_('Default'); ?></option>
											<option class="never" value="custom"><?php echo  JText::_('Custom'); ?></option>
										</select>
										<div class="clear"></div>
										<div data-navtype="thumbs" class="toggle-custom-navigation-style-wrapper">
											<div class="toggle-custom-navigation-style"><?php echo  JText::_("Toggle Custom Navigation Styles");?></div>
											<div class="toggle-custom-navigation-styletarget navigation_thumbs_placeholder">
											</div>
										</div>
										<div class="clear"></div>
										
										<span id="label_thumb_amount" class="label"  origtitle="<?php echo  JText::_("The amount of max visible Thumbnails in the same time. ");?>"><?php echo  JText::_("Visible Thumbs Amount");?></span>
										<input type="text" class="text-sidebar withlabel" id="thumb_amount" name="thumb_amount" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "thumb_amount", "5");?>">
										<div class="clear"></div>

										<span class="label" id="label_thumbnails_space" origtitle="<?php echo  JText::_("Space between the thumbnails.");?>"><?php echo  JText::_("Space");?></span>
										<input type="text" class="text-sidebar withlabel" id="thumbnails_space" name="thumbnails_space" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'thumbnails_space', '5');?>">
										<span><?php echo  JText::_("px");?></span>
										<div class="clear"></div>

										<span class="label" id="label_thumbnail_direction" origtitle="<?php echo  JText::_("Direction of the Thumbnails. Vertical or Horizontal.");?>"><?php echo  JText::_("Direction");?></span>
										<select id="thumbnail_direction" name="thumbnail_direction" class=" withlabel">
											<option value="horizontal" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'thumbnail_direction', 'horizontal'), "horizontal");?>><?php echo  JText::_("Horizontal");?></option>
											<option value="vertical" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'thumbnail_direction', 'horizontal'), "vertical");?>><?php echo  JText::_("Vertical");?></option>
										</select>
										<div class="clear"></div>

										<h4><?php echo  JText::_("Thumbnail Container Size");?></h4>

										<span id="label_thumb_width" class="label"  origtitle="<?php echo  JText::_("The basic Width of one Thumbnail Container.");?>"><?php echo  JText::_("Container Width");?></span>
										<input type="text" class="text-sidebar withlabel" id="thumb_width" name="thumb_width" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "thumb_width", "100");?>">
										<span><?php echo  JText::_("px");?></span>
										<div class="clear"></div>

										<span id="label_thumb_height" class="label"  origtitle="<?php echo  JText::_("The basic Height of one Thumbnail.");?>"><?php echo  JText::_("Container Height");?></span>
										<input type="text" class="text-sidebar withlabel" id="thumb_height" name="thumb_height" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "thumb_height", "50");?>">
										<span><?php echo  JText::_("px");?></span>
										<div class="clear"></div>

										<span id="label_thumb_width_min" class="label"  origtitle="<?php echo  JText::_("The minimum width of the auto resized thumbs. Between Max and Min width the sizes are auto calculated).");?>"><?php echo  JText::_("Min Container Width");?></span>
										<input type="text" class="text-sidebar withlabel" id="thumb_width_min" name="thumb_width_min" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "thumb_width_min", "100");?>">
										<span><?php echo  JText::_("px");?></span>
										<div class="clear"></div>



										<h4><?php echo  JText::_("Visibility");?></h4>

										<span class="label" id="label_thumbs_always_on" origtitle="<?php echo  JText::_("Enable to make thumbnails always visible. Disable to hide thumbnails after the defined time.");?>"><?php echo  JText::_("Always Show ");?></span>
										<select id="thumbs_always_on" name="thumbs_always_on" class=" withlabel showhidewhat_truefalse" data-showhidetarget="hide_after_thumbs">
											<option value="false" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'thumbs_always_on', 'false'), "false");?>><?php echo  JText::_("Yes");?></option>
											<option value="true" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'thumbs_always_on', 'false'), "true");?>><?php echo  JText::_("No");?></option>
										</select>
										<div class="clear"></div>

										<div id="hide_after_thumbs">
											<span class="label" id="label_hide_thumbs" origtitle="<?php echo  JText::_("Time after that the thumbnails will be hidden(Default: 200 ms)");?>"><?php echo  JText::_("Hide After");?></span>
											<input type="text" class="text-sidebar withlabel" id="hide_thumbs" name="hide_thumbs" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'hide_thumbs', '200');?>">
											<span><?php echo  JText::_("ms");?></span>
											<div class="clear"></div>

											<span class="label" id="label_hide_thumbs_mobile" origtitle="<?php echo  JText::_("Time after that the thumbnails will be hidden on Mobile (Default: 1200 ms)");?>"><?php echo  JText::_("Hide After on Mobile");?></span>
											<input type="text" class="text-sidebar withlabel" id="hide_thumbs_mobile" name="hide_thumbs_mobile" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'hide_thumbs_mobile', '1200');?>">
											<span><?php echo  JText::_("ms");?></span>
											<div class="clear"></div>
										</div>

										<span class="label" id="label_hide_thumbs_on_mobile" origtitle="<?php echo  JText::_("Force Hide Navigation Thumbnails under width");?>"><?php echo  JText::_("Hide under Width");?> </span>
										<input type="checkbox" class="tp-moderncheckbox withlabel showhidewhat_truefalse" data-showhidetarget="hide_under_thumb" id="hide_thumbs_on_mobile" name="hide_thumbs_on_mobile" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "hide_thumbs_on_mobile", "off"), "on");?>>
										<div class="clear"></div>

										<div id="hide_under_thumb" class="withsublabels">
											<span id="label_thumbs_under_hidden" class="label" origtitle="<?php echo  JText::_("If browser size goes below this value, then Navigation thumbs are hidden.");?>"><?php echo  JText::_("Width");?> </span>
											<input type="text" class="text-sidebar withlabel" id="thumbs_under_hidden" name="thumbs_under_hidden" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'thumbs_under_hidden', '0');?>">
											<span><?php echo  JText::_("px");?></span>
											<div class="clear"></div>
										</div>

										<span class="label" id="label_hide_thumbs_over" origtitle="<?php echo  JText::_("Force Hide Navigation Thumbnails under width");?>"><?php echo  JText::_("Hide over Width");?> </span>
										<input type="checkbox" class="tp-moderncheckbox withlabel showhidewhat_truefalse" data-showhidetarget="hide_over_thumb" id="hide_thumbs_over" name="hide_thumbs_over" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "hide_thumbs_over", "off"), "on");?>>
										<div class="clear"></div>

										<div id="hide_over_thumb" class="withsublabels">
											<span id="label_thumbs_over_hidden" class="label" origtitle="<?php echo  JText::_("If browser size goes below this value, then Navigation thumbs are hidden.");?>"><?php echo  JText::_("Width");?> </span>
											<input type="text" class="text-sidebar withlabel" id="thumbs_over_hidden" name="thumbs_over_hidden" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'thumbs_over_hidden', '0');?>">
											<span><?php echo  JText::_("px");?></span>
											<div class="clear"></div>
										</div>

										<h4><?php echo  JText::_("Position");?></h4>
										<span class="label" id="label_thumbnails_inner_outer" origtitle="<?php echo  JText::_("Put the thumbnails inside or outside of the slider container. Outside added thumbnails will decrease the size of the slider.");?>"><?php echo  JText::_("Inner / outer");?></span>
										<select id="thumbnails_inner_outer" name="thumbnails_inner_outer" class=" withlabel">
											<option value="inner" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'thumbnails_inner_outer', 'inner'), "inner");?>><?php echo  JText::_("Inner Slider");?></option>
											<option value="outer-left" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'thumbnails_inner_outer', 'inner'), "outer-left");?>><?php echo  JText::_("Outer Left");?></option>
											<option value="outer-right" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'thumbnails_inner_outer', 'inner'), "outer-right");?>><?php echo  JText::_("Outer Right");?></option>
											<option value="outer-top" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'thumbnails_inner_outer', 'inner'), "outer-top");?>><?php echo  JText::_("Outer Top");?></option>
											<option value="outer-bottom" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'thumbnails_inner_outer', 'inner'), "outer-bottom");?>><?php echo  JText::_("Outer Bottom");?></option>

										</select>
										<div class="clear"></div>

										<span class="label" id="label_thumbnails_align_hor" origtitle="<?php echo  JText::_("Horizontal position of thumbnails");?>"><?php echo  JText::_("Horizontal Align");?></span>
										<select id="thumbnails_align_hor" name="thumbnails_align_hor" class=" withlabel">
											<option value="left" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'thumbnails_align_hor', 'center'), "left");?>><?php echo  JText::_("Left");?></option>
											<option value="center" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'thumbnails_align_hor', 'center'), "center");?>><?php echo  JText::_("Center");?></option>
											<option value="right" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'thumbnails_align_hor', 'center'), "right");?>><?php echo  JText::_("Right");?></option>
										</select>
										<div class="clear"></div>

										<span class="label" id="label_thumbnails_align_vert" origtitle="<?php echo  JText::_("Vertical position of thumbnails");?>"><?php echo  JText::_("Vertical Align");?></span>
										<select id="thumbnails_align_vert" name="thumbnails_align_vert" class="withlabel">
											<option value="top" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'thumbnails_align_vert', 'bottom'), "top");?>><?php echo  JText::_("Top");?></option>
											<option value="center" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'thumbnails_align_vert', 'bottom'), "center");?>><?php echo  JText::_("Center");?></option>
											<option value="bottom" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'thumbnails_align_vert', 'bottom'), "bottom");?>><?php echo  JText::_("Bottom");?></option>
										</select>
										<div class="clear"></div>

										<span class="label" id="label_thumbnails_offset_hor" origtitle="<?php echo  JText::_("Offset from current Horizontal position.");?>"><?php echo  JText::_("Horizontal Offset");?></span>
										<input type="text" class="text-sidebar withlabel" id="thumbnails_offset_hor" name="thumbnails_offset_hor" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'thumbnails_offset_hor', '0');?>">
										<span><?php echo  JText::_("px");?></span>
										<div class="clear"></div>


										<span class="label" id="label_thumbnails_offset_vert" origtitle="<?php echo  JText::_("Offset from current Vertical position.");?>"><?php echo  JText::_("Vertical Offset");?></span>
										<input type="text" class="text-sidebar withlabel" id="thumbnails_offset_vert" name="thumbnails_offset_vert" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'thumbnails_offset_vert', '20');?>">
										<span><?php echo  JText::_("px");?></span>
										<div class="clear"></div>

										<span class="label" id="label_thumbnails_position" origtitle="<?php echo  JText::_("Position the Thumbnails to Slider or Layer Grid");?>"><?php echo  JText::_("Aligned by");?></span>
										<select id="thumbnails_position" name="thumbnails_position" class="withlabel">
											<option value="slider" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "thumbnails_position", "slider"), "slider");?>><?php echo  JText::_("Slider");?></option>
											<option value="grid" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "thumbnails_position", "slider"), "grid");?>><?php echo  JText::_("Layer Grid");?></option>
										</select>
										<div class="clear"></div>
									</div>
								</div>
								<!-- END OF NAVIGATION THUMBNAILS -->

								<!-- NAVIGATION TABS-->
								<div id="navigation-tabs" style="display:none;">

									<span class="label" id="label_enable_tabs"  origtitle="<?php echo  JText::_("Enable / Disable navigation tabs.");?>"><?php echo  JText::_("Enable Tabs");?> </span>
									<input type="checkbox" class="tp-moderncheckbox withlabel" id="enable_tabs" name="enable_tabs" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "enable_tabs", "off"), "on");?>>

									<div id="nav_tabs_subs">

										<span class="label" id="label_rtl_tabs"  origtitle="<?php echo  JText::_("Change Direction of tab Functions for RTL Support");?>"><?php echo  JText::_("RTL Direction");?> </span>
										<input type="checkbox" class="tp-moderncheckbox withlabel" id="rtl_tabs" name="rtl_tabs" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "rtl_tabs", "off"), "on");?>>

										<h4><?php echo  JText::_("Wrapper Container");?></h4>

										<span class="label" id="label_tabs_padding"  origtitle="<?php echo  JText::_("The wrapper div padding of tabs.");?>"><?php echo  JText::_("Wrapper Padding");?> </span>
										<input type="text" class="text-sidebar withlabel"  id="tabs_padding" name="tabs_padding" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'tabs_padding', '5');?>">
										<div class="clear"></div>

										<span class="label" id="label_span_tabs_wrapper"  origtitle="<?php echo  JText::_("Span wrapper to full width or full height based on the direction selected.");?>"><?php echo  JText::_("Span Wrapper");?> </span>
										<input type="checkbox" class="tp-moderncheckbox withlabel" id="span_tabs_wrapper" name="span_tabs_wrapper" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "span_tabs_wrapper", "off"), "on");?>>


										<span class="label" id="label_tabs_wrapper_color" origtitle="<?php echo  JText::_("Tabs wrapper background color. For transparent leave empty.");?>"><?php echo  JText::_("Wrapper Color");?> </span>
										<input type="text"  class="my-color-field rs-layer-input-field tipsy_enabled_top withlabel" title="<?php echo  JText::_("Wrapper Color");?>"  id="tabs_wrapper_color" name="tabs_wrapper_color" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'tabs_wrapper_color', 'transparent');?>" />
										<div class="clear"></div>

										<span class="label" id="label_tabs_wrapper_opacity" origtitle="<?php echo  JText::_("Opacity of the Wrapper container. 0 - transparent, 50 - 50% opacity...");?>"><?php echo  JText::_("Wrapper Opacity");?></span>
										<input type="text" class="text-sidebar withlabel" id="tabs_wrapper_opacity" name="tabs_wrapper_opacity" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'tabs_wrapper_opacity', '5');?>">
										<div class="clear"></div>

										<h4><?php echo  JText::_("Tabs");?></h4>

										<span class="triggernavstyle label" id="label_tabs_style" origtitle="<?php echo  JText::_("Style of the tabs.");?>"><?php echo  JText::_("Tabs Style");?></span>
										<select id="tabs_style" name="tabs_style" class="triggernavstyle withlabel">
											<option value="" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'tabs_style', 'round'), ''); ?>><?php echo  JText::_('No Style'); ?></option>
											<?php
											if (!empty($arr_navigations)) {
												foreach ($arr_navigations as $cur_nav) {
													if (isset($cur_nav['markup']['tabs'])) {
														?>
														<option value="<?php echo $cur_nav['handle'];?>" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'tabs_style', 'round'), $cur_nav['handle']);?>><?php echo $cur_nav['name'];?></option>
														<?php
													}
												}
											}
											?>
										</select>
										<script>
											jQuery(document).ready(function() {


												function checkMetis(){
													var v = jQuery('#tabs_style').val().toLowerCase();
													if (v.indexOf('metis')>=0) {
														jQuery('#tabs_padding').val(0).attr('disabled','disabled');														
														jQuery('#tabs_wrapper_opacity').val(0).attr('disabled','disabled');
														jQuery('#tabs_width_min').val(0).attr('disabled','disabled');
														jQuery('#tabs_direction').val('vertical').attr('disabled','disabled');
														jQuery('#tabs_align_hor').val('left').attr('disabled','disabled');
														jQuery('#span_tabs_wrapper').attr('checked','checked');
														ZtSliderSettings.onoffStatus(jQuery('#span_tabs_wrapper'));
													} else {
														jQuery('#tabs_padding').removeAttr('disabled');
														jQuery('#tabs_wrapper_opacity').removeAttr('disabled');
														jQuery('#tabs_direction').removeAttr('disabled');
														jQuery('#tabs_width_min').removeAttr('disabled');
														jQuery('#tabs_align_hor').removeAttr('disabled');														
													}

												}
												checkMetis();
												jQuery('#tabs_style').change(checkMetis);
											});
										</script>
										<div class="clear"></div>
										<span class="label triggernavstyle" id="label_navigation_tabs_preset" origtitle="<?php echo  JText::_("Preset");?>"><?php echo  JText::_("Preset");?></span>
										<select id="navigation_tabs_preset" name="navigation_tabs_preset" class="withlabel triggernavstyle" data-startvalue="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'navigation_tabs_preset', 'default'); ?>">
											<option class="never" value="default" selected="selected"><?php echo  JText::_('Default'); ?></option>
											<option class="never" value="custom"><?php echo  JText::_('Custom'); ?></option>
										</select>
										<div class="clear"></div>
										<div data-navtype="tabs" class="toggle-custom-navigation-style-wrapper">
											<div class="toggle-custom-navigation-style"><?php echo  JText::_("Toggle Custom Navigation Styles");?></div>
											<div class="toggle-custom-navigation-styletarget navigation_tabs_placeholder">
											</div>
										</div>
										<div class="clear"></div>
										
										<span id="label_tabs_amount" class="label"  origtitle="<?php echo  JText::_("The amount of max visible tabs in same time.");?>"><?php echo  JText::_("Visible Tabs Amount");?></span>
										<input type="text" class="text-sidebar withlabel" id="tabs_amount" name="tabs_amount" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "tabs_amount", "5");?>">
										<div class="clear"></div>

										<span class="label" id="label_tabs_space" origtitle="<?php echo  JText::_("Space between the tabs.");?>"><?php echo  JText::_("Space");?></span>
										<input type="text" class="text-sidebar withlabel" id="tabs_space" name="tabs_space" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'tabs_space', '5');?>">
										<span><?php echo  JText::_("px");?></span>
										<div class="clear"></div>

										<span class="label" id="label_tabs_direction" origtitle="<?php echo  JText::_("Direction of the Tabs. Vertical or Horizontal.");?>"><?php echo  JText::_("Direction");?></span>
										<select id="tabs_direction" name="tabs_direction" class=" withlabel">
											<option value="horizontal" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'tabs_direction', 'horizontal'), "horizontal");?>><?php echo  JText::_("Horizontal");?></option>
											<option value="vertical" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'tabs_direction', 'horizontal'), "vertical");?>><?php echo  JText::_("Vertical");?></option>
										</select>
										<div class="clear"></div>

										<h4><?php echo  JText::_("Tab Sizes");?></h4>

										<span id="label_tabs_width" class="label"  origtitle="<?php echo  JText::_("The basic width of one tab.");?>"><?php echo  JText::_("Tabs Width");?></span>
										<input type="text" class="text-sidebar withlabel" id="tabs_width" name="tabs_width" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "tabs_width", "100");?>">
										<span><?php echo  JText::_("px");?></span>
										<div class="clear"></div>

										<span id="label_tabs_height" class="label"  origtitle="<?php echo  JText::_("the basic height of one tab.");?>"><?php echo  JText::_("Tabs Height");?></span>
										<input type="text" class="text-sidebar withlabel" id="tabs_height" name="tabs_height" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "tabs_height", "50");?>">
										<span><?php echo  JText::_("px");?></span>
										<div class="clear"></div>

										<span id="label_tabs_width_min" class="label"  origtitle="<?php echo  JText::_("The minimum width of the auto resized Tabs. Between Max and Min width the sizes are auto calculated).");?>"><?php echo  JText::_("Min. Tab Width");?></span>
										<input type="text" class="text-sidebar withlabel" id="tabs_width_min" name="tabs_width_min" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "tabs_width_min", "100");?>">
										<span><?php echo  JText::_("px");?></span>
										<div class="clear"></div>

										<h4><?php echo  JText::_("Visibility");?></h4>

										<span class="label" id="label_tabs_always_on" origtitle="<?php echo  JText::_("Enable to make tabs always visible. Disable to hide tabs after the defined time.");?>"><?php echo  JText::_("Always Show ");?></span>
										<select id="tabs_always_on" name="tabs_always_on" class=" withlabel showhidewhat_truefalse" data-showhidetarget="hide_after_tabs">
											<option value="false" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'tabs_always_on', 'false'), "false");?>><?php echo  JText::_("Yes");?></option>
											<option value="true" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'tabs_always_on', 'false'), "true");?>><?php echo  JText::_("No");?></option>
										</select>
										<div class="clear"></div>

										<div id="hide_after_tabs">
											<span class="label" id="label_hide_tabs" origtitle="<?php echo  JText::_("Time after that the tabs will be hidden(Default: 200 ms)");?>"><?php echo  JText::_("Hide  After");?></span>
											<input type="text" class="text-sidebar withlabel" id="hide_tabs" name="hide_tabs" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'hide_tabs', '200');?>">
											<span><?php echo  JText::_("ms");?></span>
											<div class="clear"></div>
											<span class="label" id="label_hide_tabs_mobile" origtitle="<?php echo  JText::_("Time after that the tabs will be hidden on Mobile (Default: 1200 ms)");?>"><?php echo  JText::_("Hide  After on Mobile");?></span>
											<input type="text" class="text-sidebar withlabel" id="hide_tabs_mobile" name="hide_tabs_mobile" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'hide_tabs_mobile', '1200');?>">
											<span><?php echo  JText::_("ms");?></span>
											<div class="clear"></div>
										</div>

										<span class="label" id="label_hide_tabs_on_mobile" origtitle="<?php echo  JText::_("Force Hide Navigation tabs under width");?>"><?php echo  JText::_("Hide under Width");?> </span>
										<input type="checkbox" class="tp-moderncheckbox withlabel showhidewhat_truefalse" data-showhidetarget="hide_under_tab" id="hide_tabs_on_mobile" name="hide_tabs_on_mobile" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "hide_tabs_on_mobile", "off"), "on");?>>
										<div class="clear"></div>

										<div id="hide_under_tab" class="withsublabels">
											<span id="label_tabs_under_hidden" class="label" origtitle="<?php echo  JText::_("If browser size goes below this value, then Navigation tabs are hidden.");?>"><?php echo  JText::_("Width");?> </span>
											<input type="text" class="text-sidebar withlabel" id="tabs_under_hidden" name="tabs_under_hidden" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'tabs_under_hidden', '0');?>">
											<span><?php echo  JText::_("px");?></span>
											<div class="clear"></div>
										</div>

										<span class="label" id="label_hide_tabs_over" origtitle="<?php echo  JText::_("Force Hide Navigation tabs under width");?>"><?php echo  JText::_("Hide over Width");?> </span>
										<input type="checkbox" class="tp-moderncheckbox withlabel showhidewhat_truefalse" data-showhidetarget="hide_over_tab" id="hide_tabs_over" name="hide_tabs_over" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "hide_tabs_over", "off"), "on");?>>
										<div class="clear"></div>

										<div id="hide_over_tab" class="withsublabels">
											<span id="label_tabs_over_hidden" class="label" origtitle="<?php echo  JText::_("If browser size goes below this value, then Navigation tabs are hidden.");?>"><?php echo  JText::_("Width");?> </span>
											<input type="text" class="text-sidebar withlabel" id="tabs_over_hidden" name="tabs_over_hidden" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'tabs_over_hidden', '0');?>">
											<span><?php echo  JText::_("px");?></span>
											<div class="clear"></div>
										</div>

										<h4><?php echo  JText::_("Position");?></h4>

										<span class="label" id="label_tabs_inner_outer" origtitle="<?php echo  JText::_("Put the tabs inside or outside of the slider container. Outside added tabs will decrease the size of the slider.");?>"><?php echo  JText::_("Inner / outer");?></span>
										<select id="tabs_inner_outer" name="tabs_inner_outer" class=" withlabel">
											<option value="inner" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'tabs_inner_outer', 'inner'), "inner");?>><?php echo  JText::_("Inner Slider");?></option>
											<option value="outer-left" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'tabs_inner_outer', 'inner'), "outer-left");?>><?php echo  JText::_("Outer Left");?></option>
											<option value="outer-right" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'tabs_inner_outer', 'inner'), "outer-right");?>><?php echo  JText::_("Outer Right");?></option>
											<option value="outer-top" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'tabs_inner_outer', 'inner'), "outer-top");?>><?php echo  JText::_("Outer Top");?></option>
											<option value="outer-bottom" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'tabs_inner_outer', 'inner'), "outer-bottom");?>><?php echo  JText::_("Outer Bottom");?></option>

										</select>
										<div class="clear"></div>


										<span class="label" id="label_tabs_align_hor" origtitle="<?php echo  JText::_("Horizontal position of tabs.");?>"><?php echo  JText::_("Horizontal Align");?></span>
										<select id="tabs_align_hor" name="tabs_align_hor" class=" withlabel">
											<option value="left" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'tabs_align_hor', 'center'), "left");?>><?php echo  JText::_("Left");?></option>
											<option value="center" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'tabs_align_hor', 'center'), "center");?>><?php echo  JText::_("Center");?></option>
											<option value="right" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'tabs_align_hor', 'center'), "right");?>><?php echo  JText::_("Right");?></option>
										</select>
										<div class="clear"></div>

										<span class="label" id="label_tabs_align_vert" origtitle="<?php echo  JText::_("Vertical position of tabs.");?>"><?php echo  JText::_("Vertical Align");?></span>
										<select id="tabs_align_vert" name="tabs_align_vert" class="withlabel">
											<option value="top" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'tabs_align_vert', 'bottom'), "top");?>><?php echo  JText::_("Top");?></option>
											<option value="center" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'tabs_align_vert', 'bottom'), "center");?>><?php echo  JText::_("Center");?></option>
											<option value="bottom" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'tabs_align_vert', 'bottom'), "bottom");?>><?php echo  JText::_("Bottom");?></option>
										</select>
										<div class="clear"></div>

										<span class="label" id="label_tabs_offset_hor" origtitle="<?php echo  JText::_("Offset from current horizontal position of tabs.");?>"><?php echo  JText::_("Horizontal Offset");?></span>
										<input type="text" class="text-sidebar withlabel" id="tabs_offset_hor" name="tabs_offset_hor" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'tabs_offset_hor', '0');?>">
										<span><?php echo  JText::_("px");?></span>
										<div class="clear"></div>


										<span class="label" id="label_tabs_offset_vert" origtitle="<?php echo  JText::_("Offset from current vertical position of tabs.");?>"><?php echo  JText::_("Vertical Offset");?></span>
										<input type="text" class="text-sidebar withlabel" id="tabs_offset_vert" name="tabs_offset_vert" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'tabs_offset_vert', '20');?>">
										<span><?php echo  JText::_("px");?></span>
										<div class="clear"></div>
										
										<div id="rs-tabs_position_wrapper">
											<span class="label" id="label_tabs_position" origtitle="<?php echo  JText::_("Position the Tabs to Slider or Layer Grid");?>"><?php echo  JText::_("Aligned by");?></span>
											<select id="tabs_position" name="tabs_position" class="withlabel">
												<option value="slider" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "tabs_position", "slider"), "slider");?>><?php echo  JText::_("Slider");?></option>
												<option value="grid" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "tabs_position", "slider"), "grid");?>><?php echo  JText::_("Layer Grid");?></option>
											</select>
											<div class="clear"></div>
										</div>
									</div>
								</div>
								<!-- END OF NAVIGATION TABS-->

								<!-- TOUCH NAVIGATION -->
								<div id="navigation-touch" style="display:none;">

									<span class="label" id="label_touchenabled" origtitle="<?php echo  JText::_("Enable Swipe Function on touch devices");?>"><?php echo  JText::_("Touch Enabled");?> </span>
									<input type="checkbox" class="tp-moderncheckbox withlabel" id="touchenabled" name="touchenabled" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "touchenabled", "off"), "on");?>>
									<div class="clear"></div>

									<span class="label" id="label_drag_block_vertical" origtitle="<?php echo  JText::_("Scroll below slider on vertical swipe");?>"><?php echo  JText::_("Drag Block Vertical");?> </span>
									<input type="checkbox" class="tp-moderncheckbox withlabel" id="drag_block_vertical" name="drag_block_vertical" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "drag_block_vertical", "off"), "on");?>>
									<div class="clear"></div>

									<span class="label" id="label_swipe_velocity" origtitle="<?php echo  JText::_("Defines the sensibility of gestures. Smaller values mean a higher sensibility");?>"><?php echo  JText::_("Swipe Treshhold (0 - 200)");?> </span>
									<input type="text" class="text-sidebar withlabel" id="swipe_velocity" name="swipe_velocity" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "swipe_velocity", "75");?>">
									<div class="clear"></div>

									<span class="label" id="label_swipe_min_touches" origtitle="<?php echo  JText::_("Defines how many fingers are needed minimum for swiping");?>"><?php echo  JText::_("Swipe Min Finger");?> </span>
									<input type="text" class="text-sidebar withlabel" id="swipe_min_touches" name="swipe_min_touches" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "swipe_min_touches", "1");?>">
									<div class="clear"></div>

									<span class="label" id="label_swipe_direction" origtitle="<?php echo  JText::_("Swipe Direction to swap slides?");?>"><?php echo  JText::_("Swipe Direction");?></span>
									<select id="swipe_direction" name="swipe_direction" class="withlabel">
										<option value="horizontal" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'swipe_direction', 'horizontal'), "horizontal");?>><?php echo  JText::_("Horizontal");?></option>
										<option value="vertical" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'swipe_direction', 'horizontal'), "vertical");?>><?php echo  JText::_("Vertical");?></option>											
									</select>
									<div class="clear"></div>

								</div> <!-- END TOUCH NAVIGATION -->

								<!-- KEYBOARD NAVIGATION -->
								<div id="navigation-keyboard" style="display:none;">
									<span class="label" id="label_keyboard_navigation" origtitle="<?php echo  JText::_("Allow/disallow to navigate the slider with keyboard.");?>"><?php echo  JText::_("Keyboard Navigation");?></span>
									<input type="checkbox" class="tp-moderncheckbox withlabel" id="keyboard_navigation" name="keyboard_navigation" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, 'keyboard_navigation', 'off'), "on");?>>
									<div class="clear"></div>
									
									<span class="label" id="label_keyboard_direction" origtitle="<?php echo  JText::_("Keyboard Direction to swap slides (horizontal - left/right arrow, vertical - up/down arrow)?");?>"><?php echo  JText::_("Key Direction");?></span>
									<select id="keyboard_direction" name="keyboard_direction" class="withlabel">
										<option value="horizontal" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'keyboard_direction', 'horizontal'), "horizontal");?>><?php echo  JText::_("Horizontal");?></option>
										<option value="vertical" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'keyboard_direction', 'horizontal'), "vertical");?>><?php echo  JText::_("Vertical");?></option>
									</select>
									<div class="clear"></div>

									<span class="label" id="label_mousescroll_navigation" origtitle="<?php echo  JText::_("Allow/disallow to navigate the slider with Mouse Scroll.");?>"><?php echo  JText::_("Mouse Scroll Navigation");?></span>
									<select id="mousescroll_navigation" name="mousescroll_navigation" class="withlabel">
										<option value="on" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'mousescroll_navigation', 'off'), "on");?>><?php echo  JText::_("On");?></option>
										<option value="off" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'mousescroll_navigation', 'off'), "off");?>><?php echo  JText::_("Off");?></option>
										<option value="carousel" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'mousescroll_navigation', 'off'), "carousel");?>><?php echo  JText::_("Carousel");?></option>
									</select>

									<span class="label" id="label_mousescroll_navigation_reverse" origtitle="<?php echo  JText::_("Reverse the functionality of the Mouse Scroll Navigation");?>"><?php echo  JText::_("Reverse Mouse Scroll");?></span>
									<select id="mousescroll_navigation_reverse" name="mousescroll_navigation_reverse" class="withlabel">
										<option value="reverse" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'mousescroll_navigation_reverse', 'default'), "reverse");?>><?php echo  JText::_("Reverse");?></option>
										<option value="default" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'mousescroll_navigation_reverse', 'default'), "default");?>><?php echo  JText::_("Default");?></option>
										
									</select>

									<div class="clear"></div>
									
								</div><!-- END KEYBOARD NAVIGATION -->
								
								<!-- PREVIEW IMAGE SIZES -->									
								<div id="navigation-miniimagedimensions" style="border-top:1px solid #f1f1f1; margin:20px -20px 0px; padding:0px 20px">
									<h4><?php echo  JText::_("Preview Image Size");?></h4>

									<span id="label_previewimage_width" class="label"  origtitle="<?php echo  JText::_("The basic Width of one Preview Image.");?>"><?php echo  JText::_("Preview Image Width");?></span>
									<input type="text" class="text-sidebar withlabel" id="previewimage_width" name="previewimage_width" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "previewimage_width", ZTSliderFunctions::getVal($this->arrFieldsParams, "thumb_width", 100));?>">
									<span><?php echo  JText::_("px");?></span>
									<div class="clear"></div>

									<span id="label_previewimage_height" class="label"  origtitle="<?php echo  JText::_("The basic Height of one Preview Image.");?>"><?php echo  JText::_("Preview Image Height");?></span>
									<input type="text" class="text-sidebar withlabel" id="previewimage_height" name="previewimage_height" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "previewimage_height", ZTSliderFunctions::getVal($this->arrFieldsParams, "thumb_height", 50));?>">
									<span><?php echo  JText::_("px");?></span>
									<div class="clear"></div>
									
								</div>
							</div>
							<script>
								jQuery('#navigation-settings-wrapper input, #navigation-settings-wrapper select').on("change",drawToolBarPreview);
								
								// NOT NICE, BUT SURELY UNBREAKABLE LATER :) 
								jQuery('#enable_arrows').on("change",function() {
										var sbi = jQuery(this);
										if (sbi.attr("checked") === "checked") {
											jQuery('#nav_arrows_subs').show();
										} else {
											jQuery('#nav_arrows_subs').hide();
										}
										drawToolBarPreview();
								});


								jQuery('#enable_bullets').on("change",function() {
										var sbi = jQuery(this);
										if (sbi.attr("checked") === "checked") {
											jQuery('#nav_bullets_subs').show();
										} else {
											jQuery('#nav_bullets_subs').hide();
										}
										drawToolBarPreview();
								});


								jQuery('#enable_thumbnails').on("change",function() {
										var sbi = jQuery(this);
										if (sbi.attr("checked") === "checked") {
											jQuery('#nav_thumbnails_subs').show();
										} else {
											jQuery('#nav_thumbnails_subs').hide();
										}
										drawToolBarPreview();
								});


								jQuery('#enable_tabs').on("change",function() {
										var sbi = jQuery(this);
										if (sbi.attr("checked") === "checked") {
											jQuery('#nav_tabs_subs').show();
										} else {
											jQuery('#nav_tabs_subs').hide();
										}
										drawToolBarPreview();
								});

								jQuery('.showhidewhat_truefalse').on("change",function() {
										var sbi = jQuery(this);
										if (sbi.val() === true || sbi.val() === "true" || sbi.attr("checked")) {
											jQuery("#"+sbi.data("showhidetarget")).show();
										} else {
											jQuery("#"+sbi.data("showhidetarget")).hide();
										}									
									});

								

								jQuery('#thumbnails_inner_outer').on('change',function() {
										var sbi = jQuery(this),
											v = sbi.find('option:selected').val();
										if (v==="outer-top" || v==="outer-bottom") {
											if (v==="outer-top") jQuery('#thumbnails_align_vert').val("top");
											if (v==="outer-bottom") jQuery('#thumbnails_align_vert').val("bottom");
											jQuery('#thumbnails_align_vert').attr("disabled","disabled");
											jQuery('#thumbnail_direction').val("horizontal");
											jQuery('#thumbnail_direction').change();
										}
										else
											jQuery('#thumbnails_align_vert').removeAttr("disabled");


										if (v==="outer-left" || v==="outer-right") {
											if (v==="outer-left") jQuery('#thumbnails_align_hor').val("left");
											if (v==="outer-right") jQuery('#thumbnails_align_hor').val("right");
											jQuery('#thumbnails_align_hor').attr("disabled","disabled");
											jQuery('#thumbnail_direction').val("vertical");
											jQuery('#thumbnail_direction').change();
										}
										else
											jQuery('#thumbnails_align_hor').removeAttr("disabled");


										if (v==="outer-left" || v==="outer-right" || v==="outer-top" || v==="outer-bottom")
											jQuery('#thumbnail_direction').attr("disabled","disabled");
										else
											jQuery('#thumbnail_direction').removeAttr("disabled");

								});

								jQuery('#tabs_inner_outer').on('change',function() {
										var sbi = jQuery(this),
											v = sbi.find('option:selected').val();
										if (v==="outer-top" || v==="outer-bottom") {
											if (v==="outer-top") jQuery('#tabs_align_vert').val("top");
											if (v==="outer-bottom") jQuery('#tabs_align_vert').val("bottom");
											jQuery('#tabs_align_vert').attr("disabled","disabled");
											jQuery('#tabs_direction').val("horizontal");
											jQuery('#tabs_direction').change();
											jQuery('#tabs_direction').attr("disabled","disabled");
										}
										else
											jQuery('#tabs_align_vert').removeAttr("disabled");

										if (v==="outer-left" || v==="outer-right") {
											if (v==="outer-left") jQuery('#tabs_align_hor').val("left");
											if (v==="outer-right") jQuery('#tabs_align_hor').val("right");
											jQuery('#tabs_align_hor').attr("disabled","disabled");
											jQuery('#tabs_direction').val("vertical");
											jQuery('#tabs_direction').change();
											jQuery('#tabs_direction').attr("disabled","disabled");
										}
										else
											jQuery('#tabs_align_hor').removeAttr("disabled");

										if (v==="outer-left" || v==="outer-right" || v==="outer-top" || v==="outer-bottom")
											jQuery('#tabs_direction').removeAttr("disabled","disabled");
								});


								
								jQuery('.showhidewhat_truefalse').change();
								jQuery('#thumbnails_inner_outer').change();
								jQuery('#tabs_inner_outer').change();
								jQuery('#enable_arrows').change();
								jQuery('#enable_thumbnails').change();
								jQuery('#enable_bullets').change();
								jQuery('#enable_tabs').change();

							</script>
						</div><!-- END OF NAVIGATION SETTINGS -->


						<!-- CAROUSEL SETTINGS -->
						<div class="setting_box dontshowonhero dontshowonstandard">
							<h3 class="box_closed"><i class="rs-rp-accordion-icon eg-icon-ccw"></i>
								<div class="setting_box-arrow"></div>
								<span><?php echo  JText::_("Carousel Settings");?></span>
							</h3>

							<div class="inside" style="display: none;">

								<ul class="main-options-small-tabs" style="display:inline-block; ">
									<li data-content="#carousel-basics" class="selected"><?php echo  JText::_('Basics');?></li>
									<li data-content="#carousel-trans"><?php echo  JText::_('Transformations');?></li>
									<li data-content="#carousel-aligns"><?php echo  JText::_('Aligns');?></li>
								</ul>
								<div id="carousel-basics">
									<!-- Infinity -->
									<span class="label" id="label_carousel_infinity" origtitle="<?php echo  JText::_("Infinity Carousel Scroll. No Endpoints exists at first and last slide if valuse is set to ON.");?>"><?php echo  JText::_("Infinity Scroll");?> </span>
									<input type="checkbox" class="tp-moderncheckbox withlabel" id="carousel_infinity" name="carousel_infinity" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_infinity', 'off'), 'on');?>>
									<div class="clearfix"></div>

									<!-- Carousel Spaces -->
									<span class="label" id="label_carousel_space" origtitle="<?php echo  JText::_("The horizontal gap/space between the slides");?>"><?php echo  JText::_("Space between slides");?> </span>
									<input type="text" class="text-sidebar withlabel"   id="carousel_space" name="carousel_space" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_space', '0');?>">
									<span><?php echo  JText::_("px");?></span>
									<div class="clear"></div>

									<!-- Border Radius -->
									<span class="label" id="label_carousel_borderr" origtitle="<?php echo  JText::_("The border radius of slides");?>"><?php echo  JText::_("Border Radius");?> </span>
									<input style="width:60px;min-width:60px;max-width:60px;" type="text" class="text-sidebar withlabel"   id="carousel_borderr" name="carousel_borderr" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_borderr', '0');?>">
									
									<!-- Border Radius Unit -->
									<select style="width:45px;min-width:45px;max-width:45px;" id="carousel_borderr_unit" name="carousel_borderr_unit">
										<option value="px" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_borderr_unit', 'px'), "px");?>><?php echo  JText::_("px");?></option>
										<option value="%" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_borderr_unit', 'px'), "%");?>><?php echo  JText::_("%");?></option>
									</select>
									<div class="clear"></div>
									
									<!-- Padding -->
									<span class="label" id="label_carousel_padding_top" origtitle="<?php echo  JText::_("The padding top of slides");?>"><?php echo  JText::_("Padding Top");?> </span>
									<input type="text" class="text-sidebar withlabel"   id="carousel_padding_top" name="carousel_padding_top" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_padding_top', '0');?>"> <?php echo  JText::_('px'); ?>
									
									<span class="label" id="label_carousel_padding_bottom" origtitle="<?php echo  JText::_("The padding bottom of slides");?>"><?php echo  JText::_("Padding Bottom");?> </span>
									<input type="text" class="text-sidebar withlabel"   id="carousel_padding_bottom" name="carousel_padding_bottom" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_padding_bottom', '0');?>"> <?php echo  JText::_('px'); ?>

									<!-- Carousel Max Visible Items -->
									<span class="label" id="label_carousel_maxitems" origtitle="<?php echo  JText::_("The maximum visible items in same time.");?>"><?php echo  JText::_("Max. Visible Items");?> </span>
									<select id="carousel_maxitems" class="withlabel"  name="carousel_maxitems">
										<option value="1" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_maxitems', '3'), "1");?>><?php echo  JText::_("1");?></option>
										<option value="3" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_maxitems', '3'), "3");?>><?php echo  JText::_("3");?></option>
										<option value="5" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_maxitems', '3'), "5");?>><?php echo  JText::_("5");?></option>
										<option value="7" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_maxitems', '3'), "7");?>><?php echo  JText::_("7");?></option>
										<option value="9" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_maxitems', '3'), "9");?>><?php echo  JText::_("9");?></option>
										<option value="11" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_maxitems', '3'), "11");?>><?php echo  JText::_("11");?></option>
										<option value="13" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_maxitems', '3'), "13");?>><?php echo  JText::_("13");?></option>
										<option value="15" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_maxitems', '3'), "15");?>><?php echo  JText::_("15");?></option>
										<option value="17" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_maxitems', '3'), "17");?>><?php echo  JText::_("17");?></option>
										<option value="19" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_maxitems', '3'), "19");?>><?php echo  JText::_("19");?></option>
									</select>

									<!-- Carousel Stretch Out -->
									<span class="label" id="label_carousel_stretch" origtitle="<?php echo  JText::_("Stretch carousel element width to the wrapping container width.  Using this you can see only 1 item in same time.");?>"><?php echo  JText::_("Stretch Element");?> </span>
									<input type="checkbox" class="tp-moderncheckbox withlabel" id="carousel_stretch" name="carousel_stretch" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_stretch', 'off'), 'on');?>>
									<div class="clearfix"></div>
									
									<div class="clear"></div>
								</div>


								<div id="carousel-trans" style="display:none">
									<!-- Carousel Fade Out -->
									<span class="label" id="label_carousel_fadeout" origtitle="<?php echo  JText::_("All elements out of focus will get some Opacity value based on the Distance to the current focused element, or only the coming/leaving elements.");?>"><?php echo  JText::_("Fade All Elements");?> </span>
									<input type="checkbox" class="tp-moderncheckbox withlabel" id="carousel_fadeout" name="carousel_fadeout" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_fadeout', 'on'), "on");?>>
									<div class="clearfix"></div>

									<div id="carousel-fade-row" class="withsublabels">
										<!-- Carousel Rotation Varying Out -->
										<span class="label" id="label_carousel_varyfade" origtitle="<?php echo  JText::_("Fade is varying based on the distance to the focused element.");?>"><?php echo  JText::_("Varying Fade");?> </span>
										<input type="checkbox" class="tp-moderncheckbox withlabel" id="carousel_varyfade" name="carousel_varyfade" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_varyfade', 'off'), "on");?>>
										<div class="clearfix"></div>
									</div>


									<!-- Carousel Rotation  -->
									<span class="label label-with-subsection" id="label_carousel_rotation" origtitle="<?php echo  JText::_("Rotation enabled/disabled for not focused elements.");?>"><?php echo  JText::_("Rotation");?> </span>
									<input type="checkbox" class="tp-moderncheckbox withlabel" id="carousel_rotation" name="carousel_rotation" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_rotation', 'off'), "on");?>>
									<div class="clearfix"></div>

									<div id="carousel-rotation-row" class="withsublabels">

										<!-- Carousel Rotation Varying Out -->
										<span class="label" id="label_carousel_varyrotate" origtitle="<?php echo  JText::_("Rotation is varying based on the distance to the focused element.");?>"><?php echo  JText::_("Varying Rotation");?> </span>
										<input type="checkbox" class="tp-moderncheckbox withlabel" id="carousel_varyrotate" name="carousel_varyrotate" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_varyrotate', 'off'), "on");?>>
										<div class="clearfix"></div>

										<!-- Carousel Max Rotation -->
										<span class="label" id="label_carousel_maxrotation" origtitle="<?php echo  JText::_("The maximum rotation of the Side elements. Rotation will depend on the element distance to the current focused element. 0 will turn off the Rotation");?>"><?php echo  JText::_("Max. Rotation");?> </span>
										<input type="text" class="text-sidebar withlabel"   id="carousel_maxrotation" name="carousel_maxrotation" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_maxrotation', '0');?>">
										<span><?php echo  JText::_("deg");?></span>
										<div class="clear" ></div>
									</div>

									<!-- Carousel Scale -->
									<span class="label label-with-subsection" id="label_carousel_scale" origtitle="<?php echo  JText::_("Scale enabled/disabled for not focused elements.");?>"><?php echo  JText::_("Scale");?> </span>
									<input type="checkbox" class="tp-moderncheckbox withlabel" id="carousel_scale" name="carousel_scale" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_scale', 'off'), "on");?>>
									<div class="clearfix"></div>


									<div id="carousel-scale-row" class="withsublabels">

										<!-- Carousel Scale Varying Out -->
										<span class="label" id="label_carousel_varyscale" origtitle="<?php echo  JText::_("Scale is varying based on the distance to the focused element.");?>"><?php echo  JText::_("Varying Scale");?> </span>
										<input type="checkbox" class="tp-moderncheckbox withlabel" id="carousel_varyscale" name="carousel_varyscale" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_varyscale', 'off'), "on");?>>
										<div class="clearfix"></div>

										<!-- Carousel Min Scale Down -->
										<span class="label" id="label_carousel_scaledown" origtitle="<?php echo  JText::_("The maximum scale down of the Side elements. Scale will depend on the element distance to the current focused element. Min value is 0 and max value is 100.");?>"><?php echo  JText::_("Max. Scaledown");?> </span>
										<input type="text" class="text-sidebar withlabel"   id="carousel_scaledown" name="carousel_scaledown" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_scaledown', '50');?>">
										<span><?php echo  JText::_("%");?></span>
										<div class="clear"></div>
									</div>
								</div>

								<div id="carousel-aligns" style="display:none">

									<!-- Align of Carousel -->
									<span class="label" id="label_carousel_hposition" origtitle="<?php echo  JText::_("Horizontal Align of the Carousel.");?>"><?php echo  JText::_("Horizontal Aligns");?> </span>
									<select id="carousel_hposition" class="withlabel"  name="carousel_hposition">
										<option value="left" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_hposition', 'center'), "left");?>><?php echo  JText::_("Left");?></option>
										<option value="center" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_hposition', 'center'), "center");?>><?php echo  JText::_("Center");?></option>
										<option value="right" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_hposition', 'center'), "right");?>><?php echo  JText::_("Right");?></option>
									</select>
									<div class="clear"></div>

									<span class="label" id="label_carousel_vposition" origtitle="<?php echo  JText::_("Vertical Align of the Carousel.");?>"><?php echo  JText::_("Vertical Aligns");?> </span>
									<select id="carousel_vposition" class="withlabel"  name="carousel_vposition">
										<option value="top" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_vposition', 'center'), "top");?>><?php echo  JText::_("Top");?></option>
										<option value="center" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_vposition', 'center'), "center");?>><?php echo  JText::_("Center");?></option>
										<option value="bottom" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'carousel_vposition', 'center'), "bottom");?>><?php echo  JText::_("Bottom");?></option>
									</select>
									<div class="clear"></div>
								</div>


								<script>
									jQuery('#carousel_stretch').on("change",function() {
											var sbi = jQuery(this);
											if (sbi.attr("checked") === "checked" && jQuery('#carousel_maxitems option[value="1"]').attr("selected")===undefined) {
												jQuery('#carousel_maxitems option:selected').removeAttr('selected');
												jQuery('#carousel_maxitems option[value="1"]').attr("selected","selected");
											}
									});

									jQuery('#carousel_maxitems').on("change",function() {
										if (jQuery('#carousel_stretch').attr("checked") === "checked" && jQuery('#carousel_maxitems option[value="1"]').attr("selected")===undefined) {
											jQuery('#carousel_stretch').removeAttr("checked");
											jQuery('#carousel_stretch').change();
										}
									});

									jQuery('#carousel_fadeout').on("change",function() {
										var sbi = jQuery(this);

										if (sbi.attr("checked") === "checked") {
											jQuery('#carousel-fade-row').show();

										} else {
											jQuery('#carousel-fade-row').hide();
										}
									});

									jQuery('#carousel_rotation').on("change",function() {
										var sbi = jQuery(this);

										if (sbi.attr("checked") === "checked") {
											jQuery('#carousel-rotation-row').show();

										} else {
											jQuery('#carousel-rotation-row').hide();
										}
									});

									jQuery('#carousel_scale').on("change",function() {
										var sbi = jQuery(this);

										if (sbi.attr("checked") === "checked") {
											jQuery('#carousel-scale-row').show();

										} else {
											jQuery('#carousel-scale-row').hide();
										}
									});
									jQuery('#carousel_scale').change();
									jQuery('#carousel_fadeout').change();
									jQuery('#carousel_rotation').change();
									jQuery('#first_transition_active').change();

								</script>

							</div>
						</div> <!-- END OF CAROUSEL SETTINGS -->

						<!-- Parallax Level -->
						<div class="setting_box">
							<h3 class="box_closed"><i class="rs-rp-accordion-icon eg-icon-camera-alt"></i>

							<div class="setting_box-arrow"></div>

							<span><?php echo  JText::_('Parallax & 3D');?></span>
							</h3>

							<div class="inside" style="display:none">
								<span class="label" id="label_use_parallax" origtitle="<?php echo  JText::_("Enabling this, will give you new options in the slides to create a unique parallax effect");?>"><?php echo  JText::_("Enable Parallax / 3D");?> </span>
								<input type="checkbox" class="tp-moderncheckbox withlabel" id="use_parallax" name="use_parallax" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "use_parallax", "off"), "on");?>>
								<div class="clear"></div>

								<div id="parallax_settings_row">

									<span id="label_disable_parallax_mobile" class="label" origtitle="<?php echo  JText::_("If set to on, parallax will be disabled on mobile devices to save performance");?>"><?php echo  JText::_("Disable on Mobile");?> </span>
									<input type="checkbox" class="tp-moderncheckbox withlabel" id="disable_parallax_mobile" name="disable_parallax_mobile" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "disable_parallax_mobile", "off"), "on");?>>
									<div class="clear"></div>

									<span class="label" id="label_ddd_parallax" origtitle="<?php echo  JText::_("Enabling this, will build a ddd_Rotating World of your Slides.");?>"><?php echo  JText::_("3D");?> </span>
									<input type="checkbox" class="tp-moderncheckbox withlabel" id="ddd_parallax" name="ddd_parallax" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "ddd_parallax", "off"), "on");?>>
									<div class="clear"></div>

									
									
									<div class="show_on_ddd_parallax">
										<h4><?php echo  JText::_("3D Settings");?></h4>
										<div class="withsublabels">
											<span class="label" id="label_ddd_parallax_shadow" origtitle="<?php echo  JText::_("Enabling 3D Shadow");?>"><?php echo  JText::_("3D Shadow");?> </span>
											<input type="checkbox" class="tp-moderncheckbox withlabel" id="ddd_parallax_shadow" name="ddd_parallax_shadow" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "ddd_parallax_shadow", "off"), "on");?>>
											<div class="clear"></div>

											<span class="label" id="label_ddd_parallax_bgfreeze" origtitle="<?php echo  JText::_("BG 3D Disabled");?>"><?php echo  JText::_("3D Background Disabled");?> </span>
											<input type="checkbox" class="tp-moderncheckbox withlabel" id="ddd_parallax_bgfreeze" name="ddd_parallax_bgfreeze" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "ddd_parallax_bgfreeze", "off"), "on");?>>
											<div class="clear"></div>

											<span class="label" id="label_ddd_parallax_overflow" origtitle="<?php echo  JText::_("If option is enabled, all slides and Layers are cropped by the Slider sides.");?>"><?php echo  JText::_("Slider Overflow Hidden");?> </span>
											<input type="checkbox" class="tp-moderncheckbox withlabel" id="ddd_parallax_overflow" name="ddd_parallax_overflow" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "ddd_parallax_overflow", "off"), "on");?>>
											<div class="clear"></div>

											<span class="label" id="label_ddd_parallax_layer_overflow" origtitle="<?php echo  JText::_("If option enabled, Layers are cropped by the Grid Layer Dimensions to avoid Floated 3d Texts and hide Elements outside of the Slider.");?>"><?php echo  JText::_("Layers Overflow Hidden");?> </span>
											<input type="checkbox" class="tp-moderncheckbox withlabel" id="ddd_parallax_layer_overflow" name="ddd_parallax_layer_overflow" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "ddd_parallax_layer_overflow", "off"), "on");?>>
											<div class="clear"></div>


											<!--<span class="label" id="label_ddd_parallax_path" origtitle="<?php echo  JText::_("Select the Events which should trigger the 3D Animation.  Mouse - Mouse Movements will rotate the Slider, Static Paths will set Single or Animated 3d Rotations per Slides (Edit these paths via the Slide Editor), and both will allow you to use both in the same Time.");?>"><?php echo  JText::_("3D Path");?> </span>
											<select id="ddd_parallax_path" class="withlabel"  name="ddd_parallax_path" style="max-width:110px">
												<option value="mouse" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'ddd_parallax_path', 'mouse'), "mouse");?>><?php echo  JText::_("Mouse Based");?></option>
												<option value="static" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'ddd_parallax_path', 'mouse'), "static");?>><?php echo  JText::_("Static Path (Set Slide by Slide)");?></option>
												<option value="both" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, 'ddd_parallax_path', 'mouse'), "both");?>><?php echo  JText::_("Both");?></option>												
											</select>
											<div class="clear"></div>-->

											<span class="label" id="label_ddd_parallax_zcorrection" origtitle="<?php echo  JText::_("Solves issues in Safari Browser. It will move layers along z-axis if BG Freeze enabled to avoid 3d Rendering issues");?>"><?php echo  JText::_("3D Crop Fix (z)");?> </span>
											<input type="text" class="text-sidebar withlabel" id="ddd_parallax_zcorrection" name="ddd_parallax_zcorrection" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "ddd_parallax_zcorrection", "65");?>">									
											<span ><?php echo  JText::_("px");?></span>
											<div class="clear"></div>

										</div>
									</div>

									

									
									<h4><?php echo  JText::_("Mouse Sensibility");?></h4>
									<div class="withsublabels">
										<div class="hide_on_ddd_parallax">
											<span id="label_parallax_type" class="label" origtitle="<?php echo  JText::_("Defines on what event type the parallax should react to");?>"><?php echo  JText::_("Event");?></span>
											<select id="parallax_type" name="parallax_type"  class="withlabel">
												<option value="mouse" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "parallax_type", "mouse"), "mouse");?>><?php echo  JText::_("Mouse Move");?></option>
												<option value="scroll" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "parallax_type", "mouse"), "scroll");?>><?php echo  JText::_("Scroll Position");?></option>
												<option value="mouse+scroll" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "parallax_type", "mouse"), "mouse+scroll");?>><?php echo  JText::_("Move and Scroll");?></option>
											</select>
											<div class="clear"></div>

											<span id="label_parallax_origo" class="label" origtitle="<?php echo  JText::_("Mouse Based parallax calculation Origo");?>"><?php echo  JText::_("Parallax Origo");?></span>
											<select id="parallax_origo" name="parallax_origo"  class="withlabel">
												<option value="enterpoint" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "parallax_origo", "enterpoint"), "enterpoint");?>><?php echo  JText::_("Mouse Enter Point");?></option>
												<option value="slidercenter" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "parallax_origo", "enterpoint"), "slidercenter");?>><?php echo  JText::_("Slider Center");?></option>										
											</select>
											<div class="clear"></div>
										</div>

										<span class="label" id="label_parallax_speed" origtitle="<?php echo  JText::_("Parallax Speed for Mouse movents.");?>"><?php echo  JText::_("Animation Speed");?> </span>
										<input type="text" class="text-sidebar withlabel" id="parallax_speed" name="parallax_speed" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "parallax_speed", "400");?>">									
										<span ><?php echo  JText::_("ms");?></span>
										<div class="clear"></div>		
									</div>							

									<h4 class="hide_on_ddd_parallax"><?php echo  JText::_("Parallax Levels");?></h4>
									<h4 class="show_on_ddd_parallax"><?php echo  JText::_("3D Depth Levels");?></h4>

									<div class="withsublabels">
										<span class="show_on_ddd_parallax">
											<span class="label" id="label_parallax_level_16" origtitle="<?php echo  JText::_("Defines the Strength of the 3D Rotation on the Background and Layer Groups.  The Higher the Value the stronger the effect.  All other Depth will offset this default value !");?>"><span><?php echo  JText::_("Default 3D Depth");?></span> </span>
											<input type="text" class="text-sidebar withlabel" id="parallax_level_16" name="parallax_level_16" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "parallax_level_16", "55");?>">
											<span class="clear"></span>
										</span>

										<span class="label" id="label_parallax_level_1" origtitle="<?php echo  JText::_("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.");?>"><span class="hide_on_ddd_parallax"><?php echo  JText::_("Level Depth 1");?></span><span class="show_on_ddd_parallax"><?php echo  JText::_("Depth 1");?></span></span>
										<input type="text" class="text-sidebar withlabel" id="parallax_level_1" name="parallax_level_1" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "parallax_level_1", "5");?>">									
										<div class="clear"></div>

										<span class="label" id="label_parallax_level_2" origtitle="<?php echo  JText::_("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.");?>"><span class="hide_on_ddd_parallax"><?php echo  JText::_("Level Depth 2");?></span><span class="show_on_ddd_parallax"><?php echo  JText::_("Depth 2");?></span> </span>
										<input type="text" class="text-sidebar withlabel" id="parallax_level_2" name="parallax_level_2" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "parallax_level_2", "10");?>">
										<div class="clear"></div>

										<span class="label" id="label_parallax_level_3" origtitle="<?php echo  JText::_("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.");?>"><span class="hide_on_ddd_parallax"><?php echo  JText::_("Level Depth 3");?></span><span class="show_on_ddd_parallax"><?php echo  JText::_("Depth 3");?></span> </span>
										<input type="text" class="text-sidebar withlabel " id="parallax_level_3" name="parallax_level_3" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "parallax_level_3", "15");?>">
										<div class="clear"></div>

										<span class="label" id="label_parallax_level_4" origtitle="<?php echo  JText::_("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.");?>"><span class="hide_on_ddd_parallax"><?php echo  JText::_("Level Depth 4");?></span><span class="show_on_ddd_parallax"><?php echo  JText::_("Depth 4");?></span> </span>
										<input type="text" class="text-sidebar withlabel" id="parallax_level_4" name="parallax_level_4" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "parallax_level_4", "20");?>">
										<div class="clear"></div>

										<span class="label" id="label_parallax_level_5" origtitle="<?php echo  JText::_("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.");?>"><span class="hide_on_ddd_parallax"><?php echo  JText::_("Level Depth 5");?></span><span class="show_on_ddd_parallax"><?php echo  JText::_("Depth 5");?></span> </span>
										<input type="text" class="text-sidebar withlabel" id="parallax_level_5" name="parallax_level_5" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "parallax_level_5", "25");?>">
										<div class="clear"></div>
										
										<span class="label" id="label_parallax_level_6" origtitle="<?php echo  JText::_("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.");?>"><span class="hide_on_ddd_parallax"><?php echo  JText::_("Level Depth 6");?></span><span class="show_on_ddd_parallax"><?php echo  JText::_("Depth 6");?></span> </span>
										<input type="text" class="text-sidebar withlabel" id="parallax_level_6" name="parallax_level_6" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "parallax_level_6", "30");?>">
										<div class="clear"></div>

										<span class="label" id="label_parallax_level_7" origtitle="<?php echo  JText::_("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.");?>"><span class="hide_on_ddd_parallax"><?php echo  JText::_("Level Depth 7");?></span><span class="show_on_ddd_parallax"><?php echo  JText::_("Depth 7");?></span> </span>
										<input type="text" class="text-sidebar withlabel" id="parallax_level_7" name="parallax_level_7" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "parallax_level_7", "35");?>">
										<div class="clear"></div>

										<span class="label" id="label_parallax_level_8" origtitle="<?php echo  JText::_("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.");?>"><span class="hide_on_ddd_parallax"><?php echo  JText::_("Level Depth 8");?></span><span class="show_on_ddd_parallax"><?php echo  JText::_("Depth 8");?></span> </span>
										<input type="text" class="text-sidebar withlabel" id="parallax_level_8" name="parallax_level_8" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "parallax_level_8", "40");?>">
										<div class="clear"></div>

										<span class="label" id="label_parallax_level_9" origtitle="<?php echo  JText::_("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.");?>"><span class="hide_on_ddd_parallax"><?php echo  JText::_("Level Depth 9");?></span><span class="show_on_ddd_parallax"><?php echo  JText::_("Depth 9");?></span> </span>
										<input type="text" class="text-sidebar withlabel" id="parallax_level_9" name="parallax_level_9" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "parallax_level_9", "45");?>">
										<div class="clear"></div>

										<span class="label" id="label_parallax_level_10" origtitle="<?php echo  JText::_("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.");?>"><span class="hide_on_ddd_parallax"><?php echo  JText::_("Level Depth 10");?></span><span class="show_on_ddd_parallax"><?php echo  JText::_("Depth 10");?></span> </span>
										<input type="text" class="text-sidebar withlabel" id="parallax_level_10" name="parallax_level_10" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "parallax_level_10", "46");?>">
										<div class="clear"></div>

										<span class="label" id="label_parallax_level_11" origtitle="<?php echo  JText::_("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.");?>"><span class="hide_on_ddd_parallax"><?php echo  JText::_("Level Depth 11");?></span><span class="show_on_ddd_parallax"><?php echo  JText::_("Depth 11");?></span> </span>
										<input type="text" class="text-sidebar withlabel" id="parallax_level_11" name="parallax_level_11" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "parallax_level_11", "47");?>">
										<div class="clear"></div>

										<span class="label" id="label_parallax_level_12" origtitle="<?php echo  JText::_("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.");?>"><span class="hide_on_ddd_parallax"><?php echo  JText::_("Level Depth 12");?></span><span class="show_on_ddd_parallax"><?php echo  JText::_("Depth 12");?></span> </span>
										<input type="text" class="text-sidebar withlabel" id="parallax_level_12" name="parallax_level_12" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "parallax_level_12", "48");?>">
										<div class="clear"></div>

										<span class="label" id="label_parallax_level_13" origtitle="<?php echo  JText::_("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.");?>"><span class="hide_on_ddd_parallax"><?php echo  JText::_("Level Depth 13");?></span><span class="show_on_ddd_parallax"><?php echo  JText::_("Depth 13");?></span> </span>
										<input type="text" class="text-sidebar withlabel" id="parallax_level_13" name="parallax_level_13" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "parallax_level_13", "49");?>">
										<div class="clear"></div>

										<span class="label" id="label_parallax_level_14" origtitle="<?php echo  JText::_("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.");?>"><span class="hide_on_ddd_parallax"><?php echo  JText::_("Level Depth 14");?></span><span class="show_on_ddd_parallax"><?php echo  JText::_("Depth 14");?></span> </span>
										<input type="text" class="text-sidebar withlabel" id="parallax_level_14" name="parallax_level_14" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "parallax_level_14", "50");?>">
										<div class="clear"></div>

										<span class="label" id="label_parallax_level_15" origtitle="<?php echo  JText::_("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.");?>"><span class="hide_on_ddd_parallax"><?php echo  JText::_("Level Depth 15");?></span><span class="show_on_ddd_parallax"><?php echo  JText::_("Depth 15");?></span> </span>
										<input type="text" class="text-sidebar withlabel" id="parallax_level_15" name="parallax_level_15" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "parallax_level_15", "51");?>">
										<div class="clear"></div>

										
									</div>
								</div>
							</div>

							<script>
								jQuery('#use_parallax').on("change",function() {
										var sbi = jQuery(this);
										drawToolBarPreview();
										if (sbi.attr("checked") === "checked") {
											jQuery('#parallax_settings_row').show();
											jQuery('#ddd_parallax').change();
										} else {
											jQuery('#parallax_settings_row').hide();
											jQuery('.hide_on_ddd_parallax').show();
											jQuery('.show_on_ddd_parallax').hide();
										}
								});
								jQuery('#ddd_parallax').on("change",function() {
									drawToolBarPreview();
									var sbi = jQuery(this);
									if (sbi.attr("checked") === "checked" && jQuery('#use_parallax').attr("checked")=== "checked") {
											jQuery('.hide_on_ddd_parallax').hide();
											jQuery('.show_on_ddd_parallax').show();
										} else {
											jQuery('.hide_on_ddd_parallax').show();
											jQuery('.show_on_ddd_parallax').hide();
										}									
								});

								jQuery('#ddd_parallax_shadow').on("change",drawToolBarPreview);

								jQuery('#use_parallax').change();
								jQuery('#ddd_parallax').change();
							</script>

						</div>



					<?php
					if (!empty($this->item->id)) {
						?>
						<!-- SPEED MONITOR -->
						<div class="setting_box">
							<h3 class="box_closed"><i class="rs-rp-accordion-icon eg-icon-cog-alt"></i>
								<div class="setting_box-arrow"></div>
								<span><?php echo  JText::_("Performance and SEO Optimization");?></span>
							</h3>

							<div class="inside" style="display:none;">

								<!-- LAZY LOAD -->
								<?php
								$llt = ZTSliderFunctions::getVal($this->arrFieldsParams, 'lazy_load_type', false);
								if ($llt === false) {
									//do fallback checks to removed lazy_load value since version 5.0 and replaced with an enhanced version
									$old_ll = ZTSliderFunctions::getVal($this->arrFieldsParams, 'lazy_load', 'off');
									$llt = ($old_ll == 'on') ? 'all' : 'none';
								}
								?>
								<span id="label_lazy_load_type" class="label" origtitle="<?php echo  JText::_("How to load/preload the images. <br><br><strong>All</strong> - Load all image element in a sequence at the initialisation. This will boost up the loading of your page, and will preload all images to have a smooth and breakless run already in the first loop.  <br><br><strong>Smart</strong> - It will load the page as quick as possible, and load only the current and neighbour slide elements. If slide is called which not loaded yet, will be loaded on demand with minimal delays.   <br><br><strong>Single</strong> - It will load only the the start slide. Any other slides will be loaded on demand.");?>"><?php echo  JText::_("Lazy Load");?> </span>
								<select id="lazy_load_type" name="lazy_load_type" class="withlabel" >
									<option value="all" <?php echo ZTSliderFunctions::selected($llt, 'all');?>><?php echo  JText::_("All");?></option>
									<option value="smart" <?php echo ZTSliderFunctions::selected($llt, 'smart');?>><?php echo  JText::_("Smart");?></option>
									<option value="single" <?php echo ZTSliderFunctions::selected($llt, 'single');?>><?php echo  JText::_("Single");?></option>
									<option value="none" <?php echo ZTSliderFunctions::selected($llt, 'none');?>><?php echo  JText::_("No Lazy Loading");?></option>
								</select>
								<div class="clearfix"></div>
								
								<?php
								/*$seo_opti = ZTSliderFunctions::getVal($this->arrFieldsParams, 'seo_optimization', 'none');
								?>
								<span id="label_seo_optimization" class="label" origtitle="<?php echo  JText::_('Define SEO Optimization for the Images in the Slider, useful if Lazy Load is on.');?>"><?php echo  JText::_('SEO Optimization');?> </span>
								<select id="seo_optimization" name="seo_optimization" class="withlabel">
									<option value="none" <?php echo ZTSliderFunctions::selected($seo_opti, 'none');?>><?php echo  JText::_("None");?></option>
									<option value="noscript" <?php echo ZTSliderFunctions::selected($seo_opti, 'noscript');?>><?php echo  JText::_("NoScript");?></option>
									<option value="noframe" <?php echo ZTSliderFunctions::selected($seo_opti, 'noframe');?>><?php echo  JText::_("NoFrame");?></option>
								</select>
								<div class="clearfix"></div>
								*/ ?>
								
								<!-- MONITORING PART -->
								<?php //list all images and speed here ?>
								<?php
								ZTSliderOperations::get_slider_speed($this->item->id);
								?>
							</div><!-- END OF INSIDE-->
						</div>
						<script>
							jQuery(document).on("ready",function() {
								jQuery('#lazy_load_type').on("change",function() {
									switch (jQuery('#lazy_load_type option:selected').val()) {
										case "all":
										case "none":
											jQuery('.tp-monitor-single-speed').hide();
											jQuery('.tp-monitor-smart-speed').hide();
											jQuery('.tp-monitor-all-speed').show();
										break;
										case "smart":
											jQuery('.tp-monitor-single-speed').hide();
											jQuery('.tp-monitor-smart-speed').show();
											jQuery('.tp-monitor-all-speed').hide();
										break;
										case "single":
											jQuery('.tp-monitor-single-speed').show();
											jQuery('.tp-monitor-smart-speed').hide();
											jQuery('.tp-monitor-all-speed').hide();
										break;
									}
								});
								jQuery('#lazy_load_type').change();
							})
						</script>

						<?php
						}
						?>




					<!-- FALLBACKS -->
					<div class="setting_box" id="phandlings">
						<h3 class="box_closed"><i class="rs-rp-accordion-icon eg-icon-medkit"></i>

						<div class="setting_box-arrow"></div>

						<span class="phandlingstitle"><?php echo  JText::_('Problem Handlings');?></span>
						</h3>

						<div class="inside" style="display:none;">
							<ul class="main-options-small-tabs" style="display:inline-block; ">
								<li data-content="#problem-fallback" class="selected"><?php echo  JText::_('Fallbacks');?></li>
								<li id="phandling_menu" data-content="#problem-troubleshooting" class=""><?php echo  JText::_('Troubleshooting');?></li>
							</ul>
							<div id="problem-fallback">
								<span id="label_simplify_ie8_ios4" class="label" origtitle="<?php echo  JText::_("Simplyfies the Slider on IOS4 and IE8");?>"><?php echo  JText::_("Simplify on IOS4/IE8");?> </span>
								<input type="checkbox" class="tp-moderncheckbox withlabel" id="simplify_ie8_ios4" name="simplify_ie8_ios4" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "simplify_ie8_ios4", "off"), "on");?>>
								<div class="clear"></div>

								<div id="label_show_alternative_type" class="label" origtitle="<?php echo  JText::_("Disables the Slider and load an alternative image instead");?>"><?php echo  JText::_("Use Alternative Image");?> </div>
								<select id="show_alternative_type" name="show_alternative_type" class="withlabel">
									<option value="off" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "show_alternative_type", "off"), "off");?>><?php echo  JText::_("Off");?></option>
									<option value="mobile" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "show_alternative_type", "off"), "mobile");?>><?php echo  JText::_("On Mobile");?></option>
									<option value="ie8" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "show_alternative_type", "off"), "ie8");?>><?php echo  JText::_("On IE8");?></option>
									<option value="mobile-ie8" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "show_alternative_type", "off"), "mobile-ie8");?>><?php echo  JText::_("On Mobile and IE8");?></option>
								</select>
								<div class="clear"></div>
								
								<div class="enable_alternative_image">
									<div id="label_show_alternate_image" class="label" origtitle="<?php echo  JText::_("The image that will be loaded instead of the slider.");?>"><?php echo  JText::_("Alternate Image");?> </div>
									<input type="text" style="width: 104px;" class="text-sidebar-long withlabel" id="show_alternate_image" name="show_alternate_image" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, "show_alternate_image", "");?>">
									<a original-title="" href="javascript:void(0)" class="button-image-select-background-img button-primary revblue"><?php echo  JText::_('Set'); ?></a>
									<div class="clear"></div>
								</div>
								
								<div class="rs-show-on-auto">
									<div id="label_ignore_height_changes" class="label" origtitle="<?php echo  JText::_("Prevents jumping of background image for Android devices for example");?>"><?php echo  JText::_("Ignore Height Changes");?> </div>
									<select id="ignore_height_changes" name="ignore_height_changes" class="withlabel">
										<option value="off" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "ignore_height_changes", "off"), "off");?>><?php echo  JText::_("Off");?></option>
										<option value="mobile" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "ignore_height_changes", "off"), "mobile");?>><?php echo  JText::_("On Mobile");?></option>
										<option value="always" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "ignore_height_changes", "off"), "always");?>><?php echo  JText::_("Always");?></option>
									</select>
									<div class="clear"></div>
									
									<span class="label" id="label_ignore_height_changes_px" origtitle="<?php echo  JText::_("Ignores the Ignore Height Changes feature under a certain amount of pixels.");?>"><?php echo  JText::_("Ignore Height Changes Under");?></span>
									<input type="text" class="text-sidebar withlabel" id="ignore_height_changes_px" name="ignore_height_changes_px" value="<?php echo ZTSliderFunctions::getVal($this->arrFieldsParams, 'ignore_height_changes_px', '0');?>">
									<span><?php echo  JText::_("px");?></span>
									<div class="clear"></div>
								</div>
							</div>
							<div id="problem-troubleshooting" style="display:none;">
								<div id="label_jquery_noconflict" class="label" origtitle="<?php echo  JText::_("Turns on / off jquery noconflict mode. Try to enable this option if javascript conflicts exist on the page.");?>"><?php echo  JText::_("JQuery No Conflict Mode");?> </div>
								<input type="checkbox" class="tp-moderncheckbox withlabel" id="jquery_noconflict" name="jquery_noconflict" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "jquery_noconflict", "off"), "on");?>>
								<div class="clear"></div>

								<span id="label_js_to_body" class="label" origtitle="<?php echo  JText::_("Try this to fix some javascript conflicts of type: TypeError: tpj('#rev_slider_1_1').show().revolution is not a function");?>"><?php echo  JText::_("Put JS Includes To Body");?> </span>
								<span id="js_to_body" class="withlabel">
									<input type="radio" id="js_to_body_1" value="true"  name="js_to_body" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "js_to_body", "false"), "true");?>>
									<label for="js_to_body_1" style="cursor:pointer;margin-right:15px"><?php echo  JText::_('On');?></label>
									<input type="radio" id="js_to_body_2" value="false" name="js_to_body" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "js_to_body", "false"), "false");?>>
									<label for="js_to_body_2" style="cursor:pointer;"><?php echo  JText::_('Off');?></label>
								</span>
								<div class="clear"></div>

								<div id="label_output_type" class="label" origtitle="<?php echo  JText::_("Activate a protection against wordpress output filters that adds html blocks to the shortcode output like P and BR.");?>"><?php echo  JText::_("Output Filters Protection");?> </div>
								<select id="output_type" name="output_type" style="max-width:105px" class="withlabel">
									<option value="none" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "output_type", "none"), "none");?>><?php echo  JText::_("None");?></option>
									<option value="compress" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "output_type", "none"), "compress");?>><?php echo  JText::_("By Compressing Output");?></option>
									<option value="echo" <?php echo ZTSliderFunctions::selected(ZTSliderFunctions::getVal($this->arrFieldsParams, "output_type", "none"), "echo");?>><?php echo  JText::_("By Echo Output");?></option>
								</select>
								<div class="clear"></div>

								<div id="label_jquery_debugmode" class="label phandlingstitle" origtitle="<?php echo  JText::_("Turns on / off visible Debug Mode on Front End.");?>"><?php echo  JText::_("Debug Mode");?> </div>
								<input type="checkbox" class="tp-moderncheckbox withlabel" id="jquery_debugmode" name="jquery_debugmode" data-unchecked="off" <?php echo ZTSliderFunctions::checked(ZTSliderFunctions::getVal($this->arrFieldsParams, "jquery_debugmode", "off"), "on");?>>
								<div class="clear"></div>

								<div style="margin-top:15px"><a href="http://www.themepunch.com/faq/troubleshooting-tips-for-5-0/" target="_blank"><?php echo  JText::_("Follow FAQ for Troubleshooting");?></a></div>
							</div>
						</div>
						 <script>
							jQuery('#show_alternative_type').on("change",function() {
									var sbi = jQuery(this);
									switch (sbi.val()) {
										case "off":
											jQuery('.enable_alternative_image').hide();
										break;
										default:
											jQuery('.enable_alternative_image').show();
										break;
									}
							});

							jQuery('#jquery_debugmode').on("change",function() {
								if (jQuery(this).attr("checked")==="checked")
									jQuery('#phandlings').addClass("debugmodeon");
								else
									jQuery('#phandlings').removeClass("debugmodeon");
							});

							if (jQuery('#jquery_debugmode').attr("checked")==="checked")
									jQuery('#phandlings').addClass("debugmodeon");
								else
									jQuery('#phandlings').removeClass("debugmodeon");

							jQuery('#show_alternative_type').change();
						</script>
					</div>

					<div class="setting_box rs-cm-refresh">
						<h3 class="box_closed"><i class="rs-rp-accordion-icon eg-icon-font"></i>
							<div class="setting_box-arrow"></div>
							<span><?php echo  JText::_('Google Fonts');?></span>
						</h3>

						<div class="inside" style="display:none">

							<div class="rs-gf-listing">
								<?php
								$subsets = ZTSliderFunctions::getVal($this->arrFieldsParams, 'subsets', array());
								
								$gf = array();
								if ($this->is_edit) {
									if(!empty($this->item->id)) {
										$gf = $this->slider->getUsedFonts();
									}
								}
								if(!empty($gf)){
									echo '<h4 style="margin-top:0px;margin-bottom:8px">'.JText::_('Dynamically Registered Google Fonts').'</h4>';
									
									foreach($gf as $mgf => $mgv){
										echo '<div class="single-google-font-item">';
										echo '<span class="label font-name-label">'.$mgf.':';
										if(!empty($mgv['variants'])){
											$mgfirst = true;
											foreach($mgv['variants'] as $mgvk => $mgvv){
												if(!$mgfirst) echo ',';
												echo $mgvk;
												$mgfirst = false;
											}
										}	
										echo '</span>';
										echo '<div class="single-font-setting-wrapper">';
										if(!empty($mgv['slide'])){
											echo '<span class="label">Used in Slide:</span>';
											echo '<select class="google-font-slide-link-list">';
											echo '<option value="blank">Edit Slide(s)</option>';
											foreach($mgv['slide'] as $mgskey => $mgsval){
												echo '<option value="'.JRoute::_('index.php?option=com_zt_layerslider&view=slides&id='.$mgsval['id'].'&slider_id='.$this->item->id,false).'">'.JText::_('Edit:').' '.$mgsval['title'].'</option>';
											}
											echo '</select>';
											
										}
										
										if(!empty($mgv['subsets'])){
											echo '<div class="clear"></div>';
											
											foreach($mgv['subsets'] as $ssk => $ssv){
												echo '<span class="label subsetlabel">'.$ssv.'</span>';
												echo '<input class="tp-moderncheckbox" type="checkbox" data-useval="true" value="'.$mgf.'+'.$ssv.'" name="subsets[]" ';
												if(array_search($mgf.'+'.$ssv, $subsets) !== false){
													echo 'checked="checked"';
												}
												echo '> ';
											}
										}
										echo '</div>';
										echo '</div>';
									}
								} else {
									echo '<h4 style="margin-top:0px;">'.JText::_('No dynamic fonts registered').'</h4>';
								}
								
								?>
							</div>
							<script>
								jQuery('.google-font-slide-link-list').on('change', function() {
									var t = jQuery(this),
										v = t.find('option:selected').val();

									if (v!="blank") {
										var win = window.open(v,'_blank');
										if (win) {
											win.focus();
										} else {
											alert('<?php echo  JText::_('Link to Slide Editor is Blocked ! Please Allow Pop Ups for this Site !'); ?>');
										}
									}
									t.val("blank");
									
								});
							</script>
							<h4><?php echo  JText::_("Deprecated Google Font Import",'revslider');?></h4>
							<div id="rs-google-fonts">
							
							</div>
							<!--p><a class="button-primary revblue" id="add_new_google_font" original-title=""><i class="revicon-cog"></i><?php echo  JText::_('Add New Font'); ?></a></p-->
							<!--i style="font-size:10px;color:#777"><?php echo  JText::_('Copy the Google Font Family from <a href="http://www.google.com/fonts" target="_blank">http://www.google.com/fonts</a> like: <strong>Open+Sans:400,700,600</strong>'); ?></i-->
						</div>
					</div>
					

				</form>
			
				<!-- IMPORT / EXPORT SETTINGS -->
				<?php
//				if(!RS_DEMO){
					if ($this->is_edit) {
						?>
						<div class="setting_box">
							<h3 class="box_closed"><i class="rs-rp-accordion-icon eg-icon-upload"></i>
								<div class="setting_box-arrow"></div>
								<span><?php echo  JText::_('Import / Export');?></span>
							</h3>
							<div class="inside" style="display:none">
								<ul class="main-options-small-tabs" style="display:inline-block; ">
									<li data-content="#import-import" class="selected"><?php echo  JText::_('Import');?></li>
									<li data-content="#import-export" class=""><?php echo  JText::_('Export');?></li>
								</ul>
								
								<div id="import-import">
									<?php
									if(!JFactory::getUser()->authorise('core.admin')){
										echo JText::_('Import only available for Administrators');
									}else{
										?>
										<form name="import_slider_form" id="rs_import_slider_form" action="<?php echo JRoute::_('index.php?option=com_zt_layerslider'); ?>" enctype="multipart/form-data" method="post">
											<input type="hidden" name="task" value="slider.importslider">
											<input type="hidden" name="sliderid" value="<?php echo $this->item->id; ?>">
											<input type="file" name="import_file" class="input_import_slider" style="width:100%; font-size:12px;">
											<div style="width:100%;height:25px"></div>

											<span class="label label-with-subsection" id="label_update_animations" origtitle="<?php echo  JText::_("Overwrite or append the custom animations due the new imported values ?");?>"><?php echo  JText::_("Custom Animations");?> </span>
											<input class="withlabel" type="radio" name="update_animations" value="true" checked="checked"> <?php echo  JText::_("overwrite")?>
											<input class="withlabel"  type="radio" name="update_animations" value="false"> <?php echo  JText::_("append")?>
											<div class="tp-clearfix"></div>

											<span class="label label-with-subsection" id="label_update_static_captions" origtitle="<?php echo  JText::_("Overwrite or append the static styles due the new imported values ?");?>"><?php echo  JText::_("Static Styles");?> </span>
											<input class="withlabel" type="radio" name="update_static_captions" value="true"> <?php echo  JText::_("overwrite")?>
											<input class="withlabel" type="radio" name="update_static_captions" value="false"> <?php echo  JText::_("append")?>
											<input class="withlabel" type="radio" name="update_static_captions" value="none" checked="checked"> <?php echo  JText::_("ignore",'revslider'); ?>
											<div class="tp-clearfix"></div>

											<div class="divide5"></div>
											<input type="submit" style="width:100%" class="button-primary revgreen" id="rs-submit-import-form" value="<?php echo  JText::_('Import Slider'); ?>">
										</form>
										<div class="divide20"></div>
										<div class="revred api-desc" style="padding:8px;color:#fff;font-weight:600;font-size:12px"><?php echo  JText::_("Note! Style templates will be updated if they exist. Importing slider, will delete all the current slider settings and slides and replacing it with the imported content.")?></div>
										<?php
									}
									?>
								</div>

								<div id="import-export" style="display:none">
									<a id="button_export_slider" class='button-primary revgreen' href='javascript:void(0)' style="width:100%;text-align:center;" ><?php echo  JText::_("Export Slider")?></a> <div style="display: none;"><input type="checkbox" name="export_dummy_images"> <?php echo  JText::_("Export with Dummy Images")?></div>
								</div>
							</div>
						</div>
						<?php 
					}
//				}
				?> <!-- END OF IMPORT EXPORT SETTINGS -->

					
				</div>

			<!-- THE TOOLBAR FUN -->
			<div id="form_toolbar">
				<div class="toolbar-title"></div>
				<div class="toolbar-content"></div>
				<!--<div class="toolbar-title-a">Schematic</div>				-->
				<!--<div class="toolbar-media"></div>-->
				<div class="toolbar-sliderpreview">
					<div class="toolbar-slider">
						<div class="toolbar-slider-image"></div>
						<div class="toolbar-progressbar"></div>
						<div class="toolbar-dottedoverlay"></div>
						<div class="toolbar-navigation-right"></div>
						<div class="toolbar-navigation-left"></div>
						<div class="toolbar-navigation-bullets">
							<div class="toolbar-navigation-bullet"></div>
							<div class="toolbar-navigation-bullet"></div>
							<div class="toolbar-navigation-bullet" style="margin:0px !important"></div>
							<span class="tp-clearfix"></span>
						</div>

						<div class="toolbar-navigation-thumbs">
							<div class="toolbar-navigation-thumb"></div>
							<div style="border-color:#fff" class="toolbar-navigation-thumb"></div>
							<div class="toolbar-navigation-thumb" style="margin:0px !important"></div>
							<span class="toolbar-navigation-thumbs-bg tntb"></span>
							<span class="tp-clearfix"></span>
						</div>

						<div class="toolbar-navigation-tabs">
							<div class="toolbar-navigation-tab"><span class="long-lorem-ipsum"></span><span class="short-lorem-ipsum"></span></div>
							<div style="border-color:#fff"  class="toolbar-navigation-tab"><span class="long-lorem-ipsum"></span><span class="short-lorem-ipsum"></span></div>
							<div class="toolbar-navigation-tab" style="margin:0px !important"><span class="long-lorem-ipsum"></span><span class="short-lorem-ipsum"></span></div>
							<span class="toolbar-navigation-tabs-bg tntb"></span>
							<span class="tp-clearfix"></span>
						</div>
					</div>
				</div>
				<div id="preview-nav-wrapper">
					<div class="rs-editing-preview-overlay"></div>
					<div class="rs-arrows-preview">
						<div class="tp-arrows tp-leftarrow"></div>
						<div class="tp-arrows tp-rightarrow"></div>
					</div>
					<div class="rs-bullets-preview"></div>
					<div class="rs-thumbs-preview"></div>
					<div class="rs-tabs-preview"></div>
				</div>
				<div class="toolbar-extended-info"><i><?php echo  JText::_('*Only Illustration, most changes are not visible.');?></i></div>
			</div>
		</div>

		<div class="clear"></div>
		<script type="text/javascript">
			
			
			<?php
			$ph_presets_full = array();
			foreach($arr_navigations as $nav){
				if(isset($nav['settings']) && isset($nav['settings']['presets']) && !empty($nav['settings']['presets'])){
					foreach($nav['settings']['presets'] as $prsst){
						if(!isset($ph_presets_full[$nav['handle']][$prsst['type']])){
							$ph_presets_full[$nav['handle']][$prsst['type']] = array();
						}
						if(!isset($ph_presets_full[$nav['handle']][$prsst['type']][$prsst['handle']])){
							$ph_presets_full[$nav['handle']][$prsst['type']][$prsst['handle']] = $prsst;
						}
					}
				}
			}
			?>
			
			var phpfullresets = jQuery.parseJSON(<?php echo ZTSliderFunctions::jsonEncodeForClientSide($ph_presets_full); ?>);
			
			if(phpfullresets == null) phpfullresets = [];
			
			var fullresetsstay = false;
			// Some Inline Script for Right Side Panel Actions.
			jQuery(document).ready(function($) {
				ZtSliderSettings.createModernOnOff();

				jQuery(".tp-moderncheckbox").each(function() {
					ZtSliderSettings.onoffStatus(jQuery(this));
				});


				jQuery('#def-background_fit').change(function(){
					if(jQuery(this).val() == 'percentage'){
						jQuery('input[name="def-bg_fit_x"]').show();
						jQuery('input[name="def-bg_fit_y"]').show();
					}else{
						jQuery('input[name="def-bg_fit_x"]').hide();
						jQuery('input[name="def-bg_fit_y"]').hide();
					}
				});

				jQuery('#slide_bg_position').change(function(){
					if(jQuery(this).val() == 'percentage'){
						jQuery('input[name="def-bg_position_x"]').show();
						jQuery('input[name="def-bg_position_y"]').show();
					}else{
						jQuery('input[name="def-bg_position_x"]').hide();
						jQuery('input[name="def-bg_position_y"]').hide();
					}
				});

				jQuery('#slide_bg_end_position').change(function(){
					if(jQuery(this).val() == 'percentage'){
						jQuery('input[name="def-bg_end_position_x"]').show();
						jQuery('input[name="def-bg_end_position_y"]').show();
					}else{
						jQuery('input[name="def-bg_end_position_x"]').hide();
						jQuery('input[name="def-bg_end_position_y"]').hide();
					}
				});

				jQuery('input[name="def-kenburn_effect"]').change(function(){
					if(jQuery(this).attr('checked') == 'checked'){
						jQuery('#def-kenburns-wrapper').show();
					}else{
						jQuery('#def-kenburns-wrapper').hide();
					}
				});


				// Accordion
				jQuery('.settings_wrapper').find('.setting_box h3').each(function() {
		    		jQuery(this).click(function() {
		    			var btn = jQuery(this),
		    				sb = jQuery(this).closest('.setting_box'),
		    				toclose = btn.hasClass("box_closed") ? true : false;

		    			if (btn.closest('.settings_wrapper').hasClass("closeallothers"))
			    			btn.closest('.settings_wrapper').find('.setting_box').each(function() {
			    				var sb = jQuery(this);
			    				sb.find('h3').addClass("box_closed");
			    				sb.find('.inside').slideUp(200);
			    			});
			    		else
			    		{
			    			sb.find('h3').addClass("box_closed");
			    			sb.find('.inside').slideUp(200);
			    		}

		    			if (toclose) {
		    				btn.removeClass("box_closed");
		    				sb.find('.inside').slideDown(200);
		    			}
		    		});
		    	});
				
				function rs_trigger_color_picker(){					
					jQuery('.my-color-field').wpColorPicker({
						palettes:false,
						height:250,
						border:false,
						change:function() {
							drawToolBarPreview();
							try{												
								jQuery(this).closest('.placeholder-single-wrapper').find('.placeholder-checkbox').attr('checked','checked');
							} catch(e) {

							}
						}
					});


					jQuery('.alpha-color-field').each(function() {
						var a = jQuery(this);
						if (!a.hasClass("colorpicker-added")) {							
							a.addClass("colorpicker-added");
							a.alphaColorPicker({
								palettes:false,
								height:250,
								border:false								
							});
						}
					});
				}


				
				
				rs_trigger_color_picker();

				jQuery('.wp-color-result').on("click",function() {
					if (jQuery(this).hasClass("wp-picker-open"))
						jQuery(this).closest('.wp-picker-container').addClass("pickerisopen");
					else
						jQuery(this).closest('.wp-picker-container').removeClass("pickerisopen");
				});

				jQuery("body").click(function(event) {
  					jQuery('.wp-picker-container.pickerisopen').removeClass("pickerisopen");
  				})

  				// PREPARE ON/OFF BUTTON
  				jQuery('.tp-onoffbutton .withlabel').each(function() {
  					var wl = jQuery(this),
  						tpo = wl.closest('.tp-onoffbutton');
  					tpo.attr('label',wl.attr('id'));
  					tpo.addClass("withlabel");
  				})

  				jQuery('.wp-picker-container .withlabel').each(function() {
  					var wl = jQuery(this),
  						tpo = wl.closest('.wp-picker-container');
  					tpo.attr('label',wl.attr('id'));
  					tpo.addClass("withlabel");
  				});

  				//----------------------------------------------------
				// 		DRAW PREVIEW OF NAVIGATION ELEMENTS
				//----------------------------------------------------

				function convertAnytoRGBorRGBA(a,c) {
					if (a==="color" || a==="color-rgba") {						
						if (c.indexOf("rgb")>=0) {
							c = c.split('(')[1].split(")")[0];
						}
						else{							
							c = UniteAdminRev.convertHexToRGB(c);
							c = c[0]+","+c[1]+","+c[2];
						}

						if (a==="color-rgba" && c.split(",").length<4) c = c+",1";						
						
					}
					return c;
				}

				var previewNav = function(sbut,mclass,the_css,the_markup,settings) {

					var ap = jQuery('#preview-nav-wrapper .rs-arrows-preview'),
						bp = jQuery('#preview-nav-wrapper .rs-bullets-preview'),
						tabp = jQuery('#preview-nav-wrapper .rs-tabs-preview'),
						thumbp = jQuery('#preview-nav-wrapper .rs-thumbs-preview'),
						sizer = jQuery('#preview-nav-wrapper .little-sizes'),
						navpre  = jQuery('#preview-nav-wrapper');

					navpre.css({height:200});
						

					ap.html("");
					bp.html("");
					tabp.html("");
					thumbp.html("");
					
					ap.hide();
					bp.hide();
					tabp.hide();
					thumbp.hide();
					sizer.hide();

					

					var pattern = new RegExp(":hover",'g'),
						parts = the_css.split('##'),
							sfor = [],
							counter = 0,
							ph = "";
					for (var i=0;i<parts.length;i++) {
						if (counter==1) {
							ph=parts[i];
							counter=0;
							sfor.push(ph);
						} else {
							counter++;
						}
					}
					
					
					if (sbut=="arrows") {
						ap.show();
						jQuery.each(sfor,function(i,sf) {
							
							jQuery.each(settings.placeholders,function(i,o) {
								if (sf===o.handle && o["nav-type"]==="arrows") {
									var rwith=""
									if (jQuery('input[name="ph-'+mclass+'-arrows-'+o.handle+'-'+o.type+'-def"]').attr("checked")==="checked")
										rwith = jQuery('input[name="ph-'+mclass+'-arrows-'+o.handle+'-'+o.type+'"]').val();												
									 else
									 	rwith =  o.data[o.type];

									rwith = convertAnytoRGBorRGBA(o.type,rwith);									
									the_css = the_css.replace('##'+sf+'##',rwith);
								}
							});							
						});

						
					
						var t = '<style>'+the_css+'</style>';
						t = t + '<div class="'+mclass+' tparrows tp-leftarrow">'+the_markup+'</div>';
						t = t + '<div class="'+mclass+' tparrows tp-rightarrow">'+the_markup+'</div>';					
						ap.html(t);	
					
						var arh = ap.find('.tparrows').first().height()+40,
							pph = navpre.height();

						
						if (pph<arh)
							navpre.css({height:arh});

						
						
					} else 
					if (sbut=="bullets") {
						bp.show();	

						jQuery.each(sfor,function(i,sf) {
							jQuery.each(settings.placeholders,function(i,o) {
								if (sf===o.handle && o["nav-type"]==="bullets") {
									var rwith = "";
									if (jQuery('input[name="ph-'+mclass+'-bullets-'+o.handle+'-'+o.type+'-def"]').attr("checked")==="checked")
										rwith = jQuery('input[name="ph-'+mclass+'-bullets-'+o.handle+'-'+o.type+'"]').val()
									else
										rwith = o.data[o.type]

									rwith = convertAnytoRGBorRGBA(o.type,rwith);

									the_css = the_css.replace('##'+sf+'##',rwith);
									
								}
							});							
						});

						

						var t = '<style>'+the_css+'</style>';
						t = t + '<div class="'+mclass+' tp-bullets">'
						for (var i=0;i<5;i++) {
							t = t + '<div class="tp-bullet">'+the_markup+'</div>';
						}
						t= t + '</div>';
						bp.html(t);
						var b = bp.find('.tp-bullet').first(),
							bw = jQuery('#bullets_direction option:selected').attr("value")=="horizontal" ? b.outerWidth(true) : b.outerHeight(true),
							bh = jQuery('#bullets_direction option:selected').attr("value")=="vertical" ? b.outerWidth(true) : b.outerHeight(true),
							mw = 0;
						bp.find('.tp-bullet').each(function(i) {
							var e = jQuery(this);
							if (i==0) 
								setTimeout(function() {
									try{e.addClass("selected");} catch(e) {}
								},150);
							
							
							var np = i*bw + i*10;
							if (jQuery('#bullets_direction option:selected').attr("value")=="horizontal") {
								e.css({left:np+"px"});	
							} else {
								e.css({top:np+"px"});	
							}
							
							mw = mw + bw + 10;
						})
						mw = mw-10;
						if (jQuery('#bullets_direction option:selected').attr("value")=="horizontal") {
							bp.find('.tp-bullets').css({width:mw, height:bh});					
						} else {
							bp.find('.tp-bullets').css({height:mw, width:bh});					
						}

						bp.find('.tp-bullets').addClass("nav-pos-ver-"+jQuery('#bullets_align_vert').val()).addClass("nav-pos-hor-"+jQuery('#bullets_align_hor').val()).addClass("nav-dir-"+jQuery('#bullets_direction').val());

					} else
					if (sbut=="tabs") {
						tabp.show();
						jQuery.each(sfor,function(i,sf) {
							jQuery.each(settings.placeholders,function(i,o) {
								if (sf===o.handle && o["nav-type"]==="tabs") {
									var rwith = "";
									if (jQuery('input[name="ph-'+mclass+'-tabs-'+o.handle+'-'+o.type+'-def"]').attr("checked")==="checked") 
										rwith = jQuery('input[name="ph-'+mclass+'-tabs-'+o.handle+'-'+o.type+'"]').val();
									else 
										rwith = o.data[o.type];
									rwith = convertAnytoRGBorRGBA(o.type,rwith);
									the_css = the_css.replace('##'+sf+'##',rwith);
								}
							});							
						});
						
						var t = '<style>'+the_css+'</style>';
						t = t + '<div class="'+mclass+'"><div class="tp-tab">'+the_markup+'</div></div>';
						tabp.html(t);
						var s = new Object();
						s.w = 160,
						s.h = 160;
						if (settings!="" && settings!=undefined) {							
							if (settings.width!=undefined && settings.width.tabs!=undefined)
								s.w=settings.width.tabs;
							if (settings.height!=undefined && settings.height.tabs!=undefined)
								s.h=settings.height.tabs;
						}
						tabp.find('.tp-tab').each(function(){
							jQuery(this).css({width:s.w+"px",height:s.h+"px"});
						});
						var tabc = tabp.find('.tp-tab');
						tabc.addClass("nav-pos-ver-"+jQuery('#tabs_align_vert').val()).addClass("nav-pos-hor-"+jQuery('#tabs_align_hor').val()).addClass("nav-dir-"+jQuery('#tabs_direction').val());

						var arh = tabc.height()+40,
							pph = navpre.height();

						
						if (pph<arh)
							navpre.css({height:arh});

						return s;
					
					} else
					if (sbut=="thumbs") {
						thumbp.show();
						jQuery.each(sfor,function(i,sf) {
							jQuery.each(settings.placeholders,function(i,o) {
								if (sf===o.handle && o["nav-type"]==="thumbs") {
									var rwith="";
									if (jQuery('input[name="ph-'+mclass+'-thumbs-'+o.handle+'-'+o.type+'-def"]').attr("checked")==="checked") 
										rwith = jQuery('input[name="ph-'+mclass+'-thumbs-'+o.handle+'-'+o.type+'"]').val();
									else 
										rwith = o.data[o.type];
									rwith = convertAnytoRGBorRGBA(o.type,rwith);
									the_css = the_css.replace('##'+sf+'##',rwith);
								}
							});							
						});

						var t = '<style>'+the_css+'</style>';
						t = t + '<div class="'+mclass+'"><div class="tp-thumb">'+the_markup+'</div></div>';
						thumbp.html(t);
						var s = new Object();
						s.w = 160,
						s.h = 160;
						if (settings!="" && settings!=undefined) {							
							if (settings.width!=undefined && settings.width.thumbs!=undefined)
								s.w=settings.width.thumbs;
							if (settings.height!=undefined && settings.height.thumbs!=undefined)
								s.h=settings.height.thumbs;
						}
						thumbp.find('.tp-thumb').each(function(){
							jQuery(this).css({width:s.w+"px",height:s.h+"px"});			
						});

						var thumbsc = thumbp.find('.tp-thumb').parent();
						thumbsc.addClass("nav-pos-ver-"+jQuery('#thumbs_align_vert').val()).addClass("nav-pos-hor-"+jQuery('#thumbs_align_hor').val()).addClass("nav-dir-"+jQuery('#thumbs_direction').val());
						return s;
					}

				}
				
				
				fillNavStylePlaceholderOnInit();

				jQuery('.toggle-custom-navigation-style').click(function() {
					var p = jQuery(this).closest('.toggle-custom-navigation-style-wrapper'),
						c = p.find('.toggle-custom-navigation-styletarget'); 	
					
					if (c.hasClass("opened")) {
						c.removeClass("opened");
						c.slideUp(200);
					} else {
						c.slideDown(200);
						c.addClass("opened");
					}
				});
				
				
				function fillNavStylePlaceholderOnInit(){
					<?php
					$ph_types = array('navigation_arrow_style' => 'arrows', 'navigation_bullets_style' => 'bullets', 'tabs_style' => 'tabs', 'thumbnails_style' => 'thumbs');
					foreach($ph_types as $phname => $pht){

						$ph_arr_type = ZTSliderFunctions::getVal($this->arrFieldsParams, $phname, '');
						
						$ph_init = array();
						$ph_presets = array();
						foreach($arr_navigations as $nav){
							if($nav['handle'] == $ph_arr_type){ //check for settings, placeholders
								if(isset($nav['settings']) && isset($nav['settings']['placeholders'])){
									foreach($nav['settings']['placeholders'] as $placeholder){
										if(empty($placeholder)) continue;
										
										$ph_vals = array();
										$ph_vals_def = array();
										
										//$placeholder['type']
										foreach($placeholder['data'] as $k => $d){
											$ph_vals[$k] = ZTSliderFunctions::getVal($this->arrFieldsParams, 'ph-'.$ph_arr_type.'-'.$pht.'-'.$placeholder['handle'].'-'.$k, $d);											
											$ph_vals_def[$k] = ZTSliderFunctions::getVal($this->arrFieldsParams, 'ph-'.$ph_arr_type.'-'.$pht.'-'.$placeholder['handle'].'-'.$k.'-def', 'off');											
										}
										
										$ph_init[] = array('nav-type' => @$placeholder['nav-type'], 'title' => @$placeholder['title'], 'handle' => $placeholder['handle'], 'type' => $placeholder['type'], 'data' => $ph_vals, 'default' => $ph_vals_def);
									}
									if(!empty($ph_vals) && isset($nav['settings']['presets']) && !empty($nav['settings']['presets'])){
										$ph_presets[$nav['handle']] = $nav['settings']['presets'];
									}
								}
								break;
							}
						}
						
						if(!empty($ph_init)){
							?>
							var phvals = jQuery.parseJSON(<?php echo ZTSliderFunctions::jsonEncodeForClientSide($ph_init); ?>);
							var phpresets = jQuery.parseJSON(<?php echo ZTSliderFunctions::jsonEncodeForClientSide($ph_presets); ?>);
							
							//check for values
							showNavStylePlaceholder('<?php echo $pht; ?>', phvals, phpresets);
							<?php
						}
					}
					?>
				}
				
				
				function showNavStylePlaceholder(navtype, vals, phpresets){

					var cur_edit_type;
					var cur_edit = {};
					var ph_div;
					var ph_preset_sel;
					switch(navtype){
						case 'arrows':
							cur_edit_type = jQuery('#navigation_arrow_style option:selected').attr('value');
							ph_div = jQuery('.navigation_arrow_placeholder');
							ph_preset_sel = jQuery('#navigation_arrows_preset');
						break;
						case 'bullets':
							cur_edit_type = jQuery('#navigation_bullets_style option:selected').attr('value');
							ph_div = jQuery('.navigation_bullets_placeholder');
							ph_preset_sel = jQuery('#navigation_bullets_preset');
						break;
						case 'tabs':
							cur_edit_type = jQuery('#tabs_style option:selected').attr('value');
							ph_div = jQuery('.navigation_tabs_placeholder');
							ph_preset_sel = jQuery('#navigation_tabs_preset');
						break;
						case 'thumbs':
							cur_edit_type = jQuery('#thumbnails_style option:selected').attr('value');
							ph_div = jQuery('.navigation_thumbs_placeholder');
							ph_preset_sel = jQuery('#navigation_thumbs_preset');
						break;
						default:
							return false;
						break;
					}
					
					
					var current_value = ph_preset_sel.val();
					
					ph_div.html('');
					ph_preset_sel.find('option').each(function(){
						if(!jQuery(this).hasClass('never')) jQuery(this).remove();
					});
					
					
					if(vals == undefined){
						for(var key in rs_navigations){									
							if(rs_navigations[key]['handle'] == cur_edit_type){								
								cur_edit = jQuery.extend(true, {}, rs_navigations[key]);
								break;
							}
						}
					}else{
						cur_edit = {'settings': {'placeholders': vals}};
					}

					var tcnsw = ph_div.closest('.toggle-custom-navigation-style-wrapper'),
						holder_type = tcnsw.data('navtype');
					
					if (cur_edit['settings'] == undefined || cur_edit['settings']['placeholders'] == undefined) {
						tcnsw.find('.toggle-custom-navigation-style').removeClass("visible");
						return false;
					}
					
					var count = 0;
					for(var key in cur_edit['settings']['placeholders']){

						var m = cur_edit['settings']['placeholders'][key];
						
						if (holder_type==m['nav-type']) count++;

						if(m['default'] == undefined) m['default'] = {};
						
						if(jQuery.isEmptyObject(m)) continue;
						
						var deftype = (m["type"] == 'font-family') ? 'font_family' : m["type"];
						
						var isfor = 'ph-'+cur_edit_type+'-'+navtype+'-'+m['handle']+'-'+deftype,
							ph_title = (m['title'] == undefined) ? '##'+m['handle']+'##' : m['title'],
							ph_html = '<div class="placeholder-single-wrapper '+m["nav-type"]+'"><input type="checkbox" name="ph-'+cur_edit_type+'-'+navtype+'-'+m['handle']+'-'+deftype+'-def" data-isfor="'+isfor+'" class="tp-moderncheckbox placeholder-checkbox custom-preset-val triggernavstyle" data-unchecked="off" ';															
						
						ph_html+= (m['default'][deftype] == undefined || m['default'][deftype] == 'off') ? '>' : ' checked="checked">';
						ph_html+= '<span class="label placeholder-label triggernavstyle">'+ph_title+'</span>';

						switch(m['type']){
							case 'color':																
								ph_html+= '<input type="text" name="ph-'+cur_edit_type+'-'+navtype+'-'+m['handle']+'-color" class="triggernavstyle alpha-color-field custom-preset-val" id="'+isfor+'" value="'+m['data']['color']+'">';																
							break;
							case 'color-rgba':																
								ph_html+= '<input type="text" name="ph-'+cur_edit_type+'-'+navtype+'-'+m['handle']+'-color-rgba" class="triggernavstyle alpha-color-field custom-preset-val" id="'+isfor+'" value="'+m['data']['color-rgba']+'">';																
							break;
							case 'font-family':								
								ph_html+= '<select style="width:112px" name="ph-'+cur_edit_type+'-'+navtype+'-'+m['handle']+'-font_family" class="triggernavstyle custom-preset-val" id="'+isfor+'">';
								<?php
								$font_families = $operations->getArrFontFamilys();
								foreach($font_families as $handle => $name){
									if($name['label'] == 'Dont Show Me') continue;
									?>
									ph_html+= '<option value="<?php echo $name['label']; ?>"';
									if(m['data']['font_family'] == '<?php echo $name['label']; ?>'){
										ph_html+= ' selected="selected"';
									}
									ph_html+= '><?php echo $name['label']; ?></option>';
									<?php
								}
								?>
								ph_html+= '</select>';
								
							break;
							case 'custom':													
								ph_html+= '<input type="text" name="ph-'+cur_edit_type+'-'+navtype+'-'+m['handle']+'-custom" value="'+m['data']['custom']+'" class="triggernavstyle custom-preset-val" id="'+isfor+'">';																
							break;
							
						}

						ph_html+= '</div><div class="clear"></div>';								
						if (holder_type==m['nav-type']) ph_div.prepend(ph_html);
					}
					
					
					if(phpresets !== undefined){
						//add presets in the box
						for(var key in phpresets){
							if(key == cur_edit_type){
								for(var kkey in phpresets[key]){
									if(phpresets[key][kkey]['type'] == navtype)
										ph_preset_sel.append('<option value="'+phpresets[key][kkey]['handle']+'">'+phpresets[key][kkey]['name']+'</option>');
								}
							}
						}
						
						//select the correct preset in select box
						ph_preset_sel.find('option[value="'+ph_preset_sel.data('startvalue')+'"]').attr('selected', true);
					
					}else{
						//phpfullresets
						//fill the field, as we have changed the nav type, and set the select to default
						for(var key in phpfullresets){
							if(key == cur_edit_type){
								if(phpfullresets[key][navtype] !== undefined){
									for(var kkey in phpfullresets[key][navtype]){
										ph_preset_sel.append('<option value="'+phpfullresets[key][navtype][kkey]['handle']+'">'+phpfullresets[key][navtype][kkey]['name']+'</option>');
									}
								}
							}
						}
						
						if(ph_preset_sel.find('option[value="'+current_value+'"]').length > 0){
							ph_preset_sel.find('option[value="'+current_value+'"]').attr('selected', true);
						}else{
							ph_preset_sel.find('option[value="default"]').attr('selected', true);
						}
						
						if(fullresetsstay !== false){
							if(ph_preset_sel.find('option[value="'+fullresetsstay+'"]').length > 0){
								ph_preset_sel.find('option[value="'+fullresetsstay+'"]').attr('selected', true);
							}
						}
						
						
						ph_preset_sel.change();
					}
					fullresetsstay = false;
					
					
					if (count>0) {
						tcnsw.find('.toggle-custom-navigation-style').addClass("visible");
						ph_div.append('<span class="overwrite-arrow"></span><span class="placeholder-description"><?php echo  JText::_('Check to use Custom'); ?></span>');
					} else {
						tcnsw.find('.toggle-custom-navigation-style').removeClass("visible");
					}
					ph_div.append('<div class="tp-clear"></div><div class="save-navigation-style-as-preset"><?php echo  JText::_("Save as Preset");?></div><div class="delete-navigation-style-as-preset"><?php echo  JText::_("Delete Preset");?></div>');
					
					rs_trigger_color_picker();
				}
				
				
				/**
				 * If changed, set parent to Custom!
				 **/
				jQuery('body').on('change', '.custom-preset-val', function(){
					var navtype = jQuery(this).closest('.toggle-custom-navigation-style-wrapper').data('navtype');
					
					switch(navtype){
						case 'arrows':
							jQuery('#navigation_arrows_preset option[value="custom"]').attr('selected', true);
						break;
						case 'bullets':
							jQuery('#navigation_bullets_preset option[value="custom"]').attr('selected', true);
						break;
						case 'thumbs':
							jQuery('#navigation_thumbs_preset option[value="custom"]').attr('selected', true);
						break;
						case 'tabs':
							jQuery('#navigation_tabs_preset option[value="custom"]').attr('selected', true);
						break;
					}
				});
				
				
				jQuery('body').on('change', '#navigation_arrows_preset, #navigation_bullets_preset, #navigation_tabs_preset, #navigation_thumbs_preset', function(){
					var myv = jQuery(this).val();
					
					var type = 'arrows';
					var sel_value = '';
					switch(jQuery(this).attr('id')){
						case 'navigation_arrows_preset':
							type = 'arrows';
							sel_value = jQuery('#navigation_arrow_style option:selected').val();
						break;
						case 'navigation_bullets_preset':
							type = 'bullets';
							sel_value = jQuery('#navigation_bullets_style option:selected').val();
						break;
						case 'navigation_tabs_preset':
							type = 'tabs';
							sel_value = jQuery('#tabs_style option:selected').val();
						break;
						case 'navigation_thumbs_preset':
							type = 'thumbs';
							sel_value = jQuery('#thumbnails_style option:selected').val();
						break;
					}
					
					if(myv == 'default' || myv !== 'custom'){
						//uncheck all checkboxes, set the values to default values
						for(var key in rs_navigations){
							if(rs_navigations[key]['handle'] == sel_value){
								if(rs_navigations[key]['settings'] !== undefined && rs_navigations[key]['settings']['placeholders'] !== undefined){
									for(var kkey in rs_navigations[key]['settings']['placeholders']){
										var m = rs_navigations[key]['settings']['placeholders'][kkey];
										switch(m['type']){
											case 'color':
												jQuery('input[name="ph-'+sel_value+'-'+type+'-'+m['handle']+'-color"]').val(m['data']['color']);
												jQuery('input[name="ph-'+sel_value+'-'+type+'-'+m['handle']+'-color-def"]').attr('checked', false);
												//trigger color change											
												//jQuery('input[name="ph-'+sel_value+'-'+type+'-'+m['handle']+'-color"]').alphaColorPicker('color', m['data']['color']);
											break;
											case 'color-rgba':
												jQuery('input[name="ph-'+sel_value+'-'+type+'-'+m['handle']+'-color"]').val(m['data']['color-rgba']);
												jQuery('input[name="ph-'+sel_value+'-'+type+'-'+m['handle']+'-color-def"]').attr('checked', false);
												//trigger color change											
												//jQuery('input[name="ph-'+sel_value+'-'+type+'-'+m['handle']+'-color"]').alphaColorPicker('color', m['data']['color-rgba']);
											break;
											case 'font_family':
												jQuery('select name=["ph-'+sel_value+'-'+type+'-'+m['handle']+'-font_family"] option[value="'+m['data']['font_family']+'"]').attr('selected', true);
												jQuery('input[name="ph-'+sel_value+'-'+type+'-'+m['handle']+'-font-family-def"]').attr('checked', false);
											break;
											case 'custom':
												jQuery('input[name="ph-'+sel_value+'-'+type+'-'+m['handle']+'-custom"]').val(m['data']['custom']);
												jQuery('input[name="ph-'+sel_value+'-'+type+'-'+m['handle']+'-custom-def"]').attr('checked', false);
											break;
										}
									}
								}
								break;
							}
						}
					}
					
					if(myv == 'custom'){
						//leave all as it is
					}else{
						//default values were set before, now set the values of the preset
						if(phpfullresets !== null && phpfullresets[sel_value] !== undefined && phpfullresets[sel_value][type] !== undefined && phpfullresets[sel_value][type][myv] !== undefined){
							if(phpfullresets[sel_value][type][myv]['values'] !== undefined){
								for(var key in phpfullresets[sel_value][type][myv]['values']){
									jQuery('#'+key).val(phpfullresets[sel_value][type][myv]['values'][key]);
									jQuery('input[name="'+key+'-def"]').attr('checked', true);
								}
							}
						}
					}
				});
				
				
				jQuery('body').on('click', '.save-navigation-style-as-preset', function(){
					//what type am I
					var ce = jQuery(this);
					var nav_type = ce.closest('.toggle-custom-navigation-style-wrapper').data('navtype');
					
					//set a name
					jQuery('.rs-dialog-save-nav-preset').dialog({
						modal:true,
						resizable:false,
						minWidth:400,
						minHeight:300,
						closeOnEscape:true,
						buttons:{
							'<?php echo  JText::_('Save As'); ?>':function(){
								
								var preset_name = jQuery('input[name="preset-name"]').val();
								var preset_handle = UniteAdminRev.sanitize_input(jQuery('input[name="preset-name"]').val());
								var preset_navigation = nav_type;
								var preset_values = {};
								var preset_nav_handle = '';
								
								switch(nav_type){
									case 'arrows':
										var ph_class = '.navigation_arrow_placeholder';
										preset_nav_handle = jQuery('#navigation_arrow_style option:selected').val();
									break;
									case 'bullets':
										var ph_class = '.navigation_bullets_placeholder';
										preset_nav_handle = jQuery('#navigation_bullets_style option:selected').val();
									break;
									case 'thumbs':
										var ph_class = '.navigation_thumbs_placeholder';
										preset_nav_handle = jQuery('#thumbnails_style option:selected').val();
									break;
									case 'tabs':
										var ph_class = '.navigation_tabs_placeholder';
										preset_nav_handle = jQuery('#tabs_style option:selected').val();
									break;
									default:
										return false;
									break;
								}
								
								var overwrite = false;
								//check if preset handle is already existing!
								if(typeof(phpfullresets[preset_nav_handle]) !== 'undefined' && typeof(phpfullresets[preset_nav_handle][nav_type]) !== 'undefined'){
									if(typeof(phpfullresets[preset_nav_handle][nav_type][preset_handle]) !== 'undefined'){
										//handle already exists, change it
										if(!confirm('<?php echo  JText::_('Handle already exists, overwrite settings for this preset?'); ?>')){
											return false;
										}
										overwrite = true;
									}
								}
								
								//get values where checkbox is selected
								jQuery(ph_class+' input[type="checkbox"]').each(function(){
									if(jQuery(this).is(':checked')){
										var jqrobj = jQuery('#'+jQuery(this).data('isfor'));
										
										preset_values[jqrobj.attr('name')] = jqrobj.val();
									}
								});
								
								if(!jQuery.isEmptyObject(preset_values)){
									
									var data = {
										navigation: preset_nav_handle,
										name: preset_name,
										handle: preset_handle,
										type: preset_navigation,
										do_overwrite: overwrite,
										values: preset_values,
										sliderid:jQuery("#sliderid").val()
									}
									
									UniteAdminRev.ajaxRequest('slider.createnavigationpreset', data, function(response){
										reload_navigation_preset_fields(response.navs, preset_handle);
										
										jQuery('.rs-dialog-save-nav-preset').dialog('close');
									});
								}else{
									alert('<?php echo  JText::_('No fields are checked, please check at least one field to save a preset'); ?>');
								}
								
							}
						}
					});
					
					
					//if exists, overwrite existing
				});
				
				jQuery('body').on('click', '.delete-navigation-style-as-preset', function(){
					//check if we are default or custom
					//if not, ask to really delete
					
					var nav_type = jQuery(this).closest('.toggle-custom-navigation-style-wrapper').data('navtype');
					
					var cur_preset = jQuery('#navigation_'+nav_type+'_preset option:selected').val();
					
					if(cur_preset !== 'default' && cur_preset !== 'custom' && cur_preset !== undefined){ //default and custom can not be deleted
						if(confirm('<?php echo  JText::_('Delete this Navigation Preset?'); ?>')){
							var style_handle = '';
							switch(nav_type){
								case 'arrows':
									style_handle = jQuery('#navigation_arrow_style option:selected').val();
								break;
								case 'bullets':
									style_handle = jQuery('#navigation_bullets_style option:selected').val();
								break;
								case 'tabs':
									style_handle = jQuery('#tabs_style option:selected').val();
								break;
								case 'thumbs':
									style_handle = jQuery('#thumbnails_style option:selected').val();
								break;
							}
							
							//delete that entry
							var data = {
								style_handle: style_handle,
								handle: cur_preset,
								type: nav_type,
								sliderid:jQuery("#sliderid").val()
							}
							
							
							UniteAdminRev.ajaxRequest('slider.deletenavigationpreset', data, function(response){
								
								reload_navigation_preset_fields(response.navs);
								
							});
							
						}
					}
				});
				
				
				function reload_navigation_preset_fields(navs, stay_at_current){
					
					//reload select boxes!
					for(var key in navs){
						if(navs[key]['settings'] !== null && navs[key]['settings'] !== undefined && navs[key]['settings']['presets'] !== undefined){
							phpfullresets[navs[key]['handle']] = {};
							
							for(var kkey in navs[key]['settings']['presets']){ //push values into phpfullresets
								var m = navs[key]['settings']['presets'][kkey];
								
								if(phpfullresets[navs[key]['handle']][m['type']] == undefined) phpfullresets[navs[key]['handle']][m['type']] = {};
								
								phpfullresets[navs[key]['handle']][m['type']][m['handle']] = m;
							}
						}
					}
					
					//select the new created preset
					//and select the things on other elements as they have changed to default
					
					//now reset all fields with the new phpfullresets
					if(stay_at_current !== undefined){
						fullresetsstay = stay_at_current;
					}
					showNavStylePlaceholder('arrows');
					showNavStylePlaceholder('bullets');
					showNavStylePlaceholder('tabs');
					showNavStylePlaceholder('thumbs');
					
				}
				

				function changeNavStyle(navtype) {
					var cur_edit = {},
						cur_edit_type,
						navtype,
						nav_id,
						mclass="";

					if (navtype == "arrows")					
						cur_edit_type=jQuery('#navigation_arrow_style option:selected').attr('value');
					else 
					if (navtype == 'bullets')
						cur_edit_type=jQuery('#navigation_bullets_style option:selected').attr('value');
					else
					if (navtype == 'tabs')
						cur_edit_type=jQuery('#tabs_style option:selected').attr('value');
					else 
					if (navtype == 'thumbs')
						cur_edit_type=jQuery('#thumbnails_style option:selected').attr('value');
					
					for(var key in rs_navigations){									
						if(rs_navigations[key]['handle'] == cur_edit_type){
							cur_edit = jQuery.extend(true, {}, rs_navigations[key]);
							break;
						}
					}
					
					var the_css = (typeof(cur_edit['css']) !== 'undefined' && cur_edit['css'] !== null && typeof(cur_edit['css'][navtype]) !== 'undefined') ? cur_edit['css'][navtype] : '',
						the_markup = (typeof(cur_edit['markup']) !== 'undefined' && cur_edit['markup'] !== null && typeof(cur_edit['markup'][navtype]) !== 'undefined') ? cur_edit['markup'][navtype] : "",
						settings = (typeof(cur_edit['settings']) !== 'undefined' && cur_edit['settings'] !== null) ? cur_edit['settings'] : "";
					
					if (cur_edit["name"]==undefined) return false;
					var mclass = UniteAdminRev.sanitize_input(cur_edit["name"].toLowerCase());
					

					if(cur_edit['css'] == null) return false;
					if(cur_edit['markup'] == null) return false;
					

					return previewNav(navtype,mclass,the_css,the_markup,settings);
					
				}

				function callChangeNavStyle(e) {
  					prev.hide();
					navpre.show();	
					punchgs.TweenLite.set(navpre,{autoAlpha:1});
					
					if (e.closest('#nav_arrows_subs').length>0) {
						navtype = 'arrows';
						title.html("Arrow Styling");
					}
					else 
					if (e.closest('#nav_bullets_subs').length>0) {
						navtype = 'bullets';	
						title.html("Bullet Styling");
					}
					else 
					if (e.closest('#nav_tabs_subs').length>0){
						navtype = 'tabs';
						title.html("Tabs Styling");
					}
					else 
					if (e.closest('#nav_thumbnails_subs').length>0){
						navtype = 'thumbs';
						title.html("Thumbnails Styling");
					}
						
					var s = changeNavStyle(navtype,jQuery(this));	
					if (s!=undefined)
						cont.html("Suggested Size:"+s.w+" x "+s.h+"px");
					else cont.hide();

  				}
  				
  					
  				jQuery('body').on('mouseenter','.label, .withlabel, .triggernavstyle, #form_toolbar',function() {  						
					drawToolBarPreview();

					var lbl 	= jQuery(this).hasClass("withlabel") ? (jQuery(this).attr('id')===undefined ? jQuery("#label_"+jQuery(this).attr("label")) : jQuery("#label_"+jQuery(this).attr("id"))) : jQuery(this),
						ft      = jQuery('#form_slider_params').offset().top;
						tb      = jQuery('#form_toolbar'),
						title   = tb.find('.toolbar-title'),
						cont    = tb.find('.toolbar-content'),
						med     = tb.find('.toolbar-media'),
						prev    = tb.find('.toolbar-sliderpreview'),
						shads   = tb.find('.toolbar-shadows'),
						img     = tb.find('.toolbar-slider-image'),
						exti 	= tb.find('.toolbar-extended-info'),
						navpre  = jQuery('#preview-nav-wrapper');

					
					if (jQuery(this).attr('id')!=="form_toolbar") {
						if (!jQuery(this).hasClass("triggernavstyle")) {
							navpre.css({height:200});
							title.html(lbl.html());
							cont.html(lbl.attr('origtitle'));
						}
						
						
						/*	if (lbl.attr('extendedinfo')!=undefined)
								exti.html(lbl.attr('extendedinfo'));

							/*if (lbl.attr('origmedia')===undefined) {
								prev.slideUp(150);
								shads.slideUp(150);
							}*/

						if (lbl.attr('origmedia')=="show" || lbl.attr('origmedia')=="showbg") {
							prev.slideDown(150);
							shads.slideDown(150);
						}

						if (lbl.attr('origmedia')=="showbg")
							img.addClass('shownowbg');
						else
						img.removeClass('shownowbg');

						var topp = (lbl.offset().top-ft-14),
							hh = tb.outerHeight(),
							so = jQuery(document).scrollTop(),
							foff = jQuery('#form_slider_params').offset().top,
							wh = jQuery(window).height(),
							diff = (so+wh-foff) - (topp+hh);

							if (diff<0) topp = topp + diff;


						if (lbl.hasClass("triggernavstyle")) {						
							callChangeNavStyle(jQuery(this));
						} else {
							cont.show();
							prev.show();
							navpre.hide();
						}
						punchgs.TweenLite.to(tb,0.5,{autoAlpha:1,right:"100%",top:topp,ease:punchgs.Power3.easeOut,overwrite:"all"});
					} else {
						punchgs.TweenLite.to(tb,0.5,{autoAlpha:1,overwrite:"all"});
					}
					
				});

				jQuery('body').on('mouseleave','.label, .withlabel, .triggernavstyle, #form_toolbar',function() {					
					 punchgs.TweenLite.to(jQuery('#form_toolbar'),0.2,{autoAlpha:0,ease:punchgs.Power3.easeOut,delay:0.2});
				});

				

				jQuery('body').on('click','.placeholder-single-wrapper input.custom-preset-val',function() {	
					if (!jQuery(this).is(":checkbox"))				
						jQuery(this).closest('.placeholder-single-wrapper').find('.placeholder-checkbox').attr('checked','checked');
					return true;
				});

				jQuery('body').on('keyup','.placeholder-single-wrapper input',function() {										
					callChangeNavStyle(jQuery(this));						
				});

  				jQuery('body').on('mousemove','.toggle-custom-navigation-styletarget',function() {  					
					callChangeNavStyle(jQuery(this));
					punchgs.TweenLite.to(jQuery('#form_toolbar'),0.2,{autoAlpha:1,ease:punchgs.Power3.easeOut,overwrite:"auto"});
  				});
				

				jQuery('#navigation_arrow_style, #navigation_bullets_style, #tabs_style, #thumbnails_style').on("change",function() {
					var e = jQuery(this),
						tb      = jQuery('#form_toolbar'),
						title   = tb.find('.toolbar-title'),
						cont    = tb.find('.toolbar-content');

					if (e.closest('#nav_arrows_subs').length>0) 
						navtype = 'arrows';
					else 
					if (e.closest('#nav_bullets_subs').length>0) 
						navtype = 'bullets';	
					else 
					if (e.closest('#nav_tabs_subs').length>0)
						navtype = 'tabs';
					else 
					if (e.closest('#nav_thumbnails_subs').length>0)
						navtype = 'thumbs';
						
					var s = changeNavStyle(navtype,jQuery(this));
					
					showNavStylePlaceholder(navtype);
					
					if (s!=undefined)
						cont.html("<?php echo  JText::_('Suggested Size:'); ?>"+s.w+" x "+s.h+"px");
					else cont.hide();
				});
				var rs_navigations = jQuery.parseJSON(<?php echo ZTSliderFunctions::jsonEncodeForClientSide($arr_navigations); ?>);								
				
				var googlef_template_container = UniteLayersRev.wptemplate( "rs-preset-googlefont" );
				
				jQuery('#add_new_google_font').click(function(){
					var content = googlef_template_container({'value':''});
					jQuery('#rs-google-fonts').append(content);
				});
				
				jQuery('body').on('click', '.rs-google-remove-field', function(){
					jQuery(this).parent().remove();
				});
				
				<?php
				$google_font = ZTSliderFunctions::getVal($this->arrFieldsParams, 'google_font', array());
				if(!empty($google_font) && is_array($google_font)){
					foreach($google_font as $gfont){
						?>
						jQuery('#rs-google-fonts').append(googlef_template_container({'value':'<?php echo $gfont; ?>'}));
						<?php
					}
				}
				?>
				/*
				
				var data = {};
				data['value'] = key;
				data['name'] = revslider_presets[key]['settings']['name'];
				data['type'] = revslider_presets[key]['settings']['preset'];
				data['img'] = (typeof(revslider_presets[key]['settings']['image']) !== 'undefined') ? revslider_presets[key]['settings']['image'] : '';
				data['class'] = (typeof(revslider_presets[key]['settings']['class']) !== 'undefined') ? revslider_presets[key]['settings']['class'] : '';
				data['custom'] = (typeof(revslider_presets[key]['settings']['custom']) !== 'undefined' && revslider_presets[key]['settings']['custom'] == true) ? true : false;
				
				var content = googlef_template_container(data);
				
				jQuery('#rs-add-new-settings-preset').before(content);
				*/
				
				jQuery(".button-image-select-bg-img").click(function(){
					UniteAdminRev.openAddImageDialog("Choose Image",function(urlImage, imageID){
						//update input:
						jQuery("#background_image").val(urlImage);
					});
				});
				jQuery(".button-image-select-background-img").click(function(){
					UniteAdminRev.openAddImageDialog("Choose Image",function(urlImage, imageID){
						//update input:
						jQuery("#show_alternate_image").val(urlImage);
					});
				});
				
			});
		</script>
	</div>

</div>




<!-- PRESET SAVING DIALOG -->
<div id="dialog-rs-add-new-setting-presets" title="<?php echo  JText::_('Save Settings as Preset');?>" style="display:none;">
	<div class="settings_wrapper unite_settings_wide">
		<p><label><?php echo  JText::_('Preset Name');?></label> <input type="text" name="rs-preset-name" /></p>
		<p><label><?php echo  JText::_('Select Image');?></label> <input type="button" value="<?php echo  JText::_('Select');?>" name="rs-button-select-img" /></p>
		<input type="hidden" name="rs-preset-image-id" value="" />
		<div id="rs-preset-img-wrapper">

		</div>
	</div>
</div>

<?php
$exampleID = '"slider1"';
?>
<!--
THE INFO ABOUT EMBEDING OF THE SLIDER
-->
<div class="rs-dialog-embed-slider" style="display: none;">
	<div class="revyellow" style="background: none repeat scroll 0% 0% #F1C40F; left:0px;top:36px;position:absolute;height:224px;padding:20px 10px;"><i style="color:#fff;font-size:25px" class="revicon-arrows-ccw"></i></div>
	<div style="margin:5px 0px; padding-left: 55px;">
		<div style="font-size:14px;margin-bottom:10px;"><strong><?php echo  JText::_("Standard Embeding",'revslider'); ?></strong></div>
		<?php echo  JText::_("For the",'revslider'); ?> <b><?php echo  JText::_("pages or posts editor",'revslider'); ?></b> <?php echo  JText::_("insert the shortcode:",'revslider'); ?> <code class="rs-example-alias-1"></code>
		<div style="width:100%;height:10px"></div>
		<?php echo  JText::_("From the",'revslider'); ?> <b><?php echo  JText::_("widgets panel",'revslider'); ?></b> <?php echo  JText::_("drag the \"Revolution Slider\" widget to the desired sidebar",'revslider'); ?>
		<div style="width:100%;height:25px"></div>
	</div>
</div>
<script>
 jQuery('#advanced-emeding').click(function() {
	jQuery('#advanced-accord').toggle(200);
 })
</script>


<div class="rs-dialog-save-nav-preset" title="<?php echo  JText::_('Add Navigation Preset'); ?>" style="display: none;">
	<p><label><?php echo  JText::_('Preset Name:'); ?></label><input type="text" name="preset-name" value="" /></p>
</div>


<script type="text/html" id="tmpl-rs-preset-container">
	<span class="rs-preset-selector rs-preset-entry {{ data['type'] }} {{ data['class'] }} " id="rs-preset-{{ data['key'] }}">
		<span class="rs-preset-image"<# if( data['img'] !== '' ){ #> style="background-image: url({{ data['img'] }});"<# } #>>
		<# if( data['custom'] == true ){ #><span class="rev-update-preset"><i class="revicon-pencil-1"></i></span><span class="rev-remove-preset"><i class="revicon-cancel"></i></span><# } #>
		</span>
		<span class="rs-preset-label">{{ data['name'] }}</span>
	</span>
</script>
<script type="text/html" id="tmpl-rs-preset-googlefont">
	<div>
		<span class="label" style="min-width:100px" origtitle="<?php echo  JText::_("Google Font String");?>"><?php echo  JText::_("Font")?>:</span>
		<input type="text" style="width:180px" name="google_font[]" value="{{ data['value'] }}">
		<a class="button-primary revred rs-google-remove-field" original-title=""><i class="revicon-trash"></i></a>
		<div class="tp-clearfix"></div>
	</div>
</script>