<?php
/**
 * ------------------------------------------------------------------------
 * JA Megafilter Component
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2016 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class JaMegaFilterModelDefault extends JModelAdmin
{
	public function getTable($type = 'JaMegaFilter', $prefix = 'JaMegaFilterTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true)
	{
		$jinput = JFactory::getApplication()->input;
		$type = $jinput->get('type', 0);
		$item = $this->getItem();
		if (empty($type)) $type = $item->type;
		if (!empty($type)) {
			$lang = JFactory::getLanguage();
			$extension = 'plg_jamegafilter_'.$type;
			$language_tag = JFactory::getLanguage()->getTag();
			$lang->load($extension, JPATH_ADMINISTRATOR, $language_tag, true);
			$xml = JPATH_PLUGINS.'/jamegafilter/'.$type.'/forms/'.$type.'.xml';
			if(JFile::exists($xml)){
				// get form from third party
				JForm::addFieldPath(JPATH_PLUGINS.'/jamegafilter/'.$type.'/fields/');
				$options = array('control' => 'jform', 'load_data' => $loadData);
				$form = JForm::getInstance('jform', $xml, $options);
			} 
		}
		if (empty($form))
		{
			return false;
		}
		return $form;
	}
	
	function saveobj()
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$post = $jinput->get('jform', array(), 'array');
		$object = $this->getTable();
		$object->type = $post['jatype'];
		$object->published = $post['published'];
		$object->title = $post['title'];
		$object->params = json_encode($post);
		if (!empty($post['id']))
			$object->id = $post['id'];
		$object->store();
		if (!empty($object->id)) {
			
			$this->exportByID($object->id);
		}
		return $object;
	}
	
	function exportByID($id)
	{
		$app = JFactory::getApplication();
		$item = $this->getItem($id);
		$isEndable = JPluginHelper::isEnabled('jamegafilter', $item->type);
		if (!$isEndable) {
			$app->enqueueMessage(JTEXT::_('COM_JAMEGAFILTER_EXPORT_FAILED_FILTER_PLUGIN_NOT_FOUND').' : '.strtoupper($item->type), 'error');
			return false;
		}
		JPluginHelper::importPlugin('jamegafilter');
		$dispatcher = JEventDispatcher::getInstance();
		$path = JPATH_SITE.'/media/com_jamegafilter/';
		if(!JFolder::exists($path)) {
			JFolder::create($path, 0755);
		}
		
		$result = $dispatcher->trigger('onAfterSave'.ucfirst($item->type).'Items', array( $item ) );
		$objectList = $result[0];

		foreach ($objectList as $key => $object) 
		{
			$json = json_encode($object);
			if (!JFile::write($path.$key.'/'.$id.'.json', $json)) {
				$app->enqueueMessage(JTEXT::_('COM_JAMEGAFILTER_CAN_NOT_EXPORT_JSON_TO_FILE').':'.$path.$key.'/'.$id.'.json', 'error');
				return false;
			}
		}
		return true;
	}
}