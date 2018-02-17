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

/**
 * Templates controller.
 *
 * @package     CSVI
 * @subpackage  Templates
 * @since       6.0
 */
class CsviControllerTemplates extends FOFController
{
	/**
	 * Redirect the user to the template fields view to manage the template fields.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   6.0
	 */
	public function templateFields()
	{
		$post = $this->input->get('cid', array(), 'array');

		if (isset($post[0]))
		{
			$this->setRedirect('index.php?option=com_csvi&view=templatefields&csvi_template_id=' . $post[0]);
		}
		else
		{
			$this->setRedirect('index.php?option=com_csvi&view=templates', JText::_('COM_CSVI_NO_TEMPLATE_SELECTED'));
		}
	}

	/**
	 * Duplicate a template.
	 *
	 * @return  void.
	 *
	 * @since   6.0
	 */
	public function save2copy()
	{
		$model = $this->getThisModel();

		try
		{
			$model->createCopy($this->input->get('cid', array(), 'array'));

			$this->setRedirect('index.php?option=com_csvi&view=templates', JText::_('COM_CSVI_TEMPLATE_COPIED'));
		}
		catch (Exception $e)
		{
			$this->setRedirect('index.php?option=com_csvi&view=templates', $e->getMessage(), 'error');
		}
	}

	/**
	 * Handle JSON requests.
	 *
	 * @return  string  JSON encoded string.
	 *
	 * @since   6.0
	 */
	public function jsonData()
	{
		$model = $this->getThisModel();

		$addon = $this->input->getString('addon', false);
		$method = $this->input->getString('method', false);
		$term = $this->input->getString('term', false);
		$language = $this->input->getInt('language_id', false);
		$results = array();

		if ($addon && $method)
		{
			$args = array('filter_name' => $term, 'language_id' => $language);

			$results = $model->loadJsonData($addon, $method, $args);
		}

		echo json_encode($results);

		JFactory::getApplication()->close();
	}

	/**
	 * Test the FTP connection details.
	 *
	 * @return  string  JSON encoded string.
	 *
	 * @since   4.3.2
	 */
	public function testFtp()
	{
		$model = $this->getThisModel();
		$result = array();

		if ($model->testFtp())
		{
			$result['message'] = JText::_('COM_CSVI_FTP_TEST_SUCCESS');
		}
		else
		{
			$result['message'] = JText::sprintf('COM_CSVI_FTP_TEST_NO_SUCCESS', "\n" . $model->getError());
		}

		echo json_encode($result);

		JFactory::getApplication()->close();
	}

	/**
	 * Delete the templates selected.
	 *
	 * @return  void.
	 *
	 * @since   6.5.0
	 */
	public function remove()
	{
		$deletedIds = array();
		$model = $this->getThisModel();

		try
		{
			$model->delete();
			$deletedIds = $model->getIds();

		}
		catch (Exception $e)
		{
			$this->setRedirect('index.php?option=com_csvi&view=templates', $e->getMessage(), 'error');
		}

		$this->setRedirect('index.php?option=com_csvi&view=templates', JText::plural('COM_CSVI_TEMPLATES_N_ITEMS_DELETED', count($deletedIds)));
	}

	/**
	 * Handle the wizard steps.
	 *
	 * @return  void.
	 *
	 * @since   6.5.0
	 */
	public function wizard()
	{
		$step = JFactory::getApplication()->input->getInt('step', 1);

		switch ($step)
		{
			case 1:
				// This step doesn't do anything as it is the first step in the process.
				break;
			default:
				if ($this->apply())
				{
					$id = $this->input->get('id', 0, 'int');
					$textkey = strtoupper($this->component) . '_LBL_' . strtoupper($this->view) . '_SAVED';

					$url = 'index.php?option=' . $this->component . '&view=' . $this->view . '&task=edit&step=' . $step . '&id=' . $id  . $this->getItemidURLSuffix();
					$this->setRedirect($url, JText::_($textkey));
				}
				break;
		}
	}

	/**
	 * Test if the URL is valid.
	 *
	 * @return  string  JSON encoded string.
	 *
	 * @since   6.5.0
	 */
	public function testURL()
	{
		$model = $this->getThisModel();
		$result = array();
		$result['message'] = JText::_('COM_CSVI_URL_TEST_NO_SUCCESS');

		if ($model->testURL())
		{
			$result['message'] = JText::_('COM_CSVI_URL_TEST_SUCCESS');
		}

		echo json_encode($result);

		JFactory::getApplication()->close();
	}

	/**
	 * Test if the server path is valid.
	 *
	 * @return  string  JSON encoded string.
	 *
	 * @since   6.5.0
	 */
	public function testPath()
	{
		$model = $this->getThisModel();
		$result = array();
		$result['message'] = JText::_('COM_CSVI_PATH_TEST_NO_SUCCESS');

		if ($model->testPath())
		{
			$result['message'] = JText::_('COM_CSVI_PATH_TEST_SUCCESS');
		}

		echo json_encode($result);

		JFactory::getApplication()->close();
	}
}
