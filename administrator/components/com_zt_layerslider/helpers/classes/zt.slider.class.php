<?php
/**
 * Created by PhpStorm.
 * User: Thuan
 * Date: 5/9/2016
 * Time: 2:39 PM
 */
// No direct access.
defined('_JEXEC') or die;

JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_zt_layerslider/models');

class ZtSliderSlider {

    private $alias;
    private $favourite;
    private $id;
    private $title;
    private $state;
    private $attribs;
    private $language;
    private $arrSlides = null;

    const DEFAULT_POST_SORTBY = "ID";
    const DEFAULT_POST_SORTDIR = "DESC";

    const UPLOADSURLEXPORTZIP = '/images/layerslider/export.zip';
    const UPLOADPATH = '/images';

    const VALIDATE_NUMERIC = "numeric";
    const VALIDATE_EMPTY = "empty";
    const FORCE_NUMERIC = "force_numeric";

    const SLIDER_TYPE_GALLERY = "gallery";
    const SLIDER_TYPE_POSTS = "posts";
    const SLIDER_TYPE_TEMPLATE = "template";
    const SLIDER_TYPE_ALL = "all";

    const YOUTUBE_EXAMPLE_ID = "iyuxFo-WBiU";
    const DEFAULT_YOUTUBE_ARGUMENTS = "hd=1&amp;wmode=opaque&amp;showinfo=0&amp;rel=0;";
    const DEFAULT_VIMEO_ARGUMENTS = "title=0&amp;byline=0&amp;portrait=0&amp;api=1";
    const LINK_HELP_SLIDERS = "https://www.themepunch.com/revslider-doc/slider-revolution-documentation/?rev=rsb";
    const LINK_HELP_SLIDER = "https://www.themepunch.com/revslider-doc/slider-settings/?rev=rsb#generalsettings";
    const LINK_HELP_SLIDE_LIST = "https://www.themepunch.com/revslider-doc/individual-slide-settings/?rev=rsb";
    const LINK_HELP_SLIDE = "https://www.themepunch.com/revslider-doc/individual-slide-settings/?rev=rsb";


    public function __construct($id = null) {
        //load slider with an id
        $this->_slider = JModelLegacy::getInstance('Slider', 'ZT_LayersliderModel');
        $this->db = JFactory::getDbo();
        if (!is_null($id)) {
            $_item = $this->_slider->getItem((int)$id);
            if (is_object($_item))$_item = get_object_vars($_item);
            $this->initByDBData($_item);
        }
    }

    /**
     *
     * check if alias exists in DB
     */
    public function isAliasExists($alias){
        $this->db->setQuery('SELECT * FROM #__zt_layerslider_slider WHERE alias = "'.trim($alias).'" AND `type` != "template"');

        $response = $this->db->loadAssoc();

        return(!empty($response));
    }

    /**
     *
     * check if alias exists in DB
     */
    public function isAliasExistsInDB($alias){
        $where = " alias='".$alias."'";
        if(!empty($this->id)){
            $where .= " AND id != ".$this->id."  AND `type` != 'template'";
        }
        $this->db->setQuery("SELECT * FROM #__zt_layerslider_slider WHERE".$where);
        $response = $this->db->loadResult();
        return(!empty($response));

    }

    /**
     * get all used fonts in the current Slider
     * @since: 5.1.0
     */
    public function getUsedFonts($full = false){
        $this->validateInited();
        $gf = array();

        $sl = new ZTSliderSlide();

        $mslides = $this->getSlides(true);

        $staticID = $sl->getStaticSlideID($this->getID());
        if($staticID !== false){
            $msl = new ZTSliderSlide();
            if(strpos($staticID, 'static_') === false){
                $staticID = 'static_'.$this->getID();
            }
            $msl->initByID($staticID);
            if($msl->getID() !== ''){
                $mslides = array_merge($mslides, array($msl));
            }
        }

        if(!empty($mslides)){
            foreach($mslides as $ms){
                $mf = $ms->getUsedFonts($full);
                if(!empty($mf)){
                    foreach($mf as $mfk => $mfv){
                        if(!isset($gf[$mfk])){
                            $gf[$mfk] = $mfv;
                        }else{
                            foreach($mfv['variants'] as $mfvk => $mfvv){
                                $gf[$mfk]['variants'][$mfvk] = true;
                            }
                        }
                        $gf[$mfk]['slide'][] = array('id' => $ms->getID(), 'title' => $ms->getTitle());
                    }
                }
            }
        }

        return $gf;
    }
    /**
     * get slides from posts
     */
    public function getSlidesFromPosts($publishedOnly = false, $gal_ids = array()){

        $slideTemplates = $this->getSlidesFromGallery($publishedOnly);
        $slideTemplates = ZTSliderFunctions::assocToArray($slideTemplates);

        if(count($slideTemplates) == 0) return array();

        $sourceType = $this->getParam("source_type","gallery");

        if(!empty($gal_ids)) $sourceType = 'specific_posts'; //change to specific posts, give the gal_ids to the list

        switch($sourceType){
            case "posts":
                //check where to get posts from
                $sourceType = $this->getParam("fetch_type","cat_tag");
                switch($sourceType){
                    case 'cat_tag':
                    default:
                        $arrPosts = $this->getPostsFromCategories($publishedOnly);
                        break;
                    case 'related':
                        $arrPosts = $this->getPostsFromRelated();
                        break;
                    case 'popular':
                        $arrPosts = $this->getPostsFromPopular();
                        break;
                    case 'recent':
                        $arrPosts = $this->getPostsFromRecent();
                        break;
                    case 'next_prev':
                        $arrPosts = $this->getPostsNextPrevious();
                        break;
                }
                break;
            case "specific_posts":
                $arrPosts = $this->getPostsFromSpecificList($gal_ids);
                break;
            case 'woocommerce':
                $arrPosts = $this->getProductsFromCategories($publishedOnly);
                break;
            default:
                ZTSliderFunctions::throwError("getSlidesFromPosts error: This source type must be from posts.");
                break;
        }

        $arrSlides = array();

        $templateKey = 0;
        $numTemplates = count($slideTemplates);


        foreach($arrPosts as $postData){
            $slideTemplate = clone($slideTemplates[$templateKey]);

            //advance the templates
            $templateKey++;
            if($templateKey == $numTemplates){
                $templateKey = 0;
                $slideTemplates = $this->getSlidesFromGallery($publishedOnly); //reset as clone did not work properly
                $slideTemplates = ZTSliderFunctions::assocToArray($slideTemplates);
            }

            $slide = new RevSlide();
            $slide->initByPostData($postData, $slideTemplate, $this->id);
            $arrSlides[] = $slide;
        }

        $this->arrSlides = $arrSlides;

        return($arrSlides);
    }

    /**
     *
     * get posts from categories (by the slider params).
     */
    private function getPostsFromCategories($publishedOnly = false){
        $this->validateInited();

        $catIDs = $this->getParam("post_category");
        $data = RevSliderFunctionsWP::getCatAndTaxData($catIDs);

        $taxonomies = $data["tax"];
        $catIDs = $data["cats"];

        $sortBy = $this->getParam("post_sortby",self::DEFAULT_POST_SORTBY);
        $sortDir = $this->getParam("posts_sort_direction",self::DEFAULT_POST_SORTDIR);
        $maxPosts = $this->getParam("max_slider_posts","30");
        if(empty($maxPosts) || !is_numeric($maxPosts))
            $maxPosts = -1;

        $postTypes = $this->getParam("post_types","any");

        //set direction for custom order
        if($sortBy == RevSliderFunctionsWP::SORTBY_MENU_ORDER)
            $sortDir = RevSliderFunctionsWP::ORDER_DIRECTION_ASC;

        //Events integration
        $arrAddition = array();
        if($publishedOnly == true)
            $arrAddition["post_status"] = RevSliderFunctionsWP::STATE_PUBLISHED;

        if(RevSliderEventsManager::isEventsExists()){

            $filterType = $this->getParam("events_filter",RevSliderEventsManager::DEFAULT_FILTER);
            $arrAddition = RevSliderEventsManager::getWPQuery($filterType, $sortBy);
        }

        $slider_id = $this->getID();
        $arrPosts = RevSliderFunctionsWP::getPostsByCategory($slider_id, $catIDs,$sortBy,$sortDir,$maxPosts,$postTypes,$taxonomies,$arrAddition);

        return($arrPosts);
    }


    /**
     * get related posts from current one
     * @since: 5.1.1
     */
    public function getPostsFromRelated(){
        $my_posts = array();

        $sortBy = $this->getParam("post_sortby",self::DEFAULT_POST_SORTBY);
        $sortDir = $this->getParam("posts_sort_direction",self::DEFAULT_POST_SORTDIR);
        $max_posts = $this->getParam("max_slider_posts","30");
        if(empty($max_posts) || !is_numeric($max_posts))
            $max_posts = -1;

        $post_id = get_the_ID();

        $tags_string = '';
        $post_tags = get_the_tags();

        if ($post_tags) {
            foreach ($post_tags as $post_tag) {
                $tags_string .= $post_tag->slug . ',';
            }
        }

        $query = array(
            'exclude' => $post_id,
            'numberposts' => $max_posts,
            'order' => $sortDir,
            'tag' => $tags_string
        );

        if(strpos($sortBy, "meta_num_") === 0){
            $metaKey = str_replace("meta_num_", "", $sortBy);
            $query["orderby"] = "meta_value_num";
            $query["meta_key"] = $metaKey;
        }else
            if(strpos($sortBy, "meta_") === 0){
                $metaKey = str_replace("meta_", "", $sortBy);
                $query["orderby"] = "meta_value";
                $query["meta_key"] = $metaKey;
            }else
                $query["orderby"] = $sortBy;

        $get_relateds = apply_filters('revslider_get_related_posts', $query, $post_id);

        $tag_related_posts = get_posts($get_relateds);


        if(count($tag_related_posts) < $max_posts){
            $ignore = array();
            foreach($tag_related_posts as $tag_related_post){
                $ignore[] = $tag_related_post->ID;
            }
            $article_categories = get_the_category($post_id);
            $category_string = '';
            foreach($article_categories as $category) {
                $category_string .= $category->cat_ID . ',';
            }
            $max = $max_posts - count($tag_related_posts);

            $excl = implode(',', $ignore);
            $query = array(
                'exclude' => $excl,
                'numberposts' => $max,
                'category' => $category_string
            );

            if(strpos($sortBy, "meta_num_") === 0){
                $metaKey = str_replace("meta_num_", "", $sortBy);
                $query["orderby"] = "meta_value_num";
                $query["meta_key"] = $metaKey;
            }else
                if(strpos($sortBy, "meta_") === 0){
                    $metaKey = str_replace("meta_", "", $sortBy);
                    $query["orderby"] = "meta_value";
                    $query["meta_key"] = $metaKey;
                }else
                    $query["orderby"] = $sortBy;

            $get_relateds = apply_filters('revslider_get_related_posts', $query, $post_id);
            $cat_related_posts = get_posts($get_relateds);

            $tag_related_posts = $tag_related_posts + $cat_related_posts;
        }

        foreach($tag_related_posts as $post){
            $the_post = array();

            if(method_exists($post, "to_array"))
                $the_post = $post->to_array();
            else
                $the_post = (array)$post;

            if($the_post['ID'] == $post_id) continue;

            $my_posts[] = $the_post;
        }

        return $my_posts;
    }


    /**
     * get popular posts
     * @since: 5.1.1
     */
    public function getPostsFromPopular($max_posts = false){
        $post_id = get_the_ID();

        if($max_posts == false){
            $max_posts = $this->getParam("max_slider_posts","30");
            if(empty($max_posts) || !is_numeric($max_posts))
                $max_posts = -1;
        }else{
            $max_posts = intval($max_posts);
        }
        $my_posts = array();

        $args = array(
            'post_type' => 'any',
            'posts_per_page' => $max_posts,
            'suppress_filters' => 0,
            'meta_key'    => '_thumbnail_id',
            'orderby'     => 'comment_count',
            'order'       => 'DESC'
        );

        $args = apply_filters('revslider_get_popular_posts', $args, $post_id);
        $posts = get_posts($args);

        foreach($posts as $post){

            if(method_exists($post, "to_array"))
                $my_posts[] = $post->to_array();
            else
                $my_posts[] = (array)$post;
        }

        return $my_posts;
    }


    /**
     * get recent posts
     * @since: 5.1.1
     */
    public function getPostsFromRecent($max_posts = false){
        $post_id = get_the_ID();

        if($max_posts == false){
            $max_posts = $this->getParam("max_slider_posts","30");
            if(empty($max_posts) || !is_numeric($max_posts))
                $max_posts = -1;
        }else{
            $max_posts = intval($max_posts);
        }

        $my_posts = array();

        $args = array(
            'post_type' => 'any',
            'posts_per_page' => $max_posts,
            'suppress_filters' => 0,
            'meta_key'    => '_thumbnail_id',
            'orderby'     => 'date',
            'order'       => 'DESC'
        );
        $args = apply_filters('revslider_get_latest_posts', $args, $post_id);

        $posts = get_posts($args);

        foreach($posts as $post){

            if(method_exists($post, "to_array"))
                $my_posts[] = $post->to_array();
            else
                $my_posts[] = (array)$post;
        }

        return $my_posts;
    }


