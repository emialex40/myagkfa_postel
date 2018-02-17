<?php
/**
 * @package ZT LayerSlider
 * @author    ZooTemplate.com
 * @copyright(C) 2015 - ZooTemplate.com
 * @license PHP files are GNU/GPL
 **/
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class ZT_LayersliderControllerSlider extends JControllerForm
{
	public function __construct(array $config)
	{
		$this->ajx = new ZTSliderFunctions();
		parent::__construct($config);
	}

	protected function allowEdit($data = array(), $key = 'id')
	{


		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;
		$user = JFactory::getUser();

		// Check general edit permission first.
		if ($user->authorise('core.edit', 'com_zt_layerslider.slider.' . $recordId))
		{
			return true;
		}


		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit($data, $key);
	}

	public function updateToggleFavourite($id)
	{
		$model = $this->getModel();
		$slider = $model->getItem($id);
		if(!$slider) return JText::_('COM_ZT_LAYERSLIDER_SLIDER_NOT_FOUND');

		if ($slider->favourite == 0) {$favourite=1;}
		else $favourite = 0;

		//
		$db = JFactory::getDbo();
		$db->setQuery("UPDATE #__zt_layerslider_slider SET favourite = ".$favourite." WHERE id=".$id);

		if(!$db->execute()) return JText::_('COM_ZT_LAYERSLIDER_COULD_NOT_BE_CHANGED');

		return true;

	}

	public function toggleFavourite() {
		$data = $this->input->get('data',array());
		if(isset($data[0]) && intval($data[0]) > 0){
			$return = $this->updateToggleFavourite(intval($data[0]));
			if($return === true){
				ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_SETTING_CHANGED!'));
			}else{
				$error = $return;
			}
		}else{
			$error = JText::_('COM_ZT_LAYERSLIDER_ID_NO_GIVEN');
		}
		ZTSliderFunctions::ajaxResponseError($error);
	}

	public function deleteSlider(){
		$data = $this->input->get('data',array());
		if(isset($data[0]) && intval($data[0]) > 0){
			$id = (int)$data[0];

			//delete  slider
			$query = "DELETE FROM #__zt_layerslider_slider WHERE id=".$id;
			JFactory::getDbo()->setQuery($query);
			JFactory::getDbo()->execute();

			//delete slides
			$query = "DELETE FROM #__zt_layerslider_slide WHERE slider_id=".$id;
			JFactory::getDbo()->setQuery($query);
			$return = JFactory::getDbo()->execute();

			if($return === true){
				ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_SLIDER_DELETED'));
			}else{
				$error = ZTSliderFunctions::ajaxResponseError(JText::_('COM_ZT_LAYERSLIDER_FAILURE'));
			}
		}else {
			$error = JText::_('COM_ZT_LAYERSLIDER_ID_NO_GIVEN');
		}
		ZTSliderFunctions::ajaxResponseError($error);
	}

	/**
	 *
	 * create / update slider from options
	 */
	private function createUpdateSliderFromOptions($options, $sliderID = null){

		$arrMain = $options['main'];
		$params = $options["params"];

		//trim all input data
		$arrMain = ZTSliderFunctions::trimArrayItems($arrMain);

		$params = ZTSliderFunctions::trimArrayItems($params);

		$params = array_merge($arrMain,$params);

		$title = ZTSliderFunctions::getVal($arrMain, "title");
		$alias = ZTSliderFunctions::getVal($arrMain, "alias");

		//params css and js check
		if(!JFactory::getUser()->authorise('core.admin')){
			//dont allow css and javascript from users other than administrator
			unset($params['custom_css']);
			unset($params['custom_javascript']);
		}

		$slider_h = new ZtSliderSlider($sliderID);
		if(!empty($sliderID)){
			if(!JFactory::getUser()->authorise('core.admin')){
				//check for js and css, add it to $params
				$params['custom_css'] = $slider_h->getParam('custom_css', '');
				$params['custom_javascript'] = $slider_h->getParam('custom_javascript', '');
			}

		}


		$slider_h->validateInputSettings($title, $alias, $params);

		$jsonParams = json_encode($params);

		//insert slider to database
		$arrData = array();
		$arrData["title"] = $title;
		$arrData["alias"] = $alias;
		$arrData["attribs"] = $jsonParams;
		$arrData["state"] = 1;
		$arrData["type"] = '';

		if(empty($sliderID)){	//create slider
			$arrData['settings'] = json_encode(array('version' => 2.0));
		}else{	//update slider
			$settings = $slider_h->getSettings();
			$settings['version'] = 2.0;
			$arrData['id'] = $sliderID;
			$arrData['settings'] = json_encode($settings);
		}

		$this->getModel()->save($arrData);
		$sliderID = JFactory::getDbo()->insertid();

		return($sliderID);
	}

	public function prepareProcess() {
		$data = $this->input->get('data',array(),'ARRAY');
		if(!JFactory::getUser()->authorise('core.create')) {ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_NOT_AUTHORIZE_EDIT'));}
		$this->ajx->onAjaxAction($data);
	}

	public function updateSlider() {
		$data = $this->input->get('data',array(),'ARRAY');
		if(!JFactory::getUser()->authorise('core.edit')) {ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_NOT_AUTHORIZE_EDIT'));}
		$data = $this->ajx->onAjaxAction($data);
		if($data){
			//do update
			try{
				$this->createUpdateSliderFromOptions($data,$data['sliderid']);
				ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_SLIDER_UPDATED'));//__("Slider updated",'revslider')
			}
			catch (Exception $e) {
				$message = $e->getMessage();
				ZTSliderFunctions::ajaxResponseError($message);
			}
		}
	}

	public function createSlider() {
		$data = $this->input->get('data',array(),'ARRAY');
		if(!JFactory::getUser()->authorise('core.creat')) {ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_NOT_AUTHORIZE_CREATE'));}
		$data = $this->ajx->onAjaxAction($data);
		if($data) {
			try{
				$newSliderID = $this->createUpdateSliderFromOptions($data);
				ZTSliderFunctions::ajaxResponseSuccessRedirect(JText::_('COM_ZT_LAYERSLIDER_SLIDER_CREATED'), 'index.php?option=com_zt_layerslider&id=new&view=slides&slider_id=' . $newSliderID);// __("Slider created",'revslider')//ZTSliderFunctions::ajaxResponseSuccessRedirect(__("Slider created",'revslider'), self::getViewUrl(self::VIEW_SLIDE, 'id=new&slider='.esc_attr($newSliderID))); //redirect to slide now
			}
			catch(Exception $e){
				$message = $e->getMessage();
				ZTSliderFunctions::ajaxResponseError($message);
			}

		}
	}


	public function createNavigationPreset() {
		$this->prepareProcess();
	}

	public function deleteNavigationPreset() {
		$this->prepareProcess();
	}

	public function getstaticcss() {
		$this->prepareProcess();
	}

	public function getDynamicCss() {
		$this->prepareProcess();
	}

	public function addNewPreset() {
		$this->prepareProcess();
	}

	public function removePreset() {
		$this->prepareProcess();
	}

	public function updatepreset() {
		$this->prepareProcess();
	}

	public function updateStaticCss() {
		$this->prepareProcess();
	}

	public function updateSlide() {
		$this->prepareProcess();
	}

	public function changeSlideTitle() {
		$this->prepareProcess();
	}

	public function duplicateSlideStay() {
		$this->prepareProcess();
	}
	public function deleteSlide() {
		$this->prepareProcess();
	}
	public function deleteSlideStay() {
		$this->prepareProcess();
	}

	public function copymoveSlideStay() {
		$this->prepareProcess();
	}
	public function addSlidetoTemplate() {
		$this->prepareProcess();
	}

	public function addSlideFromSlideView() {
		$this->prepareProcess();
	}
	public function copySlideToSlider() {
		$this->prepareProcess();
	}
	public function addBulkSlide() {
		$this->prepareProcess();
	}

	public function previewslide() {
		$this->prepareProcess();
	}

	public function getImportSlidesData() {
		$this->prepareProcess();
	}

	public function previewSlider() {
		$this->prepareProcess();
	}

	public function importSlider() {
		$this->prepareProcess();
	}

	public function duplicateSlider() {
		$this->prepareProcess();
	}

	public function importSliderSlidersView() {
		$this->prepareProcess();
	}

	public function toggleSlideState() {
		$this->prepareProcess();
	}

	public function exportSlider() {
		if(!JFactory::getUser()->authorise('core.create')) {ZTSliderFunctions::ajaxResponseSuccess(JText::_('COM_ZT_LAYERSLIDER_NOT_AUTHORIZE_EDIT'));}
		$this->ajx->onAjaxAction();
	}


}