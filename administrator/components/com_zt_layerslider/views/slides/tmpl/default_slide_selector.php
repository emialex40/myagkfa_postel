<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2015 ThemePunch
 */


defined('_JEXEC') or die;

$_width = $this->slider->getParam('width', 1280);
$_height = $this->slider->getParam('height', 868);

$the_slidertype = $this->slider->getParam('slider-type', 'standard');

if($the_slidertype == 'hero'){
	$active_slide = $this->slider->getParam('hero_active', -1);
	//check if this id is still existing
	$exists = ZtSliderSlide::isSlideByID($active_slide);
	
	if($exists == false){
		$active_slide = -1;
	}
}

?>
<input type="hidden" value="<?php echo $_width; ?>" name="rs-grid-width" />
<input type="hidden" value="<?php echo $_height; ?>" name="rs-grid-height" />

<div id="slide_selector" class="slide_selector editor_buttons_wrapper  postbox unite-postbox" style="max-width:100% !important;">
	<div class="inner_wrapper p10 boxsized">
		<ul class="list_slide_links">
			<?php
			$staticclass = '';
			$sID = $this->slider->getID();
			if($this->slide->isStaticSlide()){
				$staticclass = 'statictabselected';
			}
			
			?>
			<li class="<?php echo $staticclass; ?> eg-drag-disabled">
				<?php
				if(!$this->slide->isStaticSlide()){
					$link = JRoute::_('index.php?option=com_zt_layerslider&view=slides&id=static_'.$sID,false);
					?>
					<a href="<?php echo $link; ?>" class="add_slide">
					<?php
				}
				
				?>
				<div class="slide-media-container icon-basketball" style="border:1px solid #3498DB; border-bottom:none;"></div>
				<div class="slide-link-content alwaysbluebg" style="background:#3498DB !important; color:#fff">
					<span class="slide-link" style="width:100%;text-align: center;"><?php echo  JText::_("Static / Global Layers",'revslider'); ?></span>
				</div>
				<?php 
				if(!$this->slide->isStaticSlide()){
					?>
					</a>
					<?php
				}
				?>

				<?php
				//show/hide layers of specific slides
				if($this->slide->isStaticSlide()){
					$all_slides = $this->slider->getSlides(true);
					?>
						<span style="position:absolute; top:13px;left:0px; text-align: center">
							<span class="setting_text_3"><?php echo  JText::_("Show Layers from Slide:",'revslider'); ?></span>
							<select name="rev_show_the_slides">
								<option value="none">---</option>
								<?php
								foreach($all_slides as $c_slide){
									$c_params = $c_slide->getParams();
									?>
									<option value="<?php echo $c_slide->getID(); ?>"><?php echo stripslashes(ZTSliderFunctions::getVal($c_params, 'title', 'Slide')).' (ID: '.$c_slide->getID().')'; ?></option>
									<?php
								}
								?>
							</select>
						</span>
					
					<?php
				}
				?>
			</li>
			<?php
			
			$slidecounter = 0;

			foreach($this->arrSlides as $t_slide) {

				$slidelistID = $t_slide->getID();
				/* BACKGROUND SETTINGS */
				$c_bgType = $t_slide->getParam('background_type', 'transparent');
				$c_bgColor = $t_slide->getParam('slide_bg_color', 'transparent');

				$c_bgFit = $t_slide->getParam('bg_fit', 'cover');
				$c_bgFitX = intval($t_slide->getParam('bg_fit_x', '100'));
				$c_bgFitY = intval($t_slide->getParam('bg_fit_y', '100'));

				$c_bgPosition = $t_slide->getParam('bg_position', 'center center');
				$c_bgPositionX = intval($t_slide->getParam('bg_position_x', '0'));
				$c_bgPositionY = intval($t_slide->getParam('bg_position_y', '0'));

				$c_bgRepeat = $t_slide->getParam('bg_repeat', 'no-repeat');

				$c_isvisible = $t_slide->getParam('state', 'published');

				$c_thumb_for_admin = $t_slide->getParam('thumb_for_admin', 'off');
				$c_real_thumbURL = $t_slide->getParam('slide_thumb','');
				$c_real_thumbURL = @getimagesize($c_real_thumbURL)? $c_real_thumbURL : JUri::root().$c_real_thumbURL;

				$c_bgStyle = ' ';
				if($c_bgFit == 'percentage'){
					$c_bgStyle .= "background-size: ".$c_bgFitX.'% '.$c_bgFitY.'%;';
				}else{
					$c_bgStyle .= "background-size: ".$c_bgFit.";";
				}
				if($c_bgPosition == 'percentage'){
					$c_bgStyle .= "background-position: ".$c_bgPositionX.'% '.$c_bgPositionY.'%;';
				}else{
					$c_bgStyle .= "background-position: ".$c_bgPosition.";";
				}
				$c_bgStyle .= "background-repeat: ".$c_bgRepeat.";";
				$c_urlImageForView = $t_slide->getThumbUrl();

				$c_bg_fullstyle ='';
				$c_bg_extraClass='';
				
				if($c_bgType == 'image' || $c_bgType == 'streamvimeo' || $c_bgType == 'streamyoutube' || $c_bgType == 'streaminstagram'){
					switch($this->slider_type){
						case 'posts':
							$c_urlImageForView = ZT_ASSETS_PATH.'/assets/images/sources/post.png';
						break;
						case 'facebook':
							$c_urlImageForView = ZT_ASSETS_PATH.'/assets/images/sources/fb.png';
						break;
						case 'twitter':
							$c_urlImageForView = ZT_ASSETS_PATH.'/assets/images/sources/tw.png';
						break;
						case 'instagram':
							$c_urlImageForView = ZT_ASSETS_PATH.'/assets/images/sources/ig.png';
						break;
						case 'flickr':
							$c_urlImageForView = ZT_ASSETS_PATH.'/assets/images/sources/fr.png';
						break;
						case 'youtube':
							$c_urlImageForView = ZT_ASSETS_PATH.'/assets/images/sources/yt.png';
						break;
						case 'vimeo':
							$c_urlImageForView = ZT_ASSETS_PATH.'/assets/images/sources/vm.png';
						break;
					}
				}
				
				if ($c_bgType == 'image' || $c_bgType == 'vimeo' || $c_bgType == 'youtube' || $c_bgType == 'html5' || $c_bgType == 'streamvimeo' || $c_bgType == 'streamyoutube' || $c_bgType == 'streaminstagram')
					$c_bg_fullstyle =' style="background-image:url('.$c_urlImageForView.');'.$c_bgStyle.'" ';

				if ($c_bgType == 'solid')
					$c_bg_fullstyle =' style="background-color:'.$c_bgColor.';" ';
				if ($c_bgType == 'trans')
					$c_bg_extraClass = 'mini-transparent';

				if ($c_thumb_for_admin=="on")
					$c_bg_fullstyle =' style="background-image:url('.$c_real_thumbURL.');background-size:cover;background-position:center center" ';

				/* END OF BG SETTINGS */
				$slidecounter++;
				$title = $t_slide->getParam('title', 'Slide');
				$slideName = $title;
				$arrChildrenIDs = $t_slide->getArrChildrenIDs();

				$class = 'tipsy_enabled_top';
				$titleclass = '';
				$c_topclass = '';
//				$urlEditSlide = self::getViewUrl(ZtSliderAdmin::VIEW_SLIDE,"id=$slidelistID");
				$urlEditSlide = JRoute::_('index.php?option=com_zt_layerslider&view=slides&id='.$slidelistID,false);
				if($this->slideID == $slidelistID || in_array($this->slideID, $arrChildrenIDs)){
					$class .= ' selected';
					$c_topclass = ' selected';
					$titleclass = ' ';
					$urlEditSlide = 'javascript:void(0)';
				}

				$addParams = "class='".$class."'";
				$slideName = str_replace("'", "", $slideName);
				
				?>
				<li id="slidelist_item_<?php echo $slidelistID; ?>" class="<?php echo $c_topclass; ?>">
					<a href="<?php echo $urlEditSlide; ?>" <?php echo $addParams; ?>>
						<span class="mini-transparent mini-as-bg"></span>
						<span class="slide-media-container <?php echo $c_bg_extraClass; ?>" <?php echo $c_bg_fullstyle; ?>></span>
						<i class="slide-link-forward eg-icon-forward"></i>
					</a>
					<span class="slide-link-published-wrapper">
						<?php
						if($the_slidertype !== 'hero'){
							if($c_isvisible == 'published'){
								?>
								<span class="slide-published"></span>
								<span class="slide-unpublished pubclickable"></span>
								<?php
							}else{
								?>
								<span class="slide-unpublished"></span>
								<span class="slide-published pubclickable"></span>										
								<?php
							}
						}else{ //we are a hero blog, Slides are not published/unpublished here, but rather the active Slide can be choosen
							if($active_slide == $slidelistID || $active_slide == -1){
								?>
								<span class="slide-hero-published"></span>
								<?php
								$active_slide = -99; //so that if it was -1, it will not be done again. First slide is just active
							}else{
								?>
								<span class="slide-hero-unpublished pubclickable"></span>
								<?php
							}
						}
						?>
					</span>
					
					<div class="slide-link-content">		
						<span class="slide-link">
							<span class="slide-link-nr">#<?php echo $slidecounter; ?></span>
							<input class="slidetitleinput" name="slidetitle" value="<?php echo stripslashes($title); ?>" />
							<span class="slidelint-edit-button"></span>
						</span>						
						<div class="slide-link-toolbar">
							<?php
							if($slidelistID != $this->slideID && !in_array($this->slideID, $arrChildrenIDs)){
								?>
								<a class="slide-link-toolbar-button slide-moveto" href="#"><span class=""><i class="eg-icon-forward"></i><span><?php echo  JText::_("Copy / Move",'revslider'); ?></span></span></a>
								<?php
							}
							?>
							<a class="slide-link-toolbar-button slide-duplicate" href="#"><span class=""><i class="eg-icon-picture"></i><span><?php echo  JText::_("Duplicate",'revslider'); ?></span></span></a>
							<a class="slide-link-toolbar-button slide-add-as-template" href="#"><span class=""><i class="eg-icon-menu"></i><span><?php echo  JText::_("Add to Templates",'revslider'); ?></span></span></a>
							<a class="slide-link-toolbar-button slide-remove" href="#"><span class=""><i class="eg-icon-trash"></i><span><?php echo  JText::_("Delete",'revslider'); ?></span></span></a>
						</div>
					</div>
					
				</li>
				<?php
			}
			?>
			<li class="eg-drag-disabled">
				<a href="javascript:void(0);" class="add_slide">
					<div class="slide-media-container" style="border:1px dashed #ddd; border-bottom:none;">
						<i style="position:absolute; top:50%;left:50%; font-size:25px; color:#ddd;margin-left:-17px;margin-top:-7px;" class="eg-icon-plus"></i>
					</div>
					<div class="slide-link-content">
						<span class="slide-link" style="width:100%;text-align: center;font-weight:600;"><?php echo  JText::_("Add Slide",'revslider'); ?></span>
					</div>
				</a>
				<script>
					jQuery(document).ready(function() {
						jQuery("#link_add_bulk_slide").fancybox({
							width: '70%',height: '70%',fitToView:true,autoSize:true
						});
					});
				</script>
				<div class="slide-link-content">
					<div class="slide-link-toolbar">
						<a id="link_add_slide" href="javascript:void(0);" class="slide-link-toolbar-button"><span class="slide-add"><i class="eg-icon-picture-1" style="margin-right:5px"></i><span><?php echo  JText::_("Add Blank Slide", 'revslider'); ?></span></span></a>
						<a id="link_add_bulk_slide" href="<?php echo JUri::root() ?>/administrator/index.php?option=com_media&amp;tmpl=component&amp;view=images&amp;asset=com_zt_layerslider&amp;folder=layerslider&amp;fieldid=bulk_image_import" class="slide-link-toolbar-button fancybox fancybox.iframe"><span class="slide-add"><i class="eg-icon-picture" style="margin-right:5px"></i><span><?php echo  JText::_("Add Bulk Slides", 'revslider'); ?></span></span></a>
						<input type="hidden" name="bulk_image_import" id="bulk_image_import" value="">
						<a id="rs_copy_slide_from_slider" href="javascript:void(0);" class="slide-link-toolbar-button">
							<span class="slide-copy-from-slider"><i class="eg-icon-menu" style="margin-right:5px"></i><span><?php echo  JText::_("Add from Template", 'revslider'); ?></span></span>
						</a>
					</div>
					<span class="slide-link" style="text-align:center">
						<?php echo  JText::_("Add Slide", 'revslider'); ?>
					</span>
				</div>
				<div class="small-triangle-bar"></div>
			</li>

			<li>
				<div id="loader_add_slide" class="loader_round" style="display:none"></div>
			</li>
		</ul>
		<div class="clear"></div>
	</div>