    /**
     * get slides from posts
     */
    public function getSlidesFromStream($publishedOnly = false){

        $slideTemplates = $this->getSlidesFromGallery($publishedOnly);
        $slideTemplates = ZTSliderFunctions::assocToArray($slideTemplates);

        if(count($slideTemplates) == 0) return array();

        $arrPosts = array();

        $max_allowed = 999999;
        $sourceType = $this->getParam("source_type","gallery");
        $additions = array('fb_type' => 'album');
        switch($sourceType){
            case "facebook":
                $facebook = new ZTSliderFacebook($this->getParam('facebook-transient','1200'));
                if($this->getParam('facebook-type-source','timeline') == "album"){
                    $arrPosts = $facebook->get_photo_set_photos($this->getParam('facebook-album'),$this->getParam('facebook-count',10),$this->getParam('facebook-app-id'),$this->getParam('facebook-app-secret'));
                }
                else{
                    $user_id = $facebook->get_user_from_url($this->getParam('facebook-page-url'));
                    $arrPosts = $facebook->get_photo_feed($user_id,$this->getParam('facebook-app-id'),$this->getParam('facebook-app-secret'),$this->getParam('facebook-count',10));
                    $additions['fb_type'] = $this->getParam('facebook-type-source','timeline');
                    $additions['fb_user_id'] = $user_id;
                }

                if(!empty($arrPosts)){
                    foreach($arrPosts as $k => $p){
                        if(!isset($p->status_type)) continue;

                        if(in_array($p->status_type, array("wall_post"))) unset($arrPosts[$k]);
                    }
                }
                $max_posts = $this->getParam('facebook-count', '25', self::FORCE_NUMERIC);
                $max_allowed = 25;
                break;
            case "twitter":
                $twitter = new ZTSliderTwitter($this->getParam('twitter-consumer-key'),$this->getParam('twitter-consumer-secret'),$this->getParam('twitter-access-token'),$this->getParam('twitter-access-secret'),$this->getParam('twitter-transient','1200'));
                $arrPosts = $twitter->get_public_photos($this->getParam('twitter-user-id'),$this->getParam('twitter-include-retweets'),$this->getParam( 'twitter-exclude-replies'),$this->getParam('twitter-count'),$this->getParam('twitter-image-only'));
                $max_posts = $this->getParam('twitter-count', '500', self::FORCE_NUMERIC);
                $max_allowed = 500;
                $additions['twitter_user'] = $this->getParam('twitter-user-id');
                break;
            case "instagram":
                $instagram = new ZTSliderInstagram($this->getParam('instagram-access-token'),$this->getParam('instagram-transient','1200'));
                if($this->getParam('instagram-type','user')!="hash"){
                    $search_user_id = $this->getParam('instagram-user-id');
                    $arrPosts = $instagram->get_public_photos($search_user_id,$this->getParam('instagram-count'));
                }
                else{
                    $search_hash_tag = $this->getParam('instagram-hash-tag');
                    $arrPosts = $instagram->get_tag_photos($search_hash_tag,$this->getParam('instagram-count'));
                }

                $max_posts = $this->getParam('instagram-count', '33', self::FORCE_NUMERIC);
                $max_allowed = 33;
                break;
            case "flickr":
                $flickr = new ZTSliderFlickr($this->getParam('flickr-api-key'),$this->getParam('flickr-transient','1200'));
                switch($this->getParam('flickr-type')){
                    case 'publicphotos':
                        $user_id = $flickr->get_user_from_url($this->getParam('flickr-user-url'));
                        $arrPosts = $flickr->get_public_photos($user_id,$this->getParam('flickr-count'));
                        break;
                    case 'gallery':
                        $gallery_id = $flickr->get_gallery_from_url($this->getParam('flickr-gallery-url'));
                        $arrPosts = $flickr->get_gallery_photos($gallery_id,$this->getParam('flickr-count'));
                        break;
                    case 'group':
                        $group_id = $flickr->get_group_from_url($this->getParam('flickr-group-url'));
                        $arrPosts = $flickr->get_group_photos($group_id,$this->getParam('flickr-count'));
                        break;
                    case 'photosets':
                        $arrPosts = $flickr->get_photo_set_photos($this->getParam('flickr-photoset'),$this->getParam('flickr-count'));
                        break;
                }
                $max_posts = $this->getParam('flickr-count', '99', self::FORCE_NUMERIC);
                break;
            case 'youtube':
                $channel_id = $this->getParam('youtube-channel-id');
                $youtube = new ZTSliderYoutube($this->getParam('youtube-api'),$channel_id,$this->getParam('youtube-transient','1200'));

                if($this->getParam('youtube-type-source')=="playlist"){
                    $arrPosts = $youtube->show_playlist_videos($this->getParam('youtube-playlist'),$this->getParam('youtube-count'));
                }
                else{
                    $arrPosts = $youtube->show_channel_videos($this->getParam('youtube-count'));
                }
                $additions['yt_type'] = $this->getParam('youtube-type-source','channel');
                $max_posts = $this->getParam('youtube-count', '25', self::FORCE_NUMERIC);
                $max_allowed = 50;
                break;
            case 'vimeo':
                $vimeo = new ZTSliderVimeo($this->getParam('vimeo-transient','1200'));
                $vimeo_type = $this->getParam('vimeo-type-source');

                switch ($vimeo_type) {
                    case 'user':
                        $arrPosts = $vimeo->get_vimeo_videos($vimeo_type,$this->getParam('vimeo-username'));
                        break;
                    case 'channel':
                        $arrPosts = $vimeo->get_vimeo_videos($vimeo_type,$this->getParam('vimeo-channelname'));
                        break;
                    case 'group':
                        $arrPosts = $vimeo->get_vimeo_videos($vimeo_type,$this->getParam('vimeo-groupname'));
                        break;
                    case 'album':
                        $arrPosts = $vimeo->get_vimeo_videos($vimeo_type,$this->getParam('vimeo-albumid'));
                        break;
                    default:
                        break;

                }
                $additions['vim_type'] = $this->getParam('vimeo-type-source','user');
                $max_posts = $this->getParam('vimeo-count', '25', self::FORCE_NUMERIC);
                $max_allowed = 60;
                break;
            default:
                ZTSliderFunctions::throwError("getSlidesFromStream error: This source type must be from stream.");
                break;
        }

        if($max_posts < 0) $max_posts *= -1;


        while(count($arrPosts) > $max_posts || count($arrPosts) > $max_allowed){
            array_pop($arrPosts);
        }


        $arrSlides = array();

        $templateKey = 0;
        $numTemplates = count($slideTemplates);

        if(empty($arrPosts)) ZTSliderFunctions::throwError(JText::_('Failed to load Stream', 'revslider'));

        foreach($arrPosts as $postData){
            $slideTemplate = $slideTemplates[$templateKey];

            //advance the templates
            $templateKey++;
            if($templateKey == $numTemplates)
                $templateKey = 0;

            $slide = new ZTSlide();
            $slide->initByStreamData($postData, $slideTemplate, $this->id, $sourceType, $additions);
            $arrSlides[] = $slide;
        }

        $this->arrSlides = $arrSlides;

        return($arrSlides);
    }

    /**
     * get slides of the current slider
     */
    public function getSlidesFromGallery($publishedOnly = false, $allwpml = false){

        $this->validateInited();

        if($publishedOnly == true){
            $where = ' AND state = 1 ';
        } else {$where='';}

        $arrSlides = array();
        $this->db->setQuery('SELECT * FROM #__zt_layerslider_slide WHERE slider_id='.$this->id. $where . ' ORDER BY ordering');
        $arrSlideRecords = $this->db->loadAssocList();

        $arrChildren = array();

        foreach ($arrSlideRecords as $record){
            $slide = new ZTSlide();
            $slide->initByData($record);

            $slideID = $slide->getID();
            $arrIdsAssoc[$slideID] = true;

            if($publishedOnly == true){
                $state = $slide->getParam("state", "published");
                if($state == "unpublished"){
                    continue;
                }
            }

            $parentID = $slide->getParam("parentid","");
            if(!empty($parentID)){
                if(!isset($arrChildren[$parentID]))
                    $arrChildren[$parentID] = array();
                $arrChildren[$parentID][] = $slide;
                if(!$allwpml)
                    continue;	//skip adding to main list
            }

            //init the children array
            $slide->setArrChildren(array());

            $arrSlides[$slideID] = $slide;
        }

        //add children array to the parent slides
        foreach($arrChildren as $parentID=>$arr){
            if(!isset($arrSlides[$parentID])){
                continue;
            }
            $arrSlides[$parentID]->setArrChildren($arr);
        }

        $this->arrSlides = $arrSlides;

        return($arrSlides);
    }

    /**
     *
     * get slides for output
     * one level only without children
     */
    public function getSlidesForOutput($publishedOnly = false, $lang = 'all',$gal_ids = array()){

        $isSlidesFromPosts = $this->isSlidesFromPosts();
        $isSlidesFromStream = $this->isSlidesFromStream();

        if($isSlidesFromPosts){
            $arrParentSlides = $this->getSlidesFromPosts($publishedOnly, $gal_ids);
        }elseif($isSlidesFromStream !== false){
            $arrParentSlides = $this->getSlidesFromStream($publishedOnly);
        }else{
            $arrParentSlides = $this->getSlides($publishedOnly);
        }

        if($lang == 'all' || $isSlidesFromPosts || $isSlidesFromStream)
            return($arrParentSlides);

        $arrSlides = array();
        foreach($arrParentSlides as $parentSlide){
            $parentLang = $parentSlide->getLang();
            if($parentLang == $lang)
                $arrSlides[] = $parentSlide;

            $childAdded = false;
            $arrChildren = $parentSlide->getArrChildren();
            foreach($arrChildren as $child){
                $childLang = $child->getLang();
                if($childLang == $lang){
                    $arrSlides[] = $child;
                    $childAdded = true;
                    break;
                }
            }

            if($childAdded == false && $parentLang == "all")
                $arrSlides[] = $parentSlide;
        }

        return($arrSlides);
    }

    /**
     *
     * get setting - start with slide
     */
    public function getStartWithSlideSetting(){

        $numSlides = $this->getNumSlides();

        $startWithSlide = $this->getParam("start_with_slide","1");
        if(is_numeric($startWithSlide)){
            $startWithSlide = (int)$startWithSlide - 1;
            if($startWithSlide < 0)
                $startWithSlide = 0;

            if($startWithSlide >= $numSlides)
                $startWithSlide = 0;

        }else
            $startWithSlide = 0;

        return($startWithSlide);
    }

    /**
     *
     * get slides for export
     */
    public function getSlidesForExport($useDummy = false){
        $arrSlides = $this->getSlidesFromGallery(false, true);
        $arrSlidesExport = array();

        foreach($arrSlides as $slide){
            $slideNew = array();
            $slideNew["id"] = $slide->getID();
            $slideNew["params"] = $slide->getParamsForExport();
            $slideNew["ordering"] = $slide->getOrder();
            $slideNew["layers"] = $slide->getLayersForExport($useDummy);
            $slideNew["settings"] = $slide->getSettings();

            $arrSlidesExport[] = $slideNew;
        }

        return($arrSlidesExport);
    }

    /**
     *
     * get slides for export
     */
    public function getStaticSlideForExport($useDummy = false){
        $arrSlidesExport = array();

        $slide = new ZTSlide();

        $staticID = $slide->getStaticSlideID($this->id);
        if($staticID !== false){
            $slideNew = array();
            $slide->initByStaticID($staticID);
            $slideNew["params"] = $slide->getParamsForExport();
            $slideNew["slide_order"] = $slide->getOrder();
            $slideNew["layers"] = $slide->getLayersForExport($useDummy);
            $slideNew["settings"] = $slide->getSettings();
            $arrSlidesExport[] = $slideNew;
        }

        return($arrSlidesExport);
    }

    /**
     *
     * duplicate slide from input data
     */
    public function duplicateSlideFromData($data){

        //init the slider
        $sliderID = ZTSliderFunctions::getVal($data, "sliderID");
        ZTSliderFunctions::validateNotEmpty($sliderID,"Slider ID");
        $this->initByID($sliderID);

        //get the slide id
        $slideID = ZTSliderFunctions::getVal($data, "slideID");
        ZTSliderFunctions::validateNotEmpty($slideID,"Slide ID");
        $newSlideID = $this->duplicateSlide($slideID);

        $this->duplicateChildren($slideID, $newSlideID);

        return(array($sliderID, $newSlideID));
    }


    /**
     * set new hero slide id for the Slider
     * @since: 5.0
     */
    public function setHeroSlide($data){
        $sliderID = ZTSliderFunctions::getVal($data, "slider_id");
        ZTSliderFunctions::validateNotEmpty($sliderID,"Slider ID");
        $this->initByID($sliderID);

        $new_slide_id = ZTSliderFunctions::getVal($data, "slide_id");
        ZTSliderFunctions::validateNotEmpty($new_slide_id,"Hero Slide ID");

        $this->updateParam(array('hero_active' => intval($new_slide_id)));

        return($new_slide_id);
    }

    /**
     * duplicate slide
     */
    public function duplicateSlide($slideID){
        $slide = new ZTSlide();
        $slide->initByID($slideID);
        $order = $slide->getOrder();
        $slides = $this->getSlidesFromGallery();
        $newOrder = $order+1;
        $this->shiftOrder($newOrder);

        //do duplication
        $sqlSelect = 'SELECT slider_id,ordering,attribs,layers,language FROM #__zt_layerslider_slide WHERE id='.intval($slideID);
        $this->db->setQuery($sqlSelect);
        $arr_insert =  $this->db->loadAssoc();

        if (empty($arr_insert)) ZTSliderFunctions::ajaxResponseError(JText::_('COM_ZT_LAYERSLIDER_NOTHING_TO_INSERT'));

        $sqlInsert = "INSERT INTO #__zt_layerslider_slide VALUES (NULL, ".$arr_insert['slider_id'].",".$newOrder.",1,'".$this->db->escape($arr_insert['attribs'])."','".$this->db->escape($arr_insert['layers'])."',1,'".$this->db->escape($arr_insert['language'])."','')";
        $this->db->setQuery($sqlInsert);
        $this->db->execute();
        $lastID = $this->db->insertid();

        return($lastID);
    }

    /**
     *
     * copy / move slide
     */
    private function copyMoveSlide($slideID,$targetSliderID,$operation){

        if($operation == "move"){

            $targetSlider = new ZTSlider();
            $targetSlider->initByID($targetSliderID);
            $maxOrder = $targetSlider->getMaxOrder();
            $newOrder = $maxOrder+1;

            //update children
            $arrChildren = $this->getArrSlideChildren($slideID);
            foreach($arrChildren as $child){
                $childID = $child->getID();
                $this->db->setQuery('UPDATE #__zt_layerslider_slide SET slider_id = '.$targetSliderID.', ordering = '.$newOrder.' WHERE id='.intval($childID));
                $this->db->execute();
            }
            $this->db->setQuery('UPDATE #__zt_layerslider_slide SET slider_id = '.$targetSliderID.', ordering = '.$newOrder.' WHERE id='.intval($slideID));
            $this->db->execute();

        }else{	//in place of copy
            $newSlideID = $this->duplicateSlide($slideID);
            $this->duplicateChildren($slideID, $newSlideID);

            $this->copyMoveSlide($newSlideID,$targetSliderID,"move");
        }
    }


    /**
     * copy / move slide from data
     */
    public function copyMoveSlideFromData($data){

        $sliderID = ZtSliderFunctions::getVal($data, "sliderID");
        ZtSliderFunctions::validateNotEmpty($sliderID,"Slider ID");
        $this->initByID($sliderID);

        $targetSliderID = ZtSliderFunctions::getVal($data, "targetSliderID");
        ZtSliderFunctions::validateNotEmpty($sliderID,"Target Slider ID");
        $this->initByID($sliderID);

        if($targetSliderID == $sliderID)
            ZtSliderFunctions::throwError("The target slider can't be equal to the source slider");

        $slideID = ZtSliderFunctions::getVal($data, "slideID");
        ZtSliderFunctions::validateNotEmpty($slideID,"Slide ID");

        $operation = ZtSliderFunctions::getVal($data, "operation");

        $this->copyMoveSlide($slideID,$targetSliderID,$operation);

        return($sliderID);
    }


