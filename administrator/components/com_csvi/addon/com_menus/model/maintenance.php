<?php
/**
 * @package     CSVI
 * @subpackage  JoomlaMenus
 *
 * @author      Roland Dalmulder <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - 2016 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        http://www.csvimproved.com
 */

defined('_JEXEC') or die;

/**
 * Joomla! Menus maintenance.
 *
 * @package     CSVI
 * @subpackage  JoomlaMenus
 * @since       6.5.0
 */
class Com_MenusMaintenance
{
	/**
	 * Database connector
	 *
	 * @var    JDatabaseDriver
	 * @since  6.5.0
	 */
	private $db = null;

	/**
	 * Logger helper
	 *
	 * @var    CsviHelperLog
	 * @since  6.5.0
	 */
	private $log = null;

	/**
	 * CSVI Helper.
	 *
	 * @var    CsviHelperCsvi
	 * @since  6.5.0
	 */
	private $csvihelper = null;

	/**
	 * Constructor.
	 *
	 * @param   JDatabase       $db          The database class
	 * @param   CsviHelperLog   $log         The CSVI logger
	 * @param   CsviHelperCsvi  $csvihelper  The CSVI helper
	 *
	 * @since   6.5.0
	 */
	public function __construct($db, $log, $csvihelper)
	{
		$this->db = $db;
		$this->log = $log;
		$this->csvihelper = $csvihelper;
	}

	/**
	 * Update available fields that require extra processing.
	 *
	 * @return  void.
	 *
	 * @since   6.5.0
	 */
	public function updateAvailableFields()
	{
		$fieldnames = array();
		$components = array();

		// Get the list of XML files, these may contain fieldnames
		$files = JFolder::files(JPATH_ROOT . '/components', '.xml', true, true);

		// Loop through the files to see if there are any fields to store
		foreach ($files as $file)
		{
			// Check which extension the XML file belongs to
			$componentFolder = str_replace(JPATH_ROOT . '/components', '', $file);
			$folderParts = explode('/', $componentFolder);

			if (isset($folderParts[1]))
			{
				// Check if the component is installed
				if (!isset($components[$folderParts[1]]))
				{
					$components[$folderParts[1]] = JComponentHelper::isInstalled($folderParts[1]);
				}

				// Only continue if component is installed
				if ($components[$folderParts[1]])
				{
					$form = new JForm('availablefield');

					// Check if we have a real XML file
					$handle = @fopen($file, "r");

					if (fread($handle, 5) == '<?xml')
					{
						if ($form->loadFile($file, true, '/metadata'))
						{
							foreach ($form->getFieldsets() as $fieldset)
							{
								$fields = $form->getFieldset($fieldset->name);

								foreach ($fields as $field)
								{
									$fieldnames[] = str_replace($field->group . '_', '', $field->id);
								}
							}
						}
					}
				}
			}
		}

		if (count($fieldnames) > 0)
		{
			// Start the query
			$query = $this->db->getQuery(true)
				->insert($this->db->quoteName('#__csvi_availablefields'))
				->columns($this->db->quoteName(array('csvi_name', 'component_name', 'component_table', 'component', 'action')));

			$fieldnames = array_unique($fieldnames);

			foreach ($fieldnames as $csvi_name)
			{
				$query->values(
					$this->db->quote($csvi_name) . ',' .
					$this->db->quote($csvi_name) . ',' .
					$this->db->quote('menu') . ',' .
					$this->db->quote('com_menus') . ',' .
					$this->db->quote('import')
				);
				$query->values(
					$this->db->quote($csvi_name) . ',' .
					$this->db->quote($csvi_name) . ',' .
					$this->db->quote('menu') . ',' .
					$this->db->quote('com_menus') . ',' .
					$this->db->quote('export')
				);
			}

			$this->db->setQuery($query)->execute();
		}
	}
}
