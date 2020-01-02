<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class PPPhoca
{
	protected $file = JPATH_ROOT . '/components/com_phocadownload/phocadownload.php';
	
	/**
	 * Determines if phocadownload exists
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function exists()
	{
		$enabled = JComponentHelper::isEnabled('com_phocadownload');
		$exists = JFile::exists($this->file);

		if (!$exists || !$enabled) {
			return false;
		}

		return true;
	}

	/**
	 * Retrieves a list of categories from Phoca Download
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getCategories()
	{
		static $categories = null;
		
		if (!empty($categories)) {
			return $categories;	
		}
		
		$db = JFactory::getDBO();
		$query = 'SELECT id, title, description, parent_id FROM ' . $db->qn('#__phocadownload_categories');

		$db->setQuery($query);
		$categories = $db->loadObjectList('id');

		return $categories; 
	}
}