    /**
     *
     * shift order of the slides from specific order
     */
    private function shiftOrder($fromOrder){
        $where = " slider_id = ".$this->id." and ordering >= ".$fromOrder;
        $sql = "UPDATE #__zt_layerslider_slide SET ordering=(ordering + 1) WHERE $where";
        $this->db->setQuery($sql);
        $this->db->execute();
    }


    /**
     * duplicate slide children
     * @param $slideID
     */
    private function duplicateChildren($slideID,$newSlideID){

        $arrChildren = $this->getArrSlideChildren($slideID);

        foreach($arrChildren as $childSlide){
            $childSlideID = $childSlide->getID();
            //duplicate
            $duplicatedSlideID = $this->duplicateSlide($childSlideID);

            //update parent id
            $duplicatedSlide = new ZTSlide();
            $duplicatedSlide->initByID($duplicatedSlideID);
            $duplicatedSlide->updateParentSlideID($newSlideID);
        }

    }


    /**
     *
     * validate settings for add
     */
    public function validateInputSettings($title,$alias,$params){
        ZtSliderFunctions::validateNotEmpty($title,"title");
        ZtSliderFunctions::validateNotEmpty($alias,"alias");

        if($this->isAliasExistsInDB($alias))
            ZtSliderFunctions::throwError(JText::_('COM_ZT_LAYERSLIDER_SLIDER_ALIAS ').$alias.JText::_('COM_ZT_LAYERSLIDER_EXISTS'));//"Some other slider with alias '$alias' already exists"

    }

    /**
     *
     * validate that the slider is inited. if not - throw error
     */
    private function validateInited(){
        if(empty($this->id))
            ZTSliderFunctions::throwError(JText::_('COM_ZT_LAYERSLIDER_NOT_INITIALZED'));//"The slider is not initialized!"
    }


    public function getParams() {
        return $this->attribs;
    }

    public function getLanguage() {
        return $this->language;
    }

    public function getSettings() {
        return($this->settings);
    }

    /**
     *
     * set slider params
     */
    public function setParams($arrParams){
        $this->arrParams = $arrParams;
        $this->attribs = $arrParams;
    }

    public function getAlias(){
        return($this->alias);
    }

    /**
     *
     * get array of slides numbers by id's
     */
    public function getSlidesNumbersByIDs($publishedOnly = false){

        if(empty($this->arrSlides))
            $this->getSlides($publishedOnly);

        $arrSlideNumbers = array();

        $counter = 0;

        if(empty($this->arrSlides)) return $arrSlideNumbers;

        foreach($this->arrSlides as $slide){
            $counter++;
            $slideID = $slide->getID();
            $arrSlideNumbers[$slideID] = $counter;
        }
        return($arrSlideNumbers);
    }


    /**
     * set specific slider param
     * @since: 5.1.1
     */
    public function setParam($param, $value){
        $this->arrParams[$param] = $value;
    }

    /**
     * Check if shortcodes exists in the content
     * @since: 5.0
     */
    public static function check_for_shortcodes($mid_content){
        if($mid_content !== null){
            if(has_shortcode($mid_content, 'gallery')){

                preg_match('/\[gallery.*ids=.(.*).\]/', $mid_content, $img_ids);

                if(isset($img_ids[1])){
                    if($img_ids[1] !== '') return explode(',', $img_ids[1]);
                }
            }
        }
        return false;
    }

    /**
     *
     * get parameter from params array. if no default, then the param is a must!
     */
    public function getParam($name,$default=null,$validateType = null,$title=""){

        if($default === null){
            $default = "";
        }

        $value = ZTSliderFunctions::getVal($this->arrParams, $name,$default);

        //validation:
        switch($validateType){
            case self::VALIDATE_NUMERIC:
            case self::VALIDATE_EMPTY:
                $paramTitle = !empty($title)?$title:$name;
                if($value !== "0" && $value !== 0 && empty($value))
                    ZTSliderFunctions::throwError("The param <strong>$paramTitle</strong> should not be empty.");
                break;
            case self::VALIDATE_NUMERIC:
                $paramTitle = !empty($title)?$title:$name;
                if(!is_numeric($value))
                    ZTSliderFunctions::throwError("The param <strong>$paramTitle</strong> should be numeric. Now it's: $value");
                break;
            case self::FORCE_NUMERIC:
                if(!is_numeric($value)){
                    $value = 0;
                    if(!empty($default))
                        $value = $default;
                }
                break;
        }

        return $value;
    }

    /**
     *
     * init the slider object by database id
     */
    public function initByID($sliderID){
        ZTSliderFunctions::validateNumeric($sliderID,"Slider ID");

        try{
            $sliderData = $this->_slider->getItem((int)$sliderID);
            if (is_object($sliderData)) $sliderData = get_object_vars($sliderData);
        }catch(Exception $e){
            $message = $e->getMessage();
            echo $message;
            exit;
        }

        $this->initByDBData($sliderData);
    }

    public function getId(){
        return $this->id;
    }

    public function getTitle(){
        return $this->title;
    }

    public function getState() {
        return $this->state;
    }


    public function isFavorite(){
        return $this->favourite ? true : false;
    }

    public function getShortcode(){
        $shortCode = '[zt_layerslider alias="'.$this->alias.'"]';
        return($shortCode);
    }

    public function isSlidesFromPosts() {
        $this->validateInited();
        $sourceType = $this->getParam("source_type","gallery");
        if($sourceType == "posts" || $sourceType == "specific_posts")
            return(true);

        return(false);

    }

    public function isSlidesFromStream() {
        $this->validateInited();
        $sourceType = $this->getParam("source_type","gallery");
        if($sourceType != "posts" && $sourceType != "specific_posts" && $sourceType != "gallery")
            return($sourceType);

        return(false);

    }

    /**
     * get real slides number, from posts, social streams ect.
     */
    public function getNumRealSlides($publishedOnly = false, $type = 'post'){
        $numSlides = count($this->arrSlides);

        switch($type){
            case 'post':
                if($this->getParam('fetch_type', 'cat_tag') == 'next_prev'){
                    $numSlides = 2;
                }else{
                    $numSlides = $this->getParam('max_slider_posts', count($this->arrSlides));
                    if(intval($numSlides) == 0) $numSlides = '∞';
                    //$this->getSlidesFromPosts($publishedOnly);
                    //$numSlides = count($this->arrSlides);
                }
                break;
            case 'facebook':
                $numSlides = $this->getParam('facebook-count', count($this->arrSlides));
                break;
            case 'twitter':
                $numSlides = $this->getParam('twitter-count', count($this->arrSlides));
                break;
            case 'instagram':
                $numSlides = $this->getParam('instagram-count', count($this->arrSlides));
                break;
            case 'flickr':
                $numSlides = $this->getParam('flickr-count', count($this->arrSlides));
                break;
            case 'youtube':
                $numSlides = $this->getParam('youtube-count', count($this->arrSlides));
                break;
            case 'vimeo':
                $numSlides = $this->getParam('vimeo-count', count($this->arrSlides));
                break;
        }

        return($numSlides);
    }

    /**
     *
     * get some var from array
     */
    public static function getVar($arr,$key,$defaultValue = ""){
        $val = $defaultValue;
        if(isset($arr[$key])) $val = $arr[$key];
        return($val);
    }


    public function getHtmlLink($link,$text,$id="",$class=""){

        if(!empty($class))
            $class = " class='$class'";

        if(!empty($id))
            $id = " id='$id'";

        $html = "<a href=\"$link\"".$id.$class.">$text</a>";
        return($html);
    }

    /*
     * get Slides from a slider
     */
    public function getSlides($publishedOnly = false){
        $arrSlides = $this->getSlidesFromGallery($publishedOnly);
        return($arrSlides);
    }

    /*
     *
     * get first slide
     */
    public function getFirstID() {
        if(empty($this->arrSlides)) return false;
        $_r = reset($this->arrSlides);
        return key($this->arrSlides);
    }

    /**
     *
     * export slider from data, output a file for download
     */
    public function exportSlider($useDummy = false){

        $this->validateInited();

        $sliderParams = $this->getParamsForExport();
        $arrSlides = $this->getSlidesForExport($useDummy);
        $arrStaticSlide = $this->getStaticSlideForExport($useDummy);

        $usedCaptions = array();
        $usedAnimations = array();
        $usedImages = array();
        $usedSVG = array();
        $usedVideos = array();
        $usedNavigations = array();

        $cfw = array();
        if(!empty($arrSlides) && count($arrSlides) > 0) $cfw = array_merge($cfw, $arrSlides);
        if(!empty($arrStaticSlide) && count($arrStaticSlide) > 0) $cfw = array_merge($cfw, $arrStaticSlide);


        //remove image_id as it is not needed in export
        if(!empty($arrSlides)){
            foreach($arrSlides as $k => $s){
                if(isset($arrSlides[$k]['params']['image_id'])) unset($arrSlides[$k]['params']['image_id']);
            }
        }
        if(!empty($arrStaticSlide)){
            foreach($arrStaticSlide as $k => $s){
                if(isset($arrStaticSlide[$k]['params']['image_id'])) unset($arrStaticSlide[$k]['params']['image_id']);
            }
        }

        if(!empty($cfw) && count($cfw) > 0){
            foreach($cfw as $key => $slide){
                if(isset($slide['params']['image']) && $slide['params']['image'] != '') $usedImages[$slide['params']['image']] = true; //['params']['image'] background url
                if(isset($slide['params']['background_image']) && $slide['params']['background_image'] != '') $usedImages[$slide['params']['background_image']] = true; //['params']['image'] background url
                if(isset($slide['params']['slide_thumb']) && $slide['params']['slide_thumb'] != '') $usedImages[$slide['params']['slide_thumb']] = true; //['params']['image'] background url

                //html5 video
                if(isset($slide['params']['background_type']) && $slide['params']['background_type'] == 'html5'){
                    if(isset($slide['params']['slide_bg_html_mpeg']) && $slide['params']['slide_bg_html_mpeg'] != '') $usedVideos[$slide['params']['slide_bg_html_mpeg']] = true;
                    if(isset($slide['params']['slide_bg_html_webm']) && $slide['params']['slide_bg_html_webm'] != '') $usedVideos[$slide['params']['slide_bg_html_webm']] = true;
                    if(isset($slide['params']['slide_bg_html_ogv']) && $slide['params']['slide_bg_html_ogv'] != '') $usedVideos[$slide['params']['slide_bg_html_ogv']] = true;
                }else{
                    if(isset($slide['params']['slide_bg_html_mpeg']) && $slide['params']['slide_bg_html_mpeg'] != '') $slide['params']['slide_bg_html_mpeg'] = '';
                    if(isset($slide['params']['slide_bg_html_webm']) && $slide['params']['slide_bg_html_webm'] != '') $slide['params']['slide_bg_html_webm'] = '';
                    if(isset($slide['params']['slide_bg_html_ogv']) && $slide['params']['slide_bg_html_ogv'] != '') $slide['params']['slide_bg_html_ogv'] = '';
                }

                //image thumbnail
                if(isset($slide['layers']) && !empty($slide['layers']) && count($slide['layers']) > 0){
                    foreach($slide['layers'] as $lKey => $layer){
                        if(isset($layer['style']) && $layer['style'] != '') $usedCaptions[$layer['style']] = true;
                        if(isset($layer['animation']) && $layer['animation'] != '' && strpos($layer['animation'], 'customin') !== false) $usedAnimations[str_replace('customin-', '', $layer['animation'])] = true;
                        if(isset($layer['endanimation']) && $layer['endanimation'] != '' && strpos($layer['endanimation'], 'customout') !== false) $usedAnimations[str_replace('customout-', '', $layer['endanimation'])] = true;
                        if(isset($layer['image_url']) && $layer['image_url'] != '') $usedImages[$layer['image_url']] = true; //image_url if image caption

                        if(isset($layer['type']) && ($layer['type'] == 'video' || $layer['type'] == 'audio')){

                            $video_data = (isset($layer['video_data'])) ? (array) $layer['video_data'] : array();

                            if(!empty($video_data) && isset($video_data['video_type']) && $video_data['video_type'] == 'html5'){

                                if(isset($video_data['urlPoster']) && $video_data['urlPoster'] != '') $usedImages[$video_data['urlPoster']] = true;

                                if(isset($video_data['urlMp4']) && $video_data['urlMp4'] != '') $usedVideos[$video_data['urlMp4']] = true;
                                if(isset($video_data['urlWebm']) && $video_data['urlWebm'] != '') $usedVideos[$video_data['urlWebm']] = true;
                                if(isset($video_data['urlOgv']) && $video_data['urlOgv'] != '') $usedVideos[$video_data['urlOgv']] = true;

                            }elseif(!empty($video_data) && isset($video_data['video_type']) && $video_data['video_type'] != 'html5'){ //video cover image
                                if($video_data['video_type'] == 'audio'){
                                    if(isset($video_data['urlAudio']) && $video_data['urlAudio'] != '') $usedVideos[$video_data['urlAudio']] = true;
                                }else{
                                    if(isset($video_data['previewimage']) && $video_data['previewimage'] != '') $usedImages[$video_data['previewimage']] = true;
                                }
                            }

                            if($video_data['video_type'] != 'html5'){
                                $video_data['urlMp4'] = '';
                                $video_data['urlWebm'] = '';
                                $video_data['urlOgv'] = '';
                            }
                            if($video_data['video_type'] != 'audio'){
                                $video_data['urlAudio'] = '';
                            }
                        }

                        if(isset($layer['type']) && $layer['type'] == 'svg'){
                            if(isset($layer['svg']) && isset($layer['svg']->src)){
                                if (@getimagesize($layer['svg']->src)) $layer['svg']->src = str_replace(JUri::root(),'',$layer['svg']->src);
                                $usedSVG[$layer['svg']->src] = true;
                            }
                        }
                    }
                }
            }
        }


        $arrSliderExport = array("params"=>$sliderParams,"slides"=>$arrSlides);
        if(!empty($arrStaticSlide))
            $arrSliderExport['static_slides'] = $arrStaticSlide;

        $strExport = serialize($arrSliderExport);

        //$strExportAnim = serialize(RevSliderOperations::getFullCustomAnimations());

        $exportname = (!empty($this->alias)) ? $this->alias.'.zip' : "slider_export.zip";

        //add navigations if not default animation
        if(isset($sliderParams['navigation_arrow_style'])) $usedNavigations[$sliderParams['navigation_arrow_style']] = true;
        if(isset($sliderParams['navigation_bullets_style'])) $usedNavigations[$sliderParams['navigation_bullets_style']] = true;
        if(isset($sliderParams['thumbnails_style'])) $usedNavigations[$sliderParams['thumbnails_style']] = true;
        if(isset($sliderParams['tabs_style'])) $usedNavigations[$sliderParams['tabs_style']] = true;
        $navs = false;
        if(!empty($usedNavigations)){
            $navis = new ZTSliderNavigation();
            $navs = $navis->export_navigation($usedNavigations);
            if($navs !== false) $navs = serialize($navs);
        }


        $styles = '';
        if(!empty($usedCaptions)){
            $captions = array();
            foreach($usedCaptions as $class => $val){
                $cap = ZTSliderOperations::getCaptionsContentArray($class);
                //set also advanced styles here...
                if(!empty($cap))
                    $captions[] = $cap;
            }
            $styles = ZTSliderCssParser::parseArrayToCss($captions, "\n", true);
        }

        $animations = '';
        if(!empty($usedAnimations)){
            $animation = array();
            foreach($usedAnimations as $anim => $val){
                $anima = ZTSliderOperations::getFullCustomAnimationByID($anim);
                if($anima !== false) $animation[] = $anima;

            }
            if(!empty($animation)) $animations = serialize($animation);
        }

        $usedImages = array_merge($usedImages, $usedVideos);

        $usepcl = false;
        if(class_exists('ZipArchive')){
            $zip = new ZipArchive;
            $success = $zip->open(JPATH_ROOT.self::UPLOADSURLEXPORTZIP, ZIPARCHIVE::CREATE | ZipArchive::OVERWRITE);

            if($success !== true)
                throw new Exception("Can't create zip file: ".JPATH_ROOT.self::UPLOADSURLEXPORTZIP);

        }

        //add svg to the zip
        if(!empty($usedSVG)){
            $content_url = JUri::root();
            $content_path = JPATH_ROOT.'/';
            foreach($usedSVG as $file => $val){
                if(strpos($file, 'http') !== false){ //remove all up to wp-content folder
                    $checkpath = str_replace($content_url, '', $file);

                    if(is_file($content_path.$checkpath)){
                        /*if(!$usepcl){
                            $zip->addFile($content_path.$checkpath, 'svg/'.$checkpath);
                        }else{
                            $v_list = $pclzip->add($content_path.$checkpath, PCLZIP_OPT_REMOVE_PATH, $content_path, PCLZIP_OPT_ADD_PATH, 'svg/');
                        }*/
                        $strExport = str_replace($file, $checkpath, $strExport);
                    }
                }
            }
        }

        //add images to zip
        if(!empty($usedImages)){
            $upload_dir = JPATH_ROOT.'/';
            $cont_url = JUri::root();
            $cont_url_no_www = str_replace('www.', '', JUri::root());
            $upload_dir_multisiteless = JPATH_ROOT.'/';

            foreach($usedImages as $file => $val){
                if($useDummy == "true"){ //only use dummy images

                }else{ //use the real images
                    if(strpos($file, 'http') !== false){
                        $remove = false;
                        $checkpath = str_replace(array($cont_url, $cont_url_no_www), '', $file);

                        if(is_file($upload_dir.$checkpath)){
                            if(!$usepcl){
                                $zip->addFile($upload_dir.$checkpath, 'images/'.$checkpath);
                            }
                            $remove = true;
                        }elseif(is_file($upload_dir_multisiteless.$checkpath)){
                            if(!$usepcl){
                                $zip->addFile($upload_dir_multisiteless.$checkpath, 'images/'.$checkpath);
                            }
                            $remove = true;
                        }

                        if($remove){ //as its http, remove this from strexport
                            $strExport = str_replace(array($cont_url.$checkpath, $cont_url_no_www.$checkpath), $checkpath, $strExport);
                        }
                    }else{
                        if(is_file($upload_dir.$file)){
                            if(!$usepcl){
                                $zip->addFile($upload_dir.$file, 'images/'.$file);
                            }
                        }elseif(is_file($upload_dir_multisiteless.$file)){
                            if(!$usepcl){
                                $zip->addFile($upload_dir_multisiteless.$file, 'images/'.$file);
                            }
                        }
                    }
                }
            }
        }

        if(!$usepcl){
            $zip->addFromString("slider_export.txt", $strExport); //add slider settings
        }
        if(strlen(trim($animations)) > 0){
            if(!$usepcl){
                $zip->addFromString("custom_animations.txt", $animations); //add custom animations
            }
        }
        if(strlen(trim($styles)) > 0){
            if(!$usepcl){
                $zip->addFromString("dynamic-captions.css", $styles); //add dynamic styles
            }
        }
        if(strlen(trim($navs)) > 0){
            if(!$usepcl){
                $zip->addFromString("navigation.txt", $navs); //add dynamic styles
            }
        }

        $operation = new ZTSliderOperations();
        $static_css = $operation->getStaticCss();
        if(trim($static_css) !== ''){
            if(!$usepcl){
                $zip->addFromString("static-captions.css", $static_css); //add slider settings
            }
        }
        $enable_slider_pack = true;

        if($enable_slider_pack){ //allow for slider packs the automatic creation of the info.cfg
            if(!$usepcl){
                $zip->addFromString('info.cfg', md5($this->alias)); //add slider settings
            }
        }

        if(!$usepcl){
            $zip->close();
        }else{
            //do nothing
        }


        header("Content-type: application/zip");
        header("Content-Disposition: attachment; filename=".$exportname);
        header("Pragma: no-cache");
        header("Expires: 0");
        readfile(JPATH_ROOT.self::UPLOADSURLEXPORTZIP);

        @unlink(JPATH_ROOT.self::UPLOADSURLEXPORTZIP); //delete file after sending it to user

        exit();
    }

