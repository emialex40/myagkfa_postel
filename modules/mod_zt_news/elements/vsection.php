<?php
/**
 * ZT News
 * 
 * @package     Joomla
 * @subpackage  Module
 * @version     2.0.0
 * @author      ZooTemplate 
 * @email       support@zootemplate.com 
 * @link        http://www.zootemplate.com 
 * @copyright   Copyright (c) 2015 ZooTemplate
 * @license     GPL v2
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html');
jimport('joomla.access.access');
jimport('joomla.form.formfield');

/**
 * Class exists checking
 */
class JFormFieldVsection extends JFormField
{

    /**
     * Element name
     *
     * @access	protected
     * @var		string
     */
    protected $type = 'vsection';

    protected function getInput()
    {
        $db = JFactory::getDBO();
        $document = JFactory::getDocument();
        $document->addStylesheet(JURI::root() . 'modules/mod_zt_news/admin/css/adminstyle.css');
        $cId = JRequest::getVar('id', 0);
        $sql = "SELECT params FROM #__modules WHERE id=$cId";
        $db->setQuery($sql);
        $data = $db->loadResult();
        $params = new JRegistry($data);
        $source = $params->get('source', 'content');
        $layout = $params->get('template_type');
        ?>
        <!-- For these js must be stored in js file instead -->
        <script type="text/javascript">

            jQuery(document).ready(function () {
//                sourceChange('<?php //echo $source; ?>//');
                layoutChange('<?php echo $layout; ?>');
//                jQuery('#jform_params_source').change(function () {
//                    sourceChange(jQuery(this).val());
//                });
                jQuery('#jform_params_template_type').change(function () {
                    layoutChange(jQuery(this).val());
                });

            });

            var jpaneAutoHeight = function () {
                $$('.jpane-slider').each(function (item) {
                    item.setStyle('height', 'auto');
                });
            };

//            function sourceChange(val) {
//
//                if (val == 'content') {
//                    jQuery('#jform_params_k2_cids').parents('.control-group').hide();
//                    jQuery('#jform_params_content_cids').parents('.control-group').show();
//                    jQuery('#jform_params_type_image').parents('.control-group').hide();
//                    jQuery('#jform_params_orderingk2').parents('.control-group').hide();
//                    jQuery('#jform_params_rderingcontent').parents('.control-group').show();
//                }
//                else {
//                    jQuery('#jform_params_k2_cids').parents('.control-group').show();
//                    jQuery('#jform_params_content_cids').parents('.control-group').hide();
//                    jQuery('#jform_params_type_image').parents('.control-group').show();
//                    jQuery('#jform_params_orderingk2').parents('.control-group').show();
//                    jQuery('#jform_params_orderingcontent').parents('.control-group').hide();
//                }
//
//            }
            function layoutChange(val) {
                if (val == 'horizontal') {

                    jQuery('#jform_params_breakpoint').parents('.control-group').show();
                    jQuery('#jform_params_showtitlecat').parents('.control-group').hide();
                    jQuery('#jform_params_is_subcat').parents('.control-group').hide();
                    jQuery('#jform_params_is_all').parents('.control-group').hide();
                    jQuery('#jform_params_number_link_items').parents('.control-group').hide();
                    jQuery('#jform_params_columns').parents('.control-group').hide();
                    jQuery('#jform_params_thumb_list_width').parents('.control-group').hide();
                    jQuery('#jform_params_thumb_list_height').parents('.control-group').hide();
                    jQuery('#jform_params_show_title_list').parents('.control-group').hide();
                    jQuery('#jform_params_is_image_list').parents('.control-group').hide();
                    jQuery('#jform_params_show_intro_list').parents('.control-group').hide();
                    jQuery('#jform_params_show_date_list').parents('.control-group').hide();
                } else {

                    jQuery('#jform_params_breakpoint').parents('.control-group').hide();
                    jQuery('#jform_params_showtitlecat').parents('.control-group').show();
                    jQuery('#jform_params_is_subcat').parents('.control-group').show();
                    jQuery('#jform_params_is_all').parents('.control-group').show();
                    jQuery('#jform_params_number_link_items').parents('.control-group').show();
                    jQuery('#jform_params_columns').parents('.control-group').show();
                    jQuery('#jform_params_thumb_list_width').parents('.control-group').show();
                    jQuery('#jform_params_thumb_list_height').parents('.control-group').show();
                    jQuery('#jform_params_show_title_list').parents('.control-group').show();
                    jQuery('#jform_params_is_image_list').parents('.control-group').show();
                    jQuery('#jform_params_show_intro_list').parents('.control-group').show();
                    jQuery('#jform_params_show_date_list').parents('.control-group').show();

                }
            }
        </script>
        <?php
    }

}
