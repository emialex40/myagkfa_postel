<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2015 ThemePunch
 */

if( !defined( '_JEXEC') ) exit();

class ZTSliderSlide{
	
	private $id;
	private $sliderID;
	private $slideOrder;
	
	private $imageUrl;
	private $imageID;
	private $imageThumb;
	private $imageFilepath;
	private $imageFilename;
	
	private $params;
	private $arrLayers;
	private $settings;
	private $arrChildren = null;
	private $slider;
	
	private $static_slide = false;
	
	private $postData;
	public $templateID;
	
	public function __construct(){
		$this->db = JFactory::getDbo();
	}
	
	
	/**
	 * 
	 * init slide by db record
	 */
	public function initByData($record){
		
		$this->id = $record["id"];
		$this->sliderID = $record["slider_id"];
		$this->slideOrder = (isset($record["ordering"])) ? $record["ordering"] : '';
		
		$params = $record["attribs"];
		$params = (array)json_decode($params);
		
		$layers = $record["layers"];
		$layers = (array)json_decode($layers);
		$layers = ZTSliderFunctions::convertStdClassToArray($layers);
		
		$settings = $record["settings"];
		$settings = (array)json_decode($settings);
		
		//$layers = $this->translateLayerSizes($layers);
		
//		$imageID = ZTSliderFunctions::getVal($params, "image_id");
		
		$imgResolution = ZTSliderFunctions::getVal($params, 'image_source_type', 'full');
		
		//get image url and thumb url
//		if(!empty($imageID)){
//			$this->imageID = $imageID;
//
//			$imageUrl = RevSliderFunctionsWP::getUrlAttachmentImage($imageID, $imgResolution);
//			if(empty($imageUrl)){
//				$imageUrl = ZTSliderFunctions::getVal($params, "image");
//
//				$imgID = ZTSliderFunctions::get_image_id_by_url($imageUrl);
//				if($imgID !== false){
//					$imageUrl = RevSliderFunctionsWP::getUrlAttachmentImage($imgID, $imgResolution);
//				}
//			}
//
//			$this->imageThumb = RevSliderFunctionsWP::getUrlAttachmentImage($imageID,RevSliderFunctionsWP::THUMB_MEDIUM);
//
//		}else{
		$imageUrl = ZTSliderFunctions::getVal($params, "image");
		$this->imageFilepath = $imageUrl;

		if (!empty($imageUrl)) {
			if(@getimagesize($imageUrl)) {
				$imageUrl = $imageUrl;
			} else {
				$imageUrl = JUri::root().$imageUrl;
			}
		} else {$imageUrl =  '';}

		foreach ($layers as &$lay) {
			if (isset($lay['image_url'])) {
				if(!@getimagesize($lay['image_url'])) {
					$lay['image_url'] = JUri::root().$lay['image_url'];
				}
			}
		}

		if(ZTSliderFunctions::is_ssl()){
			$imageUrl = str_replace("http://", "https://", $imageUrl);
		}
		
		//set image path, file and url
		$this->imageUrl = $imageUrl;
		$this->imageThumb = '';///using timthumb to generate. Should have config in admin. joomla does not generate thmbail like wordpress
		$realPath = JPATH_ROOT.'/'.$this->imageFilepath;

		if(file_exists($realPath) == false || is_file($realPath) == false)
			$this->imageFilepath = "";

		$this->imageFilename = basename($this->imageUrl);
		
		$this->params = $params;
		$this->attribs = $params;
		$this->arrLayers = $layers;	
		$this->settings = $settings;	
		
	}
	
	
	/**
	 * set the image by image id
	 * @since: 5.0
	 */
	public function setImageByID($imageID, $size = 'full'){
		$a = array('imageID' => $imageID, 'size' => $size);

		$imageUrl = ZTSliderFunctionsWP::getUrlAttachmentImage($a['imageID'], $a['size']);


		if(!empty($imageUrl)){
			$this->imageID = $a['imageID'];
			$this->imageUrl = $imageUrl;
			$this->imageThumb = RevSliderFunctionsWP::getUrlAttachmentImage($a['imageID'],RevSliderFunctionsWP::THUMB_MEDIUM);
			$this->imageFilename = basename($this->imageUrl);
			$this->imageFilepath = RevSliderFunctionsWP::getImagePathFromURL($this->imageUrl);
			$realPath = RevSliderFunctionsWP::getPathContent().$this->imageFilepath;

			if(file_exists($realPath) == false || is_file($realPath) == false)
				$this->imageFilepath = "";

			return true;
		}

		return false;
	}
	
	
	/**
	 * change the background_type parameter
	 * @since: 5.0
	 */
	public function setBackgroundType($new_param){
		
		$this->params['background_type'] = $new_param;
	}
	
	
	/**
	 * 
	 * init by another slide
	 */
	public function initBySlide(ZTSliderSlide $slide){
		$this->id = "template";
		$this->templateID = $slide->getID();
		$this->sliderID = $slide->getSliderID();
		$this->slideOrder = $slide->getOrder();
		
		$this->imageUrl = $slide->getImageUrl();
		$this->imageID = $slide->getImageID();
		$this->imageThumb = $slide->getThumbUrl();
		$this->imageFilepath = $slide->getImageFilepath();
		$this->imageFilename = $slide->getImageFilename();
		
		$this->params = $slide->getParams();
		
		$this->arrLayers = $slide->getLayers();
		
		$this->settings = $slide->getSettings();
		
		$this->arrChildren = $slide->getArrChildrenPure();
	}
	
	
	/**
	 * 
	 * init slide by post data
	 */
	public function initByStreamData($postData, $slideTemplate, $sliderID, $sourceType, $additions){
		$a = array('postData' => $postData, 'slideTemplate' => $slideTemplate, 'sliderID' => $sliderID, 'sourceType' => $sourceType, 'additions' => $additions);
		
		$this->postData = array();
		$this->postData = (array)$a['postData'];
		
		//init by global template
		$this->initBySlide($a['slideTemplate']);
		
		switch($a['sourceType']){
			case 'facebook':
				$this->initByFacebook($a['sliderID'], $a['additions']);
			break;
			case 'twitter':
				$this->initByTwitter($a['sliderID'], $a['additions']);
			break;
			case 'instagram':
				$this->initByInstagram($a['sliderID']);
			break;
			case 'flickr':
				$this->initByFlickr($a['sliderID']);
			break;
			case 'youtube':
				$this->initByYoutube($a['sliderID'], $a['additions']);
			break;
			case 'vimeo':
				$this->initByVimeo($a['sliderID'], $a['additions']);
			break;
			default:
				$return = apply_filters('revslider_slide_initByStreamData_sourceType', false, $a, $this);
				if($return === false)
					ZTSliderFunctions::throwError(JText::_("Source must be from Stream", 'revslider'));
			break;
		}
	}

