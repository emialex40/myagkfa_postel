<?php
/**
 * @package ZT LayerSlider
 * @author    ZooTemplate.com
 * @copyright(C) 2015 - ZooTemplate.com
 * @license PHP files are GNU/GPL
 **/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class ZT_LayersliderModelSlider extends JModelAdmin
{
    protected $_context = 'com_zt_layerslider';

    /**
     * @var		string	The prefix to use with controller messages.
     * @since	1.6
     */
    protected $text_prefix = 'COM_ZT_LAYERSLIDER';


    /**
     * Method to test whether a record can be deleted.
     *
     * @param	object	$record	A record object.
     *
     * @return	boolean	True if allowed to delete the record. Defaults to the permission set in the component.
     * @since	1.6
     */
    protected function canDelete($record)
    {
        if (!empty($record->id)) {
            if ($record->state != -2) {
                return ;
            }
            $user = JFactory::getUser();
            return $user->authorise('core.delete', 'com_zt_layerslider.slider.'.(int) $record->id);
        }
    }

    /**
     * Method to test whether a record can have its state edited.
     *
     * @param	object	$record	A record object.
     *
     * @return	boolean	True if allowed to change the state of the record. Defaults to the permission set in the component.
     * @since	1.6
     */
    protected function canEditState($record)
    {
        $user = JFactory::getUser();

        // Check for existing slider.
        if (!empty($record->id)) {
            return $user->authorise('core.edit.state', 'com_zt_layerslider.slider.'.(int) $record->id);
        }
        else {
            return parent::canEditState('com_zt_layerslider');
        }
    }

    /**
     * Prepare and sanitise the table data prior to saving.
     *
     * @param	JTable	A JTable object.
     *
     * @return	void
     * @since	1.6
     */
    protected function prepareTable($table)
    {
        
        if (empty($table->id)) {
            $table->reorder('state >= 0');
        }

        return $table;
    }

    public function getTable($type = 'Slider', $prefix = 'ZT_LayersliderTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }


    public function getItem($pk = null)
    {
        if ($item = parent::getItem($pk)) {
            
            // Convert the params field to an array.
            $registry = new JRegistry;
            $registry->loadString($item->attribs);
            $item->attribs = $registry->toArray();

        }

        return $item;
    }

    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_zt_layerslider.slider', 'slider', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        $jinput = JFactory::getApplication()->input;

        // The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
        if ($jinput->get('a_id'))
        {
            $id =  $jinput->get('a_id', 0);
        }
        // The back end uses id so we use that the rest of the time and set it to 0 by default.
        else
        {
            $id =  $jinput->get('id', 0);
        }
        // Determine correct permissions to check.
        if ($this->getState('slider.id'))
        {
            $id = $this->getState('slider.id');
        }

        $user = JFactory::getUser();

        // Check for existing slider.
        // Modify the form based on Edit State access controls.
        if ($id != 0 && (!$user->authorise('core.edit.state', 'com_zt_layerslider.slider.'.(int) $id))
            || ($id == 0 && !$user->authorise('core.edit.state', 'com_zt_layerslider'))
        )
        {
            // Disable fields for display.

            $form->setFieldAttribute('ordering', 'disabled', 'true');
            $form->setFieldAttribute('state', 'disabled', 'true');

            // Disable fields while saving.
            // The controller has already verified this is an slider you can edit.
            $form->setFieldAttribute('state', 'filter', 'unset');
        }
        $form->setFieldAttribute('attribs', 'filter', 'raw');

        return $form;
    }

    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_zt_layerslider.edit.slider.data', array());

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

}