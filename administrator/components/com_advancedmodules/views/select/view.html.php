<?php
/**
 * @package         Advanced Module Manager
 * @version         7.12.3PRO
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2019 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Layout\FileLayout as JLayoutFile;
use Joomla\CMS\MVC\View\HtmlView as JView;
use Joomla\CMS\Toolbar\Toolbar as JToolbar;

/**
 * HTML View class for the Modules component
 */
class AdvancedModulesViewSelect extends JView
{
	protected $state;

	protected $items;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$state = $this->get('State');
		$items = $this->get('Items');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		$this->state = &$state;
		$this->items = &$items;

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		// Add page title
		JToolbarHelper::title(JText::_('COM_MODULES_MANAGER_MODULES'), 'advancedmodulemanager icon-reglab');

		// Get the toolbar object instance
		$bar = JToolbar::getInstance('toolbar');

		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new JLayoutFile('toolbar.cancelselect');

		$bar->appendButton('Custom', $layout->render([]), 'new');
	}
}
