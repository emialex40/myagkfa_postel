<?php
defined('_JEXEC') or die;

//get taxonomies with cats
$postTypesWithCats  = new ZTSliderOperations();
$this->postTypesWithCats = $postTypesWithCats->getCatsForClient();
$this->jsonTaxWithCats = ZTSliderFunctions::jsonEncodeForClientSide($this->postTypesWithCats);

//check existing slider data:
$sliderID = $this->item->id;

$this->arrFieldsParams = array();
$slider = new ZtSliderSlider($this->item->id);
$this->slider = $slider;
if(!empty($sliderID)){

    $slider->initByID($sliderID);

    //get setting fields
    $settingsFields = $slider->getSettingsFields();
    $arrFieldsMain = $settingsFields['main'];
    $this->arrFieldsParams = $settingsFields['params'];

    $linksEditSlides = JRoute::_('index.php?option=com_zt_layerslider&view=slides&slider_id='.$this->item->id,false);

    echo $this->loadTemplate('slider');
}else{
    echo $this->loadTemplate('create_slider');
}

?>