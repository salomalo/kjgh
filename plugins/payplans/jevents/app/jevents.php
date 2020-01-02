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

require_once(__DIR__ . '/lib.php');

class PPAppJEvents extends PPApp
{
	public function _isApplicable(PPAppTriggerableInterface $refObject, $eventname = '')
	{
		$lib = new PPJEvents();

		return $lib->exists();
	}

	/**
	 * Triggered when a user subscription is saved
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansSubscriptionBeforeSave($prev, $new)
	{
		// no need to trigger if previous and current state is same
		if ($prev != null && $prev->getStatus() == $new->getStatus()) {
			return true;
		}

		$user = $new->getBuyer();

		// if subscription is active
		if ($new->isActive()) {
			$this->helper->addAcl($new, $user);

			return true;
		}
		
		// if subscription is hold
		if ($new->isOnHold()) {
			$this->helper->revoke($new, $user);
			
			return true;
		}

		// if subscription is expired
		if ($new->isExpired()) {
			$this->helper->revoke($new, $user);
			return true;
		}
		
		return true;
	}	
}