    /**
     *
     * import slider from multipart form
     */
    public function importSliderFromPost($updateAnim = true, $updateStatic = true, $exactfilepath = false, $is_template = false, $single_slide = false, $updateNavigation = true){

        try{

            $sliderID = ZTSliderFunctions::getPostVariable("sliderid");
            $sliderExists = !empty($sliderID);

            if($sliderExists)
                $this->initByID($sliderID);

            if($exactfilepath !== false){
                $filepath = $exactfilepath;
            }else{
                switch ($_FILES['import_file']['error']) {
                    case UPLOAD_ERR_OK:
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        ZTSliderFunctions::throwError(JText::_('No file sent.'));
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        ZtSliderFunctions::throwError(JText::_('Exceeded filesize limit.'));

                    default:
                        break;
                }
                $filepath = $_FILES["import_file"]["tmp_name"];
            }

            if(file_exists($filepath) == false)
                ZtSliderFunctions::throwError(JText::_('Import file not found!!!'));

            $upload_dir = JPATH_ROOT;
            $d_path = $upload_dir.'/tmp/ztlayerslider/'.$_FILES["import_file"]["name"];
            $d_extr = $upload_dir.'/tmp/ztlayerslider/';
            //

            // Move uploaded file.
            jimport('joomla.filesystem.file');
            JFile::upload($filepath, $d_path, false, true);

            // Unpack the downloaded package file.
            $package = JInstallerHelper::unpack($d_path, true);
            //
            if ($package) {
                $d_extr = $package['dir'].'/';
            } else {
                ZTSliderFunctions::ajaxResponseError('UPLOAD_ZIP_ERROR');
            }

            if( $package ){
                $importZip = true; //raus damit..

                //read all files needed
                $content = ( @file_exists( $d_extr.'slider_export.txt' ) ) ? @file_get_contents( $d_extr.'slider_export.txt' ) : '';
                if($content == ''){
                    ZtSliderFunctions::throwError(JText::_('slider_export.txt does not exist!'));
                }
                $animations = ( @file_exists( $d_extr.'custom_animations.txt' ) ) ? @file_get_contents( $d_extr.'custom_animations.txt' ) : '';
                $dynamic = ( @file_exists( $d_extr.'dynamic-captions.css' ) ) ? @file_get_contents( $d_extr.'dynamic-captions.css' ) : '';
                $static = ( @file_exists( $d_extr.'static-captions.css' ) ) ? @file_get_contents( $d_extr.'static-captions.css' ) : '';
                $navigations = ( @file_exists( $d_extr.'navigation.txt' ) ) ? @file_get_contents( $d_extr.'navigation.txt' ) : '';

                $uid_check = ( @file_exists( $d_extr.'info.cfg' ) ) ? @file_get_contents( $d_extr.'info.cfg' ) : '';
                $version_check = ( @file_exists( $d_extr.'version.cfg' ) ) ? @file_get_contents( $d_extr.'version.cfg' ) : '';

                if($is_template !== false){
                    if($uid_check != $is_template){
                        return(array("success"=>false,"error"=>JText::_('Please select the correct zip file, checksum failed!', 'revslider')));
                    }
                }else{ //someone imported a template base Slider, check if it is existing in Base Sliders, if yes, check if it was imported
                    if($uid_check !== ''){
                        $tmpl = new ZTSliderTemplate();
                        $tmpl_slider = $tmpl->getThemePunchTemplateSliders();

                        foreach($tmpl_slider as $tp_slider){
                            if(!isset($tp_slider['installed'])) continue;

                            if($tp_slider['uid'] == $uid_check){
                                $is_template = $uid_check;
                                break;
                            }
                        }
                    }
                }

                //update/insert custom animations
                $animations = @unserialize($animations);
                if(!empty($animations)){
                    foreach($animations as $key => $animation){ //$animation['id'], $animation['handle'], $animation['params']
                        $animation['handle'] = is_array($animation['handle']) ? json_encode($animation['handle']) : $animation['handle'];
                        $this->db->setQuery('SELECT * FROM #__zt_layerslider_layer_animations WHERE handle="'.$this->db->escape($animation['handle']).'"');
                        $exist = $this->db->loadAssocList();
                        if(!empty($exist)){ //update the animation, get the ID
                            if($updateAnim == "true"){ //overwrite animation if exists
                                $arrUpdate = array();
                                $arrUpdate['params'] = stripslashes(json_encode(str_replace("'", '"', $animation['params'])));
                                $this->db->setQuery('UPDATE #__zt_layerslider_layer_animations SET params="'.$this->db->escape($arrUpdate['params']).'" WHERE handle="'.$this->db->escape($animation['handle']).'"');
                                $this->db->execute();
                                $anim_id = $exist['0']['id'];
                            }else{ //insert with new handle
                                $arrInsert = array();
                                $arrInsert["handle"] = 'copy_'.$animation['handle'];
                                $arrInsert["params"] = stripslashes(json_encode(str_replace("'", '"', $animation['params'])));
                                $this->db->setQuery('INSERT INTO #__zt_layerslider_layer_animations VALUES (NULL ,"'.$this->db->escape($arrInsert["handle"]).'","'.$this->db->escape($arrInsert["params"]).'","")');
                                $this->db->execute();
                                $anim_id = $this->db->insertid();
                            }
                        }else{ //insert the animation, get the ID
                            $arrInsert = array();
                            $arrInsert["handle"] = $animation['handle'];
                            $arrInsert["params"] = stripslashes(json_encode(str_replace("'", '"', $animation['params'])));
                            $this->db->setQuery('INSERT INTO #__zt_layerslider_layer_animations VALUES (NULL ,"'.$this->db->escape($arrInsert["handle"]).'","'.$this->db->escape($arrInsert["params"]).'","")');
                            $this->db->execute();
                            $anim_id = $this->db->insertid();
                        }

                        //and set the current customin-oldID and customout-oldID in slider params to new ID from $id
                        $content = str_replace(array('customin-'.$animation['id'].'"', 'customout-'.$animation['id'].'"'), array('customin-'.$anim_id.'"', 'customout-'.$anim_id.'"'), $content);
                    }
                    ZtSliderFunctions::dmp(JText::_("animations imported!",'revslider'));
                }

                //overwrite/append static-captions.css
                if(!empty($static)){
                    if($updateStatic == "true"){ //overwrite file
                        ZTSliderOperations::updateStaticCss($static);
                    }elseif($updateStatic == 'none'){
                        //do nothing
                    }else{//append
                        $static_cur = ZTSliderOperations::getStaticCss();
                        $static = $static_cur."\n".$static;
                        ZTSliderOperations::updateStaticCss($static);
                    }
                }

                //overwrite/create dynamic-captions.css
                //parse css to classes
                $dynamicCss = ZTSliderCssParser::parseCssToArray($dynamic);
                if(is_array($dynamicCss) && $dynamicCss !== false && count($dynamicCss) > 0){
                    foreach($dynamicCss as $class => $styles){
                        //check if static style or dynamic style
                        $class = trim($class);

                        if(strpos($class, ',') !== false && strpos($class, '.tp-caption') !== false){ //we have something like .tp-caption.redclass, .redclass
                            $class_t = explode(',', $class);
                            foreach($class_t as $k => $cl){
                                if(strpos($cl, '.tp-caption') !== false) $class = $cl;
                            }
                        }

                        if((strpos($class, ':hover') === false && strpos($class, ':') !== false) || //before, after
                            strpos($class," ") !== false || // .tp-caption.imageclass img or .tp-caption .imageclass or .tp-caption.imageclass .img
                            strpos($class,".tp-caption") === false || // everything that is not tp-caption
                            (strpos($class,".") === false || strpos($class,"#") !== false) || // no class -> #ID or img
                            strpos($class,">") !== false){ //.tp-caption>.imageclass or .tp-caption.imageclass>img or .tp-caption.imageclass .img
                            continue;
                        }

                        //is a dynamic style
                        if(strpos($class, ':hover') !== false){
                            $class = trim(str_replace(':hover', '', $class));
                            $arrInsert = array();
                            $arrInsert["hover"] = json_encode($styles);
                            $arrInsert["params"] = '';
                            $arrInsert["settings"] = json_encode(array('hover' => 'true'));
                        }else{
                            $arrInsert = array();
                            $arrInsert["hover"] =  null;
                            $arrInsert["params"] = json_encode($styles);
                            $arrInsert["settings"] = '';
                        }
                        //check if class exists
                        $class = is_array($class) ? json_encode($class) : $class;
                        $this->db->setQuery('SELECT * FROM #__zt_layerslider_css WHERE handle="'.$this->db->escape($class).'"');
                        $result = $this->db->loadAssocList();
                        if(!empty($result)){ //update
                            $this->db->setQuery('UPDATE #__zt_layerslider_css SET hover = "'.$this->db->escape($arrInsert["hover"]).'", params  = "'.$this->db->escape($arrInsert["params"]).'", settings="'.$this->db->escape($arrInsert["settings"]).'" WHERE handle="'.$this->db->escape($class).'"');
                            $this->db->execute();
                        }else{ //insert
                            $arrInsert["handle"] = $class;
                            $this->db->setQuery('INSERT INTO #__zt_layerslider_css VALUES (NULL ,"'.$this->db->escape($arrInsert["handle"]).'","'.$this->db->escape($arrInsert["settings"]).'","'.$this->db->escape($arrInsert["hover"]).'","'.$this->db->escape($arrInsert["params"]).'","")');
                            $this->db->execute();
                        }
                    }
                    ZTSliderFunctions::dmp(JText::_("dynamic styles imported!",'revslider'));
                }

                //update/insert custom animations
                $navigations = @unserialize($navigations);
                if(!empty($navigations)){

                    foreach($navigations as $key => $navigation){
                        $navigation['handle'] = is_array($navigation['handle']) ? json_encode($navigation['handle']) : $navigation['handle'];
                        $this->db->setQuery('SELECT * FROM #__zt_layerslider_navigations WHERE handle="'.$this->db->escape($navigation['handle']).'"');
                        $exist = $this->db->loadAssocList();
                        unset($navigation['id']);
                        $rh = $navigation["handle"];
                        if(!empty($exist)){ //create new navigation, get the ID
                            if($updateNavigation == "true"){ //overwrite navigation if exists
                                unset($navigation['handle']);
                                $this->db->setQuery('UPDATE #__zt_layerslider_navigations SET handle = "'.$this->db->escape($rh).'", css  = "'.$this->db->escape($navigation["css"]).'", markup="'.$this->db->escape($navigation["markup"]).'", settings="'.$this->db->escape($navigation["settings"]).'" WHERE handle="'.$this->db->escape($rh).'"');
                                $this->db->execute();

                            }else{
                                //insert with new handle
                                $navigation["handle"] = $navigation['handle'].'-'.date('is');
                                $navigation["name"] = $navigation['name'].'-'.date('is');
                                $content = str_replace($rh.'"', $navigation["handle"].'"', $content);
                                $navigation["css"] = str_replace('.'.$rh, '.'.$navigation["handle"], $navigation["css"]); //change css class to the correct new class
                                $this->db->setQuery('INSERT INTO #__zt_layerslider_navigations VALUES (NULL ,"'.$this->db->escape($navigation["name"]).'","'.$this->db->escape($navigation["handle"]).'","'.$this->db->escape($navigation["css"]).'","'.$this->db->escape($navigation["markup"]).'","'.$this->db->escape($navigation["settings"]).'")');
                                $this->db->execute();
                                $navi_id = $this->db->insertid();

                            }
                        }else{
                            $this->db->setQuery('INSERT INTO #__zt_layerslider_navigations VALUES (NULL ,"'.$this->db->escape($navigation["name"]).'","'.$this->db->escape($navigation["handle"]).'","'.$this->db->escape($navigation["css"]).'","'.$this->db->escape($navigation["markup"]).'","'.$this->db->escape($navigation["settings"]).'")');
                            $this->db->execute();
                        }
                    }
                    ZTSliderFunctions::dmp(JText::_("navigations imported!",'revslider'));
                }
            }else{

                @unlink($d_extr);

                return(array("success"=>false,"error"=>JText::_('CANT_UNZIP_THE_FILE')));
            }

            //$content = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $content); //clear errors in string //deprecated in newest php version
            $content = preg_replace_callback('!s:(\d+):"(.*?)";!', array('ZtSliderSlider', 'clear_error_in_string') , $content); //clear errors in string

            $arrSlider = @unserialize($content);
            if(empty($arrSlider)){
                @unlink($d_extr);
                ZTSliderFunctions::throwError(JText::_('Wrong export slider file format! Please make sure that the uploaded file is either a zip file with a correct slider_export.txt in the root of it or an valid slider_export.txt file.', 'revslider'));
            }

            //update slider params
            $sliderParams = $arrSlider["params"];

            if($sliderExists){
                $sliderParams["title"] = $this->arrParams["title"];
                $sliderParams["alias"] = $this->arrParams["alias"];
                $sliderParams["shortcode"] = $this->arrParams["shortcode"];
            }

            if(isset($sliderParams["background_image"]))
                $sliderParams["background_image"] = @getimagesize($sliderParams["background_image"]) ? $sliderParams["background_image"] : JUri::root().'/'.$sliderParams["background_image"];


            $import_statics = true;
            if(isset($sliderParams['enable_static_layers'])){
                if($sliderParams['enable_static_layers'] == 'off') $import_statics = false;
                unset($sliderParams['enable_static_layers']);
            }

            $sliderParams['version'] = $version_check;

            $json_params = json_encode($sliderParams);

            //update slider or create new
            if($sliderExists){
                $this->db->setQuery('UPDATE #__zt_layerslider_slider SET attribs = "'.$this->db->escape($json_params).'" WHERE id='.(int)$sliderID);
                $this->db->execute();
            }else{	//new slider
                $arrInsert = array();
                $arrInsert['params'] = $json_params;
                $arrInsert['type'] = '';
                //check if Slider with title and/or alias exists, if yes change both to stay unique


                $arrInsert['title'] = ZTSliderFunctions::getVal($sliderParams, 'title', 'Slider1');
                $arrInsert['alias'] = ZTSliderFunctions::getVal($sliderParams, 'alias', 'slider1');
                if($is_template === false){ //we want to stay at the given alias if we are a template
                    $talias = $arrInsert['alias'];
                    $ti = 1;
                    while($this->isAliasExistsInDB($talias)){ //set a new alias and title if its existing in database
                        $talias = $arrInsert['alias'] . $ti;
                        $ti++;
                    }

                    if($talias !== $arrInsert['alias']){
                        $sliderParams['title'] = $talias;
                        $sliderParams['alias'] = $talias;
                        $arrInsert['title'] = $talias;
                        $arrInsert['alias'] = $talias;
                        $json_params = json_encode($sliderParams);
                        $arrInsert['params'] = $json_params;
                    }
                }

                if($is_template !== false){ //add that we are an template
                    $arrInsert['type'] = 'template';
                    $sliderParams['uid'] = $is_template;
                    $json_params = json_encode($sliderParams);
                    $arrInsert['params'] = $json_params;
                }


                $this->db->setQuery('INSERT INTO #__zt_layerslider_slider VALUES (NULL ,"'.$arrInsert['title'].'","'.$arrInsert['alias'].'",1,"'.$this->db->escape($arrInsert['params']).'",0,1,0,"'.$arrInsert['type'].'","","'.JFactory::getApplication()->getUserStateFromRequest('com_zt_layerslider.filter.language','filter_language', '').'")');
                $this->db->execute();
                $sliderID = $this->db->insertid();
            }

            //-------- Slides Handle -----------

            //delete current slides
            if($sliderExists)
                $this->deleteAllSlides();

            //create all slides
            $arrSlides = $arrSlider["slides"];

            $alreadyImported = array();

            $content_url = JUri::root();
            $content_path = JPATH_ROOT.'/';

            //wpml compatibility
            $slider_map = array();
            foreach($arrSlides as $sl_key => $slide){
                $params = $slide["params"];
                $layers = $slide["layers"];
                $settings = (isset($slide["settings"])) ? $slide["settings"] : '';

                //convert params images:
                if($importZip === true){ //we have a zip, check if exists
                    //remove image_id as it is not needed in import
                    if(isset($params['image_id'])) unset($params['image_id']);

                    if(isset($params["image"])){
                        $params["image"] = ZTSliderFunctions::check_file_in_zip($d_extr, $params["image"], $sliderParams["alias"], $alreadyImported);
                    }

                    if(isset($params["background_image"])){
                        $params["background_image"] = ZTSliderFunctions::check_file_in_zip($d_extr, $params["background_image"], $sliderParams["alias"], $alreadyImported);
//                        $params["background_image"] = @getimagesize($params["background_image"]) ? $params["background_image"] : JUri::root().$params["background_image"];
                    }

                    if(isset($params["slide_thumb"])){
                        $params["slide_thumb"] = ZTSliderFunctions::check_file_in_zip($d_extr, $params["slide_thumb"], $sliderParams["alias"], $alreadyImported);
//                        $params["slide_thumb"] = @getimagesize($params["slide_thumb"]) ? $params["slide_thumb"] : JUri::root().$params["slide_thumb"];
                    }

                    if(isset($params["show_alternate_image"])){
                        $params["show_alternate_image"] = ZTSliderFunctions::check_file_in_zip($d_extr, $params["show_alternate_image"], $sliderParams["alias"], $alreadyImported);
//                        $params["show_alternate_image"] = @getimagesize($params["show_alternate_image"]) ? $params["show_alternate_image"] : JUri::root().$params["show_alternate_image"];
                    }
                    if(isset($params['background_type']) && $params['background_type'] == 'html5'){
                        if(isset($params['slide_bg_html_mpeg']) && $params['slide_bg_html_mpeg'] != ''){
                            $params["slide_bg_html_mpeg"]=  ZTSliderFunctions::check_file_in_zip($d_extr, $params["slide_bg_html_mpeg"], $sliderParams["alias"], $alreadyImported, true);
//                            $params["slide_bg_html_mpeg"] = @getimagesize($params["slide_bg_html_mpeg"]) ? $params["slide_bg_html_mpeg"] : JUri::root().$params["slide_bg_html_mpeg"];
                        }
                        if(isset($params['slide_bg_html_webm']) && $params['slide_bg_html_webm'] != ''){
                            $params["slide_bg_html_webm"] = ZTSliderFunctions::check_file_in_zip($d_extr, $params["slide_bg_html_webm"], $sliderParams["alias"], $alreadyImported, true);
//                            $params['slide_bg_html_webm'] = @getimagesize($params["slide_bg_html_webm"]) ? $params["slide_bg_html_webm"] : JUri::root().$params["slide_bg_html_webm"];
                        }
                        if(isset($params['slide_bg_html_ogv'])  && $params['slide_bg_html_ogv'] != ''){
                            $params["slide_bg_html_ogv"] = ZTSliderFunctions::check_file_in_zip($d_extr, $params["slide_bg_html_ogv"], $sliderParams["alias"], $alreadyImported, true);
//                            $params['slide_bg_html_ogv'] = @getimagesize($params["slide_bg_html_ogv"])? $params["slide_bg_html_ogv"] : JUri::root().$params["slide_bg_html_ogv"];
                        }
                    }
                }


                //convert layers images:
                foreach($layers as $key=>$layer){
                    //import if exists in zip folder
                    if($importZip === true){ //we have a zip, check if exists
                        if(isset($layer["image_url"])){
                            $layer["image_url"] = ZTSliderFunctions::check_file_in_zip($d_extr, $layer["image_url"], $sliderParams["alias"], $alreadyImported);
//                            $layer["image_url"] = @getimagesize($layer["image_url"]) ? $layer["image_url"] : JUri::root().$layer["image_url"];
                        }
                        if(isset($layer['type']) && ($layer['type'] == 'video' || $layer['type'] == 'audio')){

                            $video_data = (isset($layer['video_data'])) ? (array) $layer['video_data'] : array();

                            if(!empty($video_data) && isset($video_data['video_type']) && $video_data['video_type'] == 'html5'){

                                if(isset($video_data['urlPoster']) && $video_data['urlPoster'] != ''){
                                    $video_data['urlPoster'] = ZTSliderFunctions::check_file_in_zip($d_extr, $video_data["urlPoster"], $sliderParams["alias"], $alreadyImported);
//                                    $video_data['urlPoster'] = @getimagesize($video_data['urlPoster']) ? $video_data['urlPoster'] : JUri::root().$video_data['urlPoster'];
                                }

                                if(isset($video_data['urlMp4']) && $video_data['urlMp4'] != ''){
                                    $video_data['urlMp4'] = ZTSliderFunctions::check_file_in_zip($d_extr, $video_data["urlMp4"], $sliderParams["alias"], $alreadyImported, true);
//                                    $video_data['urlMp4']  = @getimagesize($video_data['urlMp4'])? $video_data['urlMp4'] : JUri::root().$video_data['urlMp4'];
                                }
                                if(isset($video_data['urlWebm']) && $video_data['urlWebm'] != ''){
                                    $video_data['urlWebm'] = ZTSliderFunctions::check_file_in_zip($d_extr, $video_data["urlWebm"], $sliderParams["alias"], $alreadyImported, true);
//                                    $video_data['urlWebm'] = @getimagesize($video_data['urlWebm'])? $video_data['urlWebm'] : JUri::root().$video_data['urlWebm'];
                                }
                                if(isset($video_data['urlOgv']) && $video_data['urlOgv'] != ''){
                                    $video_data['urlOgv'] = ZTSliderFunctions::check_file_in_zip($d_extr, $video_data["urlOgv"], $sliderParams["alias"], $alreadyImported, true);
//                                    $video_data['urlOgv'] = @getimagesize($video_data['urlOgv']) ? $video_data['urlOgv'] : JUri::root().$video_data['urlOgv'];
                                }

                            }elseif(!empty($video_data) && isset($video_data['video_type']) && $video_data['video_type'] != 'html5'){ //video cover image
                                if($video_data['video_type'] == 'audio'){
                                    if(isset($video_data['urlAudio']) && $video_data['urlAudio'] != ''){
                                        $video_data['urlAudio'] = ZTSliderFunctions::check_file_in_zip($d_extr, $video_data["urlAudio"], $sliderParams["alias"], $alreadyImported, true);
//                                        $video_data['urlAudio'] = @getimagesize($video_data['urlAudio'])?$video_data['urlAudio']:JUri::root().$video_data['urlAudio'];
                                    }
                                }else{
                                    if(isset($video_data['previewimage']) && $video_data['previewimage'] != ''){
                                        $video_data['previewimage'] = ZTSliderFunctions::check_file_in_zip($d_extr, $video_data["previewimage"], $sliderParams["alias"], $alreadyImported);
//                                        $video_data['previewimage'] = @getimagesize($video_data['previewimage']) ? $video_data['previewimage'] : JUri::root().$video_data['previewimage'];
                                    }
                                }
                            }

                            $layer['video_data'] = $video_data;

                        }

                        if(isset($layer['type']) && $layer['type'] == 'svg'){
                            if(isset($layer['svg']) && isset($layer['svg']->src)){
                                $layer['svg']->src = $content_url.$layer['svg']->src;
                            }
                        }

                    }

                    $layer['text'] = stripslashes($layer['text']);
                    $layers[$key] = $layer;
                }
                $arrSlides[$sl_key]['layers'] = $layers;

                //create new slide
                $arrCreate = array();
                $arrCreate["slider_id"] = $sliderID;
                $arrCreate["slide_order"] = isset($slide["ordering"]) ? $slide["ordering"] : (isset($slide["slide_order"]) ? $slide["slide_order"] : '');

                $my_layers = json_encode($layers);
                if(empty($my_layers))
                    $my_layers = stripslashes(json_encode($layers));
                $my_params = json_encode($params);
                if(empty($my_params))
                    $my_params = stripslashes(json_encode($params));
                $my_settings = json_encode($settings);
                if(empty($my_settings))
                    $my_settings = stripslashes(json_encode($settings));



                $arrCreate["layers"] = $my_layers;
                $arrCreate["params"] = $my_params;
                $arrCreate["settings"] = $my_settings;

                $this->db->setQuery('INSERT INTO #__zt_layerslider_slide VALUES (NULL ,'.$arrCreate["slider_id"].','.$arrCreate["slide_order"].',1,"'.$this->db->escape($arrCreate["params"]).'","'.$this->db->escape($arrCreate["layers"]).'",1,"'.JFactory::getApplication()->getUserStateFromRequest('com_zt_layerslider.filter.language','filter_language', '').'","'.$this->db->escape($arrCreate["settings"]).'")');
                $this->db->execute();
                $last_id = $this->db->insertid();

                if(isset($slide['id'])){
                    $slider_map[$slide['id']] = $last_id;
                }
            }

            //change for WPML the parent IDs if necessary
            if(!empty($slider_map)){
                foreach($arrSlides as $sl_key => $slide){
                    if(isset($slide['params']['parentid']) && isset($slider_map[$slide['params']['parentid']])){
                        $update_id = $slider_map[$slide['id']];
                        $parent_id = $slider_map[$slide['params']['parentid']];

                        $arrCreate = array();

                        $arrCreate["params"] = $slide['params'];
                        $arrCreate["params"]['parentid'] = $parent_id;
                        $my_params = json_encode($arrCreate["params"]);
                        if(empty($my_params))
                            $my_params = stripslashes(json_encode($arrCreate["params"]));

                        $arrCreate["params"] = $my_params;

                        $this->db->setQuery('UPDATE #__zt_layerslider_slide SET attribs="'.$this->db->escape($arrCreate["params"]).'" WHERE id='.intval($update_id));

                        $this->db->execute();
                    }

                    $did_change = false;
                    foreach($slide['layers'] as $key => $value){
                        if(isset($value['layer_action'])){
                            if(isset($value['layer_action']->jump_to_slide) && !empty($value['layer_action']->jump_to_slide)){
                                $value['layer_action']->jump_to_slide = (array)$value['layer_action']->jump_to_slide;
                                foreach($value['layer_action']->jump_to_slide as $jtsk => $jtsval){
                                    if(isset($slider_map[$jtsval])){
                                        $slide['layers'][$key]['layer_action']->jump_to_slide[$jtsk] = $slider_map[$jtsval];
                                        $did_change = true;
                                    }
                                }
                            }
                        }

                        $link_slide = ZtSliderFunctions::getVal($value, 'link_slide', false);
                        if($link_slide != false && $link_slide !== 'nothing'){ //link to slide/scrollunder is set, move it to actions
                            if(!isset($slide['layers'][$key]['layer_action'])) $slide['layers'][$key]['layer_action'] = new stdClass();
                            switch($link_slide){
                                case 'link':
                                    $link = ZtSliderFunctions::getVal($value, 'link');
                                    $link_open_in = ZtSliderFunctions::getVal($value, 'link_open_in');
                                    $slide['layers'][$key]['layer_action']->action = array('a' => 'link');
                                    $slide['layers'][$key]['layer_action']->link_type = array('a' => 'a');
                                    $slide['layers'][$key]['layer_action']->image_link = array('a' => $link);
                                    $slide['layers'][$key]['layer_action']->link_open_in = array('a' => $link_open_in);

                                    unset($slide['layers'][$key]['link']);
                                    unset($slide['layers'][$key]['link_open_in']);
                                case 'next':
                                    $slide['layers'][$key]['layer_action']->action = array('a' => 'next');
                                    break;
                                case 'prev':
                                    $slide['layers'][$key]['layer_action']->action = array('a' => 'prev');
                                    break;
                                case 'scroll_under':
                                    $scrollunder_offset = ZtSliderFunctions::getVal($value, 'scrollunder_offset');
                                    $slide['layers'][$key]['layer_action']->action = array('a' => 'scroll_under');
                                    $slide['layers'][$key]['layer_action']->scrollunder_offset = array('a' => $scrollunder_offset);

                                    unset($slide['layers'][$key]['scrollunder_offset']);
                                    break;
                                default: //its an ID, so its a slide ID
                                    $slide['layers'][$key]['layer_action']->action = array('a' => 'jumpto');
                                    $slide['layers'][$key]['layer_action']->jump_to_slide = array('a' => $slider_map[$link_slide]);
                                    break;

                            }
                            $slide['layers'][$key]['layer_action']->tooltip_event = array('a' => 'click');

                            unset($slide['layers'][$key]['link_slide']);

                            $did_change = true;
                        }


                        if($did_change === true){

                            $arrCreate = array();
                            $my_layers = json_encode($slide['layers']);
                            if(empty($my_layers))
                                $my_layers = stripslashes(json_encode($layers));

                            $arrCreate['layers'] = $my_layers;

                            $this->db->setQuery('UPDATE #__zt_layerslider_slide SET layers="'.$this->db->escape($arrCreate['layers']).'" WHERE id='.intval($slide['id']));

                            $this->db->execute();
                        }
                    }
                }
            }

            //check if static slide exists and import
            if(isset($arrSlider['static_slides']) && !empty($arrSlider['static_slides']) && $import_statics){
                $static_slide = $arrSlider['static_slides'];
                foreach($static_slide as $slide){

                    $params = $slide["params"];
                    $layers = $slide["layers"];
                    $settings = (isset($slide["settings"])) ? $slide["settings"] : '';

                    //remove image_id as it is not needed in import
                    if(isset($params['image_id'])) unset($params['image_id']);

                    //convert params images:
                    if(isset($params["image"])){
                        //import if exists in zip folder
                        if(strpos($params["image"], 'http') !== false){
                        }else{
                            if(trim($params["image"]) !== ''){
                                if($importZip === true){ //we have a zip, check if exists
                                    $image = @file_exists( $d_extr.'images/'.$params["image"] );
                                    if(!$image){
                                        echo $params["image"].JText::_(' not found!<br>', 'revslider');
                                    }else{
                                        if(!isset($alreadyImported['images/'.$params["image"]])){
                                            $importImage = ZTSliderFunctions::import_media($d_extr.'images/'.$params["image"], $sliderParams["alias"].'/',$layer["image_url"]);

                                            if($importImage !== false){
                                                $alreadyImported['images/'.$params["image"]] = $importImage['path'];

                                                $params["image"] = $importImage['path'];
                                            }
                                        }else{
                                            $params["image"] = $alreadyImported['images/'.$params["image"]];
                                        }


                                    }
                                }
                            }
                            $params["image"] = @getimagesize($params["image"]) ? $params["image"] : (@getimagesize(JUri::root().$params["image"]) ? JUri::root().$params["image"] : '');
                        }
                    }

                    //convert layers images:
                    foreach($layers as $key=>$layer){
                        if(isset($layer["image_url"])){
                            //import if exists in zip folder
                            if(trim($layer["image_url"]) !== ''){
                                if(strpos($layer["image_url"], 'http') !== false){
                                }else{
                                    if($importZip === true){ //we have a zip, check if exists
                                        $image_url = @file_exists( $d_extr.'/images/'.$layer["image_url"] );
                                        if(!$image_url){
                                            echo $layer["image_url"].JText::_(' not found!<br>');
                                        }else{
                                            if(!isset($alreadyImported['images/'.$layer["image_url"]])){
                                                $importImage = ZTSliderFunctions::import_media($d_extr.'images/'.$layer["image_url"], $sliderParams["alias"].'/',$layer["image_url"]);

                                                if($importImage !== false){
                                                    $alreadyImported['images/'.$layer["image_url"]] = $importImage['path'];

                                                    $layer["image_url"] = $importImage['path'];
                                                }
                                            }else{
                                                $layer["image_url"] = $alreadyImported['images/'.$layer["image_url"]];
                                            }
                                        }
                                    }
                                }
                            }
                            $layer["image_url"] = @getimagesize($layer["image_url"]) ? $layer["image_url"]  : JUri::root().$layer["image_url"];
                        }

                        $layer['text'] = stripslashes($layer['text']);

                        if(isset($layer['type']) && ($layer['type'] == 'video' || $layer['type'] == 'audio')){

                            $video_data = (isset($layer['video_data'])) ? (array) $layer['video_data'] : array();

                            if(!empty($video_data) && isset($video_data['video_type']) && $video_data['video_type'] == 'html5'){

                                if(isset($video_data['urlPoster']) && $video_data['urlPoster'] != ''){
                                    $video_data['urlPoster'] = ZTSliderFunctions::check_file_in_zip($d_extr, $video_data["urlPoster"], $sliderParams["alias"], $alreadyImported);
//                                    $video_data['urlPoster'] = @getimagesize($video_data['urlPoster']) ? $video_data['urlPoster'] : JUri::root().$video_data['urlPoster'];
                                }

                                if(isset($video_data['urlMp4']) && $video_data['urlMp4'] != ''){
                                    $video_data['urlMp4'] = ZTSliderFunctions::check_file_in_zip($d_extr, $video_data["urlMp4"], $sliderParams["alias"], $alreadyImported);
//                                    $video_data['urlMp4'] = @getimagesize($video_data['urlMp4']) ? $video_data['urlMp4'] : JUri::root().$video_data['urlMp4'];
                                }
                                if(isset($video_data['urlWebm']) && $video_data['urlWebm'] != ''){
                                    $video_data['urlWebm'] = ZTSliderFunctions::check_file_in_zip($d_extr, $video_data["urlWebm"], $sliderParams["alias"], $alreadyImported);
//                                    $video_data['urlWebm'] = @getimagesize($video_data['urlWebm']) ? $video_data['urlWebm'] : JUri::root().$video_data['urlWebm'];

                                }
                                if(isset($video_data['urlOgv']) && $video_data['urlOgv'] != ''){
                                    $video_data['urlOgv'] = ZTSliderFunctions::check_file_in_zip($d_extr, $video_data["urlOgv"], $sliderParams["alias"], $alreadyImported);
//                                    $video_data['urlOgv'] = @getimagesize($video_data['urlOgv']) ? $video_data['urlOgv'] : JUri::root().$video_data['urlOgv'];
                                }

                            }elseif(!empty($video_data) && isset($video_data['video_type']) && $video_data['video_type'] != 'html5'){ //video cover image
                                if($video_data['video_type'] == 'audio'){
                                    if(isset($video_data['urlAudio']) && $video_data['urlAudio'] != ''){
                                        $video_data['urlAudio'] = ZTSliderFunctions::check_file_in_zip($d_extr, $video_data["urlAudio"], $sliderParams["alias"], $alreadyImported);
//                                        $video_data['urlAudio'] = @getimagesize($video_data['urlAudio']) ? $video_data['urlAudio'] : JUri::root().$video_data['urlAudio'];
                                    }
                                }else{
                                    if(isset($video_data['previewimage']) && $video_data['previewimage'] != ''){
                                        $video_data['previewimage'] = ZTSliderFunctions::check_file_in_zip($d_extr, $video_data["previewimage"], $sliderParams["alias"], $alreadyImported);
//                                        $video_data['previewimage'] = @getimagesize($video_data['previewimage']) ? $video_data['previewimage'] : JUri::root().$video_data['previewimage'];
                                    }
                                }
                            }

                            $layer['video_data'] = $video_data;
                        }

                        if(isset($layer['type']) && $layer['type'] == 'svg'){
                            if(isset($layer['svg']) && isset($layer['svg']->src)){
                                $layer['svg']->src = $content_url.$layer['svg']->src;
                            }
                        }

                        if(isset($layer['layer_action'])){
                            if(isset($layer['layer_action']->jump_to_slide) && !empty($layer['layer_action']->jump_to_slide)){
                                foreach($layer['layer_action']->jump_to_slide as $jtsk => $jtsval){
                                    if(isset($slider_map[$jtsval])){
                                        $layer['layer_action']->jump_to_slide[$jtsk] = $slider_map[$jtsval];
                                    }
                                }
                            }
                        }

                        $link_slide = ZtSliderFunctions::getVal($layer, 'link_slide', false);
                        if($link_slide != false && $link_slide !== 'nothing'){ //link to slide/scrollunder is set, move it to actions
                            if(!isset($layer['layer_action'])) $layer['layer_action'] = new stdClass();

                            switch($link_slide){
                                case 'link':
                                    $link = ZtSliderFunctions::getVal($layer, 'link');
                                    $link_open_in = ZtSliderFunctions::getVal($layer, 'link_open_in');
                                    $layer['layer_action']->action = array('a' => 'link');
                                    $layer['layer_action']->link_type = array('a' => 'a');
                                    $layer['layer_action']->image_link = array('a' => $link);
                                    $layer['layer_action']->link_open_in = array('a' => $link_open_in);

                                    unset($layer['link']);
                                    unset($layer['link_open_in']);
                                case 'next':
                                    $layer['layer_action']->action = array('a' => 'next');
                                    break;
                                case 'prev':
                                    $layer['layer_action']->action = array('a' => 'prev');
                                    break;
                                case 'scroll_under':
                                    $scrollunder_offset = ZtSliderFunctions::getVal($value, 'scrollunder_offset');
                                    $layer['layer_action']->action = array('a' => 'scroll_under');
                                    $layer['layer_action']->scrollunder_offset = array('a' => $scrollunder_offset);

                                    unset($layer['scrollunder_offset']);
                                    break;
                                default: //its an ID, so its a slide ID
                                    $layer['layer_action']->action = array('a' => 'jumpto');
                                    $layer['layer_action']->jump_to_slide = array('a' => $slider_map[$link_slide]);
                                    break;

                            }
                            $layer['layer_action']->tooltip_event = array('a' => 'click');

                            unset($layer['link_slide']);

                            $did_change = true;
                        }

                        $layers[$key] = $layer;
                    }

                    //create new slide
                    $arrCreate = array();
                    $arrCreate["slider_id"] = $sliderID;

                    $my_layers = json_encode($layers);
                    if(empty($my_layers))
                        $my_layers = stripslashes(json_encode($layers));
                    $my_params = json_encode($params);
                    if(empty($my_params))
                        $my_params = stripslashes(json_encode($params));
                    $my_settings = json_encode($settings);
                    if(empty($my_settings))
                        $my_settings = stripslashes(json_encode($settings));


                    $arrCreate["layers"] = $my_layers;
                    $arrCreate["params"] = $my_params;
                    $arrCreate["settings"] = $my_settings;

                    if($sliderExists){
                        unset($arrCreate["slider_id"]);
                        $this->db->setQuery('UPDATE #__zt_layerslider_static_slides SET layers = "'.$this->db->escape($arrCreate["layers"]).'", attribs = "'.$this->db->escape($arrCreate["params"]).'", settings = "'.$this->db->escape($arrCreate["settings"]).'" WHERE id='.$slide['id']);
                        $this->db->execute();
                    }else{
                        $this->db->setQuery('INSERT INTO #__zt_layerslider_static_slides VALUES (NULL ,'.$arrCreate["slider_id"].', "'.$this->db->escape($arrCreate["params"]).'", "'.$this->db->escape($arrCreate["layers"]).'", "'.$this->db->escape($arrCreate["settings"]).'")');
                        $this->db->execute();
                    }
                }
            }

            $c_slider = new ZTSliderSlider();
            $c_slider->initByID($sliderID);

            $cus_js = $c_slider->getParam('custom_javascript', '');

            if(strpos($cus_js, 'revapi') !== false){
                if(preg_match_all('/revapi[0-9]*/', $cus_js, $results)){

                    if(isset($results[0]) && !empty($results[0])){
                        foreach($results[0] as $replace){
                            $cus_js = str_replace($replace, 'revapi'.$sliderID, $cus_js);
                        }
                    }

                    $c_slider->updateParam(array('custom_javascript' => $cus_js));

                }

            }

            if($is_template !== false){ //duplicate the slider now, as we just imported the "template"
                if($single_slide !== false){ //add now one Slide to the current Slider
                    $mslider = new ZTSlider();

                    //change slide_id to correct, as it currently is just a number beginning from 0 as we did not have a correct slide ID yet.
                    $i = 0;
                    $changed = false;
                    foreach($slider_map as $value){
                        if($i == $single_slide['slide_id']){
                            $single_slide['slide_id'] = $value;
                            $changed = true;
                            break;
                        }
                        $i++;
                    }

                    if($changed){
                        $return = $mslider->copySlideToSlider($single_slide);
                    }else{
                        return(array("success"=>false,"error"=>JText::_('could not find correct Slide to copy, please try again.', 'revslider'),"sliderID"=>$sliderID));
                    }

                }else{
                    $mslider = new ZTSlider();
                    $title = ZtSliderFunctions::getVal($sliderParams, 'title', 'slider1');
                    $talias = $title;
                    $ti = 1;
                    while($this->isAliasExistsInDB($talias)){ //set a new alias and title if its existing in database
                        $talias = $title . $ti;
                        $ti++;
                    }
                    $mslider->duplicateSliderFromData(array('sliderid' => $sliderID, 'title' => $talias));
                }
            }

            @unlink($d_extr, true);


        }catch(Exception $e){
            $errorMessage = $e->getMessage();

            if(isset($d_extr)){
                @unlink($d_extr, true);
            }
            return(array("success"=>false,"error"=>$errorMessage,"sliderID"=>$sliderID));
        }

        return(array("success"=>true,"sliderID"=>$sliderID));
    }

