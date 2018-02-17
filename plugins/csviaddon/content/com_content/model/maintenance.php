<?php
/**
 * @package     CSVI
 * @subpackage  JoomlaContent
 *
 * @author      Roland Dalmulder <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - 2016 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        http://www.csvimproved.com
 */

defined('_JEXEC') or die;

/**
 * Content maintenance.
 *
 * @package     CSVI
 * @subpackage  JoomlaContent
 * @since       6.5.0
 */
class Com_ContentMaintenance
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
	 * Update Custom available fields that require extra processing.
	 *
	 * @return  void.
	 *
	 * @since   6.5.0
	 */
	public function customAvailableFields()
	{
		$query = $this->db->getQuery(true)
			->select($this->db->quoteName('extension_id'))
			->from($this->db->quoteName('#__extensions'))
			->where($this->db->quoteName('name') . ' = ' . $this->db->quote('plg_content_swmap'))
			->where($this->db->quoteName('type') . ' = ' . $this->db->quote('plugin'))
			->where($this->db->quoteName('folder') . ' = ' . $this->db->quote('content'));
		$this->db->setQuery($query);

		$extension_id = $this->db->loadResult();

		// Insert fields only when the plugin is installed
		if ($extension_id)
		{
			// Start the query
			$query->clear()
				->insert($this->db->quoteName('#__csvi_availablefields'))
				->columns($this->db->quoteName(array('csvi_name', 'component_name', 'component_table', 'component', 'action')));

			$fields = array
						(
							'address',
							'latitude',
							'lognitude',
							'type',
							'icon',
							'theicon',
							'zoom',
							'panoramioitem',
							'adsenseitem',
							'publisher',
							'formatitem',
							'positionitem'
						);

			foreach ($fields as $field)
			{
				$query->values(
					$this->db->quote($field) . ',' .
					$this->db->quote($field) . ',' .
					$this->db->quote('content') . ',' .
					$this->db->quote('com_content') . ',' .
					$this->db->quote('import')
				);
				$query->values(
					$this->db->quote($field) . ',' .
					$this->db->quote($field) . ',' .
					$this->db->quote('content') . ',' .
					$this->db->quote('com_content') . ',' .
					$this->db->quote('export')
				);
			}

			$this->db->setQuery($query)->execute();
		}
	}
}
