<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2019 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class EventbookingViewSearchHtml extends RADViewHtml
{
	protected function prepareView()
	{
		parent::prepareView();

		$document   = JFactory::getDocument();
		$model      = $this->getModel();
		$items      = $model->getData();
		$pagination = $model->getPagination();
		$document->setTitle(JText::_('EB_SEARCH_RESULT'));
		$config = EventbookingHelper::getConfig();

		if ($config->multiple_booking)
		{
			if ($this->deviceType == 'mobile')
			{
				EventbookingHelperJquery::colorbox('eb-colorbox-addcart', '100%', '450px', 'false', 'false');
			}
			else
			{
				EventbookingHelperJquery::colorbox('eb-colorbox-addcart', '800px', 'false', 'false', 'false', 'false');
			}
		}

		if ($config->show_list_of_registrants)
		{
			EventbookingHelperJquery::colorbox('eb-colorbox-register-lists');
		}

		if ($config->show_location_in_category_view)
		{
			EventbookingHelperJquery::loadColorboxForMap();
		}

		$this->viewLevels      = JFactory::getUser()->getAuthorisedViewLevels();
		$this->items           = $items;
		$this->pagination      = $pagination;
		$this->config          = $config;
		$this->nullDate        = JFactory::getDbo()->getNullDate();
		$this->bootstrapHelper = EventbookingHelperBootstrap::getInstance();
	}
}