	public function updateSlideFromData($data){

		$slideID = ZTSliderFunctions::getVal($data, "slideid");
		$this->initByID($slideID);

		//treat params
		$params = ZTSliderFunctions::getVal($data, "params");
		if (!empty($params['image_url'])) {
			if (@getimagesize($params['image_url'])) {
				$params['image_url'] = str_replace(JUri::root(),'',$params['image_url']);
			}
		}

		if (!empty($params['image'])) {
			if (@getimagesize($params['image'])) $params['image'] = str_replace(JUri::root(),'',$params['image']);
		}
		$params = $this->normalizeParams($params);

		//preserve old data that not included in the given data
		$params = array_merge($this->params,$params);

		//treat layers
		$layers = ZTSliderFunctions::getVal($data, "layers");

		if(gettype($layers) == "string"){
			$layersStrip = stripslashes($layers);
			$layersDecoded = json_decode($layersStrip);
			if(empty($layersDecoded))
				$layersDecoded = json_decode($layers);

			$layers = ZTSliderFunctions::convertStdClassToArray($layersDecoded);
		}

		if(empty($layers) || gettype($layers) != "array")
			$layers = array();


		$settings = ZTSliderFunctions::getVal($data, "settings");

		$arrUpdate = array();
		$arrUpdate["layers"] = json_encode($layers);
		$arrUpdate["params"] = json_encode($params);

		$arrUpdate["params"]  = str_replace((str_replace('"','',json_encode(JUri::root()))),'',$arrUpdate["params"]);
		$arrUpdate["layers"]  = str_replace((str_replace('"','',json_encode(JUri::root()))),'',$arrUpdate["layers"]);
//		$arrUpdate["params"]  = str_replace(JUri::root().'/','',$arrUpdate["params"]);

		$arrUpdate["settings"] = json_encode($settings);

		$this->db->setQuery('UPDATE #__zt_layerslider_slide SET layers="'.$this->db->escape($arrUpdate["layers"]).'",settings="'.$this->db->escape($arrUpdate["settings"]).'",attribs="'.$this->db->escape($arrUpdate["params"]).'" WHERE id='.(int)$this->id);

		try{
			$this->db->execute();
		}catch (Exception $e) {
			$ms = $e->getMessage();
			ZTSliderFunctions::ajaxResponseError($ms);
		}

		return $arrUpdate;
		//RevSliderOperations::updateDynamicCaptions();
	}
	
	
	/**
	 * init the data for facebook
	 * @since: 5.0
	 * @change: 5.1.1 Facebook Album
	 */
	private function initByFacebook($sliderID, $additions){
		$this->postData = apply_filters('revslider_slide_initByFacebook_pre', $this->postData, $sliderID, $additions, $this);
		
		//set some slide params
		$this->id = ZTSliderFunctions::getVal($this->postData, 'id');
		
		$this->params["title"] = ZTSliderFunctions::getVal($this->postData, 'name');
		
		if(isset($this->params['enable_link']) && $this->params['enable_link'] == "true" && isset($this->params['link_type']) && $this->params['link_type'] == "regular"){
			$link = ZTSliderFunctions::getVal($this->postData, 'link');
			$this->params["link"] = str_replace(array("%link%", '{{link}}'), $link, $this->params["link"]);
		}

		$this->params["state"] = "published";
		
		if($this->params["background_type"] == 'image'){ //if image is choosen, use featured image as background
			if($additions['fb_type'] == 'album'){
				$this->imageUrl = 'https://graph.facebook.com/'.ZTSliderFunctions::getVal($this->postData, 'id').'/picture';
				$this->imageThumb = ZTSliderFunctions::getVal($this->postData, 'picture');
			}else{
				$img = $this->get_facebook_timeline_image();
				$this->imageUrl = $img;
				$this->imageThumb = $img;
			}
			
			if(empty($this->imageUrl))
				$this->imageUrl = ZT_ASSETS_PATH.'assets/images/sources/fb.png';
			
			if(ZTSliderFunctions::is_ssl()){
				$this->imageUrl = str_replace("http://", "https://", $this->imageUrl);
			}
			
			$this->imageFilename = basename($this->imageUrl);
		}
		
		$this->postData = apply_filters('revslider_slide_initByFacebook_post', $this->postData, $sliderID, $additions, $this);
		//replace placeholders in layers:
		$this->setLayersByStreamData($sliderID, 'facebook', $additions);
	}
	
	
	/**
	 * init the data for twitter
	 * @since: 5.0
	 */
	private function initByTwitter($sliderID, $additions){
		$this->postData = apply_filters('revslider_slide_initByTwitter_pre', $this->postData, $sliderID, $additions, $this);
		
		//set some slide params
		$this->id = ZTSliderFunctions::getVal($this->postData, 'id');

		$this->params["title"] = ZTSliderFunctions::getVal($this->postData, 'title');
		
		if(isset($this->params['enable_link']) && $this->params['enable_link'] == "true" && isset($this->params['link_type']) && $this->params['link_type'] == "regular"){
			$link = 'https://twitter.com/'.$additions['twitter_user'].'/status/'.ZTSliderFunctions::getVal($this->postData, 'id_str');
			$this->params["link"] = str_replace(array("%link%", '{{link}}'), $link, $this->params["link"]);
		}

		$this->params["state"] = "published";
		
		if($this->params["background_type"] == 'trans' || $this->params["background_type"] == 'image' || $this->params["background_type"] == 'streamtwitter' || $this->params["background_type"] == 'streamtwitterboth'){ //if image is choosen, use featured image as background
			$img_sizes = ZTSliderFunctions::get_all_image_sizes('twitter');
			
			$imgResolution = ZTSliderFunctions::getVal($this->params, 'image_source_type', reset($img_sizes));
			$this->imageID = ZTSliderFunctions::getVal($this->postData, 'id');
			if(!isset($img_sizes[$imgResolution])) $imgResolution = key($img_sizes);
			
			$image_url_array = ZTSliderFunctions::getVal($this->postData, 'media');
			$image_url_large = ZTSliderFunctions::getVal($image_url_array, 'large');
			
			$img = ZTSliderFunctions::getVal($image_url_large, 'media_url', '');
			$entities = ZTSliderFunctions::getVal($this->postData, 'entities');
			
			if($img == ''){
				$image_url_array = ZTSliderFunctions::getVal($entities, 'media');
				if(is_array($image_url_array) && isset($image_url_array[0])){
					if(is_ssl()){
						$img = ZTSliderFunctions::getVal($image_url_array[0], 'media_url_https');
					}else{
						$img = ZTSliderFunctions::getVal($image_url_array[0], 'media_url');
					}
					
				}
			}
			
			$urls = ZTSliderFunctions::getVal($entities, 'urls');
			if(is_array($urls) && isset($urls[0])){
				$display_url = ZTSliderFunctions::getVal($urls[0], 'display_url');
				
				
				//check if youtube or vimeo is inside
				if(strpos($display_url, 'youtu.be') !== false){
					$raw = explode('/', $display_url);
					$yturl = $raw[1];
					$this->params["slide_bg_youtube"] = $yturl; //set video for background video
				}elseif(strpos($display_url, 'vimeo.com') !== false){
					$raw = explode('/', $display_url);
					$vmurl = $raw[1];
					$this->params["slide_bg_vimeo"] = $vmurl; //set video for background video
				}
			}
			
			$image_url_array = ZTSliderFunctions::getVal($entities, 'media');
			if(is_array($image_url_array) && isset($image_url_array[0])){
				$video_info = ZTSliderFunctions::getVal($image_url_array[0], 'video_info');
				$variants = ZTSliderFunctions::getVal($video_info, 'variants');
				if(is_array($variants) && isset($variants[0])){
					$mp4 = ZTSliderFunctions::getVal($variants[0], 'url');

					$this->params["slide_bg_html_mpeg"] = $mp4; //set video for background video
				}
			}
			
			$entities = ZTSliderFunctions::getVal($this->postData, 'extended_entities');
			if($img == ''){
				$image_url_array = ZTSliderFunctions::getVal($entities, 'media');
				if(is_array($image_url_array) && isset($image_url_array[0])){
					if(is_ssl()){
						$img = ZTSliderFunctions::getVal($image_url_array[0], 'media_url_https');
					}else{
						$img = ZTSliderFunctions::getVal($image_url_array[0], 'media_url');
					}
					
				}
			}
			
			$urls = ZTSliderFunctions::getVal($entities, 'urls');
			if(is_array($urls) && isset($urls[0])){
				$display_url = ZTSliderFunctions::getVal($urls[0], 'display_url');
				
				
				//check if youtube or vimeo is inside
				if(strpos($display_url, 'youtu.be') !== false){
					$raw = explode('/', $display_url);
					$yturl = $raw[1];
					$this->params["slide_bg_youtube"] = $yturl; //set video for background video
				}elseif(strpos($display_url, 'vimeo.com') !== false){
					$raw = explode('/', $display_url);
					$vmurl = $raw[1];
					$this->params["slide_bg_vimeo"] = $vmurl; //set video for background video
				}
			}
			
			$image_url_array = ZTSliderFunctions::getVal($entities, 'media');
			if(is_array($image_url_array) && isset($image_url_array[0])){
				$video_info = ZTSliderFunctions::getVal($image_url_array[0], 'video_info');
				$variants = ZTSliderFunctions::getVal($video_info, 'variants');
				if(is_array($variants) && isset($variants[0])){
					$mp4 = ZTSliderFunctions::getVal($variants[0], 'url');
					$this->params["slide_bg_html_mpeg"] = $mp4; //set video for background video
				}
			}
			
			if($img !== ''){
				$this->imageUrl = $img;
				$this->imageThumb = $img;
			}
			
			//if(empty($this->imageUrl))
			//	return(false);
			
			if(empty($this->imageUrl))
				$this->imageUrl = ZT_ASSETS_PATH.'/assets/images/sources/tw.png';
			
			if(is_ssl()){
				$this->imageUrl = str_replace("http://", "https://", $this->imageUrl);
			}
			
			$this->imageFilename = basename($this->imageUrl);
		}
		
		$this->postData = apply_filters('revslider_slide_initByTwitter_post', $this->postData, $sliderID, $additions, $this);
		
		//replace placeholders in layers:
		$this->setLayersByStreamData($sliderID, 'twitter', $additions);
	}
	
	
	/**
	 * init the data for instagram
	 * @since: 5.0
	 */
	private function initByInstagram($sliderID){
		$this->postData = apply_filters('revslider_slide_initByInstagram_pre', $this->postData, $sliderID, $this);
		
		//set some slide params
		$this->id = ZTSliderFunctions::getVal($this->postData, 'id');
		
		$caption = ZTSliderFunctions::getVal($this->postData, 'caption');
		
		$this->params["title"] = ZTSliderFunctions::getVal($caption, 'text');
		
		$link = ZTSliderFunctions::getVal($this->postData, 'link');
		
		if(isset($this->params['enable_link']) && $this->params['enable_link'] == "true" && isset($this->params['link_type']) && $this->params['link_type'] == "regular"){
			$this->params["link"] = str_replace(array("%link%", '{{link}}'), $link, $this->params["link"]);
		}

		$this->params["state"] = "published";
		
		if($this->params["background_type"] == 'trans' || $this->params["background_type"] == 'image' || $this->params["background_type"] == 'streaminstagram' || $this->params["background_type"] == 'streaminstagramboth'){ //if image is choosen, use featured image as background
			$img_sizes = ZTSliderFunctions::get_all_image_sizes('instagram');
			
			$imgResolution = ZTSliderFunctions::getVal($this->params, 'image_source_type', reset($img_sizes));
			if(!isset($img_sizes[$imgResolution])) $imgResolution = key($img_sizes);
			
			$this->imageID = ZTSliderFunctions::getVal($this->postData, 'id');
			$imgs = ZTSliderFunctions::getVal($this->postData, 'images', array());
			$is = array();
			foreach($imgs as $k => $im){
				$is[$k] = $im->url;
			}
			
			$this->imageUrl = $is[$imgResolution];
			$this->imageThumb = $is['thumbnail'];
			
			//if(empty($this->imageUrl))
			//	return(false);
			
			if(empty($this->imageUrl))
				$this->imageUrl = ZT_ASSETS_PATH.'/assets/images/sources/ig.png';
			
			if(is_ssl()){
				$this->imageUrl = str_replace("http://", "https://", $this->imageUrl);
			}
			
			$this->imageFilename = basename($this->imageUrl);
		}
		
		$videos = ZTSliderFunctions::getVal($this->postData, 'videos');
		
		if(!empty($videos) && isset($videos->standard_resolution) && isset($videos->standard_resolution->url)){
			$this->params["slide_bg_instagram"] = $videos->standard_resolution->url; //set video for background video
			$this->params["slide_bg_html_mpeg"] = $videos->standard_resolution->url; //set video for background video
		}
		
		$this->postData = apply_filters('revslider_slide_initByInstagram_post', $this->postData, $sliderID, $this);
		
		//replace placeholders in layers:
		$this->setLayersByStreamData($sliderID, 'instagram');	
	}
	
	
	/**
	 * init the data for flickr
	 * @since: 5.0
	 */
	private function initByFlickr($sliderID){
		$this->postData = apply_filters('revslider_slide_initByFlickr_pre', $this->postData, $sliderID, $this);
		
		//set some slide params
		$this->id = ZTSliderFunctions::getVal($this->postData, 'id');
		$this->params["title"] = ZTSliderFunctions::getVal($this->postData, 'title');
		
		if(isset($this->params['enable_link']) && $this->params['enable_link'] == "true" && isset($this->params['link_type']) && $this->params['link_type'] == "regular"){
			$link = 'http://flic.kr/p/'.$this->base_encode(ZTSliderFunctions::getVal($this->postData, 'id'));
			$this->params["link"] = str_replace(array("%link%", '{{link}}'), $link, $this->params["link"]);
		}
		
		$this->params["state"] = "published";
		
		if($this->params["background_type"] == 'image'){ //if image is choosen, use featured image as background
			//facebook check which image size is choosen
			$img_sizes = ZTSliderFunctions::get_all_image_sizes('flickr');
			
			$imgResolution = ZTSliderFunctions::getVal($this->params, 'image_source_type', reset($img_sizes));

			$this->imageID = ZTSliderFunctions::getVal($this->postData, 'id');
			if(!isset($img_sizes[$imgResolution])) $imgResolution = key($img_sizes);
			
			$is = @array(
				'square' 		=> 	ZTSliderFunctions::getVal($this->postData, 'url_sq'),
				'large-square' 	=> 	ZTSliderFunctions::getVal($this->postData, 'url_q'),
				'thumbnail' 	=> 	ZTSliderFunctions::getVal($this->postData, 'url_t'),
				'small' 		=> 	ZTSliderFunctions::getVal($this->postData, 'url_s'),
				'small-320' 	=> 	ZTSliderFunctions::getVal($this->postData, 'url_n'),
				'medium' 		=> 	ZTSliderFunctions::getVal($this->postData, 'url_m'),
				'medium-640' 	=> 	ZTSliderFunctions::getVal($this->postData, 'url_z'),
				'medium-800' 	=> 	ZTSliderFunctions::getVal($this->postData, 'url_c'),
				'large' 		=>	ZTSliderFunctions::getVal($this->postData, 'url_l'),
				'original'		=>	ZTSliderFunctions::getVal($this->postData, 'url_o')
			);
			
			$this->imageUrl = (isset($is[$imgResolution])) ? $is[$imgResolution] : '';
			$this->imageThumb = (isset($is['thumbnail'])) ? $is['thumbnail'] : '';
			
			//if(empty($this->imageUrl))
			//	return(false);
			
			if(empty($this->imageUrl))
				$this->imageUrl = ZT_ASSETS_PATH.'/assets/images/sources/fr.png';
			
			if(is_ssl()){
				$this->imageUrl = str_replace("http://", "https://", $this->imageUrl);
			}
			
			$this->imageFilename = basename($this->imageUrl);
			
			if(!empty($thumbID))
				$this->setImageByImageURL($thumbID);
			
		}
		
		$this->postData = apply_filters('revslider_slide_initByFlickr_post', $this->postData, $sliderID, $this);
		//replace placeholders in layers:
		$this->setLayersByStreamData($sliderID, 'flickr');
	}
	
	
	/**
	 * init the data for youtube
	 * @since: 5.0
	 */
	private function initByYoutube($sliderID, $additions){
		$this->postData = apply_filters('revslider_slide_initByYoutube_pre', $this->postData, $sliderID, $additions, $this);
		
		//set some slide params
		$snippet = ZTSliderFunctions::getVal($this->postData, 'snippet');
		$resource = ZTSliderFunctions::getVal($snippet, 'resourceId');
		
		if($additions['yt_type'] == 'channel'){
			$link_raw = ZTSliderFunctions::getVal($this->postData, 'id');
			$link = ZTSliderFunctions::getVal($link_raw, 'videoId');
		}else{
			$link_raw = ZTSliderFunctions::getVal($snippet, 'resourceId');
			$link = ZTSliderFunctions::getVal($link_raw, 'videoId');
		}
		
		
		if(isset($this->params['enable_link']) && $this->params['enable_link'] == "true" && isset($this->params['link_type']) && $this->params['link_type'] == "regular"){
			
			if($link !== '') $link = '//youtube.com/watch?v='.$link;
			
			$this->params["link"] = str_replace(array("%link%", '{{link}}'), $link, $this->params["link"]);
		}
		
		$this->params["slide_bg_youtube"] = $link; //set video for background video

		
		switch($additions['yt_type']){
			case 'channel':
				$id = ZTSliderFunctions::getVal($this->postData, 'id');
				$this->id = ZTSliderFunctions::getVal($id, 'videoId');
			break;
			case 'playlist':
				$this->id = ZTSliderFunctions::getVal($resource, 'videoId');
			break;
		}
		if($this->id == '') $this->id = 'not-found';
		
		$this->params["title"] = ZTSliderFunctions::getVal($snippet, 'title');
		
		$this->params["state"] = "published";
		
		if($this->params["background_type"] == 'trans' || $this->params["background_type"] == 'image' || $this->params["background_type"] == 'streamyoutube' || $this->params["background_type"] == 'streamyoutubeboth'){ //if image is choosen, use featured image as background
			//facebook check which image size is choosen
			$img_sizes = ZTSliderFunctions::get_all_image_sizes('youtube');
			
			$imgResolution = ZTSliderFunctions::getVal($this->params, 'image_source_type', reset($img_sizes));

			$this->imageID = ZTSliderFunctions::getVal($resource, 'videoId');
			if(!isset($img_sizes[$imgResolution])) $imgResolution = key($img_sizes);
			
			$thumbs = ZTSliderFunctions::getVal($snippet, 'thumbnails');
			$is = array();
			if(!empty($thumbs)){
				foreach($thumbs as $name => $vals){
					$is[$name] = ZTSliderFunctions::getVal($vals, 'url');
				}
			}
			
			$this->imageUrl = (isset($is[$imgResolution])) ? $is[$imgResolution] : '';
			$this->imageThumb = (isset($is['medium'])) ? $is['medium'] : '';
			
			//if(empty($this->imageUrl))
			//	return(false);
			
			if(empty($this->imageUrl))
				$this->imageUrl = ZT_ASSETS_PATH.'/assets/images/sources/yt.png';
			
			if(is_ssl()){
				$this->imageUrl = str_replace("http://", "https://", $this->imageUrl);
			}
			
			$this->imageFilename = basename($this->imageUrl);
			
			if(!empty($thumbID))
				$this->setImageByImageURL($thumbID);
			
		}
		
		$this->postData = apply_filters('revslider_slide_initByYoutube_post', $this->postData, $sliderID, $additions, $this);
		
		//replace placeholders in layers:
		$this->setLayersByStreamData($sliderID, 'youtube', $additions);
	}
	
	
	/**
	 * init the data for vimeo
	 * @since: 5.0
	 */
	private function initByVimeo($sliderID, $additions){
		
		$this->postData = apply_filters('revslider_slide_initByVimeo_pre', $this->postData, $sliderID, $additions, $this);
		
		//set some slide params
		$this->id = ZTSliderFunctions::getVal($this->postData, 'id');
		$this->params["title"] = ZTSliderFunctions::getVal($this->postData, 'title');
		
		if(isset($this->params['enable_link']) && $this->params['enable_link'] == "true" && isset($this->params['link_type']) && $this->params['link_type'] == "regular"){
			$link = ZTSliderFunctions::getVal($this->postData, 'url');
			$this->params["link"] = str_replace(array("%link%", '{{link}}'), $link, $this->params["link"]);
		}
		
		$this->params["slide_bg_vimeo"] = ZTSliderFunctions::getVal($this->postData, 'url');
		
		$this->params["state"] = "published";
		
		if($this->params["background_type"] == 'trans' || $this->params["background_type"] == 'image' || $this->params["background_type"] == 'streamvimeo' || $this->params["background_type"] == 'streamvimeoboth'){ //if image is choosen, use featured image as background
			//facebook check which image size is choosen
			$img_sizes = ZTSliderFunctions::get_all_image_sizes('vimeo');
			$imgResolution = ZTSliderFunctions::getVal($this->params, 'image_source_type', reset($img_sizes));

			$this->imageID = ZTSliderFunctions::getVal($this->postData, 'id');
			if(!isset($img_sizes[$imgResolution])) $imgResolution = key($img_sizes);
			
			$is = array();
			
			foreach($img_sizes as $handle => $name){
				$is[$handle] = ZTSliderFunctions::getVal($this->postData, $handle);
			}
			
			
			$this->imageUrl = (isset($is[$imgResolution])) ? $is[$imgResolution] : '';
			$this->imageThumb = (isset($is['thumbnail'])) ? $is['thumbnail'] : '';
			
			//if(empty($this->imageUrl))
			//	return(false);
			
			if(empty($this->imageUrl))
				$this->imageUrl = ZT_ASSETS_PATH.'/assets/images/sources/vm.png';
			
			if(is_ssl()){
				$this->imageUrl = str_replace("http://", "https://", $this->imageUrl);
			}
			
			$this->imageFilename = basename($this->imageUrl);
			
			if(!empty($thumbID))
				$this->setImageByImageURL($thumbID);
			
		}
		
		$this->postData = apply_filters('revslider_slide_initByVimeo_post', $this->postData, $sliderID, $additions, $this);
		
		//replace placeholders in layers:
		$this->setLayersByStreamData($sliderID, 'vimeo', $additions);
	}
	
	
	/**
	 * replace layer placeholders by stream data
	 * @since: 5.0
	 */
	private function setLayersByStreamData($sliderID, $stream_type, $additions = array()){
		
		$a = apply_filters('revslider_slide_setLayersByStreamData_pre', array('arrLayers' => $this->arrLayers, 'params' => $this->params), $sliderID, $stream_type, $additions, $this);
		
		$this->params = $a['params'];
		$this->arrLayers = $a['arrLayers'];
		
		
		$attr = $this->return_stream_data($stream_type, $additions);
		
		foreach($this->arrLayers as $key=>$layer){
			
			$text = ZTSliderFunctions::getVal($layer, "text");
			
			$text = apply_filters('revslider_mod_stream_meta', $text, $sliderID, $stream_type, $this->postData); //option to add your own filter here to modify meta to your likings
			
			$text = $this->set_stream_data($text, $attr, $stream_type, $additions);
			
			$layer["text"] = $text;
			
			//set link actions to the stream data
			$layer['layer_action'] = (array) $layer['layer_action'];
			if(isset($layer['layer_action'])){
				if(isset($layer['layer_action']['image_link']) && !empty($layer['layer_action']['image_link'])){
					foreach($layer['layer_action']['image_link'] as $jtsk => $jtsval){
						$layer['layer_action']['image_link'][$jtsk] = $this->set_stream_data($layer['layer_action']['image_link'][$jtsk], $attr, $stream_type, $additions, true);
					}
				}
			}
			$this->arrLayers[$key] = $layer;
		}
		
		/*$params = $this->getParams();
		
		foreach($params as $key => $param){ //set metas on all params except arrays
			if(!is_array($param)){
				$pa = $this->set_stream_data($param, $attr, $stream_type, $additions);
				$this->setParam($key, $pa);
			}
		}*/
		
		//set params to the stream data
		for($mi=1;$mi<=10;$mi++){
			$pa = $this->getParam('params_'.$mi, '');
			$pa = $this->set_stream_data($pa, $attr, $stream_type, $additions);
			$this->setParam('params_'.$mi, $pa);
		}
		$param_list = array('id_attr', 'class_attr', 'data_attr');
		//set params to the stream data
		foreach($param_list as $p){
			$pa = $this->getParam($p, '');
			$pa = $this->set_stream_data($pa, $attr, $stream_type, $additions);
			$this->setParam($p, $pa);
		}
		
		$a = apply_filters('revslider_slide_setLayersByStreamData_post', array('arrLayers' => $this->arrLayers, 'params' => $this->params), $sliderID, $stream_type, $additions, $this);
		
		$this->params = $a['params'];
		$this->arrLayers = $a['arrLayers'];
	}
	
	
	public function set_stream_data($text, $attr, $stream_type, $additions = array(), $is_action = false){
		$img_sizes = ZTSliderFunctions::get_all_image_sizes($stream_type);
		
		$text = apply_filters('revslider_slide_set_stream_data_pre', $text, $attr, $stream_type, $additions, $is_action, $img_sizes);
		
		$title = ($stream_type == 'twitter' && $is_action === false) ? ZTSliderFunctions::add_wrap_around_url($attr['title']) : $attr['title'];
		$excerpt = ($stream_type == 'twitter' && $is_action === false) ? ZTSliderFunctions::add_wrap_around_url($attr['excerpt']) : $attr['excerpt'];
		$content = ($stream_type == 'twitter' && $is_action === false) ? ZTSliderFunctions::add_wrap_around_url($attr['content']) : $attr['content'];
		
		$text = str_replace(array('%title%', '{{title}}'), $title, $text);
		$text = str_replace(array('%excerpt%', '{{excerpt}}'), $excerpt, $text);
		$text = str_replace(array('%alias%', '{{alias}}'), $attr['alias'], $text);
		$text = str_replace(array('%content%', '{{content}}'), $content, $text);
		$text = str_replace(array('%link%', '{{link}}'), $attr['link'], $text);
		$text = str_replace(array('%date_published%', '{{date_published}}', '%date%', '{{date}}'), $attr['date'], $text);
		$text = str_replace(array('%date_modified%', '{{date_modified}}'), $attr['date_modified'], $text);
		$text = str_replace(array('%author_name%', '{{author_name}}'), $attr['author_name'], $text);
		$text = str_replace(array('%num_comments%', '{{num_comments}}'), $attr['num_comments'], $text);
		$text = str_replace(array('%catlist%', '{{catlist}}'), $attr['catlist'], $text);
		$text = str_replace(array('%catlist_raw%', '{{catlist_raw}}'), $attr['catlist_raw'], $text);
		$text = str_replace(array('%taglist%', '{{taglist}}'), $attr['taglist'], $text);
		$text = str_replace(array('%likes%', '{{likes}}'), $attr['likes'], $text);
		$text = str_replace(array('%retweet_count%', '{{retweet_count}}'), $attr['retweet_count'], $text);
		$text = str_replace(array('%favorite_count%', '{{favorite_count}}'), $attr['favorite_count'], $text);
		$text = str_replace(array('%views%', '{{views}}'), $attr['views'], $text);
		
		$arrMatches = array();
		preg_match_all("/{{content:\w+[\:]\w+}}/", $text, $arrMatches);
		foreach($arrMatches as $matched){
			foreach($matched as $match) {
				//now check length and type
				
				$meta = str_replace("{{content:", "", $match);
				$meta = str_replace("}}","",$meta);
				$meta = str_replace('_REVSLIDER_', '-', $meta);
				$vals = explode(':', $meta);
				
				if(count($vals) !== 2) continue; //not correct values
				$vals[1] = intval($vals[1]); //get real number
				if($vals[1] === 0 || $vals[1] < 0) continue; //needs to be at least 1 
				
				if($vals[0] == 'words'){
					$metaValue = explode(' ', strip_tags($content), $vals[1]+1);
					if(is_array($metaValue) && count($metaValue) > $vals[1]) array_pop($metaValue);
					$metaValue = implode(' ', $metaValue);
				}elseif($vals[0] == 'chars'){
					$metaValue = substr(strip_tags($content), 0, $vals[1]);
				}else{
					continue;
				}
				
				$text = str_replace($match,$metaValue,$text);	
			}
		}
		
		
		switch($stream_type){
			case 'facebook':
				foreach($img_sizes as $img_handle => $img_name){
					if(!isset($attr['img_urls'])) $attr['img_urls'] = array();
					if(!isset($attr['img_urls'][$img_handle])) $attr['img_urls'][$img_handle] = array();
					if(!isset($attr['img_urls'][$img_handle]['url'])) $attr['img_urls'][$img_handle]['url'] = '';
					if(!isset($attr['img_urls'][$img_handle]['tag'])) $attr['img_urls'][$img_handle]['tag'] = '';
						
					if($additions['fb_type'] == 'album'){
						$text = str_replace(array('%image_url_'.$img_handle.'%', '{{image_url_'.$img_handle.'}}'), $attr['img_urls'][$img_handle]['url'], $text);
						$text = str_replace(array('%image_'.$img_handle.'%', '{{image_'.$img_handle.'}}'), $attr['img_urls'][$img_handle]['tag'], $text);
					}else{
						$text = str_replace(array('%image_url_'.$img_handle.'%', '{{image_url_'.$img_handle.'}}'), $attr['img_urls']['url'], $text);
						$text = str_replace(array('%image_'.$img_handle.'%', '{{image_'.$img_handle.'}}'), $attr['img_urls']['tag'], $text);
					}
				}
			break;
			case 'youtube':
			case 'vimeo':
				//$text = str_replace(array('%image_url_'.$img_handle.'%', '{{image_url_'.$img_handle.'}}'), @$attr['img_urls'][$img_handle]['url'], $text);
				//$text = str_replace(array('%image_'.$img_handle.'%', '{{image_'.$img_handle.'}}'), @$attr['img_urls'][$img_handle]['tag'], $text);
			case 'twitter':
			case 'instagram':
			case 'flickr':
				foreach($img_sizes as $img_handle => $img_name){
					if(!isset($attr['img_urls'])) $attr['img_urls'] = array();
					if(!isset($attr['img_urls'][$img_handle])) $attr['img_urls'][$img_handle] = array();
					if(!isset($attr['img_urls'][$img_handle]['url'])) $attr['img_urls'][$img_handle]['url'] = '';
					if(!isset($attr['img_urls'][$img_handle]['tag'])) $attr['img_urls'][$img_handle]['tag'] = '';
					
					$text = str_replace(array('%image_url_'.$img_handle.'%', '{{image_url_'.$img_handle.'}}'), $attr['img_urls'][$img_handle]['url'], $text);
					$text = str_replace(array('%image_'.$img_handle.'%', '{{image_'.$img_handle.'}}'), $attr['img_urls'][$img_handle]['tag'], $text);
				}
			break;
		}
		
		return apply_filters('revslider_slide_set_stream_data_post', $text, $attr, $stream_type, $additions, $is_action, $img_sizes);
	}
	
	
	public function return_stream_data($stream_type, $additions = array()){
		$attr = array();
		$attr['title'] = '';
		$attr['excerpt'] = '';
		$attr['alias'] = '';
		$attr['content'] = '';
		$attr['link'] = '';
		$attr['date'] = '';
		$attr['date_modified'] = '';
		$attr['author_name'] = '';
		$attr['num_comments'] = '';
		$attr['catlist'] = '';
		$attr['catlist_raw'] = '';
		$attr['taglist'] = '';
		$attr['likes'] = '';
		$attr['retweet_count'] = '';
		$attr['favorite_count'] = '';
		$attr['views'] = '';
		$attr['img_urls'] = array();
		
		$img_sizes = ZTSliderFunctions::get_all_image_sizes($stream_type);
		
		$attr = apply_filters('revslider_slide_return_stream_data_pre', $attr, $stream_type, $additions, $img_sizes);
		
		switch($stream_type){
			case 'facebook':
				if($additions['fb_type'] == 'album'){
					$attr['title'] = ZTSliderFunctions::getVal($this->postData, 'name');
					$attr['content'] = ZTSliderFunctions::getVal($this->postData, 'name');
					$attr['link'] = ZTSliderFunctions::getVal($this->postData, 'link');
					$attr['date'] = RevSliderFunctionsWP::convertPostDate(ZTSliderFunctions::getVal($this->postData, 'created_time'), true);
					$attr['date_modified'] = RevSliderFunctionsWP::convertPostDate(ZTSliderFunctions::getVal($this->postData, 'updated_time'), true);
					$author_name_raw = ZTSliderFunctions::getVal($this->postData, 'from');
					$attr['author_name'] = $author_name_raw->name;
					$likes_data = ZTSliderFunctions::getVal($this->postData, 'likes');
					$attr['likes'] = count(ZTSliderFunctions::getVal($likes_data, 'data'));
					$fb_img_thumbnail = ZTSliderFunctions::getVal($this->postData, 'picture');
					$fb_img = 'https://graph.facebook.com/'.ZTSliderFunctions::getVal($this->postData, 'id').'/picture';
					
					$attr['img_urls']['full'] = array(
						'url' => $fb_img,
						'tag' => '<img src="'.$fb_img.'" data-no-retina />'
					);
					$attr['img_urls']['thumbnail'] = array(
						'url' => $fb_img_thumbnail,
						'tag' => '<img src="'.$fb_img_thumbnail.'" data-no-retina />'
					);

				}else{
					$attr['title'] = ZTSliderFunctions::getVal($this->postData, 'message');
					$attr['content'] = ZTSliderFunctions::getVal($this->postData, 'message');
					$post_url = explode('_', ZTSliderFunctions::getVal($this->postData, 'id'));
					$attr['link'] = 'https://www.facebook.com/'.$additions['fb_user_id'].'/posts/'.$post_url[1];
					$attr['date'] = RevSliderFunctionsWP::convertPostDate(ZTSliderFunctions::getVal($this->postData, 'created_time'), true);
					$attr['date_modified'] = RevSliderFunctionsWP::convertPostDate(ZTSliderFunctions::getVal($this->postData, 'updated_time'), true);
					$author_name_raw = ZTSliderFunctions::getVal($this->postData, 'from');
					$attr['author_name'] = $author_name_raw->name;
					$likes_data = ZTSliderFunctions::getVal($this->postData, 'likes');
					
					$likes_data = ZTSliderFunctions::getVal($likes_data, 'summary');
					$likes_data = ZTSliderFunctions::getVal($likes_data, 'total_count');
					
					$attr['likes'] = intval($likes_data);
					$img = $this->get_facebook_timeline_image();
					$attr['img_urls'] = array(
						'url' => $img,
						'tag' => '<img src="'.$img.'" data-no-retina />'
					);
				}
			break;
			case 'twitter':
				$user = ZTSliderFunctions::getVal($this->postData, 'user');
				$attr['title'] = ZTSliderFunctions::getVal($this->postData, 'text');
				$attr['content'] = ZTSliderFunctions::getVal($this->postData, 'text');
				$attr['link'] = 'https://twitter.com/'.$additions['twitter_user'].'/status/'.ZTSliderFunctions::getVal($this->postData, 'id_str');
				$attr['date'] = RevSliderFunctionsWP::convertPostDate(ZTSliderFunctions::getVal($this->postData, 'created_at'), true);
				$attr['author_name'] = ZTSliderFunctions::getVal($user, 'screen_name');
				$attr['retweet_count'] = ZTSliderFunctions::getVal($this->postData, 'retweet_count', '0');
				$attr['favorite_count'] = ZTSliderFunctions::getVal($this->postData, 'favorite_count', '0');
				$image_url_array = ZTSliderFunctions::getVal($this->postData, 'media');
				$image_url_large = ZTSliderFunctions::getVal($image_url_array, 'large');
				$img = ZTSliderFunctions::getVal($image_url_large, 'media_url', '');
				if($img == ''){
					$entities = ZTSliderFunctions::getVal($this->postData, 'entities');
					$image_url_array = ZTSliderFunctions::getVal($entities, 'media');
					if(is_array($image_url_array) && isset($image_url_array[0])){
						if(is_ssl()){
							$img = ZTSliderFunctions::getVal($image_url_array[0], 'media_url_https');
						}else{
							$img = ZTSliderFunctions::getVal($image_url_array[0], 'media_url');
						}
						
						$image_url_large = $image_url_array[0];
					}
				}
				if($img == ''){
					$entities = ZTSliderFunctions::getVal($this->postData, 'extended_entities');
					$image_url_array = ZTSliderFunctions::getVal($entities, 'media');
					if(is_array($image_url_array) && isset($image_url_array[0])){
						if(is_ssl()){
							$img = ZTSliderFunctions::getVal($image_url_array[0], 'media_url_https');
						}else{
							$img = ZTSliderFunctions::getVal($image_url_array[0], 'media_url');
						}
						
						$image_url_large = $image_url_array[0];
					}
				}
				if($img !== ''){
					$w = ZTSliderFunctions::getVal($image_url_large, 'w', '');
					$h = ZTSliderFunctions::getVal($image_url_large, 'h', '');
					$attr['img_urls']['large'] = array(
						'url' => $img,
						'tag' => '<img src="'.$img.'" width="'.$w.'" height="'.$h.'" data-no-retina />'
					);
				}
			break;
			case 'instagram':
				$caption = ZTSliderFunctions::getVal($this->postData, 'caption');
				$user = ZTSliderFunctions::getVal($this->postData, 'user');
				
				$attr['title'] = ZTSliderFunctions::getVal($caption, 'text');
				$attr['content'] = ZTSliderFunctions::getVal($caption, 'text');
				$attr['link'] = ZTSliderFunctions::getVal($this->postData, 'link');
				$attr['date'] = RevSliderFunctionsWP::convertPostDate(ZTSliderFunctions::getVal($this->postData, 'created_time'), true);
				$attr['author_name'] = ZTSliderFunctions::getVal($user, 'username');
				
				$likes_raw = ZTSliderFunctions::getVal($this->postData, 'likes');
				$attr['likes'] = ZTSliderFunctions::getVal($likes_raw, 'count');
				
				$comments_raw = ZTSliderFunctions::getVal($this->postData, 'comments');
				$attr['num_comments'] = ZTSliderFunctions::getVal($comments_raw, 'count');
				
				$inst_img = ZTSliderFunctions::getVal($this->postData, 'images', array());
				foreach($inst_img as $key => $img){
					$attr['img_urls'][$key] = array(
						'url' => $img->url,
						'tag' => '<img src="'.$img->url.'" width="'.$img->width.'" height="'.$img->height.'" data-no-retina />'
					);
				}
			break;
			case 'flickr':
				$attr['title'] = ZTSliderFunctions::getVal($this->postData, 'title');
				$tc = ZTSliderFunctions::getVal($this->postData, 'description');
				$attr['content'] = ZTSliderFunctions::getVal($tc, '_content');
				$attr['date'] = RevSliderFunctionsWP::convertPostDate(ZTSliderFunctions::getVal($this->postData, 'datetaken'));
				$attr['author_name'] = ZTSliderFunctions::getVal($this->postData, 'ownername');
				$attr['link'] = 'http://flic.kr/p/'.$this->base_encode(ZTSliderFunctions::getVal($this->postData, 'id'));
				$attr['views'] = ZTSliderFunctions::getVal($this->postData, 'views');
				
				$attr['img_urls'] = @array(
					'square' 		=> 	array('url' => ZTSliderFunctions::getVal($this->postData, 'url_sq'), 'tag' => '<img src="'.ZTSliderFunctions::getVal($this->postData, 'url_sq').'" width="'.ZTSliderFunctions::getVal($this->postData, 'width_sq').'" height="'.ZTSliderFunctions::getVal($this->postData, 'height_sq').'" data-no-retina />'),
					'large-square' 	=> 	array('url' => ZTSliderFunctions::getVal($this->postData, 'url_q'), 'tag' => '<img src="'.ZTSliderFunctions::getVal($this->postData, 'url_q').'" width="'.ZTSliderFunctions::getVal($this->postData, 'width_q').'" height="'.ZTSliderFunctions::getVal($this->postData, 'height_q').'"  data-no-retina />'),
					'thumbnail' 	=> 	array('url' => ZTSliderFunctions::getVal($this->postData, 'url_t'), 'tag' => '<img src="'.ZTSliderFunctions::getVal($this->postData, 'url_t').'" width="'.ZTSliderFunctions::getVal($this->postData, 'width_t').'" height="'.ZTSliderFunctions::getVal($this->postData, 'height_t').'"  data-no-retina />'),
					'small' 		=> 	array('url' => ZTSliderFunctions::getVal($this->postData, 'url_s'), 'tag' => '<img src="'.ZTSliderFunctions::getVal($this->postData, 'url_s').'" width="'.ZTSliderFunctions::getVal($this->postData, 'width_s').'" height="'.ZTSliderFunctions::getVal($this->postData, 'height_s').'"  data-no-retina />'),
					'small-320' 	=> 	array('url' => ZTSliderFunctions::getVal($this->postData, 'url_n'), 'tag' => '<img src="'.ZTSliderFunctions::getVal($this->postData, 'url_n').'" width="'.ZTSliderFunctions::getVal($this->postData, 'width_n').'" height="'.ZTSliderFunctions::getVal($this->postData, 'height_n').'"  data-no-retina />'),
					'medium' 		=> 	array('url' => ZTSliderFunctions::getVal($this->postData, 'url_m'), 'tag' => '<img src="'.ZTSliderFunctions::getVal($this->postData, 'url_m').'" width="'.ZTSliderFunctions::getVal($this->postData, 'width_m').'" height="'.ZTSliderFunctions::getVal($this->postData, 'height_m').'"  data-no-retina />'),
					'medium-640' 	=> 	array('url' => ZTSliderFunctions::getVal($this->postData, 'url_z'), 'tag' => '<img src="'.ZTSliderFunctions::getVal($this->postData, 'url_z').'" width="'.ZTSliderFunctions::getVal($this->postData, 'width_z').'" height="'.ZTSliderFunctions::getVal($this->postData, 'height_z').'"  data-no-retina />'),
					'medium-800' 	=> 	array('url' => ZTSliderFunctions::getVal($this->postData, 'url_c'), 'tag' => '<img src="'.ZTSliderFunctions::getVal($this->postData, 'url_c').'" width="'.ZTSliderFunctions::getVal($this->postData, 'width_c').'" height="'.ZTSliderFunctions::getVal($this->postData, 'height_c').'"  data-no-retina />'),
					'large' 		=>	array('url' => ZTSliderFunctions::getVal($this->postData, 'url_l'), 'tag' => '<img src="'.ZTSliderFunctions::getVal($this->postData, 'url_l').'" width="'.ZTSliderFunctions::getVal($this->postData, 'width_l').'" height="'.ZTSliderFunctions::getVal($this->postData, 'height_l').'"  data-no-retina />'),
					'original'		=>	array('url' => ZTSliderFunctions::getVal($this->postData, 'url_o'), 'tag' => '<img src="'.ZTSliderFunctions::getVal($this->postData, 'url_o').'" width="'.ZTSliderFunctions::getVal($this->postData, 'width_o').'" height="'.ZTSliderFunctions::getVal($this->postData, 'height_o').'"  data-no-retina />')
				);
			break;
			case 'youtube':
				$snippet = ZTSliderFunctions::getVal($this->postData, 'snippet');
				$attr['title'] = ZTSliderFunctions::getVal($snippet, 'title');
				$attr['excerpt'] = ZTSliderFunctions::getVal($snippet, 'description');
				$attr['content'] = ZTSliderFunctions::getVal($snippet, 'description');
				$attr['date'] = RevSliderFunctionsWP::convertPostDate(ZTSliderFunctions::getVal($snippet, 'publishedAt'));
				
				if($additions['yt_type'] == 'channel'){
					$link_raw = ZTSliderFunctions::getVal($this->postData, 'id');
					$attr['link'] = ZTSliderFunctions::getVal($link_raw, 'videoId');
					if($attr['link'] !== '') $attr['link'] = '//youtube.com/watch?v='.$attr['link'];
				}else{
					$link_raw = ZTSliderFunctions::getVal($this->postData, 'resourceId');
					$attr['link'] = ZTSliderFunctions::getVal($link_raw, 'videoId');
					if($attr['link'] !== '') $attr['link'] = '//youtube.com/watch?v='.$attr['link'];
				}
				
				$thumbs = ZTSliderFunctions::getVal($snippet, 'thumbnails');
				$attr['img_urls'] = array();
				if(!empty($thumbs)){
					foreach($thumbs as $name => $vals){
						$attr['img_urls'][$name] = array(
							'url' => ZTSliderFunctions::getVal($vals, 'url'),
						);
						switch($additions['yt_type']){
							case 'channel':
								$attr['img_urls'][$name]['tag'] = '<img src="'.ZTSliderFunctions::getVal($vals, 'url').'" data-no-retina />';
							break;
							case 'playlist':
								$attr['img_urls'][$name]['tag'] = '<img src="'.ZTSliderFunctions::getVal($vals, 'url').'" width="'.ZTSliderFunctions::getVal($vals, 'width').'" height="'.ZTSliderFunctions::getVal($vals, 'height').'" data-no-retina />';
							break;
						}
					}
				}
			break;
			case 'vimeo':
				$attr['title'] = ZTSliderFunctions::getVal($this->postData, 'title');
				$attr['excerpt'] = ZTSliderFunctions::getVal($this->postData, 'description');
				$attr['content'] = ZTSliderFunctions::getVal($this->postData, 'description');
				$attr['date'] = RevSliderFunctionsWP::convertPostDate(ZTSliderFunctions::getVal($this->postData, 'upload_date'));
				$attr['likes'] = ZTSliderFunctions::getVal($this->postData, 'stats_number_of_likes');
				$attr['views'] = ZTSliderFunctions::getVal($this->postData, 'stats_number_of_plays');
				$attr['num_comments'] = ZTSliderFunctions::getVal($this->postData, 'stats_number_of_comments');
				$attr['link'] = ZTSliderFunctions::getVal($this->postData, 'url');
				$attr['author_name'] = ZTSliderFunctions::getVal($this->postData, 'user_name');
				
				$attr['img_urls'] = array();
				if(!empty($img_sizes)){
					foreach($img_sizes as $name => $vals){
						$attr['img_urls'][$name] = array(
							'url' => ZTSliderFunctions::getVal($this->postData, $name),
							'tag' => '<img src="'.ZTSliderFunctions::getVal($this->postData, $name).'" data-no-retina />'
						);
					}
				}
				
			break;
			
		}
		
		return apply_filters('revslider_slide_return_stream_data_post', $attr, $stream_type, $additions, $img_sizes);
	}
	
	
	public function find_biggest_photo($image_urls, $wanted_size, $avail_sizes){
		if(!$this->isEmpty(@$image_urls[$wanted_size])) return $image_urls[$wanted_size];	
		$wanted_size_pos = array_search($wanted_size, $avail_sizes);
		for ($i=$wanted_size_pos; $i < 7; $i++) { 
			if(!$this->isEmpty(@$image_urls[$avail_sizes[$i]])) return $image_urls[$avail_sizes[$i]];	
		}
		for ($i=$wanted_size_pos; $i >= 0; $i--) { 
			if(!$this->isEmpty(@$image_urls[$avail_sizes[$i]])) return $image_urls[$avail_sizes[$i]];	
		}
	}

	
	public function isEmpty($stringOrArray) {
	    if(is_array($stringOrArray)) {
	        foreach($stringOrArray as $value) {
	            if(!$this->isEmpty($value)) {
	                return false;
	            }
	        }
	        return true;
	    }

	    return !strlen($stringOrArray);  // this properly checks on empty string ('')
	}
	
	
	public function get_facebook_timeline_image(){
		$return = '';
		
		$object_id = ZTSliderFunctions::getVal($this->postData, 'object_id', '');
		$picture = ZTSliderFunctions::getVal($this->postData, 'picture', '');
		if(!empty($object_id)){
			$return = 'https://graph.facebook.com/'.ZTSliderFunctions::getVal($this->postData, 'object_id', '').'/picture';//$photo->picture;
		}elseif(!empty($picture)) {
			
			$image_url = $this->decode_facebook_url(ZTSliderFunctions::getVal($this->postData, 'picture', ''));
			$image_url = parse_str(parse_url($image_url, PHP_URL_QUERY), $array);
			$image_url = explode('&', $array['url']);
			$return = $image_url[0];
		}
		return apply_filters('revslider_slide_get_facebook_timeline_image', $return, $object_id, $picture, $this);
	}
	
	
	private function decode_facebook_url($url) {
		$url = str_replace('u00253A',':',$url);
		$url = str_replace('\u00255C\u00252F','/',$url);
		$url = str_replace('u00252F','/',$url);
		$url = str_replace('u00253F','?',$url);
		$url = str_replace('u00253D','=',$url);
		$url = str_replace('u002526','&',$url);
		return $url;
	}
	
