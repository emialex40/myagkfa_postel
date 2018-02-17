<?php
/**
 * @package     CSVI
 * @subpackage  JoomlaCategory
 *
 * @author      Roland Dalmulder <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - 2016 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        http://www.csvimproved.com
 */

defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_csvi/models/exports.php';

/**
 * Export Joomla Categories.
 *
 * @package     CSVI
 * @subpackage  JoomlaCategory
 * @since       6.0
 */
class Com_CategoriesModelExportCategory extends CsviModelExports
{
	/**
	 * The custom fields that from other extensions.
	 *
	 * @var    array
	 * @since  6.5.0
	 */
	private $pluginfieldsExport = array();

	/**
	 * Export the data.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	protected function exportBody()
	{
		if (parent::exportBody())
		{
			// Get some basic data
			$this->loadPluginFields();

			// Build something fancy to only get the fieldnames the user wants
			$userfields = array();
			$exportfields = $this->fields->getFields();

			foreach ($exportfields as $field)
			{
				switch ($field->field_name)
				{
					case 'category_path':
						$userfields[] = $this->db->quoteName('c.path');
						break;
					case 'meta_author':
					case 'meta_robots':
						$userfields[] = $this->db->quoteName('c.metadata');
						break;
					case 'category_layout':
					case 'image':
						$userfields[] = $this->db->quoteName('c.params');
						break;
					case 'custom':
						break;
					default:
						// Do not include custom fields into the query
						if (!in_array($field->field_name, $this->pluginfieldsExport))
						{
							$userfields[] = $this->db->quoteName($field->field_name);
						}
						break;
				}
			}

			// Build the query
			$userfields = array_unique($userfields);
			$query = $this->db->getQuery(true);
			$query->select(implode(",\n", $userfields));
			$query->from($this->db->quoteName('#__categories', 'c'));

			// Make sure the ID is always greater than 0 as we don't want to export the root
			$query->where('asset_id > 0');

			// Filter by published state
			$publish_state = $this->template->get('publish_state');

			if ($publish_state != '' && ($publish_state == 1 || $publish_state == 0))
			{
				$query->where($this->db->quoteName('e.published') . ' = ' . (int) $publish_state);
			}

			// Add a limit if user wants us to
			$limits = $this->getExportLimit();

			// Execute the query
			$this->csvidb->setQuery($query, $limits['offset'], $limits['limit']);
			$this->log->add('Export query' . $query->__toString(), false);

			// Check if there are any records
			$logcount = $this->csvidb->getNumRows();

			if ($logcount > 0)
			{
				while ($record = $this->csvidb->getRow())
				{
					$this->log->incrementLinenumber();

					foreach ($exportfields as $field)
					{
						$fieldname = $field->field_name;

						// Set the field value
						if (isset($record->$fieldname))
						{
							$fieldvalue = $record->$fieldname;
						}
						else
						{
							$fieldvalue = '';
						}

						// Process the field
						switch ($fieldname)
						{
							case 'category_path':
								$fieldvalue = $record->path;
								break;
							case 'meta_author':
							case 'meta_robots':
								$metadata = json_decode($record->metadata);

								if (isset($metadata->$fieldname))
								{
									$fieldvalue = $metadata->$fieldname;
								}
								break;
							case 'category_layout':
							case 'image':
								$params = json_decode($record->params);

								if (isset($params->$fieldname))
								{
									$fieldvalue = $params->$fieldname;
								}
								break;
							default:
								if (in_array($fieldname, $this->pluginfieldsExport))
								{
									$fieldvalue = '';

									// Get value from content plugin
									$dispatcher = new RantaiPluginDispatcher;
									$dispatcher->importPlugins('csviext', $this->db);
									$result = $dispatcher->trigger(
										'onExportContent',
										array(
											'extension' => 'joomla',
											'operation' => 'category',
											'id' => $record->id,
											'fieldname' => $fieldname,
											'log' => $this->log
										)
									);

									if (isset($result[0]))
									{
										$fieldvalue = $result[0];
									}
								}
								break;
						}

						// Store the field value
						$this->fields->set($field->csvi_templatefield_id, $fieldvalue);
					}

					// Output the data
					$this->addExportFields();

					// Output the contents
					$this->writeOutput();
				}
			}
			else
			{
				$this->addExportContent('COM_CSVI_NO_DATA_FOUND');

				// Output the contents
				$this->writeOutput();
			}
		}
	}

	/**
	 * Get a list of plugin fields that can be used as available field.
	 *
	 * @return  void.
	 *
	 * @since   6.5.0
	 */
	private function loadPluginFields()
	{
		$dispatcher = new RantaiPluginDispatcher;
		$dispatcher->importPlugins('csviext', $this->db);
		$result = $dispatcher->trigger(
			'getAttributes',
			array(
				'extension' => 'joomla',
				'operation' => 'category',
				'log' => $this->log
			)
		);

		if (is_array($result) && !empty($result))
		{
			$this->pluginfieldsExport = array_merge($this->pluginfieldsExport, $result[0]);
		}
	}
}
