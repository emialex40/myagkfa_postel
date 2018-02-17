<?php
/**
 * Created by PhpStorm.
 * User: Thuan
 * Date: 5/9/2016
 * Time: 2:19 PM
 */
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$user = JFactory::getUser();
$_slides = new ZTSliderSlide();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;
$saveOrder = $listOrder == 'a.ordering';
if ($saveOrder)
{
    $saveOrderingUrl = 'index.php?option=com_zt_layerslider&task=sliders.saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'sliderList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$sortFields = $this->getSortFields();
$addNewLink = JRoute::_('index.php?option=com_zt_layerslider&view=slider&layout=edit',false);
?>
    <ul class="tp-list_sliders">
	<?php
	if(!empty($this->items)){

        $useSliders = $this->items;

        foreach($this->items as $slider){

            try{
                $errorMessage = '';
                    $silder_helper = new ZtSliderSlider($slider->id);

                $id = $slider->id;
                $title = $showTitle = $slider->title;
                $alias = $slider->alias;
                $isFromPosts = $silder_helper->isSlidesFromPosts();
                $isFromStream = $silder_helper->isSlidesFromStream();
                $strSource = JText::_("COM_ZT_LAYERSLIDER_GALLERY");
                $preicon = "revicon-picture-1";

                $is_favorite = $slider->favourite ? true :false;

                $shortCode = $silder_helper->getShortcode();
                $numSlides = $silder_helper->getNumSlides();
                $numReal = '';

                $rowClass = "";
                $slider_type = 'gallery';
                if($isFromPosts == true){
                    $strSource = JText::_("COM_ZT_LAYERSLIDER_POSTS");
                    $preicon ="revicon-doc";
                    $rowClass = "class='row_alt'";
                    $numReal = $silder_helper->getNumRealSlides();
                    $slider_type = 'posts';
                }elseif($isFromStream !== false){
                    $strSource = JText::_("COM_ZT_LAYERSLIDER_SOCIALS");
                    $preicon ="revicon-doc";
                    $rowClass = "class='row_alt'";
                    switch($isFromStream){
                        case 'facebook':
                            $strSource = JText::_("COM_ZT_LAYERSLIDER_FACEBOOK_SOURCE");
                            $preicon ="eg-icon-facebook";
                            $numReal = $silder_helper->getNumRealSlides(false, 'facebook');
                            $slider_type = 'facebook';
                            break;
                        case 'twitter':
                            $strSource = JText::_("COM_ZT_LAYERSLIDER_TWITTER_SOURCE");
                            $preicon ="eg-icon-twitter";
                            $numReal = $silder_helper->getNumRealSlides(false, 'twitter');
                            $slider_type = 'twitter';
                            break;
                        case 'instagram':
                            $strSource = JText::_("COM_ZT_LAYERSLIDER_INSTAGRAM_SOURCE");
                            $preicon ="eg-icon-info";
                            $numReal = $silder_helper->getNumRealSlides(false, 'instagram');
                            $slider_type = 'instagram';
                            break;
                        case 'flickr':
                            $strSource = JText::_("COM_ZT_LAYERSLIDER_FLICKR_SOURCE");
                            $preicon ="eg-icon-flickr";
                            $numReal = $silder_helper->getNumRealSlides(false, 'flickr');
                            $slider_type = 'flickr';
                            break;
                        case 'youtube':
                            $strSource = JText::_("COM_ZT_LAYERSLIDER_YOUTUBE_SOURCE");
                            $preicon ="eg-icon-youtube";
                            $numReal = $silder_helper->getNumRealSlides(false, 'youtube');
                            $slider_type = 'youtube';
                            break;
                        case 'vimeo':
                            $strSource = JText::_("COM_ZT_LAYERSLIDER_VIMEO_SOURCE");
                            $preicon ="eg-icon-vimeo";
                            $numReal = $silder_helper->getNumRealSlides(false, 'vimeo');
                            $slider_type = 'vimeo';
                            break;

                    }

                }

                $first_slide_image_thumb = array('url' => '', 'class' => 'mini-transparent', 'style' => '');

                if(intval($numSlides) == 0){
                    $first_slide_id = 'new&slider_id='.$id;


                }else{
                    $slides = $silder_helper->getSlides(false);

                    if(!empty($slides)){
                        $first_slide_id = $silder_helper->getFirstID();

                        $first_slide_image_thumb = $_slides->get_image_attributes($slider_type,$first_slide_id);
                    }else{
                        $first_slide_id = 'new&slider_id='.$id;
                    }
                }

                $editLink = JRoute::_('index.php?option=com_zt_layerslider&task=slider.edit&id=' . $id,false);

                $editSlidesLink = JRoute::_('index.php?option=com_zt_layerslider&view=slides&id=' . $first_slide_id,false);

                $showTitle = $silder_helper->getHtmlLink($editLink, $showTitle);

            }catch(Exception $e){
                $errorMessage = "ERROR: ".$e->getMessage();
                $strSource = "";
                $numSlides = "";
                $isFromPosts = false;
            }

            ?>
            <li class="tls-slide tls-stype-all tls-stype-<?php echo $slider_type; ?>" data-favorit="<?php echo ($is_favorite) ? 'a' : 'b'; ?>" data-id="<?php echo $id; ?>" data-name="<?php echo $title; ?>" data-type="<?php echo $slider_type; ?>">
                <div class="tls-main-metas">

                    <span class="tls-firstslideimage <?php echo (is_array($first_slide_image_thumb) and !empty($first_slide_image_thumb)) ? $first_slide_image_thumb['class'] : ''; ?>" style="<?php echo (is_array($first_slide_image_thumb) and !empty($first_slide_image_thumb)) ? $first_slide_image_thumb['style'] : ''; ?>;<?php if (!empty($first_slide_image_thumb['url'])) {?>background-image:url( <?php echo $first_slide_image_thumb['url']; ?>) <?php } ?>"></span>
                    <a href="<?php echo $editSlidesLink; ?>" class="tls-grad-bg tls-bg-top"></a>
                    <span class="tls-source"><?php echo "<i class=".$preicon."></i>".$strSource; ?></span>
                    <span class="tls-star"><a href="javascript:void(0);" class="rev-toogle-fav" id="reg-toggle-id-<?php echo $id; ?>"><i class="eg-icon-star<?php echo ($is_favorite) ? '' : '-empty'; ?>"></i></a></span>
                    <span class="tls-slidenr"><?php echo $numSlides; if($numReal !== '') echo ' ('.$numReal.')'; ?></span>

					<span class="tls-title-wrapper">
						<span class="tls-id">#<?php echo $id; ?><span id="slider_title_<?php echo $id; ?>" class="hidden"><?php echo $title; ?></span><span class="tls-alias hidden" ><?php echo $alias; ?></span></span>
						<span class="tls-title"><?php echo $showTitle; ?>
                            <?php if(!empty($errorMessage)){ ?>
                                <span class='error_message'><?php echo $errorMessage; ?></span>
                            <?php } ?>
						</span>
						<a class="button-primary tls-settings" href='<?php echo $editLink; ?>'><i class="revicon-cog"></i></a>
						<a class="button-primary tls-editslides" href='<?php echo $editSlidesLink; ?>'><i class="revicon-pencil-1"></i></a>
						<span class="button-primary tls-showmore"><i class="eg-icon-down-open"></i></span>

					</span>

                </div>

                <div class="tls-hover-metas">
                    <!--<span class="tls-shortcode"><?php echo $shortCode; ?></span>-->
                    <span class="button-primary rs-embed-slider" ><i class="eg-icon-plus"></i><?php echo JText::_("COM_ZT_LAYERSLIDER_EMBED_SLIDER"); ?></span>
                    <a class="button-primary  export_slider_overview" id="export_slider_<?php echo $id; ?>" href="javascript:void(0);" ><i class="revicon-export"></i><?php echo JText::_("COM_ZT_LAYERSLIDER_EXPORT"); ?></a>
                    <?php

                    $com_zt_params = JComponentHelper::getParams('com_zt_layerslider');
                    $show_dev_export = $com_zt_params->get('show_dev_export',0);

                    if($show_dev_export){
                        ?>
                        <a class="button-primary  export_slider_standalone" id="export_slider_standalone_<?php echo $id; ?>" href="javascript:void(0);" ><i class="revicon-export"></i><?php echo JText::_("COM_ZT_LAYERSLIDER_EXPORT_TO_HTML"); ?></a>
                        <?php
                    }
                    ?>
                    <a class="button-primary  button_delete_slider" id="button_delete_<?php echo $id; ?>" href='javascript:void(0)'><i class="revicon-trash"></i><?php echo JText::_("COM_ZT_LAYERSLIDER_DELETE"); ?></a>
                    <a class="button-primary  button_duplicate_slider" id="button_duplicate_<?php echo $id; ?>" href='javascript:void(0)'><i class="revicon-picture"></i><?php echo JText::_("COM_ZT_LAYERSLIDER_DUPPLICATE"); ?></a>
                    <div id="button_preview_<?php echo $id; ?>" class="button_slider_preview button-primary revgray"><i class="revicon-search-1"></i><?php echo JText::_("COM_ZT_LAYERSLIDER_PREVIEW"); ?></div>
                </div>
                <div class="tls-dimmme"></div>
            </li>

            <?php
        }
    }
	?>

<li class="tls-slide tls-addnewslider">
    <a href='<?php echo $addNewLink; ?>'>
			<span class="tls-main-metas">
				<span class="tls-new-icon-wrapper">
					<span class="slider_list_add_buttons add_new_slider_icon"></span>
				</span>
				<span class="tls-title-wrapper">
					<span class="tls-title"><?php echo JText::_('COM_ZT_LAYERSLIDER_NEW_SLIDER'); ?></span>
				</span>
			</span>
    </a>
</li>

<?php //if(!RevSliderFunctionsWP::isAdminUser() && apply_filters('revslider_restrict_role', true)){ }else{ ?>
    <li class="tls-slide tls-addnewslider">
        <a href="javascript:void(0);" id="button_import_slider">
				<span class="tls-main-metas">
					<span class="tls-new-icon-wrapper">
						<i class="slider_list_add_buttons  add_new_import_icon"></i>
					</span>
					<span class="tls-title-wrapper">
						<span class="tls-title"><?php echo JText::_('COM_ZT_LAYERSLIDER_IMPORT_SLIDER');?></span>
					</span>
				</span>
        </a>
    </li>
<?php //} ?>
</ul>

<script>
    jQuery(document).on("ready",function() {
        jQuery('.tls-showmore').click(function() {
            jQuery(this).closest('.tls-slide').find('.tls-hover-metas').show();
            var elements = jQuery('.tls-slide:not(.hovered) .tls-dimmme');
            punchgs.TweenLite.to(elements,0.5,{autoAlpha:0.6,overwrite:"all",ease:punchgs.Power3.easeInOut});
            punchgs.TweenLite.to(jQuery(this).find('.tls-dimmme'),0.3,{autoAlpha:0,overwrite:"all",ease:punchgs.Power3.easeInOut})
        })

        jQuery('.tls-slide').hover(function() {
            jQuery(this).addClass("hovered");
        }, function() {
            var elements = jQuery('.tls-slide .tls-dimmme');
            punchgs.TweenLite.to(elements,0.5,{autoAlpha:0,overwrite:"auto",ease:punchgs.Power3.easeInOut});
            jQuery(this).removeClass("hovered");
            jQuery(this).find('.tls-hover-metas').hide();
        });


    })

    jQuery('#filter-sliders').on("change",function() {
        jQuery('.tls-slide').hide();
        jQuery('.tls-stype-'+jQuery(this).val()).show();
        jQuery('.tls-addnewslider').show();
    })

    function sort_li(a, b){
        return (jQuery(b).data(jQuery('#sort-sliders').val())) < (jQuery(a).data(jQuery('#sort-sliders').val())) ? 1 : -1;
    }

    jQuery('#sort-sliders').on('change',function() {
        jQuery(".tp-list_sliders li").sort(sort_li).appendTo('.tp-list_sliders');
        jQuery('.tls-addnewslider').appendTo('.tp-list_sliders');
    });

    jQuery('.slider-lg-views').click(function() {
        var tls =jQuery('.tp-list_sliders'),
            t = jQuery(this);
        jQuery('.slider-lg-views').removeClass("active");
        jQuery(this).addClass("active");
        tls.removeClass("rs-listview");
        tls.removeClass("rs-gridview");
        tls.addClass(t.data('type'));
    })

</script>