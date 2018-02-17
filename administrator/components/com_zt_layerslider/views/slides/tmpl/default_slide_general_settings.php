<?php
defined('_JEXEC') or die;
$slide_general_addon = array();
//JHtml::_('behavior.modal');
?>
<style>
	#form_general_settings input[type=email], #form_general_settings input[type=number], #form_general_settings input[type=password], #form_general_settings input[type=search], #form_general_settings input[type=tel], #form_general_settings input[type=text], #form_general_settings input[type=url], #viewWrapper input[type=email], #viewWrapper input[type=number], #viewWrapper input[type=password], #viewWrapper input[type=search], #viewWrapper input[type=tel], #viewWrapper input[type=text], #viewWrapper input[type=url], input[type=text].adb-input, input[type=text].ads-input {height:auto;}
	#slide-general-settings-content select{width:214px;}
	.vidsrcchanger-div {display:none!important;}
</style>
<script>
	function jInsertFieldValue(val,id) {
		jQuery('#'+id).val(val);
		if (id == 'add_layer_image') {
			var image = new Image();
			image.src = '<?php echo JUri::root() ;?>'+val;
			var size = image.size;
			var imgobj = {imgurl: image.src, imgid: 0, imgwidth: image.width, imgheight: image.height};
			UniteLayersRev.addLayerImage(imgobj,par);
		} else if(id=='slide_thumb') {
			ZtSliderAdmin.updateThumbnailChange('<?php echo JUri::root() ;?>'+val);
		} else if(id == 'bulk_image_import') {
			val = val.split(',');
			var data = {sliderid:<?php echo $this->sliderID ?>,obj:val};
			UniteAdminRev.ajaxRequest("slider.addbulkslide", data);

		}else if(id == 'input_video_preview') {
			var image = '<?php echo JUri::root() ;?>'+val;
			jQuery("#input_video_preview").val(image);
			//update preview image:
			jQuery("#video-thumbnail-preview").css({backgroundImage:'url('+image+')','background-size':'200px 150px'})
		} else {
			ZtSliderAdmin.updateImageChange('<?php echo JUri::root() ;?>'+val);
		}

	}
	function jModalClose() {
		jQuery.fancybox.close();
	}
	jQuery(document).ready(function(){function r(r){var i;return r=r.replace(/ /g,""),r.match(/rgba\(\d+\,\d+\,\d+\,([^\)]+)\)/)?(i=100*parseFloat(r.match(/rgba\(\d+\,\d+\,\d+\,([^\)]+)\)/)[1]).toFixed(2),i=parseInt(i)):i=100,i}function i(r,i,e,t){var n,o,c;n=i.data("a8cIris"),o=i.data("wpWpColorPicker"),n._color._alpha=r,c=n._color.toString(),i.val(c),o.toggler.css({"background-color":c}),t&&a(r,e),i.wpColorPicker("color",c)}function a(r,i){i.slider("value",r),i.find(".ui-slider-handle").text(r.toString())}Color.prototype.toString=function(r){if("no-alpha"==r)return this.toCSS("rgba","1").replace(/\s+/g,"");if(1>this._alpha)return this.toCSS("rgba",this._alpha).replace(/\s+/g,"");var i=parseInt(this._color,10).toString(16);if(this.error)return"";if(i.length<6)for(var a=6-i.length-1;a>=0;a--)i="0"+i;return"#"+i},jQuery.fn.alphaColorPicker=function(){return this.each(function(){var e,t,n,o,c,l,s,d,u,p,f;e=jQuery(this),e.wrap('<div class="alpha-color-picker-wrap"></div>'),n=e.attr("data-palette")||"true",o=e.attr("data-show-opacity")||"true",c=e.attr("data-default-color")||"",l=-1!==n.indexOf("|")?n.split("|"):"false"==n?!1:!0,t=e.val().replace(/\s+/g,""),""==t&&(t=c),s={change:function(i,a){var t,n,o,l;t=e.attr("data-customize-setting-link"),n=e.wpColorPicker("color"),c==n&&(o=r(n),u.find(".ui-slider-handle").text(o)),"undefined"!=typeof wp.customize&&wp.customize(t,function(r){r.set(n)}),l=d.find(".transparency"),l.css("background-color",a.color.toString("no-alpha"))},palettes:l},e.wpColorPicker(s),d=e.parents(".wp-picker-container:first"),jQuery('<div class="alpha-color-picker-container"><div class="min-click-zone click-zone"></div><div class="max-click-zone click-zone"></div><div class="alpha-slider"></div><div class="transparency"></div></div>').appendTo(d.find(".wp-picker-holder")),u=d.find(".alpha-slider"),p=r(t),f={create:function(r,i){var a=jQuery(this).slider("value");jQuery(this).find(".ui-slider-handle").text(a),jQuery(this).siblings(".transparency ").css("background-color",t)},value:p,range:"max",step:1,min:0,max:100,animate:300},u.slider(f),"true"==o&&u.find(".ui-slider-handle").addClass("show-opacity"),d.find(".min-click-zone").on("click",function(){i(0,e,u,!0)}),d.find(".max-click-zone").on("click",function(){i(100,e,u,!0)}),d.find(".iris-palette").on("click",function(){var i,t;i=jQuery(this).css("background-color"),t=r(i),a(t,u),100!=t&&(i=i.replace(/[^,]+(?=\))/,(t/100).toFixed(2))),e.wpColorPicker("color",i)}),d.find(".button.wp-picker-default").on("click",function(){var i=r(c);a(i,u)}),e.on("input",function(){var i=jQuery(this).val(),e=r(i);a(e,u)}),u.slider().on("slide",function(r,a){var t=parseFloat(a.value)/100;i(t,e,u,!1),jQuery(this).find(".ui-slider-handle").text(a.value)})})}});
</script>


