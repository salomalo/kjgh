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

jimport('joomla.filesystem.file');

class PPPaywithpointsLibAbstract
{
	protected $user = null;

	public function __construct(PPUser $user)
	{
		$this->user = $user;
	}

	/**
	 * Determines if a component and file exists
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function exists($component)
	{
		$exists = JFile::exists($this->file);
		$enabled = JComponentHelper::isEnabled($component);

		if (!$exists || !$enabled) {
			return false;
		}

		require_once($this->file);
		return true;
	}
}