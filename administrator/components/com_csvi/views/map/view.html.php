<?php
/**
 * @package     CSVI
 * @subpackage  Fieldmapper
 *
 * @author      Roland Dalmulder <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - 2016 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        http://www.csvimproved.com
 */

defined('_JEXEC') or die;

/**
 * Field mapper edit screen.
 *
 * @package     CSVI
 * @subpackage  Fieldmapper
 * @since       6.0
 */
class CsviViewMap extends FOFViewHtml
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
	 * The form with fields
	 *
	 * @var    FOFForm
	 * @since  6.0
	 */
	protected $form;

	/**
	 * Executes before rendering the page for the Add task.
	 *
	 * @param   string  $tpl  Subtemplate to use
	 *
	 * @return  boolean  Return true to allow rendering of the page
	 *
	 * @since   6.0
	 */
	protected function onAdd($tpl = null)
	{
		// Let the parent get the setting details
		parent::onAdd($tpl);

		// Load the extra help settings
		$db = JFactory::getDbo();
		$settings = new CsviHelperSettings($db);
		$this->extraHelp = $settings->get('extraHelp');

		// Load the helper
		$helper = new CsviHelperCsvi;
		$this->components = $helper->getComponents();

		$this->form = FOFForm::getInstance('map', 'map');
		$this->form->bind(array('jform' => $this->item->getData()));

		// Display it all
		return true;
	}
}