	/**
	 * Encode the flickr ID for URL (base58)
	 *
	 * @since    5.0
	 * @param    string    $num 	flickr photo id
	 */
	private function base_encode($num, $alphabet='123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ') {
		$base_count = strlen($alphabet);
		$encoded = '';
		while ($num >= $base_count) {
			$div = $num/$base_count;
			$mod = ($num-($base_count*intval($div)));
			$encoded = $alphabet[$mod] . $encoded;
			$num = intval($div);
		}
		if ($num) $encoded = $alphabet[$num] . $encoded;
		return $encoded;
	}
	
	/**
	 * 
	 * init slide by post data
	 */
	public function initByPostData($postData, RevSliderSlide $slideTemplate, $sliderID){
		$this->postData = apply_filters('revslider_slide_initByPostData', $postData, $slideTemplate, $sliderID, $this);
		
		$postID = $postData["ID"];
		
		$slideTemplateID = get_post_meta($postID, 'slide_template', true);
		if($slideTemplateID == '') $slideTemplateID = 'default';
		
		if(!empty($slideTemplateID) && is_numeric($slideTemplateID)){
				//init by local template, if fail, init by global (slider) template
			try{
				//check if slide exists
				$slideTemplateLocal = new ZTSliderSlide();
				if($slideTemplateLocal->isSlideByID($slideTemplateID)){
					$slideTemplateLocal->initByID($slideTemplateID);
					$this->initBySlide($slideTemplateLocal);
				}else{
					$this->initBySlide($slideTemplate);
				}
			}
			catch(Exception $e){
				$this->initBySlide($slideTemplate);
			}
			
		}else{
			//init by global template
			$this->initBySlide($slideTemplate);
		}
		
		//set some slide params
		$this->id = $postID;
		$this->params["title"] = ZTSliderFunctions::getVal($postData, "post_title");
		
		if(isset($this->params['enable_link']) && $this->params['enable_link'] == "true" && isset($this->params['link_type']) && $this->params['link_type'] == "regular"){
			$link = get_permalink($postID);
			$this->params["link"] = str_replace(array("%link%", '{{link}}'), $link, $this->params["link"]);
			$this->params["link"] = str_replace('-', '_REVSLIDER_', $this->params["link"]);
			
			//process meta tags:
			$arrMatches = array();
			preg_match('/%meta:\w+%/', $this->params["link"], $arrMatches);
			
			foreach($arrMatches as $match){
				$meta = str_replace("%meta:", "", $match);
				$meta = str_replace("%","",$meta);
				$meta = str_replace('_REVSLIDER_', '-', $meta);
				$metaValue = get_post_meta($postID,$meta,true);
				$this->params["link"] = str_replace($match,$metaValue,$this->params["link"]);
			}
			
			
			$arrMatches = array();
			preg_match('/{{meta:\w+}}/', $this->params["link"], $arrMatches);
			
			foreach($arrMatches as $match){
				$meta = str_replace("{{meta:", "", $match);
				$meta = str_replace("}}","",$meta);
				$meta = str_replace('_REVSLIDER_', '-', $meta);
				$metaValue = get_post_meta($postID,$meta,true);
				$this->params["link"] = str_replace($match,$metaValue,$this->params["link"]);
			}
			
			$this->params["link"] = str_replace('_REVSLIDER_','-',$this->params["link"]);
			
		}
		
		$status = $postData["post_status"];
		
		if($status == "publish")
			$this->params["state"] = "published";
		else
			$this->params["state"] = "unpublished";
		
		if($this->params["background_type"] == 'image'){ //if image is choosen, use featured image as background
			//set image
			$thumbID = RevSliderFunctionsWP::getPostThumbID($postID);
			
			if(!empty($thumbID))
				$this->setImageByImageID($thumbID);
			
		}
		
		//replace placeholders in layers:
		$this->setLayersByPostData($postData, $sliderID);
	}


