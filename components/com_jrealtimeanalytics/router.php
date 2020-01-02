<?php
// namespace components\com_jrealtimeanalytics;
/**
 *
 * @package JREALTIMEANALYTICS::components::com_jrealtimeanalytics
 * @author Joomla! Extensions Store
 * @copyright (C) 2014 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Polyfill abstract class for class based router on Joomla 3.3-
 *
 * @package JREALTIMEANALYTICS::components::com_jrealtimeanalytics
 * @since 2.4
 */
if (! class_exists ( 'JComponentRouterBase' )) {
	abstract class JComponentRouterBase {
		abstract public function build(&$query);
		abstract public function parse(&$segments);
	}
}

/**
 * Routing class from com_jrealtimeanalytics
 *
 * @package JREALTIMEANALYTICS::components::com_jrealtimeanalytics
 * @since 2.4
 */
class JRealtimeAnalyticsRouter extends JComponentRouterBase {
	/**
	 * Chat Joomla router, embeds little helper route
	 *
	 * @package JREALTIMEANALYTICS::components::com_jrealtimeanalytics
	 */
	function build(&$query) {
		$config = JFactory::getConfig();
		static $appSuffix;
		if($appSuffix) {
			$config->set('sef_suffix', $appSuffix);
		}
		
		// Segments that will be translated and built for this URL, subtracted from $query that will be untranslated and not built
		$segments = array ();
		
		$app = JFactory::getApplication ();
		// Get all site menus
		$menus = $app->getMenu ( 'site' );
		
		// Mapping fallback for generic task name = view name if view is not used. View concept is only used wrong way by Joomla menus
		if (! isset ( $query ['view'] ) && isset ( $query ['task'] )) {
			if (strpos ( $query ['task'], '.' )) {
				list ( $controller_name, $controller_task ) = explode ( '.', $query ['task'] );
			}
			$mappedView = $controller_name;
		}
		
		// Helper Route here for existing menu item pointing to this $query, so try finding Itemid before all
		if (empty ( $query ['Itemid'] )) {
			$component = JComponentHelper::getComponent ( 'com_jrealtimeanalytics' );
			$menuItems = $menus->getItems ( 'component_id', $component->id );
			if (! empty ( $menuItems )) {
				foreach ( $menuItems as $menuItem ) {
					if (isset ( $menuItem->query ) && isset ( $menuItem->query ['view'] )) {
						if (isset ( $query ['view'] ) && $menuItem->query ['view'] == $query ['view']) {
							// Found a link exact match to sitemap view default html format within a site menu, use the Itemid for alias: component/com_jrealtimeanalytics=>alias
							$query ['Itemid'] = $menuItem->id;
							break;
						}
						
						if (isset ( $mappedView ) && $menuItem->query ['view'] == $mappedView) {
							// Found a link exact match to sitemap view default html format within a site menu, use the Itemid for alias: component/com_jrealtimeanalytics=>alias
							$query ['Itemid'] = $menuItem->id;
							break;
						}
					}
				}
			}
		}
		
		// Lookup for an menu itemid in $query, should be helped by route helper if any, for mod_menu links there is always $query = http://domain.com/?Itemid=123, and all is desetted by default
		if (empty ( $query ['Itemid'] )) {
			$menuItem = $menus->getActive ();
		} else {
			$menuItem = $menus->getItem ( $query ['Itemid'] );
		}
		// Store query info for menu, for example view name, for the menu selected fom Itemid or current as fallback
		$mView = (empty ( $menuItem->query ['view'] )) ? null : $menuItem->query ['view'];
		$mLayout = (empty ( $menuItem->query ['layout'] )) ? null : $menuItem->query ['layout'];
		$mFormat = (empty ( $menuItem->query ['format'] )) ? 'html' : $menuItem->query ['format'];
		
		// If this is a link to HTML menu format view assigned already to a menu, ensure to unset all by default to leave only menu alias
		if (isset ( $query ['view'] ) && ($mView == $query ['view'])  && (! isset ( $query ['format'] ) || $mFormat == $query ['format']) && (! isset ( $query ['layout'] ) || $mLayout == $query ['layout'])) {
			unset ( $query ['view'] );
			if (isset($query['layout'])) {
				unset ( $query ['layout'] );
			}
			// Return empty segments ONLY if link has a view specified that match a menu item. Controller.task is always left as a segment because could have specific behavior
			return $segments;
		}
		
		// Start desetting $query chunks assigning to segments
		// UNSET VIEW
		if (isset ( $query ['view'] )) {
			// Store view info for $query link
			$view = $query ['view'];
			// Assign and unset
			$segments [] = $query ['view'];
			unset ( $query ['view'] );
		}
		
		// UNSET TASK
		if (isset ( $query ['task'] )) {
			// Assign and unset
			$segments [] = str_replace ( '.', '-', $query ['task'] );
			unset ( $query ['task'] );
		}
		
		// UNSET LAYOUT
		if (isset ( $query ['layout'] )) {
			// Assign and unset
			$segments [] = $query ['layout'];
			unset ( $query ['layout'] );
		}
		
		// UNSET FORMAT
		if (isset($query['format'])) {
			// Assign and unset
			$segments[] = $query['format'];
			unset($query['format']);
			
			// Manage XML/NOT HTML JDocument format if detected
			$appSuffix = $config->get('sef_suffix');
			$config->set('sef_suffix', false);
		}
		
		// Finally return processed segments
		return $segments;
	}
	
