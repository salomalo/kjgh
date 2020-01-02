<?php
/**
* Mosets Tree 
*
* @package Mosets Tree 2.0
* @copyright (C) 2004-present Lee Cher Yeong
* @url http://www.mosets.com/
* @author Lee Cher Yeong <mtree@mosets.com>
**/
defined('_JEXEC') or die('Restricted access');

JLoader::register('JTableUser', JPATH_LIBRARIES.'/joomla/database/table/user.php');

//Base plugin class.
require_once JPATH_ROOT.'/components/com_mtree/Savant2/Plugin.php';

class Savant2_Plugin_ahrefclaim extends Savant2_Plugin {
	
	function plugin()
	{
		global $mtconf;
		$my = JFactory::getUser();

		list($link, $attr) = array_merge(func_get_args(), array(null));

		$database = JFactory::getDBO();
		
		# Load Parameters
		$params = new JRegistry( $link->attribs );
		$params->def( 'show_claim', $mtconf->get('show_claim') );
		
		$owner = new JTableUser( $database );
		$owner->load( $link->user_id );

		$usergroup_ids = JAccess::getGroupsByUser($link->user_id,false);
		$arr_intersect = array_intersect($usergroup_ids, array(6,7,8));

		$user_listing_is_claimable = false;
		if( count($arr_intersect) > 0 ) {
			$user_listing_is_claimable = true;
		}

		if (
			$params->get( 'show_claim' ) == 1
			&&
            $user_listing_is_claimable
		) {

			$html = '';
			$html .= '<a href="';
			$html .= JRoute::_( 'index.php?option=com_mtree&task=claim&link_id='.$link->link_id);
			$html .= '"';

			# Insert attributes
			if (is_array($attr)) {
				// from array
				foreach ($attr as $key => $val) {
					$key = htmlspecialchars($key);
					$val = htmlspecialchars($val);
					$html .= " $key=\"$val\"";
				}
			} elseif (! is_null($attr)) {
				// from scalar
				$html .= " $attr";
			}
			
			$html .= '>'.JText::_( 'COM_MTREE_CLAIM' )	."</a>";

			# Return the claim listing link
			return $html;
		}

	}

}