    /**
     *
     * delete slider from input data
     */
    public function duplicateSliderFromData($data){
        $sliderID = ZTSliderFunctions::getVal($data, "sliderid");
        ZTSliderFunctions::validateNotEmpty($sliderID,"Slider ID");
        $this->initByID($sliderID);
        $this->duplicateSlider(ZTSliderFunctions::getVal($data, "title"));
    }

    public static function clear_error_in_string($m){
        return 's:'.strlen($m[2]).':"'.$m[2].'";';
    }
    /**
     *
     * duplicate slider in datatase
     */
    private function duplicateSlider($title = false, $prefix = false){

        $this->validateInited();

        //insert a new slider
        $this->db->setQuery('SELECT * FROM #__zt_layerslider_slider WHERE id='.intval($this->id));
        $_result = $this->db->loadObject();

        $this->db->setQuery("INSERT INTO #__zt_layerslider_slider VALUES (null,'".$_result->title."','".$_result->alias."',".$_result->state.",'".$this->db->escape($_result->attribs)."',".($_result->ordering + 1).",".$_result->access.",'".$_result->favourite."','".$_result->type."','".$this->db->escape($_result->settings)."','".JFactory::getApplication()->getUserStateFromRequest('com_zt_layerslider.filter.language', 'filter_language', '*')."')");
        $this->db->execute();
        $lastID = $this->db->insertid();
        ZTSliderFunctions::validateNotEmpty($lastID);


        $params = $this->arrParams;

        if($title === false){
            //get slider number:
            $this->db->setQuery('SELECT count(id) FROM #__zt_layerslider_slider');
            $numSliders = $this->db->loadResult();
            $newSliderSerial = $numSliders+1;

            $newSliderTitle = "Slider".$newSliderSerial;
            $newSliderAlias = "slider".$newSliderSerial;
        }else{
            if($prefix !== false){
                $newSliderTitle = trim($title.' '.$params['title']);
                $newSliderAlias = trim($title.' '.$params['title']);
            }else{
                $newSliderTitle = trim($title);
                $newSliderAlias = trim($title);
            }
            // Check Duplicate Alias
            $this->db->setQuery('SELECT id FROM #__zt_layerslider_slider WHERE alias ="'.$this->db->escape($newSliderAlias).'" ');
            $sqlTitle = $this->db->loadAssocList();
            if(!empty($sqlTitle)){
                $this->db->setQuery('SELECT count(id) FROM #__zt_layerslider_slider');
                $numSliders = $this->db->loadResult();
                $newSliderSerial = $numSliders+1;
                $newSliderTitle .= $newSliderSerial;
                $newSliderAlias .= $newSliderSerial;
            }
        }

        //update params

        $params["title"] = $newSliderTitle;
        $params["alias"] = $newSliderAlias;
        $params["shortcode"] = "[rev_slider alias=\"". $newSliderAlias ."\"]";

        //update the new slider with the title and the alias values
        $arrUpdate = array();
        $arrUpdate["title"] = $newSliderTitle;
        $arrUpdate["alias"] = $newSliderAlias;



        $jsonParams = json_encode($params);
        $arrUpdate["params"] = $jsonParams;

        $arrUpdate["type"] = '';//remove the type as we do not want it to be template if it was

        $this->db->setQuery('UPDATE #__zt_layerslider_slider SET attribs="'.$this->db->escape($arrUpdate["params"]).'", type="'.$arrUpdate["type"].'", title="'.$arrUpdate["title"] = $newSliderTitle.'", alias="'.$arrUpdate["alias"].'" WHERE id='.intval($lastID));
        $this->db->execute();

        //duplicate Slides
        $this->db->setQuery('SELECT * FROM #__zt_layerslider_slide WHERE slider_id='.intval($this->id));
        $slides = $this->db->loadAssocList();
        if(!empty($slides)){
            foreach($slides as $slide){
                $slide['slider_id'] = $lastID;
                $myID = $slide['id'];
                unset($slide['id']);
                $this->db->setQuery('INSERT INTO #__zt_layerslider_slide VALUES (NULL , '.$slide['slider_id'].', '.$slide['ordering'].', '.$slide['state'].',"'.$this->db->escape($slide['attribs']).'","'.$this->db->escape($slide['layers']).'",1,"'.JFactory::getApplication()->getUserStateFromRequest('com_zt_layerslider.filter.language', 'filter_language', '*').'","'.$this->db->escape($slide['attribs']).'") ');
                $this->db->execute();
                $last_id = $this->db->insertid();

                if(isset($myID)){
                    $slider_map[$myID] = $last_id;
                }
            }
        }

        //duplicate static slide if exists
        $slide = new ZTSlide();
        $staticID = $slide->getStaticSlideID($this->id);
        $static_id = 0;
        if($staticID !== false){
            $this->db->setQuery('SELECT * FROM #__zt_layerslider_static_slides WHERE id='.intval($staticID));
            $record = $this->db->loadAssoc();
            unset($record['id']);
            $record['slider_id'] = $lastID;

            $this->db->setQuery('INSERT INTO #__zt_layerslider_static_slides VALUES (NULL ,'.$lastID.',"'.$this->db->escape($record['attribs']).'","'.$this->db->escape($record['layers']).'","'.$this->db->escape($record['settings']).'")');
            $this->db->execute();
            $static_id = $this->db->insertid() ;
        }


        //update actions
        $this->db->setQuery('SELECT * FROM #__zt_layerslider_slide WHERE slider_id= '.intval($lastID));
        $slides = $this->db->loadAssocList();
        if($static_id > 0){
            $this->db->setQuery('SELECT * FROM #__zt_layerslider_static_slides WHERE id='.intval($static_id));
            $slides_static = $this->db->loadAssocList();
            $slides = array_merge($slides, $slides_static);
        }
        if(!empty($slides)){
            foreach($slides as $slide){
                $c_slide = new ZTSlide();
                $c_slide->initByData($slide);

                $layers = $c_slide->getLayers();
                $did_change = false;
                foreach($layers as $key => $value){
                    if(isset($value['layer_action'])){
                        if(isset($value['layer_action']->jump_to_slide) && !empty($value['layer_action']->jump_to_slide)){
                            foreach($value['layer_action']->jump_to_slide as $jtsk => $jtsval){
                                if(isset($slider_map[$jtsval])){

                                    $layers[$key]['layer_action']->jump_to_slide[$jtsk] = $slider_map[$jtsval];
                                    $did_change = true;
                                }
                            }
                        }
                    }
                }

                if($did_change === true){

                    $arrCreate = array();
                    $my_layers = json_encode($layers);
                    if(empty($my_layers))
                        $my_layers = stripslashes(json_encode($layers));

                    $arrCreate['layers'] = $my_layers;

                    if($slide['id'] == $static_id){
                        $this->db->setQuery('UPDATE #__zt_layerslider_static_slides SET layers="'.$this->db->escape($arrCreate['layers']).'" WHERE id='.(int)$static_id);
                        $this->db->execute();
                    }else{
                        $this->db->setQuery('UPDATE #__zt_layerslider_static_slides SET layers="'.$this->db->escape($arrCreate['layers']).'" WHERE id='.(int)$slide['id']);
                        $this->db->execute();
                    }

                }
            }
        }

        //change the javascript api ID to the correct one
        $c_slider = new ZTSliderSlider();
        $c_slider->initByID($lastID);

        $cus_js = $c_slider->getParam('custom_javascript', '');

        if(strpos($cus_js, 'revapi') !== false){
            if(preg_match_all('/revapi[0-9]*/', $cus_js, $results)){

                if(isset($results[0]) && !empty($results[0])){
                    foreach($results[0] as $replace){
                        $cus_js = str_replace($replace, 'revapi'.$lastID, $cus_js);
                    }
                }

                $c_slider->updateParam(array('custom_javascript' => $cus_js));

            }
        }
    }

