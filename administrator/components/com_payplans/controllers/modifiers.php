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

class PayplansControllerModifiers extends PayPlansController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('plans');

		$this->registerTask('save', 'save');
		$this->registerTask('savenew', 'save');
		$this->registerTask('apply', 'save');

		$this->registerTask('close', 'cancel');

		$this->registerTask('publish', 'togglePublish');
		$this->registerTask('unpublish', 'togglePublish');
	}

	/**
	 * Delete a list of plan modifiers instance from the site
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function delete()
	{
		$ids = $this->input->get('cid', 0, 'int');

		foreach ($ids as $id) {
			$app = PP::app((int) $id);
			$app->delete();
		}

		$this->info->set('COM_PP_MODIFIER_DELETED_SUCCESS');
		return $this->redirectToView('modifiers');
	}

	/**
	 * Cancel process
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function cancel()
	{
		return $this->app->redirect('index.php?option=com_payplans&view=modifiers');
	}

	/**
	 * Allow caller to toggle published state
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function togglePublish()
	{

		$ids = $this->input->get('cid', 0, 'int');

		$task = $this->getTask();

		foreach ($ids as $id) {
			$table = PP::table('App');
			$table->load($id);

			$table->$task();
		}

		$message = $task == 'publish' ? 'COM_PP_ITEM_PUBLISHED_SUCCESSFULLY' : 'COM_PP_ITEM_UNPUBLISHED_SUCCESSFULLY';

		$this->info->set($message);
		return $this->redirectToView('modifiers');
	}

	/**
	 * Saves a modifier
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function save()
	{
		$id = $this->input->get('app_id', 0, 'int');
		$data = $this->input->post->getArray();

		if (empty($data['title'])) {
			$this->info->set('COM_PP_TITLE_REQUIRED', 'error');
			return $this->redirectToView('modifiers', 'form');
		}

		$app = PP::app($id);
		$app->bind($data);
		$app->type = 'planmodifier';
		$app->group = 'core';

		// Insert the core params
		$coreParams = new JRegistry($data['core_params']);
		$app->setCoreParams($data['core_params']);

		// Insert the app params
		$data['app_params']['time_price'] = serialize($data['app_params']['time_price']);
		$appParams = $app->collectAppParams($data);
		$app->setAppParams($appParams);

		// Save the app
		$state = $app->save();

		$message = 'COM_PP_MODIFIER_CREATED_SUCCESS';

		if ($state === false) {
			$this->info->set('COM_PP_MODIFIER_SAVED_FAILED', 'error');

			return $this->redirectToView('modifiers', 'form');
		}

		if ($id) {
			$message = 'COM_PP_MODIFIER_SAVED_SUCCESS';
		}

		$this->info->set($message, 'success');

		$task = $this->getTask();

		if ($task == 'apply') {
			return $this->redirectToView('modifiers', 'form', 'id=' . $app->getId());
		}

		if ($task == 'savenew') {
			return $this->redirectToView('modifiers', 'form');
		}

		return $this->redirectToView('modifiers');
	}

}
