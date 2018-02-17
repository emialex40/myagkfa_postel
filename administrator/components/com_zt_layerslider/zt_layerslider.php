<?php
/**
 * @package ZT LayerSlider
 * @author    ZooTemplate.com
 * @copyright(C) 2015 - ZooTemplate.com
 * @license PHP files are GNU/GPL
 **/
defined('_JEXEC') or die;
define('ZT_ASSETS_PATH',JPATH_ROOT.'/administrator/components/com_zt_layerslider');
define('ZT_ASSETS_URL',JUri::root().'administrator/components/com_zt_layerslider');

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
//define( 'RS_DEMO', false );

$document = JFactory::getDocument();
// CSS
jimport( 'joomla.application.component.view' );
$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/css/color-picker.min.css');
$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/css/zt_layerslider.css');
$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/css/admin.css');
$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/css/tipsy.css');
$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/js/codemirror/codemirror.css');
$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/css/edit_layers.css');
$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/css/settings.css');
$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/fonts/font-awesome/css/font-awesome.css');
$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css');
$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/css/global.css');
$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/css/wp.css');

$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/css/thickbox.css');
$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/css/jquery-ui.css');
$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/css/common.min.css');
$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/css/edit.min.css');
$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/css/jquery.fancybox.css');

//check if defer js is enabled
$zt_params = JComponentHelper::getParams('com_zt_layerslider');
$js_defer = $zt_params->get('js_defer') ? 'defer="defer"' : '';

//JText::_('COM_ZT_LAYERSLIDER_FEATURE_REQUIRES_INLINE_FRAMES')."';__('This feature requires inline frames. You have iframes disabled or your browser does not support them.'),
//$document->addScriptDeclaration( "var thickboxL10n = {'next' : '".JText::_("COM_ZT_LAYERSLIDER_NEXT")."&gt;','prev' :'&lt;".JText::_("COM_ZT_LAYERSLIDER_PREVIOUS")."','image' : '".JText::_('COM_ZT_LAYERSLIDER_IMAGE')."','of' :  '".JText::_('COM_ZT_LAYERSLIDER_OF')."','close': '".JText::_('COM_ZT_LAYERSLIDER_CLOSE')."','noiframes' : '".JText::_('COM_ZT_LAYERSLIDER_FEATURE_REQUIRES_INLINE_FRAMES')."','loadingAnimation' : '".JUri::root().'administrator/components/com_zt_layerslider/assets/images/loadingAnimation.gif'."'};");

//js added if does not exist
if(!JFactory::getApplication()->get('jquery')){
    JHtml::_('jquery.framework');
}


$document->addScript(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/js/ui/jquery-ui.js','text/javascript',$js_defer);
$document->addScript(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/js/rev_lang.js','text/javascript',$js_defer);
$document->addScript(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/js/underscore.min.js','text/javascript',$js_defer);
$document->addScript(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/js/iris.min.js','text/javascript',$js_defer);
$document->addScript(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/js/color-picker.js','text/javascript',$js_defer);
//$document->addScript(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/js/thickbox.js','text/javascript',$js_defer);
$document->addScript(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/js/settings.js','text/javascript',$js_defer);
$document->addScript(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/js/admin.js','text/javascript',$js_defer);
$document->addScript(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/js/jquery.tipsy.js','text/javascript',$js_defer);
$document->addScript(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/js/codemirror/codemirror.js','text/javascript',$js_defer);
$document->addScript(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/js/codemirror/util/match-highlighter.js','text/javascript',$js_defer);
$document->addScript(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/js/codemirror/util/searchcursor.js','text/javascript',$js_defer);
$document->addScript(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/js/edit_layers_timeline.js','text/javascript',$js_defer);
$document->addScript(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/js/edit_layers.js','text/javascript',$js_defer);
$document->addScript(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/js/css_editor.js','text/javascript',$js_defer);
//$document->addScript(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/js/alpha-color-picker.js','text/javascript',$js_defer);
$document->addScript(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/js/rev_admin.js','text/javascript',$js_defer);
$document->addScript(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/js/jquery.themepunch.tools.min.js','text/javascript',$js_defer);
$document->addScript(JUri::root(true) . '/administrator/components/com_zt_layerslider/assets/js/jquery.fancybox.pack.js','text/javascript',$js_defer);
//

// Register helper class
require_once dirname(__FILE__) . '/helpers/classes/zt.slider.class.php';
require_once dirname(__FILE__) . '/helpers/classes/zt.navigation.class.php';
require_once dirname(__FILE__) . '/helpers/classes/zt.operations.class.php';
require_once dirname(__FILE__) . '/helpers/classes/zt.slider.functions.php';

require_once dirname(__FILE__) . '/helpers/classes/zt.cssparser.class.php';
require_once dirname(__FILE__) . '/helpers/classes/zt.slide.class.php';
require_once dirname(__FILE__) . '/helpers/classes/zt.template.class.php';
require_once dirname(__FILE__) . '/helpers/classes/zt.output.class.php';
require_once dirname(__FILE__) . '/helpers/classes/zt.external.sources.class.php';

$controller = JControllerLegacy::getInstance('ZT_Layerslider');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
