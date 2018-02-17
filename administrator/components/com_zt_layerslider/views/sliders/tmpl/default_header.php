<?php

defined('_JEXEC') or die;

//order=asc&ot=name&type=reg
//if(isset($_GET['ot']) && isset($_GET['order']) && isset($_GET['type'])){
//    $order = array();
//    switch($_GET['ot']){
//        case 'alias':
//            $order['alias'] = ($_GET['order'] == 'asc') ? 'ASC' : 'DESC';
//            break;
//        case 'favorite':
//            $order['favorite'] = ($_GET['order'] == 'asc') ? 'ASC' : 'DESC';
//            break;
//        case 'name':
//        default:
//            $order['title'] = ($_GET['order'] == 'asc') ? 'ASC' : 'DESC';
//            break;
//    }
//
//    $orders = $order;
//}
//
// ORDER ----------------------------------------------------------------------------------------- will do in model
//$slider = new ZTSlider();
//$arrSliders = $slider->getArrSliders($orders);

$addNewLink = JRoute::_('index.php?option=com_zt_layerslider&view=slider&layout=edit',false);

$exampleID = '"slider1"';
if(!empty($this->items))
    $exampleID = '"'.$this->items[0]->alias.'"';

?>

    <div class='wrap'>
        <div class="title_line nobgnopd" style="height:auto; min-height:50px">
            <div class="slider-sortandfilter">
                <span class="slider-listviews slider-lg-views" data-type="rs-listview"><i class="eg-icon-align-justify"></i></span>
                <span class="slider-gridviews slider-lg-views active" data-type="rs-gridview"><i class="eg-icon-th"></i></span>
                <span class="slider-sort-drop"><?php echo JText::_("COM_ZT_LAYERSLIDER_SORT_BY");  ?></span>
                <select id="sort-sliders" name="sort-sliders" style="max-width: 105px;" class="withlabel">
                    <option value="id" selected="selected"><?php echo JText::_("COM_ZT_LAYERSLIDER_BY_ID");  ?></option>
                    <option value="name"><?php echo JText::_("COM_ZT_LAYERSLIDER_BY_NAME"); ?></option>
                    <option value="type"><?php echo JText::_("COM_ZT_LAYERSLIDER_BY_TYPE"); ?></option>
                    <option value="favorit"><?php echo JText::_("COM_ZT_LAYERSLIDER_BY_FAVOURTIE"); ?></option>
                </select>

                <span class="slider-filter-drop"><?php echo JText::_("COM_ZT_LAYERSLIDER_FILTER_BY");?></span>

                <select id="filter-sliders" name="filter-sliders" style="max-width: 105px;" class="withlabel">
                    <option value="all" selected="selected"><?php echo JText::_("COM_ZT_LAYERSLIDER_ALL");  ?></option>
                    <option value="posts"><?php echo JText::_("COM_ZT_LAYERSLIDER_POSTS");?></option>
                    <option value="gallery"><?php echo JText::_("COM_ZT_LAYERSLIDER_GALLERY"); ?></option>
                    <option value="vimeo"><?php echo JText::_("COM_ZT_LAYERSLIDER_VIMEO");?></option>
                    <option value="youtube"><?php echo JText::_("COM_ZT_LAYERSLIDER_YOUTUBE"); ?></option>
                    <option value="twitter"><?php echo JText::_("COM_ZT_LAYERSLIDER_TWITTER");?></option>
                    <option value="facebook"><?php echo JText::_("COM_ZT_LAYERSLIDER_FACEBOOK");?></option>
                    <option value="instagram"><?php echo JText::_("COM_ZT_LAYERSLIDER_INSTAGRAM");?></option>
                    <option value="flickr"><?php echo JText::_("COM_ZT_LAYERSLIDER_FLICKR");?></option>
                </select>
                <form style="display: inline-block" action="<?php echo JRoute::_('index.php?option=com_zt_layerslider');?>" method="post" name="adminForm" id="adminForm" class="form-inline">
                    <label class="slider-sort-drop" for="zt-language-change"><?php echo JText::_('JOPTION_SELECT_LANGUAGE') ?></label>
                    <select id="zt-language-change" name="filter_language" class="input-medium" onchange="this.form.submit()">
                        <option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE'); ?></option>
                        <?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language')); ?>
                    </select>
                </form>
            </div>
            <div style="width:100%;height:1px;float:none;clear:both"></div>

        </div>

        <?php
        if(empty($this->items)){
            ?>
            <span style="display:block;margin-top:15px;margin-bottom:15px;">
		<?php  echo JText::_("COM_ZT_LAYERSLIDER_NO_SLIDER_FOUND");?>
		</span>
            <?php
        }

        echo $this->loadTemplate('sliders_list');

        ?>
        <!--
        THE INFO ABOUT EMBEDING OF THE SLIDER
        -->
        <div class="rs-dialog-embed-slider" title="<?php echo JText::_("COM_ZT_LAYERSLIDER_EMBED_SLIDER");?>" style="display: none;">
            <div class="revyellow" style="background: none repeat scroll 0% 0% #F1C40F; left:0px;top:55px;position:absolute;height:205px;padding:20px 10px;"><i style="color:#fff;font-size:25px" class="revicon-arrows-ccw"></i></div>
            <div style="margin:5px 0px; padding-left: 55px;">
                <div style="font-size:14px;margin-bottom:10px;"><strong><?php echo JText::_("COM_ZT_LAYERSLIDER_STANDARD_EMBEDING");?></strong></div>
                <?php echo JText::_("COM_ZT_LAYERSLIDER_FOR_THE");?> <b><?php echo JText::_("COM_ZT_LAYERSLIDER_PAGE_OR_POST_EDITOR"); ?></b> <?php echo JText::_("COM_ZT_LAYERSLIDER_INSERT_THE_SHORTCODE");?> <code class="rs-example-alias-1"></code>
                <div style="width:100%;height:10px"></div>
