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

class PayplansControllerSystem extends PayPlansController
{
	/**
	 * Allows user to upgrade payplans
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function upgrade()
	{
		$model = PP::model('System');
		$state = $model->update();

		if ($state === false) {
			$this->info->set($model->getError(), 'error');
			return $this->app->redirect('index.php?option=com_payplans');
		}

		$this->info->set('PayPlans updated to the latest version successfully', 'success');
		return $this->app->redirect('index.php?option=com_payplans');
	}
}
