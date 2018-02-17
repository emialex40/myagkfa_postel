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


class ZT_LayersliderControllerSlides extends JControllerAdmin
{

    /**
     * Constructor.
     *
     * @param    array $config    An optional associative array of configuration settings.
     * @return    ZTLayersliderControllerSlides
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
    public function getModel($name = 'Slide', $prefix = 'ZT_LayersliderModel', $config = array('ignore_request' => true))
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



    public function publish()
    {
        // Check for request forgeries
        JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

        // Get items to publish from the request.
        $cid = JFactory::getApplication()->input->get('cid', array(), 'array');
        $data = array('publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3);
        $task = $this->getTask();
        $value = JArrayHelper::getValue($data, $task, 0, 'int');

        if (empty($cid))
        {
            JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
        }
        else
        {
            // Get the model.
            $model = $this->getModel();

            // Make sure the item ids are integers
            JArrayHelper::toInteger($cid);

            // Publish the items.
            if (!$model->publish($cid, $value))
            {
                JLog::add($model->getError(), JLog::WARNING, 'jerror');
            }
            else
            {
                if ($value == 1)
                {
                    $ntext = $this->text_prefix . '_N_ITEMS_PUBLISHED';
                }
                elseif ($value == 0)
                {
                    $ntext = $this->text_prefix . '_N_ITEMS_UNPUBLISHED';
                }
                elseif ($value == 2)
                {
                    $ntext = $this->text_prefix . '_N_ITEMS_ARCHIVED';
                }
                else
                {
                    $ntext = $this->text_prefix . '_N_ITEMS_TRASHED';
                }
                $this->setMessage(JText::plural($ntext, count($cid)));
            }
        }
        $extension = $this->input->get('extension');
        $extensionURL = ($extension) ? '&extension=' . $extension : '';
        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . '&slider='. JFactory::getApplication()->input->getInt('slider') . $extensionURL, false));
    }

    /**
     * Duplicate slide item
     */
    public function duplicate () {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $ids = JRequest::getVar('cid', null, 'post', 'array');
        //print_r($ids);
        if(count($ids) > 0 ){
            $db = JFactory::getDbo();

            foreach($ids as $id) {

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

                $query = $db->getQuery(true);
                $query->select('*');
                $query->from($db->quoteName('#__zt_layerslider_slide'));
                $query->where($db->quoteName('id') . ' = '. $id);
                $query->order('ordering ASC');

                // Reset the query using our newly populated query object.
                $db->setQuery($query);

                // Load the results as a list of stdClass objects (see later for more options on retrieving data).
                $results = $db->loadObjectList();

                if(isset($results[0])) {

                    // Create and populate an object.
                    $slide = new stdClass();
                    $slide->id = $max_id + 1;
                    $slide->title = $results[0]->title;
                    $slide->slider_id = $results[0]->slider_id;
                    $slide->ordering = $max_order + 1;
                    $slide->state = $results[0]->state;
                    $slide->attribs = $results[0]->attribs;
                    $slide->layers = $results[0]->layers;
                    $slide->access = $results[0]->access;
                    $slide->language = $results[0]->language;

                    // Insert the object into the user profile table.
                    $db->insertObject('#__zt_layerslider_slide', $slide);
                }
            }
        }
        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . '&slider='. JFactory::getApplication()->input->getInt('slider') . $extensionURL, false));
    }

    /**
     * Changes the order of one or more records.
     *
     * @return  boolean  True on success
     *
     * @since   11.1
     */
    public function reorder()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Initialise variables.
        $ids = JRequest::getVar('cid', null, 'post', 'array');
        $inc = ($this->getTask() == 'orderup') ? -1 : +1;

        $model = $this->getModel();
        $return = $model->reorder($ids, $inc);
        if ($return === false)
        {
            // Reorder failed.
            $message = JText::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
            $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list .'&slider='. JFactory::getApplication()->input->getInt('slider'), false), $message, 'error');
            return false;
        }
        else
        {
            // Reorder succeeded.
            $message = JText::_('JLIB_APPLICATION_SUCCESS_ITEM_REORDERED');
            $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . '&slider='. JFactory::getApplication()->input->getInt('slider'), false), $message);
            return true;
        }
    }

    /**
     * Method to save the submitted ordering values for records.
     *
     * @return  boolean  True on success
     *
     * @since   11.1
     */
    public function saveorder()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Get the input
        $pks = JRequest::getVar('cid', null, 'post', 'array');
        $order = JRequest::getVar('order', null, 'post', 'array');

        // Sanitize the input
        JArrayHelper::toInteger($pks);
        JArrayHelper::toInteger($order);

        // Get the model
        $model = $this->getModel();

        // Save the ordering
        $return = $model->saveorder($pks, $order);

        if ($return === false)
        {
            // Reorder failed
            $message = JText::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
            $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . '&slider='. JFactory::getApplication()->input->getInt('slider'), false), $message, 'error');
            return false;
        }
        else
        {
            // Reorder succeeded.
            $this->setMessage(JText::_('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED'));
            $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . '&slider='. JFactory::getApplication()->input->getInt('slider'), false));
            return true;
        }
    }
    
    

}
