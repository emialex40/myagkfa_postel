<?php
/**
 * @package     CSVI
 * @subpackage  View
 *
 * @author      Roland Dalmulder <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - 2016 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        http://www.csvimproved.com
 */

defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_csvi/views/default/view.html.php';

/**
 * Maintenance view.
 *
 * @package     CSVI
 * @subpackage  View
 * @since       6.0
 */
class CsviViewMaintenance extends CsviViewDefault
{
	/**
	 * Show the extra help
	 *
	 * @var    int
	 * @since  6.5.0
	 */
	protected $extraHelp;

	/**
	 * List of supported components
	 *
	 * @var    array
	 * @since  6.0
	 */
	protected $components;

	/**
	 * Array of options for the component
	 *
	 * @var    array
	 * @since  6.0
	 */
	protected $options = array();

	/**
	 * Display the maintenance screen.
	 *
	 * @param   string  $tpl  The template to use.
	 *
	 * @return  bool True on success | False on failure
	 *
	 * @since   3.0
	 */
	public function onDetail($tpl = null)
	{
		// Load the extra help settings
		$db = JFactory::getDbo();
		$settings = new CsviHelperSettings($db);
		$this->extraHelp = $settings->get('extraHelp');

		/** @var CsviModelMaintenances $model */
		$model = $this->getModel();

		// Get the component list
		$this->components = $model->getComponents();

		// Get the maintenance options
		$component = strtolower($this->input->get('component'));

		if (!empty($component))
		{
			$this->options = $model->getOperations($component);
		}

		return true;
	}
}