<!--                --><?php //echo JText::_("COM_ZT_LAYERSLIDER_FROM_THE");?><!-- <b>--><?php //echo JText::_("COM_ZT_LAYERSLIDER_WIDGETS_PANEL");?><!--</b> --><?php //echo JText::_("COM_ZT_LAYERSLIDER_DRAG_THE");  ?>
                <div style="width:100%;height:25px"></div>

            </div>
        </div>
        <script>
            jQuery('#advanced-emeding').click(function() {
                jQuery('#advanced-accord').toggle(200);
            });
        </script>


        <div style="width:100%;height:40px"></div>

        <!-- THE UPDATE HISTORY OF SLIDER REVOLUTION -->
        <div style="width:100%;height:40px"></div>
        <div class="rs-update-history-wrapper">
            <div class="rs-dash-title-wrap">
                <div class="rs-dash-title"><?php echo JText::_("COM_ZT_LAYERSLIDER_UPDATE_HISTORY");?></div>
            </div>
            <div class="rs-update-history"><?php echo file_get_contents(JPATH_COMPONENT_ADMINISTRATOR.'/release_log.html'); ?></div>
        </div>

    </div>

    <!-- Import slider dialog -->
    <div id="dialog_import_slider" title="<?php echo JText::_("COM_ZT_LAYERSLIDER_IMPORT_SLIDER");?>" class="dialog_import_slider" style="display:none">
        <form action="index.php" enctype="multipart/form-data" method="post">
            <br>
            <?php echo JText::_("COM_ZT_LAYERSLIDER_CHOOSE_IMPORT_FILE");?>:
            <br>
            <input type="file" size="60" name="import_file" class="input_import_slider">
            <br><br>
            <span style="font-weight: 700;">Note: styles templates will be updated if they exist!</span><br><br>
            <table>
                <tr>
                    <td><?php echo JText::_("COM_ZT_LAYERSLIDER_CUSTOM_ANIMATIONS");?></td>
                    <td><input type="radio" name="update_animations" value="true" checked="checked"> <?php echo JText::_("COM_ZT_LAYERSLIDER_OVERWRITE"); ?></td>
                    <td><input type="radio" name="update_animations" value="false"> <?php echo JText::_("COM_ZT_LAYERSLIDER_APPEND");?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_("COM_ZT_LAYERSLIDER_CUSTOM_NAVAGATIONS");?></td>
                    <td><input type="radio" name="update_navigations" value="true" checked="checked"> <?php echo JText::_("COM_ZT_LAYERSLIDER_OVERWRITE");?></td>
                    <td><input type="radio" name="update_navigations" value="false"> <?php echo JText::_("COM_ZT_LAYERSLIDER_APPEND");?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_("COM_ZT_LAYERSLIDER_STATIC_STYLES"); ?></td>
                    <td><input type="radio" name="update_static_captions" value="true"> <?php echo JText::_("COM_ZT_LAYERSLIDER_OVERWRITE");?></td>
                    <td><input type="radio" name="update_static_captions" value="false"> <?php echo JText::_("COM_ZT_LAYERSLIDER_APPEND");?></td>
                    <td><input type="radio" name="update_static_captions" value="none" checked="checked"> <?php echo JText::_("COM_ZT_LAYERSLIDER_IGNORE");?></td>
                </tr>
            </table>
            <br><br>
            <input type="submit" class="button-primary revblue tp-be-button rev-import-slider-button" style="display: none;" value="<?php echo JText::_("COM_ZT_LAYERSLIDER_IMPORT_SLIDER");?>">
            <input type="hidden" name="option" value="com_zt_layerslider">
            <input type="hidden" name="task" value="slider.importsliderslidersview">
        </form>
    </div>

    <div id="dialog_duplicate_slider" class="dialog_duplicate_layer" title="<?php echo JText::_("COM_ZT_LAYERSLIDER_DUPPLICATE");?>" style="display:none;">
        <div style="margin-top:14px">
            <span style="margin-right:15px"><?php echo JText::_("COM_ZT_LAYERSLIDER_TITLE");?></span><input id="rs-duplicate-animation" type="text" name="rs-duplicate-animation" value="" />
        </div>
    </div>

    <div id="dialog_duplicate_slider_package" class="dialog_duplicate_layer" title="<?php echo JText::_("COM_ZT_LAYERSLIDER_DUPPLICATE");?>" style="display:none;">
        <div style="margin-top:14px">
            <span style="margin-right:15px"><?php echo JText::_("COM_ZT_LAYERSLIDER_PREFIX");?></span><input id="rs-duplicate-prefix" type="text" name="rs-duplicate-prefix" value="" />
        </div>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function(){
            ZtSliderAdmin.initSlidersListView();
            ZtSliderAdmin.initNewsletterRoutine();

            jQuery('#benefitsbutton').hover(function() {
                jQuery('#benefitscontent').slideDown(200);
            }, function() {
                jQuery('#benefitscontent').slideUp(200);
            });

            jQuery('#why-subscribe').hover(function() {
                jQuery('#why-subscribe-wrapper').slideDown(200);
            }, function() {
                jQuery('#why-subscribe-wrapper').slideUp(200);
            });

            jQuery('#tp-validation-box').click(function() {
                jQuery(this).css({cursor:"default"});
                if (jQuery('#rs-validation-wrapper').css('display')=="none") {
                    jQuery('#tp-before-validation').hide();
                    jQuery('#rs-validation-wrapper').slideDown(200);
                }
            });

            jQuery('body').on('click','.rs-dash-more-info',function() {
                var btn = jQuery(this),
                    p = btn.closest('.rs-dash-widget-inner'),
                    tmb = btn.data('takemeback'),
                    btxt = '';

                btxt = btxt + '<div class="rs-dash-widget-warning-panel">';
                btxt = btxt + '	<i class="eg-icon-cancel rs-dash-widget-wp-cancel"></i>';
                btxt = btxt + '	<div class="rs-dash-strong-content">'+ btn.data("title")+'</div>';
                btxt = btxt + '	<div class="rs-dash-content-space"></div>';
                btxt = btxt + '	<div>'+btn.data("content")+'</div>';

                if (tmb!=="false" && tmb!==false) {
                    btxt = btxt + '	<div class="rs-dash-content-space"></div>';
                    btxt = btxt + '	<span class="rs-dash-invers-button-gray rs-dash-close-panel">Thanks! Take me back</span>';
                }
                btxt = btxt + '</div>';

                p.append(btxt);
                var panel = p.find('.rs-dash-widget-warning-panel');

                punchgs.TweenLite.fromTo(panel,0.3,{y:-10,autoAlpha:0},{autoAlpha:1,y:0,ease:punchgs.Power3.easeInOut});
                panel.find('.rs-dash-widget-wp-cancel, .rs-dash-close-panel').click(function() {
                    punchgs.TweenLite.to(panel,0.3,{y:-10,autoAlpha:0,ease:punchgs.Power3.easeInOut});
                    setTimeout(function() {
                        panel.remove();
                    },300)
                })
            });
        });
    </script>
<?php
echo $this->loadTemplate('template_slider_selector');
?>