	public function getExcerptById($id,$excerpt_limit) {

	}


	
	/**
	 * 
	 * replace layer placeholders by post data
	 */
	private function setLayersByPostData($postData,$sliderID){
		
		$postID = $postData["id"];
		
		$attr = array();
		$attr['title'] = ZTSliderFunctions::getVal($postData, "post_title");
		
		//check if we are woocommerce or not
		$excerpt_limit = $this->getSliderParam($sliderID,"excerpt_limit",55,ZTSlider::VALIDATE_NUMERIC);
		$excerpt_limit = (int)$excerpt_limit;

		
		$attr['excerpt'] = $this->getExcerptById($postID, $excerpt_limit);
		
		$attr['alias'] = ZTSliderFunctions::getVal($postData, "post_name");
		
		$attr['content'] = ZTSliderFunctions::getVal($postData, "post_content");
		
		$attr['link'] = get_permalink($postID);
		
		$postDate = ZTSliderFunctions::getVal($postData, "post_date_gmt");
		$attr['postDate'] = RevSliderFunctionsWP::convertPostDate($postDate);
		
		$dateModified = ZTSliderFunctions::getVal($postData, "post_modified");
		$attr['dateModified'] = RevSliderFunctionsWP::convertPostDate($dateModified);
		
		$authorID = ZTSliderFunctions::getVal($postData, "post_author");
		$attr['authorName'] = RevSliderFunctionsWP::getUserDisplayName($authorID);
		
		$postCatsIDs = $postData["post_category"];
		$attr['catlist'] = RevSliderFunctionsWP::getCategoriesHtmlList($postCatsIDs);
		$attr['catlist_raw'] = strip_tags(RevSliderFunctionsWP::getCategoriesHtmlList($postCatsIDs));
		$attr['taglist'] = RevSliderFunctionsWP::getTagsHtmlList($postID);
		
		$ptid = get_post_thumbnail_id($postID);
		
		$img_sizes = ZTSliderFunctions::get_all_image_sizes();
		$attr['img_urls'] = array();
		foreach($img_sizes as $img_handle => $img_name){
			$featured_image_url = wp_get_attachment_image_src($ptid, $img_handle);
			if($featured_image_url !== false){
				$attr['img_urls'][$img_handle] = array(
					'url' => $featured_image_url[0],
					'tag' => '<img src="'.$featured_image_url[0].'" width="'.$featured_image_url[1].'" height="'.$featured_image_url[2].'" data-no-retina />'
				);
			}
		}
		
		$attr['numComments'] = ZTSliderFunctions::getVal($postData, "comment_count");
		
		$attr = apply_filters('revslider_slide_setLayersByPostData_post', $attr, $postData, $sliderID, $this);
		
		foreach($this->arrLayers as $key=>$layer){
			
			$text = ZTSliderFunctions::getVal($layer, "text");
			$text = apply_filters('revslider_mod_meta', $text, $postID, $postData); //option to add your own filter here to modify meta to your likings
			
			$text = $this->set_post_data($text, $attr, $postID);
			
			$layer["text"] = $text;
			
			$all_actions = ZTSliderFunctions::getVal($layer, 'layer_action', array());
			if(!empty($all_actions)){
				$a_image_link = ZTSliderFunctions::cleanStdClassToArray(ZTSliderFunctions::getVal($all_actions, 'image_link', array()));
				if(!empty($a_image_link)){
					foreach($a_image_link as $ik => $ilink){
						$ilink = $this->set_post_data($ilink, $attr, $postID);
						$a_image_link[$ik] = $ilink;
					}
					$layer['layer_action']->image_link = $a_image_link;
				}
			}
			
			$this->arrLayers[$key] = $layer;
		}
		
		
		/*$params = $this->getParams();
		
		foreach($params as $key => $param){ //set metas on all params except arrays
			if(!is_array($param)){
				$pa = $this->set_post_data($param, $attr, $postID);
				$this->setParam($key, $pa);
			}
		}*/
		
		for($mi=1;$mi<=10;$mi++){ //set params to the post data
			$pa = $this->getParam('params_'.$mi, '');
			$pa = $this->set_post_data($pa, $attr, $postID);
			$this->setParam('params_'.$mi, $pa);
		}
		
		$param_list = array('id_attr', 'class_attr', 'data_attr');
		//set params to the stream data
		foreach($param_list as $p){
			$pa = $this->getParam($p, '');
			$pa = $this->set_post_data($pa, $attr, $postID);
			$this->setParam($p, $pa);
		}
		
	}
	
	
	public function set_post_data($text, $attr, $post_id){
		$img_sizes = ZTSliderFunctions::get_all_image_sizes();
		$title = (isset($attr['title'])) ? $attr['title'] : '';
		$excerpt = (isset($attr['excerpt'])) ? $attr['excerpt'] : '';
		$alias = (isset($attr['alias'])) ? $attr['alias'] : '';
		$content = (isset($attr['content'])) ? $attr['content'] : '';
		$link = (isset($attr['link'])) ? $attr['link'] : '';
		$postDate = (isset($attr['postDate'])) ? $attr['postDate'] : '';
		$dateModified = (isset($attr['dateModified'])) ? $attr['dateModified'] : '';
		$authorName = (isset($attr['authorName'])) ? $attr['authorName'] : '';
		$numComments = (isset($attr['numComments'])) ? $attr['numComments'] : '';
		$catlist = (isset($attr['catlist'])) ? $attr['catlist'] : '';
		$catlist_raw = (isset($attr['catlist_raw'])) ? $attr['catlist_raw'] : '';
		$taglist = (isset($attr['taglist'])) ? $attr['taglist'] : '';
		
		//add filter for addon metas
		$text = apply_filters( 'rev_slider_insert_meta', $text, $post_id );

		$text = str_replace(array('%title%', '{{title}}'), $title, $text);
		$text = str_replace(array('%excerpt%', '{{excerpt}}'), $excerpt, $text);
		$text = str_replace(array('%alias%', '{{alias}}'), $alias, $text);
		$text = str_replace(array('%content%', '{{content}}'), $content, $text);
		$text = str_replace(array('%link%', '{{link}}'), $link, $text);
		$text = str_replace(array('%date%', '{{date}}'), $postDate, $text);
		$text = str_replace(array('%date_modified%', '{{date_modified}}'), $dateModified, $text);
		$text = str_replace(array('%author_name%', '{{author_name}}'), $authorName, $text);
		$text = str_replace(array('%num_comments%', '{{num_comments}}'), $numComments, $text);
		$text = str_replace(array('%catlist%', '{{catlist}}'), $catlist, $text);
		$text = str_replace(array('%catlist_raw%', '{{catlist_raw}}'), $catlist_raw, $text);
		$text = str_replace(array('%taglist%', '{{taglist}}'), $taglist, $text);
		
		foreach($img_sizes as $img_handle => $img_name){
			$url = (isset($attr['img_urls']) && isset($attr['img_urls'][$img_handle]) && isset($attr['img_urls'][$img_handle]['url'])) ? $attr['img_urls'][$img_handle]['url'] : '';
			$tag = (isset($attr['img_urls']) && isset($attr['img_urls'][$img_handle]) && isset($attr['img_urls'][$img_handle]['tag'])) ? $attr['img_urls'][$img_handle]['tag'] : '';
			
			$text = str_replace(array('%featured_image_url_'.$img_handle.'%', '{{featured_image_url_'.$img_handle.'}}'), $url, $text);
			$text = str_replace(array('%featured_image_'.$img_handle.'%', '{{featured_image_'.$img_handle.'}}'), $tag, $text);
		}


		//process meta tags:
		$text = str_replace('-', '_REVSLIDER_', $text);
		
		$arrMatches = array();
		preg_match_all('/%meta:\w+%/', $text, $arrMatches);

		foreach($arrMatches as $matched){
			
			foreach($matched as $match) {
			
				$meta = str_replace("%meta:", "", $match);
				$meta = str_replace("%","",$meta);
				$meta = str_replace('_REVSLIDER_', '-', $meta);
				$metaValue = get_post_meta($post_id,$meta,true);
				
				$text = str_replace($match,$metaValue,$text);	
			}
		}
		
		$arrMatches = array();
		preg_match_all('/{{meta:\w+}}/', $text, $arrMatches);

		foreach($arrMatches as $matched){
			foreach($matched as $match) {
				$meta = str_replace("{{meta:", "", $match);
				$meta = str_replace("}}","",$meta);
				$meta = str_replace('_REVSLIDER_', '-', $meta);
				$metaValue = get_post_meta($post_id,$meta,true);
				
				$text = str_replace($match,$metaValue,$text);	
			}
		}
		
		$arrMatches = array();
		preg_match_all("/{{content:\w+[\:]\w+}}/", $text, $arrMatches);
		foreach($arrMatches as $matched){
			foreach($matched as $match) {
				//now check length and type
				
				$meta = str_replace("{{content:", "", $match);
				$meta = str_replace("}}","",$meta);
				$meta = str_replace('_REVSLIDER_', '-', $meta);
				$vals = explode(':', $meta);
				
				if(count($vals) !== 2) continue; //not correct values
				$vals[1] = intval($vals[1]); //get real number
				if($vals[1] === 0 || $vals[1] < 0) continue; //needs to be at least 1 
				
				if($vals[0] == 'words'){
					$metaValue = explode(' ', strip_tags($content), $vals[1]+1);
					if(is_array($metaValue) && count($metaValue) > $vals[1]) array_pop($metaValue);
					$metaValue = implode(' ', $metaValue);
				}elseif($vals[0] == 'chars'){
					$metaValue = substr(strip_tags($content), 0, $vals[1]);
				}else{
					continue;
				}
				
				$text = str_replace($match,$metaValue,$text);	
			}
		}
		
		$text = str_replace('_REVSLIDER_','-',$text);
		
		return $text;
	}
	
	
	/**
	 * get slide data by id
	 * @since: 5.2.0
	 */
	public function getDataByID($slideid){
		$return = false;
		
		if(strpos($slideid, 'static_') !== false){
			$sliderID = str_replace('static_', '', $slideid);
			$record = $this->db->fetch(RevSliderGlobals::$table_static_slides, $this->db->prepare("slider_id = %s", array($sliderID)));
			if(!empty($record)){
				$return = $record[0];
			}
			//$return = false;
		}else{
			$record = $this->db->fetchSingle(RevSliderGlobals::$table_slides, $this->db->prepare("id = %d", array($slideid)));
			$return = $record;
		}

	}
	
	
	/**
	 * init the slider by id
	 */
	public function initByID($slideid){
		try{
			if(strpos($slideid, 'static_') !== false){
				$this->static_slide = true;
				$sliderID = str_replace('static_', '', $slideid);
				
				ZTSliderFunctions::validateNumeric($sliderID,"Slider ID");
				$this->db->setQuery('SELECT * FROM #__zt_layerslider_static_slides WHERE slider_id='.$sliderID);
				$record = $this->db->loadAssoc();
				
				if(empty($record)){
					try{
						//create a new static slide for the Slider and then use it
						$slide_id = $this->createSlide($sliderID,"",true);
						$this->db->setQuery('SELECT * FROM #__zt_layerslider_static_slides WHERE slider_id='.$sliderID);
						$record = $this->db->loadAssoc();
						$this->initByData($record);
					}catch(Exception $e){}
				}else{
					$this->initByData($record);
				}
			}else{
				ZTSliderFunctions::validateNumeric($slideid,"Slide ID");
				$this->db->setQuery('SELECT * FROM #__zt_layerslider_slide WHERE id='.$slideid);
				$record = $this->db->loadAssoc();
				$this->initByData($record);
			}
		}catch(Exception $e){
			$message = $e->getMessage();
			echo $message;
			exit;
		}
	}
	
	
	/**
	 * Check if Slide Exists with given ID
	 * @since: 5.0
	 */
	public static function isSlideByID($slideid){
		$db = new ZTSliderDB();
		try{
			if(strpos($slideid, 'static_') !== false){
				
				$sliderID = str_replace('static_', '', $slideid);
				
				ZTSliderFunctions::validateNumeric($sliderID,"Slider ID");
				
				$record = $db->fetch(RevSliderGlobals::$table_static_slides, $db->prepare("slider_id = %s", array($sliderID)));
				
				if(empty($record)) return false;
				
				return true;
				
			}else{
				
				$record = $db->fetchSingle(RevSliderGlobals::$table_slides, $db->prepare("id = %s", array($slideid)));
				
				if(empty($record)) return false;
				
				return true;
				
			}
		}catch(Exception $e){
			return false;
		}
	}
	
	
	/**
	 * 
	 * init the slider by id
	 */
	public function initByStaticID($slideid){
	
		ZTSliderFunctions::validateNumeric($slideid,"Slide ID");

		$this->db->setQuery('SELECT * FROM #__zt_layerslider_static_slides WHERE id='.(int)$slideid);
		$record = $this->db->loadAssoc();
		
		$this->initByData($record);
	}
	
	
	/**
	 * 
	 * getStaticSlide
	 */
	public function getStaticSlideID($sliderID){
		
		ZTSliderFunctions::validateNumeric($sliderID,"Slider ID");
		$this->db->setQuery('SELECT * FROM #__zt_layerslider_static_slides WHERE slider_id='.$sliderID);
		
		$record = $this->db->loadAssoc();
		
		if(empty($record)){
			return false;
		}else{
			return $record['id'];
		}
	}
	
	
	/**
	 * 
	 * set slide image by image id
	 */
	private function setImageByImageID($imageID){
		
		$imageID = apply_filters('revslider_slide_setImageByImageID', $imageID, $this);
		
		$imgResolution = ZTSliderFunctions::getVal($this->params, 'image_source_type', 'full');
		
		$this->imageID = $imageID;
		
		$this->imageUrl = RevSliderFunctionsWP::getUrlAttachmentImage($imageID, $imgResolution);
		$this->imageThumb = RevSliderFunctionsWP::getUrlAttachmentImage($imageID,RevSliderFunctionsWP::THUMB_MEDIUM);
		
		if(empty($this->imageUrl))
			return(false);
		
		$this->params["background_type"] = "image";
		
		if(is_ssl()){
			$this->imageUrl = str_replace("http://", "https://", $this->imageUrl);
		}
		
		$this->imageFilepath = RevSliderFunctionsWP::getImagePathFromURL($this->imageUrl);
		$realPath = RevSliderFunctionsWP::getPathContent().$this->imageFilepath;
		
		if(file_exists($realPath) == false || is_file($realPath) == false)
			$this->imageFilepath = "";
		
		$this->imageFilename = basename($this->imageUrl);
	}
	
	
	/**
	 * 
	 * set children array
	 */
	public function setArrChildren($arrChildren){
		$this->arrChildren = $arrChildren;
	}
	
	
	/**
	 * 
	 * get children array
	 */
	public function getArrChildren(){
		
		$this->validateInited();
		
		if($this->arrChildren === null){
			$slider = new ZTSlider();
			$slider->initByID($this->sliderID);
			$this->arrChildren = $slider->getArrSlideChildren($this->id);
		}

		return $this->arrChildren;

	}
	
