<?php
/**
 * @package ZT LayerSlider
 * @author    ZooTemplate.com
 * @copyright(C) 2015 - ZooTemplate.com
 * @license PHP files are GNU/GPL
 **/
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');


class ZT_LayersliderControllerSliders extends JControllerAdmin
{

    /**
     * Constructor.
     *
     * @param    array $config    An optional associative array of configuration settings.
     * @return    ZTLayersliderControllerSliders
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array())
    {

        parent::__construct($config);
        $version = new JVersion;
        if (version_compare($version->getShortVersion(), '3.0.0', '<')) {
            $this->input = JFactory::getApplication()->input;
        }
    }

    /**
     * Proxy for getModel.
     *
     * @param    string $name    The name of the model.
     * @param    string $prefix    The prefix for the PHP class name.
     *
     * @return    JModel
     * @since    1.6
     */
    public function getModel($name = 'Slider', $prefix = 'ZT_LayersliderModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }


    /**
     * Method to save the submitted ordering values for records via AJAX.
     *
     * @return  void
     *
     * @since   3.0
     */
    public function saveOrderAjax()
    {
        $pks = $this->input->post->get('cid', array(), 'array');
        $order = $this->input->post->get('order', array(), 'array');

        // Sanitize the input
        JArrayHelper::toInteger($pks);
        JArrayHelper::toInteger($order);

        // Get the model
        $model = $this->getModel();

        // Save the ordering
        $return = $model->saveorder($pks, $order);

        if ($return)
        {
            echo "1";
        }

        // Close the application
        JFactory::getApplication()->close();
    }

    public function duplicate() {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $ids = JRequest::getVar('cid', null, 'post', 'array');

        if(count($ids) > 0) {
            $db = JFactory::getDbo();
            foreach($ids as $id) {
                $query = $db->getQuery(true);
                $query->select('*');
                $query->from($db->quoteName('#__zt_layerslider_slider'));
                $query->where($db->quoteName('id') . ' = '. $id);
                $query->order('ordering ASC');

                // Reset the query using our newly populated query object.
                $db->setQuery($query);

                // Load the results as a list of stdClass objects (see later for more options on retrieving data).
                $results = $db->loadObjectList();

                if(isset($results[0])) {

                    // Create and populate an object.
                    $slider = new stdClass();
                    $slider->title = $results[0]->title;
                    $slider->alias = $results[0]->alias;
                    $slider->state = $results[0]->state;
                    $slider->attribs = $results[0]->attribs;
                    $slider->ordering = $results[0]->ordering;
                    $slider->access = $results[0]->access;
                    $slider->language = $results[0]->language;
                    $slider->type = $results[0]->type;
                    $slider->favourite = $results[0]->favourite;

                    // Insert the object into the user profile table.
                    $db->insertObject('#__zt_layerslider_slider', $slider);


                    $query_slide = $db->getQuery(true);
                    $query_slide->select('*');
                    $query_slide->from($db->quoteName('#__zt_layerslider_slide'));
                    $query_slide->where($db->quoteName('slider_id') . ' = '. $results[0]->id);
                    $query_slide->order('ordering ASC');

                    // Reset the query using our newly populated query object.
                    $db->setQuery($query_slide);

                    // Load the results as a list of stdClass objects (see later for more options on retrieving data).
                    $slides = $db->loadObjectList();


                    if(count($slides) > 0) {
                        foreach($slides as $slide_old) {
                            //get max id
                            $max_id = 1;
                            $get_id_query = $db->getQuery(true);
                            $get_id_query = "SELECT id FROM  `#__zt_layerslider_slide` ORDER BY id DESC LIMIT 1";
                            $db->setQuery($get_id_query);
                            $id_result = $db->loadObjectList();
                            if(isset($id_result[0])) {
                                $max_id = $id_result[0]->id;
                            }

                            //get max ordering number
                            $max_order = 1;
                            $get_order_query = $db->getQuery(true);
                            $get_order_query = "SELECT ordering FROM  `#__zt_layerslider_slide` ORDER BY ordering DESC LIMIT 1";
                            $db->setQuery($get_order_query);
                            $order_result = $db->loadObjectList();
                            if(isset($order_result[0])) {
                                $max_order = $order_result[0]->ordering;
                            }

                            // Create and populate an object.
                            $slide = new stdClass();
                            $slide->id = $max_id + 1;
                            $slide->title = $slide_old->title;
                            $slide->slider_id = $slider->id;
                            $slide->ordering = $max_order + 1;
                            $slide->state = $slide_old->state;
                            $slide->attribs = $slide_old->attribs;
                            $slide->layers = $slide_old->layers;
                            $slide->access = $slide_old->access;
                            $slide->language = $slide_old->language;

                            // Insert the object into the user profile table.
                            $db->insertObject('#__zt_layerslider_slide', $slide);

                        }
                    }

                }
            }
        }
        $this->setRedirect(JRoute::_('index.php?option=' . $this->option ));
    }
}
