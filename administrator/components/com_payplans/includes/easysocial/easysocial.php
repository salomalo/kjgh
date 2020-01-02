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

class PPEasysocial
{
	protected $file = JPATH_ROOT . '/administrator/components/com_easysocial/includes/easysocial.php';

	/**
	 * Determines if Easysocial exists
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function exists()
	{
		$enabled = JComponentHelper::isEnabled('com_easysocial');
		$exists = JFile::exists($this->file);

		if (!$exists || !$enabled) {
			return false;
		}

		require_once($this->file);

		return true;
	}

	/**
	 * Retrieves a list of profile type
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getProfileTypes()
	{
		static $profileTypes = null;

		if (is_null($profileTypes)) {
			$db = PP::db();
			$query = 'SELECT * FROM ' . $db->qn('#__social_profiles') . ' WHERE ' . $db->qn('state') . '=' . $db->Quote(1);
			$db->setQuery($query);

			$profileTypes = $db->loadObjectList();
		}

		return $profileTypes;

	}

	/**
	 * Retrieves a list of badges
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getBadges()
	{
		static $badges = null;

		if (is_null($badges)) {
			$db = PP::db();
			$query = 'SELECT * FROM ' . $db->qn('#__social_badges') . ' WHERE ' . $db->qn('state') . '=' . $db->Quote(1);
			$db->setQuery($query);

			$badges = $db->loadObjectList();
		}

		return $badges;
	}
}
