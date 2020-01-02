<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2019 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

class plgSearchEventBooking extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access      protected
	 *
	 * @param       object $subject The object to observe
	 * @param       array  $config  An array that holds the plugin configuration
	 *
	 * @since       1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * @return array An array of search areas
	 */
	public function onContentSearchAreas()
	{
		static $areas = array(
			'eb_search' => 'Events',
		);

		return $areas;
	}

	public function onContentSearch($text, $phrase = '', $ordering = '', $areas = null)
	{
		// Require library + register autoloader
		require_once JPATH_ADMINISTRATOR . '/components/com_eventbooking/libraries/rad/bootstrap.php';

		$db     = JFactory::getDbo();
		$config = EventbookingHelper::getConfig();

		if (is_array($areas))
		{
			if (!array_intersect($areas, array_keys($this->onContentSearchAreas())))
			{
				return array();
			}
		}

		// load plugin params info

		if ($this->params->get('item_id'))
		{
			$Itemid = $this->params->get('item_id');
		}
		else
		{
			$Itemid = EventbookingHelper::getItemid();
		}

		$limit = $this->params->def('search_limit', 50);
		$text  = trim($text);

		if ($text == '')
		{
			return array();
		}

		$section = JText::_('Events');

		switch ($phrase)
		{
			case 'exact':
				$text      = $db->quote('%' . $db->escape($text, true) . '%', false);
				$wheres2   = array();
				$wheres2[] = 'a.title LIKE ' . $text;
				$wheres2[] = 'a.short_description LIKE ' . $text;
				$wheres2[] = 'a.description LIKE ' . $text;
				$where     = '(' . implode(') OR (', $wheres2) . ')';
				break;

			case 'all':
			case 'any':
			default:
				$words  = explode(' ', $text);
				$wheres = array();
				foreach ($words as $word)
				{
					$word      = $db->quote('%' . $db->escape($word, true) . '%', false);
					$wheres2   = array();
					$wheres2[] = 'a.title LIKE ' . $word;
					$wheres2[] = 'a.short_description LIKE ' . $word;
					$wheres2[] = 'a.description LIKE ' . $word;
					$wheres[]  = implode(' OR ', $wheres2);
				}
				$where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
				break;
		}

		switch ($ordering)
		{
			case 'oldest':
				$order = 'a.event_date ASC';
				break;
			case 'alpha':
				$order = 'a.title ASC';
				break;
			case 'newest':
				$order = 'a.event_date ASC';
				break;
			default:
				$order = 'a.ordering ';
		}

		$user  = JFactory::getUser();
		$query = 'SELECT a.id, a.category_id AS cat_id, a.title AS title, a.description AS text, event_date AS `created`, '
			. $db->quote($section) . ' AS section,'
			. ' "2" AS browsernav'
			. ' FROM #__eb_events AS a'
			. ' WHERE (' . $where . ') AND a.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')'
			. ($config->hide_past_events ? ' AND DATE(a.event_date) >= CURDATE()' : '')
			. ' AND a.published = 1'
			. ' ORDER BY ' . $order;
		$db->setQuery($query, 0, $limit);
		$rows = $db->loadObjectList();

		if (count($rows))
		{
			foreach ($rows as $key => $row)
			{
				$rows[$key]->href = EventbookingHelperRoute::getEventRoute($row->id, 0, $Itemid);
			}
		}

		return $rows;
	}
}