<div class="editor_buttons_wrapper  postbox unite-postbox" style="max-width:100% !important; min-width:1152 !important;">
	<div class="box-closed tp-accordion" style="border-bottom:5px solid #ddd;">
		<ul class="rs-slide-settings-tabs">
			<?php
			if(!$this->slide->isStaticSlide()){
				?>
				<li data-content="#slide-main-image-settings-content" class="selected"><i style="height:45px" class="rs-mini-layer-icon eg-icon-picture-1 rs-toolbar-icon"></i><span><?php echo  JText::_("Main Background",'revslider'); ?></span></li>					
				<?php
			}
			?>				
				<li class="<?php echo ($this->slide->isStaticSlide()) ? ' selected' : ''; ?>" data-content="#slide-general-settings-content"><i style="height:45px" class="rs-mini-layer-icon rs-icon-chooser-2 rs-toolbar-icon"></i><?php echo  JText::_("General Settings",'revslider'); ?></li>
			<?php
			if(!$this->slide->isStaticSlide()){
				?>
				<li data-content="#slide-thumbnail-settings-content"><i style="height:45px" class="rs-mini-layer-icon eg-icon-flickr-1 rs-toolbar-icon"></i><?php echo  JText::_("Thumbnail",'revslider'); ?></li>
				<li data-content="#slide-animation-settings-content" id="slide-animation-settings-content-tab"><i style="height:45px" class="rs-mini-layer-icon rs-icon-chooser-3 rs-toolbar-icon"></i><?php echo  JText::_("Slide Animation",'revslider'); ?></li>
				<li data-content="#slide-seo-settings-content"><i style="height:45px" class="rs-mini-layer-icon rs-icon-advanced rs-toolbar-icon"></i><?php echo  JText::_("Link & Seo",'revslider'); ?></li>
				<li data-content="#slide-info-settings-content"><i style="height:45px; font-size:16px;" class="rs-mini-layer-icon eg-icon-info-circled rs-toolbar-icon"></i><?php echo  JText::_("Slide Info",'revslider'); ?></li>						
				<li id="main-menu-nav-settings-li" data-content="#slide-nav-settings-content"><i style="height:45px; font-size:16px;" class="rs-mini-layer-icon eg-icon-magic rs-toolbar-icon"></i><?php echo  JText::_("Nav. Overwrite",'revslider'); ?></li>
				<?php
			}
			?>				
			<?php if(!empty($slide_general_addon)){ ?>
				<li data-content="#slide-addon-wrapper"><i style="height:45px; font-size:16px;" class="rs-mini-layer-icon eg-icon-plus-circled rs-toolbar-icon"></i><?php echo  JText::_("AddOns",'revslider'); ?></li>
			<?php } ?>
		</ul>

		<div style="clear:both"></div>
		<script type="text/javascript">
			jQuery('document').ready(function() {
				jQuery('.rs-slide-settings-tabs li').click(function() {
					var tw = jQuery('.rs-slide-settings-tabs .selected'),
						tn = jQuery(this);
					jQuery(tw.data('content')).hide(0);
					tw.removeClass("selected");
					tn.addClass("selected");
					jQuery(tn.data('content')).show(0);
				});
			});
		</script>
	</div>
	<div style="padding:15px">
		<form name="form_slide_params" id="form_slide_params" class="slide-main-settings-form">
			<?php 
			if(!$this->slide->isStaticSlide()){
				?>
				<div id="slide-main-image-settings-content" class="slide-main-settings-form">

					<ul class="rs-layer-main-image-tabs" style="display:inline-block; ">
						<li data-content="#mainbg-sub-source" class="selected"><?php echo  JText::_('Source', 'revslider'); ?></li>
						<li class="mainbg-sub-settings-selector" data-content="#mainbg-sub-setting"><?php echo  JText::_('Source Settings', 'revslider'); ?></li>					
						<li class="mainbg-sub-parallax-selector" data-content="#mainbg-sub-parallax"><?php echo  JText::_('Parallax / 3D', 'revslider'); ?></li>
						<li class="mainbg-sub-kenburns-selector" data-content="#mainbg-sub-kenburns"><?php echo  JText::_('Ken Burns', 'revslider'); ?></li>
					</ul>

					<div class="tp-clearfix"></div>

					<script type="text/javascript">
						jQuery('document').ready(function() {
							jQuery('.rs-layer-main-image-tabs li').click(function() {
								var tw = jQuery('.rs-layer-main-image-tabs .selected'),
									tn = jQuery(this);
								jQuery(tw.data('content')).hide(0);
								tw.removeClass("selected");
								tn.addClass("selected");
								jQuery(tn.data('content')).show(0);
							});
						});
					</script>


					<!-- SLIDE MAIN IMAGE -->
					<span id="mainbg-sub-source" style="display:block">
						<div style="float:none; clear:both; margin-bottom: 10px;"></div>
						<input type="hidden" name="rs-gallery-type" value="<?php echo trim($this->slider_type); ?>" />
						<span class="diblock bg-settings-block">
							<!-- IMAGE FROM MEDIAGALLERY -->												
							<?php
							if($this->slider_type == 'posts' || $this->slider_type == 'specific_posts'){
								?>
								<label><?php echo  JText::_("Featured Image",'revslider'); ?></label>
								<input type="radio" name="background_type" value="image" class="bgsrcchanger" data-callid="tp-bgimagewpsrc" data-imgsettings="on" data-bgtype="image" id="radio_back_image" <?php ZTSliderFunctions::checked($this->bgType, 'image'); ?>>
								
								<?php
								/*
								<div class="tp-clearfix"></div>
								<label><?php echo  JText::_("Meta Image",'revslider'); ?></label>
								<input type="radio" name="background_type" value="meta" class="bgsrcchanger" data-callid="tp-bgimagewpsrc" data-imgsettings="on" data-bgtype="meta" <?php ZTSliderFunctions::checked($this->bgType, 'meta'); ?>>
								<span id="" class="" style="margin-left:20px;">
									<span style="margin-right: 10px"><?php echo  JText::_('Meta Handle', 'revslider'); ?></span>
									<input type="text" id="meta_handle" name="meta_handle" value="<?php echo $this->meta_handle; ?>">
								</span>*/ ?>
								<?php
							}elseif($this->slider_type !== 'gallery'){
								?>
								<label><?php echo  JText::_("Stream Image",'revslider'); ?></label>
								<input type="radio" name="background_type" value="image" class="bgsrcchanger" data-callid="tp-bgimagewpsrc" data-imgsettings="on" data-bgtype="image" id="radio_back_image" <?php ZTSliderFunctions::checked($this->bgType, 'image'); ?>>
								<?php
								if($this->slider_type == 'vimeo' || $this->slider_type == 'youtube' || $this->slider_type == 'instagram' || $this->slider_type == 'twitter'){
									?>
									<div class="tp-clearfix"></div>
									<label><?php echo  JText::_("Stream Video",'revslider'); ?></label>
									<input type="radio" name="background_type" value="stream<?php echo $this->slider_type; ?>" class="bgsrcchanger" data-callid="tp-bgimagewpsrc" data-imgsettings="on" data-bgtype="stream<?php echo $this->slider_type; ?>" <?php ZTSliderFunctions::checked($this->bgType, 'stream'.$this->slider_type); ?>>
									<span id="streamvideo_cover" class="streamvideo_cover" style="display:none;margin-left:20px;">
										<span style="margin-right: 10px"><?php echo  JText::_("Use Cover",'revslider'); ?></span>
										<input type="checkbox" class="tp-moderncheckbox" id="stream_do_cover" name="stream_do_cover" data-unchecked="off" <?php ZTSliderFunctions::checked($this->stream_do_cover, 'on'); ?>>
									</span>
									
									<div class="tp-clearfix"></div>
									<label><?php echo  JText::_("Stream Video + Image",'revslider'); ?></label>
									<input type="radio" name="background_type" value="stream<?php echo $this->slider_type; ?>both" class="bgsrcchanger" data-callid="tp-bgimagewpsrc" data-imgsettings="on" data-bgtype="stream<?php echo $this->slider_type; ?>both" <?php ZTSliderFunctions::checked($this->bgType, 'stream'.$this->slider_type.'both'); ?>>
									<span id="streamvideo_cover_both" class="streamvideo_cover_both" style="display:none;margin-left:20px;">
										<span style="margin-right: 10px"><?php echo  JText::_("Use Cover",'revslider'); ?></span>
										<input type="checkbox" class="tp-moderncheckbox" id="stream_do_cover_both" name="stream_do_cover_both" data-unchecked="off" <?php ZTSliderFunctions::checked($this->stream_do_cover_both, 'on'); ?>>
									</span>
									<?php
								}
							}else{
								?>
								<label ><?php echo  JText::_("Main / Background Image",'revslider'); ?></label>
								<input type="radio" name="background_type" value="image" class="bgsrcchanger" data-callid="tp-bgimagewpsrc" data-imgsettings="on" data-bgtype="image" id="radio_back_image" <?php ZTSliderFunctions::checked($this->bgType, 'image'); ?>>
								
								<?php
							}
							?>
							<!-- THE BG IMAGE CHANGED DIV -->
							<span id="tp-bgimagewpsrc" class="bgsrcchanger-div" style="display:none;margin-left:20px;">
								<script>
									jQuery(document).ready(function() {
										jQuery("#button_change_image").fancybox({width: '70%',height: '70%',fitToView:true,autoSize:true});
										jQuery("#slide_thumb_button").fancybox({width: '70%',height: '70%',fitToView:true,autoSize:true});
									});
								</script>
								<a href="<?php echo JUri::root() ?>/administrator/index.php?option=com_media&amp;tmpl=component&amp;view=images&amp;asset=com_zt_layerslider&amp;folder=layerslider&amp;fieldid=layer_image" id="button_change_image" class="button-primary revblue fancybox fancybox.iframe"><?php echo JText::_("Change Image"); ?></a>
								<input type="hidden" name="layer_image" id="layer_image" value="">
							</span>
							
							<div class="tp-clearfix"></div>
							
							<!-- IMAGE FROM EXTERNAL -->
							<label><?php echo  JText::_("External URL",'revslider'); ?></label>
							<input type="radio" name="background_type" value="external" data-callid="tp-bgimageextsrc" data-imgsettings="on" class="bgsrcchanger" data-bgtype="external" id="radio_back_external" <?php ZTSliderFunctions::checked($this->bgType, 'external'); ?>>

							<!-- THE BG IMAGE FROM EXTERNAL SOURCE -->
							<span id="tp-bgimageextsrc" class="bgsrcchanger-div" style="display:none;margin-left:20px;">
								<input type="text" name="bg_external" id="slide_bg_external" value="<?php echo $this->slideBGExternal?>" <?php echo ($this->bgType != 'external') ? ' class="disabled"' : ''; ?>>
								<a href="javascript:void(0)" id="button_change_external" class="button-primary revblue" ><?php echo  JText::_("Get External",'revslider'); ?></a>

							</span>
							
							<div class="tp-clearfix"></div>
							
							<!-- TRANSPARENT BACKGROUND -->
							<label><?php echo  JText::_("Transparent",'revslider'); ?></label>
							<input type="radio" name="background_type" value="trans" data-callid="" class="bgsrcchanger" data-bgtype="trans" id="radio_back_trans" <?php ZTSliderFunctions::checked($this->bgType, 'trans'); ?>>
							<div class="tp-clearfix"></div>
							
							<!-- COLORED BACKGROUND -->
							<label><?php echo  JText::_("Solid Colored",'revslider'); ?></label>
							<input type="radio" name="background_type" value="solid"  data-callid="tp-bgcolorsrc" class="bgsrcchanger" data-bgtype="solid" id="radio_back_solid" <?php ZTSliderFunctions::checked($this->bgType, 'solid'); ?>>
							
							<!-- THE COLOR SELECTOR -->
							<span id="tp-bgcolorsrc"  class="bgsrcchanger-div"  style="display:none;margin-left:20px;">
								<input type="text" name="bg_color" id="slide_bg_color" class="my-color-field" value="<?php echo $this->slideBGColor; ?>">
							</span>
							<div class="tp-clearfix"></div>

							<!-- THE YOUTUBE SELECTOR -->
							<label><?php echo  JText::_("YouTube Video",'revslider'); ?></label>
							<input type="radio" name="background_type" value="youtube"  data-callid="tp-bgyoutubesrc" class="bgsrcchanger" data-bgtype="youtube" id="radio_back_youtube" <?php ZTSliderFunctions::checked($this->bgType, 'youtube'); ?>>
							<div class="tp-clearfix"></div>
							
							<!-- THE BG IMAGE FROM YOUTUBE SOURCE -->
							<span id="tp-bgyoutubesrc" class="bgsrcchanger-div" style="display:none; margin-left:20px;">
								<label style="min-width:180px"><?php echo  JText::_("ID:",'revslider'); ?></label>
								<input type="text" name="slide_bg_youtube" id="slide_bg_youtube" value="<?php echo $this->slideBGYoutube; ?>" <?php echo ($this->bgType != 'youtube') ? ' class="disabled"' : ''; ?>>							
								<?php echo  JText::_('example: T8--OggjJKQ', 'revslider'); ?>
								<div class="tp-clearfix"></div>
								<label style="min-width:180px"><?php echo  JText::_("Cover Image:",'revslider'); ?></label>
								<span id="youtube-image-picker"></span>
							</span>
							<div class="tp-clearfix"></div>
							
							<!-- THE VIMEO SELECTOR -->
							<label><?php echo  JText::_("Vimeo Video",'revslider'); ?></label>
							<input type="radio" name="background_type" value="vimeo"  data-callid="tp-bgvimeosrc" class="bgsrcchanger" data-bgtype="vimeo" id="radio_back_vimeo" <?php ZTSliderFunctions::checked($this->bgType, 'vimeo'); ?>>
							<div class="tp-clearfix"></div>

							<!-- THE BG IMAGE FROM VIMEO SOURCE -->
							<span id="tp-bgvimeosrc" class="bgsrcchanger-div" style="display:none; margin-left:20px;">
								<label style="min-width:180px"><?php echo  JText::_("ID:",'revslider'); ?></label>
								<input type="text" name="slide_bg_vimeo" id="slide_bg_vimeo" value="<?php echo $this->slideBGVimeo; ?>" <?php echo ($this->bgType != 'vimeo') ? ' class="disabled"' : ''; ?>>							
								<?php echo  JText::_('example: 30300114', 'revslider'); ?>
								<div class="tp-clearfix"></div>
								<label style="min-width:180px"><?php echo  JText::_("Cover Image:",'revslider'); ?></label>
								<span id="vimeo-image-picker"></span>
							</span>
							<div class="tp-clearfix"></div>

							<!-- THE HTML5 SELECTOR -->
							<label><?php echo  JText::_("HTML5 Video",'revslider'); ?></label>
							<input type="radio" name="background_type" value="html5"  data-callid="tp-bghtmlvideo" class="bgsrcchanger" data-bgtype="html5" id="radio_back_htmlvideo" <?php ZTSliderFunctions::checked($this->bgType, 'html5'); ?>>
							<div class="tp-clearfix"></div>
							<!-- THE BG IMAGE FROM HTML5 SOURCE -->
							<span id="tp-bghtmlvideo" class="bgsrcchanger-div" style="display:none; margin-left:20px;">
								
								<label style="min-width:180px"><?php echo  JText::_('MPEG:', 'revslider'); ?></label>
								<input type="text" name="slide_bg_html_mpeg" id="slide_bg_html_mpeg" value="<?php echo $this->slideBGhtmlmpeg; ?>" <?php echo ($this->bgType != 'html5') ? ' class="ajaxcheck disabled"' : 'class="ajaxcheck"'; ?>>
								<span class="vidsrcchanger-div" style="margin-left:20px;">
									<a href="javascript:void(0)" data-inptarget="slide_bg_html_mpeg" class="button_change_video button-primary revblue" ><?php echo  JText::_('Change Video', 'revslider'); ?></a>
								</span>
								<div class="tp-clearfix"></div>
								<label style="min-width:180px"><?php echo  JText::_('WEBM:', 'revslider'); ?></label>
								<input type="text" name="slide_bg_html_webm" id="slide_bg_html_webm" value="<?php echo $this->slideBGhtmlwebm; ?>" <?php echo ($this->bgType != 'html5') ? ' class="ajaxcheck disabled"' : 'class="ajaxcheck"'; ?>>
								<span class="vidsrcchanger-div" style="margin-left:20px;">
									<a href="javascript:void(0)" data-inptarget="slide_bg_html_webm" class="button_change_video button-primary revblue" ><?php echo  JText::_('Change Video', 'revslider'); ?></a>
								</span>
								<div class="tp-clearfix"></div>
								<label style="min-width:180px"><?php echo  JText::_('OGV:', 'revslider'); ?></label>
								<input type="text" name="slide_bg_html_ogv" id="slide_bg_html_ogv" value="<?php echo $this->slideBGhtmlogv; ?>" <?php echo ($this->bgType != 'html5') ? ' class="ajaxcheck disabled"' : 'class="ajaxcheck"'; ?>>
								<span class="vidsrcchanger-div" style="margin-left:20px;">
									<a href="javascript:void(0)" data-inptarget="slide_bg_html_ogv" class="button_change_video button-primary revblue" ><?php echo  JText::_('Change Video', 'revslider'); ?></a>
								</span>
								<div class="tp-clearfix"></div>
								<label style="min-width:180px"><?php echo  JText::_('Cover Image:', 'revslider'); ?></label>
								<span id="html5video-image-picker"></span>
							</span>
						</span>
					</span>
					<div id="mainbg-sub-setting" style="display:none">
						<div style="float:none; clear:both; margin-bottom: 10px;"></div>
						<div class="rs-img-source-url">						
							<label><?php echo  JText::_('Source Info:', 'revslider'); ?></label>
							<span class="text-selectable" id="the_image_source_url" style="margin-right:20px"></span>
							<span class="description"><?php echo  JText::_('Read Only ! Image can be changed from "Source Tab"','revslider'); ?></span>
						</div>

						<div class="rs-img-source-size">
							
							<label><?php echo  JText::_('Image Source Size:', 'revslider'); ?></label>
							<span style="margin-right:20px">
								<select name="image_source_type">
									<?php
									foreach($this->img_sizes as $imghandle => $imgSize){
										$sel = ($this->bg_image_size  == $imghandle) ? ' selected="selected"' : '';
										echo '<option value="'.trim($imghandle).'"'.$sel.'>'.$imgSize.'</option>';
									}
									?>
								</select>
							</span>
						</div>
						
						<div id="tp-bgimagesettings" class="bgsrcchanger-div" style="display:none;">
							<!-- ALT -->
							<div>
								<?php $alt_option = ZTSliderFunctions::getVal($this->slideParams, 'alt_option', 'file_name'); ?>
								<label><?php echo  JText::_("Alt:",'revslider'); ?></label>
								<select id="alt_option" name="alt_option">
									<option value="file_name" <?php ZTSliderFunctions::selected($alt_option, 'file_name'); ?>><?php echo  JText::_('From Filename', 'revslider'); ?></option>
									<option value="custom" <?php ZTSliderFunctions::selected($alt_option, 'custom'); ?>><?php echo  JText::_('Custom', 'revslider'); ?></option>
								</select>
								<?php $alt_attr = ZTSliderFunctions::getVal($this->slideParams, 'alt_attr', ''); ?>
								<input style="<?php echo ($alt_option !== 'custom') ? 'display:none;' : ''; ?>" type="text" id="alt_attr" name="alt_attr" value="<?php echo $alt_attr; ?>">
							</div>
							<div class="ext_setting" style="display: none;">
								<label><?php echo  JText::_('Width:', 'revslider')?></label>
								<input type="text" name="ext_width" value="<?php echo $this->ext_width; ?>" />
							</div>
							<div class="ext_setting" style="display: none;">
								<label><?php echo  JText::_('Height:', 'revslider')?></label>
								<input type="text" name="ext_height" value="<?php echo $this->ext_height; ?>" />
							</div>
						
							<!-- TITLE -->
							<div>
								<?php $title_option = ZTSliderFunctions::getVal($this->slideParams, 'title_option', 'file_name'); ?>
								<label><?php echo  JText::_('Title:','revslider'); ?></label>
								<select id="title_option" name="title_option">
									<option value="file_name" <?php ZTSliderFunctions::selected($title_option, 'file_name'); ?>><?php echo  JText::_('From Filename', 'revslider'); ?></option>
									<option value="custom" <?php ZTSliderFunctions::selected($title_option, 'custom'); ?>><?php echo  JText::_('Custom', 'revslider'); ?></option>
								</select>
								<?php $title_attr = ZTSliderFunctions::getVal($this->slideParams, 'title_attr', ''); ?>
								<input style="<?php echo ($title_option !== 'custom') ? 'display:none;' : ''; ?>" type="text" id="title_attr" name="title_attr" value="<?php echo $title_attr; ?>">
							</div>
						</div>					
						
						<div id="video-settings" style="display: block;">
							<div>
								<label for="video_force_cover" class="video-label"><?php echo  JText::_('Force Cover:', 'revslider'); ?></label>
								<input type="checkbox" class="tp-moderncheckbox" id="video_force_cover" name="video_force_cover" data-unchecked="off" <?php ZTSliderFunctions::checked($this->video_force_cover, 'on'); ?>>
							</div>
							<span id="video_dotted_overlay_wrap">
								<label for="video_dotted_overlay">
									<?php echo  JText::_('Dotted Overlay:', 'revslider'); ?>
								</label>				
								<select id="video_dotted_overlay" name="video_dotted_overlay" style="width:100px">
									<option <?php ZTSliderFunctions::selected($this->video_dotted_overlay, 'none'); ?> value="none"><?php echo  JText::_('none', 'revslider'); ?></option>
									<option <?php ZTSliderFunctions::selected($this->video_dotted_overlay, 'twoxtwo'); ?> value="twoxtwo"><?php echo  JText::_('2 x 2 Black', 'revslider'); ?></option>
									<option <?php ZTSliderFunctions::selected($this->video_dotted_overlay, 'twoxtwowhite'); ?> value="twoxtwowhite"><?php echo  JText::_('2 x 2 White', 'revslider'); ?></option>
									<option <?php ZTSliderFunctions::selected($this->video_dotted_overlay, 'threexthree'); ?> value="threexthree"><?php echo  JText::_('3 x 3 Black', 'revslider'); ?></option>
									<option <?php ZTSliderFunctions::selected($this->video_dotted_overlay, 'threexthreewhite'); ?> value="threexthreewhite"><?php echo  JText::_('3 x 3 White', 'revslider'); ?></option>
								</select>
								<div style="clear: both;"></div>
							</span>
							<label for="video_ratio">
								<?php echo  JText::_("Aspect Ratio:", 'revslider'); ?>
							</label>				
							<select id="video_ratio" name="video_ratio" style="width:100px">
								<option <?php ZTSliderFunctions::selected($this->video_ratio, '16:9');?> value="16:9"><?php echo  JText::_('16:9','revslider'); ?></option>
								<option <?php ZTSliderFunctions::selected($this->video_ratio, '4:3');?> value="4:3"><?php echo  JText::_('4:3','revslider'); ?></option>
							</select>
							<div style="clear: both;"></div>
							<div>
								<label for="video_ratio">
									<?php echo  JText::_("Start At:", 'revslider'); ?>
								</label>				
								<input type="text" value="<?php echo $this->video_start_at; ?>" name="video_start_at"> <?php echo  JText::_('For Example: 00:17', 'revslider'); ?>
								<div style="clear: both;"></div>
							</div>
							<div>
								<label for="video_ratio">
									<?php echo  JText::_("End At:", 'revslider'); ?>
								</label>				
								<input type="text" value="<?php echo $this->video_end_at; ?>" name="video_end_at"> <?php echo  JText::_('For Example: 02:17', 'revslider'); ?>
								<div style="clear: both;"></div>
							</div>
							<div>
								<label for="video_loop"><?php echo  JText::_('Loop Video:', 'revslider'); ?></label>
								<select id="video_loop" name="video_loop" style="width: 200px;">
									<option <?php ZTSliderFunctions::selected($this->video_loop, 'none');?> value="none"><?php echo  JText::_('Disable', 'revslider'); ?></option>
									<option <?php ZTSliderFunctions::selected($this->video_loop, 'loop');?> value="loop"><?php echo  JText::_('Loop, Slide is paused', 'revslider'); ?></option>
									<option <?php ZTSliderFunctions::selected($this->video_loop, 'loopandnoslidestop');?> value="loopandnoslidestop"><?php echo  JText::_('Loop, Slide does not stop', 'revslider'); ?></option>
								</select>
							</div>
							
							<div>	
								<label for="video_nextslide"><?php echo  JText::_('Next Slide On End:', 'revslider'); ?></label>
								<input type="checkbox" class="tp-moderncheckbox" id="video_nextslide" name="video_nextslide" data-unchecked="off" <?php ZTSliderFunctions::checked($this->video_nextslide, 'on'); ?>>
							</div>
							<div>
								<label for="video_force_rewind"><?php echo  JText::_('Rewind at Slide Start:', 'revslider'); ?></label>
								<input type="checkbox" class="tp-moderncheckbox" id="video_force_rewind" name="video_force_rewind" data-unchecked="off" <?php ZTSliderFunctions::checked($this->video_force_rewind, 'on'); ?>>
							</div>
							
							<div>	
								<label for="video_mute"><?php echo  JText::_('Mute Video:', 'revslider'); ?></label>
								<input type="checkbox" class="tp-moderncheckbox" id="video_mute" name="video_mute" data-unchecked="off" <?php ZTSliderFunctions::checked($this->video_mute, 'on'); ?>>
							</div>
							
							<div class="vid-rev-vimeo-youtube video_volume_wrapper">
								<label for="video_volume"><?php echo  JText::_('Video Volume:', 'revslider'); ?></label>
								<input type="text" id="video_volume" name="video_volume" <?php echo trim($this->video_volume); ?>>
							</div>

							<span id="vid-rev-youtube-options">
								<div>
									<label for="video_speed"><?php echo  JText::_('Video Speed:', 'revslider'); ?></label>
									<select id="video_speed" name="video_speed" style="width:75px">
										<option <?php ZTSliderFunctions::selected($this->video_speed, '0.25');?> value="0.25"><?php echo  JText::_('0.25', 'revslider'); ?></option>
										<option <?php ZTSliderFunctions::selected($this->video_speed, '0.50');?> value="0.50"><?php echo  JText::_('0.50', 'revslider'); ?></option>
										<option <?php ZTSliderFunctions::selected($this->video_speed, '1');?> value="1"><?php echo  JText::_('1', 'revslider'); ?></option>
										<option <?php ZTSliderFunctions::selected($this->video_speed, '1.5');?> value="1.5"><?php echo  JText::_('1.5', 'revslider'); ?></option>
										<option <?php ZTSliderFunctions::selected($this->video_speed, '2');?> value="2"><?php echo  JText::_('2', 'revslider'); ?></option>
									</select>
								</div>
								<div>
									<label><?php echo  JText::_('Arguments YouTube:', 'revslider'); ?></label>
									<input type="text" id="video_arguments" name="video_arguments" style="width:350px;" value="<?php echo trim($this->video_arguments); ?>">
								</div>
							</span>
							<div id="vid-rev-vimeo-options">
								<label><?php echo  JText::_('Arguments Vimeo:', 'revslider'); ?></label>
								<input type="text" id="video_arguments_vim" name="video_arguments_vim" style="width:350px;" value="<?php echo trim($this->video_arguments_vim); ?>">
							</div>
						</div>
						
						<div id="bg-setting-wrap">
							<div>
								<label for="slide_bg_fit"><?php echo  JText::_('Background Fit:', 'revslider'); ?></label>
								<select name="bg_fit" id="slide_bg_fit" style="margin-right:20px">
									<option value="cover"<?php ZTSliderFunctions::selected($this->bgFit, 'cover'); ?>>cover</option>
									<option value="contain"<?php ZTSliderFunctions::selected($this->bgFit, 'contain'); ?>>contain</option>
									<option value="percentage"<?php ZTSliderFunctions::selected($this->bgFit, 'percentage'); ?>>(%, %)</option>
									<option value="normal"<?php ZTSliderFunctions::selected($this->bgFit, 'normal'); ?>>normal</option>
								</select>
								<input type="text" name="bg_fit_x" style="min-width:54px;width:54px; <?php if($this->bgFit != 'percentage') echo 'display: none; '; ?> width:60px;margin-right:10px" value="<?php echo $this->bgFitX; ?>" />
								<input type="text" name="bg_fit_y" style="min-width:54px;width:54px;  <?php if($this->bgFit != 'percentage') echo 'display: none; '; ?> width:60px;margin-right:10px"  value="<?php echo $this->bgFitY; ?>" />
							</div>
							<div>
								<label for="slide_bg_position" id="bg-position-lbl"><?php echo  JText::_('Background Position:', 'revslider'); ?></label>
								<span id="bg-start-position-wrapper">
									<select name="bg_position" id="slide_bg_position">
										<option value="center top"<?php ZTSliderFunctions::selected($this->bgPosition, 'center top'); ?>>center top</option>
										<option value="center right"<?php ZTSliderFunctions::selected($this->bgPosition, 'center right'); ?>>center right</option>
										<option value="center bottom"<?php ZTSliderFunctions::selected($this->bgPosition, 'center bottom'); ?>>center bottom</option>
										<option value="center center"<?php ZTSliderFunctions::selected($this->bgPosition, 'center center'); ?>>center center</option>
										<option value="left top"<?php ZTSliderFunctions::selected($this->bgPosition, 'left top'); ?>>left top</option>
										<option value="left center"<?php ZTSliderFunctions::selected($this->bgPosition, 'left center'); ?>>left center</option>
										<option value="left bottom"<?php ZTSliderFunctions::selected($this->bgPosition, 'left bottom'); ?>>left bottom</option>
										<option value="right top"<?php ZTSliderFunctions::selected($this->bgPosition, 'right top'); ?>>right top</option>
										<option value="right center"<?php ZTSliderFunctions::selected($this->bgPosition, 'right center'); ?>>right center</option>
										<option value="right bottom"<?php ZTSliderFunctions::selected($this->bgPosition, 'right bottom'); ?>>right bottom</option>
										<option value="percentage"<?php ZTSliderFunctions::selected($this->bgPosition, 'percentage'); ?>>(x%, y%)</option>
									</select>
									<input type="text" name="bg_position_x" style="min-width:54px;width:54px; <?php if($this->bgPosition != 'percentage') echo 'display: none;'; ?>width:60px;margin-right:10px" value="<?php echo $this->bgPositionX; ?>" />
									<input type="text" name="bg_position_y" style="min-width:54px;width:54px; <?php if($this->bgPosition != 'percentage') echo 'display: none;'; ?>width:60px;margin-right:10px" value="<?php echo $this->bgPositionY; ?>" />
								</span>
							</div>

							<div>
								<label><?php echo  JText::_("Background Repeat:",'revslider')?></label>
								<span>
									<select name="bg_repeat" id="slide_bg_repeat" style="margin-right:20px">
										<option value="no-repeat"<?php ZTSliderFunctions::selected($this->bgRepeat, 'no-repeat'); ?>>no-repeat</option>
										<option value="repeat"<?php ZTSliderFunctions::selected($this->bgRepeat, 'repeat'); ?>>repeat</option>
										<option value="repeat-x"<?php ZTSliderFunctions::selected($this->bgRepeat, 'repeat-x'); ?>>repeat-x</option>
										<option value="repeat-y"<?php ZTSliderFunctions::selected($this->bgRepeat, 'repeat-y'); ?>>repeat-y</option>
									</select>
								</span>
							</div>
						</div>
						
					</div>

					<span id="mainbg-sub-parallax" style="display:none">
						<p>
							<?php 
							if ($this->use_parallax=="off") {
								echo '<i style="color:#c0392b">';
								echo JText::_("Parallax Feature in Slider Settings is deactivated, parallax will be ignored.",'revslider');
								echo '</i>';
							} else {
							
									if ($this->parallaxisddd=="off") {
								?>
								<label><?php echo  JText::_("Parallax Level:",'revslider'); ?></label>
								<select name="slide_parallax_level" id="slide_parallax_level">
									<option value="-" <?php ZTSliderFunctions::selected($this->slide_parallax_level, '-'); ?>><?php echo  JText::_('No Parallax', 'revslider'); ?></option>
									<option value="1" <?php ZTSliderFunctions::selected($this->slide_parallax_level, '1'); ?>>1 - (<?php echo $this->parallax_level[0]; ?>%)</option>
									<option value="2" <?php ZTSliderFunctions::selected($this->slide_parallax_level, '2'); ?>>2 - (<?php echo $this->parallax_level[1]; ?>%)</option>
									<option value="3" <?php ZTSliderFunctions::selected($this->slide_parallax_level, '3'); ?>>3 - (<?php echo $this->parallax_level[2]; ?>%)</option>
									<option value="4" <?php ZTSliderFunctions::selected($this->slide_parallax_level, '4'); ?>>4 - (<?php echo $this->parallax_level[3]; ?>%)</option>
									<option value="5" <?php ZTSliderFunctions::selected($this->slide_parallax_level, '5'); ?>>5 - (<?php echo $this->parallax_level[4]; ?>%)</option>
									<option value="6" <?php ZTSliderFunctions::selected($this->slide_parallax_level, '6'); ?>>6 - (<?php echo $this->parallax_level[5]; ?>%)</option>
									<option value="7" <?php ZTSliderFunctions::selected($this->slide_parallax_level, '7'); ?>>7 - (<?php echo $this->parallax_level[6]; ?>%)</option>
									<option value="8" <?php ZTSliderFunctions::selected($this->slide_parallax_level, '8'); ?>>8 - (<?php echo $this->parallax_level[7]; ?>%)</option>
									<option value="9" <?php ZTSliderFunctions::selected($this->slide_parallax_level, '9'); ?>>9 - (<?php echo $this->parallax_level[8]; ?>%)</option>
									<option value="10" <?php ZTSliderFunctions::selected($this->slide_parallax_level, '10'); ?>>10 - (<?php echo $this->parallax_level[9]; ?>%)</option>
									<option value="11" <?php ZTSliderFunctions::selected($this->slide_parallax_level, '11'); ?>>11 - (<?php echo $this->parallax_level[10]; ?>%)</option>
									<option value="12" <?php ZTSliderFunctions::selected($this->slide_parallax_level, '12'); ?>>12 - (<?php echo $this->parallax_level[11]; ?>%)</option>
									<option value="13" <?php ZTSliderFunctions::selected($this->slide_parallax_level, '13'); ?>>13 - (<?php echo $this->parallax_level[12]; ?>%)</option>
									<option value="14" <?php ZTSliderFunctions::selected($this->slide_parallax_level, '14'); ?>>14 - (<?php echo $this->parallax_level[13]; ?>%)</option>
									<option value="15" <?php ZTSliderFunctions::selected($this->slide_parallax_level, '15'); ?>>15 - (<?php echo $this->parallax_level[14]; ?>%)</option>							
								</select>
								<?php } else {
									if ($parallaxbgfreeze=="off") {
								?>							
									<label><?php echo  JText::_("Selected 3D Depth:",'revslider'); ?></label>
									<input style="min-width:54px;width:54px" type="text" disabled value="<?php echo $this->parallax_level[15];?>%" />							
									<span><i><?php echo  JText::_('3D Parallax is Enabled via Slider Settings !', 'revslider'); ?></i></span>
								<?php
									} else {								
										?>
											<label><?php echo  JText::_("Background 3D is Disabled",'revslider'); ?></label>									
											<span style="display: inline-block;vertical-align: middle;line-height:32px"><i><?php echo  JText::_('To Enable 3D Parallax for Background please change the Option "BG 3D Disabled" to "OFF" via the Slider Settings !', 'revslider'); ?></i></span>
										<?php
									}
								}
							}
							?>
						</p>
						
					</span>

					<span id="mainbg-sub-kenburns" style="display:none">
						<p>
							<label><?php echo  JText::_('Ken Burns / Pan Zoom:', 'revslider'); ?></label>
							<input type="checkbox" class="tp-moderncheckbox withlabel" id="kenburn_effect" name="kenburn_effect" data-unchecked="off" <?php ZTSliderFunctions::checked($this->kenburn_effect, 'on'); ?>>
						</p>
						<span id="kenburn_wrapper" <?php echo ($this->kenburn_effect == 'off') ? 'style="display: none;"' : ''; ?>>
							<p>
								<label><?php echo  JText::_('Scale: (in %):', 'revslider'); ?></label>
								<label style="min-width:40px"><?php echo  JText::_('From', 'revslider'); ?></label>
								<input style="min-width:54px;width:54px" type="text" name="kb_start_fit" value="<?php echo intval($this->kb_start_fit); ?>" />
								<label style="min-width:20px"><?php echo  JText::_('To', 'revslider')?></label>
								<input style="min-width:54px;width:54px" type="text" name="kb_end_fit" value="<?php echo intval($this->kb_end_fit); ?>" />
							</p>
							
							<p>
								<label><?php echo  JText::_('Horizontal Offsets:', 'revslider')?></label>
								<label style="min-width:40px"><?php echo  JText::_('From', 'revslider'); ?></label>							
								<input style="min-width:54px;width:54px" type="text" name="kb_start_offset_x" value="<?php echo $this->kbStartOffsetX; ?>" />
								<label style="min-width:20px"><?php echo  JText::_('To', 'revslider')?></label>
								<input style="min-width:54px;width:54px" type="text" name="kb_end_offset_x" value="<?php echo $this->kbEndOffsetX; ?>" />
								<span><i><?php echo  JText::_('Use Negative and Positive Values to offset from the Center !', 'revslider'); ?></i></span>
							</p>

							<p>
								<label><?php echo  JText::_('Vertical Offsets:', 'revslider')?></label>		
								<label style="min-width:40px"><?php echo  JText::_('From', 'revslider'); ?></label>												
								<input style="min-width:54px;width:54px" type="text" name="kb_start_offset_y" value="<?php echo $this->kbStartOffsetY; ?>" />
								<label style="min-width:20px"><?php echo  JText::_('To', 'revslider')?></label>
								<input style="min-width:54px;width:54px" type="text" name="kb_end_offset_y" value="<?php echo $this->kbEndOffsetY; ?>" />
								<span><i><?php echo  JText::_('Use Negative and Positive Values to offset from the Center !', 'revslider'); ?></i></span>
							</p>
							
							<p>
								<label><?php echo  JText::_('Rotation:', 'revslider')?></label>		
								<label style="min-width:40px"><?php echo  JText::_('From', 'revslider'); ?></label>												
								<input style="min-width:54px;width:54px" type="text" name="kb_start_rotate" value="<?php echo $this->kbStartRotate; ?>" />
								<label style="min-width:20px"><?php echo  JText::_('To', 'revslider')?></label>
								<input style="min-width:54px;width:54px" type="text" name="kb_end_rotate" value="<?php echo $this->kbEndRotate; ?>" />
							</p>
							
							<p>
								<label><?php echo  JText::_('Easing:', 'revslider'); ?></label>
								<select name="kb_easing">
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Linear.easeNone'); ?> value="Linear.easeNone">Linear.easeNone</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Power0.easeIn'); ?> value="Power0.easeIn">Power0.easeIn  (linear)</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Power0.easeInOut'); ?> value="Power0.easeInOut">Power0.easeInOut  (linear)</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Power0.easeOut'); ?> value="Power0.easeOut">Power0.easeOut  (linear)</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Power1.easeIn'); ?> value="Power1.easeIn">Power1.easeIn</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Power1.easeInOut'); ?> value="Power1.easeInOut">Power1.easeInOut</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Power1.easeOut'); ?> value="Power1.easeOut">Power1.easeOut</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Power2.easeIn'); ?> value="Power2.easeIn">Power2.easeIn</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Power2.easeInOut'); ?> value="Power2.easeInOut">Power2.easeInOut</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Power2.easeOut'); ?> value="Power2.easeOut">Power2.easeOut</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Power3.easeIn'); ?> value="Power3.easeIn">Power3.easeIn</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Power3.easeInOut'); ?> value="Power3.easeInOut">Power3.easeInOut</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Power3.easeOut'); ?> value="Power3.easeOut">Power3.easeOut</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Power4.easeIn'); ?> value="Power4.easeIn">Power4.easeIn</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Power4.easeInOut'); ?> value="Power4.easeInOut">Power4.easeInOut</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Power4.easeOut'); ?> value="Power4.easeOut">Power4.easeOut</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Back.easeIn'); ?> value="Back.easeIn">Back.easeIn</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Back.easeInOut'); ?> value="Back.easeInOut">Back.easeInOut</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Back.easeOut'); ?> value="Back.easeOut">Back.easeOut</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Bounce.easeIn'); ?> value="Bounce.easeIn">Bounce.easeIn</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Bounce.easeInOut'); ?> value="Bounce.easeInOut">Bounce.easeInOut</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Bounce.easeOut'); ?> value="Bounce.easeOut">Bounce.easeOut</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Circ.easeIn'); ?> value="Circ.easeIn">Circ.easeIn</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Circ.easeInOut'); ?> value="Circ.easeInOut">Circ.easeInOut</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Circ.easeOut'); ?> value="Circ.easeOut">Circ.easeOut</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Elastic.easeIn'); ?> value="Elastic.easeIn">Elastic.easeIn</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Elastic.easeInOut'); ?> value="Elastic.easeInOut">Elastic.easeInOut</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Elastic.easeOut'); ?> value="Elastic.easeOut">Elastic.easeOut</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Expo.easeIn'); ?> value="Expo.easeIn">Expo.easeIn</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Expo.easeInOut'); ?> value="Expo.easeInOut">Expo.easeInOut</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Expo.easeOut'); ?> value="Expo.easeOut">Expo.easeOut</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Sine.easeIn'); ?> value="Sine.easeIn">Sine.easeIn</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Sine.easeInOut'); ?> value="Sine.easeInOut">Sine.easeInOut</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'Sine.easeOut'); ?> value="Sine.easeOut">Sine.easeOut</option>
									<option <?php ZTSliderFunctions::selected($this->kb_easing, 'SlowMo.ease'); ?> value="SlowMo.ease">SlowMo.ease</option>
								</select>
							</p>
							<p>
								<label><?php echo  JText::_('Duration (in ms):', 'revslider')?></label>
								<input type="text" name="kb_duration" value="<?php echo intval($this->kb_duration); ?>" />
							</p>
						</span>
					</span>
					
					<input type="hidden" id="image_url" name="image_url" value="<?php echo $this->imageUrl; ?>" />
					<input type="hidden" id="image_id" name="image_id" value="<?php echo $this->imageID; ?>" />
				</div>
			<?php
			}
			?>	
				<div id="slide-general-settings-content" style="<?php echo (!$this->slide->isStaticSlide()) ? ' display:none' : ''; ?>">
					<?php 
					if(!$this->slide->isStaticSlide()){
					?>
						<!-- SLIDE TITLE -->
						<p style="display:none">
							<?php $title = ZTSliderFunctions::getVal($this->slideParams, 'title','Slide'); ?>
							<label><?php echo  JText::_("Slide Title",'revslider'); ?></label>
							<input type="text" class="medium" id="title" disabled="disabled" name="title" value="<?php echo $title; ?>">
							<span class="description"><?php echo  JText::_("The title of the slide, will be shown in the slides list.",'revslider'); ?></span>
						</p>

						<!-- SLIDE DELAY -->
						<p>
							<?php $delay = ZTSliderFunctions::getVal($this->slideParams, 'delay',''); ?>
							<label><?php echo  JText::_('Slide "Delay":','revslider'); ?></label>
							<input type="text" class="small-text" id="delay" name="delay" value="<?php echo $delay; ?>">
							<span class="description"><?php echo  JText::_("A new delay value for the Slide. If no delay defined per slide, the delay defined via Options (9000ms) will be used.",'revslider'); ?></span>
						</p>

						<!-- SLIDE PAUSE ON PURPOSE -->
						<p>
							<?php $stoponpurpose = ZTSliderFunctions::getVal($this->slideParams, 'stoponpurpose','published'); ?>
							<label><?php echo  JText::_("Pause Slider:",'revslider'); ?></label>
							<select id="stoponpurpose" name="stoponpurpose">
								<option value="false"<?php ZTSliderFunctions::selected($stoponpurpose, 'false'); ?>><?php echo  JText::_("Default",'revslider'); ?></option>
								<option value="true"<?php ZTSliderFunctions::selected($stoponpurpose, 'true'); ?>><?php echo  JText::_("Stop Slider Progress",'revslider'); ?></option>						
							</select>
							<span class="description"><?php echo  JText::_("Stop Slider Progress on this slider or use Slider Settings Defaults",'revslider'); ?></span>
						</p>


						<!-- SLIDE PAUSE ON PURPOSE -->
						<p>
							<?php $invisibleslide = ZTSliderFunctions::getVal($this->slideParams, 'invisibleslide','published'); ?>
							<label><?php echo  JText::_("Slide in Navigation (invisible):",'revslider'); ?></label>
							<select id="invisibleslide" name="invisibleslide">
								<option value="false"<?php ZTSliderFunctions::selected($invisibleslide, 'false'); ?>><?php echo  JText::_("Show Always",'revslider'); ?></option>
								<option value="true"<?php ZTSliderFunctions::selected($invisibleslide, 'true'); ?>><?php echo  JText::_("Only Via Actions",'revslider'); ?></option>						
							</select>
							<span class="description"><?php echo  JText::_("Show Slide always or only on Action calls. Invisible slides are not available due Navigation Elements.",'revslider'); ?></span>
						</p>


						<!-- SLIDE STATE -->
						<p>
							<?php $state = ZTSliderFunctions::getVal($this->slideParams, 'state','published'); ?>
							<label><?php echo  JText::_("Slide State:",'revslider'); ?></label>
							<select id="state" name="state">
								<option value="published"<?php ZTSliderFunctions::selected($state, 'published'); ?>><?php echo  JText::_("Published",'revslider'); ?></option>
								<option value="unpublished"<?php ZTSliderFunctions::selected($state, 'unpublished'); ?>><?php echo  JText::_("Unpublished",'revslider'); ?></option>
							</select>
							<span class="description"><?php echo  JText::_("The state of the slide. The unpublished slide will be excluded from the slider.",'revslider'); ?></span>
						</p>

						<!-- SLIDE HIDE AFTER LOOP -->
						<p>
							<?php $hideslideafter = ZTSliderFunctions::getVal($this->slideParams, 'hideslideafter',0); ?>
							<label><?php echo  JText::_('Hide Slide After Loop:','revslider'); ?></label>
							<input type="text" class="small-text" id="hideslideafter" name="hideslideafter" value="<?php echo $hideslideafter; ?>">
							<span class="description"><?php echo  JText::_("After how many Loops should the Slide be hidden ? 0 = Slide is never hidden.",'revslider'); ?></span>
						</p>

						<!-- HIDE SLIDE ON MOBILE -->
						<p>
							<?php $hideslideonmobile = ZTSliderFunctions::getVal($this->slideParams, 'hideslideonmobile', 'off'); ?>
							<label><?php echo  JText::_('Hide Slide On Mobile:','revslider'); ?></label>
							<span style="display:inline-block; width:200px; margin-right:20px;line-height:27px">
								<input type="checkbox" class="tp-moderncheckbox" id="hideslideonmobile" name="hideslideonmobile" data-unchecked="off" <?php ZTSliderFunctions::checked($hideslideonmobile, 'on'); ?>>
							</span>
							<span class="description"><?php echo  JText::_("Show/Hide this Slide if Slider loaded on Mobile Device.",'revslider'); ?></span>
						</p>
						<!-- SLIDE VISIBLE FROM -->
						<p>
							<?php $date_from = ZTSliderFunctions::getVal($this->slideParams, 'date_from',''); ?>
							<label><?php echo  JText::_("Visible from:",'revslider'); ?></label>
							<input type="text" class="inputDatePicker" id="date_from" name="date_from" value="<?php echo $date_from; ?>">
							<span class="description"><?php echo  JText::_("If set, slide will be visible after the date is reached.",'revslider'); ?></span>
						</p>

						<!-- SLIDE VISIBLE UNTIL -->
						<p>
							<?php $date_to = ZTSliderFunctions::getVal($this->slideParams, 'date_to',''); ?>
							<label><?php echo  JText::_("Visible until:",'revslider'); ?></label>
							<input type="text" class="inputDatePicker" id="date_to" name="date_to" value="<?php echo $date_to; ?>">
							<span class="description"><?php echo  JText::_("If set, slide will be visible till the date is reached.",'revslider'); ?></span>
						</p>

						
						<!-- SLIDE VISIBLE FROM -->
						<p style="display:none">
							<?php $save_performance = ZTSliderFunctions::getVal($this->slideParams, 'save_performance','off'); ?>
							<label><?php echo  JText::_("Save Performance:",'revslider'); ?></label>
							<span style="display:inline-block; width:200px; margin-right:20px;">
								<input type="checkbox" class="tp-moderncheckbox withlabel" id="save_performance" name="save_performance" data-unchecked="off" <?php ZTSliderFunctions::checked( $save_performance, "on" ); ?>>
							</span>
							<span class="description"><?php echo  JText::_("Slide End Transition will first start when last Layer has been removed.",'revslider'); ?></span>
						</p>
					<?php
					} else {
					?>
						<!-- STATIC LAYER OVERFLOW (ON/OFF) -->
						<p>
							<?php $staticoverflow = ZTSliderFunctions::getVal($this->slideParams, 'staticoverflow','published'); ?>
							<label><?php echo  JText::_("Static Layers Overflow:",'revslider'); ?></label>
							<select id="staticoverflow" name="staticoverflow">
								<option value="visible"<?php ZTSliderFunctions::selected($staticoverflow, 'visible'); ?>><?php echo  JText::_("Visible",'revslider'); ?></option>
								<option value="hidden"<?php ZTSliderFunctions::selected($staticoverflow, 'hidden'); ?>><?php echo  JText::_("Hidden",'revslider'); ?></option>						
							</select>
							<span class="description"><?php echo  JText::_("Set the Overflow of Static Layers to Visible or Hidden.",'revslider'); ?></span>
						</p>
					<?php
					}
					?>
				</div>
			<?php
			if(!$this->slide->isStaticSlide()){
				?>

				<!-- THUMBNAIL SETTINGS -->
				<div id="slide-thumbnail-settings-content" style="display:none">
					<!-- THUMBNAIL SETTINGS -->
					<div style="margin-top:10px">
						<?php $slide_thumb = ZTSliderFunctions::getVal($this->slideParams, 'slide_thumb','');
								$slide_thumb = @getimagesize($slide_thumb) ? $slide_thumb : JUri::root().$slide_thumb;
						?>
						<span style="display:inline-block; vertical-align: top;">
							<label><?php echo  JText::_("Thumbnail:",'revslider'); ?></label>
						</span>
						<div style="display:inline-block; vertical-align: top;">
							<div id="slide_thumb_button_preview" class="setting-image-preview"><?php
							if(intval($slide_thumb) > 0){
								?>
								<div style="width:100px;height:70px;background:url('<?php echo JRoute::_('index.php?option=com_zt_layerslider'); ?>&task=slide.slidershowimage&amp;img=<?php echo $slide_thumb; ?>&amp;w=100&amp;h=70&amp;t=exact'); background-position:center center; background-size:cover;"></div>
								<?php
							}elseif($slide_thumb !== ''){
								?>
								<div style="width:100px;height:70px;background:url('<?php echo (@getimagesize($slide_thumb) ? $slide_thumb : JUri::root().'/'.$slide_thumb); ?>'); background-position:center center; background-size:cover;"></div>
								<?php
							}
							?></div>
							<input type="hidden" id="slide_thumb" name="slide_thumb" value="<?php echo $slide_thumb; ?>">
							<span style="clear:both;display:block"></span>
							<a href="<?php echo JUri::root() ?>/administrator/index.php?option=com_media&amp;tmpl=component&amp;view=images&amp;asset=com_zt_layerslider&amp;folder=layerslider&amp;fieldid=slide_thumb" id="slide_thumb_button" style="width:110px !important; display:inline-block;" class="button-image-select button-primary revblue fancybox fancybox.iframe" original-title=""><?php echo JText::_('Choose Image') ?></a>
							<input type="button" id="slide_thumb_button_remove" style="margin-right:20px !important; width:85px !important; display:inline-block;" class="button-image-remove button-primary revred"  value="Remove" original-title="">
							<span class="description"><?php echo  JText::_("Slide Thumbnail. If not set - it will be taken from the slide image.",'revslider'); ?></span>
						</div>
					</div>
					<?php $thumb_dimension = ZTSliderFunctions::getVal($this->slideParams, 'thumb_dimension', 'slider'); ?>
					<?php $thumb_for_admin = ZTSliderFunctions::getVal($this->slideParams, 'thumb_for_admin', 'off'); ?>

					<p>
						<span style="display:inline-block; vertical-align: top;">
							<label><?php echo  JText::_("Thumbnail Dimensions:",'revslider'); ?></label>
						</span>
						<select name="thumb_dimension">
							<option value="slider" <?php ZTSliderFunctions::selected($thumb_dimension, 'slider'); ?>><?php echo  JText::_('From Slider Settings', 'revslider'); ?></option>
							<option value="orig" <?php ZTSliderFunctions::selected($thumb_dimension, 'orig'); ?>><?php echo  JText::_('Original Size', 'revslider'); ?></option>
						</select>
						<span class="description"><?php echo  JText::_("Width and height of thumbnails can be changed in the Slider Settings -> Navigation -> Thumbs tab.",'revslider'); ?></span>
					</p>

					<p style="display:none;" class="show_on_thumbnail_exist">
						<span style="display:inline-block; vertical-align: top;">
							<label><?php echo  JText::_("Thumbnail Admin Purpose:",'revslider'); ?></label>
						</span>
						<span style="display:inline-block; width:200px; margin-right:20px;line-height:27px">
							<input type="checkbox" class="tp-moderncheckbox" id="thumb_for_admin" name="thumb_for_admin" data-unchecked="off" <?php ZTSliderFunctions::checked($thumb_for_admin, 'on'); ?>>
						</span>
						<span class="description"><?php echo  JText::_("Use the Thumbnail also for Admin purposes. This will use the selected Thumbnail to represent the Slide in all Slider Admin area.",'revslider'); ?></span>
					</p>
				</div>

				<!-- SLIDE ANIMATIONS -->
				<div id="slide-animation-settings-content" style="display:none">

					<!-- ANIMATION / TRANSITION -->
					<div id="slide_transition_row">
						<?php
							$slide_transition = ZTSliderFunctions::getVal($this->slideParams, 'slide_transition',$this->def_transition);
							if(!is_array($slide_transition))
								$slide_transition = explode(',', $slide_transition);
							
							if(!is_array($slide_transition)) $slide_transition = array($slide_transition);
							$this->operations = new ZTSliderOperations();
							$transitions = $this->operations->getArrTransition();
						?>
						<?php $slot_amount = (array) ZTSliderFunctions::getVal($this->slideParams, 'slot_amount','default'); ?>
						<?php $transition_rotation = (array) ZTSliderFunctions::getVal($this->slideParams, 'transition_rotation','0'); ?>
						<?php $transition_duration = (array) ZTSliderFunctions::getVal($this->slideParams, 'transition_duration',$this->def_transition_duration); ?>
						<?php $transition_ease_in = (array) ZTSliderFunctions::getVal($this->slideParams, 'transition_ease_in','default'); ?>
						<?php $transition_ease_out = (array) ZTSliderFunctions::getVal($this->slideParams, 'transition_ease_out','default'); ?>
						<script type="text/javascript">
							var choosen_slide_transition = [];
							<?php
							$tr_count = count($slide_transition);
							foreach($slide_transition as $tr){
								echo 'choosen_slide_transition.push("'.$tr.'");'."\n";
							}
							?>
							var transition_settings = {
								'slot': [],
								'rotation': [],
								'duration': [],
								'ease_in': [],
								'ease_out': []
								};
							<?php
							foreach($slot_amount as $sa){
								echo 'transition_settings["slot"].push("'.$sa.'");'."\n";
							}
							$sac = count($slot_amount);
							if($sac < $tr_count){
								while($sac < $tr_count){
									$sac++;
									echo 'transition_settings["slot"].push("'.$slot_amount[0].'");'."\n";
								}
							}
							
							foreach($transition_rotation as $sa){
								echo 'transition_settings["rotation"].push("'.$sa.'");'."\n";
							}
							$sac = count($transition_rotation);
							if($sac < $tr_count){
								while($sac < $tr_count){
									$sac++;
									echo 'transition_settings["rotation"].push("'.$transition_rotation[0].'");'."\n";
								}
							}
							
							foreach($transition_duration as $sa){
								echo 'transition_settings["duration"].push("'.$sa.'");'."\n";
							}
							$sac = count($transition_duration);
							if($sac < $tr_count){
								while($sac < $tr_count){
									$sac++;
									echo 'transition_settings["duration"].push("'.$transition_duration[0].'");'."\n";
								}
							}
							
							foreach($transition_ease_in as $sa){
								echo 'transition_settings["ease_in"].push("'.$sa.'");'."\n";
							}
							$sac = count($transition_ease_in);
							if($sac < $tr_count){
								while($sac < $tr_count){
									$sac++;
									echo 'transition_settings["ease_in"].push("'.$transition_ease_in[0].'");'."\n";
								}
							}
							
							foreach($transition_ease_out as $sa){
								echo 'transition_settings["ease_out"].push("'.$sa.'");'."\n";
							}
							$sac = count($transition_ease_out);
							if($sac < $tr_count){
								while($sac < $tr_count){
									$sac++;
									echo 'transition_settings["ease_out"].push("'.$transition_ease_out[0].'");'."\n";
								}
							}
							
							?>
						</script>
						<div id="slide_transition"  multiple="" size="1" style="z-index: 100;">
							<?php
							if(!empty($transitions) && is_array($transitions)){
								$counter = 0;
								$optgroupexist = false;
								$transmenu = '<ul class="slide-trans-menu">';
								$lastclass = '';
								$transchecks ='';
								$listoftrans = '<div class="slide-trans-lists">';
								
								foreach($transitions as $tran_handle => $tran_name){

									$sel = (in_array($tran_handle, $slide_transition)) ? ' checked="checked"' : '';

									if (strpos($tran_handle, 'notselectable') !== false) {
										$listoftrans = $listoftrans.$transchecks;
										$lastclass = "slide-trans-".$tran_handle;
										$transmenu = $transmenu.'<li class="slide-trans-menu-element" data-reference="'.$lastclass.'">'.$tran_name.'</li>';
										$transchecks ='';		

									}
									else
										$transchecks = $transchecks.'<div class="slide-trans-checkelement '.$lastclass.'"><input name="slide_transition[]" type="checkbox" data-useval="true" value="'.$tran_handle.'"'.$sel.'>'.$tran_name.'</div>';							
								}

								$listoftrans = $listoftrans.$transchecks;
								$transmenu = $transmenu."</ul>";
								$listoftrans = $listoftrans."</div>";
								echo $transmenu;
								echo $listoftrans;							
							}
							?>
							
							<div class="slide-trans-example">
								<div class="slide-trans-example-inner">
									<div class="oldslotholder" style="overflow:hidden;width:100%;height:100%;position:absolute;top:0px;left:0px;z-index:1">
										<div class="tp-bgimg defaultimg"></div>
									</div>
									<div class="slotholder" style="overflow:hidden;width:100%;height:100%;position:absolute;top:0px;left:0px;z-index:1">
										<div class="tp-bgimg defaultimg"></div>
									</div>
								</div>
							</div>
							<div class="slide-trans-cur-selected">
								<p><?php echo  JText::_("Used Transitions (Order in Loops)",'revslider'); ?></p>
								<ul class="slide-trans-cur-ul">
								</ul>
							</div>
							<div class="slide-trans-cur-selected-settings">
								<!-- SLOT AMOUNT -->
								
								<label><?php echo  JText::_("Slot / Box Amount:",'revslider'); ?></label>
								<input type="text" class="small-text input-deepselects" id="slot_amount" name="slot_amount" value="<?php echo $slot_amount[0]; ?>" data-selects="1||Random||Custom||Default" data-svalues ="1||random||3||default" data-icons="thumbs-up||shuffle||wrench||key">
								<span class="tp-clearfix"></span>
								<span class="description"><?php echo  JText::_("# of slots/boxes the slide is divided into.",'revslider'); ?></span>					
								<span class="tp-clearfix"></span>
								
								<!-- ROTATION -->
								
								<label><?php echo  JText::_("Slot Rotation:",'revslider'); ?></label>
								<input type="text" class="small-text input-deepselects" id="transition_rotation" name="transition_rotation" value="<?php echo $transition_rotation[0]; ?>" data-selects="0||Random||Custom||Default||45||90||180||270||360" data-svalues ="0||random||-75||default||45||90||180||270||360" data-icons="thumbs-up||shuffle||wrench||key||star-empty||star-empty||star-empty||star-empty||star-empty">
								<span class="tp-clearfix"></span>
								<span class="description"><?php echo  JText::_("Start Rotation of Transition (deg).",'revslider'); ?></span>
								<span class="tp-clearfix"></span>

								<!-- DURATION -->
								
								<label><?php echo  JText::_("Animation Duration:",'revslider'); ?></label>
								<input type="text" class="small-text input-deepselects" id="transition_duration" name="transition_duration" value="<?php echo $transition_duration[0]; ?>" data-selects="300||Random||Custom||Default" data-svalues ="500||random||650||default" data-icons="thumbs-up||shuffle||wrench||key">
								<span class="tp-clearfix"></span>
								<span class="description"><?php echo  JText::_("The duration of the transition.",'revslider'); ?></span>
								<span class="tp-clearfix"></span>

								<!-- IN EASE -->
								
								<label><?php echo  JText::_("Easing In:",'revslider'); ?></label>
								<select name="transition_ease_in">
										<option value="default">Default</option>
										<option value="Linear.easeNone">Linear.easeNone</option>
										<option value="Power0.easeIn">Power0.easeIn  (linear)</option>
										<option value="Power0.easeInOut">Power0.easeInOut  (linear)</option>
										<option value="Power0.easeOut">Power0.easeOut  (linear)</option>
										<option value="Power1.easeIn">Power1.easeIn</option>
										<option value="Power1.easeInOut">Power1.easeInOut</option>
										<option value="Power1.easeOut">Power1.easeOut</option>
										<option value="Power2.easeIn">Power2.easeIn</option>
										<option value="Power2.easeInOut">Power2.easeInOut</option>
										<option value="Power2.easeOut">Power2.easeOut</option>
										<option value="Power3.easeIn">Power3.easeIn</option>
										<option value="Power3.easeInOut">Power3.easeInOut</option>
										<option value="Power3.easeOut">Power3.easeOut</option>
										<option value="Power4.easeIn">Power4.easeIn</option>
										<option value="Power4.easeInOut">Power4.easeInOut</option>
										<option value="Power4.easeOut">Power4.easeOut</option>
										<option value="Back.easeIn">Back.easeIn</option>
										<option value="Back.easeInOut">Back.easeInOut</option>
										<option value="Back.easeOut">Back.easeOut</option>
										<option value="Bounce.easeIn">Bounce.easeIn</option>
										<option value="Bounce.easeInOut">Bounce.easeInOut</option>
										<option value="Bounce.easeOut">Bounce.easeOut</option>
										<option value="Circ.easeIn">Circ.easeIn</option>
										<option value="Circ.easeInOut">Circ.easeInOut</option>
										<option value="Circ.easeOut">Circ.easeOut</option>
										<option value="Elastic.easeIn">Elastic.easeIn</option>
										<option value="Elastic.easeInOut">Elastic.easeInOut</option>
										<option value="Elastic.easeOut">Elastic.easeOut</option>
										<option value="Expo.easeIn">Expo.easeIn</option>
										<option value="Expo.easeInOut">Expo.easeInOut</option>
										<option value="Expo.easeOut">Expo.easeOut</option>
										<option value="Sine.easeIn">Sine.easeIn</option>
										<option value="Sine.easeInOut">Sine.easeInOut</option>
										<option value="Sine.easeOut">Sine.easeOut</option>
										<option value="SlowMo.ease">SlowMo.ease</option>
								</select>
								<span class="tp-clearfix"></span>
								<span class="description"><?php echo  JText::_("The easing of Appearing transition.",'revslider'); ?></span>
								<span class="tp-clearfix"></span>

								<!-- OUT EASE -->
								
								<label><?php echo  JText::_("Easing Out:",'revslider'); ?></label>
								<select name="transition_ease_out">
										<option value="default">Default</option>
										<option value="Linear.easeNone">Linear.easeNone</option>
										<option value="Power0.easeIn">Power0.easeIn  (linear)</option>
										<option value="Power0.easeInOut">Power0.easeInOut  (linear)</option>
										<option value="Power0.easeOut">Power0.easeOut  (linear)</option>
										<option value="Power1.easeIn">Power1.easeIn</option>
										<option value="Power1.easeInOut">Power1.easeInOut</option>
										<option value="Power1.easeOut">Power1.easeOut</option>
										<option value="Power2.easeIn">Power2.easeIn</option>
										<option value="Power2.easeInOut">Power2.easeInOut</option>
										<option value="Power2.easeOut">Power2.easeOut</option>
										<option value="Power3.easeIn">Power3.easeIn</option>
										<option value="Power3.easeInOut">Power3.easeInOut</option>
										<option value="Power3.easeOut">Power3.easeOut</option>
										<option value="Power4.easeIn">Power4.easeIn</option>
										<option value="Power4.easeInOut">Power4.easeInOut</option>
										<option value="Power4.easeOut">Power4.easeOut</option>
										<option value="Back.easeIn">Back.easeIn</option>
										<option value="Back.easeInOut">Back.easeInOut</option>
										<option value="Back.easeOut">Back.easeOut</option>
										<option value="Bounce.easeIn">Bounce.easeIn</option>
										<option value="Bounce.easeInOut">Bounce.easeInOut</option>
										<option value="Bounce.easeOut">Bounce.easeOut</option>
										<option value="Circ.easeIn">Circ.easeIn</option>
										<option value="Circ.easeInOut">Circ.easeInOut</option>
										<option value="Circ.easeOut">Circ.easeOut</option>
										<option value="Elastic.easeIn">Elastic.easeIn</option>
										<option value="Elastic.easeInOut">Elastic.easeInOut</option>
										<option value="Elastic.easeOut">Elastic.easeOut</option>
										<option value="Expo.easeIn">Expo.easeIn</option>
										<option value="Expo.easeInOut">Expo.easeInOut</option>
										<option value="Expo.easeOut">Expo.easeOut</option>
										<option value="Sine.easeIn">Sine.easeIn</option>
										<option value="Sine.easeInOut">Sine.easeInOut</option>
										<option value="Sine.easeOut">Sine.easeOut</option>
										<option value="SlowMo.ease">SlowMo.ease</option>
								</select>
								<span class="tp-clearfix"></span>
								<span class="description"><?php echo  JText::_("The easing of Disappearing transition.",'revslider'); ?></span>
								
							</div>

						</div>
						
					</div>

					
				</div>
				
				<!-- SLIDE BASIC INFORMATION -->
				<div id="slide-nav-settings-content" style="display:none">		
					<ul class="rs-layer-nav-settings-tabs" style="display:inline-block; ">
						<li id="custom-nav-arrows-tab-selector" data-content="arrows" class="selected"><?php echo  JText::_('Arrows', 'revslider'); ?></li>
						<li id="custom-nav-bullets-tab-selector" data-content="bullets"><?php echo  JText::_('Bullets', 'revslider'); ?></li>					
						<li id="custom-nav-tabs-tab-selector" data-content="tabs"><?php echo  JText::_('Tabs', 'revslider'); ?></li>
						<li id="custom-nav-thumbs-tab-selector" data-content="thumbs"><?php echo  JText::_('Thumbnails', 'revslider'); ?></li>
					</ul>

					<div class="tp-clearfix"></div>



					<ul id="navigation-placeholder-wrapper">					
						<?php
						$ph_types = array('navigation_arrow_style' => 'arrows', 'navigation_bullets_style' => 'bullets', 'tabs_style' => 'tabs', 'thumbnails_style' => 'thumbs');
						foreach($ph_types as $phname => $pht){
							
							$ph_arr_type = $this->slider->getParam($phname,'');
							
							$ph_init = array();
							foreach($this->arr_navigations as $nav){
								if($nav['handle'] == $ph_arr_type){ //check for settings, placeholders
									if(isset($nav['settings']) && isset($nav['settings']['placeholders'])){
										foreach($nav['settings']['placeholders'] as $placeholder){
											if(empty($placeholder)) continue;
											
											$ph_vals = array();
											
											//$placeholder['type']
											foreach($placeholder['data'] as $k => $d){
												$get_from = ZTSliderFunctions::getVal($this->slideParams, 'ph-'.$ph_arr_type.'-'.$pht.'-'.$placeholder['handle'].'-'.$k.'-slide', 'off');
												if($get_from == 'on'){ //get from Slide
													$ph_vals[$k] = ZTSliderFunctions::getVal($this->slideParams, 'ph-'.$ph_arr_type.'-'.$pht.'-'.$placeholder['handle'].'-'.$k, $d);											
												}else{ ////get from Slider
													$ph_vals[$k] = $this->slider->getParam('ph-'.$ph_arr_type.'-'.$pht.'-'.$placeholder['handle'].'-'.$k, $d);											
												}
											}										
											?>
											<?php if ($placeholder['nav-type'] === $pht) { ?>
												<li class="custom-nav-types nav-type-<?php echo $placeholder['nav-type']; ?>">										
												<?php
												switch($placeholder['type']){
													case 'color':
														$get_from = ZTSliderFunctions::getVal($this->slideParams, 'ph-'.$ph_arr_type.'-'.$pht.'-'.$placeholder['handle'].'-color-slide', 'off');
														?>
														<label><?php echo $placeholder['title']; ?></label>
														<input type="checkbox" class="tp-moderncheckbox" id="<?php echo 'ph-'.$ph_arr_type.'-'.$pht.'-'.$placeholder['handle'].'-color-slide'; ?>" name="<?php echo 'ph-'.$ph_arr_type.'-'.$pht.'-'.$placeholder['handle'].'-color-slide'; ?>" data-unchecked="off" <?php ZTSliderFunctions::checked($get_from, 'on'); ?>>
														<input type="text" name="<?php echo 'ph-'.$ph_arr_type.'-'.$pht.'-'.$placeholder['handle'].'-color'; ?>" class="my-alphacolor-field" value="<?php echo $ph_vals['color']; ?>">																								
														<?php
													break;

													case 'color-rgba':
														$get_from = ZTSliderFunctions::getVal($this->slideParams, 'ph-'.$ph_arr_type.'-'.$pht.'-'.$placeholder['handle'].'-color-rgba-slide', 'off');
														?>
														<label><?php echo $placeholder['title']; ?></label>
														<input type="checkbox" class="tp-moderncheckbox" id="<?php echo 'ph-'.$ph_arr_type.'-'.$pht.'-'.$placeholder['handle'].'-color-rgba-slide'; ?>" name="<?php echo 'ph-'.$ph_arr_type.'-'.$pht.'-'.$placeholder['handle'].'-color-rgba-slide'; ?>" data-unchecked="off" <?php ZTSliderFunctions::checked($get_from, 'on'); ?>>
														<input type="text" name="<?php echo 'ph-'.$ph_arr_type.'-'.$pht.'-'.$placeholder['handle'].'-color-rgba'; ?>" class="my-alphacolor-field" value="<?php echo $ph_vals['color-rgba']; ?>">																								
														<?php
													break;
													case 'font-family':
														$get_from_font_family = ZTSliderFunctions::getVal($this->slideParams, 'ph-'.$ph_arr_type.'-'.$pht.'-'.$placeholder['handle'].'-font_family-slide', 'off');
														?>
														<label><?php echo $placeholder['title']; ?></label>
														<input type="checkbox" class="tp-moderncheckbox" id="<?php echo 'ph-'.$ph_arr_type.'-'.$pht.'-'.$placeholder['handle'].'-font_family-slide'; ?>" name="<?php echo 'ph-'.$ph_arr_type.'-'.$pht.'-'.$placeholder['handle'].'-font_family-slide'; ?>" data-unchecked="off" <?php ZTSliderFunctions::checked($get_from_font_family, 'on'); ?>>
														<select style="width: 140px;" name="<?php echo 'ph-'.$ph_arr_type.'-'.$pht.'-'.$placeholder['handle'].'-font_family'; ?>">
														<?php
														$font_families = $this->operations->getArrFontFamilys();
														foreach($font_families as $handle => $name){
															if($name['label'] == 'Dont Show Me') continue;
															
															echo '<option value="'. trim($name['label']) .'"';
															if($ph_vals['font_family'] == trim($name['label'])){
																echo ' selected="selected"';
															}
															echo '>'. trim($name['label']) .'</option>';													
														}
														?>
														</select>												
														<?php
													break;
													case 'custom':
														$get_from_custom = ZTSliderFunctions::getVal($this->slideParams, 'ph-'.$ph_arr_type.'-'.$pht.'-'.$placeholder['handle'].'-custom-slide', 'off');
														?>
														<label><?php echo $placeholder['title']; ?></label>
														<input type="checkbox" class="tp-moderncheckbox" id="<?php echo 'ph-'.$ph_arr_type.'-'.$pht.'-'.$placeholder['handle'].'-custom-slide'; ?>" name="<?php echo 'ph-'.$ph_arr_type.'-'.$pht.'-'.$placeholder['handle'].'-custom-slide'; ?>" data-unchecked="off" <?php ZTSliderFunctions::checked($get_from_custom, 'on'); ?>>
														<input type="text" name="<?php echo 'ph-'.$ph_arr_type.'-'.$pht.'-'.$placeholder['handle'].'-custom'; ?>" value="<?php echo $ph_vals['custom']; ?>">												
														<?php
													break;
												}
												?>
												</li>
											<?php
											}
											?>
											<?php
										}
									}
									break;
								}
							}
						}
						?>
						
						
						
					</ul>
					<p style="margin-top:25px"><i><?php echo  JText::_("The Custom Settings are always depending on the current selected Navigation Elements in Slider Settings, and will only be active on the current Slide.",'revslider'); ?></i></p>
					<script type="text/javascript">
						jQuery(document).ready(function() {
							if (jQuery('.custom-nav-types.nav-type-arrows').length==0)
								jQuery('#custom-nav-arrows-tab-selector').remove();

							if (jQuery('.custom-nav-types.nav-type-bullets').length==0)
								jQuery('#custom-nav-bullets-tab-selector').remove();

							if (jQuery('.custom-nav-types.nav-type-tabs').length==0)
								jQuery('#custom-nav-tabs-tab-selector').remove();

							if (jQuery('.custom-nav-types.nav-type-thumbs').length==0)
								jQuery('#custom-nav-thumbs-tab-selector').remove();

							if (jQuery('#navigation-placeholder-wrapper li').length==0) 
								jQuery('#main-menu-nav-settings-li').remove();
							
						
							jQuery('document').ready(function() {
								jQuery('.rs-layer-nav-settings-tabs li').click(function() {							
									var tn = jQuery(this);							
									jQuery('.custom-nav-types').hide();
									jQuery('.custom-nav-types.nav-type-'+tn.data('content')).show();
									jQuery('.rs-layer-nav-settings-tabs .selected').removeClass("selected");
									tn.addClass("selected");							
								});
							});
							setTimeout(function() {
								jQuery('.rs-layer-nav-settings-tabs li:nth-child(1)').click();	
							},100)
							
						});
					</script>
				</div>
				<?php
			}
			?>
			<!-- SLIDE ADDON WRAP -->
			<div id="slide-addon-wrapper" style="margin:-15px; display:none">
				<div id="rs-addon-wrapper-button-row">
					<span class="rs-layer-toolbar-box" style="padding:5px 20px"><?php echo  JText::_('Select Add-on', 'revslider'); ?></span>
					<?php
					if(!empty($slide_general_addon)){
						foreach($slide_general_addon as $rs_addon_handle => $rs_addon){
							?>
							<span class="rs-layer-toolbar-box">
								<span id="rs-addon-settings-trigger-<?php echo trim($rs_addon_handle); ?>" class="rs-addon-settings-trigger"><?php echo trim($rs_addon['title']); ?></span>
							</span>
							<?php
						}
					}
					?>
				</div>
				<div style="border-top:1px solid #ddd;">
					<?php
					if(!empty($slide_general_addon)){
						foreach($slide_general_addon as $rs_addon_handle => $rs_addon){
							?>
							<div id="rs-addon-settings-trigger-<?php echo trim($rs_addon_handle); ?>-settings" class="rs-addon-settings-wrapper-settings" style="display: none;">
								<?php echo $rs_addon['markup']; ?>
								<script type="text/javascript">
									<?php echo $rs_addon['javascript']; ?>
								</script>
							</div>
							<?php
						}
					}
					?>
					<script type="text/javascript">
						jQuery('.rs-addon-settings-trigger').click(function(){
							var show_addon = jQuery(this).attr('id');
							jQuery('.rs-addon-settings-trigger').removeClass("selected");
							jQuery(this).addClass("selected");
							jQuery('.rs-addon-settings-wrapper-settings').hide();
							jQuery('#'+show_addon+'-settings').show();
						});
					</script>
				</div>
			</div>
			<?php 
			if(!$this->slide->isStaticSlide()){
				?>
			
				<!-- SLIDE BASIC INFORMATION -->
				<div id="slide-info-settings-content" style="display:none">
					<ul>
						<?php
						for($i=1;$i<=10;$i++){
							?>
							<li>
								<label><?php echo  JText::_('Parameter', 'revslider'); echo ' '.$i; ?></label> <input type="text" name="params_<?php echo $i; ?>" value="<?php echo stripslashes(trim(ZTSliderFunctions::getVal($this->slideParams, 'params_'.$i,''))); ?>">
								<?php echo  JText::_('Max. Chars', 'revslider'); ?> <input type="text" style="width: 50px; min-width: 50px;" name="params_<?php echo $i; ?>_chars" value="<?php echo trim(ZTSliderFunctions::getVal($this->slideParams, 'params_'.$i.'_chars',10, ZTSlider::FORCE_NUMERIC)); ?>">
								<?php if($this->slider_type !== 'gallery'){ ?><i class="eg-icon-pencil rs-param-meta-open" data-curid="<?php echo $i; ?>"></i><?php } ?>
							</li>
							<?php
						}
						?>
					</ul>
					
					<!-- BASIC DESCRIPTION -->
					<p>
						<?php $slide_description = stripslashes(ZTSliderFunctions::getVal($this->slideParams, 'slide_description', '')); ?>
						<label><?php echo  JText::_("Description of Slider:",'revslider'); ?></label>

						<textarea name="slide_description" style="height: 425px; width: 100%"><?php echo $slide_description; ?></textarea>
						<span class="description"><?php echo  JText::_('Define a description here to show at the navigation if enabled in Slider Settings','revslider'); ?></span>
					</p>
				</div>

				<!-- SLIDE SEO INFORMATION -->
				<div id="slide-seo-settings-content" style="display:none">
					<!-- CLASS -->
					<p>
						<?php $class_attr = ZTSliderFunctions::getVal($this->slideParams, 'class_attr',''); ?>
						<label><?php echo  JText::_("Class:",'revslider'); ?></label>
						<input type="text" class="" id="class_attr" name="class_attr" value="<?php echo $class_attr; ?>">
						<span class="description"><?php echo  JText::_('Adds a unique class to the li of the Slide like class="rev_special_class" (add only the classnames, seperated by space)','revslider'); ?></span>
					</p>

					<!-- ID -->
					<p>
						<?php $id_attr = ZTSliderFunctions::getVal($this->slideParams, 'id_attr',''); ?>
						<label><?php echo  JText::_("ID:",'revslider'); ?></label>
						<input type="text" class="" id="id_attr" name="id_attr" value="<?php echo $id_attr; ?>">
						<span class="description"><?php echo  JText::_('Adds a unique ID to the li of the Slide like id="rev_special_id" (add only the id)','revslider'); ?></span>
					</p>

					<!-- CUSTOM FIELDS -->
					<p>
						<?php $data_attr = stripslashes(ZTSliderFunctions::getVal($this->slideParams, 'data_attr','')); ?>
						<label><?php echo  JText::_("Custom Fields:",'revslider'); ?></label>
						<textarea id="data_attr" name="data_attr"><?php echo $data_attr; ?></textarea>
						<span class="description"><?php echo  JText::_('Add as many attributes as you wish here. (i.e.: data-layer="firstlayer" data-custom="somevalue").','revslider'); ?></span>
					</p>

					<!-- Enable Link -->
					<p>
						<?php $enable_link = ZTSliderFunctions::getVal($this->slideParams, 'enable_link','false'); ?>
						<label><?php echo  JText::_("Enable Link:",'revslider'); ?></label>
						<select id="enable_link" name="enable_link">
							<option value="true"<?php ZTSliderFunctions::selected($enable_link, 'true'); ?>><?php echo  JText::_("Enable",'revslider'); ?></option>
							<option value="false"<?php ZTSliderFunctions::selected($enable_link, 'false'); ?>><?php echo  JText::_("Disable",'revslider'); ?></option>
						</select>
						<span class="description"><?php echo  JText::_('Link the Full Slide to an URL or Action.','revslider'); ?></span>
					</p>
					
					<div class="rs-slide-link-setting-wrapper">
						<!-- Link Type -->
						<p>
							<?php $enable_link = ZTSliderFunctions::getVal($this->slideParams, 'link_type','regular'); ?>
							<label><?php echo  JText::_("Link Type:",'revslider'); ?></label>
							<span style="display:inline-block; width:200px; margin-right:20px;">
								<input type="radio" id="link_type_1" value="regular" name="link_type"<?php ZTSliderFunctions::checked($enable_link, 'regular'); ?>><span style="line-height:30px; vertical-align: middle; margin:0px 20px 0px 10px;"><?php echo  JText::_('Regular','revslider'); ?></span>
								<input type="radio" id="link_type_2" value="slide" name="link_type"<?php ZTSliderFunctions::checked($enable_link, 'slide'); ?>><span style="line-height:30px; vertical-align: middle; margin:0px 20px 0px 10px;"><?php echo  JText::_('To Slide','revslider'); ?></span>
							</span>
							<span class="description"><?php echo  JText::_('Regular - Link to URL,  To Slide - Call a Slide Action','revslider'); ?></span>
						</p>

						<div class="rs-regular-link-setting-wrap">
							<!-- SLIDE LINK -->
							<p>
								<?php $val_link = ZTSliderFunctions::getVal($this->slideParams, 'link',''); ?>
								<label><?php echo  JText::_("Slide Link:",'revslider'); ?></label>
								<input type="text" id="rev_link" name="link" value="<?php echo $val_link; ?>">
								<span class="description"><?php echo  JText::_('A link on the whole slide pic (use {{link}} or {{meta:somemegatag}} in template sliders to link to a post or some other meta)','revslider'); ?></span>
							</p>
						
							<!-- LINK TARGET -->
							<p>
								<?php $link_open_in = ZTSliderFunctions::getVal($this->slideParams, 'link_open_in','same'); ?>
								<label><?php echo  JText::_("Link Target:",'revslider'); ?></label>
								<select id="link_open_in" name="link_open_in">
									<option value="same"<?php ZTSliderFunctions::selected($link_open_in, 'same'); ?>><?php echo  JText::_('Same Window','revslider'); ?></option>
									<option value="new"<?php ZTSliderFunctions::selected($link_open_in, 'new'); ?>><?php echo  JText::_('New Window','revslider'); ?></option>
								</select>
								<span class="description"><?php echo  JText::_('The target of the slide link.','revslider'); ?></span>
							</p>
						</div>
						<!-- LINK TO SLIDE -->
						<p class="rs-slide-to-slide">
							<?php $slide_link = ZTSliderFunctions::getVal($this->slideParams, 'slide_link','nothing');
							//num_slide_link
							$arrSlideLink = array();
							$arrSlideLink["nothing"] = JText::_("-- Not Chosen --",'revslider');
							$arrSlideLink["next"] = JText::_("-- Next Slide --",'revslider');
							$arrSlideLink["prev"] = JText::_("-- Previous Slide --",'revslider');

							$arrSlideLinkLayers = $arrSlideLink;
							$arrSlideLinkLayers["scroll_under"] = JText::_("-- Scroll Below Slider --",'revslider');
							$this->arrSlideNames = array();
							if(isset($this->slider) && $this->slider->isInited())
								$this->arrSlideNames = $this->slider->getArrSlideNames();
							if(!empty($this->arrSlideNames) && is_array($this->arrSlideNames)){
								foreach($this->arrSlideNames as $slideNameID=>$arr){
									$slideName = $arr["title"];
									$arrSlideLink[$slideNameID] = $slideName;
									$arrSlideLinkLayers[$slideNameID] = $slideName;
								}
							}
							?>
							<label><?php echo  JText::_("Link To Slide:",'revslider'); ?></label>
							<select id="slide_link" name="slide_link">
								<?php
								if(!empty($arrSlideLinkLayers) && is_array($arrSlideLinkLayers)){
									foreach($arrSlideLinkLayers as $link_handle => $link_name){
										$sel = ($link_handle == $slide_link) ? ' selected="selected"' : '';
										echo '<option value="'.$link_handle.'"'.$sel.'>'.$link_name.'</option>';
									}
								}
								?>
							</select>
							<span class="description"><?php echo  JText::_('Call Slide Action','revslider'); ?></span>
						</p>
						<!-- Link POSITION -->
						<p>
							<?php $link_pos = ZTSliderFunctions::getVal($this->slideParams, 'link_pos','front'); ?>
							<label><?php echo  JText::_("Link Sensibility:",'revslider'); ?></label>
							<span style="display:inline-block; width:200px; margin-right:20px;">
								<input type="radio" id="link_pos_1" value="front" name="link_pos"<?php ZTSliderFunctions::checked($link_pos, 'front'); ?>><span style="line-height:30px; vertical-align: middle; margin:0px 20px 0px 10px;"><?php echo  JText::_('Front','revslider'); ?></span>
								<input type="radio" id="link_pos_2" value="back" name="link_pos"<?php ZTSliderFunctions::checked($link_pos, 'back'); ?>><span style="line-height:30px; vertical-align: middle; margin:0px 20px 0px 10px;"><?php echo  JText::_('Back','revslider'); ?></span>
							</span>
							<span class="description"><?php echo  JText::_('The z-index position of the link related to layers','revslider'); ?></span>
						</p>
					</div>
				</div>
			<?php
			}
			?>

		</form>

	</div>
</div>
<script type="text/javascript">
	var rs_plugin_url = '<?php echo ZT_ASSETS_URL; ?>';
	
	jQuery('document').ready(function() {
		jQuery('.my-alphacolor-field').alphaColorPicker();
		jQuery('#enable_link').change(function(){
			if(jQuery(this).val() == 'true'){
				jQuery('.rs-slide-link-setting-wrapper').show();
			}else{
				jQuery('.rs-slide-link-setting-wrapper').hide();
			}
		});
		jQuery('#enable_link option:selected').change();
		
		jQuery('input[name="link_type"]').change(function(){
			if(jQuery(this).val() == 'regular'){
				jQuery('.rs-regular-link-setting-wrap').show();
				jQuery('.rs-slide-to-slide').hide();
			}else{
				jQuery('.rs-regular-link-setting-wrap').hide();
				jQuery('.rs-slide-to-slide').show();
			}
		});
		jQuery('input[name="link_type"]:checked').change();
		
	});
</script>