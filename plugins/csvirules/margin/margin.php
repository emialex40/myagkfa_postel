<?php
/**
 * @package     CSVI
 * @subpackage  Plugin.Margin
 *
 * @author      Roland Dalmulder <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - 2016 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        http://www.csvimproved.com
 */

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * Replaces values.
 *
 * @package     CSVI
 * @subpackage  Plugin.Margin
 * @since       6.0
 */
class plgCsvirulesMargin extends RantaiPluginDispatcher
{

	/**
	 * The unique ID of the plugin
	 *
	 * @var    string
	 * @since  6.0
	 */
	private $id = 'csvimargin';

	/**
	 * Return the name of the plugin.
	 *
	 * @return  array  The name and ID of the plugin.
	 *
	 * @since   6.0
	 */
	public function getName()
	{
		return array('value' => 'csvimargin', 'text' => 'CSVI Margin');
	}

	/**
	 * Method to get the name only of the plugin.
	 *
	 * @param   string  $plugin  The ID of the plugin
	 *
	 * @return  string  The name of the plugin.
	 *
	 * @since   6.0
	 */
	public function getSingleName($plugin)
	{
		if ($plugin == $this->id)
		{
			return 'CSVI Margin';
		}
	}

	/**
	 * Method to get the field options.
	 *
	 * @param   string  $plugin   The ID of the plugin
	 * @param   array   $options  An array of settings
	 *
	 * @return  string  The rendered form with plugin options.
	 *
	 * @since   6.0
	 */
	public function getForm($plugin, $options=false)
	{
		if ($plugin == $this->id)
		{
			// Load the language files
			$lang = JFactory::getLanguage();
			$lang->load('plg_csvirules_margin', JPATH_ADMINISTRATOR, 'en-GB', true);
			$lang->load('plg_csvirules_margin', JPATH_ADMINISTRATOR, null, true);

			// Add the form path for this plugin
			FOFForm::addFormPath(JPATH_PLUGINS . '/csvirules/margin/');

			// Load the helper that renders the form
			$helper = new CsviHelperCsvi;

			// Instantiate the form
			$form = FOFForm::getInstance('margin', 'form_margin');

			// Bind any existing data
			$form->bind(array('pluginform' => $options));

			// Create some dummies
			$input = new FOFInput();
			$model = new FOFModel();

			// Render the form
			return $helper->renderMyForm($form, $model, $input);
		}
	}

	/**
	 * Run the rule.
	 *
	 * @param   string  $plugin    The ID of the plugin.
	 * @param   array   $settings  The plugin settings set for the field.
	 * @param   array   $field     The field being process.
	 * @param   array   $fields    All fields used for import/export.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	public function runRule($plugin, $settings, $field, $fields)
	{
		if ($plugin == $this->id)
		{
			// Perform the margin
			if (!empty($settings))
			{
				$margin = $settings->margin;
				$value = $field->value;

				// Check if we have a percentage
				if ($settings->valuetype == 'percentage')
				{
					// Calculate the margin
					$margin = (100 + $settings->margin) / 100;
				}

				switch ($settings->operation)
				{
					case 'multiplication':
						$value = $field->value * $margin;
						break;
					case 'addition':
						$value = $field->value + $margin;
						break;
					case 'subtraction':
						$value = $field->value - $margin;
						break;
					case 'division':
						$value = $field->value / $margin;
						break;
				}

				$fields->updateField($field, $value);
			}
		}
	}
}
