<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2015 ThemePunch
 */

defined('_JEXEC') or die;

class ZTSliderOutput {

	private static $sliderSerial = 0;
	
	private $sliderHtmlID;
	private $sliderHtmlID_wrapper;
	private $markup_export = false;
	private $oneSlideMode = false;
	private $oneSlideData;
	private $previewMode = false;	//admin preview mode
	private $slidesNumIndex;
	private $sliderLang = 'all';
	private $hasOnlyOneSlide = false;
	private $rev_inline_js = '';
	public $rev_custom_navigation_css = '';
	public $slider;
	public $static_slide = array();
	public $class_include = array();

	public function __construct(){
		$this->db = JFactory::getDbo();
	}

	/**
	 *
	 * check the put in string
	 * return true / false if the put in string match the current page.
	 */
	public static function isPutIn($putIn,$emptyIsFalse = false){
	
		$putIn = strtolower($putIn);
		$putIn = trim($putIn);
		$menu = JFactory::getApplication()->getMenu();

		if($emptyIsFalse && empty($putIn))
			return(false);

		if($putIn == 'homepage'){		//filter by homepage
			if(JFactory::getApplication()->input->getInt('Itemid') != $menu->getDefault())
				return(false);
		}
		else		//case filter by pages
		if(!empty($putIn)){
			$arrPutInPages = array();
			$arrPagesTemp = explode(",", $putIn);
			foreach($arrPagesTemp as $page){
				$page = trim($page);
				if(is_numeric($page) || $page == 'homepage')
					$arrPutInPages[] = $page;
			}
			if(!empty($arrPutInPages)){

				//get current page id

				$currentPageID = "";
				if(JFactory::getApplication()->input->getInt('Itemid') == $menu->getDefault()){
					$currentPageID = 'homepage';
				}else{
					global $post;
					if(isset($post->ID))
						$currentPageID = $post->ID;
				}

				//do the filter by pages
				if(array_search($currentPageID, $arrPutInPages) === false)
					return(false);
			}
		}

		return(true);
	}


	/**
	 * put the rev slider slider on the html page.
	 * @param $data - mixed, can be ID ot Alias.
	 */
	public static function putSlider($sliderID,$putIn="", $gal_ids = array(), $settings = array(), $order = array()){
		
		$isPutIn = self::isPutIn($putIn);
		if($isPutIn == false)
			return(false);
		
		//check if on mobile and if option hide on mobile is set

		$output = new ZTSliderOutput();

		$output->putSliderBase($sliderID, $gal_ids, false, $settings, $order);

		$slider = $output->getSlider();
		return($slider);
	}

	
	public function getSliderHtmlID(){
		return $this->sliderHtmlID;
	}
	
	
	/**
	 * set language
	 */
	public function setLang($lang){
		$this->sliderLang = $lang;
	}

	/**
	 *
	 * set one slide mode for preview
	 */
	public function setOneSlideMode($data){
		$this->oneSlideMode = true;
		$this->oneSlideData = $data;
	}

	/**
	 *
	 * set preview mode
	 */
	public function setPreviewMode(){
		$this->previewMode = true;
	}

	/**
	 *
	 * get the last slider after the output
	 */
	public function getSlider(){
		return($this->slider);
	}

	/**
	 *
	 * get slide full width video data
	 */
	private function getSlideFullWidthVideoData(ZTSliderSlide $slide){

		$response = array('found' => false);

		//deal full width video:
		$enableVideo = $slide->getParam('enable_video', 'false');
		if($enableVideo != 'true')
			return($response);

		$videoID = $slide->getParam('video_id', '');
		$videoID = trim($videoID);

		if(empty($videoID))
			return($response);

		$response["found"] = true;

		$videoType = is_numeric($videoID) ? 'vimeo' : 'youtube';
		$videoAutoplay = $slide->getParam('video_autoplay', false);
		$videoCover = $slide->getParam('cover', false);
		$videoAutoplayOnlyFirstTime = $slide->getParam('autoplayonlyfirsttime', false);
		$previewimage = $slide->getParam('previewimage', '');
		$videoNextslide = $slide->getParam('video_nextslide', 'off');
		$mute = $slide->getParam('mute', false);

		$response['type'] = $videoType;
		$response['videoID'] = $videoID;
		$response['autoplay'] = ZTSliderFunctions::strToBool($videoAutoplay);
		$response['cover'] = ZTSliderFunctions::strToBool($videoCover);
		$response['autoplayonlyfirsttime'] = ZTSliderFunctions::strToBool($videoAutoplayOnlyFirstTime);
		$response['previewimage'] = ZTSliderFunctions::strToBool($previewimage);
		$response['nextslide'] = ($videoNextslide == 'on') ? true : false;
		$response['mute'] = ZTSliderFunctions::strToBool($mute);

		return($response);
	}
	
	
	/**
	 * Get the Hero Slide of the Slider
	 * @since: 5.0
	 */
	private function getHeroSlide($slides){
		$hero_id = $this->slider->getParam('hero_active', -1);
		
		
		if(empty($slides)) return $slides;
		
		foreach($slides as $slide){
			$slideID = $slide->getID();
			
			if($slideID == $hero_id){
				return $slide;
			}
			if($this->sliderLang !== 'all'){
				if($slide->getParentSlideID() == $hero_id){
					return $slide;
				}
			}
		}
		
		//could not be found, use first slide
		foreach($slides as $slide){
			return $slide;
		}
	}
	
	/**
	 *
	 * filter the slides for one slide preview
	 */
	private function filterOneSlide($slides){

		$oneSlideID = $this->oneSlideData['slideid'];
		
		
		if(strpos($oneSlideID, 'static_') !== false){
			global $wpdb;
			$sliderID = str_replace('static_', '', $oneSlideID);
			
			$tmp_slides = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".RevSliderGlobals::$table_static_slides." WHERE slider_id = %s", array($sliderID)), ARRAY_A);
			
			if(!empty($tmp_slides)){
				$n_slides = new ZTSliderSlide();
				$n_slides->initByData($tmp_slides[0]);
				
				$slides[0] = $n_slides;
				$oneSlideID = $n_slides->getID();
			}
		}

		$oneSlideParams = ZTSliderFunctions::getVal($this->oneSlideData, 'params');
		$oneSlideLayers = ZTSliderFunctions::getVal($this->oneSlideData, 'layers');
		
		if(gettype($oneSlideParams) == 'object')
			$oneSlideParams = (array)$oneSlideParams;

		if(gettype($oneSlideLayers) == 'object')
			$oneSlideLayers = (array)$oneSlideLayers;

		if(!empty($oneSlideLayers))
			$oneSlideLayers = ZTSliderFunctions::convertStdClassToArray($oneSlideLayers);

		$newSlides = array();
		foreach($slides as $slide){
			$slideID = $slide->getID();
			
			if($slideID == $oneSlideID){

				if(!empty($oneSlideParams))
					$slide->setParams($oneSlideParams);

				if(!empty($oneSlideLayers))
					$slide->setLayers($oneSlideLayers);

				$newSlides[] = $slide;	//add 2 slides
				$newSlides[] = $slide;
			}
		}

		return($newSlides);
	}