	/**
	 * 
	 * return if the slide from post
	 */
	public function isFromPost(){
		return !empty($this->postData);
	}
	
	
	/**
	 * 
	 * get post data
	 */
	public function getPostData(){
		return($this->postData);
	}
	
	
	/**
	 * 
	 * get children array as is
	 */
	public function getArrChildrenPure(){
		return($this->arrChildren);
	}
	
	/**
	 * 
	 * return if the slide is parent slide
	 */
	public function isParent(){
		$parentID = $this->getParam("parentid","");
		return(!empty($parentID));
	}
	
	
	/**
	 * 
	 * get slide language
	 */
	public function getLang(){
		$lang = $this->getParam("lang","all");
		return($lang);
	}
	
	/**
	 * 
	 * return parent slide. If the slide is parent, return this slide.
	 */
	public function getParentSlide(){
		$parentID = $this->getParam("parentid","");
		if(empty($parentID))
			return($this);
			
		$parentSlide = new RevSlide();
		$parentSlide->initByID($parentID);
		return($parentSlide);
	}
	
	/**
	 * return parent slide id
	 * @since: 5.0
	 */
	public function getParentSlideID(){
		$parentID = $this->getParam("parentid","");
		
		return $parentID;
	}
	
	/**
	 * 
	 * get array of children id's
	 */
	public function getArrChildrenIDs(){
		$arrChildren = $this->getArrChildren();
		$arrChildrenIDs = array();
		foreach($arrChildren as $child){
			$childID = $child->getID();
			$arrChildrenIDs[] = $childID;
		}
		
		return($arrChildrenIDs);
	}
	
	
	/**
	 * 
	 * get array of children array and languages, the first is current language.
	 */
	public function getArrChildrenLangs($includeParent = true){
		$this->validateInited();
		$slideID = $this->id;
		
		if($includeParent == true){
			$lang = $this->getParam("lang","all");
			$arrOutput = array();
			$arrOutput[] = array("slideid"=>$slideID,"lang"=>$lang,"isparent"=>true);
		}
		
		$arrChildren = $this->getArrChildren();
		
		foreach($arrChildren as $child){
			$childID = $child->getID();
			$childLang = $child->getParam("lang","all");
			$arrOutput[] = array("slideid"=>$childID,"lang"=>$childLang,"isparent"=>false);
		}
		
		return($arrOutput);
	}
	
