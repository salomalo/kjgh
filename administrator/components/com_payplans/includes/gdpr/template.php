<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class PayplansGdprTemplate extends JObject
{
	public $id = null;
	public $type = null;
	public $created = null;

	// Determines if the item has an extended view (standalone pag)
	public $view = false;

	// Determines if the item is related to another view (standalone page)
	public $relation = false;

	// Title of the subject
	public $title = null;

	// Is only used in the listing to preview a snippet of html codes
	public $intro = null;

	// Is only used to generate contents on the item view page
	public $content = null;

	public $source = null;

	// to override the source filename.
	// in some situation, the source might be a hash only.
	// thus we need this attribute to hold the filename.
	// the array size of sourceFilename should be indentical to $source
	public $sourceFilename = null;


	/**
	 * Generates the view file
	 *
	 * @since   3.7
	 * @access  public
	 */
	public function createViewFile(PayplansGdrpTab $tab)
	{
		$viewFile = $this->getViewFile($tab);
		$contents = $this->getItemContent($tab);

		JFile::write($viewFile, $contents);
	}

	/**
	 * Renders the output
	 *
	 * @since   3.7
	 * @access  public
	 */
	public function getListingContent(PayplansGdrpTab $tab)
	{
		$relation = $this->hasRelation($tab);
		ob_start();
?>
		<div class="gdpr-item">
			<?php if ($relation) { ?>
			<a href="<?php echo $relation;?>" class="gdpr-item__title"><?php echo $this->title;?></a>
			<?php } else { ?>
				<?php if ($this->hasView()) { ?>
					<a href="<?php echo $this->getLink();?>" class="gdpr-item__title"><?php echo $this->title;?></a>
				<?php } else { ?>
					<b><?php echo $this->title;?></b>
				<?php } ?>
			<?php } ?>

			<?php if ($this->hasIntro()) { ?>
			<div class="gdpr-item__intro">
				<?php echo $this->intro;?>
			</div>
			<?php } ?>
		</div>
<?php
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}

	/**
	 * Determines if the item has relation
	 *
	 * @since   3.7
	 * @access  public
	 */
	public function hasRelation(PayplansGdrpTab $tab)
	{
		$path = false;

		if ($this->relation) {
			$userTmpPath = PayplansGdpr::getUserTempPath($tab->adapter->user);

			$path = $userTmpPath . '/' . $this->relation;
		}

		return $path;
	}

	/**
	 * Renders the output for the item view
	 *
	 * @since   3.7
	 * @access  public
	 */
	public function getItemContent($tab)
	{
		$baseUrl = '';
		$sidebar = PayplansGdpr::getSidebarContents(false, $tab->key);

		$content = $this->content;

		$contents = $content;
		$hasBack = true;
		$sectionTitle = $this->title;
		$sectionDesc = false; 

		ob_start(); ?>

		<!DOCTYPE html>
		<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			<base href="<?php echo $baseUrl;?>" />
			<title><?php echo JText::_('COM_PAYPLANS_GDPR_YOUR_INFORMATION'); ?></title>
			<style type="text/css">
				html,
				body {
					height: 100%;
					background: #f8f7fc;
				}
				html,*{
					font-family:-apple-system, BlinkMacSystemFont,"Segoe UI","Roboto", "Droid Sans","Helvetica Neue", Helvetica, Arial, sans-serif;
					line-height:1.5;
					-webkit-text-size-adjust:100%;
				}
				body {
					font-size: 1em;
					color: #444;
				}
				a {
					color: #007bff;
					text-decoration: none;
				}
				.container-wrapper {
					max-width: 900px;
					height: 100%;
					margin: 0 auto;
				}
				.gdpr-container {
					display: table;
					width: 100%;
					height: 100%;
					position: relative;
					background: #f8f7fc;
					font-size: .9rem;
				}
				.gdpr-container:before {
					position: absolute;
					content: '';
					background-color: #fff;
					top: 0;
					left: -100%;
					width: 100%;
					height: 100%;
					display: block;
				}
				.gdpr-container__nav {
					display: table-cell;
					width: 220px;
					padding: 20px 40px 20px 20px;
					background: #fff;
				}
				.gdpr-container__content {
					display: table-cell;
					padding: 20px 20px 20px 40px;
				}

				@media (max-width: 720px) {
					.gdpr-container,
					.gdpr-container__nav,
					.gdpr-container__content {
						display: block;
						width: 100%;
						padding: 0;
					}
					.gdpr-content {
						padding: 20px;
					}
				}
				/*Elements*/

				/*ul*/
				.gdpr-nav {
					list-style: none;
					margin: 0;
					padding: 0;
					display: -webkit-box;
					display: -ms-flexbox;
					display: flex;
					-ms-flex-wrap: wrap;
					flex-wrap: wrap;
					font-size: .85em;
				}
				.gdpr-nav li {
					display: -webkit-box;
					display: -ms-flexbox;
					display: flex;
					width: 100%;
					border-bottom: 1px solid #e1e1e1;
				}
				.gdpr-nav li.is-active {
					border-bottom: 1px solid #007bff;
				}
				.gdpr-nav li a {
					display: block;
					width: 100%;
					padding: .5rem 1rem .5rem 0;
					text-decoration: none;
					color: #333;

					/*color: #007bff;*/
					background-color: transparent;
				}

				.gdpr-header {
					margin-bottom: 1em;
					font-size: .9rem;
				}
				.gdpr-content {
					
				}
				.gdpr-main-title {
					font-weight: bold;
					padding: .5rem 0 0;
					margin-bottom: .4rem;
				}
				.gdpr-main-desc {
					margin-bottom: .8rem;	

				}
				.gdpr-section-title {
					font-weight: bold;
					margin-bottom: .2rem;
				}
				.gdpr-section-desc {
					margin-bottom: .2rem;
				}
				.gdpr-back {
					margin-bottom: 20px;
				}
				.gdpr-item {
					background: #fff;
					padding: 16px;
					border-radius: 3px;
					box-shadow: 0px 1px 0px rgba(0,0,0,.125);
					font-size: .85rem;
					margin-bottom: 1rem;
				}
				.gdpr-item a {
					text-decoration: none;
					font-weight: bold;
				}
				.gdpr-item__title {
					margin-bottom: .2rem;
				}
				.gdpr-item__desc {
					margin-bottom: .2rem;
				}
				.gdpr-item__desc audio {
					width: 100% !important;
				}
				.gdpr-item__meta {
					color: #aaa;
					font-size: .7rem;
				}
				.gdpr-item__label {
					
					margin-top: .8rem;
				}
				.gdpr-label {
					font-size: .7rem;
					background: #EAFFFC;
					border: 1px solid #BDD4D6;
					padding: .08em .8em;
					border-radius: 1px;
				}
				.video-container {
					position: relative;
					padding-bottom: 56.25%;
					height: 0;
					overflow: hidden;
				}
				.video-container iframe,
				.audio-container iframe, 
				.video-container object,
				.video-container embed {
					position: absolute;
					top: 0;
					left: 0;
					width: 100%;
					height: 100%;
				}
				.audio-container {
					display: block;
					overflow: hidden;
					position: relative;
					height: 0;
					padding: 0;
				}
				.audio-container.is-soundcloud {
					padding-top: 150px;
				}
				.audio-container.is-spotify {
					padding-top: 80px;
				}

				.gdpr-table {
					border-spacing: 0;
					border-collapse: collapse;
				}
				.gdpr-table > tbody > tr > td,
				.gdpr-table > tbody > tr > th,
				.gdpr-table > tfoot > tr > td, 
				.gdpr-table > tfoot > tr > th,
				.gdpr-table > thead > tr > td, 
				.gdpr-table > thead > tr > th {
					padding: 8px;
					line-height: 1.5;
					vertical-align: top;
					border-top: 1px solid #e1e1e1;
				}
				.gdpr-table > thead > tr > th {
					vertical-align: bottom;
					border-bottom: 2px solid #e1e1e1;
					border-top: 0;
				}

				/* rating style */
				.stars .star {
				    float: left;
				}
				.stars .star polygon {
				    fill: #d8d8d8;
				}
				.stars[data-stars] .star polygon {
				    fill: #ffd055;
				}
				.stars[data-stars="1"] .star:nth-child(1) ~ .star polygon {
				    fill: #d8d8d8;
				}
				.stars[data-stars="2"] .star:nth-child(2) ~ .star polygon {
				    fill: #d8d8d8;
				}
				.stars[data-stars="3"] .star:nth-child(3) ~ .star polygon {
				    fill: #d8d8d8;
				}
				.stars[data-stars="4"] .star:nth-child(4) ~ .star polygon {
				    fill: #d8d8d8;
				}
				.stars[data-stars="5"] .star:nth-child(5) ~ .star polygon {
				    fill: #d8d8d8;
				}
				.gdpr-item__rating {
				   display: inline-block;
				}

			</style>
		</head>

		<body>
			<div class="container-wrapper">
				<div class="gdpr-container">
					<div class="gdpr-container__nav">
						<div class="gdpr-side">
							<?php echo $sidebar; ?>
						</div>
					</div>

					<div class="gdpr-container__content">
						<div class="gdpr-header">
							<?php if ($hasBack) { ?>
							<div class="gdpr-back"><a href="javascript:history.go(-1);"><?php echo JText::_('COM_ES_BACK'); ?></a></div>
							<?php } ?>

							<?php if ($sectionTitle) { ?>
							<h1 class="gdpr-section-title"><?php echo JText::_($sectionTitle);?></h1>
							<?php } ?>

							<?php if ($sectionDesc) { ?>
							<div class="gdpr-section-desc"><?php echo JText::_($sectionDesc);?></div>
							<?php } ?>
						</div>

						<div class="gdpr-content">
							<?php echo $contents; ?>
						</div>
					</div>
				</div>
			</div>
		</body>
		</html>

		<?php $output = ob_get_contents();
		ob_end_clean();
	
		return $output;
	}

	/**
	 * Retrieves the link of the item
	 *
	 * @since   3.7
	 * @access  public
	 */
	public function getLink()
	{
		$link = $this->id . '.html';

		return $link;
	}

	/**
	 * Retrieves the file path to the view file
	 *
	 * @since   3.7
	 * @access  public
	 */
	public function getViewFile($tab)
	{
		$path = $tab->path . '/' . $this->id . '.html';

		return $path;
	}

	/**
	 * Determines if the item has an intro
	 *
	 * @since   3.7
	 * @access  public
	 */
	public function hasIntro()
	{
		if (!$this->intro) {
			return false;
		}

		return true;
	}

	/**
	 * Determines if the item should have an item view
	 *
	 * @since   3.7
	 * @access  public
	 */
	public function hasView()
	{
		return $this->view;
	}

	/**
	 * Determines if the item should process media file
	 *
	 * @since   3.7
	 * @access  public
	 */
	public function hasSource()
	{
		return $this->source;
	}

	/**
	 * Download/Copy over the files
	 *
	 * @since   3.7
	 * @access  public
	 */
	public function download($source, $destination)
	{
			$sourceFile = JPATH_ROOT . $source;

			if (JFile::exists($sourceFile)) {
				return JFile::copy($sourceFile, $destination);
			}

			return false;
		}
}