	/**
	 *
	 * put the slider slides
	 */
	private function putSlides($gal_ids = array(), $order = array()){
		//go to template slider if post template
		$sliderType = $this->slider->getParam('slider_type');
		$slider_type = $this->slider->getParam('slider-type'); //standard, carousel or hero
		$source_type = $this->slider->getParam('source_type'); //vimeo, post ect.
		
		$publishedOnly = true;
		if($slider_type == 'hero' || !empty($order)){
			$publishedOnly = false; //take all, even unpublished ones
		}
		
		if($this->previewMode == true && $this->oneSlideMode == true){
			$previewSlideID = ZTSliderFunctions::getVal($this->oneSlideData, 'slideid');
			$previewSlide = new ZTSlide();
			$previewSlide->initByID($previewSlideID);
			$slides = array($previewSlide);
		}else{
			$slides = $this->slider->getSlidesForOutput($publishedOnly,$this->sliderLang,$gal_ids);
			
			if(!empty($gal_ids) && $gal_ids[0]){ //add slides from the images
				if(count($slides) > 0){ //check if we have at least one slide. If not, then it may result in errors here
					if(count($gal_ids) !== count($slides)){ //set slides to the same amount as
						if(count($gal_ids) < count($slides)){
							$slides = array_slice($slides, 0, count($gal_ids));
						}else{ // >
							while(count($slides) < count($gal_ids)){
								foreach($slides as $slide){
									$new_slide = clone $slide;
									array_push($slides, $new_slide);
									if(count($slides) >= count($gal_ids)) break;
								}
							}
							if(count($gal_ids) < count($slides)){
								$slides = array_slice($slides, 0, count($gal_ids));
							}
						}
					}
					
					$sliderSize = $this->slider->getParam('def-image_source_type', 'full');
					
					$isSlidesFromPosts = $this->slider->isSlidesFromPosts();
					
					$gi = 0;
					foreach($slides as $skey => $slide){ //add gallery images into slides
						//set post id to imageid
						
						//check if slider is Post Based, if yes use $slide->getID(); else use $gal_ids[$gi]
						if($isSlidesFromPosts){
							$ret = $slide->setImageByID($slide->getID(), $sliderSize);
						}else{
							$ret = $slide->setImageByID($gal_ids[$gi], $sliderSize);
						}
						if($ret === true){ //set slide type to image instead of for example external or transparent
							$slide->setBackgroundType('image');
						}else{
							unset($slides[$skey]);
						}
						
						$gi++;
					}
				}
			}elseif(!empty($order)){
				$tempSlides = $slides;
				$slides = array();
				
				foreach($order as $order_slideid){
					foreach($tempSlides as $tempSlide){
						if($tempSlide->getID() == $order_slideid){
							$tempSlide->setParam('state', 'published'); //set to published
							$slides[] = $tempSlide;
							break;
						}
					}
				}
				
				if(count($slides) == 1){ //remove navigation
					$this->slider->setParam('enable_arrows', 'off');
					$this->slider->setParam('enable_bullets', 'off');
					$this->slider->setParam('enable_tabs', 'off');
					$this->slider->setParam('enable_thumbnails', 'off');
				}
			}
		}
		
		$this->slidesNumIndex = $this->slider->getSlidesNumbersByIDs(true);
		
		if($slider_type == 'hero' && empty($order) && empty($gal_ids)){ //we are a hero Slider, show only one Slide!
			$hero = $this->getHeroSlide($slides);
			$slides = (!empty($hero)) ? array($hero) : array();
		}
		
		//check if mobile, if yes, then remove certain slides
		$usragent = (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$mobile = (strstr($usragent,'Android') || strstr($usragent,'webOS') || strstr($usragent,'iPhone') ||strstr($usragent,'iPod') || strstr($usragent,'iPad') || strstr($usragent,'Windows Phone') || ZtSliderFunctions::is_mobile()) ? true : false;
		if($mobile && !empty($slides)){
			foreach($slides as $ss => $sv){
				$hsom = $sv->getParam('hideslideonmobile', 'off');
				if($hsom == 'on'){
					unset($slides[$ss]);
				}
			}
		}
		
		if(empty($slides)){
			?>
			<div class="no-slides-text">
				<?php
				if($this->slider->isSlidesFromPosts()){
					JText::_('No slides found, please add at least one Slide Template to the choosen language.', 'revslider');
				}else{
					JText::_('No slides found, please add some slides', 'revslider');
				}
				?>
			</div>
			<?php
		}

		//set that we are originally template slider
		$post_based_slider = false;
		if($this->slider->isSlidesFromPosts()){
			$post_based_slider = true;
		}
		
		$slideWidth = $this->slider->getParam('width',900);
		$slideHeight = $this->slider->getParam('height',300);
		
		$do_bullets = $this->slider->getParam('enable_bullets','off');
		$do_thumbnails = $this->slider->getParam('enable_thumbnails','off');
		$do_arrows = $this->slider->getParam('enable_arrows','off');
		$do_tabs = $this->slider->getParam('enable_tabs','off');
		$isThumbsActive = ($do_bullets == 'on' || $do_thumbnails == 'on' || $do_arrows == 'on' || $do_tabs == 'on') ? true : false;

		$lazyLoad = $this->slider->getParam('lazy_load_type', false);
		if($lazyLoad === false){ //do fallback checks to removed lazy_load value since version 5.0 and replaced with an enhanced version
			$old_ll = $this->slider->getParam('lazy_load', 'off');
			$lazyLoad = ($old_ll == 'on') ? 'all' : 'none';
		}
		
		//for one slide preview
		//if($this->oneSlideMode == true)
		//	$slides = $this->filterOneSlide($slides);

		echo "<ul>";

		$htmlFirstTransWrap = "";

		$startWithSlide = $this->slider->getStartWithSlideSetting();

		$firstTransActive = $this->slider->getParam('first_transition_active', 'false');
		
		if($firstTransActive == 'on' && $slider_type !== 'hero'){

			$firstTransition = $this->slider->getParam('first_transition_type', 'fade');
			$htmlFirstTransWrap .= ' data-fstransition="'.$firstTransition.'"';

			$firstDuration = $this->slider->getParam('first_transition_duration', '300');
			if(!empty($firstDuration) && is_numeric($firstDuration))
				$htmlFirstTransWrap .= ' data-fsmasterspeed="'.$firstDuration.'"';

			$firstSlotAmount = $this->slider->getParam('first_transition_slot_amount', '7');
			if(!empty($firstSlotAmount) && is_numeric($firstSlotAmount))
			$htmlFirstTransWrap .= ' data-fsslotamount="'.$firstSlotAmount.'"';

		}
		
		$oneSlideLoop = $this->slider->getParam("loop_slide","loop");
		
		if(($oneSlideLoop == 'loop' || $oneSlideLoop == 'on') && $slider_type !== 'hero'){
			if(count($slides) == 1 && $this->oneSlideMode == false){
				$new_slide = clone(reset($slides));
				$new_slide->ignore_alt = true;
				$new_slide->setID($new_slide->getID().'-1');

				$slides[] = $new_slide;
				$this->hasOnlyOneSlide = true;
			}
		}

		if(count($slides) == 0) return false; // No Slides added yet
		
		$def_transition = $this->slider->getParam('def-slide_transition', 'fade');
		$def_image_source_type = $this->slider->getParam('def-image_source_type', 'full');
		
		$do_static =  true;
		
		if($do_static){
			$sliderID = $this->slider->getID();
			foreach($slides as $slide){
				$staticID = $slide->getStaticSlideID($sliderID);
				if($staticID !== false){
					$static_slide = new ZTSlide();
					$static_slide->initByStaticID($staticID);
					$this->static_slide = $static_slide;
				}
				break;
			}
		}
		
		$index = 0;
		foreach($slides as $slide){
			
			$params = $slide->getParams();

			$navigation_arrow_stlye = $this->slider->getParam('navigation_arrow_style', 'round');
			$navigation_bullets_style = $this->slider->getParam('navigation_bullets_style', 'round');

			//check if date is set
			$date_from = $slide->getParam('date_from', '');
			$date_to = $slide->getParam('date_to', '');
			
			if($this->previewMode === false){ // do only if we are not in preview mode
				$ts = JHtml::date();
					
				if($date_from != ''){
					$date_from = strtotime($date_from);
					if($ts < $date_from) continue;
				}
				
				if($date_to != ''){
					$date_to = strtotime($date_to);
					if($ts > $date_to) continue;
				}
			}
			
			$transition = $slide->getParam('slide_transition', $def_transition);
			
			if(!is_array($transition)){
				$transition_arr = explode(',', $transition);
			}else{
				$transition_arr = $transition;
				$transition = implode(',', $transition);
			}
			
			
			$add_rand = '';
			if(is_array($transition_arr) && !empty($transition_arr)){
				foreach($transition_arr as $tkey => $trans){
					if($trans == 'random-selected'){
						$add_rand = ' data-randomtransition="on"';
						unset($transition_arr[$tkey]);
						$transition = implode(',', $transition_arr);
						break;
					}
				}
			}
			
			$slotAmount = $slide->getParam('slot_amount', '7');
			if(is_array($slotAmount)) $slotAmount = implode(',', $slotAmount);
			
			$imageAddParams = '';
			
			$isExternal = $slide->getParam('background_type', 'image');
			if($isExternal != 'external'){
				$urlSlideImage = $slide->getImageUrl();
				
				//get image alt
				$alt_type = $slide->getParam('alt_option', 'file_name');
				$title_type = $slide->getParam('title_option', 'file_name');
				
				$alt = '';
				$img_title = '';
				$img_id = $slide->getImageID();
				
				switch($alt_type){
					case 'file_name':
						$imageFilename = $slide->getImageFilename();
						$info = pathinfo($imageFilename);
						$alt = $info['filename'];
					break;
					case 'custom':
						$alt = trim($slide->getParam('alt_attr', ''));
					break;
				}
				
				switch($title_type){
					case 'file_name':
						$imageFilename = $slide->getImageFilename();
						$info = pathinfo($imageFilename);
						$img_title = $info['filename'];
					break;
					case 'custom':
						$img_title = trim($slide->getParam('title_attr', ''));
					break;
				}
				
				$img_w = '';
				$img_h = '';
				
				$img_size = $slide->getParam('image_source_type', $def_image_source_type);
				
				if($img_id !== false){
					$img_data = false;
					if($img_data !== false && !empty($img_data)){
						if($img_size !== 'full'){
							if(isset($img_data['sizes']) && isset($img_data['sizes'][$img_size])){
								$img_w = (isset($img_data['sizes'][$img_size]['width'])) ? $img_data['sizes'][$img_size]['width'] : '';
								$img_h = (isset($img_data['sizes'][$img_size]['height'])) ? $img_data['sizes'][$img_size]['height'] : '';
							}
						}
						
						if($img_w == '' || $img_h == ''){
							$img_w = (isset($img_data['width'])) ? $img_data['width'] : '';
							$img_h = (isset($img_data['height'])) ? $img_data['height'] : '';
						}
						$imageAddParams .= ' width="'.$img_w.'" height="'.$img_h.'"';
					}
				}
				
			}else{
				$urlSlideImage = $slide->getParam('slide_bg_external', '');
				$alt = trim($slide->getParam('alt_attr', ''));
				$img_title = trim($slide->getParam('title_attr', ''));
				
				$img_w = $slide->getParam('ext_width', '1920');
				$img_h = $slide->getParam('ext_height', '1080');
				
				$imageAddParams .= ' width="'.$img_w.'" height="'.$img_h.'"';
			}
			
			if(isset($slide->ignore_alt)) $alt = '';

			$bgType = $slide->getParam('background_type', 'image');

			//get thumb url

			$is_special_nav = false;
			switch($navigation_arrow_stlye){ //generate also if we have a special navigation selected
				case 'preview1':
				case 'preview2':
				case 'preview3':
				case 'preview4':
				case 'custom':
					$is_special_nav = true;
			}
			switch($navigation_bullets_style){ //generate also if we have a special navigation selected
				case 'preview1':
				case 'preview2':
				case 'preview3':
				case 'preview4':
				case 'custom':
					$is_special_nav = true;
			}
			

			$htmlThumb = "";
			if($isThumbsActive == true || $is_special_nav == true){
				
				$urlThumb = null;

				if(empty($urlThumb)){
					$urlThumb = $slide->getParam('slide_thumb', '');
				}
				
				$thumb_do = $slide->getParam('thumb_dimension', 'slider');
				if($thumb_do == 'slider'){ //use the slider settings for width / height
				
					$th_width = intval($this->slider->getParam('previewimage_width', $this->slider->getParam('thumb_width', 100)));
					$th_height = intval($this->slider->getParam('previewimage_height', $this->slider->getParam('thumb_height', 50)));
					if($th_width == 0) $th_width = 100;
					if($th_height == 0) $th_height = 50;
					
					if($source_type == 'youtube' || $source_type == 'vimeo' || 
						$bgType == 'image' || $bgType == 'vimeo' || $bgType == 'youtube' || $bgType == 'html5' || 
						$bgType == 'streamvimeo' || $bgType == 'streamyoutube' || $bgType == 'streaminstagram' || $bgType == 'streamtwitter' ||
						$bgType == 'streamvimeoboth' || $bgType == 'streamyoutubeboth' || $bgType == 'streaminstagramboth' || $bgType == 'streamtwitterboth'){

						
						if(empty($urlThumb)){	//try to get resized thumb
							$url_img_link = $slide->getImageUrl();
							if (strpos($url_img_link, JUri::root()) === false) {$url_img_link = JUri::root().$url_img_link;}
							$urlThumb = JUri::root() . 'administrator/components/com_zt_layerslider/helpers/html/timthumb.php?src=' . $url_img_link . '&amp;h=' . $th_height . '&amp;w=' . $th_width;;//will use timthumb
						}else{
							if (strpos($urlThumb, JUri::root()) === false) {$urlThumb = JUri::root().$urlThumb;}
							$urlThumb = JUri::root() . 'administrator/components/com_zt_layerslider/helpers/html/timthumb.php?src=' . $urlThumb . '&amp;h=' . $th_height . '&amp;w=' . $th_width;;//will use timthumb
							if(empty($urlThumb)){
								$urlThumb = $slide->getImageUrl();
								if (strpos($urlThumb, JUri::root()) === false) {$urlThumb = JUri::root().$urlThumb;}
								if (strpos($urlThumb, JUri::root()) === false) {$urlThumb = JUri::root().$urlThumb;}
								$urlThumb = JUri::root() . 'administrator/components/com_zt_layerslider/helpers/html/timthumb.php?src=' . $urlThumb . '&amp;h=' . $th_height . '&amp;w=' . $th_width;;//will use timthumb
							}
							
						}
						
						//if not - put regular image:
						if(empty($urlThumb))
							$urlThumb = $slide->getImageUrl();
							if (strpos($urlThumb, JUri::root()) === false) {$urlThumb = JUri::root().$urlThumb;}
					}
				}else{
					//if not - put regular image:
					if(empty($urlThumb))
						$urlThumb = $slide->getImageUrl();
						if (strpos($urlThumb, JUri::root()) === false) {$urlThumb = JUri::root().$urlThumb;}
				}

				if(ZTSliderFunctions::is_ssl()){
					$urlThumb = str_replace('http://', 'https://', $urlThumb);
				}else{
					$urlThumb = str_replace('https://', 'http://', $urlThumb);
				}
				
				$htmlThumb = ' data-thumb="'.$urlThumb.'" ';
				
			}

			//get link
			$htmlLink = '';
			$enableLink = $slide->getParam('enable_link', 'false');
			if($enableLink == 'true'){
				$linkType = $slide->getParam('link_type', 'regular');
				switch($linkType){

					//---- normal link
					default:
					case 'regular':
						$link = $slide->getParam('link', '');///
						$linkOpenIn = $slide->getParam('link_open_in', 'same');
						$htmlTarget = '';
						if($linkOpenIn == 'new')
							$htmlTarget = ' data-target="_blank"';
						$htmlLink = ' data-link="'.$link.'" '.$htmlTarget.' ';
					break;

					//---- link to slide
					case 'slide':
						$slideLink = ZTSliderFunctions::getVal($params, 'slide_link');
						if(!empty($slideLink) && $slideLink != 'nothing'){
							//get slide index from id
							if(is_numeric($slideLink))
								$slideLink = ZTSliderFunctions::getVal($this->slidesNumIndex, $slideLink);

							if(!empty($slideLink)){
								$htmlLink = ' data-link="slide" data-linktoslide="'.$slideLink.'" ';
							}
						}
					break;
				}

				//set link position:
				$linkPos = ZTSliderFunctions::getVal($params, 'link_pos', 'front');
				if($linkPos == 'back')
					$htmlLink .= ' data-slideindex="back"';
			}

			//set delay
			$htmlDelay = '';
			$delay = $slide->getParam('delay', '');
			if(!empty($delay) && is_numeric($delay))
				$htmlDelay = ' data-delay="'.$delay.'" ';

			//set Stop Slide on Purpose
			$htmlStopPurpose= '';
			$stoponpurpose = $slide->getParam('stoponpurpose', '');
			if (!empty($stoponpurpose) && $stoponpurpose=="true")
				$htmlStopPurpose = ' data-ssop="true" ';

			//set Stop Slide on Purpose
			$htmlInvisibleSlide= '';
			$invisibleslide = $slide->getParam('invisibleslide', '');
			if (!empty($invisibleslide) && $invisibleslide=="true")
				$htmlInvisibleSlide = ' data-invisible="true" ';

			//get duration
			$htmlDuration = '';
			$duration = $slide->getParam('transition_duration', $this->slider->getParam('def-transition_duration', ''));
			
			if(is_array($duration)) $duration = implode(',', $duration);
			
			if(!empty($duration))
				$htmlDuration = ' data-masterspeed="'.$duration.'" ';

			//get performance
			$htmlPerformance = '';
			$performance = $slide->getParam('save_performance', 'off');
			if(!empty($performance) && ($performance == 'on' || $performance == 'off'))
				$htmlPerformance = ' data-saveperformance="'.$performance.'" ';



			//get rotation
			$htmlRotation = '';
			$rotation = (array) $slide->getParam('transition_rotation', '');
			if(!empty($rotation)){
				$rot_string = '';
				foreach($rotation as $rkey => $rot){
					$rot = (int)$rot;
					if($rot != 0){
						if($rot > 720 && $rot != 999)
							$rot = 720;
						if($rot < -720)
							$rot = -720;
					}
					if($rkey > 0) $rot_string .= ',';
					$rot_string .= $rot;
				}
				$htmlRotation = ' data-rotate="'.$rot_string.'" ';
			}
			
			$htmlEaseIn = '';
			$easein = $slide->getParam('transition_ease_in', 'default');
			if(!empty($easein) && is_array($easein)) $easein = implode(',', $easein);
			if($easein !== '') $htmlEaseIn = ' data-easein="'.$easein.'"';
			
			$htmlEaseOut = '';
			$easeout = $slide->getParam('transition_ease_out', 'default');
			
			if(!empty($easeout) && is_array($easeout)) $easeout = implode(',', $easeout);
			
			if($easeout !== '') $htmlEaseOut = ' data-easeout="'.$easeout.'"';
			
			$fullWidthVideoData = $this->getSlideFullWidthVideoData($slide);
			
			//set first slide transition
			$htmlFirstTrans = '';
			
			if($index == $startWithSlide && $slider_type !== 'hero'){
				$htmlFirstTrans = $htmlFirstTransWrap;
			}//first trans

			$htmlParams = $htmlEaseIn.$htmlEaseOut.$htmlDuration.$htmlLink.$htmlThumb.$htmlDelay.$htmlRotation.$htmlFirstTrans.$htmlPerformance.$htmlStopPurpose.$htmlInvisibleSlide;


			$styleImage = '';
			$urlImageTransparent = ZT_ASSETS_URL.'/assets/images/transparent.png';

			switch($bgType){
				case 'trans':
					$urlSlideImage = $urlImageTransparent;
				break;
				case 'solid':
					$urlSlideImage = $urlImageTransparent;
					$slideBGColor = $slide->getParam('slide_bg_color', '#d0d0d0');
					$styleImage = "style='background-color:".$slideBGColor."'";
				break;
				case 'streamvimeo':
				case 'streamyoutube':
				case 'streaminstagram':
				case 'streamtwitter':
					if($slide->getParam('stream_do_cover', 'on') == 'off'){
						$urlSlideImage = $urlImageTransparent;
					}
				break;
				case 'streamvimeoboth':
				case 'streamyoutubeboth':
				case 'streaminstagramboth':
				case 'streamtwitterboth':
					if($this->checkIfStreamVideoExists($slide)){
						if($slide->getParam('stream_do_cover_both', 'on') == 'off'){
							$urlSlideImage = $urlImageTransparent;
						}
					}
				break;
			}
			
			if(trim($urlSlideImage) == '') $urlSlideImage = $urlImageTransparent; //go back to transparent if img is empty
			
			//additional params
			if($lazyLoad != 'none'){
				$imageAddParams .= ' data-lazyload="'.$urlSlideImage.'"';
				$urlSlideImage = ZT_ASSETS_URL.'/assets/images/dummy.png';
			}
			
			//additional background params
			$bgFit = $slide->getParam('bg_fit', $this->slider->getParam('def-background_fit', 'cover'));
			$bgFitX = intval($slide->getParam('bg_fit_x', $this->slider->getParam('def-bg_fit_x', '100')));
			$bgFitY = intval($slide->getParam('bg_fit_y', $this->slider->getParam('def-bg_fit_y', '100')));

			$bgPosition = $slide->getParam('bg_position', $this->slider->getParam('def-bg_position', 'center center'));
			
			if($bgType == 'streamvimeoboth' || $bgType == 'streamyoutubeboth' || $bgType == 'streaminstagramboth' || $bgType == 'streamtwitterboth'){
				if($this->checkIfStreamVideoExists($slide)){
					$bgPosition = 'center center';
				}
			}else{
				if($bgType == 'youtube' || $bgType == 'vimeo' || $bgType == 'html5' || $bgType == 'streamvimeo' || $bgType == 'streamyoutube' || $bgType == 'streaminstagram' || $bgType == 'streamtwitter'){
					$bgPosition = 'center center';
				}
			}
			
			$bgPositionX = intval($slide->getParam('bg_position_x', $this->slider->getParam('def-bg_position_x', '0')));
			$bgPositionY = intval($slide->getParam('bg_position_y', $this->slider->getParam('def-bg_position_y', '0')));

			$bgRepeat = $slide->getParam('bg_repeat', $this->slider->getParam('def-bg_repeat', 'no-repeat'));

			if($bgPosition == 'percentage'){
				$imageAddParams .= ' data-bgposition="'.$bgPositionX.'% '.$bgPositionY.'%"';
			}else{
				$imageAddParams .= ' data-bgposition="'.$bgPosition.'"';
			}

			//check for kenburn & pan zoom
			$kenburn_effect = $slide->getParam('kenburn_effect', $this->slider->getParam('def-kenburn_effect', 'off'));
			$kb_duration = intval($slide->getParam('kb_duration', $this->slider->getParam('def-kb_duration', '10000')));
			$kb_ease = $slide->getParam('kb_easing', $this->slider->getParam('def-kb_easing', 'Linear.easeNone'));
			$kb_start_fit = $slide->getParam('kb_start_fit', $this->slider->getParam('def-kb_start_fit', '100'));
			$kb_end_fit = $slide->getParam('kb_end_fit', $this->slider->getParam('def-kb_end_fit', '100'));
			
			$kb_start_offset_x = $slide->getParam('kb_start_offset_x', $this->slider->getParam('def-kb_start_offset_x', '0'));
			$kb_start_offset_y = $slide->getParam('kb_start_offset_y', $this->slider->getParam('def-kb_start_offset_y', '0'));
			$kb_end_offset_x = $slide->getParam('kb_end_offset_x', $this->slider->getParam('def-kb_end_offset_x', '0'));
			$kb_end_offset_y = $slide->getParam('kb_end_offset_y', $this->slider->getParam('def-kb_end_offset_y', '0'));
			$kb_start_rotate = $slide->getParam('kb_start_rotate', $this->slider->getParam('def-kb_start_rotate', '0'));
			$kb_end_rotate = $slide->getParam('kb_end_rotate', $this->slider->getParam('def-kb_end_rotate', '0'));

			$kb_pz = '';
			
			if($kenburn_effect == "on" && ($bgType == 'image' || $bgType == 'external')){
				$kb_pz .= ' data-kenburns="on"';
				$kb_pz .= ' data-duration="'.$kb_duration.'"';
				$kb_pz .= ' data-ease="'.$kb_ease.'"';
				$kb_pz .= ' data-scalestart="'.$kb_start_fit.'"';
				$kb_pz .= ' data-scaleend="'.$kb_end_fit.'"';
				$kb_pz .= ' data-rotatestart="'.$kb_start_rotate.'"';
				$kb_pz .= ' data-rotateend="'.$kb_end_rotate.'"';
				$kb_pz .= ' data-offsetstart="'.$kb_start_offset_x.' '.$kb_start_offset_y.'"';
				$kb_pz .= ' data-offsetend="'.$kb_end_offset_x.' '.$kb_end_offset_y.'"';
				
			}else{ //only set if kenburner is off and not a background video
				if($bgType == 'youtube' || $bgType == 'html5' || $bgType == 'vimeo' || $bgType == 'streamvimeo' || $bgType == 'streamyoutube' || $bgType == 'streaminstagram' || $bgType == 'streamtwitter'){
					$imageAddParams .= ' data-bgfit="cover"';
				}else{
					if($bgFit == 'percentage'){
						$imageAddParams .= ' data-bgfit="'.$bgFitX.'% '.$bgFitY.'%"';
					}else{
						$imageAddParams .= ' data-bgfit="'.$bgFit.'"';
					}

					$imageAddParams .= ' data-bgrepeat="'.$bgRepeat.'"';
				}
			}


			//add Slide Title if we have special navigation type choosen
			$slide_title = '';

			$class_attr = $slide->getParam("class_attr","");
			if($class_attr !== '')
				$htmlParams .= ' class="'.$class_attr.'"';

			$id_attr = $slide->getParam('id_attr', '');
			if($id_attr !== '')
				$htmlParams .= ' id="'.$id_attr.'"';


			$data_attr = stripslashes($slide->getParam('data_attr', ''));
			if($data_attr !== '')
				$htmlParams .= ' '.$data_attr;
			
			if($post_based_slider){//check if we are post based or normal slider
				$this->db->setQuery('SELECT * FROM #__content WHERE id='.$slide->getID());
				$_result = $this->db->loadObject();
				if ($_result) {
					$the_post = $_result->id;
					$new_title = $_result->title;

					//prepare cotent
					$dispatcher = JEventDispatcher::getInstance();
					JPluginHelper::importPlugin('content');
					$dispatcher->trigger('onContentPrepare', array ('com_content.article', &$_result, &$_result->params, 0));

					if (!empty($_result->introtext)) {
						$the_excerpt = $_result->introtext;
					} else {
						$the_excerpt = substr($_result->fulltext,0,99);
					}

					$slide_title = ' data-title="'.stripslashes(trim($new_title)).'"';
					$slide_description = ' data-description="'.str_replace(array("\'", '\"'), array("'", '"'), trim($the_excerpt)).'"';
				}
			}else{
				$slide_title = ' data-title="'.stripslashes(trim($slide->getParam("title","Slide"))).'"';
				$slide_description = ' data-description="'.str_replace(array("\'", '\"'), array("'", '"'), trim($slide->getParam('slide_description', ''))).'"';
			}
			
			$slide_id = $slide->getID();
			if($slide->getParam('slide_id','') !== '') $slide_id = trim($slide_id);
			
			$add_params = '';
			for($mi=1;$mi<=10;$mi++){
				$pa = $slide->getParam('params_'.$mi,'');
				
				//add meta functionality here
				
				if($pa !== ''){
					$pa_limit = $slide->getParam('params_'.$mi.'_chars',10,ZTSlider::FORCE_NUMERIC);
					$pa = strip_tags($pa);
					$pa = mb_substr($pa, 0, $pa_limit, 'utf-8');
				}
				$add_params .= ' data-param'.$mi.'="'.stripslashes(trim($pa)).'"';
			}
			
			$use_parallax = $this->slider->getParam("use_parallax", $this->slider->getParam('use_parallax', 'off'));

			$parallax_attr = '';
			if($use_parallax == 'on'){
				$slide_level = $slide->getParam('slide_parallax_level', '-');
				if($slide_level == '-') $slide_level = 'off';
					
				$parallax_attr = ' data-bgparallax="'.$slide_level.'"';
			}
			
			$hideslideafter = $slide->getParam("hideslideafter",0);
			
			$hsom = $slide->getParam('hideslideonmobile', 'off');
			
			if($img_title !== '') $img_title = ' title="'.$img_title.'"';
			
			//Html rev-main-
			echo '	<!-- SLIDE  -->'."\n";
			echo '	<li data-index="rs-'.$slide_id.'" data-transition="'.$transition.'" data-slotamount="'. $slotAmount.'" data-hideafterloop="'.$hideslideafter.'" data-hideslideonmobile="'.$hsom.'" '.$add_rand.$htmlParams.$slide_title.$add_params.$slide_description;

			
			echo '>'."\n";
			
			echo '		<!-- MAIN IMAGE -->'."\n";
			echo '		<img src="'. $urlSlideImage .'" '. $styleImage.' alt="'. $alt . '"'. $img_title.' '. $imageAddParams. $kb_pz . $parallax_attr .' class="rev-slidebg" data-no-retina>'."\n";
			echo '		<!-- LAYERS -->'."\n";
			
			//check if we are youtube, vimeo or html5
			if($bgType == 'youtube' || $bgType == 'html5' || $bgType == 'vimeo' || $bgType == 'streamvimeo' || $bgType == 'streamyoutube' || $bgType == 'streaminstagram' || $bgType == 'streamtwitter'){
				$this->putBackgroundVideo($slide);
			}
			if($bgType == 'streamvimeoboth' || $bgType == 'streamyoutubeboth' || $bgType == 'streaminstagramboth' || $bgType == 'streamtwitterboth'){
				if($this->checkIfStreamVideoExists($slide)){
					$this->putBackgroundVideo($slide);
				}
			}
			
			$this->putCreativeLayer($slide);

			
			echo "	</li>\n";
			
			
			$index++;
			
		}	//get foreach
		
		echo "</ul>\n";
		
		if($do_static && !empty($this->static_slide)){
			$layers = $this->static_slide->getLayers();
			if(!empty($layers)){
				$htmlstaticoverflow = '';
				$staticoverflow = $this->static_slide->getParam('staticoverflow', '');
				if (!empty($staticoverflow) && $staticoverflow=="hidden")
					$htmlstaticoverflow = 'overflow:hidden;width:100%;height:100%;top:0px;left:0px;';

				
				//check for static layers
				echo '<div style="'.$htmlstaticoverflow.'" class="tp-static-layers">'."\n";
				$this->putCreativeLayer($this->static_slide, true);

				
				echo '</div>'."\n";
			}
		}
		
		$this->add_custom_navigation_css($slides);
		
	}
	
	
	/**
	 * check if a stream video exists
	 * @since: 5.0
	 */
	public function checkIfStreamVideoExists($slide){
		$type = $slide->getParam('background_type', 'image');
		
		$vid = '';
		switch($type){
			case 'streamyoutubeboth'://youtube
				$vid = $slide->getParam('slide_bg_youtube', '');
			break;
			case 'streamvimeoboth'://vimeo
				$vid = $slide->getParam('slide_bg_vimeo', '');
			break;
			case 'streaminstagramboth'://instagram
				$vid = $slide->getParam('slide_bg_html_mpeg', '');
			break;
			case 'streamtwitterboth'://instagram
				$vid = $slide->getParam('slide_bg_html_mpeg', '');
					if($vid !== '') return true;
				$vid = $slide->getParam('slide_bg_youtube', '');
					if($vid !== '') return true;
				$vid = $slide->getParam('slide_bg_vimeo', '');
					if($vid !== '') return true;
			break;
		}
		
		return ($vid == '') ? false : true;
	}
	
	
	/**
	 * add background video layer
	 * @since: 5.0
	 */
	public function putBackgroundVideo($slide){
		
		$add_data = '';
		
		$enable_custom_size_notebook = $this->slider->getParam('enable_custom_size_notebook','off');
		$enable_custom_size_tablet = $this->slider->getParam('enable_custom_size_tablet','off');
		$enable_custom_size_iphone = $this->slider->getParam('enable_custom_size_iphone','off');
		$enabled_sizes = array('desktop' => 'on', 'notebook' => $enable_custom_size_notebook, 'tablet' => $enable_custom_size_tablet, 'mobile' => $enable_custom_size_iphone);
		$adv_resp_sizes = ($enable_custom_size_notebook == 'on' || $enable_custom_size_tablet == 'on' || $enable_custom_size_iphone == 'on') ? true : false;
		
		$videoType = $slide->getParam('background_type', 'image');
		
		$poster_url = $slide->getImageUrl();

		$cover = $slide->getParam('video_force_cover', 'on');
		$dotted_overlay = $slide->getParam('video_dotted_overlay', 'none');
		$ratio = $slide->getParam('video_ratio', 'none');
		$loop = $slide->getParam('video_loop', 'none');
		
		$nextslide = $slide->getParam('video_nextslide', 'off');
		$force_rewind = $slide->getParam('video_force_rewind', 'off');
		$mute_video = $slide->getParam('video_mute', 'on');
		$volume_video = $slide->getParam('video_volume', '100');
		
		$video_start_at = $slide->getParam('video_start_at', '');
		$video_end_at = $slide->getParam('video_end_at', '');
		
		//------------------------
		$videoWidth = '100%';
		$videoHeight = '100%';
		
		if($adv_resp_sizes == true){
			if(is_object($videoWidth)){
				$videoWidth = ZTSliderFunctions::normalize_device_settings($videoWidth, $enabled_sizes, 'html-array');
			}
			if(is_object($videoHeight)){
				$videoHeight = ZTSliderFunctions::normalize_device_settings($videoHeight, $enabled_sizes, 'html-array');
			}
		}else{
			if(is_object($videoWidth)) $videoWidth = ZTSliderFunctions::get_biggest_device_setting($videoWidth, $enabled_sizes);
			if(is_object($videoHeight)) $videoHeight = ZTSliderFunctions::get_biggest_device_setting($videoHeight, $enabled_sizes);
		}
		
		$add_data = ($force_rewind == 'on') ? '			data-forcerewind="on"'." \n" : '';
		$add_data .= ($mute_video == 'on') ? '			data-volume="mute"'." \n" : '';
		
		$setBase = (ZTSliderFunctions::is_ssl()) ? 'https://' : 'http://';
		
		$video_added = false;
		
		switch($videoType){
			case 'streamtwitter':
			case 'streamtwitterboth':
			case 'twitter':
				$youtube_id = $slide->getParam('slide_bg_youtube', '');
				$vimeo_id = $slide->getParam('slide_bg_vimeo', '');
				$html_mpeg = $slide->getParam('slide_bg_html_mpeg', '');
				
				if($youtube_id !== ''){
					$arguments = $slide->getParam('video_arguments', ZTSlider::DEFAULT_YOUTUBE_ARGUMENTS);
					$speed = $slide->getParam('video_speed', '1');
				
					if(empty($arguments))
						$arguments = ZTSlider::DEFAULT_YOUTUBE_ARGUMENTS;
					
					if($mute_video == 'off'){
						$add_data .= '			data-volume="'.intval($volume_video).'"'." \n";
						$arguments = 'volume='.intval($volume_video).'&amp;'.$arguments;
					}
					
					$arguments.=';origin='.$setBase.$_SERVER['SERVER_NAME'].';';
					$add_data .= '			data-ytid="'.$youtube_id.'"'." \n";
					$add_data .= '			data-videoattributes="version=3&amp;enablejsapi=1&amp;html5=1&amp;'.$arguments.'"'." \n";
					$add_data .= '			data-videorate="'.$speed.'"'." \n";
					$add_data .= '			data-videowidth="'.$videoWidth.'"'." \n";
					$add_data .= '			data-videoheight="'.$videoHeight.'"'." \n";
					$add_data .= '			data-videocontrols="none"'." \n";
					
					$video_added = true;
				}elseif($vimeo_id !== ''){
					$arguments = $slide->getParam('video_arguments_vim', ZTSlider::DEFAULT_VIMEO_ARGUMENTS);
				
					if(empty($arguments))
						$arguments = ZTSlider::DEFAULT_VIMEO_ARGUMENTS;
					
					if($mute_video == 'off'){
						$add_data .= '			data-volume="'.intval($volume_video).'"'." \n";
					}
					
					//check if full URL
					if(strpos($vimeo_id, 'http') !== false){
						//we have full URL, split it to ID
						$video_id = explode('vimeo.com/', $vimeo_id);
						$vimeo_id = $video_id[1];
					}
					
					$add_data .= '			data-vimeoid="'.$vimeo_id.'"'." \n";
					$add_data .= '			data-videoattributes="'.$arguments.'"'." \n";
					$add_data .= '			data-videowidth="'.$videoWidth.'"'." \n";
					$add_data .= '			data-videoheight="'.$videoHeight.'"'." \n";
					$add_data .= '			data-videocontrols="none"'." \n";
					
					$video_added = true;
				}elseif($html_mpeg !== ''){
					$add_data .=  '			data-videowidth="'.$videoWidth.'"'." \n";
					$add_data .= '			data-videoheight="'.$videoHeight.'"'." \n";
					
					if(!empty($html_mpeg)) $add_data .= '			data-videomp4="'.$html_mpeg.'"'." \n";
					
					$add_data .= '			data-videopreload="auto"'." \n";
					
					$video_added = true;
				}
			break;
			case 'streamyoutube':
			case 'streamyoutubeboth':
			case 'youtube':
				$arguments = $slide->getParam('video_arguments', ZTSlider::DEFAULT_YOUTUBE_ARGUMENTS);
				$speed = $slide->getParam('video_speed', '1');
				
				if(empty($arguments))
					$arguments = ZTSlider::DEFAULT_YOUTUBE_ARGUMENTS;
				
				if($mute_video == 'off'){
					$add_data .= '			data-volume="'.intval($volume_video).'"'." \n";
					$arguments = 'volume='.intval($volume_video).'&amp;'.$arguments;
				}
				
				$youtube_id = $slide->getParam('slide_bg_youtube', '');
				
				if($youtube_id == '') return false;
				
				//check if full URL
				if(strpos($youtube_id, 'http') !== false){
					//we have full URL, split it to ID
					parse_str( parse_url( $youtube_id, PHP_URL_QUERY ), $my_v_ret );
					$youtube_id = $my_v_ret['v'];
				}
				
				$arguments.=';origin='.$setBase.$_SERVER['SERVER_NAME'].';';
				$add_data .= '			data-ytid="'.$youtube_id.'"'." \n";
				$add_data .= '			data-videoattributes="version=3&amp;enablejsapi=1&amp;html5=1&amp;'.$arguments.'"'." \n";
				$add_data .= '			data-videorate="'.$speed.'"'." \n";
				$add_data .= '			data-videowidth="'.$videoWidth.'"'." \n";
				$add_data .= '			data-videoheight="'.$videoHeight.'"'." \n";
				$add_data .= '			data-videocontrols="none"'." \n";
				
				$video_added = true;
				
			break;
			case 'streamvimeo':
			case 'streamvimeoboth':
			case 'vimeo':
				$arguments = $slide->getParam('video_arguments_vim', ZTSlider::DEFAULT_VIMEO_ARGUMENTS);
				
				if(empty($arguments))
					$arguments = ZTSlider::DEFAULT_VIMEO_ARGUMENTS;
				
				$arguments ='background=1&'.$arguments;
				if($mute_video == 'off'){
					$add_data .= '			data-volume="'.intval($volume_video).'"'." \n";
				}
				
				$vimeo_id = $slide->getParam('slide_bg_vimeo', '');
				
				if($vimeo_id == '') return false;
				
				//check if full URL
				if(strpos($vimeo_id, 'http') !== false){
					//we have full URL, split it to ID
					$video_id = explode('vimeo.com/', $vimeo_id);
					$vimeo_id = $video_id[1];
				}
				
				$add_data .= '			data-vimeoid="'.$vimeo_id.'"'." \n";
				$add_data .= '			data-videoattributes="'.$arguments.'"'." \n";
				$add_data .= '			data-videowidth="'.$videoWidth.'"'." \n";
				$add_data .= '			data-videoheight="'.$videoHeight.'"'." \n";
				$add_data .= '			data-videocontrols="none"'." \n";
				
				$video_added = true;
			break;
			case 'streaminstagram':
			case 'streaminstagramboth':
			case 'html5':
				
				$html_mpeg = $slide->getParam('slide_bg_html_mpeg', '');
				$html_webm = $slide->getParam('slide_bg_html_webm', '');
				$html_ogv = $slide->getParam('slide_bg_html_ogv', '');
				
				if($videoType == 'streaminstagram' || $videoType == 'streaminstagramboth'){
					$html_webm = '';
					$html_ogv = '';
				}
				
				$add_data .=  '			data-videowidth="'.$videoWidth.'"'." \n";
				$add_data .= '			data-videoheight="'.$videoHeight.'"'." \n";
				
				if(!empty($html_ogv)) $add_data .= '			data-videoogv="'.$html_ogv.'"'." \n";
				if(!empty($html_webm)) $add_data .= '			data-videowebm="'.$html_webm.'"'." \n";
				if(!empty($html_mpeg)) $add_data .= '			data-videomp4="'.$html_mpeg.'"'." \n";
				
				$add_data .= '			data-videopreload="auto"'." \n";
				
				$video_added = true;
			break;
		}
		
		if($video_added == false) return false; // return here if no valid video was found.
		
		if($video_start_at !== '')
			$add_data .= '			data-videostartat="'.$video_start_at.'"'." \n";
		if($video_end_at !== '')
			$add_data .= '			data-videoendat="'.$video_end_at.'"'." \n";
		
		if($loop === true){ //fallback
			$add_data .= '			data-videoloop="loop"'." \n";
		}else{
			$add_data .= '			data-videoloop="'.$loop.'"'." \n";
		}
		
		if($cover == 'on'){
			if($dotted_overlay !== 'none')
				$add_data .= '			data-dottedoverlay="'.$dotted_overlay.'"'." \n";
				
			$add_data .=  '			data-forceCover="1"'." \n";
				
			
		}
		
		if(!empty($ratio))
			$add_data .= '			data-aspectratio="'.$ratio.'"'." \n";
			
		$add_data .= '			data-autoplay="true"'." \n";
		$add_data .= '			data-autoplayonlyfirsttime="false"'." \n";
		if($nextslide === 'on')
			$add_data .= '			data-nextslideatend="true"'." \n";
		
		echo "\n		<!-- BACKGROUND VIDEO LAYER -->\n";
		echo '		<div class="rs-background-video-layer" '."\n";
		echo $add_data;
		echo '></div>';
	}

	
	/**
	 * put creative layer
	 */
	private function putCreativeLayer(ZTSliderSlide $slide, $static_slide = false){
		$layers = $slide->getLayers();
		$slider_type = $this->slider->getParam('slider-type');
		$icon_sets = ZtSliderSlider::set_icon_sets(array());
		
		$ignore_styles = array('font-family', 'color', 'font-weight', 'font-style', 'text-decoration');
		
		$customAnimations = ZTSliderOperations::getCustomAnimations('customin'); //get all custom animations
		$customEndAnimations = ZTSliderOperations::getCustomAnimations('customout'); //get all custom animations
		
		$startAnimations = ZTSliderOperations::getArrAnimations(false); //only get the standard animations
		$endAnimations = ZTSliderOperations::getArrEndAnimations(false); //only get the standard animations
		
		$lazyLoad = $this->slider->getParam('lazy_load_type', false);
		if($lazyLoad === false){ //do fallback checks to removed lazy_load value since version 5.0 and replaced with an enhanced version
			$old_ll = $this->slider->getParam('lazy_load', 'off');
			$lazyLoad = ($old_ll == 'on') ? 'all' : 'none';
		}
		
		$isTemplate = $this->slider->getParam('template', 'false');
		
		$enable_custom_size_notebook = $this->slider->getParam('enable_custom_size_notebook', 'off');
		$enable_custom_size_tablet = $this->slider->getParam('enable_custom_size_tablet', 'off');
		$enable_custom_size_iphone = $this->slider->getParam('enable_custom_size_iphone', 'off');
		$enabled_sizes = array('desktop' => 'on', 'notebook' => $enable_custom_size_notebook, 'tablet' => $enable_custom_size_tablet, 'mobile' => $enable_custom_size_iphone);
		$adv_resp_sizes = ($enable_custom_size_notebook == 'on' || $enable_custom_size_tablet == 'on' || $enable_custom_size_iphone == 'on') ? true : false;
		
		$slider_selectable = $this->slider->getParam('def-layer_selection', 'off');
		
		$image_source_type = $this->slider->getParam('def-image_source_type', 'full');
		
		if(empty($layers))
			return(false);

		$zIndex = 5;
			
		$slideID = $slide->getID();
		
		$in_class_usage = array();
		
		foreach($layers as $layer){
			$unique_id = ZTSliderFunctions::getVal($layer, 'unique_id');
			
			if($unique_id == '')
				$unique_id = $zIndex - 4;
			
			//$visible = ZTSliderFunctions::getVal($layer, 'visible', true);
			//if($visible == false) continue;
			
			$type = ZTSliderFunctions::getVal($layer, 'type', 'text');
			$svg_val = '';

			//set if video full screen
			$videoclass = '';
			
			$isFullWidthVideo = false;
			if($type == 'video'){
				$videoclass = ' tp-videolayer';
				$videoData = ZTSliderFunctions::getVal($layer, 'video_data');
				if(!empty($videoData)){
					$videoData = (array)$videoData;
					$isFullWidthVideo = ZTSliderFunctions::getVal($videoData, 'fullwidth');
					$isFullWidthVideo = ZTSliderFunctions::strToBool($isFullWidthVideo);
				}else
					$videoData = array();
			}elseif($type == 'audio'){
				$videoclass = ' tp-audiolayer';
				$videoData = ZTSliderFunctions::getVal($layer, 'video_data');
				if(!empty($videoData)){
					$videoData = (array)$videoData;
				}else
					$videoData = array();
			}

			$class = ZTSliderFunctions::getVal($layer, 'style');
			
			if(trim($class) !== ''){
				$this->class_include['.'.trim($class)] = true; //add classname for style inclusion
				//get class styles for further compare usage
				
				if(!isset($in_class_usage[trim($class)])) $in_class_usage[trim($class)] = ZTSliderOperations::getCaptionsContentArray(trim($class));
			}
			
			//set defaults for stylings
			$dff = '';
			$dta = 'left';
			$dttr = 'none';
			$dfs = 'normal';
			$dtd = 'none';
			$dpa = '0px 0px 0px 0px';
			$dbs = 'none';
			$dbw = '0px';
			$dbr = '0px 0px 0px 0px';
			$dc = 'auto';
			
			$dfos = false;
			$dlh = false;
			$dfw = false;
			
			$dco = false;
			$dcot = 1;
			$dbc = 'transparent';
			$dbt = 1;
			$dboc = 'transparent';
			$dbot = 1;
			
			/**
			* remove this following to get back to 5.0.4.1 in terms of output styling
			**/
			$do_remove_inline = true;
			
			if($do_remove_inline){
				if(isset($in_class_usage[trim($class)]) && isset($in_class_usage[trim($class)]['params'])){//defaults get set here

					$dfos = (isset($in_class_usage[trim($class)]['params']->{'font-size'})) ? $in_class_usage[trim($class)]['params']->{'font-size'} : $dfos;
					$dlh = (isset($in_class_usage[trim($class)]['params']->{'line-height'})) ? $in_class_usage[trim($class)]['params']->{'line-height'} : $dlh;
					$dfw = (isset($in_class_usage[trim($class)]['params']->{'font-weight'})) ? $in_class_usage[trim($class)]['params']->{'font-weight'} : $dfw;
					
					$dco = (isset($in_class_usage[trim($class)]['params']->{'color'})) ? $in_class_usage[trim($class)]['params']->{'color'} : $dco;
					$dcot = (isset($in_class_usage[trim($class)]['params']->{'color-transparency'})) ? $in_class_usage[trim($class)]['params']->{'color-transparency'} : $dcot;
					$dbc = (isset($in_class_usage[trim($class)]['params']->{'background-color'})) ? $in_class_usage[trim($class)]['params']->{'background-color'} : $dbc;
					$dbt = (isset($in_class_usage[trim($class)]['params']->{'background-transparency'})) ? $in_class_usage[trim($class)]['params']->{'background-transparency'} : $dbt;
					$dboc = (isset($in_class_usage[trim($class)]['params']->{'border-color'})) ? $in_class_usage[trim($class)]['params']->{'border-color'} : $dboc;
					$dbot = (isset($in_class_usage[trim($class)]['params']->{'border-transparency'})) ? $in_class_usage[trim($class)]['params']->{'border-transparency'} : $dbot;
					
					
					$dff = (isset($in_class_usage[trim($class)]['params']->{'font-family'})) ? $in_class_usage[trim($class)]['params']->{'font-family'} : $dff;
					$dta =  (isset($in_class_usage[trim($class)]['params']->{'text-align'})) ?     $in_class_usage[trim($class)]['params']->{'text-align'} : $dta;
					$dttr = (isset($in_class_usage[trim($class)]['params']->{'text-transform'})) ? $in_class_usage[trim($class)]['params']->{'text-transform'} : $dttr;
					$dfs = (isset($in_class_usage[trim($class)]['params']->{'font-styles'})) ? $in_class_usage[trim($class)]['params']->{'font-styles'} : $dfs;
					$dtd = (isset($in_class_usage[trim($class)]['params']->{'text-decoration'})) ? $in_class_usage[trim($class)]['params']->{'text-decoration'} : $dtd;
					$dpa = (isset($in_class_usage[trim($class)]['params']->{'padding'})) ? $in_class_usage[trim($class)]['params']->{'padding'} : $dpa;
					if(is_array($dpa)) $dpa = implode(' ', $dpa);
					$dbs = (isset($in_class_usage[trim($class)]['params']->{'border-style'})) ? $in_class_usage[trim($class)]['params']->{'border-style'} : $dbs;
					$dbw = (isset($in_class_usage[trim($class)]['params']->{'border-width'})) ? $in_class_usage[trim($class)]['params']->{'border-width'} : $dbw;
					$dbr = (isset($in_class_usage[trim($class)]['params']->{'border-radius'})) ? $in_class_usage[trim($class)]['params']->{'border-radius'} : $dbr;
					if(is_array($dbr)) $dbr = implode(' ', $dbr);
					$dc = (isset($in_class_usage[trim($class)]['params']->{'css_cursor'})) ? $in_class_usage[trim($class)]['params']->{'css_cursor'} : $dc;
					
				}
			}
			
			$animation = ZTSliderFunctions::getVal($layer, 'animation', 'tp-fade');
			//if($animation == "fade") $animation = "tp-fade";

			$customin = '';
			$maskin = '';

			if(!array_key_exists($animation, $startAnimations) && array_key_exists($animation, $customAnimations)){ //if true, add custom animation
				//check with custom values, if same, we do not need to write all down and just refer
			}
			
			$tcin = ZTSliderOperations::parseCustomAnimationByArray($layer, 'start');
			if($tcin !== ''){
				$customin = ' data-transform_in="' . $tcin . '"';
			}
			
			$do_mask_in = ZTSliderFunctions::getVal($layer, 'mask_start',false);
			if($do_mask_in){
				$tmask = ZTSliderOperations::parseCustomMaskByArray($layer, 'start');
				if($tmask !== ''){
					$maskin = ' data-mask_in="' . $tmask . '"';
				}
			}
			
			
			//if(strpos($animation, 'customin-') !== false || strpos($animation, 'customout-') !== false) $animation = "tp-fade";

			//set output class:

			$layer_2d_rotation = intval(ZTSliderFunctions::getVal($layer, '2d_rotation', '0'));
			
			$internal_class = ZTSliderFunctions::getVal($layer, 'internal_class', '');

			$layer_selectable = ZTSliderFunctions::getVal($layer, 'layer-selectable', 'default');
			
			$outputClass = 'tp-caption '. trim($class);
			
			$outputClass = trim($outputClass) . ' ' . $internal_class . ' ';
			if ($layer_selectable !== "default"){
				if($layer_selectable == 'on'){
					$outputClass = trim($outputClass) . ' tp-layer-selectable ';
				}
			}else{
				if($slider_selectable == 'on'){
					$outputClass = trim($outputClass) . ' tp-layer-selectable ';
				}
			}
			
			//if($type == 'button') $outputClass .= ' ';
			
			//$speed = ZTSliderFunctions::getVal($layer, "speed",300);
			$time = ZTSliderFunctions::getVal($layer, 'time', 0);
			$easing = ZTSliderFunctions::getVal($layer, 'easing', 'easeOutExpo');
			$randomRotate = ZTSliderFunctions::getVal($layer, 'random_rotation', 'false');
			$randomRotate = ZTSliderFunctions::boolToStr($randomRotate);

			$splitin = ZTSliderFunctions::getVal($layer, 'split', 'none');
			$splitout = ZTSliderFunctions::getVal($layer, 'endsplit', 'none');
			$elementdelay = intval(ZTSliderFunctions::getVal($layer, 'splitdelay', 0));
			$endelementdelay = intval(ZTSliderFunctions::getVal($layer, 'endsplitdelay', 0));
			
			$basealign = ZTSliderFunctions::getVal($layer, 'basealign', 'grid');
			
			if($elementdelay > 0) $elementdelay /= 100;
			if($endelementdelay > 0) $endelementdelay /= 100;


			$text = ZTSliderFunctions::getVal($layer, 'text');
			if(function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')){ //use qTranslate
				$text = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($text);
			}elseif(function_exists('ppqtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')){ //use qTranslate plus
				$text = ppqtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($text);
			}elseif(function_exists('qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage')){ //use qTranslate X
				$text = qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage($text);
			}

			$text_toggle = ZTSliderFunctions::getVal($layer, 'texttoggle');
			if(function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')){ //use qTranslate
				$text_toggle = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($text_toggle);
			}elseif(function_exists('ppqtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage')){ //use qTranslate plus
				$text_toggle = ppqtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($text_toggle);
			}elseif(function_exists('qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage')){ //use qTranslate X
				$text_toggle = qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage($text_toggle);
			}
			
			$htmlVideoAutoplay = '';
			$htmlVideoAutoplayOnlyFirstTime = '';
			$htmlVideoNextSlide = '';
			$htmlVideoThumbnail = '';
			$htmlMute = '';
			$htmlCover = '';
			$htmlDotted = '';
			$htmlRatio = '';
			$htmlRewind = '';
			$htmlStartAt = '';
			$htmlEndAt = '';
			$htmlCoverPause = '';
			$htmlDisableOnMobile = '';

			$ids = ZTSliderFunctions::getVal($layer, 'attrID');
			$classes = ZTSliderFunctions::getVal($layer, 'attrClasses');
			$title = ZTSliderFunctions::getVal($layer, 'attrTitle');
			$rel = ZTSliderFunctions::getVal($layer, 'attrRel');
			
			if(trim($ids) == ''){
				if($static_slide){
					$ids = 'slider-'.preg_replace("/[^\w]+/", "", $this->slider->getID()).'-layer-'.$unique_id;
				}else{
					$ids = 'slide-'.preg_replace("/[^\w]+/", "", $slideID).'-layer-'.$unique_id;
				}
			}
				
			$ids = ($ids != '') ? ' id="'.$ids.'"' : '';
			$classes = ($classes != '') ? ' '.$classes : '';
			$title = ($title != '') ? ' title="'.$title.'"' : '';
			$rel = ($rel != '') ? ' rel="'.$rel.'"' : '';

			$inline_styles = '';
			$do_rotation = false;
			$add_data = '';
			$videoType = '';
			$cover = false;
			$toggle_allow = ZTSliderFunctions::getVal($layer, 'toggle',false);
			
			//set html:
			$html = '';
			$html_toggle='';
			switch($type){
				case 'shape':
				break;
				case 'svg':
					$max_width = ZTSliderFunctions::getVal($layer, 'max_width', 'auto');
					$max_height = ZTSliderFunctions::getVal($layer, 'max_height', 'auto');										
					$max_width = (is_object($max_width)) ? ZTSliderFunctions::get_biggest_device_setting($max_width, $enabled_sizes) : $max_width;
					$max_height = (is_object($max_height)) ? ZTSliderFunctions::get_biggest_device_setting($max_height, $enabled_sizes) : $max_height;
					
					if($max_width !== 'auto'){
						$max_width = ($max_width !== 'none') ? ZTSliderFunctions::add_missing_val($max_width) : $max_width;
						$inline_styles .= ' min-width: '.$max_width.'; max-width: '.$max_width.';';
					}
					if($max_height !== 'auto'){
						$max_height = ($max_height !== 'none') ? ZTSliderFunctions::add_missing_val($max_height) : $max_height;
						$inline_styles .= ' max-width: '.$max_height.'; max-width: '.$max_height.';';
					}

					$svg_val = ZTSliderFunctions::getVal($layer, 'svg', false);

					$static_styles = ZTSliderFunctions::getVal($layer, 'static_styles', array());
					
					if(!empty($static_styles)){
						$static_styles = (array)$static_styles;
						
						if(!empty($static_styles['color'])){
							if(is_object($static_styles['color'])){
								$use_color = ZTSliderFunctions::get_biggest_device_setting($static_styles['color'], $enabled_sizes);
							}else{
								$use_color = $static_styles['color'];
							}
							
							$def_val = (array) ZTSliderFunctions::getVal($layer, 'deformation', array());
							
							$color_trans = ZTSliderFunctions::getVal($def_val, 'color-transparency', 1);
							
							if($color_trans != $dcot || $use_color != $dco){							
								if($color_trans > 0) $color_trans *= 100;
								$color_trans = intval($color_trans);
								$use_color = ZTSliderFunctions::hex2rgba($use_color, $color_trans);
								
								$inline_styles .= ' color: '.$use_color.';';
							}
							
						}
					}

					

				break;
				default:
				//case 'no_edit':
				case 'text':
				case 'button':
					$html = $text;
					$html_toggle = $text_toggle;
					
					global $fa_icon_var, $pe_7s_var;
					foreach($icon_sets as $is){
						if(strpos($html, $is) !== false){ //include default Icon Sets if used
							$font_var = str_replace("-", "_", $is)."var";
							$$font_var = true;
							//wp_enqueue_style('rs-icon-set-'.$is);
						}
					}
					
					$max_width = ZTSliderFunctions::getVal($layer, 'max_width', 'auto');
					$max_height = ZTSliderFunctions::getVal($layer, 'max_height', 'auto');
					$white_space = ZTSliderFunctions::getVal($layer, 'whitespace', 'nowrap');
					
					$max_width = (is_object($max_width)) ? ZTSliderFunctions::get_biggest_device_setting($max_width, $enabled_sizes) : $max_width;
					$max_height = (is_object($max_height)) ? ZTSliderFunctions::get_biggest_device_setting($max_height, $enabled_sizes) : $max_height;
					$white_space = (is_object($white_space)) ? ZTSliderFunctions::get_biggest_device_setting($white_space, $enabled_sizes) : $white_space;
					
					if($max_width !== 'auto'){
						$max_width = ($max_width !== 'none') ? ZTSliderFunctions::add_missing_val($max_width) : $max_width;
						$inline_styles .= ' min-width: '.$max_width.'; max-width: '.$max_width.';';
					}
					if($max_height !== 'auto'){
						$max_height = ($max_height !== 'none') ? ZTSliderFunctions::add_missing_val($max_height) : $max_height;
						$inline_styles .= ' max-width: '.$max_height.'; max-width: '.$max_height.';';
					}
					
					$inline_styles .= ' white-space: '.$white_space.';';
					
					//$inline_styles .= ' min-width: '.$max_width.'; min-height: '.$max_height.'; white-space: '.$white_space.';';
					//$inline_styles .= ' max-width: '.$max_width.'; max-height: '.$max_height.';';
					
					//add more inline styling
					$static_styles = ZTSliderFunctions::getVal($layer, 'static_styles', array());
					
					if(!empty($static_styles)){
						$static_styles = (array)$static_styles;
						if(!empty($static_styles['font-size'])){
							$static_styles['font-size'] = ZTSliderFunctions::add_missing_val($static_styles['font-size'], 'px');
							if(is_object($static_styles['font-size'])){
								$mcfs = ZTSliderFunctions::get_biggest_device_setting($static_styles['font-size'], $enabled_sizes);
							}else{
								$mcfs = $static_styles['font-size'];
							}
							if($mcfs !== $dfos) $inline_styles .= ' font-size: '.$mcfs.';';
							
						}
						if(!empty($static_styles['line-height'])){
							$static_styles['line-height'] = ZTSliderFunctions::add_missing_val($static_styles['line-height'], 'px');
							if(is_object($static_styles['line-height'])){
								$mclh = ZTSliderFunctions::get_biggest_device_setting($static_styles['line-height'], $enabled_sizes);
							}else{
								$mclh = $static_styles['line-height'];
							}
							if($mclh !== $dlh) $inline_styles .= ' line-height: '.$mclh.';';
						}
						if(!empty($static_styles['font-weight'])){
							if(is_object($static_styles['font-weight'])){
								$mcfw = ZTSliderFunctions::get_biggest_device_setting($static_styles['font-weight'], $enabled_sizes);
							}else{
								$mcfw = $static_styles['font-weight'];
							}
							if($mcfw !== $dfw) $inline_styles .= ' font-weight: '.$mcfw.';';
						}
						if(!empty($static_styles['color'])){
							if(is_object($static_styles['color'])){
								$use_color = ZTSliderFunctions::get_biggest_device_setting($static_styles['color'], $enabled_sizes);
							}else{
								$use_color = $static_styles['color'];
							}
							
							$def_val = (array) ZTSliderFunctions::getVal($layer, 'deformation', array());
							
							$color_trans = ZTSliderFunctions::getVal($def_val, 'color-transparency', 1);
							
							if($color_trans != $dcot || $use_color != $dco){							
								if($color_trans > 0) $color_trans *= 100;
								$color_trans = intval($color_trans);
								$use_color = ZTSliderFunctions::hex2rgba($use_color, $color_trans);
								
								$inline_styles .= ' color: '.$use_color.';';
							}
							
						}
					}
					
					if($layer_2d_rotation !== 0)
						$do_rotation = true;
				break;
				case 'image':
					$additional = '';
					
					$urlImage = ZTSliderFunctions::getVal($layer, 'image_url');
					
					$do_image_change = ZTSliderFunctions::getVal($layer, 'image-size', 'auto');
					
					$img_size = 'full';
					switch($do_image_change){
						case 'auto':
							$img_size = $image_source_type;
						break;
						default:
							$img_size = $do_image_change;
						break;
					}
					
					$cur_img_id = false;
					
					$img_w = '';
					$img_h = '';
					
					$alt = '';
					$alt_option = ZTSliderFunctions::getVal($layer, 'alt_option', 'media_library');
					
					switch($alt_option){
						case 'file_name':
							$info = pathinfo($urlImage);
							$alt = $info['filename'];
						break;
						case 'custom':
							$alt = ZTSliderFunctions::getVal($layer, 'alt');
						break;
					}
					
					
					if(isset($slide->ignore_alt)) $alt = '';
					
					$scaleX = ZTSliderFunctions::getVal($layer, 'scaleX');
					$scaleY = ZTSliderFunctions::getVal($layer, 'scaleY');
					
					$cover_mode = ZTSliderFunctions::getVal($layer, 'cover_mode', array());
					
					if(is_string($cover_mode)){
						$cover_mode = array('desktop' => $cover_mode, 'notebook' => $cover_mode, 'tablet' => $cover_mode, 'mobile' => $cover_mode);
					}else{ //change to array
						$cover_mode = (array) $cover_mode;
					}
					
					if($adv_resp_sizes == true){
						$y_is_single = true;
						$x_is_single = true;
						
						if($scaleX !== ''){
							if(!is_object($scaleX)){
								if(trim($scaleX) == '' || $scaleX == 'NaNpx') $scaleX = 'auto';
								$myScaleX = $scaleX;
								foreach($cover_mode as $cvmk => $cvmv){
									if($cvmv == 'fullwidth' || $cvmv == 'cover') $myScaleX = 'full';
									if($cvmv == 'cover-proportional') $myScaleX = 'full-proportional';
									break;
								}
							}else{
								foreach($cover_mode as $cvmk => $cvmv){
									if($cvmv == 'fullwidth' || $cvmv == 'cover') $scaleX->$cvmk = 'full';
									if($cvmv == 'cover-proportional') $scaleX->$cvmk = 'full-proportional';
								}
								$myScaleX = ZTSliderFunctions::normalize_device_settings($scaleX, $enabled_sizes, 'html-array', array('NaNpx' => '', 'auto' => ''));
								if($myScaleX == "['','','','']") $myScaleX = '';
								
								if(strpos($myScaleX, '[') !== false) $y_is_single = false;
							}
							if($y_is_single){ //force to array if voffset is also array
								if(!isset($myScaleX)) $myScaleX = ZTSliderFunctions::get_biggest_device_setting($scaleX, $enabled_sizes);
								if(trim($myScaleX) == '' || $myScaleX == 'NaNpx' || $myScaleX == 'auto'){
									$myScaleX = '';
								}else{
									$myScaleX = "['".$myScaleX."','".$myScaleX."','".$myScaleX."','".$myScaleX."']";
								}
							}
						}
						if($scaleY !== ''){
							if(!is_object($scaleY)){
								if(trim($scaleY) == '' || $scaleY == 'NaNpx') $scaleY = 'auto';
								$myScaleY = $scaleY;
								foreach($cover_mode as $cvmk => $cvmv){
									if($cvmv == 'fullheight' || $cvmv == 'cover') $myScaleY = 'full';
									if($cvmv == 'cover-proportional') $myScaleY = 'full-proportional';
									break;
								}
							}else{
								foreach($cover_mode as $cvmk => $cvmv){
									if($cvmv == 'fullheight' || $cvmv == 'cover') $scaleY->$cvmk = 'full';
									if($cvmv == 'cover-proportional') $scaleY->$cvmk = 'full-proportional';
								}
								
								$myScaleY = ZTSliderFunctions::normalize_device_settings($scaleY, $enabled_sizes, 'html-array', array('NaNpx' => '', 'auto' => ''));
								if($myScaleY == "['','','','']") $myScaleY = '';
								
								if(strpos($myScaleY, '[') !== false) $y_is_single = false;
							}
							if($y_is_single){ //force to array if voffset is also array
								if(!isset($myScaleY)) $myScaleY = ZTSliderFunctions::get_biggest_device_setting($scaleY, $enabled_sizes);
								if(trim($myScaleY) == '' || $myScaleY == 'NaNpx' || $myScaleY == 'auto'){
									$myScaleY = '';
								}else{
									$myScaleY = "['".$myScaleY."','".$myScaleY."','".$myScaleY."','".$myScaleY."']";
								}
							}
						}
						
						
					}else{
						$myScaleX = (is_object($scaleX)) ? ZTSliderFunctions::get_biggest_device_setting($scaleX, $enabled_sizes) : $scaleX;
						if(trim($myScaleX) == '' || $myScaleX == 'NaNpx') $myScaleX = 'auto';
						
						$myScaleY = (is_object($scaleY)) ? ZTSliderFunctions::get_biggest_device_setting($scaleY, $enabled_sizes) : $scaleY;
						if(trim($myScaleY) == '' || $myScaleY == 'NaNpx') $myScaleY = 'auto';
						
						foreach($cover_mode as $cvmk => $cvmv){
							if($cvmv == 'fullwidth' || $cvmv == 'cover') $myScaleX = 'full';
							if($cvmv == 'fullheight' || $cvmv == 'cover') $myScaleY = 'full';
							if($cvmv == 'cover-proportional') $myScaleX = 'full-proportional';
							if($cvmv == 'cover-proportional') $myScaleY = 'full-proportional';
							break;
						}
						
					}
					
					if($scaleX != '') $additional .= ' data-ww="'.$myScaleX.'"';
					if($scaleY != '') $additional .= ' data-hh="'.$myScaleY.'"';
					if(ZTSliderFunctions::is_ssl()){
						$urlImage = str_replace("http://", "https://", $urlImage);
					}
					
					$do_ll = ZTSliderFunctions::getVal($layer, "lazy-load", 'auto');
					
					$imageAddParams = "";
					if($lazyLoad != 'none' || $do_ll == 'force' && $do_ll !== 'ignore'){
						$seo_opti = ZTSliderFunctions::getVal($layer, 'seo-optimized', false);
						if($seo_opti === 'false' || $seo_opti === false){
							$imageAddParams .= ' data-lazyload="'.$urlImage.'"';
							$urlImage = ZT_ASSETS_URL."/assets/images/dummy.png";
						}else{
							$additional .= ' class="forceseo"';
						}
					}
					
					$html = '<img src="'.$urlImage.'" alt="'.$alt.'"'.$additional.$imageAddParams.' data-no-retina>';
					$imageLink = ZTSliderFunctions::getVal($layer, "link","");
					
					//add more inline styling
					$static_styles = ZTSliderFunctions::getVal($layer, 'static_styles', array());
					
					if(!empty($static_styles) && trim($class) !== ''){
						$static_styles = (array)$static_styles;
						if(!empty($static_styles['line-height'])){
							$static_styles['line-height'] = ZTSliderFunctions::add_missing_val($static_styles['line-height'], 'px');
							if(is_object($static_styles['line-height'])){
								$mclh = ZTSliderFunctions::get_biggest_device_setting($static_styles['line-height'], $enabled_sizes);
							}else{
								$mclh = $static_styles['line-height'];
							}
							if($mclh !== $dlh) $inline_styles .= ' line-height: '.$mclh.';';
						}
					}
					
					if($layer_2d_rotation !== 0)
						$do_rotation = true;
				break;
				case 'audio':
					$videoType = trim(ZTSliderFunctions::getVal($layer, 'video_type'));
					$videoWidth = ZTSliderFunctions::getVal($layer, 'video_width');
					$videoHeight = ZTSliderFunctions::getVal($layer, 'video_height');
					$videoArgs = trim(ZTSliderFunctions::getVal($videoData, 'args'));
					$v_controls = ZTSliderFunctions::getVal($videoData, 'controls');
					$v_controls = ZTSliderFunctions::strToBool($v_controls);
					$v_visible = ZTSliderFunctions::getVal($videoData, 'video_show_visibility');
					$v_visible = ZTSliderFunctions::strToBool($v_visible);
					$videopreload = ZTSliderFunctions::getVal($videoData, "preload_audio");
					$videopreloadwait = ZTSliderFunctions::getVal($videoData, "preload_wait");
					
					$start_at = ZTSliderFunctions::getVal($videoData, 'start_at');
					$htmlStartAt = ($start_at !== '') ? ' data-videostartat="'.$start_at.'"' : '';
					$end_at = ZTSliderFunctions::getVal($videoData, 'end_at');
					$htmlEndAt = ($end_at !== '') ? ' data-videoendat="'.$end_at.'"' : '';
					
					$rewind = ZTSliderFunctions::getVal($videoData, 'forcerewind');
					$rewind = ZTSliderFunctions::strToBool($rewind);
					$htmlRewind = ($rewind == true) ? ' data-forcerewind="on"' : '';
					
					$volume = ZTSliderFunctions::getVal($videoData, "volume", '100');
					$htmlMute = '			data-volume="'.intval($volume).'"';
					
					if($v_visible === true){
						$videoclass.= ' tp-hiddenaudio';
					}
					
					if($adv_resp_sizes == true){
						if(is_object($videoWidth)){
							$videoWidth = ZTSliderFunctions::normalize_device_settings($videoWidth, $enabled_sizes, 'html-array');
						}
						if(is_object($videoHeight)){
							$videoHeight = ZTSliderFunctions::normalize_device_settings($videoHeight, $enabled_sizes, 'html-array');
						}
					}else{
						if(is_object($videoWidth)) $videoWidth = ZTSliderFunctions::get_biggest_device_setting($videoWidth, $enabled_sizes);
						if(is_object($videoHeight)) $videoHeight = ZTSliderFunctions::get_biggest_device_setting($videoHeight, $enabled_sizes);
					}
					
					$videoloop = ZTSliderFunctions::getVal($videoData, "videoloop");
					
					$add_data .= ($v_controls) ? ' data-videocontrols="none"' : ' data-videocontrols="controls"';
					$add_data .= ' data-videowidth="'.$videoWidth.'" data-videoheight="'.$videoHeight.'"';
					
					if(!empty($videopreload)){
						$add_data .= ' data-videopreload="'.$videopreload.'"';
						$add_data .= ' data-videopreloadwait="'.intval($videopreloadwait).'"';
					}
					
					$add_data .= ' data-audio="html5"';
					$urlAudio = trim(ZTSliderFunctions::getVal($videoData, 'urlAudio'));
					if(!empty($urlAudio)) $add_data .= ' data-videomp4="'.$urlAudio.'"';
					
					
					if(ZTSliderFunctions::strToBool($videoloop) == true){ //fallback
						$add_data .= ' data-videoloop="loop"';
					}else{
						$add_data .= ' data-videoloop="'.$videoloop.'"';
					}
					
					$videoAutoplay = false;
					
					if(array_key_exists("autoplayonlyfirsttime", $videoData)){
						$autoplayonlyfirsttime = ZTSliderFunctions::strToBool(ZTSliderFunctions::getVal($videoData, "autoplayonlyfirsttime"));
						if($autoplayonlyfirsttime == true) $videoAutoplay = '1sttime';
					}
					
					if($videoAutoplay == false){
						if(array_key_exists("autoplay", $videoData))
							$videoAutoplay = ZTSliderFunctions::getVal($videoData, "autoplay");
						else	//backword compatability
							$videoAutoplay = ZTSliderFunctions::getVal($layer, "video_autoplay");
					}
					
					if($videoAutoplay !== false && $videoAutoplay !== 'false'){
						if($videoAutoplay === true || $videoAutoplay === 'true') $videoAutoplay = 'on';
						
						$htmlVideoAutoplay = '			data-autoplay="'.$videoAutoplay.'"'." \n";
					}else{
						$htmlVideoAutoplay = '			data-autoplay="off"'." \n";
					}
					
					$videoNextSlide = ZTSliderFunctions::getVal($videoData, "nextslide");
					$videoNextSlide = ZTSliderFunctions::strToBool($videoNextSlide);

					if($videoNextSlide == true)
						$htmlVideoNextSlide = '			data-nextslideatend="true"'." \n";

					$stopallvideo = ZTSliderFunctions::getVal($videoData, "stopallvideo");
					$stopallvideo = ZTSliderFunctions::strToBool($stopallvideo);
					$htmlDisableOnMobile .= ($stopallvideo)	? '			data-stopallvideos="true"'." \n" : '';
					
				break;
				case 'video':
					$videoType = trim(ZTSliderFunctions::getVal($layer, 'video_type'));
					$videoID = trim(ZTSliderFunctions::getVal($layer, 'video_id'));
					$videoWidth = ZTSliderFunctions::getVal($layer, 'video_width');
					$videoHeight = ZTSliderFunctions::getVal($layer, 'video_height');
					$videoArgs = trim(ZTSliderFunctions::getVal($layer, 'video_args'));
					$v_controls = ZTSliderFunctions::getVal($videoData, 'controls');
					$v_controls = ZTSliderFunctions::strToBool($v_controls);
					
					$start_at = ZTSliderFunctions::getVal($videoData, 'start_at');
					$htmlStartAt = ($start_at !== '') ? ' data-videostartat="'.$start_at.'"' : '';
					$end_at = ZTSliderFunctions::getVal($videoData, 'end_at');
					$htmlEndAt = ($end_at !== '') ? ' data-videoendat="'.$end_at.'"' : '';
					
					$show_cover_pause = ZTSliderFunctions::getVal($videoData, 'show_cover_pause');
					$show_cover_pause = ZTSliderFunctions::strToBool($show_cover_pause);
					$htmlCoverPause = ($show_cover_pause == true) ? ' data-showcoveronpause="on"' : '';
					
					$rewind = ZTSliderFunctions::getVal($videoData, 'forcerewind');
					$rewind = ZTSliderFunctions::strToBool($rewind);
					$htmlRewind = ($rewind == true) ? ' data-forcerewind="on"' : '';
					
					$only_poster_on_mobile = (isset($layer['video_data']->use_poster_on_mobile)) ? $layer['video_data']->use_poster_on_mobile : '';
					$only_poster_on_mobile = ZTSliderFunctions::strToBool($only_poster_on_mobile);
					
					if($isFullWidthVideo == true){ // || $cover == true
						$videoWidth = '100%';
						$videoHeight = '100%';
					}
					
					if($adv_resp_sizes == true){
						if(is_object($videoWidth)){
							$videoWidth = ZTSliderFunctions::normalize_device_settings($videoWidth, $enabled_sizes, 'html-array');
						}
						if(is_object($videoHeight)){
							$videoHeight = ZTSliderFunctions::normalize_device_settings($videoHeight, $enabled_sizes, 'html-array');
						}
					}else{
						if(is_object($videoWidth)) $videoWidth = ZTSliderFunctions::get_biggest_device_setting($videoWidth, $enabled_sizes);
						if(is_object($videoHeight)) $videoHeight = ZTSliderFunctions::get_biggest_device_setting($videoHeight, $enabled_sizes);
					}

					$setBase = (ZTSliderFunctions::is_ssl()) ? 'https://' : 'http://';
					
					$cover = ZTSliderFunctions::getVal($videoData, 'cover');
					$cover = ZTSliderFunctions::strToBool($cover);
					
					$videoloop = ZTSliderFunctions::getVal($videoData, "videoloop");
					
					$mute = ZTSliderFunctions::getVal($videoData, "mute");
					$mute = ZTSliderFunctions::strToBool($mute);
					
					$htmlMute = ($mute)	? '			data-volume="mute"' : '';
					
					switch($videoType){
						case 'streamyoutube':
						case 'streamyoutubeboth':
						case 'youtube':
							if($videoType == 'streamyoutube' || $videoType == 'streamyoutubeboth'){ //change $videoID to the stream!
								$videoID = $slide->getParam('slide_bg_youtube', '');
							}
							
							if(empty($videoArgs))
								$videoArgs = ZtSliderSlider::DEFAULT_YOUTUBE_ARGUMENTS;
							
							if(!$mute){
								$volume = ZTSliderFunctions::getVal($videoData, "volume", '100');
								$htmlMute = '			data-volume="'.intval($volume).'"';
								$videoArgs = 'volume='.intval($volume).'&'.$videoArgs;
							}
							
							if($start_at !== ''){
								$start_raw = explode(':', $start_at);
								if(count($start_raw) == 2){
									if(intval($start_raw[0]) > 0){
										$start_at = $start_raw[0]*60 + $start_raw[1];
									}else{
										$start_at = $start_raw[1];
									}
								}
								$videoArgs .= ($start_at !== '') ? '&start='.$start_at : '';
							}
							if($end_at !== ''){
								$end_raw = explode(':', $end_at);
								if(count($end_raw) == 2){
									if(intval($end_raw[0]) > 0){
										$end_at = $end_raw[0]*60 + $end_raw[1];
									}else{
										$end_at = $end_raw[1];
									}
								}
								$videoArgs .= ($end_at !== '') ? '&end='.$end_at : '';
							}
							
							//check if full URL
							if(strpos($videoID, 'http') !== false){
								//we have full URL, split it to ID
								parse_str( parse_url( $videoID, PHP_URL_QUERY ), $my_v_ret );
								$videoID = $my_v_ret['v'];
							}
							
							$videospeed = ZTSliderFunctions::getVal($videoData, 'videospeed', '1');
							
							$videoArgs.=';origin='.$setBase.$_SERVER['SERVER_NAME'].';';
							
							$add_data = ' data-ytid="'.$videoID.'" data-videoattributes="version=3&amp;enablejsapi=1&amp;html5=1&amp;'.$videoArgs.'" data-videorate="'.$videospeed.'" data-videowidth="'.$videoWidth.'" data-videoheight="'.$videoHeight.'"';
							$add_data .= ($v_controls) ? ' data-videocontrols="none"' : ' data-videocontrols="controls"';
							
						break;
						case 'streamvimeo':
						case 'streamvimeoboth':
						case 'vimeo':
							if($videoType == 'streamvimeo' || $videoType == 'streamvimeoboth'){ //change $videoID
								$videoID = $slide->getParam('slide_bg_vimeo', '');
							}
							
							if(empty($videoArgs))
								$videoArgs = ZtSliderSlider::DEFAULT_VIMEO_ARGUMENTS;

							if ($v_controls)
								$videoArgs = 'background=1&'.$videoArgs;

							//check if full URL
							if(strpos($videoID, 'http') !== false){
								//we have full URL, split it to ID
								$videoID = (int) substr(parse_url($videoID, PHP_URL_PATH), 1);
							}
							
							if(!$mute){
								$volume = ZTSliderFunctions::getVal($videoData, "volume", '100');
								$htmlMute = '			data-volume="'.intval($volume).'"';
							}
							
							$add_data = ' data-vimeoid="'.$videoID.'" data-videoattributes="'.$videoArgs.'" data-videowidth="'.$videoWidth.'" data-videoheight="'.$videoHeight.'"';
							
							
						break;
						case 'streaminstagram':
						case 'streaminstagramboth':
						case 'html5':
							$urlPoster = ZTSliderFunctions::getVal($videoData, "urlPoster");
							$urlMp4 = ZTSliderFunctions::getVal($videoData, "urlMp4");
							$urlWebm = ZTSliderFunctions::getVal($videoData, "urlWebm");
							$urlOgv = ZTSliderFunctions::getVal($videoData, "urlOgv");
							$videopreload = ZTSliderFunctions::getVal($videoData, "preload");
							$leave_on_pause = ZTSliderFunctions::getVal($videoData, "leave_on_pause");
							$leave_on_pause = ZTSliderFunctions::strToBool($leave_on_pause);
							
							if(!$leave_on_pause){
								$add_data .= ' data-exitfullscreenonpause="off"'; //nur wenn off, standard on
							}
							
							if($videoType == 'streaminstagram' || $videoType == 'streaminstagramboth'){ //change $videoID
								$urlMp4 = $slide->getParam('slide_bg_instagram', '');
								$urlWebm = '';
								$urlOgv = '';
							}
							
							$add_data .= ($v_controls) ? ' data-videocontrols="none"' : ' data-videocontrols="controls"';
							$add_data .= ' data-videowidth="'.$videoWidth.'" data-videoheight="'.$videoHeight.'"';
							
							if(ZTSliderFunctions::is_ssl()){
								$urlPoster = str_replace("http://", "https://", $urlPoster);
							}
							
							if(!empty($urlPoster)) $add_data .= ' data-videoposter="'.$urlPoster.'"';
							if(!empty($urlOgv)) $add_data .= ' data-videoogv="'.$urlOgv.'"';
							if(!empty($urlWebm)) $add_data .= ' data-videowebm="'.$urlWebm.'"';
							if(!empty($urlMp4)) $add_data .= ' data-videomp4="'.$urlMp4.'"';
							
							if(!empty($urlPoster)){
								if($only_poster_on_mobile === true){
									//$add_data .= ' data-posterOnMobile="on"';
									$add_data .= ' data-noposteronmobile="on"';
								}else{
									//$add_data .= ' data-posterOnMobile="off"';
									$add_data .= ' data-noposteronmobile="off"';
								}
							}

							if(!empty($videopreload)) $add_data .= ' data-videopreload="'.$videopreload.'"';
							
						break;
						default:
							ZTSliderFunctions::throwError("wrong video type: $videoType");
						break;
					}
					
					if(ZTSliderFunctions::strToBool($videoloop) == true){ //fallback
						$add_data .= ' data-videoloop="loop"';
					}else{
						$add_data .= ' data-videoloop="'.$videoloop.'"';
					}
					
					if($cover == true){
						$dotted = ZTSliderFunctions::getVal($videoData, "dotted");
						if($dotted !== 'none')
							$add_data .= ' data-dottedoverlay="'.$dotted.'"';
							
						$add_data .=  ' data-forceCover="1"';
							
						$ratio = ZTSliderFunctions::getVal($videoData, "ratio");
							if(!empty($ratio))
								$add_data .= ' data-aspectratio="'.$ratio.'"';
					}
					
					$videoAutoplay = false;
					
					if(array_key_exists("autoplayonlyfirsttime", $videoData)){
						$autoplayonlyfirsttime = ZTSliderFunctions::strToBool(ZTSliderFunctions::getVal($videoData, "autoplayonlyfirsttime"));
						if($autoplayonlyfirsttime == true) $videoAutoplay = '1sttime';
					}
					
					if($videoAutoplay == false){
						if(array_key_exists("autoplay", $videoData))
							$videoAutoplay = ZTSliderFunctions::getVal($videoData, "autoplay");
						else	//backword compatability
							$videoAutoplay = ZTSliderFunctions::getVal($layer, "video_autoplay");
					}
					
					if($videoAutoplay !== false && $videoAutoplay !== 'false'){
						if($videoAutoplay === true || $videoAutoplay === 'true') $videoAutoplay = 'on';
						
						$htmlVideoAutoplay = '			data-autoplay="'.$videoAutoplay.'"'." \n";
					}else{
						$htmlVideoAutoplay = '			data-autoplay="off"'." \n";
					}
					
					$large_controls = ZTSliderFunctions::strToBool(ZTSliderFunctions::getVal($videoData, "large_controls"));
					if(!$large_controls){
						$add_data .= ' data-viodelargecontrols="off"';
					}
					
					$videoNextSlide = ZTSliderFunctions::getVal($videoData, "nextslide");
					$videoNextSlide = ZTSliderFunctions::strToBool($videoNextSlide);

					if($videoNextSlide == true)
						$htmlVideoNextSlide = '			data-nextslideatend="true"'." \n";

					$videoThumbnail = (isset($videoData["previewimage"])) ? $videoData["previewimage"] : '';
					if(ZTSliderFunctions::is_ssl()){
						$videoThumbnail = str_replace("http://", "https://", $videoThumbnail);
					}else{
						$videoThumbnail = str_replace("https://", "http://", $videoThumbnail);
					}

					if(trim($videoThumbnail) !== '') $htmlVideoThumbnail = '			data-videoposter="'.$videoThumbnail.'"'." \n";
					if(!empty($videoThumbnail)){
						if($only_poster_on_mobile === true){
							//$htmlVideoThumbnail .= '			data-posterOnMobile="on"'." \n";
							$htmlVideoThumbnail .= '			data-noposteronmobile="on"'." \n";
						}else{
							//$htmlVideoThumbnail .= '			data-posterOnMobile="off"'." \n";
							$htmlVideoThumbnail .= '			data-noposteronmobile="off"'." \n";
						}
					}
					
					$disable_on_mobile = ZTSliderFunctions::getVal($videoData, "disable_on_mobile");
					$disable_on_mobile = ZTSliderFunctions::strToBool($disable_on_mobile);
					$htmlDisableOnMobile = ($disable_on_mobile)	? '			data-disablevideoonmobile="1"'." \n" : '';
					$stopallvideo = ZTSliderFunctions::getVal($videoData, "stopallvideo");
					$stopallvideo = ZTSliderFunctions::strToBool($stopallvideo);
					$allowfullscreenvideo = ZTSliderFunctions::getVal($videoData, "allowfullscreen");
					$allowfullscreenvideo = ZTSliderFunctions::strToBool($allowfullscreenvideo);
					$htmlDisableOnMobile .= ($stopallvideo)	? '			data-stopallvideos="true"'." \n" : '';
					$htmlDisableOnMobile .= ($allowfullscreenvideo)	? '			data-allowfullscreenvideo="true"'." \n" : '';
					
				break;
			}
			
			$has_trigger = false;
			foreach($layers as $cl){
				if($has_trigger) break;
				$all_actions = ZTSliderFunctions::getVal($cl, 'layer_action', array());
				if(!empty($all_actions)){
					$a_action = ZTSliderFunctions::cleanStdClassToArray(ZTSliderFunctions::getVal($all_actions, 'action', array()));
					$a_layer_target = ZTSliderFunctions::cleanStdClassToArray(ZTSliderFunctions::getVal($all_actions, 'layer_target', array()));
					foreach($a_action as $ak => $aa){
						switch($aa){
							case 'start_in':
							case 'start_out':
							case 'toggle_layer':
							/*case 'stop_video':
							case 'start_video':
							case 'toggle_video':*/
								if($unique_id == $a_layer_target[$ak]){
									$has_trigger = true;
									break;
								}
							break;
						}
					}
				}
			}
			
			$last_trigger_state = '';
			$animation_overwrite = 'default';
			$trigger_memory = 'keep';
			if($has_trigger){
				$animation_overwrite = ZTSliderFunctions::getVal($layer, 'animation_overwrite', 'wait');
				$trigger_memory = ZTSliderFunctions::getVal($layer, 'trigger_memory', 'keep');
				
				$last_trigger_state = '			data-lasttriggerstate="'.trim($trigger_memory).'"';
				
			}
			if($animation_overwrite == 'waitin' || $animation_overwrite == 'wait'){
				$time = 'bytrigger';
			}
			if($animation_overwrite == 'waitout' || $animation_overwrite == 'wait'){
				$htmlEnd = ' data-end="bytrigger"'."\n";
			}else{
				//handle end transitions:
				$endTime = trim(ZTSliderFunctions::getVal($layer, 'endtime', 0));
				$endWithSlide = ZTSliderFunctions::getVal($layer, 'endWithSlide', false);
				
				//backwards compatibility
				$realEndTime = ZTSliderFunctions::getVal($layer, "realEndTime", false);

				if($realEndTime !== false){
					$endSpeed = trim(ZTSliderFunctions::getVal($layer, "endspeed"));
					$speed = ZTSliderFunctions::getVal($layer, "speed",300);
					$calcSpeed = (!empty($endSpeed)) ? $endSpeed : $speed;
					
					if(!empty($calcSpeed) && $realEndTime - $calcSpeed !== $endTime){
						$endTime = $realEndTime - $calcSpeed;
					}
				}
				//backwards compatibility end
				
				$htmlEnd = '';
				
				
				//endtime - endspeed
				$es = ZTSliderFunctions::getVal($layer, 'endspeed', 0);
				
				if(!empty($endTime) && $endTime - $es < 0){
					$endTime = 0;
				}else{
					$endTime = $endTime - $es;
				}
				
			}
			
			$show_on_hover = ZTSliderFunctions::getVal($layer, 'show-on-hover', false);
			if($show_on_hover === 'show-on-hover' || is_array($show_on_hover) && $show_on_hover[0] == 'show-on-hover') $show_on_hover = false;//fix for layers that occured in version 5.2.2 and 5.2.3

			if($show_on_hover == true){
				$time = 'sliderenter';
				$htmlEnd = ' data-end="sliderleave"'." \n";
			}else{
				if(!empty($endTime) && $endWithSlide !== true){
					$htmlEnd = ' data-end="'.$endTime.'"'." \n";
				}
			}
			
			$customout = '';
			$maskout = '';
			
				
			//add animation to class
			$endAnimation = trim(ZTSliderFunctions::getVal($layer, "endanimation"));
			if($endAnimation == "fade") $endAnimation = "tp-fade";

			if(!array_key_exists($endAnimation, $endAnimations) && array_key_exists($endAnimation, $customEndAnimations)){ //if true, add custom animation
				//check with custom values, if same, we do not need to write all down and just refer
			}
			
			$tcout = '';
			
			if($endAnimation !== 'auto'){
				$tcout = ZTSliderOperations::parseCustomAnimationByArray($layer, 'end');
			}else{ //automatic reverse
				$tcout .= 'auto:auto;';
			}
			
			if($endAnimation == 'auto'){
				$es = ZTSliderFunctions::getVal($layer, 'endspeed');
				$ee = trim(ZTSliderFunctions::getVal($layer, 'endeasing'));
				if(!empty($es)){
					$tcout .= 's:'.$es.';';
				}
				if(!empty($ee) && $ee !== 'nothing'){
					$tcout .= 'e:'.$ee.';';
				}
			}
			
			if($tcout !== ''){
				$customout = ' data-transform_out="' . $tcout . '"';
			}
			
			$do_mask_out = ZTSliderFunctions::getVal($layer, 'mask_end',false);
			if($do_mask_out){
				$tmask = ZTSliderOperations::parseCustomMaskByArray($layer, 'end');
				if($tmask !== ''){
					$maskout = ' data-mask_out="' . $tmask . '"';
				}
			}

			//slide link
			$html_simple_link = '';
			
			//hidden under resolution
			$htmlHidden = '';
			$layerHidden = ZTSliderFunctions::getVal($layer, 'hiddenunder');
			if($layerHidden == 'true' || $layerHidden == '1')
				$htmlHidden = '			data-captionhidden="on"'." \n";

			$htmlParams = $add_data.$htmlEnd.$last_trigger_state.$htmlVideoAutoplay.$htmlVideoNextSlide.$htmlVideoThumbnail.$htmlHidden.$htmlMute.$htmlDisableOnMobile.$htmlCover.$htmlDotted.$htmlRatio.$htmlRewind.$htmlStartAt.$htmlEndAt.$htmlCoverPause."\n"; //.$htmlVideoAutoplayOnlyFirstTime

			//set positioning options
			$alignHor = ZTSliderFunctions::getVal($layer, 'align_hor', 'left');
			$alignVert = ZTSliderFunctions::getVal($layer, 'align_vert', 'top');
			$left = ZTSliderFunctions::getVal($layer, 'left', 0);
			$top = ZTSliderFunctions::getVal($layer, 'top', 0);
			
			$htmlPosX = '';
			$htmlPosY = '';
			$static_data = '';
			$extra_data = '';
			
			if($adv_resp_sizes == true){
				$do_x_full = false;
				$do_y_full = false;
				$x_is_single = true;
				$y_is_single = true;
				if(!is_object($left) ){
					$myLeft = $left;
					$myLeft = "['".$myLeft."','".$myLeft."','".$myLeft."','".$myLeft."']";
				}else{
					$myLeft = ZTSliderFunctions::normalize_device_settings($left, $enabled_sizes, 'html-array');
					if(strpos($myLeft, '[') !== false){
						$do_x_full = true;
					}else{
						$myLeft = "['".$myLeft."','".$myLeft."','".$myLeft."','".$myLeft."']";
					}
				}
				if(!is_object($top)){
					$myTop = $top;
					$myTop = "['".$myTop."','".$myTop."','".$myTop."','".$myTop."']";
				}else{
					$myTop = ZTSliderFunctions::normalize_device_settings($top, $enabled_sizes, 'html-array');
					if(strpos($myTop, '[') !== false){
						$do_y_full = true;
					}else{
						$myTop = "['".$myTop."','".$myTop."','".$myTop."','".$myTop."']";
					}
				}
				
				if(!is_object($alignHor)){
					$myHor = $alignHor;
					$myHor = "['".$myHor."','".$myHor."','".$myHor."','".$myHor."']";
				}else{
					$myHor = ZTSliderFunctions::normalize_device_settings($alignHor, $enabled_sizes, 'html-array');
					if(strpos($myHor, '[') !== false){
						$x_is_single = false;
					}else{
						$myHor = "['".$myHor."','".$myHor."','".$myHor."','".$myHor."']";
					}
				}

				
				if(!is_object($alignVert)){
					$myVer = $alignVert;
					$myVer = "['".$myVer."','".$myVer."','".$myVer."','".$myVer."']";
				}else{
					$myVer = ZTSliderFunctions::normalize_device_settings($alignVert, $enabled_sizes, 'html-array');
					if(strpos($myVer, '[') !== false){
						$y_is_single = false;
					}else{
						$myVer = "['".$myVer."','".$myVer."','".$myVer."','".$myVer."']";
					}
				}

				
				$htmlPosX = ' data-x="'.$myHor.'" data-hoffset="'.$myLeft.'"';
				$htmlPosY = ' data-y="'.$myVer.'" data-voffset="'.$myTop.'"';
				
				$static_styles = ZTSliderFunctions::getVal($layer, 'static_styles', array());
				if(!empty($static_styles)){
					$static_styles = (array)$static_styles;
					if(!empty($static_styles['font-size'])){
						if(is_object($static_styles['font-size'])){
							$ss_fs = ZTSliderFunctions::normalize_device_settings($static_styles['font-size'], $enabled_sizes, 'html-array');
							if(strpos($ss_fs, '[') !== false) $static_data .= str_replace('px', '', '			data-fontsize="'.$ss_fs.'"')."\n";
						}
					}
					if(!empty($static_styles['line-height'])){
						if(is_object($static_styles['line-height'])){
							$ss_lh = ZTSliderFunctions::normalize_device_settings($static_styles['line-height'], $enabled_sizes, 'html-array');
							if(strpos($ss_lh, '[') !== false) $static_data .= str_replace('px', '', '			data-lineheight="'.$ss_lh.'"')."\n";
						}
					}
					if(!empty($static_styles['font-weight'])){
						if(is_object($static_styles['font-weight'])){
							$ss_fw = ZTSliderFunctions::normalize_device_settings($static_styles['font-weight'], $enabled_sizes, 'html-array');
							if(strpos($ss_fw, '[') !== false) $static_data .= str_replace('px', '', '			data-fontweight="'.$ss_fw.'"')."\n";
						}
					}
					if(!empty($static_styles['color'])){
						if(is_object($static_styles['color'])){
							$def_val = (array) ZTSliderFunctions::getVal($layer, 'deformation', array());
							
							foreach($static_styles['color'] as $sk => $sv){
								if(strpos($sv, 'rgb') !== false)
									$static_styles['color']->$sk = ZTSliderFunctions::rgba2hex($sv);
								
								$color_trans = ZTSliderFunctions::getVal($def_val, 'color-transparency', 1);
								if($color_trans > 0) $color_trans *= 100;
								$color_trans = intval($color_trans);
								
								$static_styles['color']->$sk = ZTSliderFunctions::hex2rgba($static_styles['color']->$sk, $color_trans);
							}
							
							
							
							
							$ss_c = ZTSliderFunctions::normalize_device_settings($static_styles['color'], $enabled_sizes, 'html-array');
							if(strpos($ss_c, '[') !== false){
								$static_data .= '			data-color="'.$ss_c.'"'."\n";
							}
						}
					}
				}
				
				$max_width = ZTSliderFunctions::getVal($layer, 'max_width', 'auto');
				$max_height = ZTSliderFunctions::getVal($layer, 'max_height', 'auto');
				$white_space = ZTSliderFunctions::getVal($layer, 'whitespace', 'nowrap');
				
				$cover_mode = ZTSliderFunctions::getVal($layer, 'cover_mode', array());
				
				if(is_string($cover_mode)){
					$cover_mode = array('desktop' => $cover_mode, 'notebook' => $cover_mode, 'tablet' => $cover_mode, 'mobile' => $cover_mode);
				}else{ //change to array
					$cover_mode = (array) $cover_mode;
				}
				
				if(is_object($max_width)){
					$max_width = ZTSliderFunctions::normalize_device_settings($max_width, $enabled_sizes);
					
					if($max_width->desktop == 'auto') $max_width->desktop = 'none';
					if($max_width->notebook == 'auto') $max_width->notebook = 'none';
					if($max_width->tablet == 'auto') $max_width->tablet = 'none';
					if($max_width->mobile == 'auto') $max_width->mobile = 'none';
					if($max_width->desktop == $max_width->notebook && $max_width->desktop == $max_width->tablet && $max_width->desktop == $max_width->mobile) $max_width = $max_width->desktop;
				}
				if(is_object($max_height)){
					$max_height = ZTSliderFunctions::normalize_device_settings($max_height, $enabled_sizes);
					
					if($max_height->desktop == 'auto') $max_height->desktop = 'none';
					if($max_height->notebook == 'auto') $max_height->notebook = 'none';
					if($max_height->tablet == 'auto') $max_height->tablet = 'none';
					if($max_height->mobile == 'auto') $max_height->mobile = 'none';
					if($max_height->desktop == $max_height->notebook && $max_height->desktop == $max_height->tablet && $max_height->desktop == $max_height->mobile) $max_height = $max_height->desktop;
				}
				if(is_object($white_space)){
					$white_space = ZTSliderFunctions::normalize_device_settings($white_space, $enabled_sizes);
					
					if($white_space->desktop == $white_space->notebook && $white_space->desktop == $white_space->tablet && $white_space->desktop == $white_space->mobile) $white_space = $white_space->desktop;
				}
				
				switch($type){
					case 'shape':
					case 'image':
						if(is_object($max_width)){
							foreach($cover_mode as $cvmk => $cvmv){
								if($cvmv == 'fullwidth' || $cvmv == 'cover'){
									$max_width->$cvmk = 'full';
								}elseif($cvmv == 'cover-proportional'){
									$max_width->$cvmk = 'full-proportional';
								}else{
									if($type == 'image') $max_width->$cvmk = 'none';
								}
							}
						}else{
							foreach($cover_mode as $cvmk => $cvmv){
								if($type == 'image'){
									if($cvmv == 'fullwidth' || $cvmv == 'cover'){
										$max_width = 'full';
									}elseif($cvmv == 'cover-proportional'){
										$max_width = 'full-proportional';
									}else{
										$max_width = 'none';
									}
								}else{
									if($cvmv == 'fullwidth' || $cvmv == 'cover'){
										$max_width = 'full';
									}elseif($cvmv == 'cover-proportional'){
										$max_width = 'full-proportional';
									}
								}
								break;
							}
						}
						if(is_object($max_height)){
							foreach($cover_mode as $cvmk => $cvmv){
								if($cvmv == 'fullheight' || $cvmv == 'cover'){
									$max_height->$cvmk = 'full';
								}elseif($cvmv == 'cover-proportional'){
									$max_height->$cvmk = 'full-proportional';
								}else{
									if($type == 'image') $max_height->$cvmk = 'none';
								}
							}
						}else{
							foreach($cover_mode as $cvmk => $cvmv){
								if($type == 'image'){
									if($cvmv == 'fullheight' || $cvmv == 'cover'){
										$max_height = 'full';
									}elseif($cvmv == 'cover-proportional'){
										$max_height = 'full-proportional';
									}else{
										$max_height = 'none';
									}
								}else{
									if($cvmv == 'fullheight' || $cvmv == 'cover'){
										$max_height = 'full';
									}elseif($cvmv == 'cover-proportional'){
										$max_height = 'full-proportional';
									}
								}
								break;
							}
						}
					break;
					case 'svg':
					break;
				}
				
				if($type !== 'video'){
					//$static_data .= (is_object($max_width)) ? str_replace('px', '', "			data-minwidth=\"['".implode("','", (array)$max_width)."']\"")."\n" : str_replace('px', '', '			data-minwidth="'.$max_width.'"')."\n";
					$static_data .= (is_object($max_width)) ? str_replace('px', '', "			data-width=\"['".implode("','", (array)$max_width)."']\"")."\n" : str_replace('px', '', '			data-width="'.$max_width.'"')."\n";
					//$static_data .= (is_object($max_height)) ? str_replace('px', '', "			data-minheight=\"['".implode("','", (array)$max_height)."']\"")."\n" : str_replace('px', '', '			data-minheight="'.$max_height.'"')."\n";
					$static_data .= (is_object($max_height)) ? str_replace('px', '', "			data-height=\"['".implode("','", (array)$max_height)."']\"")."\n" : str_replace('px', '', '			data-height="'.$max_height.'"')."\n";
				}
				$static_data .= (is_object($white_space)) ? str_replace('px', '', "			data-whitespace=\"['".implode("','", (array)$white_space)."']\"")."\n" : str_replace('px', '', '			data-whitespace="'.$white_space.'"')."\n";
				
			}else{
				if(is_object($alignHor)) $alignHor = ZTSliderFunctions::get_biggest_device_setting($alignHor, $enabled_sizes);
				if(is_object($alignVert)) $alignVert = ZTSliderFunctions::get_biggest_device_setting($alignVert, $enabled_sizes);
				if(is_object($left)) $left = ZTSliderFunctions::get_biggest_device_setting($left, $enabled_sizes);
				if(is_object($top)) $top = ZTSliderFunctions::get_biggest_device_setting($top, $enabled_sizes);
				
				switch($alignHor){
					default:
					case 'left':
						$htmlPosX = ' data-x="'.$left.'"';
					break;
					case 'center':
						$htmlPosX = ' data-x="center" data-hoffset="'.$left.'"';
					break;
					case 'right':
						//$left = (int)$left*-1;
						$htmlPosX = ' data-x="right" data-hoffset="'.$left.'"';
					break;
				}
				
				switch($alignVert){
					default:
					case 'top':
						$htmlPosY = ' data-y="'.$top.'"';
					break;
					case 'middle':
						$htmlPosY = ' data-y="center" data-voffset="'.$top.'"';
					break;
					case 'bottom':
						//$top = (int)$top*-1;
						$htmlPosY = ' data-y="bottom" data-voffset="'.$top.'"';
					break;
				}
				
				
				$max_width = ZTSliderFunctions::getVal($layer, 'max_width', 'auto');
				$max_height = ZTSliderFunctions::getVal($layer, 'max_height', 'auto');
				$cover_mode = ZTSliderFunctions::getVal($layer, 'cover_mode', array());
				
				if(is_string($cover_mode)){
					$cover_mode = array('desktop' => $cover_mode, 'notebook' => $cover_mode, 'tablet' => $cover_mode, 'mobile' => $cover_mode);
				}else{ //change to array
					$cover_mode = (array) $cover_mode;
				}
				
				switch($type){
					case 'shape':
					case 'image':
						if(is_object($max_width)){
							foreach($cover_mode as $cvmk => $cvmv){
								if($cvmv == 'fullwidth' || $cvmv == 'cover'){
									$max_width->$cvmk = 'full';
								}elseif($cvmv == 'cover-proportional'){
									$max_width->$cvmk = 'full-proportional';
								}else{
									if($type == 'image') $max_width->$cvmk = 'none';
								}
							}
						}else{
							foreach($cover_mode as $cvmk => $cvmv){
								if($type == 'image'){
									if($cvmv == 'fullwidth' || $cvmv == 'cover'){
										$max_width = 'full';
									}elseif($cvmv == 'cover-proportional'){
										$max_width = 'full-proportional';
									}else{
										$max_width = 'none';
									}
								}else{
									if($cvmv == 'fullwidth' || $cvmv == 'cover'){
										$max_width = 'full';
									}elseif($cvmv == 'cover-proportional'){
										$max_width = 'full-proportional';
									}
								}
								break;
							}
						}
						if(is_object($max_height)){
							foreach($cover_mode as $cvmk => $cvmv){
								if($cvmv == 'fullheight' || $cvmv == 'cover'){
									$max_height->$cvmk = 'full';
								}elseif($cvmv == 'cover-proportional'){
									$max_height->$cvmk = 'full-proportional';
								}else{
									if($type == 'image') $max_height->$cvmk = 'none';
								}
							}
						}else{
							foreach($cover_mode as $cvmk => $cvmv){
								if($type == 'image'){
									if($cvmv == 'fullheight' || $cvmv == 'cover'){
										$max_height = 'full';
									}elseif($cvmv == 'cover-proportional'){
										$max_height = 'full-proportional';
									}else{
										$max_height = 'none';
									}
								}else{
									if($cvmv == 'fullheight' || $cvmv == 'cover'){
										$max_height = 'full';
									}elseif($cvmv == 'cover-proportional'){
										$max_height = 'full-proportional';
									}
								}
								break;
							}
						}
					break;
					case 'svg':						
						
					break;
				}
				
				if($type !== 'video'){
					$static_data .= (is_object($max_width)) ? str_replace('px', '', "			data-width=\"['".implode("','", (array)$max_width)."']\"")."\n" : str_replace('px', '', '			data-width="'.$max_width.'"')."\n";
					$static_data .= (is_object($max_height)) ? str_replace('px', '', "			data-height=\"['".implode("','", (array)$max_height)."']\"")."\n" : str_replace('px', '', '			data-height="'.$max_height.'"')."\n";
				}
			}
			
			$style_string = '';
			
			$vis_desktop = ZTSliderFunctions::getVal($layer, 'visible-desktop', true);
			$vis_notebook = ZTSliderFunctions::getVal($layer, 'visible-notebook', true);
			$vis_tablet = ZTSliderFunctions::getVal($layer, 'visible-tablet', true);
			$vis_mobile = ZTSliderFunctions::getVal($layer, 'visible-mobile', true);
			$vis_notebook = ($vis_notebook === true) ? 'on' : 'off';
			$vis_desktop = ($vis_desktop === true) ? 'on' : 'off';
			$vis_tablet = ($vis_tablet === true) ? 'on' : 'off';
			$vis_mobile = ($vis_mobile === true) ? 'on' : 'off';
			
			if($vis_notebook == 'off' || $vis_desktop == 'off' || $vis_tablet == 'off' || $vis_mobile == 'off')
				$static_data .= "			data-visibility=\"['".$vis_desktop."','".$vis_notebook."','".$vis_tablet."','".$vis_mobile."']\"\n";
			
			$il_style = (array)ZTSliderFunctions::getVal($layer, 'inline', array());
			$adv_style = (array)ZTSliderFunctions::getVal($layer, 'advanced', array());
			
			//add deformation and hover deformation to the layers
			$def_val = (array) ZTSliderFunctions::getVal($layer, 'deformation', array());
			$def = array();
			$st_idle = array();
			$def['o'] = array(ZTSliderFunctions::getVal($def_val, 'opacity', '0'), '0');
			$def['sX'] = array(ZTSliderFunctions::getVal($def_val, 'scalex', '1'), '1');
			$def['sY'] = array(ZTSliderFunctions::getVal($def_val, 'scaley', '1'), '1');
			$def['skX'] = array(ZTSliderFunctions::getVal($def_val, 'skewx', '0'), '0');
			$def['skY'] = array(ZTSliderFunctions::getVal($def_val, 'skewy', '0'), '0');
			$def['rX'] = array(ZTSliderFunctions::getVal($def_val, 'xrotate', '0'), '0');
			$def['rY'] = array(ZTSliderFunctions::getVal($def_val, 'yrotate', '0'), '0');
			$def['rZ'] = array(ZTSliderFunctions::getVal($layer, '2d_rotation', '0'), '0');
			$orix = ZTSliderFunctions::getVal($def_val, '2d_origin_x', '50%');
			if(strpos($orix, '%') === false) $orix .= '%';
			$oriy = ZTSliderFunctions::getVal($def_val, '2d_origin_y', '50%');
			if(strpos($oriy, '%') === false) $oriy .= '%';
			$def['tO'] = array($orix . ' ' . $oriy, '50% 50%');
			$def['tP'] = array(ZTSliderFunctions::getVal($def_val, 'pers', '600'), '600');
			$def['z'] = array(ZTSliderFunctions::getVal($def_val, 'z', '0'), '0');
			
			$st_idle['font-family'] = array(str_replace('"', "'", ZTSliderFunctions::getVal($def_val, 'font-family', '')), str_replace('"', "'", $dff));
			$st_idle['text-align'] 		= array(ZTSliderFunctions::getVal($def_val, 'text-align', 'left'), $dta);
			$st_idle['text-transform'] 	= array(ZTSliderFunctions::getVal($def_val, 'text-transform', 'left'), $dttr);
			
			//styling
			$mfs = ZTSliderFunctions::getVal($def_val, 'font-style', 'off');
			$font_style = ($mfs == 'on' || $mfs == 'italic') ? 'italic' : 'normal';
			
			$st_idle['font-style'] = array($font_style, $dfs);
			$st_idle['text-decoration'] = array(ZTSliderFunctions::getVal($def_val, 'text-decoration', 'none'), $dtd);
			
			$bg_color = ZTSliderFunctions::getVal($def_val, 'background-color', $dbc);
			if($bg_color !== 'transparent'){
				$bg_trans = ZTSliderFunctions::getVal($def_val, 'background-transparency', $dbt);
				if($bg_trans > 0) $bg_trans *= 100;
				if($dbt > 0) $dbt *= 100;
				$bg_trans = intval($bg_trans);
				$dbt = intval($dbt);
				$st_idle['background-color'] = array(ZTSliderFunctions::hex2rgba($bg_color, $bg_trans), ZTSliderFunctions::hex2rgba($dbc, $dbt)); //'ALWAYS'
			}
			
			$my_padding = ZTSliderFunctions::getVal($def_val, 'padding', array('0px','0px','0px','0px'));
			if(!empty($my_padding)){
				if(is_array($my_padding))
					$my_padding = implode(' ', $my_padding);
				
				if(trim($my_padding) != '')
					$st_idle['padding'] = array($my_padding, $dpa);
			}
			
			$border_color = ZTSliderFunctions::getVal($def_val, 'border-color', $dboc);
			if($border_color !== 'transparent'){
				$border_trans = ZTSliderFunctions::getVal($def_val, 'border-transparency', $dbot);
				if($border_trans > 0) $border_trans *= 100;
				if($dbot > 0) $dbot *= 100;
				$border_trans = intval($border_trans);
				$dbot = intval($dbot);
				$st_idle['border-color'] = array(ZTSliderFunctions::hex2rgba($border_color, $border_trans), ZTSliderFunctions::hex2rgba($dboc, $dbot)); //'ALWAYS'
			}
			
			$st_idle['border-style'] = array(ZTSliderFunctions::getVal($def_val, 'border-style', 'none'), $dbs);
			
			$bdw = ZTSliderFunctions::getVal($def_val, 'border-width', '0px');
			$bdw = ZTSliderFunctions::add_missing_val($bdw, 'px');
			if($bdw == '0px') unset($st_idle['border-style']);
			$st_idle['border-width'] = array($bdw, $dbw);
			
			$my_border = ZTSliderFunctions::getVal($def_val, 'border-radius', array('0px','0px','0px','0px'));
			if(!empty($my_border)){
				$my_border = implode(' ', $my_border);
				if(trim($my_border) != '')
					$st_idle['border-radius'] = array($my_border, $dbr);
			}
			
			
			//Advanced Styles here:
			if(isset($adv_style['idle'])){
				$adv_style['idle'] = (array)$adv_style['idle'];
				if(!empty($adv_style['idle'])){
					foreach($adv_style['idle'] as $n => $v){
						$st_idle[$n] = array($v, 'ALWAYS');
					}
				}
			}
			
			//Advanced Styles here:
			if(isset($il_style['idle'])){
				$il_style['idle'] = (array)$il_style['idle'];
				if(!empty($il_style['idle'])){
					foreach($il_style['idle'] as $n => $v){
						$st_idle[$n] = array($v, 'ALWAYS');
					}
				}
			}
			
			//get hover stuff, because of css_cursor
			$def_val = (array) ZTSliderFunctions::getVal($layer, 'deformation-hover', array());
			
			//add the css_cursor to the idle styles
			$css_cursor = ZTSliderFunctions::getVal($def_val, 'css_cursor', 'auto');
			
			if(trim($css_cursor) !== '' && $css_cursor !== 'auto'){
				if($css_cursor == 'zoom-in') $css_cursor = '-webkit-zoom-in; cursor: -moz-zoom-in';
				if($css_cursor == 'zoom-out') $css_cursor = '-webkit-zoom-out; cursor: -moz-zoom-out';
				$st_idle['cursor']  =  array($css_cursor, 'auto');
			}
			
			$def_string = '';
			foreach($def as $key => $value){
				if(trim($value[0]) == '' || $value[0] == $value[1]) continue;
				if(str_replace('px', '', $value[0]) == str_replace('px', '', $value[1])) continue;
				$def_string .= $key.':'.$value[0].';';
			}
			
			foreach($st_idle as $key => $value){
				if($type == 'image' || $type == 'video'){ //do not print unneeded styles
					if(in_array($key, $ignore_styles)) continue;
				}
				if(trim($value[0]) == '' || $value[0] == $value[1]) continue;
				if(str_replace('px', '', $value[0]) == str_replace('px', '', $value[1])) continue;
				$style_string .= $key.':'.$value[0].';';
			}
			
			$deform = '			data-transform_idle="'.str_replace('"', "'", $def_string).'"'."\n";
			$idle_style = $style_string;
			
			//check if hover is active for the layer
			$is_hover_active = ZTSliderFunctions::getVal($layer, 'hover', '0');
			
			$deform_hover = '';
			$style_hover = '';
			$def = array();
			
			$st_h_string = '';
			
			if($is_hover_active){
				
				$def['o'] = array(ZTSliderFunctions::getVal($def_val, 'opacity', '0'), '0');
				$def['sX'] = array(ZTSliderFunctions::getVal($def_val, 'scalex', '1'), '1');
				$def['sY'] = array(ZTSliderFunctions::getVal($def_val, 'scaley', '1'), '1');
				$def['skX'] = array(ZTSliderFunctions::getVal($def_val, 'skewx', '0'), '0');
				$def['skY'] = array(ZTSliderFunctions::getVal($def_val, 'skewy', '0'), '0');
				$def['rX'] = array(ZTSliderFunctions::getVal($def_val, 'xrotate', '0'), '0');
				$def['rY'] = array(ZTSliderFunctions::getVal($def_val, 'yrotate', '0'), '0');
				$def['rZ'] = array(ZTSliderFunctions::getVal($def_val, '2d_rotation', '0'), 'inherit');
				$def['z'] = array(ZTSliderFunctions::getVal($def_val, 'z', '0'), '0');
				
				$def['s'] = array(ZTSliderFunctions::getVal($def_val, 'speed', '300'), 'ALWAYS');
				$def['e'] = array(ZTSliderFunctions::getVal($def_val, 'easing', 'easeOutExpo'), 'ALWAYS');
				
				//style
				$st_hover = array();
				$font_color = ZTSliderFunctions::getVal($def_val, 'color', '#000');
				if($font_color !== 'transparent'){
					$font_trans = ZTSliderFunctions::getVal($def_val, 'color-transparency', 1);
					if($font_trans > 0) $font_trans *= 100;
					$font_trans = intval($font_trans);
					$st_hover['c'] = array(ZTSliderFunctions::hex2rgba($font_color, $font_trans), 'ALWAYS');
				}else{
					$st_hover['c'] = array($font_color, 'ALWAYS');
				}
				$mfs = ZTSliderFunctions::getVal($def_val, 'font-style', 'off');
				$font_style = ($mfs == 'on' || $mfs == 'italic') ? 'italic' : 'normal';
				$st_hover['fs'] = array($font_style, 'normal');
				$st_hover['td'] = array(ZTSliderFunctions::getVal($def_val, 'text-decoration', 'none'), 'none');
				$bg_color = ZTSliderFunctions::getVal($def_val, 'background-color', 'transparent');
				if($bg_color !== 'transparent'){
					$bg_trans = ZTSliderFunctions::getVal($def_val, 'background-transparency', 1);
					if($bg_trans > 0) $bg_trans *= 100;
					$bg_trans = intval($bg_trans);
					$st_hover['bg'] = array(ZTSliderFunctions::hex2rgba($bg_color, $bg_trans), 'ALWAYS');
				}
				
				$my_padding = ZTSliderFunctions::getVal($def_val, 'padding', array('0px','0px','0px','0px'));
				if(!empty($my_padding)){
					$my_padding = implode(' ', $my_padding);
					if(trim($my_padding) != '')
						$st_hover['p'] = array($my_padding, '0px 0px 0px 0px');
				}
				
				$border_color = ZTSliderFunctions::getVal($def_val, 'border-color', 'transparent');
				if($border_color !== 'transparent'){
					$border_trans = ZTSliderFunctions::getVal($def_val, 'border-transparency', 1);
					if($border_trans > 0) $border_trans *= 100;
					$border_trans = intval($border_trans);
					$st_hover['bc'] = array(ZTSliderFunctions::hex2rgba($border_color, $border_trans), 'ALWAYS');
				}
				
				$st_hover['bs'] = array(ZTSliderFunctions::getVal($def_val, 'border-style', 'none'), 'none');
				$st_hover['bw'] = array(ZTSliderFunctions::add_missing_val(ZTSliderFunctions::getVal($def_val, 'border-width', '0px'), 'px'), '0px');
				if($st_hover['bw'][0] == '0px') unset($st_hover['bs']);
				
				$my_border = ZTSliderFunctions::getVal($def_val, 'border-radius', array('0px','0px','0px','0px'));
				if(!empty($my_border)){
					$my_border = implode(' ', $my_border);
					
					if(trim($my_border) != ''){
						$brsthoverdw = (isset($st_idle['border-radius']) && $st_idle['border-radius'] !== '0px 0px 0px 0px') ? 'ALWAYS' : '0px 0px 0px 0px';
						$st_hover['br'] = array($my_border, $brsthoverdw);
					}
				}
				
				$st_trans = array( 
					'c' => 'color',
					'fs' => 'font-style',
					'td' => 'text-decoration',
					'bg' => 'background-color',
					'p' => 'padding',
					'bc' => 'border-color',
					'bs' => 'border-style',
					'bw' => 'border-width',
					'br' => 'border-radius',
				);
				
				foreach($st_hover as $sk => $sv){ //do not write values for hover if idle is the same value
					if(isset($st_idle[$st_trans[$sk]]) && $st_idle[$st_trans[$sk]][0] == $sv[0]) unset($st_hover[$sk]);
				}
				
				
				//Advanced Styles here:
				if(isset($adv_style['hover'])){
					$adv_style['hover'] = (array)$adv_style['hover'];
					if(!empty($adv_style['hover'])){
						foreach($adv_style['hover'] as $n => $v){
							$st_hover[$n] = array($v, 'ALWAYS');
						}
					}
				}
				
				//Advanced Styles here:
				if(isset($il_style['hover'])){
					$il_style['hover'] = (array)$il_style['hover'];
					if(!empty($il_style['hover'])){
						foreach($il_style['hover'] as $n => $v){
							$st_hover[$n] = array($v, 'ALWAYS');
						}
					}
				}
				
				$def_string = '';
				foreach($def as $key => $value){
					if(trim($value[0]) == '' || $value[0] === $value[1]) continue;
					$def_string .= $key.':'.$value[0].';';
				}
				
				
				foreach($st_hover as $key => $value){
					if(trim($value[0]) == '' || $value[0] === $value[1]) continue;
					$st_h_string .= $key.':'.$value[0].';';
				}
				
				
				$deform_hover = '				data-transform_hover="'.$def_string.'"'."\n";
			}
			
			
			if($st_h_string !== ''){
				$style_hover = '				data-style_hover="'.$st_h_string.'"'."\n";
			}
			
			$static_data .= $deform.$deform_hover.$style_hover;


			//set corners
			$htmlCorners = "";
			
			//if($type == "no_edit"){}
			
			if($type == "text" || $type == "button"){
				$cornerdef = ZTSliderFunctions::getVal($layer, "deformation");
				$cornerLeft = ZTSliderFunctions::getVal($cornerdef, "corner_left");
				$cornerRight = ZTSliderFunctions::getVal($cornerdef, "corner_right");
				switch($cornerLeft){
					case "curved":
						$htmlCorners .= "<div class='frontcorner'></div>";
					break;
					case "reverced":
						$htmlCorners .= "<div class='frontcornertop'></div>";
					break;
				}

				switch($cornerRight){
					case "curved":
						$htmlCorners .= "<div class='backcorner'></div>";
					break;
					case "reverced":
						$htmlCorners .= "<div class='backcornertop'></div>";
					break;
				}

			}
			
			//add resizeme class
			$resize_full = ZTSliderFunctions::getVal($layer, "resize-full", true);
			if($resize_full === true || $resize_full == 'true' || $resize_full == '1'){
				$resizeme = ZTSliderFunctions::getVal($layer, "resizeme", true);
				if($resizeme == "true" || $resizeme == "1")
					$outputClass .= ' tp-resizeme';

			}else{//end text related layer
				$extra_data .= '			data-responsive="off"';
			}
			
			//make some modifications for the full screen video
			if($isFullWidthVideo == true){
				$htmlPosX = ' data-x="0"';
				$htmlPosY = ' data-y="0"';
				$outputClass .= ' fullscreenvideo';
				
			}

			//parallax part
			$use_parallax = $this->slider->getParam('use_parallax', $this->slider->getParam('use_parallax', 'off'));

			$parallax_class = '';
			if($use_parallax == 'on'){
				$ldef = ZTSliderFunctions::getVal($layer, 'deformation', array());
				$slide_level = ZTSliderFunctions::getVal($ldef, 'parallax', '-');				
				$parallax_ddd = $this->slider->getParam("ddd_parallax","off");
				
				$parallax_ddd_bgfreeze=$this->slider->getParam("ddd_parallax_bgfreeze","off");
				$ddd_z = ZTSliderFunctions::getVal($layer, 'parallax_layer_ddd_zlevel', ''); //bg , layers 

				if ($parallax_ddd=="on" && $slide_level=="-" && $ddd_z==="bg") $slide_level = "tobggroup";
				
				if($slide_level !== '-')
					$parallax_class = ' rs-parallaxlevel-'.$slide_level;
			}
			
			//check for actions
			$all_actions = ZTSliderFunctions::getVal($layer, 'layer_action', array());
			
			$a_tooltip_event = ZTSliderFunctions::cleanStdClassToArray(ZTSliderFunctions::getVal($all_actions, 'tooltip_event', array()));
			$a_action = ZTSliderFunctions::cleanStdClassToArray(ZTSliderFunctions::getVal($all_actions, 'action', array()));
			$a_image_link = ZTSliderFunctions::cleanStdClassToArray(ZTSliderFunctions::getVal($all_actions, 'image_link', array()));
			$a_link_open_in = ZTSliderFunctions::cleanStdClassToArray(ZTSliderFunctions::getVal($all_actions, 'link_open_in', array()));
			$a_jump_to_slide = ZTSliderFunctions::cleanStdClassToArray(ZTSliderFunctions::getVal($all_actions, 'jump_to_slide', array()));
			$a_scrolloffset = ZTSliderFunctions::cleanStdClassToArray(ZTSliderFunctions::getVal($all_actions, 'scrollunder_offset', array()));
			$a_actioncallback = ZTSliderFunctions::cleanStdClassToArray(ZTSliderFunctions::getVal($all_actions, 'actioncallback', array()));
			$a_target = ZTSliderFunctions::cleanStdClassToArray(ZTSliderFunctions::getVal($all_actions, 'layer_target', array()));
			$a_action_delay = ZTSliderFunctions::cleanStdClassToArray(ZTSliderFunctions::getVal($all_actions, 'action_delay', array()));
			$a_link_type = ZTSliderFunctions::cleanStdClassToArray(ZTSliderFunctions::getVal($all_actions, 'link_type', array()));
			$a_toggle_layer_type = ZTSliderFunctions::cleanStdClassToArray(ZTSliderFunctions::getVal($all_actions, 'toggle_layer_type', array()));
			$a_toggle_class = ZTSliderFunctions::cleanStdClassToArray(ZTSliderFunctions::getVal($all_actions, 'toggle_class', array()));
			
			$a_html = '';
			$a_events = array();
			if(!empty($a_action)){
				$a_html .= "			data-actions='";
				foreach($a_action as $num => $action){
					$layer_attrID = '';
					switch($action){
						case 'start_in':
						case 'start_out':
						case 'start_video':
						case 'stop_video':
						case 'toggle_layer':
						case 'toggle_video':
						case 'simulate_click':
						case 'toggle_class':
						case 'toggle_mute_video':						
						case 'mute_video':
						case 'unmute_video':
							//get the ID of the layer with the unique_id that is $a_target[$num]
							$layer_attrID = $slide->getLayerID_by_unique_id($a_target[$num], $this->static_slide);
							if($a_target[$num] == 'backgroundvideo' || $a_target[$num] == 'firstvideo'){
								$layer_attrID = $a_target[$num];
							}elseif(trim($layer_attrID) == ''){
								if(strpos($a_target[$num], 'static-') !== false || $static_slide){
									$layer_attrID = 'slider-'.preg_replace("/[^\w]+/", "", $this->slider->getID()).'-layer-'.str_replace('static-', '', $a_target[$num]);
								}else{
									$layer_attrID = 'slide-'.preg_replace("/[^\w]+/", "", $slideID).'-layer-'.$a_target[$num];
								}
							}
						break;
					}
					
					switch($action){
						case 'none':
							continue;
						break;
						case 'link':
							//if post based, replace {{}} with correct info
							//a_image_link
							if(isset($a_image_link[$num])) $a_image_link[$num] = $a_image_link[$num];
							if(isset($a_link_type[$num]) && $a_link_type[$num] == 'jquery'){
								/*
								$setBase = (is_ssl()) ? "https://" : "http://";
								if(strpos($a_image_link[$num], 'http') === false)
									$a_image_link[$num] = $setBase .$a_image_link[$num];
								*/
								$a_tooltip_event[$num] = (isset($a_tooltip_event[$num])) ? $a_tooltip_event[$num] : '';
								$a_link_open_in[$num] = (isset($a_link_open_in[$num])) ? $a_link_open_in[$num] : '';
								$a_image_link[$num] = (isset($a_image_link[$num])) ? $a_image_link[$num] : '';
								$a_action_delay[$num] = (isset($a_action_delay[$num])) ? $a_action_delay[$num] : '';
								
								$a_events[] = array(
									'event' => $a_tooltip_event[$num],
									'action' => 'simplelink',
									'target' => $a_link_open_in[$num],
									'url' => $a_image_link[$num],
									'delay' => $a_action_delay[$num]
								);
							}else{
								if($html_simple_link == ''){ //adds the link to the layer
									/*
									$setBase = (is_ssl()) ? "https://" : "http://";
									if(strpos($a_image_link[$num], 'http') === false)
										$a_image_link[$num] = $setBase .$a_image_link[$num];
									*/
									$a_image_link[$num] = (isset($a_image_link[$num])) ? $a_image_link[$num] : '';
									$a_link_open_in[$num] = (isset($a_link_open_in[$num])) ? $a_link_open_in[$num] : '';
									
									$html_simple_link = ' href="'.$a_image_link[$num].'"';
									$html_simple_link .=' target="'.$a_link_open_in[$num].'"';
								}
							}
						break;
						case 'jumpto':
							$a_tooltip_event[$num] = (isset($a_tooltip_event[$num])) ? $a_tooltip_event[$num] : '';
							$a_jump_to_slide[$num] = (isset($a_jump_to_slide[$num])) ? $a_jump_to_slide[$num] : '';
							$a_action_delay[$num] = (isset($a_action_delay[$num])) ? $a_action_delay[$num] : '';
							
							$a_events[] = array(
								'event' => $a_tooltip_event[$num],
								'action' => 'jumptoslide',
								'slide' => 'rs-'.$a_jump_to_slide[$num],
								'delay' => $a_action_delay[$num]
							);
						break;
						case 'next':
							$a_tooltip_event[$num] = (isset($a_tooltip_event[$num])) ? $a_tooltip_event[$num] : '';
							$a_action_delay[$num] = (isset($a_action_delay[$num])) ? $a_action_delay[$num] : '';
							
							$a_events[] = array(
								'event' => $a_tooltip_event[$num],
								'action' => 'jumptoslide',
								'slide' => 'next',
								'delay' => $a_action_delay[$num]
							);
						break;
						case 'gofullscreen':
							$a_tooltip_event[$num] = (isset($a_tooltip_event[$num])) ? $a_tooltip_event[$num] : '';
							$a_action_delay[$num] = (isset($a_action_delay[$num])) ? $a_action_delay[$num] : '';
							
							$a_events[] = array(
								'event' => $a_tooltip_event[$num],
								'action' => 'gofullscreen',								
								'delay' => $a_action_delay[$num]
							);
						break;
						case 'toggle_global_mute_video':
							$a_tooltip_event[$num] = (isset($a_tooltip_event[$num])) ? $a_tooltip_event[$num] : '';
							$a_action_delay[$num] = (isset($a_action_delay[$num])) ? $a_action_delay[$num] : '';
							
							$a_events[] = array(
								'event' => $a_tooltip_event[$num],
								'action' => 'toggle_global_mute_video',								
								'delay' => $a_action_delay[$num]
							);
						break;
						case 'exitfullscreen':
							$a_tooltip_event[$num] = (isset($a_tooltip_event[$num])) ? $a_tooltip_event[$num] : '';
							$a_action_delay[$num] = (isset($a_action_delay[$num])) ? $a_action_delay[$num] : '';
							
							$a_events[] = array(
								'event' => $a_tooltip_event[$num],
								'action' => 'exitfullscreen',								
								'delay' => $a_action_delay[$num]
							);
						break;
						case 'togglefullscreen':
							$a_tooltip_event[$num] = (isset($a_tooltip_event[$num])) ? $a_tooltip_event[$num] : '';
							$a_action_delay[$num] = (isset($a_action_delay[$num])) ? $a_action_delay[$num] : '';
							
							$a_events[] = array(
								'event' => $a_tooltip_event[$num],
								'action' => 'togglefullscreen',								
								'delay' => $a_action_delay[$num]
							);
						break;
						case 'prev':
							$a_tooltip_event[$num] = (isset($a_tooltip_event[$num])) ? $a_tooltip_event[$num] : '';
							$a_action_delay[$num] = (isset($a_action_delay[$num])) ? $a_action_delay[$num] : '';
							
							$a_events[] = array(
								'event' => $a_tooltip_event[$num],
								'action' => 'jumptoslide',
								'slide' => 'previous',
								'delay' => $a_action_delay[$num]
							);
						break;
						case 'pause':
							$a_tooltip_event[$num] = (isset($a_tooltip_event[$num])) ? $a_tooltip_event[$num] : '';
							$a_action_delay[$num] = (isset($a_action_delay[$num])) ? $a_action_delay[$num] : '';
							$a_events[] = array(
								'event' => $a_tooltip_event[$num],
								'action' => 'pauseslider',
								'delay' => $a_action_delay[$num]
							);
						break;
						case 'resume':
							$a_tooltip_event[$num] = (isset($a_tooltip_event[$num])) ? $a_tooltip_event[$num] : '';
							$a_action_delay[$num] = (isset($a_action_delay[$num])) ? $a_action_delay[$num] : '';
							$a_events[] = array(
								'event' => $a_tooltip_event[$num],
								'action' => 'playslider',
								'delay' => $a_action_delay[$num]
							);
						break;
						case 'toggle_slider':
							$a_tooltip_event[$num] = (isset($a_tooltip_event[$num])) ? $a_tooltip_event[$num] : '';
							$a_action_delay[$num] = (isset($a_action_delay[$num])) ? $a_action_delay[$num] : '';
							$a_events[] = array(
								'event' => $a_tooltip_event[$num],
								'action' => 'toggleslider',
								'delay' => $a_action_delay[$num]
							);
						break;
						case 'callback':
							$a_tooltip_event[$num] = (isset($a_tooltip_event[$num])) ? $a_tooltip_event[$num] : '';
							$a_actioncallback[$num] = (isset($a_actioncallback[$num])) ? $a_actioncallback[$num] : '';
							$a_action_delay[$num] = (isset($a_action_delay[$num])) ? $a_action_delay[$num] : '';
							$a_events[] = array(
								'event' => $a_tooltip_event[$num],
								'action' => 'callback',
								'callback' => $a_actioncallback[$num],
								'delay' => $a_action_delay[$num]
							);
						break;
						case 'scroll_under': //ok
							$a_tooltip_event[$num] = (isset($a_tooltip_event[$num])) ? $a_tooltip_event[$num] : '';
							$a_scrolloffset[$num] = (isset($a_scrolloffset[$num])) ? $a_scrolloffset[$num] : '';
							$a_action_delay[$num] = (isset($a_action_delay[$num])) ? $a_action_delay[$num] : '';
							$a_events[] = array(
								'event' => $a_tooltip_event[$num],
								'action' => 'scrollbelow',
								'offset' => ZTSliderFunctions::add_missing_val($a_scrolloffset[$num], 'px'),
								'delay' => $a_action_delay[$num]
							);
						break;
						case 'start_in':
							$a_tooltip_event[$num] = (isset($a_tooltip_event[$num])) ? $a_tooltip_event[$num] : '';
							$a_action_delay[$num] = (isset($a_action_delay[$num])) ? $a_action_delay[$num] : '';
							
							$a_events[] = array(
								'event' => $a_tooltip_event[$num],
								'action' => 'startlayer',
								'layer' => $layer_attrID,
								'delay' => $a_action_delay[$num]
							);
						break;
						case 'start_out':
							$a_tooltip_event[$num] = (isset($a_tooltip_event[$num])) ? $a_tooltip_event[$num] : '';
							$a_action_delay[$num] = (isset($a_action_delay[$num])) ? $a_action_delay[$num] : '';
							
							$a_events[] = array(
								'event' => $a_tooltip_event[$num],
								'action' => 'stoplayer',
								'layer' => $layer_attrID,
								'delay' => $a_action_delay[$num]
							);
						break;
						case 'toggle_layer':
							$a_tooltip_event[$num] = (isset($a_tooltip_event[$num])) ? $a_tooltip_event[$num] : '';
							$a_toggle_layer_type[$num] = (isset($a_toggle_layer_type[$num])) ? $a_toggle_layer_type[$num] : '';
							$a_action_delay[$num] = (isset($a_action_delay[$num])) ? $a_action_delay[$num] : '';
							
							$a_events[] = array(
								'event' => $a_tooltip_event[$num],
								'action' => 'togglelayer',
								'layerstatus' => $a_toggle_layer_type[$num],
								'layer' => $layer_attrID,
								'delay' => $a_action_delay[$num]
							);
						break;
						case 'start_video':
							$a_tooltip_event[$num] = (isset($a_tooltip_event[$num])) ? $a_tooltip_event[$num] : '';
							$a_action_delay[$num] = (isset($a_action_delay[$num])) ? $a_action_delay[$num] : '';
							
							$a_events[] = array(
								'event' => $a_tooltip_event[$num],
								'action' => 'playvideo',
								'layer' => $layer_attrID,
								'delay' => $a_action_delay[$num]
							);
						break;
						case 'stop_video':
							$a_tooltip_event[$num] = (isset($a_tooltip_event[$num])) ? $a_tooltip_event[$num] : '';
							$a_action_delay[$num] = (isset($a_action_delay[$num])) ? $a_action_delay[$num] : '';
							
							$a_events[] = array(
								'event' => $a_tooltip_event[$num],
								'action' => 'stopvideo',
								'layer' => $layer_attrID,
								'delay' => $a_action_delay[$num]
							);
						break;
						case 'mute_video':
							$a_tooltip_event[$num] = (isset($a_tooltip_event[$num])) ? $a_tooltip_event[$num] : '';
							$a_action_delay[$num] = (isset($a_action_delay[$num])) ? $a_action_delay[$num] : '';
							
							$a_events[] = array(
								'event' => $a_tooltip_event[$num],
								'action' => 'mutevideo',
								'layer' => $layer_attrID,
								'delay' => $a_action_delay[$num]
							);
						break;
						case 'unmute_video':
							$a_tooltip_event[$num] = (isset($a_tooltip_event[$num])) ? $a_tooltip_event[$num] : '';
							$a_action_delay[$num] = (isset($a_action_delay[$num])) ? $a_action_delay[$num] : '';
							
							$a_events[] = array(
								'event' => $a_tooltip_event[$num],
								'action' => 'unmutevideo',
								'layer' => $layer_attrID,
								'delay' => $a_action_delay[$num]
							);
						break;
						case 'toggle_video':
							$a_tooltip_event[$num] = (isset($a_tooltip_event[$num])) ? $a_tooltip_event[$num] : '';
							$a_action_delay[$num] = (isset($a_action_delay[$num])) ? $a_action_delay[$num] : '';
							
							$a_events[] = array(
								'event' => $a_tooltip_event[$num],
								'action' => 'togglevideo',
								'layer' => $layer_attrID,
								'delay' => $a_action_delay[$num]
							);
						break;
						case 'toggle_mute_video':
							$a_tooltip_event[$num] = (isset($a_tooltip_event[$num])) ? $a_tooltip_event[$num] : '';
							$a_action_delay[$num] = (isset($a_action_delay[$num])) ? $a_action_delay[$num] : '';
							
							$a_events[] = array(
								'event' => $a_tooltip_event[$num],
								'action' => 'toggle_mute_video',
								'layer' => $layer_attrID,
								'delay' => $a_action_delay[$num]
							);
						break;
						case 'simulate_click':
							$a_tooltip_event[$num] = (isset($a_tooltip_event[$num])) ? $a_tooltip_event[$num] : '';
							$a_action_delay[$num] = (isset($a_action_delay[$num])) ? $a_action_delay[$num] : '';
							
							$a_events[] = array(
								'event' => $a_tooltip_event[$num],
								'action' => 'simulateclick',
								'layer' => $layer_attrID,
								'delay' => $a_action_delay[$num]
							);
						break;
						case 'toggle_class':
							$a_tooltip_event[$num] = (isset($a_tooltip_event[$num])) ? $a_tooltip_event[$num] : '';
							$a_action_delay[$num] = (isset($a_action_delay[$num])) ? $a_action_delay[$num] : '';
							$a_toggle_class[$num] = (isset($a_toggle_class[$num])) ? $a_toggle_class[$num] : '';
							
							$a_events[] = array(
								'event' => $a_tooltip_event[$num],
								'action' => 'toggleclass',
								'layer' => $layer_attrID,
								'delay' => $a_action_delay[$num],
								'classname' => $a_toggle_class[$num]
							);
						break;
					}
				}
				if(!empty($a_events)){
					$a_html .= json_encode($a_events);
				}
				$a_html .= "'\n";
			}
			
			//check for loop animation here
			$do_loop = ZTSliderFunctions::getVal($layer, 'loop_animation', 'none');
			$loop_data = '';
			$loop_class = '';
			
			if($do_loop !== 'none'){
				$loop_class = ' '.$do_loop;
				switch($do_loop){
					case 'rs-pendulum':
						$loop_data.= ' data-easing="'.ZTSliderFunctions::getVal($layer, 'loop_easing', 'Power3.easeInOut').'"';
						$loop_data.= ' data-startdeg="'.ZTSliderFunctions::getVal($layer, 'loop_startdeg', '-20').'"';
						$loop_data.= ' data-enddeg="'.ZTSliderFunctions::getVal($layer, 'loop_enddeg', '20').'"';
						$loop_data.= ' data-speed="'.ZTSliderFunctions::getVal($layer, 'loop_speed', '2').'"';
						$loop_data.= ' data-origin="'.ZTSliderFunctions::getVal($layer, 'loop_xorigin', '50').'% '.ZTSliderFunctions::getVal($layer, 'loop_yorigin', '50').'%"';
					break;
					case 'rs-rotate':
						$loop_data.= ' data-easing="'.ZTSliderFunctions::getVal($layer, 'loop_easing', 'Power3.easeInOut').'"';
						$loop_data.= ' data-startdeg="'.ZTSliderFunctions::getVal($layer, 'loop_startdeg', '-20').'"';
						$loop_data.= ' data-enddeg="'.ZTSliderFunctions::getVal($layer, 'loop_enddeg', '20').'"';
						$loop_data.= ' data-speed="'.ZTSliderFunctions::getVal($layer, 'loop_speed', '2').'"';
						$loop_data.= ' data-origin="'.ZTSliderFunctions::getVal($layer, 'loop_xorigin', '50').'% '.ZTSliderFunctions::getVal($layer, 'loop_yorigin', '50').'%"';
					break;
					
					case 'rs-slideloop':
						$loop_data.= ' data-easing="'.ZTSliderFunctions::getVal($layer, 'loop_easing', 'Power3.easeInOut').'"';
						$loop_data.= ' data-speed="'.ZTSliderFunctions::getVal($layer, 'loop_speed', '1').'"';
						$loop_data.= ' data-xs="'.ZTSliderFunctions::getVal($layer, 'loop_xstart', '0').'"';
						$loop_data.= ' data-xe="'.ZTSliderFunctions::getVal($layer, 'loop_xend' ,'0').'"';
						$loop_data.= ' data-ys="'.ZTSliderFunctions::getVal($layer, 'loop_ystart', '0').'"';
						$loop_data.= ' data-ye="'.ZTSliderFunctions::getVal($layer, 'loop_yend', '0').'"';
					break;
					case 'rs-pulse':
						$loop_data.= ' data-easing="'.ZTSliderFunctions::getVal($layer, 'loop_easing', 'Power3.easeInOut').'"';
						$loop_data.= ' data-speed="'.ZTSliderFunctions::getVal($layer, 'loop_speed', '1').'"';
						$loop_data.= ' data-zoomstart="'.ZTSliderFunctions::getVal($layer, 'loop_zoomstart', '1').'"';
						$loop_data.= ' data-zoomend="'.ZTSliderFunctions::getVal($layer, 'loop_zoomend', '1').'"';
					break;
					case 'rs-wave':
						$loop_data.= ' data-speed="'.ZTSliderFunctions::getVal($layer, 'loop_speed', '1').'"';
						$loop_data.= ' data-angle="'.ZTSliderFunctions::getVal($layer, 'loop_angle', '0').'"';
						$loop_data.= ' data-radius="'.ZTSliderFunctions::getVal($layer, 'loop_radius', '10').'"';
						$loop_data.= ' data-origin="'.ZTSliderFunctions::getVal($layer, 'loop_xorigin', '50').'% '.ZTSliderFunctions::getVal($layer, 'loop_yorigin', '50').'%"';
					break;
				}
			}

			if (!empty($svg_val) &&  sizeof($svg_val)>0) $outputClass .=' tp-svg-layer'; 
			
			$layer_id = $zIndex - 4;
			
			$use_tag = ZTSliderFunctions::getVal($layer, 'html_tag', 'div');
			
			if($html_simple_link !== '') $use_tag = 'a';
			
			echo "\n		<!-- LAYER NR. ";
			echo $layer_id;
			echo " -->\n";
			echo '		<'.$use_tag.' class="'.$outputClass;
			echo ($classes != '') ? ' '.$classes : '';
			echo $parallax_class;
			if($static_slide) echo ' tp-static-layer';
			
			echo $videoclass;
			
			echo "\" \n";
			echo $html_simple_link;
			echo ($ids != '') ? '			'.$ids." \n" : '';
			echo ($title != '') ? '			'.$title." \n" : '';
			echo ($rel != '') ? '			'.$rel." \n" : '';
			if($htmlPosX != '') echo '			'.$htmlPosX." \n";
			if($htmlPosY != '') echo '			'.$htmlPosY." \n";
			if($static_data != '') echo '			'.$static_data." \n";
			if($customin != '') echo '			'.$customin." \n";
			if($customout != '') echo '			'.$customout." \n";
			if($maskin != '') echo '			'.$maskin." \n";
			if($maskout != '') echo '			'.$maskout." \n";
			echo '			data-start="'.$time.'"'." \n";
			
			//if($type == 'no_edit'){}
			
			if($type == 'text' || $type == 'button'){ //only output if we are a text layer
				echo '			data-splitin="'.$splitin.'"'." \n";
				echo '			data-splitout="'.$splitout.'"'." \n";
			}
			
			echo $a_html;
			
			// SVG OUTPUT
			if (!empty($svg_val) && sizeof($svg_val)>0) {				
				echo '			data-svg_src="'.$svg_val->{'src'}.'"'." \n";
				echo '			data-svg_idle="sc:'.$svg_val->{'svgstroke-color'}.';sw:'.$svg_val->{'svgstroke-width'}.';sda:'.$svg_val->{'svgstroke-dasharray'}.';sdo:'.$svg_val->{'svgstroke-dashoffset'}.';"'." \n";
				if($is_hover_active){
					echo '			data-svg_hover="sc:'.$svg_val->{'svgstroke-hover-color'}.';sw:'.$svg_val->{'svgstroke-hover-width'}.';sda:'.$svg_val->{'svgstroke-hover-dasharray'}.';sdo:'.$svg_val->{'svgstroke-hover-dashoffset'}.';"'." \n";
				}
			}

			if($basealign !== 'grid'){
				echo '			data-basealign="'.$basealign.'"'." \n";
			}
			$ro = ZTSliderFunctions::getVal($layer, 'responsive_offset', true);
			
			if($ro!=1){
				echo '			data-responsive_offset="off"'." \n";
			} else {
				echo '			data-responsive_offset="on"'." \n";
			}
			
			echo $extra_data."\n";
			//check if static layer and if yes, set values for it.
			if($static_slide){
				if($isTemplate != "true" && $slider_type !== 'hero'){
					$start_on_slide = intval(ZTSliderFunctions::getVal($layer,"static_start",1)) - 1;
					$end_on_slide = ZTSliderFunctions::getVal($layer,"static_end",2);
					if($end_on_slide !== 'last'){
						$end_on_slide = intval($end_on_slide) - 1;
					}else{ //change number to the last slide number
						$end_on_slide = count($this->slider->getArrSlideNames()) - 1;
					}
				}else{
					$start_on_slide = '-1';
					$end_on_slide = '-1';
				}
				
				echo '			data-startslide="'.$start_on_slide.'"'." \n";
				echo '			data-endslide="'.$end_on_slide.'"'." \n";
			}
			if($splitin !== 'none'){
				echo '			data-elementdelay="'.$elementdelay.'"'." \n";
			}
			if($splitout !== 'none'){
				echo '			data-endelementdelay="'.$endelementdelay.'"'." \n";
			}
			if($htmlParams != "") echo "			".$htmlParams;
			echo '			style="z-index: '.$zIndex.';'.$inline_styles. $idle_style.'"'; //
			echo '>';
			
			if($do_loop !== 'none'){
				echo "\n".'<div class="rs-looped '.trim($loop_class).'" '.$loop_data.'>';
			}
			
			if ($toggle_allow!=false) {
				echo '<div class="rs-untoggled-content">';
			}
			echo $html." ";
			if ($toggle_allow!=false) {
				echo '</div>';
				echo '<div class="rs-toggled-content">'.stripslashes($html_toggle).'</div>';
			}
			if($htmlCorners != ""){
				echo $htmlCorners." ";
			}
			if($do_loop !== 'none'){
				echo "</div>";
			}
			echo '</'.$use_tag.'>'."\n";
			$zIndex++;
		}
	}

	/**
	 *
	 * put slider javascript
	 */
	private function putJS($markup_export = false){

		$params = $this->slider->getParams();
		$sliderType = $this->slider->getParam('slider_type');
		$force_fullwidth = $this->slider->getParam('force_full_width','on');

		if($sliderType == 'fixed' || $sliderType == 'responsitive' || ($sliderType == 'fullwidth' && $force_fullwidth=="off")) $sliderType = 'auto';

		$optFullWidth = ($sliderType == 'fullwidth') ? 'on' : 'off';
		$optFullScreen = 'off';

		if($sliderType == 'fullscreen'){
			$optFullWidth = 'off';
			$optFullScreen = 'on';
		}

		$use_spinner = $this->slider->getParam('use_spinner', '0');
		$spinner_color = $this->slider->getParam('spinner_color', '#FFFFFF');

		$noConflict = $this->slider->getParam("jquery_noconflict","on");
		$debugmode = ($this->slider->getParam("jquery_debugmode","off")=='on') ? 'true' : 'false';
		

		//set thumb amount
		$numSlides = $this->slider->getNumSlides(true);

		//get stop slider options
		
		$stopSlider = $this->slider->getParam("stop_slider","off");
		$stopAfterLoops = $this->slider->getParam("stop_after_loops","0");
		$stopAtSlide = $this->slider->getParam("stop_at_slide","2");

		if($stopSlider == "off"){
			$stopAfterLoops = "-1";
			$stopAtSlide = "-1";
		}

		$oneSlideLoop = $this->slider->getParam("loop_slide","loop");
		if($oneSlideLoop == 'noloop' && $this->hasOnlyOneSlide == true){
			$stopAfterLoops = '0';
			$stopAtSlide = '1';
		}

		$sliderID = $this->slider->getID();

		//treat hide slider at limit
		$hideSliderAtLimit = $this->slider->getParam("hide_slider_under","0",ZTSlider::VALIDATE_NUMERIC);
		if(!empty($hideSliderAtLimit))
			$hideSliderAtLimit++;

		$hideCaptionAtLimit = $this->slider->getParam("hide_defined_layers_under","0",ZTSlider::VALIDATE_NUMERIC);;
		if(!empty($hideCaptionAtLimit))
			$hideCaptionAtLimit++;

		$hideAllCaptionAtLimit = $this->slider->getParam("hide_all_layers_under","0",ZTSlider::VALIDATE_NUMERIC);;
		if(!empty($hideAllCaptionAtLimit))
			$hideAllCaptionAtLimit++;

		//start_with_slide
		$startWithSlide = $this->slider->getStartWithSlideSetting();

		//modify navigation type (backward compatability)
		//FALLBACK
		$enable_arrows = $this->slider->getParam('enable_arrows','off');
		$arrowsType = ($enable_arrows == 'on') ? 'solo' : 'none';
		

		//More Mobile Options
		$hideThumbsOnMobile = $this->slider->getParam("hide_thumbs_on_mobile","off");
		$enable_progressbar =  $this->slider->getParam('enable_progressbar','on');
		
		$disableKenBurnOnMobile =  $this->slider->getParam("disable_kenburns_on_mobile","off");

		$swipe_velocity = $this->slider->getParam("swipe_velocity","0.7",ZTSlider::VALIDATE_NUMERIC);
		$swipe_min_touches = $this->slider->getParam("swipe_min_touches","1",ZTSlider::VALIDATE_NUMERIC);
		$swipe_direction = $this->slider->getParam("swipe_direction", "horizontal");
		$drag_block_vertical = $this->slider->getParam("drag_block_vertical","false");

		$use_parallax = $this->slider->getParam("use_parallax",$this->slider->getParam('use_parallax', 'off'));
		$disable_parallax_mobile = $this->slider->getParam("disable_parallax_mobile","off");

		if($use_parallax == 'on'){
			$parallax_ddd = $this->slider->getParam("ddd_parallax","off");
			$parallax_type = $this->slider->getParam("parallax_type","mouse");
			$parallax_origo = $this->slider->getParam("parallax_origo","enterpoint");
			$parallax_speed = $this->slider->getParam("parallax_speed","400");

			if ($parallax_ddd=="on") {
				$parallax_type="3D";
				$parallax_origo="slidercenter";
				$parallax_ddd_shadow=$this->slider->getParam("ddd_parallax_shadow","on");
				$parallax_ddd_bgfreeze=$this->slider->getParam("ddd_parallax_bgfreeze","off");
				$parallax_ddd_overflow=$this->slider->getParam("ddd_parallax_overflow","off");
				$parallax_ddd_layer_overflow=$this->slider->getParam("ddd_parallax_layer_overflow","off");
				
				$parallax_ddd_overflow= $parallax_ddd_overflow == "off" ? "visible" : "hidden";
				$parallax_ddd_layer_overflow= $parallax_ddd_layer_overflow == "off" ? "visible" : "hidden";
				$parallax_ddd_zcorrection = $this->slider->getParam("ddd_parallax_zcorrection","400");
				//$parallax_ddd_path = $this->slider->getParam("ddd_parallax_path","mouse");
			}
			
			$parallax_level[] = intval($this->slider->getParam("parallax_level_1","5"));
			$parallax_level[] = intval($this->slider->getParam("parallax_level_2","10"));
			$parallax_level[] = intval($this->slider->getParam("parallax_level_3","15"));
			$parallax_level[] = intval($this->slider->getParam("parallax_level_4","20"));
			$parallax_level[] = intval($this->slider->getParam("parallax_level_5","25"));
			$parallax_level[] = intval($this->slider->getParam("parallax_level_6","30"));
			$parallax_level[] = intval($this->slider->getParam("parallax_level_7","35"));
			$parallax_level[] = intval($this->slider->getParam("parallax_level_8","40"));
			$parallax_level[] = intval($this->slider->getParam("parallax_level_9","45"));
			$parallax_level[] = intval($this->slider->getParam("parallax_level_10","45"));
			$parallax_level[] = intval($this->slider->getParam("parallax_level_11","46"));
			$parallax_level[] = intval($this->slider->getParam("parallax_level_12","47"));
			$parallax_level[] = intval($this->slider->getParam("parallax_level_13","48"));
			$parallax_level[] = intval($this->slider->getParam("parallax_level_14","49"));			
			$parallax_level[] = intval($this->slider->getParam("parallax_level_15","50"));
			$parallax_level[] = intval($this->slider->getParam("parallax_level_16","55"));
			$parallax_level = implode(',', $parallax_level);
		}

		$operations = new ZTSliderOperations();
		$arrValues = $operations->getGeneralSettingsValues();
		
		$do_delay = $this->slider->getParam("start_js_after_delay","0");
		$do_delay = intval($do_delay);
		
		$js_to_footer = (isset($arrValues['js_to_footer']) && $arrValues['js_to_footer'] == 'on') ? true : false;
		
		$slider_type = $this->slider->getParam('slider-type', 'standard');
		
		//add inline style into the footer
		if($js_to_footer && $this->previewMode == false && $markup_export == false){
			ob_start();
		}
		
		$nav_css = '';
		
		if($markup_export === true){
			echo '<!-- STYLE -->';
		}
		//ADD SCOPED INLINE STYLES 			
		$this->add_inline_styles();
		
		if($markup_export === true){
			echo '<!-- /STYLE -->';
		}
		$csizes = ZTSliderFunctions::get_responsive_size($this);
		
		if($markup_export === true){
			echo '<!-- SCRIPT -->';
		}
		?>
		<script type="text/javascript">
			<?php if(!$markup_export){ //not needed for html markup export ?>
			/******************************************
				-	PREPARE PLACEHOLDER FOR SLIDER	-
			******************************************/

			var setREVStartSize=function(){
				try{var e=new Object,i=jQuery(window).width(),t=9999,r=0,n=0,l=0,f=0,s=0,h=0;
					e.c = jQuery('#<?php echo $this->sliderHtmlID;?>');
<?php if(isset($csizes['level']) && !empty($csizes['level'])){ ?>
					e.responsiveLevels = <?php echo '['. $csizes['level'] .']'; ?>;
					e.gridwidth = <?php echo '['. $csizes['width'] .']'; ?>;
					e.gridheight = <?php echo '['. $csizes['height'] .']'; ?>;
<?php } else {?>
					e.gridwidth = <?php echo '['. $csizes['width'] .']'; ?>;
					e.gridheight = <?php echo '['. $csizes['height'] .']'; ?>;
<?php } ?>							
<?php if($optFullScreen == 'on'){
						$sl_layout = 'fullscreen';
					}elseif($optFullWidth == 'on'){
						$sl_layout = 'fullwidth';
					}else{
						$sl_layout = 'auto';}?>
					e.sliderLayout = "<?php echo $sl_layout; ?>";
<?php if($this->slider->getParam("slider_type") == "fullscreen"){ ?>
					e.fullScreenAutoWidth='<?php echo trim($this->slider->getParam("autowidth_force","off")); ?>';
					e.fullScreenAlignForce='<?php echo trim($this->slider->getParam("full_screen_align_force","off")); ?>';
					e.fullScreenOffsetContainer= '<?php echo trim($this->slider->getParam("fullscreen_offset_container","")); ?>';
					e.fullScreenOffset='<?php echo trim($this->slider->getParam("fullscreen_offset_size","")); ?>';
<?php } ?>
<?php $minHeight = ($this->slider->getParam('slider_type') !== 'fullscreen') ? $this->slider->getParam('min_height', '0') : $this->slider->getParam('fullscreen_min_height', '0');
					if($minHeight > 0){ ?>
					e.minHeight = "<?php echo $minHeight; ?>";
<?php } ?>
					if(e.responsiveLevels&&(jQuery.each(e.responsiveLevels,function(e,f){f>i&&(t=r=f,l=e),i>f&&f>r&&(r=f,n=e)}),t>r&&(l=n)),f=e.gridheight[l]||e.gridheight[0]||e.gridheight,s=e.gridwidth[l]||e.gridwidth[0]||e.gridwidth,h=i/s,h=h>1?1:h,f=Math.round(h*f),"fullscreen"==e.sliderLayout){var u=(e.c.width(),jQuery(window).height());if(void 0!=e.fullScreenOffsetContainer){var c=e.fullScreenOffsetContainer.split(",");if (c) jQuery.each(c,function(e,i){u=jQuery(i).length>0?u-jQuery(i).outerHeight(!0):u}),e.fullScreenOffset.split("%").length>1&&void 0!=e.fullScreenOffset&&e.fullScreenOffset.length>0?u-=jQuery(window).height()*parseInt(e.fullScreenOffset,0)/100:void 0!=e.fullScreenOffset&&e.fullScreenOffset.length>0&&(u-=parseInt(e.fullScreenOffset,0))}f=u}else void 0!=e.minHeight&&f<e.minHeight&&(f=e.minHeight);e.c.closest(".rev_slider_wrapper").css({height:f})
					
				}catch(d){console.log("Failure at Presize of Slider:"+d)}
			};
			
			setREVStartSize();
			
			<?php } ?>
			var tpj=jQuery;
			<?php if($noConflict == "on"){ ?>tpj.noConflict();<?php } ?>

			var revapi<?php echo $sliderID; ?>;
			<?php

			echo 'tpj(document).ready(function() {'."\n";
			echo '				if(tpj("#'.$this->sliderHtmlID.'").revolution == undefined){'."\n";
			echo '					revslider_showDoubleJqueryError("#'.$this->sliderHtmlID.'");'."\n";
			echo '				}else{'."\n";
			echo '					revapi'. $sliderID.' = tpj("#'. $this->sliderHtmlID .'").show().revolution({'."\n";
			if($do_delay > 0){
				echo '						startDelay: '. trim($do_delay) .','."\n";
			} 
			
			echo '						sliderType:"'. trim($slider_type) .'",'."\n";
			
			$stripped_http = explode('://', ZT_ASSETS_URL);
			echo 'jsFileLocation:"//'. trim( $stripped_http[1] .'/assets/js/' ) .'",'."\n";

			if($optFullScreen == 'on'){
				$sl_layout = 'fullscreen';
			}elseif($optFullWidth == 'on'){
				$sl_layout = 'fullwidth';
			}else{
				$sl_layout = 'auto';
			}
			echo '						sliderLayout:"'.$sl_layout.'",'."\n";
			echo '						dottedOverlay:"'. trim($this->slider->getParam("background_dotted_overlay","none")).'",'."\n";
			echo '						delay:'.trim($this->slider->getParam("delay","9000",ZTSlider::FORCE_NUMERIC)) .','."\n";

			
			$enable_arrows = $this->slider->getParam('enable_arrows','off');
			$enable_bullets = $this->slider->getParam('enable_bullets','off');
			$enable_tabs = $this->slider->getParam('enable_tabs','off');
			$enable_thumbnails = $this->slider->getParam('enable_thumbnails','off');
			
			$rs_nav = new ZTSliderNavigation();
			$all_navs = $rs_nav->get_all_navigations();
			$touch_enabled = $this->slider->getParam('touchenabled', 'on');
			$keyboard_enabled = $this->slider->getParam('keyboard_navigation', 'off');
			$keyboard_direction = $this->slider->getParam('keyboard_direction', 'horizontal');
			$mousescroll_enabled = $this->slider->getParam('mousescroll_navigation', 'off');
			$mousescroll_reverse = $this->slider->getParam('mousescroll_navigation_reverse', 'default');
			
			//no navigation if we are hero
			if($slider_type !== 'hero' && ($enable_arrows == 'on' || $enable_bullets == 'on' || $enable_tabs == 'on' || $enable_thumbnails == 'on' || $touch_enabled == 'on' || $keyboard_enabled == 'on' || $mousescroll_enabled != 'off')){
				echo '						navigation: {'."\n";
				echo '							keyboardNavigation:"'. trim($keyboard_enabled) .'",'."\n";
				echo '							keyboard_direction: "'. trim($keyboard_direction) .'",'."\n";
				echo '							mouseScrollNavigation:"'. trim($mousescroll_enabled) .'",'."\n";
				echo ' 							mouseScrollReverse:"'. trim($mousescroll_reverse) .'",'."\n";
				
				if($slider_type !== 'hero')
					echo '							onHoverStop:"'. trim($this->slider->getParam("stop_on_hover","on")).'",'."\n";
				
				$add_comma = false;
				if($touch_enabled == 'on'){
					$add_comma = true;
					echo '							touch:{'."\n";
					echo '								touchenabled:"'. trim($touch_enabled).'",'."\n";
					echo '								swipe_threshold: '. trim($swipe_velocity) .','."\n";
					echo '								swipe_min_touches: '. trim($swipe_min_touches) .','."\n";
					echo '								swipe_direction: "'. trim($swipe_direction) .'",'."\n";
					echo '								drag_block_vertical: ';
					echo ($drag_block_vertical == 'true') ? 'true' : 'false';
					echo "\n";
					echo '							}'."\n";
				}
				
				if($enable_arrows == 'on'){
					$navigation_arrow_style = $this->slider->getParam('navigation_arrow_style','round');
					$arrows_always_on = ($this->slider->getParam('arrows_always_on','true') == 'true') ? 'true' : 'false';
					$hide_arrows_on_mobile = ($this->slider->getParam('hide_arrows_on_mobile','off') == 'on') ? 'true' : 'false';
					$hide_arrows_over = ($this->slider->getParam('hide_arrows_over','off') == 'on') ? 'true' : 'false';
					$arr_tmp = '';
					
					$ff = false;
					if(!empty($all_navs)){
						foreach($all_navs as $cur_nav){
							if($cur_nav['handle'] == $navigation_arrow_style){
								if(isset($cur_nav['markup']['arrows'])) $arr_tmp = $cur_nav['markup']['arrows'];
								if(isset($cur_nav['css']['arrows'])) $nav_css .= $rs_nav->add_placeholder_modifications($cur_nav['css']['arrows'], $cur_nav['handle'], 'arrows', $cur_nav['settings'], $this->slider, $this)."\n";
								$ff = true;
								break;
							}
						}
					}
					if($ff == false){
						$navigation_arrow_style = '';
					}
					
					$navigation_arrow_style = $rs_nav->translate_navigation($navigation_arrow_style);
					if($add_comma) echo '							,'."\n";
					$add_comma = true;
					echo '							arrows: {'."\n";
					echo '								style:"'. trim($navigation_arrow_style).'",'."\n";
					echo '								enable:';
					echo ($this->slider->getParam('enable_arrows','off') == 'on') ? 'true' : 'false'; 
					echo ','."\n";
					echo ($this->slider->getParam('rtl_arrows','off') == 'on') ? '								rtl:true,'."\n" : '';
					echo '								hide_onmobile:'.$hide_arrows_on_mobile.','."\n";	
					
					if($hide_arrows_on_mobile === 'true') {
						echo '								hide_under:'. trim($this->slider->getParam('arrows_under_hidden','0',ZTSlider::FORCE_NUMERIC)).','."\n";
					}
					if($hide_arrows_over === 'true') {
						echo '								hide_over:'. trim($this->slider->getParam('arrows_over_hidden','0',ZTSlider::FORCE_NUMERIC)).','."\n";
					}
					echo '								hide_onleave:'.$arrows_always_on.','."\n";
					if($arrows_always_on === 'true'){
						echo '								hide_delay:'. trim($this->slider->getParam('hide_arrows','200',ZTSlider::FORCE_NUMERIC)).','."\n";
						echo '								hide_delay_mobile:'. trim($this->slider->getParam('hide_arrows_mobile','1200',ZTSlider::FORCE_NUMERIC)).','."\n";
					}
					echo '								tmp:\'';
					echo preg_replace( "/\r|\n/", "", $arr_tmp);
					echo '\','."\n";
					echo '								left: {'."\n";
					echo (in_array($this->slider->getParam('leftarrow_position', 'slider'),array('layergrid','grid'))) ? '									container:"layergrid",'."\n" : '';
					echo '									h_align:"'. trim($this->slider->getParam('leftarrow_align_hor','left')) .'",'."\n";
					echo '									v_align:"'. trim($this->slider->getParam('leftarrow_align_vert','center')) .'",'."\n";
					echo '									h_offset:'. trim($this->slider->getParam('leftarrow_offset_hor','20',ZTSlider::FORCE_NUMERIC)) .','."\n";
					echo '									v_offset:'. trim($this->slider->getParam('leftarrow_offset_vert','0',ZTSlider::FORCE_NUMERIC))."\n";										
					echo '								},'."\n";
					echo '								right: {'."\n";
					echo (in_array($this->slider->getParam('rightarrow_position', 'slider'),array('layergrid','grid'))) ? '									container:"layergrid",'."\n" : '';
					echo '									h_align:"'. trim($this->slider->getParam('rightarrow_align_hor','right')).'",'."\n";
					echo '									v_align:"'. trim($this->slider->getParam('rightarrow_align_vert','center')).'",'."\n";
					echo '									h_offset:'. trim($this->slider->getParam('rightarrow_offset_hor','20',ZTSlider::FORCE_NUMERIC)).','."\n";
					echo '									v_offset:'. trim($this->slider->getParam('rightarrow_offset_vert','0',ZTSlider::FORCE_NUMERIC))."\n";					
					echo '								}'."\n";
					echo '							}'."\n";
				}
				
				if($enable_bullets == 'on'){
					$navigation_bullets_style = $this->slider->getParam('navigation_bullets_style','round');
					$bullets_always_on = ($this->slider->getParam('bullets_always_on','true') == 'true') ? 'true' : 'false';
					$hide_bullets_on_mobile = ($this->slider->getParam('hide_bullets_on_mobile','off') == 'on') ? 'true' : 'false';
					$hide_bullets_over = ($this->slider->getParam('hide_bullets_over','off') == 'on') ? 'true' : 'false';
					$bul_tmp = '<span class="tp-bullet-image"></span><span class="tp-bullet-title"></span>';

					if(!empty($all_navs)){
						foreach($all_navs as $cur_nav){
							if($cur_nav['handle'] == $navigation_bullets_style){
								if(isset($cur_nav['markup']['bullets'])) $bul_tmp = $cur_nav['markup']['bullets'];
								if(isset($cur_nav['css']['bullets'])) $nav_css .= $rs_nav->add_placeholder_modifications($cur_nav['css']['bullets'], $cur_nav['handle'], 'bullets', $cur_nav['settings'], $this->slider, $this)."\n";
								break;
							}
						}
					}
					
					$navigation_bullets_style = $rs_nav->translate_navigation($navigation_bullets_style);
					
					if($add_comma) echo '							,'."\n";
					$add_comma = true;
					echo '							bullets: {'."\n";
					echo '								enable:';
					echo ($this->slider->getParam('enable_bullets','off') == 'on') ? 'true' : 'false';
					echo ','."\n";
					echo ($this->slider->getParam('rtl_bullets','off') == 'on') ? '								rtl:true,'."\n" : '';
					echo '								hide_onmobile:'.$hide_bullets_on_mobile.','."\n";
					if($hide_bullets_on_mobile === 'true'){
						echo '								hide_under:'. trim($this->slider->getParam('bullets_under_hidden','0',ZTSlider::FORCE_NUMERIC)).','."\n";
					}
					if($hide_bullets_over === 'true'){
						echo '								hide_over:'. trim($this->slider->getParam('bullets_over_hidden','0',ZTSlider::FORCE_NUMERIC)).','."\n";
					}
					echo '								style:"'. trim($navigation_bullets_style).'",'."\n";
					echo '								hide_onleave:'.$bullets_always_on.','."\n";
					if($bullets_always_on === 'true'){
						echo '								hide_delay:'. trim($this->slider->getParam('hide_bullets','200',ZTSlider::FORCE_NUMERIC)).','."\n";
						echo '								hide_delay_mobile:'. trim($this->slider->getParam('hide_bullets_mobile','1200',ZTSlider::FORCE_NUMERIC)).','."\n";
					}
					echo '								direction:"'. trim($this->slider->getParam('bullets_direction','horizontal')).'",'."\n";
					echo (in_array($this->slider->getParam('bullets_position', 'slider'),array('layergrid','grid'))) ? '									container:"layergrid",'."\n" : '';
					echo '								h_align:"'. trim($this->slider->getParam('bullets_align_hor','right')).'",'."\n";
					echo '								v_align:"'. trim($this->slider->getParam('bullets_align_vert','center')).'",'."\n";
					echo '								h_offset:'. trim($this->slider->getParam('bullets_offset_hor','20',ZTSlider::FORCE_NUMERIC)).','."\n";
					echo '								v_offset:'. trim($this->slider->getParam('bullets_offset_vert','0',ZTSlider::FORCE_NUMERIC)).','."\n";
					echo '								space:'. trim($this->slider->getParam('bullets_space','5',ZTSlider::FORCE_NUMERIC)).','."\n";					
					echo '								tmp:\'';
					echo preg_replace( "/\r|\n/", "", $bul_tmp);
					echo '\''."\n";
					echo '							}'."\n";
				}
				if($enable_thumbnails == 'on'){
					$thumbnails_style = $this->slider->getParam('thumbnails_style','round');
					$thumbs_always_on = ($this->slider->getParam('thumbs_always_on','true') == 'true') ? 'true' : 'false';
					$hide_thumbs_on_mobile = ($this->slider->getParam('hide_thumbs_on_mobile','off') == 'on') ? 'true' : 'false';
					$hide_thumbs_over = ($this->slider->getParam('hide_thumbs_over','off') == 'on') ? 'true' : 'false';
					$thumbs_tmp = '<span class="tp-thumb-image"></span><span class="tp-thumb-title"></span>';
					
					if(!empty($all_navs)){
						foreach($all_navs as $cur_nav){
							if($cur_nav['handle'] == $thumbnails_style){
								if(isset($cur_nav['markup']['thumbs'])) $thumbs_tmp = $cur_nav['markup']['thumbs'];
								if(isset($cur_nav['css']['thumbs'])) $nav_css .= $rs_nav->add_placeholder_modifications($cur_nav['css']['thumbs'], $cur_nav['handle'], 'thumbs', $cur_nav['settings'], $this->slider, $this)."\n";
								break;
							}
						}
					}
					
					$thumbnails_style = $rs_nav->translate_navigation($thumbnails_style);
					
					if($add_comma) echo '							,'."\n";
					$add_comma = true;
					echo '							thumbnails: {'."\n";
					echo '								style:"'. trim($thumbnails_style).'",'."\n";
					echo '								enable:';
					echo ($this->slider->getParam('enable_thumbnails','off') == 'on') ? 'true' : 'false';
					echo ','."\n";
					echo ($this->slider->getParam('rtl_thumbnails','off') == 'on') ? '								rtl:true,'."\n" : '';
					echo '								width:'. trim($this->slider->getParam('thumb_width','100',ZTSlider::FORCE_NUMERIC)).','."\n";
					echo '								height:'. trim($this->slider->getParam('thumb_height','50',ZTSlider::FORCE_NUMERIC)).','."\n";
					echo '								min_width:'. trim($this->slider->getParam('thumb_width_min','100',ZTSlider::FORCE_NUMERIC)).','."\n";
					echo '								wrapper_padding:'. trim($this->slider->getParam('thumbnails_padding','5',ZTSlider::FORCE_NUMERIC)).','."\n";
					echo '								wrapper_color:"'. trim($this->slider->getParam('thumbnails_wrapper_color','transparent')).'",'."\n";
					echo '								wrapper_opacity:"'. trim(round($this->slider->getParam('thumbnails_wrapper_opacity','100') / 100, 2)).'",'."\n";
					echo '								tmp:\'';
					echo preg_replace( "/\r|\n/", "", $thumbs_tmp);
					echo '\','."\n";
					echo '								visibleAmount:'. trim($this->slider->getParam('thumb_amount','5',ZTSlider::FORCE_NUMERIC)).','."\n";
					echo '								hide_onmobile:'.$hide_thumbs_on_mobile.','."\n";
					if($hide_thumbs_on_mobile === 'true'){
						echo '								hide_under:'. trim($this->slider->getParam('thumbs_under_hidden','0',ZTSlider::FORCE_NUMERIC)).','."\n";
					}
					if($hide_thumbs_over === 'true'){
						echo '								hide_over:'. trim($this->slider->getParam('thumbs_over_hidden','0',ZTSlider::FORCE_NUMERIC)).','."\n";
					}
					echo '								hide_onleave:'.$thumbs_always_on.','."\n";
					if($thumbs_always_on === 'true'){
						echo '								hide_delay:'. trim($this->slider->getParam('hide_thumbs','200',ZTSlider::FORCE_NUMERIC)).','."\n";
						echo '								hide_delay_mobile:'. trim($this->slider->getParam('hide_thumbs_mobile','1200',ZTSlider::FORCE_NUMERIC)).','."\n";
					}
					echo '								direction:"'. trim($this->slider->getParam('thumbnail_direction','horizontal')).'",'."\n";
					echo '								span:';
					echo ($this->slider->getParam('span_thumbnails_wrapper','off') == 'on') ? 'true' : 'false';
					echo ','."\n";
					echo '								position:"'. trim($this->slider->getParam('thumbnails_inner_outer','inner')).'",'."\n";
					if($this->slider->getParam('thumbnails_inner_outer','inner') == 'inner'){
						echo (in_array($this->slider->getParam('thumbnails_position', 'slider'),array('layergrid','grid'))) ? '									container:"layergrid",'."\n" : '';					
					}
					echo '								space:'. trim($this->slider->getParam('thumbnails_space','5',ZTSlider::FORCE_NUMERIC)).','."\n";
					echo '								h_align:"'. trim($this->slider->getParam('thumbnails_align_hor','left')).'",'."\n";
					echo '								v_align:"'. trim($this->slider->getParam('thumbnails_align_vert','center')).'",'."\n";
					echo '								h_offset:'. trim($this->slider->getParam('thumbnails_offset_hor','20',ZTSlider::FORCE_NUMERIC)).','."\n";
					echo '								v_offset:'. trim($this->slider->getParam('thumbnails_offset_vert','0',ZTSlider::FORCE_NUMERIC))."\n";					
					echo '							}'."\n";
				}
				if($enable_tabs == 'on'){
					$tabs_style = $this->slider->getParam('tabs_style','round');
					$tabs_always_on = ($this->slider->getParam('tabs_always_on','true') == 'true') ? 'true' : 'false';
					$hide_tabs_on_mobile = ($this->slider->getParam('hide_tabs_on_mobile','off') == 'on') ? 'true' : 'false';
					$hide_tabs_over = ($this->slider->getParam('hide_tabs_over','off') == 'on') ? 'true' : 'false';
					$tabs_tmp = '<span class="tp-thumb-image"></span>';
					
					if(!empty($all_navs)){
						foreach($all_navs as $cur_nav){
							if($cur_nav['handle'] == $tabs_style){
								if(isset($cur_nav['markup']['tabs'])) $tabs_tmp = $cur_nav['markup']['tabs'];
								if(isset($cur_nav['css']['tabs'])) $nav_css .= $rs_nav->add_placeholder_modifications($cur_nav['css']['tabs'], $cur_nav['handle'], 'tabs', $cur_nav['settings'], $this->slider, $this)."\n";
								break;
							}
						}
					}
					
					$tabs_style = $rs_nav->translate_navigation($tabs_style);
					
					if($add_comma) echo '							,'."\n";
					$add_comma = true;
					echo '							tabs: {'."\n";
					echo '								style:"'. trim($tabs_style).'",'."\n";
					echo '								enable:';
					echo ($this->slider->getParam('enable_tabs','off') == 'on') ? 'true' : 'false';
					echo ','."\n";
					echo ($this->slider->getParam('rtl_tabs','off') == 'on') ? '								rtl:true,'."\n" : '';
					echo '								width:'. trim($this->slider->getParam('tabs_width','100',ZTSlider::FORCE_NUMERIC)).','."\n";
					echo '								height:'. trim($this->slider->getParam('tabs_height','50',ZTSlider::FORCE_NUMERIC)).','."\n";
					echo '								min_width:'. trim($this->slider->getParam('tabs_width_min','100',ZTSlider::FORCE_NUMERIC)).','."\n";
					echo '								wrapper_padding:'. trim($this->slider->getParam('tabs_padding','5',ZTSlider::FORCE_NUMERIC)).','."\n";
					echo '								wrapper_color:"'. trim($this->slider->getParam('tabs_wrapper_color','transparent')).'",'."\n";
					echo '								wrapper_opacity:"'. trim(round($this->slider->getParam('tabs_wrapper_opacity','100') / 100, 2)).'",'."\n";
					echo '								tmp:\'';
					echo preg_replace( "/\r|\n/", "", $tabs_tmp);
					echo '\','."\n";
					echo '								visibleAmount: '. trim($this->slider->getParam('tabs_amount','5',ZTSlider::FORCE_NUMERIC)).','."\n";
					echo '								hide_onmobile: '.$hide_tabs_on_mobile.','."\n";
					if($hide_tabs_on_mobile === 'true'){
						echo '								hide_under:'. trim($this->slider->getParam('tabs_under_hidden','0',ZTSlider::FORCE_NUMERIC)).','."\n";
					}
					if($hide_tabs_over === 'true'){
						echo '								hide_over:'. trim($this->slider->getParam('tabs_over_hidden','0',ZTSlider::FORCE_NUMERIC)).','."\n";
					}
					echo '								hide_onleave:'.$tabs_always_on.','."\n";
					if($tabs_always_on === 'true'){
						echo '								hide_delay:'. trim($this->slider->getParam('hide_tabs','200',ZTSlider::FORCE_NUMERIC)).','."\n";
						echo '								hide_delay_mobile:'. trim($this->slider->getParam('hide_tabs_mobile','1200',ZTSlider::FORCE_NUMERIC)).','."\n";
					}
					echo '								hide_delay:'. trim($this->slider->getParam('hide_tabs','200',ZTSlider::FORCE_NUMERIC)).','."\n";
					echo '								direction:"'. trim($this->slider->getParam('tabs_direction','horizontal')).'",'."\n";
					echo '								span:';
					echo ($this->slider->getParam('span_tabs_wrapper','off') == 'on') ? 'true' : 'false';
					echo ','."\n";
					echo '								position:"'. trim($this->slider->getParam('tabs_inner_outer','inner')).'",'."\n";
					if($this->slider->getParam('tabs_inner_outer','inner') == 'inner'){
						echo (in_array($this->slider->getParam('tabs_position', 'slider'),array('layergrid','grid'))) ? '									container:"layergrid",'."\n" : '';					
					}
					echo '								space:'. trim($this->slider->getParam('tabs_space','5',ZTSlider::FORCE_NUMERIC)).','."\n";
					echo '								h_align:"'. trim($this->slider->getParam('tabs_align_hor','left')).'",'."\n";
					echo '								v_align:"'. trim($this->slider->getParam('tabs_align_vert','center')).'",'."\n";
					echo '								h_offset:'. trim($this->slider->getParam('tabs_offset_hor','20',ZTSlider::FORCE_NUMERIC)).','."\n";
					echo '								v_offset:'. trim($this->slider->getParam('tabs_offset_vert','0',ZTSlider::FORCE_NUMERIC))."\n";											
					echo '							}'."\n";
				}
				echo '						},'."\n";
			}else{ //maybe write navigation stuff still here
				echo '						navigation: {'."\n";
				if($slider_type !== 'hero'){
					echo '							onHoverStop:"'. trim($this->slider->getParam("stop_on_hover","on")).'",'."\n";
				}
				echo '						},'."\n";
			}
			
			if($slider_type == 'carousel'){
				$car_inf = $this->slider->getParam('carousel_infinity','off');
				$car_space = $this->slider->getParam('carousel_space',0,ZTSlider::FORCE_NUMERIC);
				$car_stretch = $this->slider->getParam('carousel_stretch','off');
				$car_maxitems = $this->slider->getParam('carousel_maxitems',5,ZTSlider::FORCE_NUMERIC);
				$car_fadeout = $this->slider->getParam('carousel_fadeout','on');
				$car_varyfade = $this->slider->getParam('carousel_varyfade','off');
				
				$car_hpos = $this->slider->getParam('carousel_hposition','center');
				$car_vpos = $this->slider->getParam('carousel_vposition','center');
				
				$carousel_rotation = $this->slider->getParam('carousel_rotation','off');
				$car_maxrotation = $this->slider->getParam('carousel_maxrotation',90,ZTSlider::FORCE_NUMERIC);
				$car_varyrotate = $this->slider->getParam('carousel_varyrotate','off');
				
				$carousel_scale = $this->slider->getParam('carousel_scale','off');
				$car_varyscale = $this->slider->getParam('carousel_varyscale','off');
				$car_scaledown = $this->slider->getParam('carousel_scaledown',50,ZTSlider::FORCE_NUMERIC);
				if($car_scaledown > 100) $car_scaledown = 100;
				
				$car_borderr = $this->slider->getParam('carousel_borderr',0,ZTSlider::FORCE_NUMERIC);
				$car_borderr_unit = $this->slider->getParam('carousel_borderr_unit','px');
				
				$car_padding_top = $this->slider->getParam('carousel_padding_top',0,ZTSlider::FORCE_NUMERIC);
				$car_padding_bottom = $this->slider->getParam('carousel_padding_bottom',0,ZTSlider::FORCE_NUMERIC);
				
				echo '						carousel: {'."\n";
				if($car_borderr > 0){
					echo '							border_radius: "'. trim($car_borderr.$car_borderr_unit) .'",'."\n";
				}
				if($car_padding_top > 0){
					echo '							padding_top: "'. trim($car_padding_top) .'",'."\n";
				}
				if($car_padding_bottom > 0){
					echo '							padding_bottom: "'. trim($car_padding_bottom) .'",'."\n";
				}
				if($carousel_rotation == 'on'){
					echo '							maxRotation: '. trim($car_maxrotation).','."\n";
					echo '							vary_rotation: "'. trim($car_varyrotate).'",'."\n";
				}
				if($carousel_scale == 'on'){
					echo '							minScale: '. trim($car_scaledown).','."\n";
					echo '							vary_scale: "'. trim($car_varyscale).'",'."\n";
				}
				echo '							horizontal_align: "'. trim($car_hpos).'",'."\n";
				echo '							vertical_align: "'. trim($car_vpos).'",'."\n";
				echo '							fadeout: "'. trim($car_fadeout).'",'."\n";
				if($car_fadeout == 'on'){
					echo '							vary_fade: "'. trim($car_varyfade).'",'."\n";
				}
				echo '							maxVisibleItems: '. trim($car_maxitems).','."\n";
				echo '							infinity: "'. trim($car_inf).'",'."\n";
				echo '							space: '. trim($car_space).','."\n";
				echo '							stretch: "'. trim($car_stretch).'"'."\n";
				echo '						},'."\n";
			}
			
			$label_viewport = $this->slider->getParam('label_viewport', 'off');
			$viewport_start = $this->slider->getParam('viewport_start', 'wait');
			$viewport_area = $this->slider->getParam('viewport_area', 80, ZTSlider::FORCE_NUMERIC);
			if($label_viewport === 'on'){
				echo '						viewPort: {'."\n";
				echo '							enable:true,'."\n";
				echo '							outof:"'.trim($viewport_start).'",'."\n";
				echo '							visible_area:"'.trim($viewport_area).'%"'."\n";
				echo '						},'."\n";
			}
			
			if(isset($csizes['level']) && !empty($csizes['level'])){
				echo '						responsiveLevels:['. $csizes['level'] .'],'."\n";
				echo '						visibilityLevels:['. $csizes['level'] .'],'."\n";
				echo '						gridwidth:['. $csizes['width'] .'],'."\n";
				echo '						gridheight:['. $csizes['height'] .'],'."\n";
			}else{
				echo '						visibilityLevels:['. $csizes['visibilitylevel'] .'],'."\n";
				echo '						gridwidth:'. $csizes['width'].','."\n";
				echo '						gridheight:'. $csizes['height'].','."\n";
			}
			
			$lazyLoad = $this->slider->getParam('lazy_load_type', false);
			if($lazyLoad === false){ //do fallback checks to removed lazy_load value since version 5.0 and replaced with an enhanced version
				$old_ll = $this->slider->getParam('lazy_load', 'off');
				$lazyLoad = ($old_ll == 'on') ? 'all' : 'none';
			}
			echo '						lazyType:"'. trim($lazyLoad).'",'."\n";
			
			$minHeight = ($this->slider->getParam('slider_type') !== 'fullscreen') ? $this->slider->getParam('min_height', '0') : $this->slider->getParam('fullscreen_min_height', '0');
			if($minHeight > 0){
				echo '						minHeight:"'. $minHeight.'",'."\n";
			}
			
			if($use_parallax == 'on'){
				echo '						parallax: {'."\n";
				echo '							type:"'. trim($parallax_type) .'",'."\n";
				echo '							origo:"'. trim($parallax_origo) .'",'."\n";
				echo '							speed:'. trim($parallax_speed) .','."\n";
				echo '							levels:['. trim($parallax_level) .'],'."\n";
				echo '							type:"'. trim($parallax_type) .'",'."\n";
				if ($parallax_type == '3D') {
					echo '							ddd_shadow:"'. trim($parallax_ddd_shadow) .'",'."\n";
					echo '							ddd_bgfreeze:"'. trim($parallax_ddd_bgfreeze) .'",'."\n";
					echo '							ddd_overflow:"'. trim($parallax_ddd_overflow) .'",'."\n";
					echo '							ddd_layer_overflow:"'. trim($parallax_ddd_layer_overflow) .'",'."\n";
					echo '							ddd_z_correction:'. trim($parallax_ddd_zcorrection) .','."\n";
					//echo '							ddd_path:"'. trim($parallax_ddd_path) .'",'."\n";
				}				

				if($disable_parallax_mobile == 'on'){
					echo '							disable_onmobile:"on"'."\n";
				}
				echo '						},'."\n";
			}
			
			if ($use_parallax != 'on' || ($use_parallax == 'on' && $parallax_type !='3D'))
				echo '						shadow:'. trim($this->slider->getParam("shadow_type","2")) .','."\n";
			
			if($use_spinner == '-1'){
				echo '						spinner:"off",'."\n";
			}else{
				echo '						spinner:"spinner'. trim($use_spinner).'",'."\n";
			}
			
			if($slider_type !== 'hero'){
				echo '						stopLoop:"'. trim($stopSlider) .'",'."\n";
				echo '						stopAfterLoops:'. trim($stopAfterLoops) .','."\n";
				echo '						stopAtSlide:'. trim($stopAtSlide) .','."\n";
				echo '						shuffle:"'. trim($this->slider->getParam("shuffle","off")) .'",'."\n";
			}

			echo '						autoHeight:"'. trim($this->slider->getParam("auto_height", 'off')). '",'."\n";
			
			if($this->slider->getParam("slider_type") == "fullscreen"){				
				echo '						fullScreenAutoWidth:"'. trim($this->slider->getParam("autowidth_force","off")) .'",'."\n";
				echo '						fullScreenAlignForce:"'. trim($this->slider->getParam("full_screen_align_force","off")) .'",'."\n";
				echo '						fullScreenOffsetContainer: "'. trim($this->slider->getParam("fullscreen_offset_container","")) .'",'."\n";
				echo '						fullScreenOffset: "'. trim($this->slider->getParam("fullscreen_offset_size","")) .'",'."\n";
			}
			if($enable_progressbar !== 'on' || $slider_type == 'hero'){
				echo '						disableProgressBar:"on",'."\n";
			}
			echo '						hideThumbsOnMobile:"'. trim($hideThumbsOnMobile) .'",'."\n";
			echo '						hideSliderAtLimit:'. trim($hideSliderAtLimit) .','."\n";
			echo '						hideCaptionAtLimit:'. trim($hideCaptionAtLimit) .','."\n";
			echo '						hideAllCaptionAtLilmit:'. trim($hideAllCaptionAtLimit) .','."\n";
			
			if($slider_type !== 'hero'){
				$start_with_slide_enable = $this->slider->getParam('start_with_slide_enable', 'off');
				if($start_with_slide_enable == 'on'){
					echo '						startWithSlide:'. trim($startWithSlide).','."\n";
				}
			}
			
			echo '						debugMode:'.$debugmode.','."\n";
			if($this->slider->getParam('waitforinit', 'off') == 'on'){
				echo '						waitForInit:true,'."\n";
			}
			echo '						fallbacks: {'."\n";
			
			if($this->slider->getParam('ignore_height_changes', 'off') !== 'off'){
				echo '							ignoreHeightChanges:"'. trim($this->slider->getParam('ignore_height_changes', 'off')).'",'."\n";
				echo '							ignoreHeightChangesSize:'. intval(trim($this->slider->getParam('ignore_height_changes_px', '0'))).','."\n";
			}
			
			echo '							simplifyAll:"'. trim($this->slider->getParam('simplify_ie8_ios4', 'off')).'",'."\n";
			
			if($slider_type !== 'hero')
				echo '							nextSlideOnWindowFocus:"'. trim($this->slider->getParam('next_slide_on_window_focus', 'off')).'",'."\n";
			
			$dfl = ($this->slider->getParam('disable_focus_listener', 'off') == 'on') ? 'true' : 'false';
			echo '							disableFocusListener:'.$dfl.','."\n";
			
			if($disableKenBurnOnMobile == 'on'){
				echo '						panZoomDisableOnMobile:"on",'."\n";
			}
			echo '						}'."\n";
			
			echo '					});'."\n";

			if($this->slider->getParam("custom_javascript", '') !== ''){
				echo stripslashes($this->slider->getParam("custom_javascript", ''));
			}
			echo '				}'."\n";
//
//			do_action('revslider_fe_javascript_output', $this->slider, $this->sliderHtmlID);
			echo '			});	/*ready*/'."\n";
			?>
		</script>
		<?php
		if($js_to_footer && $this->previewMode == false && $markup_export == false){
			$js_content = ob_get_contents();
			ob_clean();
			ob_end_clean();

			$this->rev_inline_js = $js_content;

			$this->add_inline_js();
		}
		
		if($markup_export === true){
			echo '<!-- /SCRIPT -->';
		}
		
		switch($use_spinner){
			case '1':
			case '2':
				if(!JFactory::getApplication()->isAdmin()){?><script>
					var htmlDivCss = ' #<?php echo $this->sliderHtmlID_wrapper;?> .tp-loader.spinner<?php echo $use_spinner;?>{ background-color: <?php echo $spinner_color;?> !important; } ';
					var htmlDiv = document.getElementById('rs-plugin-settings-inline-css');
					if(htmlDiv) {
						htmlDiv.innerHTML = htmlDiv.innerHTML + htmlDivCss;
					}
					else{
						var htmlDiv = document.createElement('div');
						htmlDiv.innerHTML = '<style>' + htmlDivCss + '</style>';
						document.getElementsByTagName('head')[0].appendChild(htmlDiv.childNodes[0]);
					}
					</script>
					<?php 
				}else{
					if($markup_export === true){
						echo '<!-- STYLE -->';
					}
					echo '<style type="text/css">	#'.$this->sliderHtmlID_wrapper.' .tp-loader.spinner'.$use_spinner.'{ background-color: '.$spinner_color.' !important; } </style>';
					if($markup_export === true){
						echo '<!-- /STYLE -->';
					}
				}
			break;
			case '3':
			case '4':
				if(!JFactory::getApplication()->isAdmin()){?><script>
					var htmlDivCss = '	#<?php echo $this->sliderHtmlID_wrapper;?> .tp-loader.spinner<?php echo $use_spinner;?> div { background-color: <?php echo $spinner_color;?> !important; } ';
					var htmlDiv = document.getElementById('rs-plugin-settings-inline-css');
					if(htmlDiv) {
						htmlDiv.innerHTML = htmlDiv.innerHTML + htmlDivCss;
					}
					else{
						var htmlDiv = document.createElement('div');
						htmlDiv.innerHTML = '<style>' + htmlDivCss + '</style>';
						document.getElementsByTagName('head')[0].appendChild(htmlDiv.childNodes[0]);
					}
					</script>
					<?php
				}else{
					if($markup_export === true){
						echo '<!-- STYLE -->';
					}
					echo '<style type="text/css">	#'.$this->sliderHtmlID_wrapper.' .tp-loader.spinner'.$use_spinner.'{ background-color: '.$spinner_color.' !important; } </style>';
					if($markup_export === true){
						echo '<!-- /STYLE -->';
					}
				}
			break;
			case '0':
			case '5':
			default:
			break;

		}

		if($this->slider->getParam("custom_css", '') !== ''){
			if(!JFactory::getApplication()->isAdmin()){
				?><script>
					var htmlDivCss = unescape("<?php echo ZTSliderCssParser::compress_css(rawurlencode(stripslashes($this->slider->getParam('custom_css', ''))));?>");
					var htmlDiv = document.getElementById('rs-plugin-settings-inline-css');
					if(htmlDiv) {
						htmlDiv.innerHTML = htmlDiv.innerHTML + htmlDivCss;
					}
					else{
						var htmlDiv = document.createElement('div');
						htmlDiv.innerHTML = '<style>' + htmlDivCss + '</style>';
						document.getElementsByTagName('head')[0].appendChild(htmlDiv.childNodes[0]);
					}
				  </script><?php
			}else{
				if($markup_export === true){
					echo '<!-- STYLE -->';
				}
				?><style type="text/css"><?php echo ZTSliderCssParser::compress_css(stripslashes($this->slider->getParam('custom_css', '')));?></style><?php
				if($markup_export === true){
					echo '<!-- /STYLE -->';
				}
			}
		}
		
		
		//add custom Slide CSS here
		
		if(trim($nav_css) !== ''){
			if(!JFactory::getApplication()->isAdmin()){
				?><script>
					var htmlDivCss = unescape("<?php echo ZTSliderCssParser::compress_css(rawurlencode($nav_css));?>");
					var htmlDiv = document.getElementById('rs-plugin-settings-inline-css');
					if(htmlDiv) {
						htmlDiv.innerHTML = htmlDiv.innerHTML + htmlDivCss;
					}
					else{
						var htmlDiv = document.createElement('div');
						htmlDiv.innerHTML = '<style>' + htmlDivCss + '</style>';
						document.getElementsByTagName('head')[0].appendChild(htmlDiv.childNodes[0]);
					}
				  </script>
				<?php
			}else{
				if($markup_export === true){
					echo '<!-- STYLE -->';
				}
				?>
				<style type="text/css"><?php echo ZTSliderCssParser::compress_css(($nav_css));?></style>
				<?php
				if($markup_export === true){
					echo '<!-- /STYLE -->';
				}
			}
		}
		if(!$markup_export){ //not needed for html markup export
			echo $this->add_inline_double_jquery_error();
		}
	}

	/**
	 * Output Inline JS
	 */
	public function add_inline_js(){

		echo $this->rev_inline_js;

	}
	
	/**
	 * Output revslider_showDoubleJqueryError
	 */
	public function add_inline_double_jquery_error(){
		return '
		<script type="text/javascript">
			function revslider_showDoubleJqueryError(sliderID) {
				var errorMessage = "Revolution Slider Error: You have some jquery.js library include that comes after the revolution files js include.";
				errorMessage += "<br> This includes make eliminates the revolution slider libraries, and make it not work.";
				errorMessage += "<br><br> To fix it you can:<br>&nbsp;&nbsp;&nbsp; 1. In the Slider Settings -> Troubleshooting set option:  <strong><b>Put JS Includes To Body</b></strong> option to true.";
				errorMessage += "<br>&nbsp;&nbsp;&nbsp; 2. Find the double jquery.js include and remove it.";
				errorMessage = "<span style=\'font-size:16px;color:#BC0C06;\'>" + errorMessage + "</span>";
					jQuery(sliderID).show().html(errorMessage);
			}
		</script>';
	}


	/**
	 * Output Dynamic Inline Styles
	 */
	public function add_inline_styles(){
	
		if(!JFactory::getApplication()->isAdmin()){
			echo '<script>var htmlDiv = document.getElementById("rs-plugin-settings-inline-css"); var htmlDivCss="';
		} 
		else echo "<style>";

		$this->db->setQuery('SELECT * FROM #__zt_layerslider_css');

		$styles = $this->db->loadAssocList();

		foreach($styles as $key => $style){
			$handle = str_replace('.tp-caption', '', $style['handle']);
			if(!isset($this->class_include[$handle])) unset($styles[$key]);
		}
		
		$styles = ZTSliderCssParser::parseDbArrayToCss($styles, "\n");
		$styles = ZTSliderCssParser::compress_css($styles);
		
		if(!JFactory::getApplication()->isAdmin()){
			echo addslashes($styles).'";
				if(htmlDiv) {
					htmlDiv.innerHTML = htmlDiv.innerHTML + htmlDivCss;
				}else{
					var htmlDiv = document.createElement("div");
					htmlDiv.innerHTML = "<style>" + htmlDivCss + "</style>";
					document.getElementsByTagName("head")[0].appendChild(htmlDiv.childNodes[0]);
				}
			</script>'."\n";
		} 
		else echo $styles.'</style>';

	}

	
	/**
	 * put inline error message in a box.
	 */
	public function putErrorMessage($message){
		?>
		<div style="width:800px;height:300px;margin-bottom:10px;margin:0px auto;">
			<div style="margin: auto; line-height: 40px; font-size: 14px; color: #FFF; padding: 15px; background: #E74C3C; margin: 20px 0px;">
				<?php echo JText::_("Revolution Slider Error",'revslider'); ?>: <?php echo $message; ?>
			</div>
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery(".rev_slider").show();
			});
		</script>
		<?php
	}


	/**
	 *
	 * modify slider settings for preview mode
	 */
	private function modifyPreviewModeSettings(){
		$params = $this->slider->getParams();
		$params["js_to_body"] = "false";

		$this->slider->setParams($params);
	}
	
	
	/**
	 * modify slider settings through the shortcode directly
	 */
	private function modify_settings($settings){
		$params = $this->slider->getParams();
		
		foreach($settings as $name => $setting){
			$params[$name] = $setting;
		}
		
		$this->slider->setParams($params);
	}


	/**
	 *
	 * put html slider on the html page.
	 * @param $data - mixed, can be ID ot Alias.
	 */
	public function putSliderBase($sliderID, $gal_ids = array(), $markup_export = false, $settings = array(), $order = array()){
		
		$this->markup_export = $markup_export;
		
		try{
			$slver = '2.0';
			
			self::$sliderSerial++;
			
			$this->slider = new ZTSlider();
			if($sliderID !== '-99'){
				$this->slider->initByMixed($sliderID);
			}else{ //do default
				$this->slider->initByMixed($sliderID);
			}
			
			//modify settings if there are any special settings given through the shortcode
			if(!empty($settings))
				$this->modify_settings($settings);
			
			//modify settings for admin preview mode
			if($this->previewMode == true)
				$this->modifyPreviewModeSettings();

			//set slider language

			//edit html before slider
			$htmlBeforeSlider = "";
			
			if($markup_export === true){
				$htmlBeforeSlider .= '<!-- FONT -->';
			}
			if($this->slider->getParam("load_googlefont","false") == "true"){
				$googleFont = $this->slider->getParam("google_font");
				if(is_array($googleFont)){
					foreach($googleFont as $key => $font){
						
					}
				}else{
					$htmlBeforeSlider .= ZTSliderOperations::getCleanFontImport($googleFont);
				}
			}
			
			$gfonts = $this->slider->getParam("google_font",array());
			if(!empty($gfonts) && is_array($gfonts)){
				foreach($gfonts as $gf){
					$gf = str_replace(array('"', '+'), array('', ' '), $gf);
					$htmlBeforeSlider .= ZTSliderOperations::getCleanFontImport($gf);
				}
			}
			
			//add all google fonts of layers
			$gfsubsets = $this->slider->getParam("subsets",array());
			$gf = $this->slider->getUsedFonts(false);
			
			foreach($gf as $gfk => $gfv){
				$tcf = $gfk.':';
				if(!empty($gfv['variants'])){
					$mgfirst = true;
					foreach($gfv['variants'] as $mgvk => $mgvv){
						if(!$mgfirst) $tcf .= ',';
						$tcf .= $mgvk;
						$mgfirst = false;
					}
				}
				
				if(!empty($gfv['subsets'])){
					
					$mgfirst = true;
					foreach($gfv['subsets'] as $ssk => $ssv){
						if(array_search(trim($gfk.'+'.$ssv), $gfsubsets) !== false){
							if($mgfirst) $tcf .= '&subset=';
							if(!$mgfirst) $tcf .= ',';
							$tcf .= $ssv;
							$mgfirst = false;
						}
					}
				}
				
				$htmlBeforeSlider .= ZTSliderOperations::getCleanFontImport($tcf);
			}
			
			if($markup_export === true){
				$htmlBeforeSlider .= '<!-- /FONT -->';
			}

			//pub js to body handle
			if($this->slider->getParam("js_to_body","false") == "true"){
				$operations = new ZTSliderOperations();
				$arrValues = $operations->getGeneralSettingsValues();
				$enable_logs = ZTSliderFunctions::getVal($arrValues, "enable_logs",'off');
				
				if($markup_export === true){
					$htmlBeforeSlider .= '<!-- SCRIPTINCLUDE -->';
				}
				
				if($enable_logs == 'on'){
					$urlIncludeJS = ZT_ASSETS_URL.'/assets/js/jquery.themepunch.enablelog.js?rev='. $slver;
					$htmlBeforeSlider .= '<script type="text/javascript" src="'.$urlIncludeJS.'"></script>';
				}

				$urlIncludeJS = ZT_ASSETS_URL.'/assets/js/jquery.themepunch.tools.min.js?rev='. $slver;
				$htmlBeforeSlider .= '<script type="text/javascript" src="'.$urlIncludeJS.'"></script>';
				$urlIncludeJS = ZT_ASSETS_URL.'/assets/js/jquery.themepunch.revolution.min.js?rev='. $slver;
				$htmlBeforeSlider .= '<script type="text/javascript" src="'.$urlIncludeJS.'"></script>';
				
				if($markup_export === true){
					$htmlBeforeSlider .= '<!-- /SCRIPTINCLUDE -->';
				}
			}

			//the initial id can be alias
			$sliderID = $this->slider->getID();

			$bannerWidth = $this->slider->getParam("width",null,ZTSlider::VALIDATE_NUMERIC,"Slider Width");
			$bannerHeight = $this->slider->getParam("height",null,ZTSlider::VALIDATE_NUMERIC,"Slider Height");

			$sliderType = $this->slider->getParam("slider_type");
			$slider_type = $this->slider->getParam("slider-type");

			//set wrapper height
			$wrapperHeigh = 0;
			$wrapperHeigh += $this->slider->getParam("height");

			//add thumb height
			if($this->slider->getParam('enable_thumbnails', 'off') == 'on'){
				$wrapperHeigh += $this->slider->getParam('thumb_height');
			}
			
			$slider_id = $this->slider->getParam('slider_id', '');
			
			if(trim($slider_id) !== ''){
				$this->sliderHtmlID = $slider_id;
			}else{
				$this->sliderHtmlID = 'rev_slider_'.$sliderID.'_'.self::$sliderSerial;
			}
			$this->sliderHtmlID_wrapper = $this->sliderHtmlID.'_wrapper';
			$containerStyle = "";

			$sliderPosition = $this->slider->getParam("position","center");
			
			$do_overflow = '';
			
			//set position:
			if($sliderType != "fullscreen"){

				switch($sliderPosition){
					case "center":
					default:
						$containerStyle .= "margin:0px auto;";
					break;
					case "left":
						$containerStyle .= "float:left;";
					break;
					case "right":
						$containerStyle .= "float:right;";
					break;
				}
				
				if($this->slider->getParam('main_overflow_hidden','on') == 'on'){
					$do_overflow = ' tp-overflow-hidden';
				}

			}

			//add background color
			$backgroundColor = trim($this->slider->getParam('background_color'));
			if(!empty($backgroundColor))
				$containerStyle .= 'background-color:'.$backgroundColor.';';

			//set padding
			$containerStyle .= 'padding:'.trim($this->slider->getParam('padding','0')).'px;';

			//set margin:
			if($sliderType != 'fullscreen'){

				if($sliderPosition != 'center'){
					$containerStyle .= 'margin-left:'.trim($this->slider->getParam('margin_left', '0', ZTSlider::FORCE_NUMERIC)).'px;';
					$containerStyle .= 'margin-right:'.trim($this->slider->getParam('margin_right', '0', ZTSlider::FORCE_NUMERIC)).'px;';
				}

				$containerStyle .= 'margin-top:'.trim($this->slider->getParam('margin_top', '0', ZTSlider::FORCE_NUMERIC)).'px;';
				$containerStyle .= 'margin-bottom:'.trim($this->slider->getParam('margin_bottom', '0', ZTSlider::FORCE_NUMERIC)).'px;';
			}

			//set height and width:
			$bannerStyle = 'display:none;';

			//add background image (to banner style)
			$showBackgroundImage = $this->slider->getParam('show_background_image', 'off');

			if($showBackgroundImage == 'true' || $showBackgroundImage == 'on'){
				$backgroundImage = trim($this->slider->getParam('background_image'));
				$backgroundFit = trim($this->slider->getParam('bg_fit', $this->slider->getParam('def-background_fit', 'cover')));
				$backgroundRepeat = trim($this->slider->getParam('bg_repeat', $this->slider->getParam('def-bg_repeat', 'no-repeat')));
				$backgroundPosition = trim($this->slider->getParam('bg_position', $this->slider->getParam('def-bg_position', 'center center')));
				
				if(!empty($backgroundImage))
					$containerStyle .= "background-image:url(".$backgroundImage.");background-repeat:".$backgroundRepeat.";background-size:".$backgroundFit.";background-position:".$backgroundPosition.";";
			}

			//set wrapper and slider class:
			$sliderWrapperClass = "rev_slider_wrapper";
			$sliderClass = "rev_slider";

			switch($sliderType){
				case "responsitive": //@since 5.0: obsolete now, was custom
				case "fixed":        //@since 5.0: obsolete now
				case 'auto':
				case 'fullwidth':
					$sliderWrapperClass .= " fullwidthbanner-container";
					$sliderClass .= " fullwidthabanner";
					// KRISZTIAN REMOVED SOME LINE
					//$bannerStyle .= "max-height:".$bannerHeight."px;height:".$bannerHeight."px;";
					//$containerStyle .= "max-height:".$bannerHeight."px;";
				break;
				case 'fullscreen':
					$sliderWrapperClass .= " fullscreen-container";
					$sliderClass .= " fullscreenbanner";
				break;
				default:
					$bannerStyle .= "height:".$bannerHeight."px;width:".$bannerWidth."px;";
					$containerStyle .= "height:".$bannerHeight."px;width:".$bannerWidth."px;";
				break;
			}
			
			$maxWidth = $this->slider->getParam('max_width', '0', ZTSlider::FORCE_NUMERIC);
			if($maxWidth > 0 && $this->slider->getParam('slider_type') == 'auto'){
				$containerStyle.='max-width:'. $maxWidth.'px;';
			}

			$htmlTimerBar = "";

			$enable_progressbar =  $this->slider->getParam('enable_progressbar','on');
			$timerBar =  $this->slider->getParam('show_timerbar','top');
			$progress_height =  $this->slider->getParam('progress_height','5');
			$progress_opa =  $this->slider->getParam('progress_opa','15');
			$progressbar_color =  $this->slider->getParam('progressbar_color','#000000');
			
			if($enable_progressbar !== 'on' || $slider_type == 'hero')
				$timerBar = 'hide';
			
			$progress_style = ' style="height: '.trim($progress_height).'px; background-color: '.ZTSliderFunctions::hex2rgba($progressbar_color, $progress_opa).';"';
			
			switch($timerBar){
				case "top":
					$htmlTimerBar = '<div class="tp-bannertimer"'.$progress_style.'></div>';
				break;
				case "bottom":
					$htmlTimerBar = '<div class="tp-bannertimer tp-bottom"'.$progress_style.'></div>';
				break;
				case "hide":
					$htmlTimerBar = '<div class="tp-bannertimer tp-bottom" style="visibility: hidden !important;"></div>';
				break;
			}
			

			//check inner / outer border
			$paddingType = $this->slider->getParam("padding_type","outer");
			if($paddingType == "inner")
				$sliderWrapperClass .= " tp_inner_padding";

			global $revSliderVersion;

			$add_alias = '';
			if(JFactory::getApplication()->isAdmin()){
				$add_alias = ' data-alias="'.trim($this->slider->getAlias()).'"';
			}
			
			echo $htmlBeforeSlider."\n";
			echo '<div id="'.$this->sliderHtmlID_wrapper.'" class="'. $sliderWrapperClass .'"'.$add_alias;
			
			$show_alternate = $this->slider->getParam("show_alternative_type","off");
			if($show_alternate !== 'off'){
				$show_alternate_image = $this->slider->getParam("show_alternate_image","");
				echo ' data-aimg="'.$show_alternate_image.'" ';
				if($show_alternate == 'mobile' || $show_alternate == 'mobile-ie8'){
					echo ' data-amobile="enabled" ';
				}else{
					echo ' data-amobile="disabled" ';
				}
				if($show_alternate == 'mobile-ie8' || $show_alternate == 'ie8'){
					echo ' data-aie8="enabled" ';
				}else{
					echo ' data-aie8="disabled" ';
				}
				
			}
			
			echo ' style="'. $containerStyle .'">'."\n";

			echo '<!-- START REVOLUTION SLIDER '. $revSliderVersion .' '. $sliderType .' mode -->'."\n";

			echo '	<div id="'. $this->sliderHtmlID .'"';
			echo ' class="'. $sliderClass . $do_overflow .'"';
			echo ' style="'. $bannerStyle .'"';
			echo ' data-version="'.$revSliderVersion.'">'."\n";

			echo $this->putSlides($gal_ids, $order);
			echo $htmlTimerBar;
			echo '	</div>'."\n";
			
			$this->putJS($markup_export);
			
			echo '</div>';
			echo '<!-- END REVOLUTION SLIDER -->';
		}catch(Exception $e){
			$message = $e->getMessage();
			$this->putErrorMessage($message);
		}

	}
	
	public function add_custom_navigation_css($slides){
		$rs_nav = new ZTSliderNavigation();
		$all_navs = $rs_nav->get_all_navigations();
		$slider_type = $this->slider->getParam('slider-type');
		
		$enable_arrows = $this->slider->getParam('enable_arrows','off');
		$enable_bullets = $this->slider->getParam('enable_bullets','off');
		$enable_tabs = $this->slider->getParam('enable_tabs','off');
		$enable_thumbnails = $this->slider->getParam('enable_thumbnails','off');
		
		if($slider_type !== 'hero' && ($enable_arrows == 'on' || $enable_bullets == 'on' || $enable_tabs == 'on' || $enable_thumbnails == 'on')){
			if(!empty($slides)){
				foreach($slides as $slide){
					
					$navigation_arrow_style = $this->slider->getParam('navigation_arrow_style','round');
					$navigation_bullets_style = $this->slider->getParam('navigation_bullets_style','round');
					$tabs_style = $this->slider->getParam('tabs_style','round');
					$thumbnails_style = $this->slider->getParam('thumbnails_style','round');
					
					if(!empty($all_navs)){
						foreach($all_navs as $cur_nav){
							//get modifications out, wrap the class with slide class to be specific
							
							if($enable_arrows == 'on' && $cur_nav['handle'] == $navigation_arrow_style){
								$this->rev_custom_navigation_css .= $rs_nav->add_placeholder_sub_modifications($cur_nav['css']['arrows'], $cur_nav['handle'], 'arrows', $cur_nav['settings'], $slide, $this)."\n";
							}
							if($enable_bullets == 'on' && $cur_nav['handle'] == $navigation_bullets_style){
								$this->rev_custom_navigation_css .= $rs_nav->add_placeholder_sub_modifications($cur_nav['css']['bullets'], $cur_nav['handle'], 'bullets', $cur_nav['settings'], $slide, $this)."\n";
							}
							if($enable_tabs == 'on' && $cur_nav['handle'] == $tabs_style){
								$this->rev_custom_navigation_css .= $rs_nav->add_placeholder_sub_modifications($cur_nav['css']['tabs'], $cur_nav['handle'], 'tabs', $cur_nav['settings'], $slide, $this)."\n";
							}
							if($enable_thumbnails == 'on' && $cur_nav['handle'] == $thumbnails_style){
								$this->rev_custom_navigation_css .= $rs_nav->add_placeholder_sub_modifications($cur_nav['css']['thumbs'], $cur_nav['handle'], 'thumbs', $cur_nav['settings'], $slide, $this)."\n";
							}
						}
					}
				}
				
				if($this->markup_export === true){
					echo '<!-- STYLE -->';
				}
				if(!JFactory::getApplication()->isAdmin()){
					echo '<script>var htmlDiv = document.getElementById("rs-plugin-settings-inline-css"); var htmlDivCss="';
				} 
				else echo "<style>";

				if(!JFactory::getApplication()->isAdmin()){
					echo addslashes(ZTSliderCssParser::compress_css($this->rev_custom_navigation_css)).'";
						if(htmlDiv) {
							htmlDiv.innerHTML = htmlDiv.innerHTML + htmlDivCss;
						}else{
							var htmlDiv = document.createElement("div");
							htmlDiv.innerHTML = "<style>" + htmlDivCss + "</style>";
							document.getElementsByTagName("head")[0].appendChild(htmlDiv.childNodes[0]);
						}
					</script>'."\n";
				} 
				else echo $this->rev_custom_navigation_css.'</style>';
				
				if($this->markup_export === true){
					echo '<!-- /STYLE -->';
				}
			}
		}
	}

}
?>