    /**
     *
     * update some params in the slider
     */
    public function updateParam($arrUpdate){
        $this->validateInited();

        $this->arrParams = array_merge($this->arrParams,$arrUpdate);
        $jsonParams = json_encode($this->arrParams);
        $arrUpdateDB = array();
        $arrUpdateDB["params"] = $jsonParams;
        $this->db->setQuery('UPDATE #__zt_layerslider_slider SET attribs="'.$this->db->escape($arrUpdateDB["params"]).'" WHERE id='.intval($this->id));
        $this->db->execute();
    }


    /**
     *
     * get slider params for export slider
     */
    private function getParamsForExport(){
        $exportParams = $this->arrParams;

        //modify background image
        $urlImage = ZTSliderFunctions::getVal($exportParams, "background_image");
        if(!empty($urlImage))
            $exportParams["background_image"] = $urlImage;

        return($exportParams);
    }

    /**
     * get slides number
     */
    public function getNumSlides($publishedOnly = false){

        if($this->arrSlides == null)
            $this->getSlides($publishedOnly);

        $numSlides = count($this->arrSlides);
        return($numSlides);
    }

    /*
     *get all templates
     */
    public static function getThemePunchTemplateSliders(){

    }

    /*
     * get defaut template
     */
    public static function getDefaultTemplateSliders() {}

