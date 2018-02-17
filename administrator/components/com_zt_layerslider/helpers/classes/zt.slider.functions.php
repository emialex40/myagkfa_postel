<?php
/**
 * @author    ThemePunch <info@themepunch.com>
 * @link      http://www.themepunch.com/
 * @copyright 2015 ThemePunch
 */

defined('_JEXEC') or die;

class ZTSliderFunctions{

	public function __construct()
	{
		$this->db  = JFactory::getDbo();
	}

	public static $extension = 'com_zt_layerslider';

	public static function addSubmenu($vName)
	{
		$version = new JVersion();

		if (version_compare($version->getShortVersion(), '3.0.0', '<')) {

			JSubMenuHelper::addEntry(
				JText::_('COM_ZT_LAYERSLIDER_SLIDERS'),
				'index.php?option=com_zt_layerslider&view=sliders',
				$vName == 'sliders'
			);

		} else {
			JHtmlSidebar::addEntry(
				JText::_('COM_ZT_LAYERSLIDER_SLIDERS'),
				'index.php?option=com_zt_layerslider&view=sliders',
				$vName == 'sliders'
			);

		}

	}


	public static function getActions($sliderId = 0)
	{
		$user	= JFactory::getUser();

		$result	= new JObject;

		if (empty($sliderId)) {
			$assetName = 'com_zt_layerslider';
			$level = 'component';
		} else {
			$assetName = 'com_zt_layerslider.slider.'.(int) $sliderId;
			$level = 'slider';
		}

		$actions = JAccess::getActions('com_zt_layerslider', $level);

		foreach ($actions as $action) {
			$result->set($action->name,	$user->authorise($action->name, $assetName));
		}

		return $result;
	}

	public static function getButtons(){

		$buttons = array(
			"red"=>"Red Button",
			"green"=>"Green Button",
			"blue"=>"Blue Button",
			"orange"=>"Orange Button",
			"darkgrey"=>"Darkgrey Button",
			"lightgrey"=>"Lightgrey Button",
		);

		return $buttons;
	}


	public static function getCaptionsContent(){
		$contentCSS = file_get_contents(JUri::root() . 'administrator/components/com_zt_layerslider/assets/css/captions.css');
		return($contentCSS);
	}

	public static function updateCaptionCcss($content){

		jimport('joomla.filesystem.file');
		$content = stripslashes($content);
		$content = trim($content);
		JFile::write(JPATH_SITE . DS .'components'.DS.'com_zt_layerslider'.DS.'assets'.DS.'css'.DS.'captions.css', $content);
		$arrCaptions = ZTSliderFunctions::getCaptionClasses($content);

		return $arrCaptions;
	}

	public static function getCaptionClasses($contentCSS){

		$lines = explode("\n",$contentCSS);
		$arrClasses = array();
		foreach($lines as $key=>$line){

			$line = trim($line);
			if(strpos($line, "{") === false)
				continue;

			if(strpos($line, ".caption a") !== false)
				continue;

			if(strpos($line, ".tp-caption a") !== false)
				continue;

			$class = str_replace("{", "", $line);
			$class = str_replace(".caption.", ".", $class);
			$class = str_replace(".tp-caption.", ".", $class);

			$class = str_replace(".", "", $class);
			$class = trim($class);
			$arrWords = explode(" ", $class);
			$class = $arrWords[count($arrWords)-1];
			$class = trim($class);
			$arrClasses[] = $class;

		}


		return($arrClasses);

	}

	public static function ajaxResponse($success,$message,$arrData = null){

		$response = array();
		$response["success"] = $success;
		$response["message"] = $message;

		if(!empty($arrData)){

			if(gettype($arrData) == "string")
				$arrData = array("data"=>$arrData);

			$response = array_merge($response,$arrData);
		}


		$document = JFactory::getDocument();

		$document->setMimeEncoding('application/json');

		$json = json_encode($response);
		echo $json;
		JFactory::getApplication()->close();
	}

	public static function is_mobile() {
		if ( empty($_SERVER['HTTP_USER_AGENT']) ) {
			$is_mobile = false;
		} elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false // many mobile devices (all iPhone, iPad, etc.)
			|| strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
			|| strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
			|| strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
			|| strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
			|| strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
			|| strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false ) {
			$is_mobile = true;
		} else {
			$is_mobile = false;
		}

