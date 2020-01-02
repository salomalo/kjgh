<?php
// namespace administrator\components\com_jrealtimeanalytics\controllers;
/**
 * @package JREALTIME::GOOGLE::administrator::components::com_jrealtimeanalytics
 * @subpackage controllers
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Main controller
 * @package JREALTIME::GOOGLE::administrator::components::com_jrealtimeanalytics
 * @subpackage controllers
 * @since 2.6
 */
class JRealtimeControllerWebmasters extends JRealtimeController {
	/**
	 * Set model state from session userstate
	 * @access protected
	 * @param string $scope
	 * @return void
	 */
	protected function setModelState($scope = 'default', $ordering = true) {
		$option = $this->option;
		
		// Get default model
		$defaultModel = $this->getModel();
		
		$defaultStartPeriod = date ( "Y-m-01", strtotime ( date ( "Y-m-d" ) ) );
		$defaultEndPeriod = date ( "Y-m-d", strtotime ( "-1 day", strtotime ( "+1 month", strtotime ( date ( "Y-m-01" ) ) ) ) );
		$fromPeriod = $this->getUserStateFromRequest( "$option.$scope.fromperiod", 'fromperiod', strval($defaultStartPeriod));
		$toPeriod = $this->getUserStateFromRequest( "$option.$scope.toperiod", 'toperiod', strval($defaultEndPeriod));
		
		// Set model state
		$defaultModel->setState('fromPeriod', $fromPeriod);
		$defaultModel->setState('toPeriod', $toPeriod);
		$defaultModel->setState ( 'option', $option );
		
		return $defaultModel;
	}
	
	/**
	 * Default listEntities
	 * 
	 * @access public
	 * @param $cachable string
	 *       	 the view output will be cached
	 * @return void
	 */
	public function display($cachable = false, $urlparams = false) {
		// Access check.
		if (!$this->user->authorise ( 'jrealtime.webmasters', $this->option )) {
			$this->setRedirect('index.php?option=com_jrealtimeanalytics&task=cpanel.display', JText::_('COM_JREALTIME_ERROR_ALERT_NOACCESS'));
			return false;
		}
		
		$model = $this->setModelState('webmasters');
		
		// Switch task for XLS rendering
		if($this->task == 'displayxls') {
			// Get view always HTML format
			$view =  $this->getView ();
			// Push the model into the view (as default)
			$view->setModel ( $model, true );
			$view->setLayout('xls');
			
			//Creazione buffer output
			ob_start ();
			// Parent construction and view display
			$view->display('webmasters');
			$bufferContent = ob_get_contents ();
			ob_end_clean ();
			
			$xlsRenderer = new JRealtimeRenderersAdapterXls();
			$xlsRenderer->renderContent($bufferContent, $model, 'google_searchconsole_stats_');
		} else {
			parent::display($cachable, $urlparams);
		}
	}
	
	/**
	 * Delete a db table entity
	 *
	 * @access public
	 * @return void
	 */
	public function deleteEntity() {
		// Load della model e checkin before exit
		$model = $this->getModel ();

		if (! $model->deleteEntity ( null )) {
			// Model set exceptions for something gone wrong, so enqueue exceptions and levels on application object then set redirect and exit
			$modelException = $model->getError ( null, false );
			$this->app->enqueueMessage ( $modelException->getMessage (), $modelException->getErrorLevel () );
			$this->setRedirect ( "index.php?option=" . $this->option . "&task=" . $this->corename . ".display", JText::_ ( 'COM_JREALTIME_GOOGLE_WEBMASTERS_ERROR_' . 'LOGOUT' ) );
			return false;
		}
	
		$this->setRedirect ( "index.php?option=" . $this->option . "&task=" . $this->corename . ".display", JText::_ ( 'COM_JREALTIME_GOOGLE_WEBMASTERS_SUCCESS_LOGOUT' ) );
	}
	
	/**
	 * Class Constructor
	 *
	 * @access public
	 * @return Object&
	 */
	public function __construct($config = array()) {
		parent::__construct ( $config );
		
		$this->registerTask('displayxls', 'display');

		// Composer autoloader
		require_once JPATH_COMPONENT_ADMINISTRATOR. '/framework/composer/autoload_real.php';
		ComposerAutoloaderInitfc5c9af51413a149e4084a610a3ab6djreal::getLoader();
	}
}