    /**
     * get array of alias
     */
    public function getAllSliderAliases(){
        $where = "`type` != 'template'";

        $this->db->setQuery("SELECT * FROM #__zt_layerslider_slider WHERE ".$where." ORDER BY ID DESC");

        $response = $this->db->loadAssocList();

        $arrAliases = array();
        foreach($response as $arrSlider){
            $arrAliases[] = $arrSlider["alias"];
        }

        return($arrAliases);
    }

    /**
     *
     * init slider by alias
     */
    public function initByAlias($alias){

        try{
            $this->db->setQuery("SELECT * FROM #__zt_layerslider_slider WHERE alias = '".$alias."'  AND `type` != 'template'");
            $sliderData = $this->db->loadAssoc();
            if (is_object($sliderData)) $sliderData = get_object_vars($sliderData);

        }catch(Exception $e){
            $arrAliases = $this->getAllSliderAliases();
            $strAliases = "";

            if(!empty($arrAliases) && is_array($arrAliases)){
                $arrAliases = array_slice($arrAliases, 0, 6); //show 6 other, will be enough

                $strAliases = "'".trim(implode("' or '", $arrAliases))."'";
            }

            $errorMessage = 'Slider with alias <strong>'.trim($alias).'</strong> not found.';
            if(!empty($strAliases))
                $errorMessage .= ' <br>Maybe you mean: '.$strAliases;

            ZTSliderFunctions::throwError($errorMessage);
        }

        $this->initByDBData($sliderData);
    }


    /**
     *
     * init by id or alias
     */
    public function initByMixed($mixed){
        if(is_numeric($mixed))
            $this->initByID($mixed);
        else
            $this->initByAlias($mixed);
    }

    /**
     *
     * get the "main" and "settings" arrays, for dealing with the settings.
     */
    public function getSettingsFields(){
        $this->validateInited();

        $arrMain = array();
        $arrMain["title"] = $this->title;
        $arrMain["alias"] = $this->alias;

        $arrRespose = array("main"=>$arrMain, "params"=>$this->attribs);

        return($arrRespose);
    }

