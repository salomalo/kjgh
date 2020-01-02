<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2019 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class EventbookingViewEventHtml extends RADViewHtml
{
	use EventbookingViewEvent, EventbookingViewCaptcha;

	/**
	 * Event Data
	 *
	 * @var \stdClass
	 */
	protected $item;

	/**
	 * Model state
	 *
	 * @var RADModelState
	 */
	protected $state;

	/**
	 * Children events of the current event
	 *
	 * @var array
	 */
	protected $items;

	/**
	 * Component config
	 *
	 * @var RADConfig
	 */
	protected $config;

	/**
	 * ID of current user
	 *
	 * @var int
	 */
	protected $userId;

	/**
	 * The access levels of the current user
	 *
	 * @var array
	 */
	protected $viewLevels;

	/**
	 * The value represent database null date
	 *
	 * @var string
	 */
	protected $nullDate;

	/**
	 * Render event view
	 *
	 * @return void
	 * @throws Exception
	 */
	public function display()
	{
		if (in_array($this->getLayout(), ['form', 'simple']))
		{
			$this->displayForm();

			return;
		}

		if (!$this->input->getInt('hmvc_call'))
		{
			$this->setLayout('default');
		}

		$user   = JFactory::getUser();
		$config = EventbookingHelper::getConfig();

		/* @var EventbookingModelEvent $model */
		$model = $this->getModel();
		$item  = $model->getEventData();

		// Check to make sure the event is valid and user is allowed to access to it
		if (empty($item))
		{
			throw new \Exception(JText::_('EB_EVENT_NOT_FOUND'), 404);
		}

		if (!$item->published && !$user->authorise('core.admin', 'com_eventbooking') && $item->created_by != $user->id)
		{
			throw new \Exception(JText::_('EB_EVENT_NOT_FOUND'), 404);
		}

		if (!in_array($item->access, $user->getAuthorisedViewLevels()))
		{
			if (!$user->id)
			{
				$return = base64_encode(JUri::getInstance()->toString());
				JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_users&view=login&return=' . $return));
			}
			else
			{
				throw new \Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
			}
		}

		EventbookingHelper::callOverridableHelperMethod('Html', 'antiXSS', [$item, ['title', 'price_text']]);

		// Update Hits
		$model->updateHits($item->id);

		//Use short description in case user don't enter long description
		if (!EventbookingHelper::isValidMessage($item->description))
		{
			$item->description = $item->short_description;
		}

		if ($item->location_id)
		{
			$this->location = $item->location;
		}

		if ($item->event_type == 1 && $config->show_children_events_under_parent_event)
		{
			$this->items = EventbookingModelEvent::getAllChildrenEvents($item->id);
		}

		if (isset($item->paramData))
		{
			$this->paramData = $item->paramData;
		}

		if ($this->input->get('tmpl', '') == 'component')
		{
			$this->showTaskBar = false;
		}
		else
		{
			$this->showTaskBar = true;
		}

		JPluginHelper::importPlugin('eventbooking');
		$plugins = JFactory::getApplication()->triggerEvent('onEventDisplay', array($item));

		$horizontalPlugins = [];
		$tabbedPlugins     = [];

		foreach ($plugins as $plugin)
		{
			if (!is_array($plugin) || empty($plugin['form']))
			{
				continue;
			}

			if (isset($plugin['position']) && $plugin['position'] === 'before_register_buttons')
			{
				$horizontalPlugins[] = $plugin;
			}
			else
			{
				$tabbedPlugins[] = $plugin;
			}
		}

		if (empty(EventbookingHelperRoute::$eventsAlias))
		{
			if ($config->insert_event_id)
			{
				EventbookingHelperRoute::$eventsAlias[$item->id] = $item->id . '-' . $item->alias;
			}
			else
			{
				EventbookingHelperRoute::$eventsAlias[$item->id] = $item->alias;
			}

			EventbookingHelperRoute::$locationsAlias[$item->location_id] = $item->location_alias;
		}

		$this->viewLevels        = $user->getAuthorisedViewLevels();
		$this->item              = $item;
		$this->state             = $model->getState();
		$this->config            = $config;
		$this->userId            = $user->id;
		$this->nullDate          = JFactory::getDbo()->getNullDate();
		$this->plugins           = $tabbedPlugins;
		$this->horizontalPlugins = $horizontalPlugins;
		$this->rowGroupRates     = EventbookingHelperDatabase::getGroupRegistrationRates($item->id);
		$this->bootstrapHelper   = EventbookingHelperBootstrap::getInstance();
		$this->print             = $this->input->getInt('print', 0);

		// Prepare document meta data
		$this->prepareDocument();

		parent::display();
	}

	/**
	 * Method to prepare document before it is rendered
	 *
	 * @return void
	 */
	protected function prepareDocument()
	{
		$this->params = $this->getParams();

		// Process page meta data
		if (!$this->params->get('page_title'))
		{
			if ($this->item->page_title)
			{
				$pageTitle = $this->item->page_title;
			}
			else
			{
				$pageTitle = JText::_('EB_EVENT_PAGE_TITLE');
				$pageTitle = str_replace('[EVENT_TITLE]', $this->item->title, $pageTitle);
				$pageTitle = str_replace('[CATEGORY_NAME]', $this->item->category_name, $pageTitle);
			}

			$this->params->set('page_title', $pageTitle);
		}

		$this->params->def('page_heading', $this->item->title);

		$this->params->def('menu-meta_keywords', $this->item->meta_keywords);

		$this->params->def('menu-meta_description', $this->item->meta_description);

		// Load document assets
		$this->loadAssets();

		// Build document pathway
		$this->buildPathway();

		// Set page meta data
		$this->setDocumentMetadata();
	}

	/**
	 * Load assets (javascript/css) for this specific view
	 *
	 * @return void
	 */
	protected function loadAssets()
	{
		if ($this->config->multiple_booking)
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

		if ($this->config->show_list_of_registrants)
		{
			EventbookingHelperJquery::colorbox('eb-colorbox-register-lists');
		}

		EventbookingHelperJquery::loadColorboxForMap();

		if ($this->config->show_invite_friend)
		{
			EventbookingHelperJquery::colorbox('eb-colorbox-invite');
		}

		EventbookingHelperJquery::colorbox('a.eb-modal');
	}

	/**
	 * Method to build document pathway
	 *
	 * @return void
	 */
	protected function buildPathway()
	{
		if ($this->input->getInt('hmvc_call'))
		{
			return;
		}

		$app     = JFactory::getApplication();
		$active  = $app->getMenu()->getActive();
		$pathway = $app->getPathway();

		if (isset($active->query['view']) && ($active->query['view'] == 'categories' || $active->query['view'] == 'category'))
		{
			$categoryId = (int) $this->state->get('catid');

			if ($categoryId)
			{
				$parentId = (int) $active->query['id'];
				$paths    = EventbookingHelperData::getCategoriesBreadcrumb($categoryId, $parentId);

				for ($i = count($paths) - 1; $i >= 0; $i--)
				{
					$category = $paths[$i];
					$pathUrl  = EventbookingHelperRoute::getCategoryRoute($category->id, $this->Itemid);
					$pathway->addItem($category->name, $pathUrl);
				}

				$pathway->addItem($this->item->title);
			}
		}
		elseif (isset($active->query['view']) && in_array($active->query['view'], ['fullcalendar', 'calendar', 'upcomingevents']))
		{
			$pathway->addItem($this->item->title);
		}
	}

	/**
	 * Set Open Graph meta data
	 */
	protected function setDocumentMetadata()
	{
		parent::setDocumentMetadata();

		$document      = JFactory::getDocument();
		$rootUri       = JUri::root();
		$largeImageUri = '';
		$document->setMetaData('og:title', $this->item->page_title ?: $this->item->title, 'property');

		if ($this->item->image && file_exists(JPATH_ROOT . '/' . $this->item->image))
		{
			$largeImageUri = $rootUri . $this->item->image;
		}
		elseif ($this->item->thumb && file_exists(JPATH_ROOT . '/media/com_eventbooking/images/' . $this->item->thumb))
		{
			$largeImageUri = $rootUri . 'media/com_eventbooking/images/' . $this->item->thumb;
		}
		elseif ($this->item->thumb && file_exists(JPATH_ROOT . '/media/com_eventbooking/images/thumbs/' . $this->item->thumb))
		{
			$largeImageUri = $rootUri . 'media/com_eventbooking/images/thumbs/' . $this->item->thumb;
		}

		if ($largeImageUri)
		{
			$document->setMetaData('og:image', $largeImageUri, 'property');
		}

		$document->setMetaData('og:url', JUri::getInstance()->toString(), 'property');

		$description = $this->item->meta_description ?: $this->item->description;
		$description = JHtml::_('string.truncate', $description, 200, true, false);
		$document->setMetaData('og:description', $description, 'property');

		$document->setMetaData('og:site_name', JFactory::getConfig()->get('sitename'), 'property');
	}

	/**
	 * Display form which allows add/edit event
	 *
	 * @throws \Exception
	 */
	protected function displayForm()
	{
		EventbookingHelperJquery::colorbox('eb-colorbox-addlocation');

		$app    = JFactory::getApplication();
		$user   = JFactory::getUser();
		$db     = JFactory::getDbo();
		$config = EventbookingHelper::getConfig();
		$item   = $this->model->getData();

		if ($this->input->getInt('validate_input_error') && method_exists($item, 'bind'))
		{
			$item->bind($this->input->post->getData(), ['id']);
		}
		elseif (!$item->id)
		{
			$params = JFactory::getApplication()->getParams();

			$item->main_category_id = $params->get('default_category_id', 0);
		}

		$fieldSuffix = EventbookingHelper::getFieldSuffix();

		if ($config->submit_event_form_layout == 'simple')
		{
			$this->setLayout('simple');
		}

		if ($item->id)
		{
			$ret = EventbookingHelperAcl::checkEditEvent($item->id);
		}
		else
		{
			$ret = EventbookingHelperAcl::checkAddEvent();
		}

		if (!$ret)
		{
			if (!$user->id)
			{
				$active = $app->getMenu()->getActive();

				$option = isset($active->query['option']) ? $active->query['option'] : '';
				$view   = isset($active->query['view']) ? $active->query['view'] : '';
				$layout = isset($active->query['layout']) ? $active->query['layout'] : '';

				if ($option == 'com_eventbooking' && $view == 'events' && $layout == 'form')
				{
					$returnUrl = 'index.php?Itemid=' . $active->id;
				}
				else
				{
					$returnUrl = JUri::getInstance()->toString();
				}

				$app->redirect('index.php?option=com_users&view=login&return=' . base64_encode($returnUrl));
			}
			else
			{
				$app->enqueueMessage(JText::_('EB_NO_ADDING_EVENT_PERMISSION'), 'error');
				$app->redirect(JUri::root(), 403);
			}
		}

		$this->lists = [];

		$query = $db->getQuery(true)
			->select('id, name')
			->from('#__eb_locations')
			->where('published = 1')
			->order('name');

		if (!$user->authorise('core.admin', 'com_eventbooking') && !$config->show_all_locations_in_event_submission_form)
		{
			$query->where('user_id = ' . (int) $user->id);
		}

		$db->setQuery($query);
		$locations = $db->loadAssocList();

		// Categories dropdown
		$query->clear()
			->select('id, parent AS parent_id')
			->select($db->quoteName('name' . $fieldSuffix, 'title'))
			->from('#__eb_categories')
			->where('published = 1')
			->where('submit_event_access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')')
			->order($db->quoteName('name' . $fieldSuffix));

		$db->setQuery($query);
		$categories = $db->loadObjectList();

		$this->buildFormData($item, $categories, $locations);

		$query->clear()
			->select('id, title')
			->from('#__content')
			->where('`state` = 1')
			->order('title');
		$db->setQuery($query);
		$options                   = array();
		$options[]                 = JHtml::_('select.option', 0, JText::_('EB_SELECT_ARTICLE'), 'id', 'title');
		$options                   = array_merge($options, $db->loadObjectList());
		$this->lists['article_id'] = JHtml::_('select.genericlist', $options, 'article_id', 'class="inputbox"', 'id', 'title', $item->article_id);

		$options   = [];
		$options[] = JHtml::_('select.option', 0, JText::_('JNO'));
		$options[] = JHtml::_('select.option', 1, JText::_('JYES'));

		$this->lists['published']                  = JHtml::_('select.genericlist', $options, 'published', ' class="input-medium" ', 'value', 'text', $item->published);
		$this->lists['enable_cancel_registration'] = JHtml::_('select.genericlist', $options, 'enable_cancel_registration', ' class="input-medium" ', 'value', 'text', $item->enable_cancel_registration);
		$this->lists['enable_auto_reminder']       = JHtml::_('select.genericlist', $options, 'enable_auto_reminder', ' class="input-medium" ', 'value', 'text', $item->enable_auto_reminder);

		// Load captcha
		$this->loadCaptcha();

		$this->item           = $item;
		$this->return         = $this->input->getBase64('return');
		$this->languages      = EventbookingHelper::getLanguages();
		$this->isMultilingual = count($this->languages) && JLanguageMultilang::isEnabled();

		parent::display();
	}
}