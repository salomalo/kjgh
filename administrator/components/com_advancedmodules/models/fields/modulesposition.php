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

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Form\FormHelper as JFormHelper;

JFormHelper::loadFieldClass('list');

require_once __DIR__ . '/../../helpers/modules.php';

/**
 * ModulesPosition Field class for the Joomla Framework.
 */
class JFormFieldModulesPosition extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  3.4.2
	 */
	protected $type = 'ModulesPosition';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 */
	public function getOptions()
	{
		$clientId = JFactory::getApplication()->input->get('client_id', 0, 'int');
		$options  = ModulesHelper::getPositions($clientId);

		return array_merge(parent::getOptions(), $options);
	}
}