    /**
     * create a slide from input data
     */
    public function createSlideFromData($data,$returnSlideID = false){

        $sliderID = ZTSliderFunctions::getVal($data, "sliderid");
        $obj = ZTSliderFunctions::getVal($data, "obj");

        ZTSliderFunctions::validateNotEmpty($sliderID,"Slider ID");
        $this->initByID($sliderID);

        if(is_array($obj)){	//multiple
            foreach($obj as $item){
                $slide = new ZTSlide();
                $slideID = $slide->createSlide($sliderID, $item);
            }

            return(count($obj));

        }else{	//signle
            $urlImage = $obj;
            $slide = new ZTSlide();
            $slideID = $slide->createSlide($sliderID, $urlImage);
            if($returnSlideID == true)
                return($slideID);
            else
                return(1);	//num slides -1 slide created
        }
    }

    /**
     *
     * get all slide children
     */
    public function getArrSlideChildren($slideID){

        $this->validateInited();
        $arrSlides = $this->getSlidesFromGallery();
        if(!isset($arrSlides[$slideID]))
            ZTSliderFunctions::throwError("Slide with id: $slideID not found in the main slides of the slider. Maybe it's child slide.");

        $slide = $arrSlides[$slideID];
        $arrChildren = $slide->getArrChildren();

        return($arrChildren);
    }



    /**
     *
     * get max order
     */
    public function getMaxOrder(){
        $this->validateInited();
        $maxOrder = 0;
        $this->db->setQuery('SELECT * FROM #__zt_layerslider_slide WHERE slider_id='.$this->id.' ORDER BY ordering DESC LIMIT 1');
        $arrSlideRecords = $this->db->loadResult();
        if(empty($arrSlideRecords))
            return($maxOrder);
        $maxOrder = $arrSlideRecords;

        return($maxOrder);
    }

    /**
     *
     * delete all slides
     */
    private function deleteAllSlides(){
        $this->validateInited();
        $this->db->setQuery('DELETE FROM #__zt_layerslider_slide WHERE slider_id='.(int)$this->id);
        $this->db->execute();
    }

    /**
     * update slides order from data
     */
    public function updateSlidesOrderFromData($data){
        $sliderID = ZTSliderFunctions::getVal($data, "sliderID");
        $arrIDs = ZTSliderFunctions::getVal($data, "arrIDs");
        ZTSliderFunctions::validateNotEmpty($arrIDs,"slides");

        $this->initByID($sliderID);

        $isFromPosts = $this->isSlidesFromPosts();

        foreach($arrIDs as $index=>$slideID){

            $order = $index+1;

            if($isFromPosts){
                RevSliderFunctionsWP::updatePostOrder($slideID, $order);
            }else{

                $arrUpdate = array("slide_order"=>$order);
                $where = array("id"=>$slideID);
                $this->db->update(RevSliderGlobals::$table_slides,$arrUpdate,$where);
            }
        }//end foreach

        //update sortby
        if($isFromPosts){
            $arrUpdate = array();
            $arrUpdate["post_sortby"] = RevSliderFunctionsWP::SORTBY_MENU_ORDER;
            $this->updateParam($arrUpdate);
        }

    }

    /**
     * get sliders array - function don't belong to the object!
     */
    public function getArrSliders($orders = false, $templates = 'neither'){
        $order_fav = false;
        if($orders !== false && key($orders) != 'favorite'){
            $order_direction = reset($orders);
            $do_order = key($orders);
        }else{
            $do_order = 'id';
            $order_direction = 'ASC';
            if(is_array($orders) && key($orders) == 'favorite'){
                $order_direction = reset($orders);
                $order_fav = true;
            }
        }
        //$where = "`type` != 'template' ";
        $where = "`type` != 'template' OR `type` IS NULL";
        $this->db->setQuery('SELECT * FROM #__zt_layerslider_slider WHERE '.$where.' ORDER BY '.$do_order.' '.$order_direction);

        $response = $this->db->loadAssocList();

        $arrSliders = array();
        foreach($response as $arrData){
            $slider = new ZTSlider();
            if (is_object($arrData)) $arrData = get_object_vars($arrData);
            $slider->initByDBData($arrData);

            /*
            This part needs to stay for backwards compatibility. It is used in the update process from v4x to v5x
            */
            if($templates === true){
                if($slider->getParam("template","false") == "false") continue;
            }elseif($templates === false){
                if($slider->getParam("template","false") == "true") continue;
            }

            $arrSliders[] = $slider;
        }

        if($order_fav === true){
            $temp = array();
            $temp_not = array();
            foreach($arrSliders as $key => $slider){
                if($slider->isFavorite()){
                    $temp_not[] = $slider;
                }else{
                    $temp[] = $slider;
                }
            }
            $arrSliders = array();
            $arrSliders = ($order_direction == 'ASC') ? array_merge($temp, $temp_not) : array_merge($temp_not, $temp);
        }

        return($arrSliders);
    }

    /**
     * init slider by db data
     */
    public function initByDBData($arrData){
        $this->id = $arrData["id"];
        $this->title = $arrData["title"];
        $this->alias = $arrData["alias"];

        $this->language = $arrData['language'];
        $this->state = $arrData['state'];
        $this->type = $arrData['type'];
        $this->ordering = $arrData['ordering'];
        $this->access = $arrData['access'];

        $this->favourite = $arrData['favourite'];

        $settings = $arrData["settings"];
        $settings = (array)json_decode($settings);

        $this->settings = $settings;

        $params = $arrData["attribs"];
        if (!is_array($params)) $params = json_decode($params,true);
        $params = self::translate_settings_to_v5($params);
        $this->arrParams = $params;
        $this->attribs = $params;
    }

    public static function translate_settings_to_v5($settings){

        if(isset($settings['navigaion_type'])){
            switch($settings['navigaion_type']){
                case 'none': // all is off, so leave the defaults
                    break;
                case 'bullet':
                    $settings['enable_bullets'] = 'on';
                    $settings['enable_thumbnails'] = 'off';
                    $settings['enable_tabs'] = 'off';

                    break;
                case 'thumb':
                    $settings['enable_bullets'] = 'off';
                    $settings['enable_thumbnails'] = 'on';
                    $settings['enable_tabs'] = 'off';
                    break;
            }
            unset($settings['navigaion_type']);
        }

        if(isset($settings['navigation_arrows'])){
            $settings['enable_arrows'] = ($settings['navigation_arrows'] == 'solo' || $settings['navigation_arrows'] == 'nexttobullets') ? 'on' : 'off';
            unset($settings['navigation_arrows']);
        }

        if(isset($settings['navigation_style'])){
            $settings['navigation_arrow_style'] = $settings['navigation_style'];
            $settings['navigation_bullets_style'] = $settings['navigation_style'];
            unset($settings['navigation_style']);
        }

        if(isset($settings['navigaion_always_on'])){
            $settings['arrows_always_on'] = $settings['navigaion_always_on'];
            $settings['bullets_always_on'] = $settings['navigaion_always_on'];
            $settings['thumbs_always_on'] = $settings['navigaion_always_on'];
            unset($settings['navigaion_always_on']);
        }

        if(isset($settings['hide_thumbs']) && !isset($settings['hide_arrows']) && !isset($settings['hide_bullets'])){ //as hide_thumbs is still existing, we need to check if the other two were already set and only translate this if they are not set yet
            $settings['hide_arrows'] = $settings['hide_thumbs'];
            $settings['hide_bullets'] = $settings['hide_thumbs'];
        }

        if(isset($settings['navigaion_align_vert'])){
            $settings['bullets_align_vert'] = $settings['navigaion_align_vert'];
            $settings['thumbnails_align_vert'] = $settings['navigaion_align_vert'];
            unset($settings['navigaion_align_vert']);
        }

        if(isset($settings['navigaion_align_hor'])){
            $settings['bullets_align_hor'] = $settings['navigaion_align_hor'];
            $settings['thumbnails_align_hor'] = $settings['navigaion_align_hor'];
            unset($settings['navigaion_align_hor']);
        }

        if(isset($settings['navigaion_offset_hor'])){
            $settings['bullets_offset_hor'] = $settings['navigaion_offset_hor'];
            $settings['thumbnails_offset_hor'] = $settings['navigaion_offset_hor'];
            unset($settings['navigaion_offset_hor']);
        }

        if(isset($settings['navigaion_offset_hor'])){
            $settings['bullets_offset_hor'] = $settings['navigaion_offset_hor'];
            $settings['thumbnails_offset_hor'] = $settings['navigaion_offset_hor'];
            unset($settings['navigaion_offset_hor']);
        }

        if(isset($settings['navigaion_offset_vert'])){
            $settings['bullets_offset_vert'] = $settings['navigaion_offset_vert'];
            $settings['thumbnails_offset_vert'] = $settings['navigaion_offset_vert'];
            unset($settings['navigaion_offset_vert']);
        }

        if(isset($settings['show_timerbar']) && !isset($settings['enable_progressbar'])){
            if($settings['show_timerbar'] == 'hide'){
                $settings['enable_progressbar'] = 'off';
                $settings['show_timerbar'] = 'top';
            }else{
                $settings['enable_progressbar'] = 'on';
            }
        }

        return $settings;
    }

    /**
     * copy slide from one Slider to the given Slider ID
     * @since: 5.0
     */
    public function copySlideToSlider($data){

        $sliderID = intval(ZTSliderFunctions::getVal($data, "slider_id"));
        ZTSliderFunctions::validateNotEmpty($sliderID,"Slider ID");
        $slideID = intval(ZTSliderFunctions::getVal($data, "slide_id"));
        ZTSliderFunctions::validateNotEmpty($slideID,"Slide ID");

        $tableSliders = '#__zt_layerslider_slider';
        $tableSlides = '#__zt_layerslider_slide';

        //check if ID exists
        $this->db->setQuery("SELECT * FROM $tableSliders WHERE id=".intval($sliderID));
        $add_to_slider = $this->db->loadAssoc();

        if(empty($add_to_slider))
            return JText::_('Slide could not be duplicated');

        //get last slide in slider for the order
        $this->db->setQuery("SELECT * FROM $tableSlides WHERE slider_id = ".intval($sliderID)." ORDER BY ordering DESC");
        $slide_order = $this->db->loadAssoc();
        $order = (empty($slide_order)) ? 1 : $slide_order['ordering'] + 1;

        $this->db->setQuery("SELECT * FROM $tableSlides WHERE id = ".intval($slideID));
        $slide_to_copy = $this->db->loadAssoc();

        if(empty($slide_to_copy))
            return JText::_('Slide could not be duplicated');

        unset($slide_to_copy['id']); //remove the ID of Slide, as it will be a new Slide
        $slide_to_copy['slider_id'] = $sliderID; //set the new Slider ID to the Slide
        $slide_to_copy['ordering'] = $order; //set the next slide order, to set slide to the end

        $this->db->setQuery("INSERT INTO $tableSlides VALUES (NULL ,".$slide_to_copy['slider_id'].",".$slide_to_copy['ordering'].",".$slide_to_copy['state'].",'".$this->db->escape($slide_to_copy['attribs'])."','".$this->db->escape($slide_to_copy['layers'])."',".$slide_to_copy['access'].",'".$this->db->escape($slide_to_copy['language'])."','".$this->db->escape($slide_to_copy['settings'])."')");

        $response = $this->db->execute();

        if($response === false) return JText::_('Slide could not be copied');

        return true;
    }



    /**
     *
     * get array of slider id -> title
     */
    public function getArrSlidersShort($exceptID = null,$filterType = self::SLIDER_TYPE_ALL){
        $arrSliders = $this->getArrSliders();
        $arrShort = array();
        foreach($arrSliders as $slider){
            $id = $slider->getID();
            $isFromPosts = $slider->isSlidesFromPosts();
            $isTemplate = $slider->getParam("template","false");

            //filter by gallery only
            if($filterType == self::SLIDER_TYPE_POSTS && $isFromPosts == false)
                continue;

            if($filterType == self::SLIDER_TYPE_GALLERY && $isFromPosts == true)
                continue;

            //filter by template type
            if($filterType == self::SLIDER_TYPE_TEMPLATE && $isFromPosts == false)
                continue;

            //filter by except
            if(!empty($exceptID) && $exceptID == $id)
                continue;

            $title = $slider->getTitle();
            $arrShort[$id] = $title;
        }
        return($arrShort);
    }

    /**
     *
     * get array of sliders with slides, short, assoc.
     */
    public function getArrSlidersWithSlidesShort($filterType = self::SLIDER_TYPE_ALL){
        $arrSliders = self::getArrSlidersShort(null, $filterType);

        $output = array();
        foreach($arrSliders as $sliderID=>$sliderName){
            $slider = new ZTSlider();
            $slider->initByID($sliderID);

            $isFromPosts = $slider->isSlidesFromPosts();
            $isTemplate = $slider->getParam("template","false");

            //filter by gallery only
            if($filterType == self::SLIDER_TYPE_POSTS && $isFromPosts == false)
                continue;

            if($filterType == self::SLIDER_TYPE_GALLERY && $isFromPosts == true)
                continue;

            //filter by template type
            if($filterType == self::SLIDER_TYPE_TEMPLATE && $isFromPosts == false) //$isTemplate == "false")
                continue;

            $sliderTitle = $slider->getTitle();
            $arrSlides = $slider->getArrSlidesFromGalleryShort();

            foreach($arrSlides as $slideID=>$slideName){
                $output[$slideID] = $sliderName.", ".$slideName;
            }
        }

        return($output);
    }

    /**
     *
     * get array of slide names
     */
    public function getArrSlideNames(){
        if(empty($this->arrSlides))
            $this->getSlidesFromGallery();

        $arrSlideNames = array();

        foreach($this->arrSlides as $number=>$slide){
            $slideID = $slide->getID();
            $filename = $slide->getImageFilename();
            $slideTitle = $slide->getParam("title","Slide");
            $slideName = $slideTitle;
            if(!empty($filename))
                $slideName .= " ($filename)";

            $arrChildrenIDs = $slide->getArrChildrenIDs();

            $arrSlideNames[$slideID] = array("name"=>$slideName,"arrChildrenIDs"=>$arrChildrenIDs,"title"=>$slideTitle);
        }
        return($arrSlideNames);
    }

    /**
     *
     * return if the slider is inited or not
     */
    public function isInited(){
        if(!empty($this->id))
            return(true);

        return(false);
    }


    /**
     * add default icon sets of Slider Revolution
     * @since: 5.0
     **/
    public static function set_icon_sets($icon_sets){

        $icon_sets[] = 'fa-icon-';
        $icon_sets[] = 'pe-7s-';

        return $icon_sets;
    }


}
class ZTSlider extends ZtSliderSlider {}