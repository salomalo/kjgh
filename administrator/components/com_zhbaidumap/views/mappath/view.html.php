<?php
/*------------------------------------------------------------------------
# com_zhbaidumap - Zh BaiduMap
# ------------------------------------------------------------------------
# author:    Dmitry Zhuk
# copyright: Copyright (C) 2011 zhuk.cc. All Rights Reserved.
# license:   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
# website:   http://zhuk.cc
# Technical Support Forum: http://forum.zhuk.cc/
-------------------------------------------------------------------------*/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * ZhBaidu MapPath View
 */
class ZhBaiduMapViewMapPath extends JViewLegacy
{
	/**
	 * display method of ZhBaidu MapPath view
	 * @return void
	 */
	public function display($tpl = null) 
	{
		// get the Data
		$form = $this->get('Form');
		$item = $this->get('Item');
		$script = $this->get('Script');
		$mapList = $this->get('mapList');
		$this->assignRef( 'mapList',	$mapList);

		$markerGroupList = $this->get('markerGroupList');
		$this->assignRef( 'markerGroupList',	$markerGroupList);
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		// Assign the Data
		$this->form = $form;
		$this->item = $item;
		$this->script = $script;

		// Set the toolbar
		$this->addToolBar();

		// Map API Key
		$mapapikey = $this->get('APIKey');
		$this->assignRef( 'mapapikey',	$mapapikey );
		$mapapiversion = $this->get('APIVersion');
		$this->assignRef( 'mapapiversion',	$mapapiversion );
		// Map DefLat and DefLng
		$mapDefLat = $this->get('DefLat');
		$this->assignRef( 'mapDefLat',	$mapDefLat );
		$mapDefLng = $this->get('DefLng');
		$this->assignRef( 'mapDefLng',	$mapDefLng );

		$mapMapTypeBaidu = $this->get('MapTypeBaidu');
		$this->assignRef( 'mapMapTypeBaidu',	$mapMapTypeBaidu );
		$mapMapTypeOSM = $this->get('MapTypeOSM');
		$this->assignRef( 'mapMapTypeOSM',	$mapMapTypeOSM );
		$mapMapTypeCustom = $this->get('MapTypeCustom');
		$this->assignRef( 'mapMapTypeCustom',	$mapMapTypeCustom );

		$mapTypeList = $this->get('mapTypeList');
		$this->assignRef( 'mapTypeList',	$mapTypeList);

                $httpsprotocol = $this->get('HttpsProtocol');
		$this->assignRef( 'httpsprotocol',	$httpsprotocol);
                
                $map_height = $this->get('MapHeight');
		$this->assignRef( 'map_height',	$map_height);
                
		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() 
	{
		JRequest::setVar('hidemainmenu', true);
		$user = JFactory::getUser();
		$userId = $user->id;
		$isNew = $this->item->id == 0;
		$canDo = ZhBaiduMapHelper::getPathActions($this->item->id);
		JToolBarHelper::title($isNew ? JText::_('COM_ZHBAIDUMAP_MAPPATH_NEW') : JText::_('COM_ZHBAIDUMAP_MAPPATH_EDIT'), 'mappath');
		// Built the actions for new and existing records.
		if ($isNew) 
		{
			// For new records, check the create permission.
			if ($canDo->get('core.create')) 
			{
				JToolBarHelper::apply('mappath.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('mappath.save', 'JTOOLBAR_SAVE');
				JToolBarHelper::custom('mappath.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
			JToolBarHelper::cancel('mappath.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			if ($canDo->get('core.edit'))
			{
				// We can save the new record
				JToolBarHelper::apply('mappath.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('mappath.save', 'JTOOLBAR_SAVE');

				// We can save this record, but check the create permission to see if we can return to make a new one.
				if ($canDo->get('core.create')) 
				{
					JToolBarHelper::custom('mappath.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
				}
			}
			if ($canDo->get('core.create')) 
			{
				JToolBarHelper::custom('mappath.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
			}
			JToolBarHelper::cancel('mappath.cancel', 'JTOOLBAR_CLOSE');
		}
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$isNew = $this->item->id == 0;
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? JText::_('COM_ZHBAIDUMAP_ADMINISTRATION_MAPPATH_CREATING') : JText::_('COM_ZHBAIDUMAP_ADMINISTRATION_MAPPATH_EDITING'));
		$document->addScript(JURI::root() . $this->script);
		$document->addScript(JURI::root() . "administrator/components/com_zhbaidumap/views/mappath/submitbutton.js");
		JText::script('COM_ZHBAIDUMAP_MAPPATH_ERROR_UNACCEPTABLE');
	}
}
