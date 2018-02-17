<?php
/**
 * @package ZT Sitemap
 * @author    ZooTemplate.com
 * @copyright(C) 2015 - ZooTemplate.com
 * @license PHP files are GNU/GPL
 **/
// no direct access
defined('_JEXEC') or die;

JFactory::getDocument()->addStyleSheet(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/css/buttons.css');
?>

<style>
    .ui-dialog .ui-dialog-titlebar-close {top:40%;}
    .tp-slider-new-dialog-title .ui-button.ui-widget.ui-dialog-titlebar-close,h3 .ui-state-default, h3 .ui-widget-content .ui-state-default,h3  .ui-widget-header .ui-state-default {background: none;border:0;}
    .ui-widget-content {border: 0}
    .ui-dialog .ui-dialog-buttonpane button {background: #0085ba;border-color:#0073aa #006799 #006799;color:#fff}
    .ui-button .ui-button-text {line-height: 10px;}
</style>
<?php
//get input
$this->slideID = ZTSliderFunctions::getGetVar("id");
$cssParser = new ZTSliderCssParser();

if($this->slideID == 'new'){ //add new transparent slide
    $sID = intval(ZTSliderFunctions::getGetVar("slider_id"));
    if($sID > 0){
        $revs = new ZTSlider();
        $revs->initByID($sID);
        //check if we already have slides, if yes, go to first
        $arrS = $revs->getSlides(false);
        if(empty($arrS)){
            $this->slideID = $revs->createSlideFromData(array('sliderid'=>$sID),true);
        }else{
            $this->slideID = key($arrS);
        }
    }
}

$this->patternViewSlide = JRoute::_('index.php?option=com_zt_layerslider&view=slides&id=[slideid]',false);

//init slide object
$slide = new ZTSlide();
$slide->initByID($this->slideID);
$this->slide = $slide;

$slideParams = $slide->getParams();
$this->slideParams =$slideParams ;

$this->operations = new ZTSliderOperations();

$this->rs_nav = new ZTSliderNavigation();
$this->arr_navigations = $this->rs_nav->get_all_navigations();

//init slider object
$this->sliderID = $slide->getSliderID();
$this->slider = new ZTSlider();
$this->slider->initByID($this->sliderID);
$this->sliderParams = $this->slider->getParams();
$this->arrSlideNames = $this->slider->getArrSlideNames();

$this->arrSlides = $this->slider->getSlides(false);
$this->arrSlides = $this->arrSlides;

$this->arrSliders = $this->slider->getArrSlidersShort($this->sliderID);
$this->arrSlidersFull = $this->slider->getArrSlidersShort();
$this->selectSliders = ZTSliderFunctions::getHTMLSelect($this->arrSliders,"","id='selectSliders'",true);

//check if slider is template
$this->sliderTemplate = $this->slider->getParam("template","false");

//set slide delay
$this->sliderDelay = $this->slider->getParam("delay","9000");
$this->slideDelay = $slide->getParam("delay","");
if(empty($this->slideDelay))
    $this->slideDelay = $this->sliderDelay;

//add tools.min.js
JFactory::getDocument()->addScript(ZT_ASSETS_URL .'/assets/js/jquery.themepunch.tools.min.js');

$this->arrLayers = $slide->getLayers();

//set Layer settings
$this->cssContent = $this->operations->getCaptionsContent();

$this->arrCaptionClasses = $this->operations->getArrCaptionClasses($this->cssContent);
//$this->arrCaptionClassesSorted = $this->operations->getArrCaptionClasses($this->cssContent);
$this->arrCaptionClassesSorted = $cssParser->get_captions_sorted();

$this->arrFontFamily = $this->operations->getArrFontFamilys($this->slider);
$this->arrCSS = $this->operations->getCaptionsContentArray();

$this->arrAnim = $this->operations->getFullCustomAnimations();
$this->arrAnimDefaultIn = $this->operations->getArrAnimations(false);
$this->arrAnimDefaultOut = $this->operations->getArrEndAnimations(false);

$this->arrAnimDefault = array_merge($this->arrAnimDefaultIn, $this->arrAnimDefaultOut);

//set various parameters needed for the page
$this->width = $this->sliderParams["width"];
$this->height = $this->sliderParams["height"];

$imageUrl = $slide->getImageUrl();
$this->imageUrl = $imageUrl;
$imageID = $slide->getImageID();
$this->imageID = $imageID;

$this->slider_type = $this->slider->getParam('source_type','gallery');
$this->slider_type = $this->slider_type;

/**
 * Get Slider params which will be used as default on Slides
 * @since: 5.0
 **/
$this->def_background_fit = $this->slider->getParam('def-background_fit', 'cover');
$this->def_image_source_type = $this->slider->getParam('def-image_source_type', 'full');
$this->def_bg_fit_x = $this->slider->getParam('def-bg_fit_x', '100');
$this->def_bg_fit_y = $this->slider->getParam('def-bg_fit_y', '100');
$this->def_bg_position = $this->slider->getParam('def-bg_position', 'center center');
$this->def_bg_position_x = $this->slider->getParam('def-bg_position_x', '0');
$this->def_bg_position_y = $this->slider->getParam('def-bg_position_y', '0');
$this->def_bg_repeat = $this->slider->getParam('def-bg_repeat', 'no-repeat');
$this->def_kenburn_effect = $this->slider->getParam('def-kenburn_effect', 'off');
$this->def_kb_start_fit = $this->slider->getParam('def-kb_start_fit', '100');
$this->def_kb_easing = $this->slider->getParam('def-kb_easing', 'Linear.easeNone');
$this->def_kb_end_fit = $this->slider->getParam('def-kb_end_fit', '100');
$this->def_kb_duration = $this->slider->getParam('def-kb_duration', '10000');
$def_transition = $this->slider->getParam('def-slide_transition', 'fade');
$this->def_transition=$def_transition;
$def_transition_duration = $this->slider->getParam('def-transition_duration', 'default');
$this->def_transition_duration = $def_transition_duration;

$this->def_use_parallax = $this->slider->getParam('use_parallax', 'on');

/* NEW KEN BURN INPUTS */
$this->def_kb_start_offset_x = $this->slider->getParam('def-kb_start_offset_x', '0');
$def_kb_start_offset_y = $this->slider->getParam('def-kb_start_offset_y', '0');
$def_kb_end_offset_x = $this->slider->getParam('def-kb_end_offset_x', '0');
$def_kb_end_offset_y = $this->slider->getParam('def-kb_end_offset_y', '0');
$def_kb_start_rotate = $this->slider->getParam('def-kb_start_rotate', '0');
$def_kb_end_rotate = $this->slider->getParam('def-kb_end_rotate', '0');
/* END OF NEW KEN BURN INPUTS */

$this->imageFilename = $slide->getImageFilename();

$style = "height:".$this->height."px;"; //
$this->style = $style;

$divLayersWidth = "width:".$this->width."px;";
$this->divLayersWidth = $divLayersWidth;
$divbgminwidth = "min-width:".$this->width."px;";
$this->divbgminwidth = $divbgminwidth;
$maxbgwidth = "max-width:".$this->width."px;";
$this->maxbgwidth = $maxbgwidth;

//set iframe parameters
$iframeWidth = $this->width+60;
$iframeHeight = $this->height+50;

$this->iframeStyle = "width:".$iframeWidth."px;height:".$iframeHeight."px;";

$this->closeUrl = JRoute::_('index.php?option=com_zt_layerslider&view=slides&slider_id='.$this->sliderID,false);

$this->jsonLayers = ZTSliderFunctions::jsonEncodeForClientSide($this->arrLayers);
$this->jsonFontFamilys = ZTSliderFunctions::jsonEncodeForClientSide($this->arrFontFamily);
$this->jsonCaptions = ZTSliderFunctions::jsonEncodeForClientSide($this->arrCaptionClassesSorted);

$this->arrCssStyles = ZTSliderFunctions::jsonEncodeForClientSide($this->arrCSS);

$this->arrCustomAnim = ZTSliderFunctions::jsonEncodeForClientSide($this->arrAnim);
$this->arrCustomAnimDefault = ZTSliderFunctions::jsonEncodeForClientSide($this->arrAnimDefault);

//bg type params
$bgType = ZTSliderFunctions::getVal($slideParams, 'background_type', 'image');
$this->bgType = $bgType;
$this->slideBGColor = ZTSliderFunctions::getVal($slideParams, 'slide_bg_color', '#E7E7E7');
$this->divLayersClass = "slide_layers";

$this->meta_handle = ZTSliderFunctions::getVal($slideParams, 'meta_handle','');

$this->bgFit = ZTSliderFunctions::getVal($slideParams, 'bg_fit', $this->def_background_fit);
$this->bgFitX = intval(ZTSliderFunctions::getVal($slideParams, 'bg_fit_x', $this->def_bg_fit_x));
$this->bgFitY = intval(ZTSliderFunctions::getVal($slideParams, 'bg_fit_y', $this->def_bg_fit_y));

$this->bgPosition = ZTSliderFunctions::getVal($slideParams, 'bg_position', $this->def_bg_position);
$this->bgPositionX = intval(ZTSliderFunctions::getVal($slideParams, 'bg_position_x', $this->def_bg_position_x));
$this->bgPositionY = intval(ZTSliderFunctions::getVal($slideParams, 'bg_position_y', $this->def_bg_position_y));

$this->slide_parallax_level = ZTSliderFunctions::getVal($slideParams, 'slide_parallax_level', '-');
$kenburn_effect = ZTSliderFunctions::getVal($slideParams, 'kenburn_effect', $this->def_kenburn_effect);
$this->kenburn_effect = $kenburn_effect;
$kb_duration = ZTSliderFunctions::getVal($slideParams, 'kb_duration', $this->def_kb_duration);
$this->kb_duration = $kb_duration;
$kb_easing = ZTSliderFunctions::getVal($slideParams, 'kb_easing', $this->def_kb_easing);
$this->kb_easing = $kb_easing;
$kb_start_fit = ZTSliderFunctions::getVal($slideParams, 'kb_start_fit', $this->def_kb_start_fit);
$this->kb_start_fit = $kb_start_fit;
$kb_end_fit = ZTSliderFunctions::getVal($slideParams, 'kb_end_fit', $this->def_kb_end_fit);
$this->kb_end_fit = $kb_end_fit;

$ext_width = ZTSliderFunctions::getVal($slideParams, 'ext_width', '1920');
$this->ext_width = $ext_width;
$ext_height = ZTSliderFunctions::getVal($slideParams, 'ext_height', '1080');
$this->ext_height= $ext_height;
$use_parallax = ZTSliderFunctions::getVal($slideParams, 'use_parallax', $this->def_use_parallax);
$this->use_parallax = $use_parallax;

$parallax_level[] =  ZTSliderFunctions::getVal($this->sliderParams,"parallax_level_1","5");
$parallax_level[] =  ZTSliderFunctions::getVal($this->sliderParams,"parallax_level_2","10");
$parallax_level[] =  ZTSliderFunctions::getVal($this->sliderParams,"parallax_level_3","15");
$parallax_level[] =  ZTSliderFunctions::getVal($this->sliderParams,"parallax_level_4","20");
$parallax_level[] =  ZTSliderFunctions::getVal($this->sliderParams,"parallax_level_5","25");
$parallax_level[] =  ZTSliderFunctions::getVal($this->sliderParams,"parallax_level_6","30");
$parallax_level[] =  ZTSliderFunctions::getVal($this->sliderParams,"parallax_level_7","35");
$parallax_level[] =  ZTSliderFunctions::getVal($this->sliderParams,"parallax_level_8","40");
$parallax_level[] =  ZTSliderFunctions::getVal($this->sliderParams,"parallax_level_9","45");
$parallax_level[] =  ZTSliderFunctions::getVal($this->sliderParams,"parallax_level_10","45");
$parallax_level[] =  ZTSliderFunctions::getVal($this->sliderParams,"parallax_level_11","46");
$parallax_level[] =  ZTSliderFunctions::getVal($this->sliderParams,"parallax_level_12","47");
$parallax_level[] =  ZTSliderFunctions::getVal($this->sliderParams,"parallax_level_13","48");
$parallax_level[] =  ZTSliderFunctions::getVal($this->sliderParams,"parallax_level_14","49");
$parallax_level[] =  ZTSliderFunctions::getVal($this->sliderParams,"parallax_level_15","50");
$parallax_level[] =  ZTSliderFunctions::getVal($this->sliderParams,"parallax_level_16","55");
$this->parallax_level = $parallax_level;

$parallaxisddd = ZTSliderFunctions::getVal($this->sliderParams,"ddd_parallax","off");
$this->parallaxisddd = $parallaxisddd;

$parallaxbgfreeze = ZTSliderFunctions::getVal($this->sliderParams,"ddd_parallax_bgfreeze","off");
$this->parallaxbgfreeze = $parallaxbgfreeze;


$this->slideBGYoutube = ZTSliderFunctions::getVal($slideParams, 'slide_bg_youtube', '');
$this->slideBGVimeo = ZTSliderFunctions::getVal($slideParams, 'slide_bg_vimeo', '');
$this->slideBGhtmlmpeg = ZTSliderFunctions::getVal($slideParams, 'slide_bg_html_mpeg', '');
$this->slideBGhtmlwebm = ZTSliderFunctions::getVal($slideParams, 'slide_bg_html_webm', '');
$this->slideBGhtmlogv = ZTSliderFunctions::getVal($slideParams, 'slide_bg_html_ogv', '');

$this->stream_do_cover = ZTSliderFunctions::getVal($slideParams, 'stream_do_cover', 'on');
$this->stream_do_cover_both = ZTSliderFunctions::getVal($slideParams, 'stream_do_cover_both', 'on');

$this->video_force_cover = ZTSliderFunctions::getVal($slideParams, 'video_force_cover', 'on');
$this->video_dotted_overlay = ZTSliderFunctions::getVal($slideParams, 'video_dotted_overlay', 'none');
$this->video_ratio = ZTSliderFunctions::getVal($slideParams, 'video_ratio', 'none');
$this->video_loop = ZTSliderFunctions::getVal($slideParams, 'video_loop', 'none');
$this->video_nextslide = ZTSliderFunctions::getVal($slideParams, 'video_nextslide', 'off');
$this->video_allowfullscreen = ZTSliderFunctions::getVal($slideParams, 'video_allowfullscreen', 'on');
$this->video_force_rewind = ZTSliderFunctions::getVal($slideParams, 'video_force_rewind', 'on');
$this->video_speed = ZTSliderFunctions::getVal($slideParams, 'video_speed', '1');
$this->video_mute = ZTSliderFunctions::getVal($slideParams, 'video_mute', 'on');
$this->video_volume = ZTSliderFunctions::getVal($slideParams, 'video_volume', '100');
$this->video_start_at = ZTSliderFunctions::getVal($slideParams, 'video_start_at', '');
$this->video_end_at = ZTSliderFunctions::getVal($slideParams, 'video_end_at', '');
$this->video_arguments = ZTSliderFunctions::getVal($slideParams, 'video_arguments', ZtSliderSlider::DEFAULT_YOUTUBE_ARGUMENTS);
$this->video_arguments_vim = ZTSliderFunctions::getVal($slideParams, 'video_arguments_vimeo', ZtSliderSlider::DEFAULT_VIMEO_ARGUMENTS);

/* NEW KEN BURN INPUTS */
$kbStartOffsetX = intval(ZTSliderFunctions::getVal($slideParams, 'kb_start_offset_x', $this->def_kb_start_offset_x));
$this->kbStartOffsetX = $kbStartOffsetX;
$kbStartOffsetY = intval(ZTSliderFunctions::getVal($slideParams, 'kb_start_offset_y', $def_kb_start_offset_y));
$this->kbStartOffsetY = $kbStartOffsetY;
$kbEndOffsetX = intval(ZTSliderFunctions::getVal($slideParams, 'kb_end_offset_x', $def_kb_end_offset_x));
$this->kbEndOffsetX = $kbEndOffsetX;
$kbEndOffsetY = intval(ZTSliderFunctions::getVal($slideParams, 'kb_end_offset_y', $def_kb_end_offset_y));
$this->kbEndOffsetY = $kbEndOffsetY;
$kbStartRotate = intval(ZTSliderFunctions::getVal($slideParams, 'kb_start_rotate', $def_kb_start_rotate));
$this->kbStartRotate = $kbStartRotate;
$kbEndRotate = intval(ZTSliderFunctions::getVal($slideParams, 'kb_end_rotate', $def_kb_end_rotate));
$this->kbEndRotate = $kbEndRotate;
/* END OF NEW KEN BURN INPUTS*/

$bgRepeat = ZTSliderFunctions::getVal($slideParams, 'bg_repeat', $this->def_bg_repeat);
$this->bgRepeat = $bgRepeat;

$slideBGExternal = ZTSliderFunctions::getVal($slideParams, "slide_bg_external","");
$this->slideBGExternal =$slideBGExternal;

$img_sizes = ZTSliderFunctions::get_all_image_sizes($this->slider_type);
$this->img_sizes = $img_sizes;

$bg_image_size = ZTSliderFunctions::getVal($slideParams, 'image_source_type', $this->def_image_source_type);
$this->bg_image_size = $bg_image_size;

$style_wrapper = '';
$class_wrapper = '';


switch($bgType){
    case "trans":
        $this->divLayersClass = "slide_layers";
        $class_wrapper = "trans_bg";
        break;
    case "solid":
        $style_wrapper .= "background-color:".$this->slideBGColor.";";
        break;
    case "image":
        switch($this->slider_type){
            case 'posts':
                $imageUrl = ZT_ASSETS_PATH.'//assets/images/sources/post.png';
                break;
            case 'facebook':
                $imageUrl = ZT_ASSETS_PATH.'//assets/images/sources/fb.png';
                break;
            case 'twitter':
                $imageUrl = ZT_ASSETS_PATH.'//assets/images/sources/tw.png';
                break;
            case 'instagram':
                $imageUrl = ZT_ASSETS_PATH.'//assets/images/sources/ig.png';
                break;
            case 'flickr':
                $imageUrl = ZT_ASSETS_PATH.'//assets/images/sources/fr.png';
                break;
            case 'youtube':
                $imageUrl = ZT_ASSETS_PATH.'//assets/images/sources/yt.png';
                break;
            case 'vimeo':
                $imageUrl = ZT_ASSETS_PATH.'//assets/images/sources/vm.png';
                break;
        }
        $style_wrapper .= "background-image:url('".$imageUrl."');";
        if($this->bgFit == 'percentage'){
            $style_wrapper .= "background-size: ".$this->bgFitX.'% '.$this->bgFitY.'%;';
        }else{
            $style_wrapper .= "background-size: ".$this->bgFit.";";
        }
        if($this->bgPosition == 'percentage'){
            $style_wrapper .= "background-position: ".$this->bgPositionX.'% '.$this->bgPositionY.'%;';
        }else{
            $style_wrapper .= "background-position: ".$this->bgPosition.";";
        }
        $style_wrapper .= "background-repeat: ".$bgRepeat.";";
        break;
    case "external":
        $style_wrapper .= "background-image:url('".$slideBGExternal."');";
        if($this->bgFit == 'percentage'){
            $style_wrapper .= "background-size: ".$this->bgFitX.'% '.$this->bgFitY.'%;';
        }else{
            $style_wrapper .= "background-size: ".$this->bgFit.";";
        }
        if($this->bgPosition == 'percentage'){
            $style_wrapper .= "background-position: ".$this->bgPositionX.'% '.$this->bgPositionY.'%;';
        }else{
            $style_wrapper .= "background-position: ".$this->bgPosition.";";
        }
        $style_wrapper .= "background-repeat: ".$bgRepeat.";";
        break;
}

$this->style_wrapper = $style_wrapper;
$this->imageUrl = $imageUrl;
$this->class_wrapper = $class_wrapper;
$this->slideTitle = $slide->getParam("title","Slide");
$this->slideOrder = $slide->getOrder();

//treat multilanguage

$this->jsonStaticLayers = "";
if(!$slide->isStaticSlide()){

    //get static slide, check all layers and add them to the action list
    $this->static_slide_id = $slide->getStaticSlideID($this->sliderID);

    if($this->static_slide_id !== false){
        $this->static_slide = new ZtSlide();
        $this->static_slide->initByStaticID($this->static_slide_id);
        $this->static_layers = $this->static_slide->getLayers();
        $this->jsonStaticLayers = ZTSliderFunctions::jsonEncodeForClientSide($this->static_layers);
    }
}

?>
    <script type="text/javascript">

        /*
         * Copyright 2015 Small Batch, Inc.
         *
         * Licensed under the Apache License, Version 2.0 (the "License"); you may not
         * use this file except in compliance with the License. You may obtain a copy of
         * the License at
         *
         * http://www.apache.org/licenses/LICENSE-2.0
         *
         * Unless required by applicable law or agreed to in writing, software
         * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
         * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
         * License for the specific language governing permissions and limitations under
         * the License.
         */
        /* Web Font Loader v1.5.18 - (c) Adobe Systems, Google. License: Apache 2.0 */
        ;(function(window,document,undefined){function aa(a,b,c){return a.call.apply(a.bind,arguments)}function ba(a,b,c){if(!a)throw Error();if(2<arguments.length){var d=Array.prototype.slice.call(arguments,2);return function(){var c=Array.prototype.slice.call(arguments);Array.prototype.unshift.apply(c,d);return a.apply(b,c)}}return function(){return a.apply(b,arguments)}}function k(a,b,c){k=Function.prototype.bind&&-1!=Function.prototype.bind.toString().indexOf("native code")?aa:ba;return k.apply(null,arguments)}var n=Date.now||function(){return+new Date};function q(a,b){this.K=a;this.w=b||a;this.G=this.w.document}q.prototype.createElement=function(a,b,c){a=this.G.createElement(a);if(b)for(var d in b)b.hasOwnProperty(d)&&("style"==d?a.style.cssText=b[d]:a.setAttribute(d,b[d]));c&&a.appendChild(this.G.createTextNode(c));return a};function r(a,b,c){a=a.G.getElementsByTagName(b)[0];a||(a=document.documentElement);a&&a.lastChild&&a.insertBefore(c,a.lastChild)}function ca(a,b){function c(){a.G.body?b():setTimeout(c,0)}c()}
            function s(a,b,c){b=b||[];c=c||[];for(var d=a.className.split(/\s+/),e=0;e<b.length;e+=1){for(var f=!1,g=0;g<d.length;g+=1)if(b[e]===d[g]){f=!0;break}f||d.push(b[e])}b=[];for(e=0;e<d.length;e+=1){f=!1;for(g=0;g<c.length;g+=1)if(d[e]===c[g]){f=!0;break}f||b.push(d[e])}a.className=b.join(" ").replace(/\s+/g," ").replace(/^\s+|\s+$/,"")}function t(a,b){for(var c=a.className.split(/\s+/),d=0,e=c.length;d<e;d++)if(c[d]==b)return!0;return!1}
            function u(a){if("string"===typeof a.na)return a.na;var b=a.w.location.protocol;"about:"==b&&(b=a.K.location.protocol);return"https:"==b?"https:":"http:"}function v(a,b){var c=a.createElement("link",{rel:"stylesheet",href:b,media:"all"}),d=!1;c.onload=function(){d||(d=!0)};c.onerror=function(){d||(d=!0)};r(a,"head",c)}
            function w(a,b,c,d){var e=a.G.getElementsByTagName("head")[0];if(e){var f=a.createElement("script",{src:b}),g=!1;f.onload=f.onreadystatechange=function(){g||this.readyState&&"loaded"!=this.readyState&&"complete"!=this.readyState||(g=!0,c&&c(null),f.onload=f.onreadystatechange=null,"HEAD"==f.parentNode.tagName&&e.removeChild(f))};e.appendChild(f);window.setTimeout(function(){g||(g=!0,c&&c(Error("Script load timeout")))},d||5E3);return f}return null};function x(a,b){this.Y=a;this.ga=b};function y(a,b,c,d){this.c=null!=a?a:null;this.g=null!=b?b:null;this.D=null!=c?c:null;this.e=null!=d?d:null}var da=/^([0-9]+)(?:[\._-]([0-9]+))?(?:[\._-]([0-9]+))?(?:[\._+-]?(.*))?$/;y.prototype.compare=function(a){return this.c>a.c||this.c===a.c&&this.g>a.g||this.c===a.c&&this.g===a.g&&this.D>a.D?1:this.c<a.c||this.c===a.c&&this.g<a.g||this.c===a.c&&this.g===a.g&&this.D<a.D?-1:0};y.prototype.toString=function(){return[this.c,this.g||"",this.D||"",this.e||""].join("")};
            function z(a){a=da.exec(a);var b=null,c=null,d=null,e=null;a&&(null!==a[1]&&a[1]&&(b=parseInt(a[1],10)),null!==a[2]&&a[2]&&(c=parseInt(a[2],10)),null!==a[3]&&a[3]&&(d=parseInt(a[3],10)),null!==a[4]&&a[4]&&(e=/^[0-9]+$/.test(a[4])?parseInt(a[4],10):a[4]));return new y(b,c,d,e)};function A(a,b,c,d,e,f,g,h){this.N=a;this.k=h}A.prototype.getName=function(){return this.N};function B(a){this.a=a}var ea=new A("Unknown",0,0,0,0,0,0,new x(!1,!1));
            B.prototype.parse=function(){var a;if(-1!=this.a.indexOf("MSIE")||-1!=this.a.indexOf("Trident/")){a=C(this);var b=z(D(this)),c=null,d=E(this.a,/Trident\/([\d\w\.]+)/,1),c=-1!=this.a.indexOf("MSIE")?z(E(this.a,/MSIE ([\d\w\.]+)/,1)):z(E(this.a,/rv:([\d\w\.]+)/,1));""!=d&&z(d);a=new A("MSIE",0,0,0,0,0,0,new x("Windows"==a&&6<=c.c||"Windows Phone"==a&&8<=b.c,!1))}else if(-1!=this.a.indexOf("Opera"))a:if(a=z(E(this.a,/Presto\/([\d\w\.]+)/,1)),z(D(this)),null!==a.c||z(E(this.a,/rv:([^\)]+)/,1)),-1!=this.a.indexOf("Opera Mini/"))a=
                z(E(this.a,/Opera Mini\/([\d\.]+)/,1)),a=new A("OperaMini",0,0,0,C(this),0,0,new x(!1,!1));else{if(-1!=this.a.indexOf("Version/")&&(a=z(E(this.a,/Version\/([\d\.]+)/,1)),null!==a.c)){a=new A("Opera",0,0,0,C(this),0,0,new x(10<=a.c,!1));break a}a=z(E(this.a,/Opera[\/ ]([\d\.]+)/,1));a=null!==a.c?new A("Opera",0,0,0,C(this),0,0,new x(10<=a.c,!1)):new A("Opera",0,0,0,C(this),0,0,new x(!1,!1))}else/OPR\/[\d.]+/.test(this.a)?a=F(this):/AppleWeb(K|k)it/.test(this.a)?a=F(this):-1!=this.a.indexOf("Gecko")?
                (a="Unknown",b=new y,z(D(this)),b=!1,-1!=this.a.indexOf("Firefox")?(a="Firefox",b=z(E(this.a,/Firefox\/([\d\w\.]+)/,1)),b=3<=b.c&&5<=b.g):-1!=this.a.indexOf("Mozilla")&&(a="Mozilla"),c=z(E(this.a,/rv:([^\)]+)/,1)),b||(b=1<c.c||1==c.c&&9<c.g||1==c.c&&9==c.g&&2<=c.D),a=new A(a,0,0,0,C(this),0,0,new x(b,!1))):a=ea;return a};
            function C(a){var b=E(a.a,/(iPod|iPad|iPhone|Android|Windows Phone|BB\d{2}|BlackBerry)/,1);if(""!=b)return/BB\d{2}/.test(b)&&(b="BlackBerry"),b;a=E(a.a,/(Linux|Mac_PowerPC|Macintosh|Windows|CrOS|PlayStation|CrKey)/,1);return""!=a?("Mac_PowerPC"==a?a="Macintosh":"PlayStation"==a&&(a="Linux"),a):"Unknown"}
            function D(a){var b=E(a.a,/(OS X|Windows NT|Android) ([^;)]+)/,2);if(b||(b=E(a.a,/Windows Phone( OS)? ([^;)]+)/,2))||(b=E(a.a,/(iPhone )?OS ([\d_]+)/,2)))return b;if(b=E(a.a,/(?:Linux|CrOS|CrKey) ([^;)]+)/,1))for(var b=b.split(/\s/),c=0;c<b.length;c+=1)if(/^[\d\._]+$/.test(b[c]))return b[c];return(a=E(a.a,/(BB\d{2}|BlackBerry).*?Version\/([^\s]*)/,2))?a:"Unknown"}
            function F(a){var b=C(a),c=z(D(a)),d=z(E(a.a,/AppleWeb(?:K|k)it\/([\d\.\+]+)/,1)),e="Unknown",f=new y,f="Unknown",g=!1;/OPR\/[\d.]+/.test(a.a)?e="Opera":-1!=a.a.indexOf("Chrome")||-1!=a.a.indexOf("CrMo")||-1!=a.a.indexOf("CriOS")?e="Chrome":/Silk\/\d/.test(a.a)?e="Silk":"BlackBerry"==b||"Android"==b?e="BuiltinBrowser":-1!=a.a.indexOf("PhantomJS")?e="PhantomJS":-1!=a.a.indexOf("Safari")?e="Safari":-1!=a.a.indexOf("AdobeAIR")?e="AdobeAIR":-1!=a.a.indexOf("PlayStation")&&(e="BuiltinBrowser");"BuiltinBrowser"==
            e?f="Unknown":"Silk"==e?f=E(a.a,/Silk\/([\d\._]+)/,1):"Chrome"==e?f=E(a.a,/(Chrome|CrMo|CriOS)\/([\d\.]+)/,2):-1!=a.a.indexOf("Version/")?f=E(a.a,/Version\/([\d\.\w]+)/,1):"AdobeAIR"==e?f=E(a.a,/AdobeAIR\/([\d\.]+)/,1):"Opera"==e?f=E(a.a,/OPR\/([\d.]+)/,1):"PhantomJS"==e&&(f=E(a.a,/PhantomJS\/([\d.]+)/,1));f=z(f);g="AdobeAIR"==e?2<f.c||2==f.c&&5<=f.g:"BlackBerry"==b?10<=c.c:"Android"==b?2<c.c||2==c.c&&1<c.g:526<=d.c||525<=d.c&&13<=d.g;return new A(e,0,0,0,0,0,0,new x(g,536>d.c||536==d.c&&11>d.g))}
            function E(a,b,c){return(a=a.match(b))&&a[c]?a[c]:""};function G(a){this.ma=a||"-"}G.prototype.e=function(a){for(var b=[],c=0;c<arguments.length;c++)b.push(arguments[c].replace(/[\W_]+/g,"").toLowerCase());return b.join(this.ma)};function H(a,b){this.N=a;this.Z=4;this.O="n";var c=(b||"n4").match(/^([nio])([1-9])$/i);c&&(this.O=c[1],this.Z=parseInt(c[2],10))}H.prototype.getName=function(){return this.N};function I(a){return a.O+a.Z}function ga(a){var b=4,c="n",d=null;a&&((d=a.match(/(normal|oblique|italic)/i))&&d[1]&&(c=d[1].substr(0,1).toLowerCase()),(d=a.match(/([1-9]00|normal|bold)/i))&&d[1]&&(/bold/i.test(d[1])?b=7:/[1-9]00/.test(d[1])&&(b=parseInt(d[1].substr(0,1),10))));return c+b};function ha(a,b){this.d=a;this.q=a.w.document.documentElement;this.Q=b;this.j="wf";this.h=new G("-");this.ha=!1!==b.events;this.F=!1!==b.classes}function J(a){if(a.F){var b=t(a.q,a.h.e(a.j,"active")),c=[],d=[a.h.e(a.j,"loading")];b||c.push(a.h.e(a.j,"inactive"));s(a.q,c,d)}K(a,"inactive")}function K(a,b,c){if(a.ha&&a.Q[b])if(c)a.Q[b](c.getName(),I(c));else a.Q[b]()};function ia(){this.C={}};function L(a,b){this.d=a;this.I=b;this.o=this.d.createElement("span",{"aria-hidden":"true"},this.I)}
            function M(a,b){var c=a.o,d;d=[];for(var e=b.N.split(/,\s*/),f=0;f<e.length;f++){var g=e[f].replace(/['"]/g,"");-1==g.indexOf(" ")?d.push(g):d.push("'"+g+"'")}d=d.join(",");e="normal";"o"===b.O?e="oblique":"i"===b.O&&(e="italic");c.style.cssText="display:block;position:absolute;top:-9999px;left:-9999px;font-size:300px;width:auto;height:auto;line-height:normal;margin:0;padding:0;font-variant:normal;white-space:nowrap;font-family:"+d+";"+("font-style:"+e+";font-weight:"+(b.Z+"00")+";")}
            function N(a){r(a.d,"body",a.o)}L.prototype.remove=function(){var a=this.o;a.parentNode&&a.parentNode.removeChild(a)};function O(a,b,c,d,e,f,g,h){this.$=a;this.ka=b;this.d=c;this.m=d;this.k=e;this.I=h||"BESbswy";this.v={};this.X=f||3E3;this.ca=g||null;this.H=this.u=this.t=null;this.t=new L(this.d,this.I);this.u=new L(this.d,this.I);this.H=new L(this.d,this.I);M(this.t,new H("serif",I(this.m)));M(this.u,new H("sans-serif",I(this.m)));M(this.H,new H("monospace",I(this.m)));N(this.t);N(this.u);N(this.H);this.v.serif=this.t.o.offsetWidth;this.v["sans-serif"]=this.u.o.offsetWidth;this.v.monospace=this.H.o.offsetWidth}
            var P={sa:"serif",ra:"sans-serif",qa:"monospace"};O.prototype.start=function(){this.oa=n();M(this.t,new H(this.m.getName()+",serif",I(this.m)));M(this.u,new H(this.m.getName()+",sans-serif",I(this.m)));Q(this)};function R(a,b,c){for(var d in P)if(P.hasOwnProperty(d)&&b===a.v[P[d]]&&c===a.v[P[d]])return!0;return!1}
            function Q(a){var b=a.t.o.offsetWidth,c=a.u.o.offsetWidth;b===a.v.serif&&c===a.v["sans-serif"]||a.k.ga&&R(a,b,c)?n()-a.oa>=a.X?a.k.ga&&R(a,b,c)&&(null===a.ca||a.ca.hasOwnProperty(a.m.getName()))?S(a,a.$):S(a,a.ka):ja(a):S(a,a.$)}function ja(a){setTimeout(k(function(){Q(this)},a),50)}function S(a,b){a.t.remove();a.u.remove();a.H.remove();b(a.m)};function T(a,b,c,d){this.d=b;this.A=c;this.S=0;this.ea=this.ba=!1;this.X=d;this.k=a.k}function ka(a,b,c,d,e){c=c||{};if(0===b.length&&e)J(a.A);else for(a.S+=b.length,e&&(a.ba=e),e=0;e<b.length;e++){var f=b[e],g=c[f.getName()],h=a.A,m=f;h.F&&s(h.q,[h.h.e(h.j,m.getName(),I(m).toString(),"loading")]);K(h,"fontloading",m);h=null;h=new O(k(a.ia,a),k(a.ja,a),a.d,f,a.k,a.X,d,g);h.start()}}
            T.prototype.ia=function(a){var b=this.A;b.F&&s(b.q,[b.h.e(b.j,a.getName(),I(a).toString(),"active")],[b.h.e(b.j,a.getName(),I(a).toString(),"loading"),b.h.e(b.j,a.getName(),I(a).toString(),"inactive")]);K(b,"fontactive",a);this.ea=!0;la(this)};
            T.prototype.ja=function(a){var b=this.A;if(b.F){var c=t(b.q,b.h.e(b.j,a.getName(),I(a).toString(),"active")),d=[],e=[b.h.e(b.j,a.getName(),I(a).toString(),"loading")];c||d.push(b.h.e(b.j,a.getName(),I(a).toString(),"inactive"));s(b.q,d,e)}K(b,"fontinactive",a);la(this)};function la(a){0==--a.S&&a.ba&&(a.ea?(a=a.A,a.F&&s(a.q,[a.h.e(a.j,"active")],[a.h.e(a.j,"loading"),a.h.e(a.j,"inactive")]),K(a,"active")):J(a.A))};function U(a){this.K=a;this.B=new ia;this.pa=new B(a.navigator.userAgent);this.a=this.pa.parse();this.U=this.V=0;this.R=this.T=!0}
            U.prototype.load=function(a){this.d=new q(this.K,a.context||this.K);this.T=!1!==a.events;this.R=!1!==a.classes;var b=new ha(this.d,a),c=[],d=a.timeout;b.F&&s(b.q,[b.h.e(b.j,"loading")]);K(b,"loading");var c=this.B,e=this.d,f=[],g;for(g in a)if(a.hasOwnProperty(g)){var h=c.C[g];h&&f.push(h(a[g],e))}c=f;this.U=this.V=c.length;a=new T(this.a,this.d,b,d);d=0;for(g=c.length;d<g;d++)e=c[d],e.L(this.a,k(this.la,this,e,b,a))};
            U.prototype.la=function(a,b,c,d){var e=this;d?a.load(function(a,b,d){ma(e,c,a,b,d)}):(a=0==--this.V,this.U--,a&&0==this.U?J(b):(this.R||this.T)&&ka(c,[],{},null,a))};function ma(a,b,c,d,e){var f=0==--a.V;(a.R||a.T)&&setTimeout(function(){ka(b,c,d||null,e||null,f)},0)};function na(a,b,c){this.P=a?a:b+oa;this.s=[];this.W=[];this.fa=c||""}var oa="//fonts.googleapis.com/css";na.prototype.e=function(){if(0==this.s.length)throw Error("No fonts to load!");if(-1!=this.P.indexOf("kit="))return this.P;for(var a=this.s.length,b=[],c=0;c<a;c++)b.push(this.s[c].replace(/ /g,"+"));a=this.P+"?family="+b.join("%7C");0<this.W.length&&(a+="&subset="+this.W.join(","));0<this.fa.length&&(a+="&text="+encodeURIComponent(this.fa));return a};function pa(a){this.s=a;this.da=[];this.M={}}
            var qa={latin:"BESbswy",cyrillic:"&#1081;&#1103;&#1046;",greek:"&#945;&#946;&#931;",khmer:"&#x1780;&#x1781;&#x1782;",Hanuman:"&#x1780;&#x1781;&#x1782;"},ra={thin:"1",extralight:"2","extra-light":"2",ultralight:"2","ultra-light":"2",light:"3",regular:"4",book:"4",medium:"5","semi-bold":"6",semibold:"6","demi-bold":"6",demibold:"6",bold:"7","extra-bold":"8",extrabold:"8","ultra-bold":"8",ultrabold:"8",black:"9",heavy:"9",l:"3",r:"4",b:"7"},sa={i:"i",italic:"i",n:"n",normal:"n"},ta=/^(thin|(?:(?:extra|ultra)-?)?light|regular|book|medium|(?:(?:semi|demi|extra|ultra)-?)?bold|black|heavy|l|r|b|[1-9]00)?(n|i|normal|italic)?$/;
            pa.prototype.parse=function(){for(var a=this.s.length,b=0;b<a;b++){var c=this.s[b].split(":"),d=c[0].replace(/\+/g," "),e=["n4"];if(2<=c.length){var f;var g=c[1];f=[];if(g)for(var g=g.split(","),h=g.length,m=0;m<h;m++){var l;l=g[m];if(l.match(/^[\w-]+$/)){l=ta.exec(l.toLowerCase());var p=void 0;if(null==l)p="";else{p=void 0;p=l[1];if(null==p||""==p)p="4";else var fa=ra[p],p=fa?fa:isNaN(p)?"4":p.substr(0,1);l=l[2];p=[null==l||""==l?"n":sa[l],p].join("")}l=p}else l="";l&&f.push(l)}0<f.length&&(e=f);
                3==c.length&&(c=c[2],f=[],c=c?c.split(","):f,0<c.length&&(c=qa[c[0]])&&(this.M[d]=c))}this.M[d]||(c=qa[d])&&(this.M[d]=c);for(c=0;c<e.length;c+=1)this.da.push(new H(d,e[c]))}};function V(a,b){this.a=(new B(navigator.userAgent)).parse();this.d=a;this.f=b}var ua={Arimo:!0,Cousine:!0,Tinos:!0};V.prototype.L=function(a,b){b(a.k.Y)};V.prototype.load=function(a){var b=this.d;"MSIE"==this.a.getName()&&1!=this.f.blocking?ca(b,k(this.aa,this,a)):this.aa(a)};
            V.prototype.aa=function(a){for(var b=this.d,c=new na(this.f.api,u(b),this.f.text),d=this.f.families,e=d.length,f=0;f<e;f++){var g=d[f].split(":");3==g.length&&c.W.push(g.pop());var h="";2==g.length&&""!=g[1]&&(h=":");c.s.push(g.join(h))}d=new pa(d);d.parse();v(b,c.e());a(d.da,d.M,ua)};function W(a,b){this.d=a;this.f=b;this.p=[]}W.prototype.J=function(a){var b=this.d;return u(this.d)+(this.f.api||"//f.fontdeck.com/s/css/js/")+(b.w.location.hostname||b.K.location.hostname)+"/"+a+".js"};
            W.prototype.L=function(a,b){var c=this.f.id,d=this.d.w,e=this;c?(d.__tpwebfontfontdeckmodule__||(d.__tpwebfontfontdeckmodule__={}),d.__tpwebfontfontdeckmodule__[c]=function(a,c){for(var d=0,m=c.fonts.length;d<m;++d){var l=c.fonts[d];e.p.push(new H(l.name,ga("font-weight:"+l.weight+";font-style:"+l.style)))}b(a)},w(this.d,this.J(c),function(a){a&&b(!1)})):b(!1)};W.prototype.load=function(a){a(this.p)};function X(a,b){this.d=a;this.f=b;this.p=[]}X.prototype.J=function(a){var b=u(this.d);return(this.f.api||b+"//use.typekit.net")+"/"+a+".js"};X.prototype.L=function(a,b){var c=this.f.id,d=this.d.w,e=this;c?w(this.d,this.J(c),function(a){if(a)b(!1);else{if(d.Typekit&&d.Typekit.config&&d.Typekit.config.fn){a=d.Typekit.config.fn;for(var c=0;c<a.length;c+=2)for(var h=a[c],m=a[c+1],l=0;l<m.length;l++)e.p.push(new H(h,m[l]));try{d.Typekit.load({events:!1,classes:!1})}catch(p){}}b(!0)}},2E3):b(!1)};
            X.prototype.load=function(a){a(this.p)};function Y(a,b){this.d=a;this.f=b;this.p=[]}Y.prototype.L=function(a,b){var c=this,d=c.f.projectId,e=c.f.version;if(d){var f=c.d.w;w(this.d,c.J(d,e),function(e){if(e)b(!1);else{if(f["__mti_fntLst"+d]&&(e=f["__mti_fntLst"+d]()))for(var h=0;h<e.length;h++)c.p.push(new H(e[h].fontfamily));b(a.k.Y)}}).id="__MonotypeAPIScript__"+d}else b(!1)};Y.prototype.J=function(a,b){var c=u(this.d),d=(this.f.api||"fast.fonts.net/jsapi").replace(/^.*http(s?):(\/\/)?/,"");return c+"//"+d+"/"+a+".js"+(b?"?v="+b:"")};
            Y.prototype.load=function(a){a(this.p)};function Z(a,b){this.d=a;this.f=b}Z.prototype.load=function(a){var b,c,d=this.f.urls||[],e=this.f.families||[],f=this.f.testStrings||{};b=0;for(c=d.length;b<c;b++)v(this.d,d[b]);d=[];b=0;for(c=e.length;b<c;b++){var g=e[b].split(":");if(g[1])for(var h=g[1].split(","),m=0;m<h.length;m+=1)d.push(new H(g[0],h[m]));else d.push(new H(g[0]))}a(d,f)};Z.prototype.L=function(a,b){return b(a.k.Y)};var $=new U(this);$.B.C.custom=function(a,b){return new Z(b,a)};$.B.C.fontdeck=function(a,b){return new W(b,a)};$.B.C.monotype=function(a,b){return new Y(b,a)};$.B.C.typekit=function(a,b){return new X(b,a)};$.B.C.google=function(a,b){return new V(b,a)};this.tpWebFont||(this.tpWebFont={},this.tpWebFont.load=k($.load,$),this.tpWebFontConfig&&$.load(this.tpWebFontConfig));})(this,document);



        var sgfamilies = [];
        <?php
        //<!--  load good font -->
        $this->operations = new ZTSliderOperations();

        $this->googleFont = $this->slider->getParam("google_font",array());
        if(!empty($this->googleFont)){
            if(is_array($this->googleFont)){
                foreach($this->googleFont as $key => $font){
                    ?>sgfamilies.push('<?php echo $font; ?>');<?php
			}
		}else{
			?>sgfamilies.push('<?php echo $this->googleFont; ?>');<?php
		}
	}

	//add here all new google fonts of the layers, with full variants and subsets
	$this->gfsubsets = $this->slider->getParam("subsets",array());
	$this->gf = $this->slider->getUsedFonts(true);

	foreach($this->gf as $this->gfk => $this->gfv){
		$tcf = $this->gfk.':';
		if(!empty($this->gfv['variants'])){
			$mgfirst = true;
			foreach($this->gfv['variants'] as $mgvk => $mgvv){
				if(!$mgfirst) $tcf .= ',';
				$tcf .= $mgvk;
				$mgfirst = false;
			}
		}

		if(!empty($this->gfv['subsets'])){

			$mgfirst = true;
			foreach($this->gfv['subsets'] as $ssk => $ssv){
				if($mgfirst) $tcf .= '&subset=';
				if(!$mgfirst) $tcf .= ',';
				$tcf .= $ssv;
				$mgfirst = false;
			}
		}

		?>sgfamilies.push('<?php echo $tcf; ?>');<?php
        }

        ?>
        var callAllIdle_LocalTimeOut;
        function fontLoaderWaitForTextLayers() {
            if (jQuery('.slide_layer_type_text').length>0) {
                tpLayerTimelinesRev.allLayerToIdle({type:"text"});
                clearTimeout(callAllIdle_LocalTimeOut);
                callAllIdle_LocalTimeOut = setTimeout(function() {
                    tpLayerTimelinesRev.allLayerToIdle({type:"text"});
                },1250);
            }
            else
                setTimeout(fontLoaderWaitForTextLayers,250);
        }

        if (sgfamilies.length){
            for(var key in sgfamilies){
                var loadnow = [sgfamilies[key]];

                tpWebFont.load({
                    timeout:10000,
                    google:{
                        families:loadnow
                    },
                    loading:function(e) {
                    },
                    active:function() {
                        fontLoaderWaitForTextLayers();
                    },
                    inactive:function() {
                        fontLoaderWaitForTextLayers();
                    },
                });
            }
        }
    </script>
<?php

if($slide->isStaticSlide() || $this->slider->isSlidesFromPosts()){ //insert sliderid for preview
    ?><input type="hidden" id="sliderid" value="<?php echo $this->slider->getID(); ?>" /><?php
}
$this->sliderId = $this->slider->getID();
$this->slider_v = JRoute::_('index.php?option=com_zt_layerslider&view=sliders',false);
$this->edit_slider_link = JRoute::_('index.php?option=com_zt_layerslider&view=slider&layout=edit&id='.$this->slider->getID(),false);
?>

    <div class="wrap settings_wrap">
        <div class="clear_both"></div>

        <div class="rs_breadcrumbs">
            <a class='breadcrumb-button' href='<?php echo $this->slider_v;?>'><i class="eg-icon-th-large"></i><?php echo  JText::_("All Sliders", 'revslider');?></a>
            <a class='breadcrumb-button' href="<?php echo $this->edit_slider_link; ?>"><i class="eg-icon-cog"></i><?php echo  JText::_('Slider Settings', 'revslider');?></a>
            <a class='breadcrumb-button selected' href="#"><i class="eg-icon-pencil-2"></i><?php echo  JText::_('Slide Editor ', 'revslider');?>"<?php echo ' '.$this->slider->getParam("title",""); ?>"</a>
            <div class="tp-clearfix"></div>


            <!-- FIXED TOOLBAR ON THE RIGHT SIDE -->
            <div class="rs-mini-toolbar">
                <?php
                if(!$slide->isStaticSlide()){
                    $this->savebtnid="button_save_slide-tb";
                    $this->prevbtn = "button_preview_slide-tb";
                    if($this->slider->isSlidesFromPosts()){
                        $this->prevbtn = "button_preview_slider-tb";
                    }
                }else{
                    $this->savebtnid="button_save_static_slide-tb";
                    $this->prevbtn = "button_preview_slider-tb";
                }
                ?>
                <div class="rs-toolbar-savebtn rs-mini-toolbar-button">
                    <a class='button-primary revgreen' href='javascript:void(0)' id="<?php echo $this->savebtnid; ?>" ><i class="rs-icon-save-light" style="display: inline-block;vertical-align: middle;width: 18px;height: 20px;background-repeat: no-repeat;"></i><span class="mini-toolbar-text"><?php echo  JText::_("Save Slide",'revslider'); ?></span></a>
                </div>

                <div class="rs-toolbar-cssbtn rs-mini-toolbar-button">
                    <a class='button-primary revpurple' href='javascript:void(0)' id='button_edit_css_global'><i class="">&lt;/&gt;</i><span class="mini-toolbar-text"><?php echo  JText::_("CSS Global",'revslider'); ?></span></a>
                </div>


                <div class="rs-toolbar-slides rs-mini-toolbar-button">
                    <?php
                    $this->slider_url = ($this->sliderTemplate == 'true') ? 'slider_template' : 'slider';
                    ?>
                    <a class="button-primary revblue" href="<?php echo $this->edit_slider_link; ?>" id="link_edit_slides_t"><i class="revicon-cog"></i><span class="mini-toolbar-text"><?php echo  JText::_("Slider Settings",'revslider'); ?></span> </a>

                </div>
                <div class="rs-toolbar-preview rs-mini-toolbar-button">
                    <a class="button-primary revgray" href="javascript:void(0)"  id="<?php echo $this->prevbtn; ?>" ><i class="revicon-search-1"></i><span class="mini-toolbar-text"><?php echo  JText::_("Preview",'revslider'); ?></span></a>
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

                })
            });
        </script>

        <?php
        $this->slider = $this->slider;
        echo $this->loadTemplate('slide_selector');

        echo $this->loadTemplate('slide_general_settings');

        $this->operations = new ZTSliderOperations();

        $settings = $slide->getSettings();
        $this->settings = $settings;

        $this->enable_custom_size_notebook = $this->slider->getParam('enable_custom_size_notebook','off');
        $this->enable_custom_size_tablet = $this->slider->getParam('enable_custom_size_tablet','off');
        $this->enable_custom_size_iphone = $this->slider->getParam('enable_custom_size_iphone','off');

        $adv_resp_sizes = ($this->enable_custom_size_notebook == 'on' || $this->enable_custom_size_tablet == 'on' || $this->enable_custom_size_iphone == 'on') ? true : false;
        $this->adv_resp_sizes = $adv_resp_sizes;
        ?>

        <div id="jqueryui_error_message" class="unite_error_message" style="display:none;">
            <?php echo  JText::_("<b>Warning!!! </b>The jquery ui javascript include that is loaded by some of the plugins are custom made and not contain needed components like 'autocomplete' or 'draggable' function.
		Without those functions the editor may not work correctly. Please remove those custom jquery ui includes in order the editor will work correctly.", 'revslider'); ?>
        </div>

        <div class="edit_slide_wrapper<?php echo ($slide->isStaticSlide()) ? ' rev_static_layers' : ''; ?>">
            <?php
            echo $this->loadTemplate('slide_stage');
            ?>
            <div style="width:100%;clear:both;height:20px"></div>

            <div id="dialog_insert_icon" class="dialog_insert_icon" title="Insert Icon" style="display:none;"></div>

            <div id="dialog_template_insert" class="dialog_template_help" title="<?php echo  JText::_('Insert Meta','revslider') ?>" style="display:none;">
                <ul class="rs-template-settings-tabs">
                    <?php
                    switch($this->slider_type){
                        case 'posts':
                        case 'specific_posts':
                            ?>
                            <li data-content="#slide-post-template-entry" class="selected"><i style="height:45px" class="rs-mini-layer-icon revicon-doc rs-toolbar-icon"></i><span><?php echo  JText::_('Post', 'revslider'); ?></span></li>
                            <?php
                            break;
                        case 'flickr':
                            ?>
                            <li data-content="#slide-flickr-template-entry" class="selected"><i style="height:45px" class="rs-mini-layer-icon eg-icon-flickr rs-toolbar-icon"></i><span><?php echo  JText::_('Flickr', 'revslider'); ?></span></li>
                            <?php
                            break;
                        case 'instagram':
                            ?>
                            <li data-content="#slide-instagram-template-entry" class="selected"><i style="height:45px" class="rs-mini-layer-icon eg-icon-info rs-toolbar-icon"></i><span><?php echo  JText::_('Instagram', 'revslider'); ?></span></li>
                            <?php
                            break;
                        case 'twitter':
                            ?>
                            <li data-content="#slide-twitter-template-entry" class="selected"><i style="height:45px" class="rs-mini-layer-icon eg-icon-twitter rs-toolbar-icon"></i><span><?php echo  JText::_('Twitter', 'revslider'); ?></span></li>
                            <?php
                            break;
                        case 'facebook':
                            ?>
                            <li data-content="#slide-facebook-template-entry" class="selected"><i style="height:45px" class="rs-mini-layer-icon eg-icon-facebook rs-toolbar-icon"></i><span><?php echo  JText::_('Facebook', 'revslider'); ?></span></li>
                            <?php
                            break;
                        case 'youtube':
                            ?>
                            <li data-content="#slide-youtube-template-entry" class="selected"><i style="height:45px" class="rs-mini-layer-icon eg-icon-youtube rs-toolbar-icon"></i><span><?php echo  JText::_('YouTube', 'revslider'); ?></span></li>
                            <?php
                            break;
                        case 'vimeo':
                            ?>
                            <li data-content="#slide-vimeo-template-entry" class="selected"><i style="height:45px" class="rs-mini-layer-icon eg-icon-vimeo rs-toolbar-icon"></i><span><?php echo  JText::_('Vimeo', 'revslider'); ?></span></li>
                            <?php
                            break;
                    }
                    // Apply Filters for Tabs from Add-Ons
//                    do_action( 'rev_slider_insert_meta_tabs',array(
//                            'dummy'=>'<li data-content="#slide-INSERT_TAB_SLUG-template-entry" class="selected"><i style="height:45px" class="rs-mini-layer-icon INSERT_ICON_CLASS rs-toolbar-icon"></i><span>INSERT_TAB_NAME</span></li>',
//                            'slider_type'=>$this->slider_type
//                        )
//                    );
                    ?>
                    <li data-content="#slide-images-template-entry" class="selected"><i style="height:45px" class="rs-mini-layer-icon eg-icon-picture-1 rs-toolbar-icon"></i><span><?php echo  JText::_('Images', 'revslider'); ?></span></li>
                </ul>
                <div style="clear: both;"></div>
                <?php
                switch($this->slider_type){
                    case 'posts':
                    case 'specific_posts':
                        ?>
                        <table class="table_template_help" id="slide-post-template-entry" style="display: none;">
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('meta:somemegatag')">{{meta:somemegatag}}</a></td><td><?php echo  JText::_("Any custom meta tag",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('title')">{{title}}</a></td><td><?php echo  JText::_("Post Title",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('excerpt')">{{excerpt}}</a></td><td><?php echo  JText::_("Post Excerpt",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('alias')">{{alias}}</a></td><td><?php echo  JText::_("Post Alias",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('content')">{{content}}</a></td><td><?php echo  JText::_("Post content",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('content:words:10')">{{content:words:10}}</a></td><td><?php echo  JText::_("Post content limit by words",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('content:chars:10')">{{content:chars:10}}</a></td><td><?php echo  JText::_("Post content limit by chars",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('link')">{{link}}</a></td><td><?php echo  JText::_("The link to the post",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('date')">{{date}}</a></td><td><?php echo  JText::_("Date created",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('date_modified')">{{date_modified}}</a></td><td><?php echo  JText::_("Date modified",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('author_name')">{{author_name}}</a></td><td><?php echo  JText::_("Author name",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('num_comments')">{{num_comments}}</a></td><td><?php echo  JText::_("Number of comments",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('catlist')">{{catlist}}</a></td><td><?php echo  JText::_("List of categories with links",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('catlist_raw')">{{catlist_raw}}</a></td><td><?php echo  JText::_("List of categories without links",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('taglist')">{{taglist}}</a></td><td><?php echo  JText::_("List of tags with links",'revslider'); ?></td></tr>
                        </table>
                        <table class="table_template_help" id="slide-images-template-entry" style="display: none;">
                            <?php
                            foreach($img_sizes as $img_handle => $img_name){
                                ?>
                                <tr><td><a href="javascript:UniteLayersRev.insertTemplate('featured_image_url_<?php echo $img_handle; ?>')">{{featured_image_url_<?php echo $img_handle; ?>}}</a></td><td><?php echo  JText::_("Featured Image URL",'revslider'); echo ' '.$img_name; ?></td></tr>
                                <tr><td><a href="javascript:UniteLayersRev.insertTemplate('featured_image_<?php echo $img_handle; ?>')">{{featured_image_<?php echo $img_handle; ?>}}</a></td><td><?php echo  JText::_("Featured Image &lt;img /&gt;",'revslider'); echo ' '.$img_name; ?></td></tr>
                                <?php
                            }
                            ?>
                        </table>
                        <?php
                        break;
                    case 'flickr':
                        ?>
                        <table class="table_template_help" id="slide-flickr-template-entry" style="display: none;">
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('title')">{{title}}</a></td><td><?php echo  JText::_("Post Title",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('content')">{{content}}</a></td><td><?php echo  JText::_("Post content",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('content:words:10')">{{content:words:10}}</a></td><td><?php echo  JText::_("Post content limit by words",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('content:chars:10')">{{content:chars:10}}</a></td><td><?php echo  JText::_("Post content limit by chars",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('link')">{{link}}</a></td><td><?php echo  JText::_("The link to the post",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('date')">{{date}}</a></td><td><?php echo  JText::_("Date created",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('author_name')">{{author_name}}</a></td><td><?php echo  JText::_('Username','revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('views')">{{views}}</a></td><td><?php echo  JText::_('Views','revslider'); ?></td></tr>
                        </table>
                        <table class="table_template_help" id="slide-images-template-entry" style="display: none;">
                            <?php
                            foreach($img_sizes as $img_handle => $img_name){
                                ?>
                                <tr><td><a href="javascript:UniteLayersRev.insertTemplate('image_url_<?php echo $img_handle; ?>')">{{image_url_<?php echo $img_handle; ?>}}</a></td><td><?php echo  JText::_("Image URL",'revslider'); echo ' '.$img_name; ?></td></tr>
                                <tr><td><a href="javascript:UniteLayersRev.insertTemplate('image_<?php echo $img_handle; ?>')">{{image_<?php echo $img_handle; ?>}}</a></td><td><?php echo  JText::_("Image &lt;img /&gt;",'revslider'); echo ' '.$img_name; ?></td></tr>
                                <?php
                            }
                            ?>
                        </table>
                        <?php
                        break;
                    case 'instagram':
                        ?>
                        <table class="table_template_help" id="slide-instagram-template-entry" style="display: none;">
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('title')">{{title}}</a></td><td><?php echo  JText::_("Title",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('content')">{{content}}</a></td><td><?php echo  JText::_("Content",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('content:words:10')">{{content:words:10}}</a></td><td><?php echo  JText::_("Post content limit by words",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('content:chars:10')">{{content:chars:10}}</a></td><td><?php echo  JText::_("Post content limit by chars",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('link')">{{link}}</a></td><td><?php echo  JText::_("Link",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('date')">{{date}}</a></td><td><?php echo  JText::_("Date created",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('author_name')">{{author_name}}</a></td><td><?php echo  JText::_('Username','revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('likes')">{{likes}}</a></td><td><?php echo  JText::_('Number of Likes','revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('num_comments')">{{num_comments}}</a></td><td><?php echo  JText::_('Number of Comments','revslider'); ?></td></tr>
                        </table>
                        <table class="table_template_help" id="slide-images-template-entry" style="display: none;">
                            <?php
                            foreach($img_sizes as $img_handle => $img_name){
                                ?>
                                <tr><td><a href="javascript:UniteLayersRev.insertTemplate('image_url_<?php echo $img_handle; ?>')">{{image_url_<?php echo $img_handle; ?>}}</a></td><td><?php echo  JText::_("Image URL",'revslider'); echo ' '.$img_name; ?></td></tr>
                                <tr><td><a href="javascript:UniteLayersRev.insertTemplate('image_<?php echo $img_handle; ?>')">{{image_<?php echo $img_handle; ?>}}</a></td><td><?php echo  JText::_("Image &lt;img /&gt;",'revslider'); echo ' '.$img_name; ?></td></tr>
                                <?php
                            }
                            ?>
                        </table>
                        <?php
                        break;
                    case 'twitter':
                        ?>
                        <table class="table_template_help" id="slide-twitter-template-entry" style="display: none;">
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('title')">{{title}}</a></td><td><?php echo  JText::_('Title','revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('content')">{{content}}</a></td><td><?php echo  JText::_('Content','revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('content:words:10')">{{content:words:10}}</a></td><td><?php echo  JText::_("Post content limit by words",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('content:chars:10')">{{content:chars:10}}</a></td><td><?php echo  JText::_("Post content limit by chars",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('link')">{{link}}</a></td><td><?php echo  JText::_("Link",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('date_published')">{{date_published}}</a></td><td><?php echo  JText::_('Pulbishing Date','revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('author_name')">{{author_name}}</a></td><td><?php echo  JText::_('Username','revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('retweet_count')">{{retweet_count}}</a></td><td><?php echo  JText::_('Retweet Count','revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('favorite_count')">{{favorite_count}}</a></td><td><?php echo  JText::_('Favorite Count','revslider'); ?></td></tr>
                        </table>
                        <table class="table_template_help" id="slide-images-template-entry" style="display: none;">
                            <?php
                            foreach($img_sizes as $img_handle => $img_name){
                                ?>
                                <tr><td><a href="javascript:UniteLayersRev.insertTemplate('image_url_<?php echo $img_handle; ?>')">{{image_url_<?php echo $img_handle; ?>}}</a></td><td><?php echo  JText::_("Image URL",'revslider'); echo ' '.$img_name; ?></td></tr>
                                <tr><td><a href="javascript:UniteLayersRev.insertTemplate('image_<?php echo $img_handle; ?>')">{{image_<?php echo $img_handle; ?>}}</a></td><td><?php echo  JText::_("Image &lt;img /&gt;",'revslider'); echo ' '.$img_name; ?></td></tr>
                                <?php
                            }
                            ?>
                        </table>
                        <?php
                        break;
                    case 'facebook':
                        ?>
                        <table class="table_template_help" id="slide-facebook-template-entry" style="display: none;">
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('title')">{{title}}</a></td><td><?php echo  JText::_('Title','revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('content')">{{content}}</a></td><td><?php echo  JText::_('Content','revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('content:words:10')">{{content:words:10}}</a></td><td><?php echo  JText::_("Post content limit by words",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('content:chars:10')">{{content:chars:10}}</a></td><td><?php echo  JText::_("Post content limit by chars",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('link')">{{link}}</a></td><td><?php echo  JText::_('Link','revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('date_published')">{{date_published}}</a></td><td><?php echo  JText::_('Pulbishing Date','revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('date_published')">{{date_modified}}</a></td><td><?php echo  JText::_('Last Modify Date','revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('author_name')">{{author_name}}</a></td><td><?php echo  JText::_('Username','revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('likes')">{{likes}}</a></td><td><?php echo  JText::_('Number of Likes','revslider'); ?></td></tr>
                        </table>
                        <table class="table_template_help" id="slide-images-template-entry" style="display: none;">
                            <?php
                            foreach($img_sizes as $img_handle => $img_name){
                                ?>
                                <tr><td><a href="javascript:UniteLayersRev.insertTemplate('image_url_<?php echo $img_handle; ?>')">{{image_url_<?php echo $img_handle; ?>}}</a></td><td><?php echo  JText::_("Image URL",'revslider'); echo ' '.$img_name; ?></td></tr>
                                <tr><td><a href="javascript:UniteLayersRev.insertTemplate('image_<?php echo $img_handle; ?>')">{{image_<?php echo $img_handle; ?>}}</a></td><td><?php echo  JText::_("Image &lt;img /&gt;",'revslider'); echo ' '.$img_name; ?></td></tr>
                                <?php
                            }
                            ?>
                        </table>
                        <?php
                        break;
                    case 'youtube':
                        ?>
                        <table class="table_template_help" id="slide-youtube-template-entry" style="display: none;">
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('title')">{{title}}</a></td><td><?php echo  JText::_('Title','revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('excerpt')">{{excerpt}}</a></td><td><?php echo  JText::_('Excerpt','revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('content')">{{content}}</a></td><td><?php echo  JText::_('Content','revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('content:words:10')">{{content:words:10}}</a></td><td><?php echo  JText::_("Post content limit by words",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('content:chars:10')">{{content:chars:10}}</a></td><td><?php echo  JText::_("Post content limit by chars",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('date_published')">{{date_published}}</a></td><td><?php echo  JText::_('Pulbishing Date','revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('link')">{{link}}</a></td><td><?php echo  JText::_('Link','revslider'); ?></td></tr>
                        </table>
                        <table class="table_template_help" id="slide-images-template-entry" style="display: none;">
                            <?php
                            foreach($img_sizes as $img_handle => $img_name){
                                ?>
                                <tr><td><a href="javascript:UniteLayersRev.insertTemplate('image_url_<?php echo $img_handle; ?>')">{{image_url_<?php echo $img_handle; ?>}}</a></td><td><?php echo  JText::_("Image URL",'revslider'); echo ' '.$img_name; ?></td></tr>
                                <tr><td><a href="javascript:UniteLayersRev.insertTemplate('image_<?php echo $img_handle; ?>')">{{image_<?php echo $img_handle; ?>}}</a></td><td><?php echo  JText::_("Image &lt;img /&gt;",'revslider'); echo ' '.$img_name; ?></td></tr>
                                <?php
                            }
                            ?>
                        </table>
                        <?php
                        break;
                    case 'vimeo':
                        ?>
                        <table class="table_template_help" id="slide-vimeo-template-entry" style="display: none;">
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('title')">{{title}}</a></td><td><?php echo  JText::_('Title','revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('excerpt')">{{excerpt}}</a></td><td><?php echo  JText::_('Excerpt','revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('content')">{{content}}</a></td><td><?php echo  JText::_('Content','revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('content:words:10')">{{content:words:10}}</a></td><td><?php echo  JText::_("Post content limit by words",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('content:chars:10')">{{content:chars:10}}</a></td><td><?php echo  JText::_("Post content limit by chars",'revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('link')">{{link}}</a></td><td><?php echo  JText::_('The link to the post','revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('date_published')">{{date_published}}</a></td><td><?php echo  JText::_('Pulbishing Date','revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('author_name')">{{author_name}}</a></td><td><?php echo  JText::_('Username','revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('likes')">{{likes}}</a></td><td><?php echo  JText::_('Number of Likes','revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('views')">{{views}}</a></td><td><?php echo  JText::_('Number of Views','revslider'); ?></td></tr>
                            <tr><td><a href="javascript:UniteLayersRev.insertTemplate('num_comments')">{{num_comments}}</a></td><td><?php echo  JText::_('Number of Comments','revslider'); ?></td></tr>
                        </table>
                        <table class="table_template_help" id="slide-images-template-entry" style="display: none;">
                            <?php
                            foreach($img_sizes as $img_handle => $img_name){
                                ?>
                                <tr><td><a href="javascript:UniteLayersRev.insertTemplate('image_url_<?php echo $img_handle; ?>')">{{image_url_<?php echo $img_handle; ?>}}</a></td><td><?php echo  JText::_("Image URL",'revslider'); echo ' '.$img_name; ?></td></tr>
                                <tr><td><a href="javascript:UniteLayersRev.insertTemplate('image_<?php echo $img_handle; ?>')">{{image_<?php echo $img_handle; ?>}}</a></td><td><?php echo  JText::_("Image &lt;img /&gt;",'revslider'); echo ' '.$img_name; ?></td></tr>
                                <?php
                            }
                            ?>
                        </table>
                        <?php
                        break;
                }
                // Apply Filters for Tab Content from Add-Ons
//                do_action( 'rev_slider_insert_meta_tab_content',array(
//                        'tab_head' => '<table class="table_template_help" id="slide-INSERT_TAB_SLUG-template-entry" style="display: none;">' ,
//                        'tab_row'  => '<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'INSERT_META_SLUG\')">{{INSERT_META_SLUG}}</a></td><td>INSERT_META_NAME</td></tr>',
//                        'tab_foot' => '</table>',
//                        'slider_type' => $this->slider_type
//                    )
//                );
                ?>
                <script type="text/javascript">
                    jQuery('document').ready(function() {
                        jQuery('.rs-template-settings-tabs li').click(function() {
                            var tw = jQuery('.rs-template-settings-tabs .selected'),
                                tn = jQuery(this);
                            jQuery(tw.data('content')).hide(0);
                            tw.removeClass("selected");
                            tn.addClass("selected");
                            jQuery(tn.data('content')).show(0);
                        });
                        jQuery('.rs-template-settings-tabs li:first-child').click();
                    });
                </script>
            </div>

            <div id="dialog_advanced_css" class="dialog_advanced_css" title="<?php echo  JText::_('Advanced CSS', 'revslider'); ?>" style="display:none;">
                <div style="display: none;"><span id="rev-example-style-layer">example</span></div>
                <div class="first-css-area">
                    <span class="advanced-css-title" style="background:#e67e22"><?php echo  JText::_('Style from Options', 'revslider'); ?><span style="margin-left:15px;font-size:11px;font-style:italic">(<?php echo  JText::_('Editable via Option Fields, Saved in the Class:', 'revslider'); ?><span class="current-advance-edited-class"></span>)</span></span>
                    <textarea id="textarea_template_css_editor_uneditable" rows="20" cols="81" disabled="disabled"></textarea>
                </div>
                <div class="second-css-area">
                    <span class="advanced-css-title"><?php echo  JText::_('Additional Custom Styling', 'revslider'); ?><span style="margin-left:15px;font-size:11px;font-style:italic">(<?php echo  JText::_('Appended in the Class:', 'revslider'); ?><span class="current-advance-edited-class"></span>)</span></span>
                    <textarea id="textarea_advanced_css_editor" rows="20" cols="81"></textarea>
                </div>
            </div>

            <div id="dialog_save_as_css" class="dialog_save_as_css" title="<?php echo  JText::_('Save As', 'revslider'); ?>" style="display:none;">
                <div style="margin-top:14px">
                    <span style="margin-right:15px"><?php echo  JText::_('Save As:', 'revslider'); ?></span><input id="rs-save-as-css" type="text" name="rs-save-as-css" value="" />
                </div>
            </div>

            <div id="dialog_rename_css" class="dialog_rename_css" title="<?php echo  JText::_('Rename CSS', 'revslider'); ?>" style="display:none;">
                <div style="margin-top:14px">
                    <span style="margin-right:15px"><?php echo  JText::_('Rename to:', 'revslider'); ?></span><input id="rs-rename-css" type="text" name="rs-rename-css" value="" />
                </div>
            </div>

            <div id="dialog_advanced_layer_css" class="dialog_advanced_layer_css" title="<?php echo  JText::_('Layer Inline CSS', 'revslider'); ?>" style="display:none;">
                <div class="first-css-area">
                    <span class="advanced-css-title" style="background:#e67e22"><?php echo  JText::_('Advanced Custom Styling', 'revslider'); ?><span style="margin-left:15px;font-size:11px;font-style:italic">(<?php echo  JText::_('Appended Inline to the Layer Markup', 'revslider'); ?>)</span></span>
                    <textarea id="textarea_template_css_editor_layer" name="textarea_template_css_editor_layer"></textarea>
                </div>
            </div>

            <div id="dialog_save_as_animation" class="dialog_save_as_animation" title="<?php echo  JText::_('Save As', 'revslider'); ?>" style="display:none;">
                <div style="margin-top:14px">
                    <span style="margin-right:15px"><?php echo  JText::_('Save As:', 'revslider'); ?></span><input id="rs-save-as-animation" type="text" name="rs-save-as-animation" value="" />
                </div>
            </div>

            <div id="dialog_save_animation" class="dialog_save_animation" title="<?php echo  JText::_('Save Under', 'revslider'); ?>" style="display:none;">
                <div style="margin-top:14px">
                    <span style="margin-right:15px"><?php echo  JText::_('Save Under:', 'revslider'); ?></span><input id="rs-save-under-animation" type="text" name="rs-save-under-animation" value="" />
                </div>
            </div>

            <script type="text/javascript">

                <?php
                $icon_sets = ZtSliderSlider::set_icon_sets(array());
                $sets = array();
                if(!empty($icon_sets)){
                    $sets = implode("','", $icon_sets);
                }
                ?>

                var rs_icon_sets = new Array('<?php echo $sets; ?>');


                jQuery(document).ready(function() {

                    UniteLayersRev.addPreventLeave();

                    <?php if(!empty($this->jsonLayers)){ ?>
                    //set init layers object
                    UniteLayersRev.setInitLayersJson(<?php echo $this->jsonLayers; ?>);
                    <?php } ?>

                    <?php
                    if($slide->isStaticSlide()){
                    $arrayDemoLayers = array();
                    $arrayDemoSettings = array();
                    if(!empty($all_slides) && is_array($all_slides)){
                        foreach($all_slides as $cSlide){
                            $arrayDemoLayers[$cSlide->getID()] = $cSlide->getLayers();
                            $arrayDemoSettings[$cSlide->getID()] = $cSlide->getParams();
                        }
                    }
                    $this->jsonDemoLayers = ZTSliderFunctions::jsonEncodeForClientSide($arrayDemoLayers);
                    $this->jsonDemoSettings = ZTSliderFunctions::jsonEncodeForClientSide($arrayDemoSettings);
                    ?>
                    //set init demo layers object
                    UniteLayersRev.setInitDemoLayersJson(<?php echo $this->jsonDemoLayers; ?>);
                    UniteLayersRev.setInitDemoSettingsJson(<?php echo $this->jsonDemoSettings; ?>);
                    <?php
                    } ?>

                    <?php if(!empty($this->jsonStaticLayers)){ ?>
                    UniteLayersRev.setInitStaticLayersJson(<?php echo $this->jsonStaticLayers; ?>);
                    <?php } ?>

                    <?php if(!empty($this->jsonCaptions)){ ?>
                    UniteLayersRev.setInitCaptionClasses(<?php echo $this->jsonCaptions; ?>);
                    <?php } ?>

                    <?php if(!empty($this->arrCustomAnim)){ ?>
                    UniteLayersRev.setInitLayerAnim(<?php echo $this->arrCustomAnim; ?>);
                    <?php } ?>

                    <?php if(!empty($this->arrCustomAnimDefault)){ ?>
                    UniteLayersRev.setInitLayerAnimsDefault(<?php echo $this->arrCustomAnimDefault; ?>);
                    <?php } ?>

                    <?php if(!empty($this->jsonFontFamilys)){ ?>
                    UniteLayersRev.setInitFontTypes(<?php echo $this->jsonFontFamilys; ?>);
                    <?php } ?>

                    <?php if(!empty($this->arrCssStyles)){ ?>
                    UniteCssEditorRev.setInitCssStyles(<?php echo $this->arrCssStyles; ?>);
                    <?php } ?>

                    <?php
                    $trans_sizes = ZTSliderFunctions::jsonEncodeForClientSide($slide->translateIntoSizes());
                    ?>
                    UniteLayersRev.setInitTransSetting(<?php echo $trans_sizes; ?>);

                    UniteLayersRev.init("<?php echo $this->slideDelay; ?>");


                    UniteCssEditorRev.init();


                    ZtSliderAdmin.initGlobalStyles();

                    ZtSliderAdmin.initLayerPreview();

                    ZtSliderAdmin.setStaticCssCaptionsUrl('<?php echo ZT_ASSETS_URL.'/assets/css/static-captions.css'; ?>');

                    /* var reproduce;
                     jQuery(window).resize(function() {
                     clearTimeout(reproduce);
                     reproduce = setTimeout(function() {
                     UniteLayersRev.refreshGridSize();
                     },100);
                     });*/

                    <?php if($kenburn_effect == 'on'){ ?>
                    jQuery('input[name="kenburn_effect"]:checked').change();
                    <?php } ?>


                    // DRAW  HORIZONTAL AND VERTICAL LINEAR
                    var horl = jQuery('#hor-css-linear .linear-texts'),
                        verl = jQuery('#ver-css-linear .linear-texts'),
                        maintimer = jQuery('#mastertimer-linear .linear-texts'),
                        mw = "<?php echo $this->tempwidth_jq; ?>";
                    mw = parseInt(mw.split(":")[1],0);

                    for (var i=-600;i<mw;i=i+100) {
                        if (mw-i<100)
                            horl.append('<li style="width:'+(mw-i)+'px"><span>'+i+'</span></li>');
                        else
                            horl.append('<li><span>'+i+'</span></li>');
                    }

                    for (var i=0;i<2000;i=i+100) {
                        verl.append('<li><span>'+i+'</span></li>');
                    }

                    for (var i=0;i<160;i=i+1) {
                        var txt = i+"s";

                        maintimer.append('<li><span>'+txt+'</span></li>');
                    }

                    // SHIFT RULERS and TEXTS and HELP LINES//
                    function horRuler() {
                        var dl = jQuery('#divLayers'),
                            l = parseInt(dl.offset().left,0) - parseInt(jQuery('#thelayer-editor-wrapper').offset().left,0);
                        jQuery('#hor-css-linear').css({backgroundPosition:(l)+"px 50%"});
                        jQuery('#hor-css-linear .linear-texts').css({left:(l-595)+"px"});
                        jQuery('#hor-css-linear .helplines-offsetcontainer').css({left:(l)+"px"});

                        jQuery('#ver-css-linear .helplines').css({left:"-15px"}).width(jQuery('#thelayer-editor-wrapper').outerWidth(true)-35);
                        jQuery('#hor-css-linear .helplines').css({top:"-15px"}).height(jQuery('#thelayer-editor-wrapper').outerHeight(true)-41);
                    }

                    horRuler();


                    jQuery('.my-color-field').wpColorPicker({
                        palettes:false,
                        height:250,

                        border:false,

                        change:function(event,ui) {
                            /*var col = jQuery(event.target).val();
                             if (col.length<5) {
                             col = "#"+col[1]+col[1]+col[2]+col[2]+col[3]+col[3];
                             }
                             jQuery(event.target).val(col);*/
                            switch (jQuery(event.target).attr('name')) {
                                case "adbutton-color-1":
                                case "adbutton-color-2":
                                case "adbutton-border-color":
                                    setExampleButtons();
                                    break;

                                case "adshape-color-1":
                                case "adshape-color-2":
                                case "adshape-border-color":
                                    setExampleShape();
                                    break;
                                case "bg_color":
                                    var bgColor = jQuery("#slide_bg_color").val();
                                    jQuery("#divbgholder").css("background-color",bgColor);
                                    jQuery('.slotholder .tp-bgimg.defaultimg').css({backgroundColor:bgColor});
                                    jQuery('#slide_selector .list_slide_links li.selected .slide-media-container ').css({backgroundColor:bgColor});
                                    break;
                            }

                            if (jQuery('.layer_selected.slide_layer').length>0) {
                                jQuery(event.target).blur().focus();
                                //jQuery('#style_form_wrapper').trigger("colorchanged");
                            }

                        },
                        clear:function(event,ui) {
                            if (jQuery('.layer_selected.slide_layer').length>0) {
                                var inp = jQuery(event.target).closest('.wp-picker-input-wrap').find('.my-color-field');
                                inp.val("transparent").blur().focus();
                                //jQuery('#style_form_wrapper').trigger("colorchanged");
                            }
                        }

                    });

                    jQuery('.adb-input').on("change blur focus",setExampleButtons);
                    jQuery('.ads-input, input[name="shape_fullwidth"], input[name="shape_fullheight"]').on("change blur focus",setExampleShape);
                    jQuery('.ui-autocomplete').on('click',setExampleButtons);

                    jQuery('.wp-color-result').on("click",function() {

                        if (jQuery(this).hasClass("wp-picker-open"))
                            jQuery(this).closest('.wp-picker-container').addClass("pickerisopen");
                        else
                            jQuery(this).closest('.wp-picker-container').removeClass("pickerisopen");
                    });

                    jQuery("body").click(function(event) {
                        jQuery('.wp-picker-container.pickerisopen').removeClass("pickerisopen");
                    })

                    // WINDOW RESIZE AND SCROLL EVENT SHOULD REDRAW RULERS
                    jQuery(window).resize(horRuler);
                    jQuery('#divLayers-wrapper').on('scroll',horRuler);


                    jQuery('#toggle-idle-hover .icon-stylehover').click(function() {
                        var bt = jQuery('#toggle-idle-hover');
                        bt.removeClass("idleisselected").addClass("hoverisselected");
                        jQuery('#tp-idle-state-advanced-style').hide();
                        jQuery('#tp-hover-state-advanced-style').show();
                    });

                    jQuery('#toggle-idle-hover .icon-styleidle').click(function() {
                        var bt = jQuery('#toggle-idle-hover');
                        bt.addClass("idleisselected").removeClass("hoverisselected");
                        jQuery('#tp-idle-state-advanced-style').show();
                        jQuery('#tp-hover-state-advanced-style').hide();
                    });


                    jQuery('input[name="hover_allow"]').on("change",function() {
                        if (jQuery(this).attr("checked")=="checked") {
                            jQuery('#idle-hover-swapper').show();
                        } else {
                            jQuery('#idle-hover-swapper').hide();
                        }
                    });


                    // HIDE /SHOW  INNER SAVE,SAVE AS ETC..
                    jQuery('.clicktoshowmoresub').click(function() {
                        jQuery(this).find('.clicktoshowmoresub_inner').show();
                    });

                    jQuery('.clicktoshowmoresub').on('mouseleave',function() {
                        jQuery(this).find('.clicktoshowmoresub_inner').hide();
                    });

                    //arrowRepeater();
                    function arrowRepeater() {
                        var tw = new punchgs.TimelineLite();
                        tw.add(punchgs.TweenLite.from(jQuery('.animatemyarrow'),0.5,{x:-10,opacity:0}),0);
                        tw.add(punchgs.TweenLite.to(jQuery('.animatemyarrow'),0.5,{x:10,opacity:0}),0.5);

                        tw.play(0);
                        tw.eventCallback("onComplete",function() {
                            tw.restart();
                        })
                    }

                    ZtSliderSettings.createModernOnOff();

                });

            </script>
        </div>

        <div class="vert_sap"></div>


        <div id="dialog_rename_animation" class="dialog_rename_animation" title="<?php echo  JText::_('Rename Animation', 'revslider'); ?>" style="display:none;">
            <div style="margin-top:14px">
                <span style="margin-right:15px"><?php echo  JText::_('Rename to:', 'revslider'); ?></span><input id="rs-rename-animation" type="text" name="rs-rename-animation" value="" />
            </div>
        </div>

        <?php
        if($slide->isStaticSlide()){
            $this->slideID = $slide->getID();
        }

        $mslide_list = ZTSliderFunctions::jsonEncodeForClientSide(array());

        ?>
        <script type="text/javascript">
            var g_patternViewSlide = '<?php echo $this->patternViewSlide; ?>';


            var g_messageDeleteSlide = "<?php echo  JText::_("Delete this slide?",'revslider'); ?>";
            jQuery(document).ready(function(){
                ZtSliderAdmin.initEditSlideView(<?php echo $this->slideID; ?>, <?php echo $this->sliderID; ?>, <?php echo ($slide->isStaticSlide()) ? 'true' : 'false'; ?>);

                UniteLayersRev.setInitSlideIds(<?php echo $mslide_list; ?>);
            });
            var curSlideID = <?php echo $this->slideID; ?>;
        </script>

        <?php
        require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/system/dialog-copy-move.php';;
        ?>


        <script type="text/javascript">
            jQuery(document).ready(function(){
                jQuery('#rs-do-set-style-on-devices').click(function(){
                    var layer = UniteLayersRev.getCurrentLayer();

                    if(layer !== false){
                        if(layer['static_styles'] == undefined) layer['static_styles'] = {};

                        var mcolor = jQuery('input[name="color_static"]').val();
                        var mfontsize = jQuery('input[name="font_size_static"]').val();
                        var mlineheight = jQuery('input[name="line_height_static"]').val();
                        var mfontweight = jQuery('select[name="font_weight_static"] option:selected').val();

                        jQuery('.rs-set-device-chk').each(function(){
                            if(jQuery(this).is(':checked')){
                                var dt = jQuery(this).data('device'); //which device to set on
                                var so = jQuery(this).data('seton'); //set on color/font-size and so on

                                switch(so){
                                    case 'color':
                                        var mval = mcolor;
                                        break;
                                    case 'font-size':
                                        var mval = mfontsize;
                                        break;
                                    case 'line-height':
                                        var mval = mlineheight;
                                        break;
                                    case 'font-weight':
                                        var mval = mfontweight;
                                        break;
                                }

                                layer['static_styles'] = UniteLayersRev.setVal(layer['static_styles'], so, mval, false, [dt]);
                            }
                        });

                        //give status that it has been done

                        jQuery('#rs-set-style-on-devices-dialog').toggle();
                    }
                });
            });
        </script>

<?php
require JPATH_COMPONENT_ADMINISTRATOR . '/helpers/html/template-selector.php';
?>