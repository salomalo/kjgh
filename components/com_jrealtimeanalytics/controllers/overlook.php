<?php
// namespace administrator\components\com_jrealtimeanalytics\controllers;
/**
 * @package JREALTIMEANALYTICS::OVERVIEW::administrator::components::com_jrealtimeanalytics
 * @subpackage controllers
 * @author Joomla! Extensions Store
 * @copyright (C) 2014 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Main controller
 * @package JREALTIMEANALYTICS::OVERVIEW::administrator::components::com_jrealtimeanalytics
 * @subpackage controllers
 * @since 2.4
 */
class JRealtimeControllerOverlook extends JRealtimeController {
	/**
	 * Setta il model state a partire dallo userstate di sessione
	 *
	 * @access protected
	 * @param string $scope        	
	 * @param Object $model        	
	 * @param Object $cParams        	
	 * @return void
	 */
	protected function setModelState($scope = 'default', $model = null, $cParams = null) {
		$option = $this->option;
	
		// Get default model
		$defaultModel = $this->getModel();
		
		$graphTheme = $this->getUserStateFromRequest( "$option.$scope.graphtheme", 'graphtheme', 'Universal');
		$graphType = $this->getUserStateFromRequest( "$option.$scope.graphtype", 'graphtype', 'Bars');
		$statsYear = $this->getUserStateFromRequest( "$option.$scope.stats_year", 'stats_year', date('Y'));
		$statsMonth = $this->getUserStateFromRequest( "$option.$scope.stats_month", 'stats_month', ($cParams->get('overview_report_type', 0) ? date('m') : ''));
		
		// Set model state
		$model->setState ( 'option', $option );
		$model->setState ( 'graphTheme', $graphTheme );
		$model->setState ( 'graphType', $graphType );
		$model->setState ( 'statsYear', $statsYear );
		$model->setState ( 'statsMonth', $statsMonth );
		$model->setState ( 'toPeriod', date('Y-m-d') );
		$model->setState ( 'hasExportPermission', $this->hasGroupsPermissions('exporter_groups', $cParams));
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
		// Get sitemap model and view core
		$document = JFactory::getDocument ();
		
		$viewType = $document->getType ();
		$coreName = $this->getNames ();
		$viewLayout = $this->app->input->get ( 'layout', 'default' );
		
		$view = $this->getView ( $coreName, $viewType, '', array (
				'base_path' => $this->basePath
		) );
		
		// Mixin, add include path for admin side to avoid DRY on model
		$this->addModelPath ( JPATH_COMPONENT_ADMINISTRATOR . '/models', 'JRealtimeModel', 'JRealtimeModel' );
		
		// Get/Create the model
		if ($model = $this->getModel ( $coreName, 'JRealtimeModel' )) {
			// Push the model into the view (as default)
			$view->setModel ( $model, true );
		}
		
		// Set cParams for pdf export usage
		$cParams = $model->getComponentParams ();
		
		// Set model state
		$this->setModelState ( 'overlook', $model, $cParams );
		
		// Graph Generators interface as Setter Dependency Injection
		$graphGenerator = new JRealtimeGraphGeneratorsCharts ( $model->getState ( 'graphTheme', 'Universal') );
		$model->setState('graphRenderer', $graphGenerator);
		
		if (!in_array($this->task, array('displaypdf', 'emailpdf'))) {
			// Set the layout
			$view->setLayout ( $viewLayout );
			$view->display ();
		} else {
			// Permissions check
			if (! $model->getState('hasExportPermission')) {
				$this->setRedirect ( JRoute::_("index.php?option=" . $this->option . "&task=" . $this->corename . ".display"), JText::_ ( 'COM_JREALTIME_ERROR_ALERT_NOACCESS' ), 'notice' );
				return false;
			}
				
			// Call main template
			$prefixPath = null;
			if ($this->task === 'displaypdf' || $this->task === 'emailpdf') {
				$prefixPath = 'pdf_';
			}
			$view->setLayout ( $prefixPath . $viewLayout . '_list' );
				
			// Creazione buffer output
			ob_start ();
			// Parent construction and view display
			$view->display ( 'main' );
			$bufferContent = ob_get_contents ();
			ob_end_clean ();
				
			// Check if report by email is required
			$mailer = null;
			$cParams = $model->getComponentParams ();
			if($cParams->get('report_byemail', 0) && strpos($this->task, 'email') !== false) {
				// Root controller -> dependency injection
				$mailer = JRealtimeHelpersMailer::getInstance('Joomla');
			}
				
			// Choose if plain HTML or PDF conversion is required based on tasks instead of document format
			switch ($this->task) {
				case 'displaypdf' :
				case 'emailpdf' :
					// Do conversion to PDF format using adapter
					$pdfRenderer = new JRealtimeRenderersAdapterTcpdf ($cParams, $mailer);
					$pdfRenderer->renderContent ( $bufferContent, $model, 'overview_stats_report_' );
					break;
				default :
					echo $bufferContent;
			}
		}
	}
}