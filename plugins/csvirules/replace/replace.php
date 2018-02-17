<?php
/**
 * @package     CSVI
 * @subpackage  Plugin.Replace
 *
 * @author      Roland Dalmulder <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - 2016 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        http://www.csvimproved.com
 */

defined('_JEXEC') or die;

/**
 * Replaces values.
 *
 * @package     CSVI
 * @subpackage  Plugin.Replace
 * @since       6.0
 */
class PlgCsvirulesReplace extends RantaiPluginDispatcher
{
	/**
	 * The unique ID of the plugin
	 *
	 * @var    string
	 * @since  6.0
	 */
	private $id = 'csvireplace';

	/**
	 * Return the name of the plugin.
	 *
	 * @return  array  The name and ID of the plugin.
	 *
	 * @since   6.0
	 */
	public function getName()
	{
		return array('value' => $this->id, 'text' => 'CSVI Replace');
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
			return 'CSVI Replace';
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
	public function getForm($plugin, $options = array())
	{
		if ($plugin == $this->id)
		{
			// Load the language files
			$lang = JFactory::getLanguage();
			$lang->load('plg_csvirules_replace', JPATH_ADMINISTRATOR, 'en-GB', true);
			$lang->load('plg_csvirules_replace', JPATH_ADMINISTRATOR, null, true);

			// Add the form path for this plugin
			FOFForm::addFormPath(JPATH_PLUGINS . '/csvirules/replace/');

			// Load the helper that renders the form
			$helper = new CsviHelperCsvi;

			// Instantiate the form
			$form = FOFForm::getInstance('replace', 'form_replace');

			// Bind any existing data
			$form->bind(array('pluginform' => $options));

			// Create some dummies
			$input = new FOFInput;
			$model = new FOFModel;

			// Render the form
			return $helper->renderMyForm($form, $model, $input);
		}
	}

	/**
	 * Run the rule.
	 *
	 * @param   string            $plugin    The ID of the plugin
	 * @param   object            $settings  The plugin settings set for the field
	 * @param   object            $field     The field being process
	 * @param   CsviHelperFields  $fields    All fields used for import/export
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	public function runRule($plugin, $settings, $field, $fields)
	{
		if ($plugin == $this->id)
		{
			// Perform the replacement
			if (!empty($settings))
			{
				// Check if we have a multivalue
				if ($settings->multivalue)
				{
					$separator = $settings->separator;
					$findtext = explode($separator, $settings->findtext);
					$replacetext = explode($separator, $settings->replacetext);
				}
				else
				{
					$findtext = $settings->findtext;
					$replacetext = $settings->replacetext;
				}

				// Set the old value
				$value = $field->value;

				switch ($settings->method)
				{
					case 'text':
						$value = str_ireplace($findtext, $replacetext, $field->value);
						break;
					case 'regex':
						$value = preg_replace($findtext, $replacetext, $field->value);
						break;
				}

				// Update the field if needed
				if ($field->value != $value)
				{
					$fields->updateField($field, $value);
				}
			}
		}
	}
}