	/**
	 * 
	 * get children language codes (including current slide lang code)
	 */
	public function getArrChildLangCodes($includeParent = true){
		$arrLangsWithSlideID = $this->getArrChildrenLangs($includeParent);
		$arrLangCodes = array();
		foreach($arrLangsWithSlideID as $item){
			$lang = $item["lang"];
			$arrLangCodes[$lang] = $lang;
		}
		
		return($arrLangCodes);
	}
	
	
	/**
	 * get slide ID
	 */
	public function getID(){
		return($this->id);
	}
	
	/**
	 * set slide ID
	 */
	public function setID($id){
		$this->id = $id;
	}
	
	/**
	 * get slide title
	 */
	public function getTitle(){
		return $this->getParam("title","Slide");
	}
	
	
	/**
	 * get slide order
	 */
	public function getOrder(){
		$this->validateInited();
		return $this->slideOrder;
	}
	
	
	/**
	 * get layers in json format
	 */
	public function getLayers(){
		$this->validateInited();
		return $this->arrLayers;
	}
	
	/**
	 * get layers in json format
	 * since: 5.0
	 */
	public function getLayerID_by_unique_id($unique_id, $static_slide){
		$this->validateInited();
		
		if(strpos($unique_id, 'static-') !== false){
			$unique_id = str_replace('static-', '', $unique_id);
			$layers = $static_slide->getLayers();
			if(!empty($layers)){
				foreach($layers as $l){
					$uid = ZTSliderFunctions::getVal($l, 'unique_id');
					if($uid == $unique_id){
						return ZTSliderFunctions::getVal($l, 'attrID');
					}
				}
			}
		}else{
			foreach($this->arrLayers as $l){
				
				$uid = ZTSliderFunctions::getVal($l, 'unique_id');
				if($uid == $unique_id){
					return ZTSliderFunctions::getVal($l, 'attrID');
				}
			}
		}
		
		return '';
	}
	
	
	/**
	 * save layers to the database
	 * @since: 5.0
	 */
	public function saveLayers(){
		$this->validateInited();
		$table = ($this->static_slide) ? RevSliderGlobals::$table_static_slides : RevSliderGlobals::$table_slides;
		
		$this->arrLayers = apply_filters('revslider_slide_saveLayers', $this->arrLayers, $this->static_slide, $this);
		
		$this->db->update($table, array('layers' => json_encode($this->arrLayers)),array('id'=>$this->id));
	}
	
	/**
	 * save params to the database
	 * @since: 5.0
	 */
	public function saveParams(){
		$this->validateInited();
		$table = ($this->static_slide) ? RevSliderGlobals::$table_static_slides : RevSliderGlobals::$table_slides;
		
		$this->params = apply_filters('revslider_slide_saveParams', $this->params, $this->static_slide, $this);
		
		$this->db->update($table, array('params' => json_encode($this->params)),array('id'=>$this->id));
	}
	
	
	/**
	 * modify layer links for export
	 */
	public function getLayersForExport($useDummy = false){
		$this->validateInited();
		$arrLayersNew = array();
		foreach($this->arrLayers as $key=>$layer){
			$imageUrl = ZTSliderFunctions::getVal($layer, "image_url");
			if(!empty($imageUrl))
				$layer["image_url"] = $imageUrl;
			
			$arrLayersNew[] = $layer;
		}
		return $arrLayersNew;
	}
	
	
	/**
	 * get params for export
	 */
	public function getParamsForExport(){
		$arrParams = $this->getParams();
		$urlImage = ZTSliderFunctions::getVal($arrParams, "image");
		if(!empty($urlImage))
			$arrParams["image"] = $urlImage;
		
		//check if we are transparent or solid and remove unneeded image
		$bgtype = ZTSliderFunctions::getVal($arrParams, "background_type", 'transparent');
		switch($bgtype){
			case 'transparent':
			case 'trans':
			case 'solid':
				$arrParams["image"] = '';
			break;
		}
		return $arrParams;

	}
	
	
	/**
	 * normalize layers text, and get layers
	 */
	public function getLayersNormalizeText(){
		$arrLayersNew = array();
		foreach ($this->arrLayers as $key=>$layer){
			$text = $layer["text"];
			$text = addslashes($text);
			$layer["text"] = $text;
			$arrLayersNew[] = $layer;
		}
	}
	

	/**
	 * get slide params
	 */
	public function getParams(){
		$this->validateInited();
		return $this->params;
	}
	

	/**
	 * get slide settings
	 * @since: 5.0
	 */
	public function getSettings(){
		$this->validateInited();
		return $this->settings;
	}

	
	/**
	 * get parameter from params array. if no default, then the param is a must!
	 */
	function getParam($name,$default=null){
		
		if($default === null){
			//if(!array_key_exists($name, $this->params))
			$default = '';
		}
		
		return ZTSliderFunctions::getVal($this->params, $name, $default);
	}
	
	
	/**
	 * set parameter
	 * @since: 5.0
	 */
	public function setParam($name, $value){
		
		$this->params[$name] = $value;
		
	}
	
	
	/**
	 * get image filename
	 */
	public function getImageFilename(){
		return($this->imageFilename);
	}
	
	
	/**
	 * get image filepath
	 */
	public function getImageFilepath(){
		return($this->imageFilepath);
	}
	
	
	/**
	 * get image url
	 */
	public function getImageUrl(){
		
		return($this->imageUrl);
	}
	
	
	/**
	 * get image id
	 */
	public function getImageID(){
		return($this->imageID);
	}
	
	/**
	 * get thumb url
	 */
	public function getThumbUrl(){
		$thumbUrl = $this->imageUrl;
		if(!empty($this->imageThumb))
			$thumbUrl = $this->imageThumb;
			
		return($thumbUrl);
	}
	
	
	/**
	 * get the slider id
	 */
	public function getSliderID(){
		return($this->sliderID);
	}
	
	/**
	 * get slider param
	 */
	private function getSliderParam($sliderID,$name,$default,$validate=null){
		
		if(empty($this->slider)){
			$this->slider = new ZTSlider();
			$this->slider->initByID($sliderID);
		}
		
		$param = $this->slider->getParam($name,$default,$validate);
		
		return($param);
	}
	
	
	/**
	 * validate that the slider exists
	 */
	private function validateSliderExists($sliderID){
		$slider = new ZTSlider();
		$slider->initByID($sliderID);
	}
	
	/**
	 * validate that the slide is inited and the id exists.
	 */
	private function validateInited(){
		if(empty($this->id))
			ZTSliderFunctions::throwError("The slide is not initialized!!!");
	}
	
	
	/**
	 * create the slide (from image)
	 */
	public function createSlide($sliderID,$obj="",$static = false){
		
		$imageID = null;
		
		if(is_array($obj)){
			$urlImage = ZTSliderFunctions::getVal($obj, "url");
			$imageID = ZTSliderFunctions::getVal($obj, "id");
		}else{
			$urlImage = $obj;
		}
		
		//get max order
		$slider = new ZTSlider();
		$slider->initByID($sliderID);
		$maxOrder = $slider->getMaxOrder();
		$order = $maxOrder+1;
		
		$params = array();
		if(!empty($urlImage)){
			$params["background_type"] = "image";
			$params["image"] = $urlImage;
			if(!empty($imageID))
				$params["image_id"] = $imageID;
				
		}else{	//create transparent slide
			
			$params["background_type"] = "trans";
		}

			
		$jsonParams  = json_encode($params);
		$jsonParams  = str_replace((str_replace('"','',json_encode(JUri::root()))),'',$jsonParams);
		
		$arrInsert = array(	
						"attribs"=>$jsonParams,
						"slider_id"=>$sliderID,
						"layers"=>""
					);

		$lang = JFactory::getApplication()->getUserStateFromRequest('com_zt_layerslider.filter.language','filter_language', '');
		if(!$static)
			$arrInsert["ordering"] = $order;
		
		if(!$static) {
			$this->db->setQuery('INSERT INTO #__zt_layerslider_slide VALUES (NULL ,'.$arrInsert['slider_id'].','.$arrInsert["ordering"].',1,"'.$this->db->escape($arrInsert["attribs"]).'","'.$this->db->escape($arrInsert["layers"]).'",1,"'.$lang.'","")');
			try{
				$this->db->execute();
				$slideID = $this->db->insertid();
			} catch (Exception $e) {
				$m = $e->getMessage();
				ZTSliderFunctions::ajaxResponseError($m);
			}

		}else {
			$this->db->setQuery('INSERT INTO #__zt_layerslider_static_slides VALUES (NULL ,'.$arrInsert['slider_id'].',"'.$this->db->escape($arrInsert["attribs"]).'","'.$this->db->escape($arrInsert["layers"]).'","")');
			try{
				$this->db->execute();
				$slideID = $this->db->insertid();

			} catch (Exception $e) {
				$m = $e->getMessage();
				ZTSliderFunctions::ajaxResponseError($m);
			}


		}

		return $slideID;
	}
	
	/**
	 * 
	 * update slide image from data
	 */
	public function updateSlideImageFromData($data){
		
		$sliderID = ZTSliderFunctions::getVal($data, "slider_id");
		$slider = new ZTSlider();
		$slider->initByID($sliderID);
		
		$slideID = ZTSliderFunctions::getVal($data, "slide_id");
		$urlImage = ZTSliderFunctions::getVal($data, "url_image");
		ZTSliderFunctions::validateNotEmpty($urlImage);
		$imageID = ZTSliderFunctions::getVal($data, "image_id");
		if($slider->isSlidesFromPosts()){
			
			if(!empty($imageID))
				RevSliderFunctionsWP::updatePostThumbnail($slideID, $imageID);
			
		}elseif($slider->isSlidesFromStream() !== false){
			//do nothing
		}else{
			$this->initByID($slideID);
			
			$arrUpdate = array();
			$arrUpdate["image"] = $urlImage;			
			$arrUpdate["image_id"] = $imageID;
			
			$this->updateParamsInDB($arrUpdate);
		}
		
		return $urlImage;
	}
	
	
	