</div>
<script>
	jQuery("document").ready(function() {
		jQuery('.list_slide_links li').each(function() {
			var li=jQuery(this);
			
			li.hover(function() {
				var li = jQuery(this),
					tb = li.find('.slide-link-toolbar');
				li.removeClass("nothovered");						
				tb.show();
			}, function() {
				var li = jQuery(this),
					tb = li.find('.slide-link-toolbar');
				li.addClass("nothovered");	
				if (!li.hasClass("infocus"))					
					tb.hide();
			})
		});
		
		var oldslidetitle = "";
		
		jQuery('.slidetitleinput').focus(function() {
			oldslidetitle=jQuery(this).val();
			jQuery(this).closest("li").addClass("infocus");
		}).blur(function() {
			jQuery(this).val(oldslidetitle);
			var li = jQuery(this).closest("li")
			li.removeClass("infocus");
			if (li.hasClass("nothovered")) {
				tb = li.find('.slide-link-toolbar');
				tb.hide();
			}
		});
		function nichange() {
			var titleinp = jQuery(this),
				slide_title = titleinp.val(),
				slide_id = jQuery(this).closest('li').attr('id').replace('slidelist_item_', '');

			oldslidetitle = slide_title;
			titleinp.blur();
			if(UniteAdminRev.sanitize_input(slide_title) == ''){
				alert('<?php echo  JText::_('Slide name should not be empty', 'revslider'); ?>');
				return false;
			}

			var data = {slideID:slide_id,slideTitle:slide_title};

			UniteAdminRev.ajaxRequest('slider.changeslidetitle', data, function(response){});

			if(jQuery(this).closest('li').hasClass('selected')){ //set input field to new value
				jQuery('input[name="title"]').val(slide_title);
			}
		}
		jQuery('.slidetitleinput').on("change",nichange);
		
		jQuery('.slidelint-edit-button').click(function() {
			jQuery('.slidetitleinput').unbind("change");
			var titleinp = jQuery(this).siblings('.slidetitleinput'),
				slide_title = titleinp.val(),
				slide_id = jQuery(this).closest('li').attr('id').replace('slidelist_item_', '');

			oldslidetitle = slide_title;
			titleinp.blur();
			if(UniteAdminRev.sanitize_input(slide_title) == ''){
				alert('<?php echo  JText::_('Slide name should not be empty', 'revslider'); ?>');
				return false;
			}

			var data = {slideID:slide_id,slideTitle:slide_title};

			UniteAdminRev.ajaxRequest('slider.changeslidetitle', data, function(response){});

			if(jQuery(this).closest('li').hasClass('selected')){ //set input field to new value
				jQuery('input[name="title"]').val(slide_title);
			}
			jQuery('.slidetitleinput').bind("change",nichange);
		});


		// OPEN THE TEMPLATE LIST ON CLICK OF ADD SLIDE TEMPLATE
		jQuery('#rs_copy_slide_from_slider').click(function() {
			jQuery('#template_area').addClass("show");
			scrollTA();
			return true;
		});

	});
	
	
</script>