	/**
	 * Parse the segments of a URL with following shapes:
	 *
	 * http://mydomain/component/jrealtimeanalytics/view-task
	 *
	 * http://mydomain/component/jrealtimeanalytics/viewname
	 * http://mydomain/component/jrealtimeanalytics/controller.task
	 *
	 * component/jrealtimeanalytics/ has to be handled through route helper for menu Itemid
	 * By convention view based Joomla components are overwritten by mapping viewname = taskname.display ex. view=sitemap is mapped to task=sitemap.display
	 *
	 * @param
	 *        	array	The segments of the URL to parse.
	 * @return array URL attributes to be used by the application.
	 */
	function parse(&$segments) {
		$vars = array ();
		$count = count ( $segments );
		
		if ($count) {
			$count --;
			// VIEW-TASK is always 1� segment
			$segment = array_shift ( $segments );
			
			// Found a view/task
			if (version_compare ( JVERSION, '3.3', '>=' )) { // Fixed the semicolo/dash router issue?
				if (strpos ( $segment, '-' )) {
					$vars ['task'] = str_replace ( '-', '.', $segment );
				} else {
					$vars ['view'] = $segment;
				}
			} else { // Fallback on the compat mode 1:item
				if (strpos ( $segment, ':' )) {
					$vars ['task'] = str_replace ( ':', '.', $segment );
				} else {
					$vars ['view'] = $segment;
				}
			}
		}
		
		if ($count) {
			$count--;
			// LAYOUT is always 2� segment
			$segment = array_shift($segments);
			if ($segment) {
				$vars['layout'] = $segment;
			}
		}
		
		if ($count) {
			$count --;
			// FORMAT is always 3� segment
			$segment = array_shift ( $segments );
			if ($segment) {
				$vars ['format'] = $segment;
			}
		}
		
		return $vars;
	}
}

/**
 * JChatSocial router functions
 *
 * These functions are proxys for the new router interface
 * for old SEF extensions.
 */
function JRealtimeAnalyticsBuildRoute(&$query) {
	$router = new JRealtimeAnalyticsRouter ();
	
	return $router->build ( $query );
}
function JRealtimeAnalyticsParseRoute($segments) {
	$router = new JRealtimeAnalyticsRouter ();
	
	return $router->parse ( $segments );
}