	/**
	 * 
	 * update slide parameters in db
	 */
	protected function updateParamsInDB($arrUpdate = array()){
		$this->validateInited();
		
		$this->params = array_merge($this->params,$arrUpdate);

		$jsonParams = json_encode($this->params);
		$jsonParams  = str_replace((str_replace('"','',json_encode(JUri::root()))),'',$jsonParams);

		$arrDBUpdate = array("params"=>$jsonParams);


		$this->db->setQuery('UPDATE #__zt_layerslider_slide SET attribs="'.$this->db->escape($arrDBUpdate['params']).'" WHERE id='.(int)$this->id);

		try {
			$this->db->execute();
		} catch (Exception $e) {
			$ms = $e->getMessage();
			ZTSliderFunctions::ajaxResponseError($ms);
		}

	}
	
	
	/**
	 * 
	 * update current layers in db
	 */
	protected function updateLayersInDB($arrLayers = null){
		$this->validateInited();
		
		if($arrLayers === null)
			$arrLayers = $this->arrLayers;
		
		$jsonLayers = json_encode($arrLayers);
		$arrDBUpdate = array("layers"=>$jsonLayers);
		
		$this->db->update(RevSliderGlobals::$table_slides,$arrDBUpdate,array("id"=>$this->id));
	} 
	
	
	/**
	 * 
	 * update parent slideID 
	 */
	public function updateParentSlideID($parentID){
		$arrUpdate = array();
		$arrUpdate["parentid"] = $parentID;
		$this->updateParamsInDB($arrUpdate);
	}
	
	
	/**
	 * 
	 * sort layers by order
	 */
	private function sortLayersByOrder($layer1,$layer2){
		$layer1 = (array)$layer1;
		$layer2 = (array)$layer2;
		
		$order1 = ZTSliderFunctions::getVal($layer1, "order",1);
		$order2 = ZTSliderFunctions::getVal($layer2, "order",2);
		if($order1 == $order2)
			return(0);
		
		return($order1 > $order2);
	}
	
	
	/**
	 * 
	 * go through the layers and fix small bugs if exists
	 */
	private function normalizeLayers($arrLayers){
		
		usort($arrLayers,array($this,"sortLayersByOrder"));
		
		$arrLayersNew = array();
		foreach ($arrLayers as $key=>$layer){
			
			$layer = (array)$layer;
			
			//set type
			$type = ZTSliderFunctions::getVal($layer, "type","text");
			$layer["type"] = $type;
			
			//normalize position:
			if(is_object($layer["left"])){
				foreach($layer["left"] as $key => $val){
					$layer["left"]->$key = round($val);
				}
			}else{
				$layer["left"] = round($layer["left"]);
			}
			if(is_object($layer["top"])){
				foreach($layer["top"] as $key => $val){
					$layer["top"]->$key = round($val);
				}
			}else{
				$layer["top"] = round($layer["top"]);
			}
			
			//unset order
			unset($layer["order"]);
			
			//modify text
			$layer["text"] = stripcslashes($layer["text"]);
			
			$arrLayersNew[] = $layer;
		}
		
		return $arrLayersNew;
	}  
	
	
	
	/**
	 * 
	 * normalize params
	 */
	private function normalizeParams($params){
		
		$urlImage = ZTSliderFunctions::getVal($params, "image_url");
		
		//init the id if absent
		$params["image_id"] = ZTSliderFunctions::getVal($params, "image_id");
		
		$params["image"] = $urlImage;
		unset($params["image_url"]);
		
		if(isset($params["video_description"]))
			$params["video_description"] = ZTSliderFunctions::normalizeTextareaContent($params["video_description"]);
		
		return $params;
	}
	
	
	/**
	 * 
	 * update slide from data
	 * @param $data
	 */
	public function updateStaticSlideFromData($data){
		
		$slideID = ZTSliderFunctions::getVal($data, "slideid");
		$this->initByStaticID($slideID);
		
		$params = ZTSliderFunctions::getVal($data, "params");
		$params = $this->normalizeParams($params);
		
		//treat layers
		$layers = ZTSliderFunctions::getVal($data, "layers");
		
		
		if(gettype($layers) == "string"){
			$layersStrip = stripslashes($layers);
			$layersDecoded = json_decode($layersStrip);
			if(empty($layersDecoded))
				$layersDecoded = json_decode($layers);
			
			$layers = ZTSliderFunctions::convertStdClassToArray($layersDecoded);
		}
		
		if(empty($layers) || gettype($layers) != "array")
			$layers = array();
		
		$layers = $this->normalizeLayers($layers);
		
		$settings = ZTSliderFunctions::getVal($data, "settings");
		
		
		$arrUpdate = array();
		$arrUpdate["layers"] = json_encode($layers);
		$arrUpdate["params"] = json_encode($params);
		$arrUpdate["settings"] = json_encode($settings);

		$this->db->setQuery('UPDATE #__zt_layerslider_static_slides SET layers="'.$this->db->escape(json_encode($layers)).'",params="'.$this->db->escape(json_encode($params)).'",settings="'.$this->db->escape(json_encode($settings)).'" WHERE id='.$this->id);
//		$this->db->update(RevSliderGlobals::$table_static_slides,$arrUpdate,array("id"=>$this->id));
		//RevSliderOperations::updateDynamicCaptions();
		try{
			$this->db->execute();
		} catch (Exception $e) {
			ZTSliderFunctions::ajaxResponseError($e->getMessage());
		}
	}
	
	
	
	/**
	 * 
	 * delete slide by slideid
	 */
	public function deleteSlide(){
		$this->validateInited();
		$this->db->setQuery('DELETE FROM #__zt_layerslider_slide WHERE id='.$this->id);
		$this->db->execute();
		return $this->id;
	}

	
	
	/**
	 * 
	 * delete slide children
	 */
	public function deleteChildren(){
		$this->validateInited();
		$arrChildren = $this->getArrChildren();
		foreach($arrChildren as $child)
			$child->deleteSlide();
	}
	
	
	/**
	 * 
	 * delete slide from data
	 */
	public function deleteSlideFromData($data){
		
		$sliderID = ZTSliderFunctions::getVal($data, "sliderID");
		$slider = new ZTSlider();
		$slider->initByID($sliderID); 			
		
		//delete slide
		$slideID = ZTSliderFunctions::getVal($data, "slideID");
		$this->initByID($slideID);
		$this->deleteChildren();
		$this->deleteSlide();
		
	}
	
	
	/**
	 * set params from client
	 */
	public function setParams($params){
		$params = $this->normalizeParams($params);
		$this->params = $params;
	}
	
	
	/**
	 * 
	 * set layers from client
	 */
	public function setLayers($layers){
		$layers = $this->normalizeLayers($layers);
		$this->arrLayers = $layers;
		
	}
	
	
	
	/**
	 * set layers from client, do not normalize as this results in loosing the order
	 * @since: 5.0
	 */
	public function setLayersRaw($layers){
		$this->arrLayers = $layers;
	}
	
	
	/**
	 * update the title of a Slide by Slide ID
	 * @since: 5.0
	 **/
	public function updateTitleByID($data){
		if(!isset($data['slideID']) || !isset($data['slideTitle'])) return false;
		
		$this->initByID($data['slideID']);
		
		$arrUpdate = array();
		$arrUpdate['title'] = $data['slideTitle'];
		
		$this->updateParamsInDB($arrUpdate);
		
	}
	
	/**
	 * toggle slide state from data
	 **/
	public function toggleSlideStatFromData($data){
		
		$sliderID = ZTSliderFunctions::getVal($data, "slider_id");
		$slider = new ZTSlider();
		$slider->initByID($sliderID);
		
		$slideID = ZTSliderFunctions::getVal($data, "slide_id");
		
		$this->initByID($slideID);
		
		$state = $this->getParam("state","published");
		$newState = ($state == "published")?"unpublished":"published";
		
		$arrUpdate = array();
		$arrUpdate["state"] = $newState;
		
		$this->updateParamsInDB($arrUpdate);
		
		return $newState;
	}
	
	
	/**
	 * 
	 * updatye slide language from data
	 */
	private function updateLangFromData($data){
		
		$slideID = ZTSliderFunctions::getVal($data, "slideid");
		$this->initByID($slideID);
		
		$lang = ZTSliderFunctions::getVal($data, "lang");
		
		$arrUpdate = array();
		$arrUpdate["lang"] = $lang;
		$this->updateParamsInDB($arrUpdate);
		
		$response = array();
		$response["url_icon"] = RevSliderWpml::getFlagUrl($lang);
		$response["title"] = RevSliderWpml::getLangTitle($lang);
		$response["operation"] = "update";
		
		return($response);
	}
	
	
	/**
	 * 
	 * add language (add slide that connected to current slide) from data
	 */
	private function addLangFromData($data){
		$sliderID = ZTSliderFunctions::getVal($data, "sliderid");
		$slideID = ZTSliderFunctions::getVal($data, "slideid");
		$lang = ZTSliderFunctions::getVal($data, "lang");
		
		//duplicate slide
		$slider = new ZTSlider();
		$slider->initByID($sliderID);
		$newSlideID = $slider->duplicateSlide($slideID);
		
		//update new slide
		$this->initByID($newSlideID);
		
		$arrUpdate = array();
		$arrUpdate["lang"] = $lang;
		$arrUpdate["parentid"] = $slideID;
		$this->updateParamsInDB($arrUpdate);
		
		$urlIcon = RevSliderWpml::getFlagUrl($lang);
		$title = RevSliderWpml::getLangTitle($lang);
		
		$newSlide = new RevSlide();
		$newSlide->initByID($slideID);
		$arrLangCodes = $newSlide->getArrChildLangCodes();
		$isAll = RevSliderWpml::isAllLangsInArray($arrLangCodes);
		
		$html = "<li>
					<img id=\"icon_lang_".$newSlideID."\" class=\"icon_slide_lang\" src=\"".$urlIcon."\" title=\"".$title."\" data-slideid=\"".$newSlideID."\" data-lang=\"".$lang."\">
					<div class=\"icon_lang_loader loader_round\" style=\"display:none\"></div>								
				</li>";
		
		$response = array();
		$response["operation"] = "add";
		$response["isAll"] = $isAll;
		$response["html"] = $html;
		
		return($response);
	}
	
	
	/**
	 * 
	 * delete slide from language menu data
	 */
	private function deleteSlideFromLangData($data){
		
		$slideID = ZTSliderFunctions::getVal($data, "slideid");
		$this->initByID($slideID);
		$this->deleteSlide();
		
		$response = array();
		$response["operation"] = "delete";
		return($response);
	}
	
	
	/**
	 * 
	 * add or update language from data
	 */
	public function doSlideLangOperation($data){
		
		$operation = ZTSliderFunctions::getVal($data, "operation");
		switch($operation){
			case "add":
				$response = $this->addLangFromData($data);	
			break;
			case "delete":
				$response = $this->deleteSlideFromLangData($data);
			break;
			case "update":
			default:
				$response = $this->updateLangFromData($data);
			break;
		}
		
		return($response);
	}
	
	/**
	 * 
	 * get thumb url
	 */
	public function getUrlImageThumb(){
		
		//get image url by thumb
		if(!empty($this->imageID)){
			$urlImage = RevSliderFunctionsWP::getUrlAttachmentImage($this->imageID, RevSliderFunctionsWP::THUMB_MEDIUM);
		}else{
			//get from cache
			if(!empty($this->imageFilepath)){
				$urlImage = ZTSliderFunctions::getImageUrl($this->imageFilepath,200,100,true);
			}
			else 
				$urlImage = $this->imageUrl;
		}
		
		if(empty($urlImage))
			$urlImage = $this->imageUrl;
		
//		$urlImage = apply_filters('revslider_slide_getUrlImageThumb', $urlImage, $this);
		
		return $urlImage;
	}
	
