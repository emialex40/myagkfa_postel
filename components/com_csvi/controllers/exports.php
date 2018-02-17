<?php
/**
 * @package     CSVI
 * @subpackage  Export
 *
 * @author      Roland Dalmulder <contact@csvimproved.com>
 * @copyright   Copyright (C) 2006 - 2016 RolandD Cyber Produksi. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        http://www.csvimproved.com
 */

defined('_JEXEC') or die;

/**
 * Export controller.
 *
 * @package     CSVI
 * @subpackage  Export
 * @since       6.0
 */
class CsviControllerExports extends CsviControllerDefault
{
	/**
	 * Export for front-end.
	 *
	 * @return  void.
	 *
	 * @since   3.0
	 */
	public function export()
	{
		// Get the template ID
		$templateId = $this->input->get('csvi_template_id', false);

		if ($templateId)
		{
			// Load the model
			/** @var CsviModelExports $model */
			$model = $this->getThisModel();

			// Initialise
			$model->initialise($templateId);

			// Get the run ID
			$runId = $model->getRunId();

			try
			{
				if ($runId)
				{
					if ($templateId)
					{
						$model->loadTemplate($templateId);

						// Load the template
						$template = $model->getTemplate();

						// Get the component and operation
						$component = $template->get('component');
						$operation = $template->get('operation');

						if ($component && $operation)
						{
							if ($template->getEnabled())
							{
								if ($template->getFrontend())
								{
									$secret = $template->getSecret();

									if (!empty($secret))
									{
										// Check if the secret key matches
										$key = $this->input->get('key', '', 'string');

										if ($key == $secret)
										{
											// Set any template details found in the request
											foreach ($_GET as $name => $var)
											{
												if ($position = stripos($name, 'form_') !== false)
												{
													// Option name
													$option = str_ireplace('form_', '', $name);

													// Get the value
													$value = $this->input->getString($name, $template->get($name));

													if (strlen($value) > 0 )
													{
														// Check for multiple values
														if (strpos($value, '|'))
														{
															$value = explode('|', $value);
														}

														// Set the template option name
														$template->set($option, $value);
													}
												}
											}

											// Setup the component autoloader
											JLoader::registerPrefix(ucfirst($component), JPATH_ADMINISTRATOR . '/components/com_csvi/addon/' . $component);

											// Load the export routine
											$classname = ucwords($component) . 'ModelExport' . ucwords($operation);
											$routine = new $classname;

											// Prepare for export
											$routine->initialiseExport($runId);
											$routine->onBeforeExport($component);

											// Inject our own template instance because we could have modified the settings
											$routine->setTemplate($template);

											if (0)
											{
												// Set the override for the operation model if exists
												$overridefile = JPATH_COMPONENT_ADMINISTRATOR . '/addon/' . $component . '/override/export/' . $operation . '.php';

												if (file_exists($overridefile))
												{
													$this->addModelPath(JPATH_COMPONENT_ADMINISTRATOR . '/addon/' . $component . '/override/export/');
												}
												else
												{
													$this->addModelPath(JPATH_COMPONENT_ADMINISTRATOR . '/addon/' . $component . '/model/export');
												}
											}

											// Start the export
											if ($routine->runExport())
											{
												// Get the export location from the URL
												$exportTo = $this->input->get('exportto', 'tofront');

												// Load the destinations
												$destinations = array($exportTo);

												if ($exportTo === 'template')
												{
													// Get output destinations from the template
													$destinations = $template->get('exportto', 'tofront');

													if (!is_array($destinations))
													{
														$destinations = array($destinations);
													}
												}

												// Load the name of the file to process
												$processfile = $routine->getProcessfile();

												// Check output destinations
												foreach ($destinations as $destination)
												{
													switch ($destination)
													{
														case 'tofile':
															$model->writeFile($processfile);
															break;
														case 'toftp':
															$model->ftpFile($processfile);
															break;
														case 'toemail':
															$model->emailFile($processfile);
															break;
														case 'todownload':
															$model->downloadFile($processfile);
															break;
														case 'tofront':
														default:
															$model->displayFile($processfile);
															break;
													}
												}

												// Remove the temporary file if needed
												JFile::delete($processfile);

												JFactory::getApplication()->close();
											}
											else
											{
												throw new CsviException(JText::_('COM_CSVI_EXPORT_RUN_FAILED'), 500);
											}
										}
										else
										{
											throw new CsviException(JText::sprintf('COM_CSVI_SECRET_KEY_DOES_NOT_MATCH', $key), 518);
										}
									}
									else
									{
										throw new CsviException(JText::_('COM_CSVI_SECRET_KEY_EMPTY'), 517);
									}
								}
								else
								{
									throw new CsviException(JText::_('COM_CSVI_EXPORT_TEMPLTE_FRONTEND_NOT_ALLOWED'), 516);
								}
							}
							else
							{
								throw new CsviException(JText::_('COM_CSVI_EXPORT_TEMPLATE_NOT_ENABLED'), 515);
							}
						}
						else
						{
							throw new CsviException(JText::_('COM_CSVI_EXPORT_NO_COMPONENT_NO_OPERATION'), 514);
						}
					}
					else
					{
						throw new CsviException(JText::_('COM_CSVI_NO_TEMPLATEID_FOUND'), 509);
					}
				}
				else
				{
					throw new CsviException(JText::_('COM_CSVI_NO_VALID_RUNID_FOUND'), 506);
				}
			}
			catch (Exception $e)
			{
				// Finalize the export
				$model = $this->getThisModel();
				$model->setEndTimestamp($runId);

				// Redirect to the template view
				$this->setRedirect('index.php', $e->getMessage(), 'error');
				$this->redirect();
			}
		}
		else
		{
			// Redirect to the template view
			$this->setRedirect('index.php', JText::_('COM_CSVI_NO_TEMPLATE_ID_FOUND'), 'error');
			$this->redirect();
		}
	}
}
