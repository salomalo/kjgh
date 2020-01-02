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

PP::import('admin:/tables/table');

class PayplansTableSubscription extends PayplansTable
{
	public $subscription_id = null;
	public $order_id = null;
	public $user_id = null;
	public $plan_id = null;
	public $status = null;
	public $total = null;
	public $subscription_date = null;
	public $expiration_date = null;
	public $cancel_date = null;
	public $checked_out = null;
	public $checked_out_time = null;
	public $modified_date = null;
	public $params = null;

	public function __construct($db)
	{
		parent::__construct('#__payplans_subscription', 'subscription_id', $db);
	}
}