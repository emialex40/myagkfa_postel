<?php
/**
 * @package     CSVI
 * @subpackage  Templates
 *
 * @author      Roland Dalmulder <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - 2016 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        http://www.csvimproved.com
 */

defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_csvi/views/default/view.html.php';

/**
 * Template view.
 *
 * @package     CSVI
 * @subpackage  Templates
 * @since       6.0
 */
class CsviViewTemplate extends CsviViewDefault
{
	/**
	 * The action to perform.
	 *
	 * @var    string
	 * @since  6.0
	 */
	protected $action = null;

	/**
	 * The component to use.
	 *
	 * @var    string
	 * @since  6.0
	 */
	protected $component = null;

	/**
	 * The operation to perform.
	 *
	 * @var    string
	 * @since  6.0
	 */
	protected $operation = null;

	/**
	 * The forms handler.
	 *
	 * @var    object
	 * @since  6.0
	 */
	protected $forms = null;

	/**
	 * List of available components.
	 *
	 * @var    array
	 * @since  6.0
	 */
	protected $components = array();

	/**
	 * List of tabs to show.
	 *
	 * @var    array
	 * @since  6.0
	 */
	protected $optiontabs = array();

	/**
	 * Template details
	 *
	 * @var    object
	 * @since  6.5.0
	 */
	protected $item;

	/**
	 * Wizard step
	 *
	 * @var    int
	 * @since  6.5.0
	 */
	protected $step;

	/**
	 * Show the extra help
	 *
	 * @var    int
	 * @since  6.5.0
	 */
	protected $extraHelp;

	/**
	 * Executes before rendering the page for the Add task.
	 *
	 * @param   string  $tpl  Subtemplate to use
	 *
	 * @return  boolean  Return true to allow rendering of the page
	 */
	protected function onAdd($tpl = null)
	{
		// Let the parent get the template details
		parent::onAdd($tpl);

		// Load the helper
		$helper = new CsviHelperCsvi;
		$jinput = JFactory::getApplication()->input;

		// Set some variables
		$post_form = $this->input->get('jform', array(), 'array');
		$this->action = (isset($post_form['action'])) ? $post_form['action'] : $this->item->options->get('action', 'import');
		$this->component = (isset($post_form['component'])) ? $post_form['component'] : $this->item->options->get('component', 'com_csvi');
		$this->operation = (isset($post_form['operation'])) ? $post_form['operation'] : $this->item->options->get('operation', 'customimport');

		// Get the step
		$this->step = $jinput->getInt('step', 0);

		if ($this->item->csvi_template_id == 0 && $jinput->getCmd('task') == 'add')
		{
			$this->step = 1;
		}

		// Load the extra help settings
		$db = JFactory::getDbo();
		$settings = new CsviHelperSettings($db);
		$this->extraHelp = $settings->get('extraHelp');

		// Reset the option values
		$this->item->options->set('action', $this->action);
		$this->item->options->set('component', $this->component);
		$this->item->options->set('operation', $this->operation);

		// Make the template available for the form fields
		$jinput->set('item', $this->item);

		// Add the form files
		JFormHelper::addFormPath(JPATH_ADMINISTRATOR . '/components/com_csvi/views/template/tmpl/');
		JFormHelper::addFormPath(JPATH_ADMINISTRATOR . '/components/com_csvi/views/template/tmpl/' . $this->action);
		JFormHelper::addFormPath(JPATH_ADMINISTRATOR . '/components/com_csvi/addon/' . $this->component . '/tmpl/' . $this->action);

		// Add the form paths
		JFormHelper::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_csvi/models/fields/');
		JFormHelper::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_csvi/addon/' . $this->component . '/fields/');

		$this->addTemplatePath(JPATH_ADMINISTRATOR . '/components/com_csvi/addon/' . $this->component . '/tmpl/' . $this->action);

		// Load the components
		$this->components = $helper->getComponents();
		array_unshift($this->components, JHtml::_('select.option', '', 'COM_CSVI_MAKE_CHOICE'));

		// Load the option tabs
		if ($this->component && $this->operation)
		{
			$this->optiontabs = FOFModel::getTmpInstance('Tasks', 'CsviModel')->getOptions($this->component, $this->action, $this->operation);
		}

		// Setup the autoloader
		$addon = ucfirst($this->component);
		$path = JPATH_ADMINISTRATOR . '/components/com_csvi/addon/' . strtolower($addon);
		JLoader::registerPrefix($addon, $path);

		// Check if we are doing a wizard
		if ($this->step == 0)
		{
			// Load the operations
			$this->forms = new stdClass;
			$form = FOFForm::getInstance('operations', 'operations');
			$form->bind(array_merge($this->item->getData(), array('jform' => $this->item->options->toArray())));
			$form->setFieldAttribute('rules', 'action', $this->action);
			$this->forms->operations = $helper->renderMyForm($form, $this->getModel(), $this->input);

			// Load the language file for the selected component
			$helper->loadLanguage($this->component);

			// Load the forms
			foreach ($this->optiontabs as $tab)
			{
				$tabname = $tab;

				if (stripos($tab, '.'))
				{
					list($tabname, $pro) = explode('.', $tab);
				}

				if (!empty($tabname))
				{
					// We don't do the fields tab as this is special, fields are loaded separately
					if ($tabname !== 'fields' && stripos($tabname, 'custom_') === false)
					{
						$form = FOFForm::getInstance($tabname, $tabname);
						$form->bind(array('jform' => $this->item->options->toArray()));

						// Render standard XMLs
						$this->forms->$tabname = $helper->renderMyForm($form, $this->getModel(), $this->input);
					}
					elseif (($tabname == 'fields' && $this->action == 'export') || stripos($tabname, 'custom_') !== false)
					{
						// Do not render any page of the type custom, this is handled in a PHP file
						$form = FOFForm::getInstance(str_ireplace('custom_', '', $tabname), str_ireplace('custom_', '', $tabname));
						$form->bind(array('jform' => $this->item->options->toArray()));
						$this->forms->$tabname = $form;
					}
				}
			}
		}
		else
		{
			// Get the page file
			$tabname = false;

			switch ($this->step)
			{
				case 1:
					$tabname = 'step1';
					break;
				case 2:
					$tabname = 'source';
					break;
				case 3:
					$tabname = 'file';
					break;
				case 4:
					if ($this->action == 'export')
					{
						$tabname = 'fields';
					}
					break;
			}

			if ($tabname)
			{
				// Load the operations
				$this->forms = new stdClass;
				/** @var FOFForm $form */
				$form = FOFForm::getInstance($tabname, $tabname);
				$form->bind(array_merge($this->item->getData(), array('jform' => $this->item->options->toArray())));

				// These fields we don't want to use in the wizard
				$form->removeField('record_name', 'jform');
				$form->removeField('export_file', 'jform');
				$form->removeField('export_site', 'jform');
				$form->setValue('enabled', '', 1);

				try
				{
					$this->forms->form = $helper->renderMyForm($form, $this->getModel(), $this->input);
				}
				catch (Exception $e)
				{
					JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
				}
			}
		}

		// Load the associated fields
		$this->fields = FOFModel::getTmpInstance('Templatefields', 'CsviModel')
			->csvi_template_id($this->item->csvi_template_id)
			->filter_order('ordering')
			->getList();

		return true;
	}
}
