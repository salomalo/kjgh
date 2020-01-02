<?php
/**
* @package      PayPlans
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class PPEasyDiscuss
{
	protected $file = JPATH_ROOT . '/administrator/components/com_easydiscuss/includes/easydiscuss.php';

	/**
	 * Determines if EasyBlog exists on the site
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function exists()
	{
		static $exists = null;

		if (is_null($exists)) {
			$enabled = JComponentHelper::isEnabled('com_easydiscuss');
			$fileExists = JFile::exists($this->file);
			$exists = false;

			if ($enabled && $fileExists) {
				$exists = true;
				require_once($this->file);
			}
		}

		return $exists;
	}

	/**
	 * Retrieves a list of EasyDiscuss categories on the site
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getCategories()
	{
		$db = PP::db();
		$query = 'SELECT * FROM `#__discuss_category` WHERE `published` = 1';
		$db->setQuery($query);
		$categories = $db->loadObjectList();

		return $categories;
	}

	/**
	 * Retrieves a list of ACL rules in EasyDiscuss
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getAclRules()
	{
		$db = PP::db();
		$query = 'SELECT * FROM `#__discuss_category_acl_item` WHERE `published`=1';

		$db->setQuery($query);
		$rules = $db->loadObjectList();

		return $rules;
	}

	/**
	 * Retrieves a list of badges from EasyDiscuss
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getBadges()
	{
		$db = PP::db();
		$query = 'SELECT * FROM `#__discuss_badges` WHERE `published`=1';
		$db->setQuery($query);
		
		return $db->loadObjectList();
	}
}
