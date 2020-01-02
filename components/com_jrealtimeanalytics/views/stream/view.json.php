<?php 
// namespace administrator\components\com_jrealtime\views\stream;

/**
 * @author Joomla! Extensions Store
 * @package JREALTIMEANALYTICS::STREAM::administrator::components::com_jrealtimeanalytics
 * @subpackage views
 * @subpackage stream
 * @copyright (C)2014 Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * View for JS client
 *
 * @package JREALTIMEANALYTICS::STREAM::administrator::components::com_jrealtimeanalytics
 * @subpackage views
 * @subpackage stream
 * @since 2.0
 */
class JRealtimeViewStream extends JRealtimeView {
	/**
	 * Return application/json response to JS client APP
	 * Replace $tpl optional param with $userData contents to inject
	 *        	
	 * @access public
	 * @param Object $streamData
	 * @return void
	 */
	public function display($streamData = null) {
		echo json_encode($streamData);  
	}
}