<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class DiscussionsViewGroups extends SocialAppsView
{
	public function display($groupId = null, $docType = null)
	{
		$group = ES::group($groupId);

		// Check if the viewer is allowed here.
		if (!$group->canViewItem()) {
			return $this->redirect($group->getPermalink(false));
		}

		$access = $group->getAccess();

		if (!$access->get('discussions.enabled', true)) {
			return false;
		}

		// Get app params
		$params = $this->app->getParams();

		$this->setTitle('APP_DISCUSSIONS_APP_TITLE');

		$model = ES::model('Discussions');
		$options = array('limit' => $params->get('total', 10));

		$counters = $model->getCounters($group);

		$discussions = $model->getDiscussions($group->id , SOCIAL_TYPE_GROUP , $options);
		$pagination = $model->getPagination();
		$pagination->setVar('option' , 'com_easysocial');
		$pagination->setVar('view' , 'groups');
		$pagination->setVar('layout' , 'item');
		$pagination->setVar('id' , $group->getAlias());
		$pagination->setVar('appId' , $this->app->getAlias());

		$helper = ES::viewHelper('Discussions', 'List');
		$filterLinks = $helper->getFilterLinks();
		$showCreateButton = $helper->showCreateButton();
		$createButtonLink = $helper->getCreateButtonLink();
		$filter = $helper->getCurrentFilter();
		$counters = $helper->getCounters();

		$theme = ES::themes();
		$theme->set('counters', $counters);
		$theme->set('app', $this->app);
		$theme->set('params', $params);
		$theme->set('pagination', $pagination);
		$theme->set('cluster', $group);
		$theme->set('discussions', $discussions);
		$theme->set('createButtonLink', $createButtonLink);
		$theme->set('filterLinks', $filterLinks);
		$theme->set('showCreateButton', $showCreateButton);
		$theme->set('filter', $filter);

		echo $theme->output('site/discussions/default/default');
	}

	public function sidebar($moduleLib, $cluster)
	{
		$helper = ES::viewHelper('Discussions', 'List');

		$filter = $helper->getCurrentFilter();
		$filters = $helper->getFilterLinks();
		$createButtonLink = $helper->getCreateButtonLink();
		$showCreateButton = $helper->showCreateButton();
		$counters = $helper->getCounters();

		$theme = ES::themes();

		$theme->set('moduleLib', $moduleLib);
		$theme->set('cluster', $cluster);
		$theme->set('createButtonLink', $createButtonLink);
		$theme->set('showCreateButton', $showCreateButton);
		$theme->set('filter', $filter);
		$theme->set('filters', $filters);
		$theme->set('counters', $counters);

		echo $theme->output('site/discussions/sidebar/default');
	}

}