		return $is_mobile;
	}

	public static function current_time( $type, $gmt = 0 ) {
		switch ( $type ) {
			case 'mysql':
				return ( $gmt ) ? gmdate( 'Y-m-d H:i:s' ) : gmdate( 'Y-m-d H:i:s', ( time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ) );
			case 'timestamp':
				return ( $gmt ) ? time() : time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
			default:
				return ( $gmt ) ? date( $type ) : date( $type, time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
		}
	}
	/**
	 *
	 * echo json ajax response
	 */
	public static function ajaxResponseError($message,$arrData = null){

		self::ajaxResponse(false,$message,$arrData,true);
	}

	/**
	 * echo ajax success response
	 */
	public static function ajaxResponseSuccessRedirect($message,$url){
		$arrData = array("is_redirect"=>true,"redirect_url"=>$url);

		self::ajaxResponse(true,$message,$arrData,true);
	}

	public static function ajaxResponseErrorRedirect($message,$url){
		$arrData = array("is_redirect"=>true,"redirect_url"=>$url);

		self::ajaxResponse(true,$message,$arrData,true);
	}

	public static function ajaxResponseData($arrData){
		if(gettype($arrData) == "string")
			$arrData = array("data"=>$arrData);

		self::ajaxResponse(true,"",$arrData);
	}

	public static function ajaxResponseSuccess($message,$arrData = null){

		self::ajaxResponse(true,$message,$arrData);

	}

	/**
	 * Check value to find if it was serialized.
	 *
	 * If $data is not an string, then returned value will always be false.
	 * Serialized data is always a string.
	 *
	 * @since 2.0.5
	 *
	 * @param string $data   Value to check to see if was serialized.
	 * @param bool   $strict Optional. Whether to be strict about the end of the string. Default true.
	 * @return bool False if not serialized and true if it was.
	 */
	public static function is_serialized( $data, $strict = true ) {
		// if it isn't a string, it isn't serialized.
		if ( ! is_string( $data ) ) {
			return false;
		}
		$data = trim( $data );
		if ( 'N;' == $data ) {
			return true;
		}
		if ( strlen( $data ) < 4 ) {
			return false;
		}
		if ( ':' !== $data[1] ) {
			return false;
		}
		if ( $strict ) {
			$lastc = substr( $data, -1 );
			if ( ';' !== $lastc && '}' !== $lastc ) {
				return false;
			}
		} else {
			$semicolon = strpos( $data, ';' );
			$brace     = strpos( $data, '}' );
			// Either ; or } must exist.
			if ( false === $semicolon && false === $brace )
				return false;
			// But neither must be in the first X characters.
			if ( false !== $semicolon && $semicolon < 3 )
				return false;
			if ( false !== $brace && $brace < 4 )
				return false;
		}
		$token = $data[0];
		switch ( $token ) {
			case 's' :
				if ( $strict ) {
					if ( '"' !== substr( $data, -2, 1 ) ) {
						return false;
					}
				} elseif ( false === strpos( $data, '"' ) ) {
					return false;
				}
			// or else fall through
			case 'a' :
			case 'O' :
				return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
			case 'b' :
			case 'i' :
			case 'd' :
				$end = $strict ? '$' : '';
				return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
		}
		return false;
	}


	/**
	 *
	 * echo json ajax response
	 */

	public static function maybe_unserialize( $original ) {
		if ( self::is_serialized( $original ) ) // don't attempt to unserialize data that wasn't serialized going in
			return @unserialize( $original );
		return $original;
	}
	
	public static function throwError($message,$code=null){
		if(!empty($code)){
			throw new Exception($message,$code);
		}else{
			throw new Exception($message);
		}
	}
	
	/*
	 * get popular posts
	 */

	public static function getPostsFromPopular($max_posts = false,$attribs = null){
		if($max_posts == false){
			$max_posts = $attribs['max_slider_posts'];
			if(empty($max_posts) || !is_numeric($max_posts))
				$max_posts = -1;
		}else{
			$max_posts = intval($max_posts);
		}

		if ($max_posts != -1) $limit = ' LIMIT '.$max_posts;
		else $limit ='';

		$order_by = ' ORDER BY hits';
		$order = ' DESC';

		$db = JFactory::getDbo();
		$db->setQuery('SELECT * FROM #__content WHERE state = 1 '.$order_by.$order.$limit);

		if (!$posts = $db->loadAssocList()) return array();

		return $posts;
	}

	public static function get_font_weight_count($string){
		$string = explode(':', $string);

		$nums = 0;

		if(count($string) >= 2){
			$string = $string[1];
			if(strpos($string, '&') !== false){
				$string = explode('&', $string);
				$string = $string[0];
			}

			$nums = count(explode(',', $string));
		}

		return $nums;
	}

	/**
	 * get recent posts
	 * @since: 5.1.1
	 */
	public static function getPostsFromRecent($max_posts = false,$attribs = null){

		if($max_posts == false){
			$max_posts = $attribs['max_slider_posts'];
			if(empty($max_posts) || !is_numeric($max_posts))
				$max_posts = -1;
		}else{
			$max_posts = intval($max_posts);
		}

		if ($max_posts != -1) $limit = ' LIMIT '.$max_posts;
		else $limit ='';

		$order_by = ' ORDER BY created';
		$order = ' DESC';

		$db = JFactory::getDbo();
		$db->setQuery('SELECT * FROM #__content WHERE state = 1 '.$order_by.$order.$limit);

		if (!$posts = $db->loadAssocList()) return array();

		return $posts;
	}


	/**
	 * set output for download
	 */
	public static function downloadFile($str,$filename="output.txt"){
		
		//output for download
		header('Content-Description: File Transfer');
		header('Content-Type: text/html; charset=UTF-8');
		header("Content-Disposition: attachment; filename=".$filename.";");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".strlen($str));			
		echo $str;			
		exit();
	}
	
	
	
	/**
	 * turn boolean to string
	 */
	public static function boolToStr($bool){
		if(gettype($bool) == "string")
			return($bool);
		if($bool == true)
			return("true");
		else 
			return("false");
	}
	
	/**
	 * convert string to boolean
	 */
	public static function strToBool($str){
		if(is_bool($str))
			return($str);
			
		if(empty($str))
			return(false);
			
		if(is_numeric($str))
			return($str != 0);
			
		$str = strtolower($str);
		if($str == "true")
			return(true);
			
		return(false);
	}
	
	
	/**
	 * get value from array. if not - return alternative
	 */
	public static function getVal($arr,$key,$altVal=""){
		if(is_array($arr)){
			if(isset($arr[$key])){
				return($arr[$key]);
			}
		}elseif(is_object($arr)){
			if(isset($arr->$key)){
				return($arr->$key);
			}
		}
		return($altVal);
	}

	public static function __checked_selected_helper( $helper, $current, $echo, $type ) {
		if ( (string) $helper === (string) $current )
			$result = " $type='$type'";
		else
			$result = '';

		if ( $echo )
			echo $result;

		return $result;
	}


	public static function selected( $selected, $current = true, $echo = true ) {
		return ZTSliderFunctions::__checked_selected_helper( $selected, $current, $echo, 'selected' );
	}


	public static function checked( $checked, $current = true, $echo = true ) {
		return ZTSliderFunctions::__checked_selected_helper( $checked, $current, $echo, 'checked' );
	}
	
	//------------------------------------------------------------
	// get variable from post or from get. get wins.
	public static function getPostGetVariable($name,$initVar = ""){
		$var = $initVar;
		if(isset($_POST[$name])) $var = $_POST[$name];
		else if(isset($_GET[$name])) $var = $_GET[$name];
		return($var);
	}
	
	
	//------------------------------------------------------------
	public static function getPostVariable($name,$initVar = ""){
		$var = $initVar;
		if(isset($_POST[$name])) $var = $_POST[$name];
		return($var);
	}
	
	
	//------------------------------------------------------------
	public static function getGetVar($name,$initVar = ""){
		$var = $initVar;
		if(isset($_GET[$name])) $var = $_GET[$name];
		return($var);
	}
	
	public static function sortByOrder($a, $b) {
		return $a['order'] - $b['order'];
	}
	
	/**
	 * validate that some file exists, if not - throw error
	 */
	public static function validateFilepath($filepath,$errorPrefix=null){
		if(file_exists($filepath) == true)
			return(false);
		if($errorPrefix == null)
			$errorPrefix = "File";
		$message = $errorPrefix." ".esc_attr($filepath)." not exists!";
		self::throwError($message);
	}
	
	/**
	 * validate that some value is numeric
	 */
	public static function validateNumeric($val,$fieldName=""){
		self::validateNotEmpty($val,$fieldName);
		
		if(empty($fieldName))
			$fieldName = "Field";
		
		if(!is_numeric($val))
			self::throwError("$fieldName should be numeric ");
	}
	
	/**
	 * validate that some variable not empty
	 */
	public static function validateNotEmpty($val,$fieldName=""){
		
		if(empty($fieldName))
			$fieldName = "Field";
			
		if(empty($val) && is_numeric($val) == false)
			self::throwError("Field <b>$fieldName</b> should not be empty");
	}

	
	//------------------------------------------------------------
	//get path info of certain path with all needed fields
	public static function getPathInfo($filepath){
		$info = pathinfo($filepath);
		
		//fix the filename problem
		if(!isset($info["filename"])){
			$filename = $info["basename"];
			if(isset($info["extension"]))
				$filename = substr($info["basename"],0,(-strlen($info["extension"])-1));
			$info["filename"] = $filename;
		}
		
		return($info);
	}
	
	/**
	 * Convert std class to array, with all sons
	 * @param unknown_type $arr
	 */
	public static function convertStdClassToArray($arr){
		$arr = (array)$arr;
		
		$arrNew = array();
		
		foreach($arr as $key=>$item){
			$item = (array)$item;
			$arrNew[$key] = $item;
		}
		
		return($arrNew);
	}
	
	public static function cleanStdClassToArray($arr){
		$arr = (array)$arr;
		
		$arrNew = array();
		
		foreach($arr as $key=>$item){
			$arrNew[$key] = $item;
		}
		
		return($arrNew);
	}
	
	
	/**
	 * encode array into json for client side
	 */
	public static function jsonEncodeForClientSide($arr){
		$json = "";
		if(!empty($arr)){
			$json = json_encode($arr);
			$json = addslashes($json);
		}

		if(empty($json)) $json = '{}';

		$json = "'".$json."'";
		
		return($json);
	}


	/**
	 * decode json from the client side
	 */
	public static function jsonDecodeFromClientSide($data){
//		$data = stripslashes($data);
		$data = str_replace('&#092;"','\"',$data);
		$data = json_decode($data);
		$data = (array)$data;
		
		return($data);
	}

	/**
	 * Change FontURL to new URL (added for chinese support since google is blocked there)
	 * @since: 5.0
	 */
	public static function modify_punch_url($url){
		$operations = new ZTSliderOperations();
		$arrValues = $operations->getGeneralSettingsValues();

		$set_diff_font = ZTSliderFunctions::getVal($arrValues, "change_font_loading",'');
		if($set_diff_font !== ''){
			return $set_diff_font;
		}else{
			return $url;
		}
	}
	
	/**
	 * do "trim" operation on all array items.
	 */
	public static function trimArrayItems($arr){
		if(gettype($arr) != "array")
			ZTSliderFunctions::throwError(JText::_('COM_ZT_LAYERSLIDER_TYPE_MUST_BE_ARRAY'));//"trimArrayItems error: The type must be array"

		foreach ($arr as $key=>$item){
			if(is_array($item)){
				foreach($item as $key => $value){
					$arr[$key][$key] = trim($value);
				}
			}else{
				$arr[$key] = trim($item);
			}
		}
		
		return($arr);
	}
	
	
	/**
	 * get link html
	 */
	public static function getHtmlLink($link,$text,$id="",$class=""){
		
		if(!empty($class))
			$class = " class='$class'";
		
		if(!empty($id))
			$id = " id='$id'";
			
		$html = "<a href=\"$link\"".$id.$class.">$text</a>";
		return($html);
	}
	
	/**
	 * get select from array
	 */
	public static function getHTMLSelect($arr,$default="",$htmlParams="",$assoc = false){
		
		$html = "<select $htmlParams>";
		foreach($arr as $key=>$item){				
			$selected = "";
			
			if($assoc == false){
				if($item == $default) $selected = " selected ";
			}else{ 
				if(trim($key) == trim($default)) $selected = " selected ";
			}
			
			
			if($assoc == true)
				$html .= "<option $selected value='$key'>$item</option>";
			else
				$html .= "<option $selected value='$item'>$item</option>";
		}
		$html.= "</select>";
		return($html);
	}
	
	
	/**
	 * convert assoc array to array
	 */
	public static function assocToArray($assoc){
		$arr = array();
		foreach($assoc as $item)
			$arr[] = $item;
		
		return($arr);
	}
	
	/**
	 * 
	 * strip slashes from textarea content after ajax request to server
	 */
	public static function normalizeTextareaContent($content){
		if(empty($content))
			return($content);
		$content = stripslashes($content);
		$content = trim($content);
		return($content);
	}
	
	
	/**
	 * get text intro, limit by number of words
	 */
	public static function getTextIntro($text, $limit){
		 
		$arrIntro = explode(' ', $text, $limit);
		 
		if (count($arrIntro)>=$limit) {
			array_pop($arrIntro);
			$intro = implode(" ",$arrIntro);
			$intro = trim($intro);
			if(!empty($intro))
				$intro .= '...';
		} else {
			$intro = implode(" ",$arrIntro);
		}
		  
		$intro = preg_replace('`\[[^\]]*\]`','',$intro);
		return($intro);
	}
	
	
	/**
	 * add missing px/% to value, do also for object and array
	 * @since: 5.0
	 **/
	public static function add_missing_val($obj, $set_to = 'px'){

		if(is_array($obj)){
			foreach($obj as $key => $value){
				if(strpos($value, $set_to) === false){
					$obj[$key] = $value.$set_to;
				}
			}
		}elseif(is_object($obj)){
			foreach($obj as $key => $value){
				if(strpos($value, $set_to) === false){
					$obj->$key = $value.$set_to;
				}
			}
		}else{
			if(strpos($obj, $set_to) === false){
				$obj .= $set_to;
			}
		}
		
		return $obj;
	}
	
	
	/**
	 * normalize object with device informations depending on what is enabled for the Slider
	 * @since: 5.0
	 **/
	public static function normalize_device_settings($obj, $enabled_devices, $return = 'obj', $set_to_if = array()){ //array -> from -> to
		/*desktop
		notebook
		tablet
		mobile*/
		
		if(!empty($set_to_if)){
			foreach($obj as $key => $value) {
				foreach($set_to_if as $from => $to){
					if(trim($value) == $from) $obj->$key = $to;
				}
			}
		}
		
		$inherit_size = self::get_biggest_device_setting($obj, $enabled_devices);
		if($enabled_devices['desktop'] == 'on'){
			if(!isset($obj->desktop) || $obj->desktop === ''){
				$obj->desktop = $inherit_size;
			}else{
				$inherit_size = $obj->desktop;
			}
		}else{
			$obj->desktop = $inherit_size;
		}
		
		if($enabled_devices['notebook'] == 'on'){
			if(!isset($obj->notebook) || $obj->notebook === ''){
				$obj->notebook = $inherit_size;
			}else{
				$inherit_size = $obj->notebook;
			}
		}else{
			$obj->notebook = $inherit_size;
		}
		
		if($enabled_devices['tablet'] == 'on'){
			if(!isset($obj->tablet) || $obj->tablet === ''){
				$obj->tablet = $inherit_size;
			}else{
				$inherit_size = $obj->tablet;
			}
		}else{
			$obj->tablet = $inherit_size;
		}
		
		if($enabled_devices['mobile'] == 'on'){
			if(!isset($obj->mobile) || $obj->mobile === ''){
				$obj->mobile = $inherit_size;
			}else{
				$inherit_size = $obj->mobile;
			}
		}else{
			$obj->mobile = $inherit_size;
		}
		
		switch($return){
			case 'obj':
				//order according to: desktop, notebook, tablet, mobile
				$new_obj = new stdClass();
				$new_obj->desktop = $obj->desktop;
				$new_obj->notebook = $obj->notebook;
				$new_obj->tablet = $obj->tablet;
				$new_obj->mobile = $obj->mobile;
				return $new_obj;
			break;
			case 'html-array':
				if($obj->desktop === $obj->notebook && $obj->desktop === $obj->mobile && $obj->desktop === $obj->tablet){
					return $obj->desktop;
				}else{
					return "['".@$obj->desktop."','".@$obj->notebook."','".@$obj->tablet."','".@$obj->mobile."']";
				}
			break;
		}
		
		return $obj;
	}
	
	
	/**
	 * return biggest value of object depending on which devices are enabled
	 * @since: 5.0
	 **/
	public static function get_biggest_device_setting($obj, $enabled_devices){
		
		if($enabled_devices['desktop'] == 'on'){
			if(isset($obj->desktop) && $obj->desktop != ''){
				return $obj->desktop;
			}
		}
		
		if($enabled_devices['notebook'] == 'on'){
			if(isset($obj->notebook) && $obj->notebook != ''){
				return $obj->notebook;
			}
		}
		
		if($enabled_devices['tablet'] == 'on'){
			if(isset($obj->tablet) && $obj->tablet != ''){
				return $obj->tablet;
			}
		}
		
		if($enabled_devices['mobile'] == 'on'){
			if(isset($obj->mobile) && $obj->mobile != ''){
				return $obj->mobile;
			}
		}
		
		return '';
	}
	
	
	/**
	 * change hex to rgba
	 */
    public static function hex2rgba($hex, $transparency = false, $raw = false, $do_rgb = false) {
        if($transparency !== false){
			$transparency = ($transparency > 0) ? number_format( ( $transparency / 100 ), 2, ".", "" ) : 0;
        }else{
            $transparency = 1;
        }

        $hex = str_replace("#", "", $hex);
		
        if(strlen($hex) == 3) {
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else if(self::isrgb($hex)){
			return $hex;
		} else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }
		
		if($do_rgb){
			$ret = $r.', '.$g.', '.$b;
		}else{
			$ret = $r.', '.$g.', '.$b.', '.$transparency;
		}
		if($raw){
			return $ret;
		}else{
			return 'rgba('.$ret.')';
		}

    }
	

	public static function isrgb($rgba){
		if(strpos($rgba, 'rgb') !== false) return true;
		
		return false;
	}

	
	/**
	 * change rgba to hex
	 * @since: 5.0
	 */
	public static function rgba2hex($rgba){
		if(strtolower($rgba) == 'transparent') return $rgba;
		
		$temp = explode(',', $rgba);
		$rgb = array();
		if(count($temp) == 4) unset($temp[3]);
		foreach($temp as $val){
			$t = dechex(preg_replace('/[^\d.]/', '', $val));
			if(strlen($t) < 2) $t = '0'.$t;
			$rgb[] = $t;
		}
		
		return '#'.implode('', $rgb);
	}
	
	
	/**
	 * get transparency from rgba
	 * @since: 5.0
	 */
	public static function get_trans_from_rgba($rgba, $in_percent = false){
		if(strtolower($rgba) == 'transparent') return 100;
		
		$temp = explode(',', $rgba);
		if(count($temp) == 4){
			return ($in_percent) ? preg_replace('/[^\d.]/', '', $temp[3]) : preg_replace('/[^\d.]/', "", $temp[3]) * 100;
		}
		return 100;
	}
	
	
	public static function get_responsive_size($slider){
		$operations = new ZTSliderOperations();
		$arrValues = $operations->getGeneralSettingsValues();
		
		$enable_custom_size_notebook = $slider->slider->getParam('enable_custom_size_notebook','off');
		$enable_custom_size_tablet = $slider->slider->getParam('enable_custom_size_tablet','off');
		$enable_custom_size_iphone = $slider->slider->getParam('enable_custom_size_iphone','off');
		$adv_resp_sizes = ($enable_custom_size_notebook == 'on' || $enable_custom_size_tablet == 'on' || $enable_custom_size_iphone == 'on') ? true : false;
		
		if($adv_resp_sizes == true){
			$width = $slider->slider->getParam("width", 1240, ZTSlider::FORCE_NUMERIC);
			$width .= ','. $slider->slider->getParam("width_notebook", 1024, ZTSlider::FORCE_NUMERIC);
			$width .= ','. $slider->slider->getParam("width_tablet", 778, ZTSlider::FORCE_NUMERIC);
			$width .= ','. $slider->slider->getParam("width_mobile", 480, ZTSlider::FORCE_NUMERIC);
			$height = $slider->slider->getParam("height", 868, ZTSlider::FORCE_NUMERIC);
			$height .= ','. $slider->slider->getParam("height_notebook", 768, ZTSlider::FORCE_NUMERIC);
			$height .= ','. intval($slider->slider->getParam("height_tablet", 960, ZTSlider::FORCE_NUMERIC));
			$height .= ','. intval($slider->slider->getParam("height_mobile", 720, ZTSlider::FORCE_NUMERIC));
						
			$responsive = (isset($arrValues['width'])) ? $arrValues['width'] : '1240';
			$def = (isset($arrValues['width'])) ? $arrValues['width'] : '1240';
			
			$responsive.= ',';
			if($enable_custom_size_notebook == 'on'){
				$responsive.= (isset($arrValues['width_notebook'])) ? $arrValues['width_notebook'] : '1024';
				$def = (isset($arrValues['width_notebook'])) ? $arrValues['width_notebook'] : '1024';
			}else{
				$responsive.= $def;
			}
			$responsive.= ',';
			if($enable_custom_size_tablet == 'on'){
				$responsive.= (isset($arrValues['width_tablet'])) ? $arrValues['width_tablet'] : '778';
				$def = (isset($arrValues['width_tablet'])) ? $arrValues['width_tablet'] : '778';
			}else{
				$responsive.= $def;
			}
			$responsive.= ',';
			if($enable_custom_size_iphone == 'on'){
				$responsive.= (isset($arrValues['width_mobile'])) ? $arrValues['width_mobile'] : '480';
				$def = (isset($arrValues['width_mobile'])) ? $arrValues['width_mobile'] : '480';
			}else{
				$responsive.= $def;
			}
			
			return array(
				'level' => $responsive,
				'height' => $height,
				'width' => $width
			);
		}else{
			
			$responsive = (isset($arrValues['width'])) ? $arrValues['width'] : '1240';
			$def = (isset($arrValues['width'])) ? $arrValues['width'] : '1240';
			$responsive.= ',';			
			$responsive.= (isset($arrValues['width_notebook'])) ? $arrValues['width_notebook'] : '1024';
			$responsive.= ',';	
			$responsive.= (isset($arrValues['width_tablet'])) ? $arrValues['width_tablet'] : '778';
			$responsive.= ',';			
			$responsive.= (isset($arrValues['width_mobile'])) ? $arrValues['width_mobile'] : '480';
			
			return array(
				'visibilitylevel' => $responsive,
				'height' => $slider->slider->getParam("height", "868", ZTSlider::FORCE_NUMERIC),
				'width' => $slider->slider->getParam("width", "1240", ZTSlider::FORCE_NUMERIC)
			);
		}
	}

	public static function dmp($str){
		echo "<div align='left'>";
		echo "<pre>";
		print_r($str);
		echo "</pre>";
		echo "</div>";
	}

	public static function import_media($file_url, $folder_name='',$image) {

		$ul_dir = JPATH_ROOT.'/';

		//if the directory doesn't exist, create it

		if(@fclose(@fopen($file_url, "r"))){ //make sure the file actually exists

			$saveDir = $ul_dir.$image;
			self::upload($file_url,$saveDir);

//			//generate metadata and thumbnails
//			if($attach_data = wp_generate_attachment_metadata($attach_id, $saveDir)) wp_update_attachment_metadata($attach_id, $attach_data);
		return array("id" => '', "path" => $image);
		}else{
			return false;
		}
	}

	public static function upload($tmpl,$des) {
		// Check for request forgeries
		$params = JComponentHelper::getParams('com_media');

		// Authorize the user
		if (!JFactory::getUser()->authorise('core.create')) {
			ZTSliderFunctions::ajaxResponseErrorRedirect('DONT_HAVE_PERMISSON_TO_UPLOAD',JUri::root().'/administrator/index.php?option=com_zt_layerslider');
		}

		// Instantiate the media helper
		$mediaHelper = new JHelperMedia;

		// Maximum allowed size of post back data in MB.
		$postMaxSize = $mediaHelper->toBytes(ini_get('post_max_size'));

		// Maximum allowed size of script execution in MB.
		$memoryLimit = $mediaHelper->toBytes(ini_get('memory_limit'));

		// Check for the total size of post back data.
		if (($postMaxSize > 0 && @filesize($tmpl) > $postMaxSize)
			|| ($memoryLimit != -1 && @filesize($tmpl) > $memoryLimit)) {
			ZTSliderFunctions::ajaxResponseErrorRedirect('FILE_LARGE',JUri::root().'/administrator/index.php?option=com_zt_layerslider');
		}

		$uploadMaxSize = $params->get('upload_maxsize', 0) * 1024 * 1024;
		$uploadMaxFileSize = $mediaHelper->toBytes(ini_get('upload_max_filesize'));


		if (($uploadMaxSize > 0 && @filesize($tmpl) > $uploadMaxSize)
			|| ($uploadMaxFileSize > 0 && @filesize($tmpl) > $uploadMaxFileSize)){
			ZTSliderFunctions::ajaxResponseErrorRedirect('FILE_LARGE',JUri::root().'/administrator/index.php?option=com_zt_layerslider');
		}

		if(!is_dir(dirname($des))) mkdir(dirname($des), 0777, true);

		if (!JFile::move($tmpl, $des)){
			// Error in upload
			ZTSliderFunctions::ajaxResponseErrorRedirect('UPLOAD_FAILURE',JUri::root().'/administrator/index.php?option=com_zt_layerslider');

		}

		return true;
	}

	/**
	 * check if file is in zip
	 * @since: 5.0
	 */
	public static function check_file_in_zip($d_path, $image, $alias, &$alreadyImported, $add_path = false){

		if(trim($image) !== ''){
			if(strpos($image, 'http') !== false){
			}else{
				$strip = false;
				$zimage = @file_exists( $d_path.'/images/'.$image );
				if(!$zimage){
					$zimage = @file_exists( str_replace('//', '/', $d_path.'/images/'.$image) );
					$strip = true;
				}

				if(!$zimage){
					echo $image.JText::_(' not found!<br>', 'revslider');
				}else{
					if(!isset($alreadyImported['images/'.$image])){
						if($strip == true){ //pclzip
							$importImage = self::import_media($d_path.str_replace('//', '/', 'images/'.$image), '',$image);
						}else{
							$importImage = self::import_media($d_path.'images/'.$image, '',$image);
						}
						if($importImage !== false){
							$alreadyImported['images/'.$image] = $importImage['path'];

							$image = $importImage['path'];
						}
					}else{
						$image = $alreadyImported['images/'.$image];
					}
				}
				if($add_path){
					$image = JUri::root() . '/' . $image;
				}
			}
		}

		return $image;
	}

/**
	 *
	 * import slideer handle (not ajax response)
	 */
	private static function importSliderHandle($viewBack = null, $updateAnim = true, $updateStatic = true, $updateNavigation = true){

		ZTSliderFunctions::dmp(JText::_("importing slider settings and data..."));

		$slider = new ZTSlider();
		$response = $slider->importSliderFromPost($updateAnim, $updateStatic, false, false, false, $updateNavigation);

		$sliderID = intval($response["sliderID"]);

		if(empty($viewBack)){
			$viewBack = JRoute::_('index.php?option=com_zt_layerslider&view=slider&layout=edit&id='.$sliderID,false);
			if(empty($sliderID))
				$viewBack = JRoute::_('index.php?option=com_zt_layerslider&view=sliders',false);
		}

		//handle error
		if($response["success"] == false){
			$message = $response["error"];
			ZTSliderFunctions::dmp($message);
			echo ZTSliderFunctions::getHtmlLink($viewBack, JText::_("Go Back",'revslider'));
		}
		else{	//handle success, js redirect.
			ZTSliderFunctions::dmp(JText::_("Slider Import Success, redirecting...",'revslider'));
			echo "<script>location.href='$viewBack'</script>";
		}
		exit();
	}

	/**
	 *
	 * get post or get variable
	 */
	protected static function getPostGetVar($key,$defaultValue = ""){

		if(array_key_exists($key, $_POST))
			$val = ZtSliderSlider::getVar($_POST, $key, $defaultValue);
		else
			$val = ZtSliderSlider::getVar($_GET, $key, $defaultValue);

		return($val);
	}


	/**
	 * Get all images sizes + custom added sizes
	 */
	public static function get_all_image_sizes($type = 'gallery'){
		$custom_sizes = array();

		switch($type){
			case 'flickr':
				$custom_sizes = array(
					'original' => JText::_('Original'),
					'large' => JText::_('Large'),
					'large-square' => JText::_('Large Square'),
					'medium' => JText::_('Medium'),
					'medium-800' => JText::_('Medium 800'),
					'medium-640' => JText::_('Medium 640'),
					'small' => JText::_('Small'),
					'small-320' => JText::_('Small 320'),
					'thumbnail'=> JText::_('Thumbnail'),
					'square' => JText::_('Square')
				);
				break;
			case 'instagram':
				$custom_sizes = array(
					'standard_resolution' => JText::_('Standard Resolution'),
					'thumbnail' => JText::_('Thumbnail'),
					'low_resolution' => JText::_('Low Resolution')
				);
				break;
			case 'twitter':
				$custom_sizes = array(
					'large' => JText::_('Standard Resolution')
				);
				break;
			case 'facebook':
				$custom_sizes = array(
					'full' => JText::_('Original Size'),
					'thumbnail' => JText::_('Thumbnail')
				);
				break;
			case 'youtube':
				$custom_sizes = array(
					'default' => JText::_('Default'),
					'medium' => JText::_('Medium'),
					'high' => JText::_('High'),
					'standard' => JText::_('Standard'),
					'maxres' => JText::_('Max. Res.')
				);
				break;
			case 'vimeo':
				$custom_sizes = array(
					'thumbnail_small' => JText::_('Small'),
					'thumbnail_medium' => JText::_('Medium'),
					'thumbnail_large' => JText::_('Large'),
				);
				break;
			case 'gallery':
			default:
				//jooml does not have thumbnail setting
//				$added_image_sizes = get_intermediate_image_sizes();
//				if(!empty($added_image_sizes) && is_array($added_image_sizes)){
//					foreach($added_image_sizes as $key => $img_size_handle){
//						$custom_sizes[$img_size_handle] = ucwords(str_replace('_', ' ', $img_size_handle));
//					}
//				}
				$custom_sizes = array(
					'full' => JText::_('Original Size'),
					'thumbnail' => JText::_('Thumbnail'),
					'medium' => JText::_('Medium'),
					'large' => JText::_('Large')
				);

				break;
		}

		return $custom_sizes;
	}

	public static function is_ssl() {
		if ( isset($_SERVER['HTTPS']) ) {
			if ( 'on' == strtolower($_SERVER['HTTPS']) )
				return true;
			if ( '1' == $_SERVER['HTTPS'] )
				return true;
		} elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
			return true;
		}
		return false;
	}
	
	

	/**
	 *
	 * onAjax action handler
	 */
	public function onAjaxAction($data = null){
		$app = JFactory::getApplication();
		if (!isset($data['sliderid']) && $app->input->get('id') && $app->input->getString('layout')== 'edit' && $app->input->getString('view')=='slider') {
			ZTSliderFunctions::ajaxResponseError(JText::_('COM_ZT_LAYERSLIDER_NO_VALID_ID'));
			exit;
		}

		$data['sliderid'] = isset($data['sliderid']) ? $data['sliderid'] : null;

		$slider = new ZtSliderSlider($data['sliderid']);
		$slide = new ZTSliderSlide();//RevSlide();
		$operations = new ZTSliderOperations();
		$action = JFactory::getApplication()->input->getString('task');

		try{
			if(!JFactory::getUser()->authorise('core.admin')){
				switch($action){
					case 'changespecificnavigation':
					case 'changenavigations':
					case 'updatestaticcss':
					case 'addnewpreset':
					case 'updatepreset':
					case 'importslider':
					case 'importsliderslidersview':
					case 'importslidertemplateslidersview':
					case 'importslidetemplateslidersview':
					case 'fixdatabaseissues':
						ZTSliderFunctions::throwError(JText::_('COM_ZT_LAYERSLIDER_ONLY_ADMINISTRATOR'));//('Function Only Available for Adminstrators', 'revslider')
						exit;
						break;
					default:

						ZTSliderFunctions::throwError(JText::_('COM_ZT_LAYERSLIDER_ONLY_ADMINISTRATOR'));//__('Function Only Available for Adminstrators', 'revslider')
						exit;
						break;
				}
			}


			switch($action){
				case 'addnewpreset':

					if(!isset($data['settings']) || !isset($data['values'])) ZTSliderFunctions::ajaxResponseError(JText::_('COM_ZT_LAYERSLIDER_MISSING_VALUES'), false);//__('Missing values to add preset', 'revslider')

					$result = $operations->add_preset_setting($data);

					if($result === true){

						$presets = $operations->get_preset_settings();

						ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_PRESET_CREATED'), array('data' => $presets));//__('Preset created', 'revslider')
					}else{
						ZTSliderFunctions::ajaxResponseError($result, false);
					}

					exit;
					break;
				case 'updatepreset':

					if(!isset($data['name']) || !isset($data['values'])) ZTSliderFunctions::ajaxResponseError(JText::_('COM_ZT_LAYERSLIDER_MISSING_VALUE'), false);//__('Missing values to update preset', 'revslider')

					$result = $operations->update_preset_setting($data);

					if($result === true){

						$presets = $operations->get_preset_settings();

						ZTSliderFunctions::ajaxResponseSuccess( JText::_('COM_ZT_LAYERSLIDER_PRESET_CREATED') ,array('data' => $presets));//__('Preset created', 'revslider'),
					}else{
						ZTSliderFunctions::ajaxResponseError($result, false);
					}

					exit;
					break;
				case 'removepreset':

					if(!isset($data['name'])) ZTSliderFunctions::ajaxResponseError(JText::_('COM_ZT_LAYERSLIDER_MISSING_VALUE'), false);//__('Missing values to remove preset', 'revslider')

					$result = $operations->remove_preset_setting($data);

					if($result === true){

						$presets = $operations->get_preset_settings();

						ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_PRESET_DELETED'), array('data' => $presets));//__('Preset deleted'
					}else{
						ZTSliderFunctions::ajaxResponseError($result, false);
					}

					exit;
					break;
				case "exportslider":
					$sliderID = self::getGetVar("sliderid");
					$dummy = self::getGetVar("dummy");
					$slider->initByID($sliderID);
					$slider->exportSlider($dummy);
					break;
				case "importslider":
					$updateAnim = self::getPostGetVar("update_animations");
					$updateNav = self::getPostGetVar("update_navigations");
					$updateStatic = self::getPostGetVar("update_static_captions");
					self::importSliderHandle(null, $updateAnim, $updateStatic, $updateNav);
					break;
				case "importsliderslidersview":
					$viewBack = JRoute::_('index.php?option=com_zt_layerslider&view=sliders',false);
					$updateAnim = self::getPostGetVar("update_animations");
					$updateNav = self::getPostGetVar("update_navigations");
					$updateStatic = self::getPostGetVar("update_static_captions");
					self::importSliderHandle($viewBack, $updateAnim, $updateStatic, $updateNav);
					break;
				case "importslidertemplateslidersview":
					$viewBack = self::getViewUrl(self::VIEW_SLIDERS);
					$updateAnim = self::getPostGetVar("update_animations");
					$updateStatic = self::getPostGetVar("update_static_captions");
					self::importSliderTemplateHandle($viewBack, $updateAnim, $updateStatic);
					break;
				case "importslidetemplateslidersview":

					$redirect_id = esc_attr(self::getPostGetVar("redirect_id"));
					$viewBack = self::getViewUrl(self::VIEW_SLIDE,"id=$redirect_id");
					$updateAnim = self::getPostGetVar("update_animations");
					$updateStatic = self::getPostGetVar("update_static_captions");
					$slidenum = intval(self::getPostGetVar("slidenum"));
					$sliderid = intval(self::getPostGetVar("slider_id"));

					self::importSliderTemplateHandle($viewBack, $updateAnim, $updateStatic, array('slider_id' => $sliderid, 'slide_id' => $slidenum));
					break;
				case "createslider":
					$data = $operations->modifyCustomSliderParams($data);
					return $data;
					break;
				case "updateslider":
					$data = $operations->modifyCustomSliderParams($data);
					return $data;
					break;
				case "deleteslider":
				case "deletesliderstay":

					$isDeleted = $slider->deleteSliderFromData($data);

					if(is_array($isDeleted)){
						$isDeleted = implode(', ', $isDeleted);
						self::ajaxResponseError(JText::_("Template can't be deleted, it is still being used by the following Sliders: ", 'revslider').$isDeleted);
					}else{
						if($action == 'delete_slider_stay'){
							ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_SLIDER_DELETED'));//__("Slider deleted",'revslider')
						}else{
							ZTSliderFunctions::ajaxResponseSuccessRedirect( JText::_('COM_ZT_LAYERSLIDER_SLIDER_DELETED'),self::getViewUrl(self::VIEW_SLIDERS));//__("Slider deleted",'revslider'),
						}
					}
					break;
				case "duplicateslider":

					$slider->duplicateSliderFromData($data);

					ZTSliderFunctions::ajaxResponseSuccessRedirect(JText::_('COM_ZT_LAYERSLIDER_SUCCESS_REFRESSHING'), JRoute::_('index.php?option=com_zt_layerslider&view=sliders',false));//__("Success! Refreshing page...",'revslider')
					break;
				case "duplicateslider_package":

					$ret = $slider->duplicateSliderPackageFromData($data);

					if($ret !== true){
						ZTSliderFunctions::throwError($ret);
					}else{
						ZTSliderFunctions::ajaxResponseSuccessRedirect(JText::_('COM_ZT_LAYERSLIDER_SUCCESS_REFRESSHING'), 'index.php?option=com_zt_layerslider&view=sliders',false);
					}
					break;
				case "addslide":
				case "addbulkslide":
					if ($data['obj']) {
						if (!empty($data['obj']) and is_array($data['obj'])) {
							$new_data = array();
							$i = 0;
							foreach ($data['obj'] as $mg) {
								list($width, $height) = getimagesize(JUri::root().$mg);
								$new_data[$i]['url'] = $mg;
								$new_data[$i]['width'] = $width;
								$new_data[$i]['height'] = $height;
								$i++;
							}
						} else {
							ZTSliderFunctions::ajaxResponseError(JText::_('COM_ZT_LAYERSLIDER_DATA_NOT_VALID'));
						}
					} else {
						ZTSliderFunctions::ajaxResponseError(JText::_('COM_ZT_LAYERSLIDER_DATA_NOT_VALID'));
					}
					$data['obj'] = $new_data;
					$numSlides = $slider->createSlideFromData($data);
					$sliderID = $data["sliderid"];

					if($numSlides == 1){
						$responseText = JText::_("Slide Created");
					}else{
						$responseText = $numSlides . " ".JText::_("Slides Created");
					}

					$urlRedirect = JRoute::_('index.php?option=com_zt_layerslider&view=slides&id=new&slider_id='.intval($sliderID),false);
					ZTSliderFunctions::ajaxResponseSuccessRedirect($responseText,$urlRedirect);

					break;
				case "addslidefromslideview":
					$slideID = $slider->createSlideFromData($data,true);
					$urlRedirect = JRoute::_('index.php?option=com_zt_layerslider&view=slides&id='.(int)$slideID,false);
					$responseText = JText::_("Slide Created, redirecting...");
					ZTSliderFunctions::ajaxResponseSuccessRedirect($responseText,$urlRedirect);
					break;
				case 'copyslidetoslider':
					$slideID = (isset($data['redirect_id'])) ? $data['redirect_id'] : -1;

					if($slideID === -1) ZTSliderFunctions::throwError(JText::_('COM_ZT_LAYERSLIDER_MISSING_ID'));//__('Missing redirect ID!', 'revslider')

					$return = $slider->copySlideToSlider($data);

					if($return !== true) ZTSliderFunctions::throwError($return);

					$urlRedirect = JRoute::_('index.php?option=com_zt_layerslider&view=slides&id='.intval($slideID),false);
					$responseText = JText::_("Slide copied to current Slider, redirecting...");
					ZTSliderFunctions::ajaxResponseSuccessRedirect($responseText,$urlRedirect);
					break;
				case 'updateslide':
					$slide->updateSlideFromData($data);
					self::ajaxResponseSuccess(JText::_("Slide updated",'revslider'));
					break;
				case "updatestaticslide":
					$slide->updateStaticSlideFromData($data);
					ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_STATIC_GLOBAL_UPDATED'));//__("Static Global Layers updated",'revslider')
					break;
				case "deleteslide":
				case "deleteslidestay":
					$isPost = $slide->deleteSlideFromData($data);
					if($isPost)
						$message = JText::_("Post deleted",'revslider');
					else
						$message = JText::_("Slide deleted",'revslider');

					$sliderID = ZTSliderFunctions::getVal($data, "sliderID");
					if($action == 'deleteslidestay'){
						ZTSliderFunctions::ajaxResponseSuccess($message);
					}else{
						ZTSliderFunctions::ajaxResponseSuccessRedirect($message, JRoute::_('index.php?option=com_zt_layerslider&view=slides&id=new&slider_id='.$sliderID,false));
					}
					break;
				case "duplicateslide":
				case "duplicateslidestay":
					$return = $slider->duplicateSlideFromData($data);
					if($action == 'duplicateslidestay'){
						ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_SIDE_DUPPLICATED'), array('id' => $return[1]));//__("Slide duplicated",'revslider')
					}else{
						ZTSliderFunctions::ajaxResponseSuccessRedirect(JText::_('COM_ZT_LAYERSLIDER_SIDE_DUPPLICATED'),JRoute::_('index.php?option=com_zt_layerslider&view=slides&slider_id='.$return[0],false));
					}
					break;
				case "copymoveslide":
				case "copymoveslidestay":
					$sliderID = $slider->copyMoveSlideFromData($data);
					if($action == 'copymoveslidestay'){
						ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_SUCCESS'));//__("Success!",'revslider')
					}else{
						ZTSliderFunctions::ajaxResponseSuccessRedirect(JText::_('COM_ZT_LAYERSLIDER_SUCCESS_REFRESHING'), JRoute::_('index.php?option=com_zt_layerslider&view=slides&slider_id='.$sliderID,false));//__("Success! Refreshing page...",'revslider')
					}
					break;
				case "addslidetotemplate":
					$template = new ZTSliderTemplate();
					if(!isset($data['slideID']) || intval($data['slideID']) == 0){
						ZTSliderFunctions::throwError(JText::_('COM_ZT_LAYERSLIDER_NO_VALID_ID'));//__('No valid Slide ID given', 'revslider')
						exit;
					}
					if(!isset($data['title']) || strlen(trim($data['title'])) < 3){
						ZTSliderFunctions::throwError(JText::_('COM_ZT_LAYERSLIDER_NO_VALID_TITLE'));//__('No valid title given', 'revslider')
						exit;
					}
					if(!isset($data['settings']) || !isset($data['settings']['width']) || !isset($data['settings']['height'])){
						ZTSliderFunctions::throwError(JText::_('COM_ZT_LAYERSLIDER_NO_VALID_TITLE'));//__('No valid title given', 'revslider')
						exit;
					}

					$return = $template->copySlideToTemplates($data['slideID'], $data['title'], $data['settings']);

					if($return == false){
						ZTSliderFunctions::throwError(JText::_('COM_ZT_LAYERSLIDER_COULDNOT_SAVE_ASTEMPLATE'));//__('Could not save Slide as Template', 'revslider')
						exit;
					}

					//get HTML for template section
					ob_start();

					$rs_disable_template_script = true; //disable the script output of template selector file

					include(JPATH_ROOT.'/administrator/components/com_zt_layerslider/helpers/html/template-selector.php');

					$html = ob_get_contents();

					ob_clean();
					ob_end_clean();

					ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_SLIDE_ADD_TO_TEMPLATES'),array('HTML' => $html));//__('Slide added to Templates', 'revslider')
					exit;
					break;
					break;
				case "getstaticcss":
					$contentCSS = $operations->getStaticCss();
					self::ajaxResponseData($contentCSS);
					break;
				case "getdynamiccss":
					$contentCSS = $operations->getDynamicCss();
					self::ajaxResponseData($contentCSS);
					break;
				case "insertcaptionscss":

					$arrCaptions = $operations->insertCaptionsContentData($data);

					if($arrCaptions !== false){
						$db = new ZTSliderDB();
						$styles = $db->fetch(RevSliderGlobals::$table_css);
						$styles = ZTSliderCssParser::parseDbArrayToCss($styles, "\n");
						$styles = ZTSliderCssParser::compress_css($styles);
						$custom_css = $operations->getStaticCss();
						$custom_css = ZTSliderCssParser::compress_css($custom_css);

						$arrCSS = $operations->getCaptionsContentArray();
						$arrCssStyles = ZTSliderFunctions::jsonEncodeForClientSide($arrCSS);
						$arrCssStyles = $arrCSS;

						ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_CSS_SAVED'),array("arrCaptions"=>$arrCaptions,'compressed_css'=>$styles.$custom_css,'initstyles'=>$arrCssStyles));//__("CSS saved",'revslider'),
					}

					ZTSliderFunctions::throwError(JText::_('COM_ZT_LAYERSLIDER_CSS_COUD_NOT_SAVED'));//__('CSS could not be saved', 'revslider')
					exit();
					break;
				case "updatecaptionscss":

					$arrCaptions = $operations->updateCaptionsContentData($data);

					if($arrCaptions !== false){
						$db = new ZTSliderDB();
						$styles = $db->fetch(RevSliderGlobals::$table_css);
						$styles = ZTSliderCssParser::parseDbArrayToCss($styles, "\n");
						$styles = ZTSliderCssParser::compress_css($styles);
						$custom_css = $operations->getStaticCss();
						$custom_css = ZTSliderCssParser::compress_css($custom_css);

						$arrCSS = $operations->getCaptionsContentArray();
						$arrCssStyles = ZTSliderFunctions::jsonEncodeForClientSide($arrCSS);
						$arrCssStyles = $arrCSS;

						ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_CSS_SAVED'),array("arrCaptions"=>$arrCaptions,'compressed_css'=>$styles.$custom_css,'initstyles'=>$arrCssStyles));
					}

					ZTSliderFunctions::throwError(JText::_('COM_ZT_LAYERSLIDER_CSS_COUD_NOT_SAVED'));//__('CSS could not be saved', 'revslider')
					exit();
					break;
				case "updatecaptionsadvancedcss":
					$arrCaptions = $operations->updateAdvancedCssData($data);
					if($arrCaptions !== false){
						$db = new ZTSliderDB();
						$styles = $db->fetch(RevSliderGlobals::$table_css);
						$styles = ZTSliderCssParser::parseDbArrayToCss($styles, "\n");
						$styles = ZTSliderCssParser::compress_css($styles);
						$custom_css = $operations->getStaticCss();
						$custom_css = ZTSliderCssParser::compress_css($custom_css);

						$arrCSS = $operations->getCaptionsContentArray();
						$arrCssStyles = ZTSliderFunctions::jsonEncodeForClientSide($arrCSS);
						$arrCssStyles = $arrCSS;

						ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_CSS_SAVED'),array("arrCaptions"=>$arrCaptions,'compressed_css'=>$styles.$custom_css,'initstyles'=>$arrCssStyles));
					}
					ZTSliderFunctions::throwError(JText::_('COM_ZT_LAYERSLIDER_CSS_COUD_NOT_SAVED'));//__('CSS could not be saved', 'revslider')
					exit();
					break;
				case "renamecaptionscss":
					//rename all captions in all sliders with new handle if success
					$arrCaptions = $operations->renameCaption($data);

					$db = new ZTSliderDB();
					$styles = $db->fetch(RevSliderGlobals::$table_css);
					$styles = ZTSliderCssParser::parseDbArrayToCss($styles, "\n");
					$styles = ZTSliderCssParser::compress_css($styles);
					$custom_css = $operations->getStaticCss();
					$custom_css = ZTSliderCssParser::compress_css($custom_css);

					$arrCSS = $operations->getCaptionsContentArray();
					$arrCssStyles = ZTSliderFunctions::jsonEncodeForClientSide($arrCSS);
					$arrCssStyles = $arrCSS;

					ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_CLASS_NAME_RENAMED'),array("arrCaptions"=>$arrCaptions,'compressed_css'=>$styles.$custom_css,'initstyles'=>$arrCssStyles));//__("Class name renamed",'revslider')
					break;
				case "deletecaptionscss":
					$arrCaptions = $operations->deleteCaptionsContentData($data);

					$db = new ZTSliderDB();
					$styles = $db->fetch(RevSliderGlobals::$table_css);
					$styles = ZTSliderCssParser::parseDbArrayToCss($styles, "\n");
					$styles = ZTSliderCssParser::compress_css($styles);
					$custom_css = $operations->getStaticCss();
					$custom_css = ZTSliderCssParser::compress_css($custom_css);

					$arrCSS = $operations->getCaptionsContentArray();
					$arrCssStyles = ZtSliderFunctions::jsonEncodeForClientSide($arrCSS);
					$arrCssStyles = $arrCSS;

					ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_STYLE_DELETED'),array("arrCaptions"=>$arrCaptions,'compressed_css'=>$styles.$custom_css,'initstyles'=>$arrCssStyles));
					break;
				case "updatestaticcss":
					if(count($data) == 2 and is_null($data['sliderid'])) $data = $data[0];
					$staticCss = $operations->updateStaticCss($data);
					$this->db->setQuery('SELECT * FROM #__zt_layerslider_css');
					$styles = $this->db->loadAssocList();
					$styles = ZTSliderCssParser::parseDbArrayToCss($styles, "\n");
					$styles = ZTSliderCssParser::compress_css($styles);
					$custom_css = $operations->getStaticCss();
					$custom_css = ZTSliderCssParser::compress_css($custom_css);
					ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_CSS_SAVED'),array("css"=>$staticCss,'compressed_css'=>$styles.$custom_css));
					break;
				case "insertcustomanim":
					$arrAnims = $operations->insertCustomAnim($data); //$arrCaptions =
					ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_ANIMATION_SAVED') , $arrAnims); //,array("arrCaptions"=>$arrCaptions)
					break;
				case "updatecustomanim":
					$arrAnims = $operations->updateCustomAnim($data);
					ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_ANIMATION_SAVED') , $arrAnims); //,array("arrCaptions"=>$arrCaptions)
					break;
				case "updatecustomanimname":
					$arrAnims = $operations->updateCustomAnimName($data);
					ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_ANIMATION_SAVED') , $arrAnims); //,array("arrCaptions"=>$arrCaptions)
					break;
				case "deletecustomanim":
					$arrAnims = $operations->deleteCustomAnim($data);
					ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_ANIMATION_DELETED') , $arrAnims); //,array("arrCaptions"=>$arrCaptions)
					break;
				case "updateslidesorder":
					$slider->updateSlidesOrderFromData($data);
					ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_ORDER_UPDATED') );
					break;
				case "changeslidetitle":
					$slide->updateTitleByID($data);
					ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_TITLE_UPDATED') );
					break;
				case "changeslideimage":
					$slide->updateSlideImageFromData($data);
					$sliderID = ZTSliderFunctions::getVal($data, "slider_id");
					ZTSliderFunctions::ajaxResponseSuccessRedirect(JText::_('COM_ZT_LAYERSLIDER_SLIDE_CHANGED'), JRoute::_('index.php?option=com_zt_layerslider&view=slides&slider_id='.$sliderID,false));//__("Slide changed",'revslider')
					break;
				case "previewslide":
					if(count($data) == 2 and is_null($data['sliderid'])) $data = $data[0];
					$operations->putSlidePreviewByData($data);
					exit;
					break;
				case "previewslider":
					$sliderID = ZtSliderFunctions::getPostGetVariable("sliderid");
					$do_markup = ZtSliderFunctions::getPostGetVariable("only_markup");

					if($do_markup == 'true')
						$operations->previewOutputMarkup($sliderID);
					else
						$operations->previewOutput($sliderID);

					exit;
					break;
				case "getimportslidesdata":
					if(count($data) == 2 and is_null($data['sliderid'])) $data = $data[0];
					$slides = array();
					if(!is_array($data)){
						$slider->initByID(intval($data));

						$full_slides = $slider->getSlides(); //static slide is missing

						if(!empty($full_slides)){
							foreach($full_slides as $slide_id => $mslide){
								$slides[$slide_id]['layers'] = $mslide->getLayers();
								$slides[$slide_id]['params'] = $mslide->getParams();
							}
						}

						$staticID = $slide->getStaticSlideID($slider->getID());
						if($staticID !== false){
							$msl = new ZTSliderSlide();
							if(strpos($staticID, 'static_') === false){
								$staticID = 'static_'.$slider->getID();
							}
							$msl->initByID($staticID);
							if($msl->getID() !== ''){
								$slides[$msl->getID()]['layers'] = $msl->getLayers();
								$slides[$msl->getID()]['params'] = $msl->getParams();
								$slides[$msl->getID()]['params']['title'] = JText::_('Static Slide', 'revslider');
							}
						}
					}
					if(!empty($slides)){
						self::ajaxResponseData(array('slides' => $slides));
					}else{
						self::ajaxResponseData('');
					}
					break;
				case "createnavigationpreset":
					$nav = new ZTSliderNavigation();

					$return = $nav->add_preset($data);

					if($return === true){
						ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_NAVI_PRESET_SAVED'), array('navs' => $nav->get_all_navigations()));//('Navigation preset saved/updated', 'revslider')
					}else{
						if($return === false) $return = JText::_('COM_ZT_LAYERSLIDER_NAVI_PRESET_COULD_NOT_BE_SAVED');//__('Preset could not be saved/values are the same', 'revslider');
						ZTSliderFunctions::ajaxResponseError($return);
					}
					break;
				case "deletenavigationpreset":
					$nav = new ZTSliderNavigation();

					$return = $nav->delete_preset($data);

					if($return){
						ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_PRESET_DELETED'), array('navs' => $nav->get_all_navigations()));//__('Navigation preset deleted', 'revslider'
					}else{
						if($return === false) $return = JText::_('COM_ZT_LAYERSLIDER_PRESET_NOT_FOUND');//'Preset not found', 'revslider';
						ZTSliderFunctions::ajaxResponseError($return);
					}
					break;
				case "toggleslidestate":
					if(count($data) == 2 and is_null($data['sliderid'])) $data = $data[0];
					$currentState = $slide->toggleSlideStatFromData($data);
					self::ajaxResponseData(array("state"=>$currentState));
					break;
				case "toggleheroslide":
					if(count($data) == 2 and is_null($data['sliderid'])) $data = $data[0];
					$currentHero = $slider->setHeroSlide($data);
					ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_SLIDE_IS_NOW_THE_NEW'));//__('Slide is now the new active Hero Slide', 'revslider')
					break;
				case "slidelangoperation":
					$responseData = $slide->doSlideLangOperation($data);
					self::ajaxResponseData($responseData);
					break;
				case "updategeneralsettings":
					$operations->updateGeneralSettings($data);
					ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_GENERAL_SETTINGS_UPDATED'));//__("General settings updated",'revslider')
					break;
				case "fixdatabaseissues":
					update_option('revslider_change_database', true);
					RevSliderFront::createDBTables();

					ZTSliderFunctions::ajaxResponseSuccess(__('Database structure creation/update done','revslider'));
					break;
				case "updatepostssortby":
					$slider->updatePostsSortbyFromData($data);
					ZTSliderFunctions::ajaxResponseSuccess(__("Sortby updated",'revslider'));
					break;
				case "replaceimageurls":
					$slider->replaceImageUrlsFromData($data);
					ZTSliderFunctions::ajaxResponseSuccess(__("All Urls replaced",'revslider'));
					break;
				case "resetslidesettings":
					$slider->resetSlideSettings($data);
					ZTSliderFunctions::ajaxResponseSuccess(__("Settings in all Slides changed",'revslider'));
					break;
				case 'changespecificnavigation':
					$nav = new ZTSliderNavigation();
					if(count($data) == 2 and is_null($data['sliderid'])) $data = $data[0];
					$found = false;
					$navigations = $nav->get_all_navigations();
					foreach($navigations as $navig){
						if($data['id'] == $navig['id']){
							$found = true;
							break;
						}
					}
					if($found){
						$nav->create_update_navigation($data, $data['id']);
					}else{
						$nav->create_update_navigation($data);
					}

					ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_NAVIGATION_SAVED_UPDATE'), array('navs' => $nav->get_all_navigations()));//__('Navigation saved/updated', 'revslider')

					break;
				case 'changenavigations':
					$nav = new ZTSliderNavigation();
					$nav->create_update_full_navigation($data);

					ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_NAVIGATION_UPDATE'), array('navs' => $nav->get_all_navigations()));
					break;
				case 'deletenavigation':
					$nav = new ZTSliderNavigation();
					if(isset($data) && intval($data) > 0){
						$return = $nav->delete_navigation($data);

						if($return !== true){
							self::ajaxResponseError($return);
						}else{
							ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_NAVIGATION_DELETED'), array('navs' => $nav->get_all_navigations()));
						}
					}

					ZTSliderFunctions::ajaxResponseError(JText::_('COM_ZT_LAYERSLIDER_WRONG_ID_GIVEN'));//__('Wrong ID given', 'revslider')
					break;
				default:
					ZTSliderFunctions::ajaxResponseError("wrong ajax action: ".$action);
					exit;
					break;
			}

		}
		catch(Exception $e){

			$message = $e->getMessage();
			if($action == "preview_slide" || $action == "preview_slider"){
				echo $message;
				exit();
			}

			ZTSliderFunctions::ajaxResponseError($message);
		}

		//it's an ajax action, so exit
		ZTSliderFunctions::ajaxResponseError(JText::_('COM_ZT_LAYERSLIDER_NO_RESPONSE_OUTPUT'));//No response output on $action action. please check with the developer.
		exit();
	}
	
	
}