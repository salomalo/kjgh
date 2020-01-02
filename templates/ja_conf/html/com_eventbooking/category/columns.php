<?php
/**
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2018 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

$description = $this->category ? $this->category->description: $this->introText;
?>
<div id="eb-category-page-columns" class="eb-container">
	<?php
	if ($this->params->get('show_page_heading'))
	{
	?>
		<h1 class="eb-page-heading"><span class="title-lead"><?php echo $this->params->get('page_heading');?></span></h1>
	<?php
	}

	if ($description)
	{
	?>
		<div class="eb-category-description clearfix">
			<?php
				if (!empty($this->category->image) && file_exists(JPATH_ROOT . '/images/com_eventbooking/categories/thumb/' . basename($this->category->image)))
				{
					$rootUri = JUri::root(true);
				?>
					<a href="<?php echo $rootUri . '/' . $this->category->image; ?>" class="eb-modal"><img src="<?php echo $rootUri . '/images/com_eventbooking/categories/thumb/' . basename($this->category->image); ?>" class="eb-thumb-left" /></a>
				<?php
				}

				echo $description;
			?>
		</div>
	<?php
	}

	if (count($this->categories))
	{
		echo EventbookingHelperHtml::loadCommonLayout('common/tmpl/categories.php', array('categories' => $this->categories, 'categoryId' => $this->categoryId, 'config' => $this->config, 'Itemid' => $this->Itemid));
	}

	if (count($this->items))
	{
		echo EventbookingHelperHtml::loadCommonLayout('common/tmpl/events_columns.php', array('events' => $this->items, 'config' => $this->config, 'Itemid' => $this->Itemid, 'nullDate' => $this->nullDate , 'ssl' => (int) $this->config->use_https, 'viewLevels' => $this->viewLevels, 'category' => $this->category, 'Itemid' => $this->Itemid, 'bootstrapHelper' => $this->bootstrapHelper));
	}
	elseif(count($this->categories) == 0)
	{
	?>
		<p class="text-info"><?php echo JText::_('EB_NO_EVENTS') ?></p>
	<?php
	}

	if ($this->pagination->total > $this->pagination->limit)
	{
	?>
		<div class="pagination">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php
	}
	?>

	<form method="post" name="adminForm" id="adminForm" action="<?php echo JRoute::_('index.php?option=com_eventbooking&view=category&layout=columns&id='.$this->categoryId.'&Itemid='.$this->Itemid); ?>">
		<input type="hidden" name="id" value="0" />
		<input type="hidden" name="task" value="" />
		<script type="text/javascript">
			function cancelRegistration(registrantId)
			{
				var form = document.adminForm ;

				if (confirm("<?php echo JText::_('EB_CANCEL_REGISTRATION_CONFIRM'); ?>"))
				{
					form.task.value = 'registrant.cancel' ;
					form.id.value = registrantId ;
					form.submit() ;
				}
			}
		</script>
	</form>
</div>