	/**
	 * 
	 * replace image url's among slide image and layer images
	 */
	public function replaceImageUrls($urlFrom, $urlTo){
		
		$this->validateInited();
		
		$isUpdated = false;
		
		$check = array('image', 'background_image', 'slide_thumb', 'show_alternate_image');
		
		if(isset($this->params['background_type']) && $this->params['background_type'] == 'html5'){
			$check[] = 'slide_bg_html_mpeg';
			$check[] = 'slide_bg_html_webm';
			$check[] = 'slide_bg_html_ogv';
		}
		
		foreach($check as $param){
			$urlImage = ZTSliderFunctions::getVal($this->params, $param, '');
			if(strpos($urlImage, $urlFrom) !== false){
				$imageNew = str_replace($urlFrom, $urlTo, $urlImage);
				$this->params[$param] = $imageNew; 
				$isUpdated = true;
			}
		}
		
		if($isUpdated == true)
			$this->updateParamsInDB();
		
		
		// update image url in layers
		$isUpdated = false;
		foreach($this->arrLayers as $key=>$layer){
			$type =  ZTSliderFunctions::getVal($layer, "type");
			
			$urlImage = ZTSliderFunctions::getVal($layer, "image_url");
			if(strpos($urlImage, $urlFrom) !== false){
				$newUrlImage = str_replace($urlFrom, $urlTo, $urlImage);
				$this->arrLayers[$key]["image_url"] = $newUrlImage;
				$isUpdated = true;
			}
			
			if(isset($type) && ($type == 'video' || $type == 'audio')){
				$video_data = (isset($layer['video_data'])) ? (array) $layer['video_data'] : array();
				
				$check = array();
				
				if(!empty($video_data) && isset($video_data['video_type']) && $video_data['video_type'] == 'html5'){
					$check[] = 'urlPoster';
					$check[] = 'urlMp4';
					$check[] = 'urlWebm';
					$check[] = 'urlOgv';
				}elseif(!empty($video_data) && isset($video_data['video_type']) && $video_data['video_type'] != 'html5'){ //video cover image
					if($video_data['video_type'] == 'audio'){
						$check[] = 'urlAudio';
					}else{
						$check[] = 'previewimage';
					}
				}
				
				if(!empty($check)){
					foreach($check as $param){
						$url = ZTSliderFunctions::getVal($video_data, $param);
						if(strpos($url, $urlFrom) !== false){
							$newUrl = str_replace($urlFrom, $urlTo, $url);
							$video_data[$param] = $newUrl;
							$isUpdated = true;
						}
					}
				}
				
				$this->arrLayers[$key]['video_data'] = $video_data;
			}elseif(isset($type) && $type == 'svg'){
				$svg_val = ZTSliderFunctions::getVal($layer, 'svg', false);
				if (!empty($svg_val) && sizeof($svg_val)>0) {
					$svg_val->{'src'} = str_replace($urlFrom, $urlTo, $svg_val->{'src'});
					
					$this->arrLayers[$key]['svg'] = $svg_val;
					$isUpdated = true;
				}
			}
			
			if(isset($layer['layer_action'])){
				if(isset($layer['layer_action']->image_link) && !empty($layer['layer_action']->image_link)){
					$layer['layer_action']->image_link = (array)$layer['layer_action']->image_link;
					foreach($layer['layer_action']->image_link as $jtsk => $jtsval){
						if(strpos($jtsval, $urlFrom) !== false){
							$this->arrLayers[$key]['layer_action']->image_link[$jtsk] = str_replace($urlFrom, $urlTo, $jtsval);
							$isUpdated = true;
						}
					}
				}
			}
			
		}
		
		if($isUpdated == true)
			$this->updateLayersInDB();

		return $this;
	}
	
	
	/**
	 * get all used fonts in the current Slide
	 * @since: 5.1.0
	 */
	public function getUsedFonts($full = false){
		$this->validateInited();
		
		$op = new ZTSliderOperations();
		$fonts = array();
		
		
		$all_fonts = $op->getArrFontFamilys();
		
		if(!empty($this->arrLayers)){
			foreach($this->arrLayers as $key=>$layer){
				$def = (array) ZTSliderFunctions::getVal($layer, 'deformation', array());
				$font = ZTSliderFunctions::getVal($def, 'font-family', '');
				$static = (array) ZTSliderFunctions::getVal($layer, 'static_styles', array());
				
				foreach($all_fonts as $f){
					if(strtolower(str_replace(array('"', "'", ' '), '', $f['label'])) == strtolower(str_replace(array('"', "'", ' '), '', $font)) && $f['type'] == 'googlefont'){
						if(!isset($fonts[$f['label']])){
							$fonts[$f['label']] = array('variants' => array(), 'subsets' => array());
						}
						if($full){ //if full, add all.
							//switch the variants around here!
							$mv = array();
							if(!empty($f['variants'])){
								foreach($f['variants'] as $fvk => $fvv){
									$mv[$fvv] = $fvv;
								}
							}
							$fonts[$f['label']] = array('variants' => $mv, 'subsets' => $f['subsets']);
						}else{ //Otherwise add only current font-weight plus italic or not
							$fw = (array) ZTSliderFunctions::getVal($static, 'font-weight', '400'); 
							$fs = ZTSliderFunctions::getVal($def, 'font-style', '');
							
							if($fs == 'italic'){
								foreach($fw as $mf => $w){
									//we check if italic is available at all for the font!
									if($w == '400'){
										if(array_search('italic', $f['variants']) !== false)
											$fw[$mf] = 'italic';
									}else{
										if(array_search($w.'italic', $f['variants']) !== false)
											$fw[$mf] = $w.'italic';
									}
								}
							}
							
							foreach($fw as $mf => $w){
								$fonts[$f['label']]['variants'][$w] = true;
							}
							
							$fonts[$f['label']]['subsets'] = $f['subsets']; //subsets always get added, needs to be done then by the Slider Settings
						}
						break;
					}
				}
			}
		}
		return $fonts;
//		return apply_filters('revslider_slide_getUsedFonts', $fonts, $this);
	}
	
	
	/**
	 * replace all css classes in all layers
	 * @since: 5.0
	 */
	public function replaceCssClass($css_from, $css_to){
		
		$this->validateInited();
		
		
		$isUpdated = false;
		
		if(!empty($this->arrLayers)){
			foreach($this->arrLayers as $key=>$layer){
				$caption = ZTSliderFunctions::getVal($layer, 'style');
				if($caption == $css_from){
					$this->arrLayers[$key]['style'] = $css_to;
					$isUpdated = true;
				}
			}
		}
		
		if($isUpdated == true)
			$this->updateLayersInDB();
	}

	public function get_image_attributes($slider_type,$slide_id = null){
		if (!is_null($slide_id))$this->initByID($slide_id);

		$params = $this->params;

		$bgType = ZtSliderSlider::getVar($params, "background_type","transparent");
		$bgColor = ZtSliderSlider::getVar($params, "slide_bg_color","transparent");

		$bgFit = ZtSliderSlider::getVar($params, "bg_fit","cover");
		$bgFitX = intval(ZtSliderSlider::getVar($params, "bg_fit_x","100"));
		$bgFitY = intval(ZtSliderSlider::getVar($params, "bg_fit_y","100"));

		$bgPosition = ZtSliderSlider::getVar($params, "bg_position","center top");
		$bgPositionX = intval(ZtSliderSlider::getVar($params, "bg_position_x","0"));
		$bgPositionY = intval(ZtSliderSlider::getVar($params, "bg_position_y","0"));

		$bgRepeat = ZtSliderSlider::getVar($params, "bg_repeat","no-repeat");

		$bgStyle = ' ';
		if($bgFit == 'percentage'){
			$bgStyle .= "background-size: ".$bgFitX.'% '.$bgFitY.'%;';
		}else{
			$bgStyle .= "background-size: ".$bgFit.";";
		}
		if($bgPosition == 'percentage'){
			$bgStyle .= "background-position: ".$bgPositionX.'% '.$bgPositionY.'%;';
		}else{
			$bgStyle .= "background-position: ".$bgPosition.";";
		}
		$bgStyle .= "background-repeat: ".$bgRepeat.";";

		$thumb = '';
		$thumb_on = ZtSliderSlider::getVar($params, "thumb_for_admin", 'off');

		switch($slider_type){
			case 'gallery':
				$thumb = ZtSliderSlider::getVar($params, "image");
				$thumb = @getimagesize($thumb) ? $thumb :JUri::root().'/'.$thumb;
				if(!@getimagesize($thumb)) $thumb='';
				if($thumb_on == 'on'){
					$thumb = ZtSliderSlider::getVar($params, "slide_thumb", '');
				}

				break;
			case 'posts':
				$thumb = ZT_ASSETS_PATH.'/assets/sources/post.png';
				$bgStyle = 'background-size: cover;';
				break;
			case 'woocommerce':
				$thumb = ZT_ASSETS_PATH.'/assets/sources/wc.png';
				$bgStyle = 'background-size: cover;';
				break;
			case 'facebook':
				$thumb = ZT_ASSETS_PATH.'/assets/sources/fb.png';
				$bgStyle = 'background-size: cover;';
				break;
			case 'twitter':
				$thumb = ZT_ASSETS_PATH.'/assets/sources/tw.png';
				$bgStyle = 'background-size: cover;';
				break;
			case 'instagram':
				$thumb = ZT_ASSETS_PATH.'/assets/sources/ig.png';
				$bgStyle = 'background-size: cover;';
				break;
			case 'flickr':
				$thumb = ZT_ASSETS_PATH.'/assets/sources/fr.png';
				$bgStyle = 'background-size: cover;';
				break;
			case 'youtube':
				$thumb = ZT_ASSETS_PATH.'/assets/sources/yt.png';
				$bgStyle = 'background-size: cover;';
				break;
			case 'vimeo':
				$thumb = ZT_ASSETS_PATH.'/assets/sources/vm.png';
				$bgStyle = 'background-size: cover;';
				break;
		}

		if($thumb == '') $thumb = ZtSliderSlider::getVar($params, "image");
		$thumb = (@getimagesize($thumb) ? $thumb :  JUri::root().$thumb);

		$bg_fullstyle ='';
		$bg_extraClass='';
		$data_urlImageForView='';

		//if($bgType=="image" || $bgType=="streamvimeo" || $bgType=="streamyoutube" || $bgType=="streaminstagram" || $bgType=="html5") {
		$data_urlImageForView = $thumb;
		$bg_fullstyle = $bgStyle;
		//}

		if($bgType=="solid"){
			if($thumb_on == 'off'){
				$bg_fullstyle ='background-color:'.$bgColor.';';
				$data_urlImageForView = '';
			}else{
				$bg_fullstyle = 'background-size: cover;';
			}
		}

		if($bgType=="trans" || $bgType=="transparent"){
			$bg_extraClass = 'mini-transparent';
			$bg_fullstyle = 'background-size: inherit; background-repeat: repeat;';
		}

		return array(
			'url' => $data_urlImageForView,
			'class' => $bg_extraClass,
			'style' => $bg_fullstyle
		);

	}

	/**
	 * get all the svg url sets used in Slider Revolution
	 * @since: 5.1.7
	 **/
	public static function get_svg_sets_url(){
		$svg_sets = array();

		$svg_sets['Actions'] = array('path' => ZT_ASSETS_PATH . '/assets/images/svg/action/', 'url' => ZT_ASSETS_URL . '/assets/images/svg/action/');
		$svg_sets['Alerts'] = array('path' => ZT_ASSETS_PATH . '/assets/images/svg/alert/', 'url' => ZT_ASSETS_URL . '/assets/images/svg/alert/');
		$svg_sets['AV'] = array('path' => ZT_ASSETS_PATH . '/assets/images/svg/av/', 'url' => ZT_ASSETS_URL . '/assets/images/svg/av/');
		$svg_sets['Communication'] = array('path' => ZT_ASSETS_PATH . '/assets/images/svg/communication/', 'url' => ZT_ASSETS_URL . '/assets/images/svg/communication/');
		$svg_sets['Content'] = array('path' => ZT_ASSETS_PATH . '/assets/images/svg/content/', 'url' => ZT_ASSETS_URL . '/assets/images/svg/content/');
		$svg_sets['Device'] = array('path' => ZT_ASSETS_PATH . '/assets/images/svg/device/', 'url' => ZT_ASSETS_URL . '/assets/images/svg/device/');
		$svg_sets['Editor'] = array('path' => ZT_ASSETS_PATH . '/assets/images/svg/editor/', 'url' => ZT_ASSETS_URL . '/assets/images/svg/editor/');
		$svg_sets['File'] = array('path' => ZT_ASSETS_PATH . '/assets/images/svg/file/', 'url' => ZT_ASSETS_URL . '/assets/images/svg/file/');
		$svg_sets['Hardware'] = array('path' => ZT_ASSETS_PATH . '/assets/images/svg/hardware/', 'url' => ZT_ASSETS_URL . '/assets/images/svg/hardware/');
		$svg_sets['Images'] = array('path' => ZT_ASSETS_PATH . '/assets/images/svg/image/', 'url' => ZT_ASSETS_URL . '/assets/images/svg/image/');
		$svg_sets['Maps'] = array('path' => ZT_ASSETS_PATH . '/assets/images/svg/maps/', 'url' => ZT_ASSETS_URL . '/assets/images/svg/maps/');
		$svg_sets['Navigation'] = array('path' => ZT_ASSETS_PATH . '/assets/images/svg/navigation/', 'url' => ZT_ASSETS_URL . '/assets/images/svg/navigation/');
		$svg_sets['Notifications'] = array('path' => ZT_ASSETS_PATH . '/assets/images/svg/notification/', 'url' => ZT_ASSETS_URL . '/assets/images/svg/notification/');
		$svg_sets['Places'] = array('path' => ZT_ASSETS_PATH . '/assets/images/svg/places/', 'url' => ZT_ASSETS_URL . '/assets/images/svg/places/');
		$svg_sets['Social'] = array('path' => ZT_ASSETS_PATH . '/assets/images/svg/social/', 'url' => ZT_ASSETS_URL . '/assets/images/svg/social/');
		$svg_sets['Toggle'] = array('path' => ZT_ASSETS_PATH . '/assets/images/svg/toggle/', 'url' => ZT_ASSETS_URL . '/assets/images/svg/toggle/');

		return $svg_sets;
	}

	/**
	 * get all the svg files for given sets used in Slider Revolution
	 * @since: 5.1.7
	 **/
	public static function get_svg_sets_full(){

		$svg_sets = self::get_svg_sets_url();

		$svg = array();

		if(!empty($svg_sets)){
			foreach($svg_sets as $handle => $values){
				$svg[$handle] = array();

				if($dir = opendir($values['path'])) {
					while(false !== ($file = readdir($dir))){
						if ($file != "." && $file != "..") {
							$filetype = pathinfo($file);

							if(isset($filetype['extension']) && $filetype['extension'] == 'svg'){
								$svg[$handle][$file] = $values['url'].$file;
							}
						}
					}
				}
			}
		}

		return $svg;
	}



	/**
	 * reset Slide to certain values
	 * @since: 5.0
	 */
	public function reset_slide_values($values){
		$this->validateInited();
		
		foreach($values as $key => $val){
			if($key !== 'sliderid'){
				$this->params[esc_attr($key)] = esc_attr($val);
			}
		}
		
		$this->updateParamsInDB();
	}
	
	
	/**
	 * return if current Slide is static Slide
	 */
	public function isStaticSlide(){
		return $this->static_slide;
	}
	
	/**
	 * Returns all layer attributes that can have more than one setting due to desktop, tablet, mobile sizes
	 * @since: 5.0
	 */
	public static function translateIntoSizes(){

		return array(
			'align_hor',
			'align_vert',
			'top',
			'left',
			'font-size',
			'line-height',
			'font-weight',
			'color',
			'max_width',
			'max_height',
			'whitespace',
			'video_height',
			'video_width',
			'scaleX',
			'scaleY'
		);
	}
	
	
	/**
	 * Translates all values that need more than one setting
	 * @since: 5.0
	 */
	public function translateLayerSizes($layers){
		$translation = self::translateIntoSizes();
		
		if(!empty($layers)){
			foreach($layers as $l => $layer){
				foreach($translation as $trans){
					if(isset($layers[$l][$trans])){
						if(!is_array($layers[$l][$trans])){
							$layers[$l][$trans] = array('desktop' => $layers[$l][$trans]);
						}
					}
				}
			}
		}
		
		return $layers;
	}
}

/**
 * old classname extends new one (old classnames will be obsolete soon)
 * @since: 5.0
 **/
class ZTSlide extends ZTSliderSlide {}
?>