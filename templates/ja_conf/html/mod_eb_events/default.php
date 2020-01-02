<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2018 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;

JHtml::_('script', JUri::root().'media/com_eventbooking/assets/js/eventbookingjq.js', false, false);	

if ($showLocation)
{
	$width = (int) $config->map_width ;
	
	if (!$width)
	{
		$width = 800 ;
	}
	
	$height = (int) $config->map_height ;
	
	if (!$height)
	{
		$height = 600 ;
	}
	
	$deviceType = EventbookingHelper::getDeviceType();
	
	if ($deviceType == 'mobile')
	{
		EventbookingHelperJquery::colorbox('eb-colorbox-map', '100%', $height . 'px', 'true', 'false');
	}
	else
	{
		EventbookingHelperJquery::colorbox('eb-colorbox-map', $width . 'px', $height . 'px', 'true', 'false');
	}
}
if (count($rows)) 
{
	$baseUri = JUri::base(true);
?>
	<table class="eb_event_list" width="100%">
		<?php
			foreach ($rows as  $row) 
			{
				$url = JRoute::_(EventbookingHelperRoute::getEventRoute($row->id, 0, $itemId));;
			?>	
				<tr>
					<td class="eb_event">
						<a href="<?php echo $url; ?>" class="eb_event_img img-wrap">
							<?php
								if ($showThumb && $row->thumb && file_exists(JPATH_ROOT.'/media/com_eventbooking/images/thumbs/'.$row->thumb))
								{
								?>
								<img src="<?php echo $baseUri . '/media/com_eventbooking/images/thumbs/' . $row->thumb; ?>" class="eb_event_thumb"/>

							<span class="event_date">
								<?php
									if ($row->event_date == '2099-12-31 00:00:00')
									{
										echo JText::_('EB_TBC');
									}
									else
									{
										echo JHtml::_('date', $row->event_date, $config->event_date_format, null);
									}
								?>
							</span>
						</a>
						<h4 class="eb_event_link"><a href="<?php echo $url; ?>"><?php } echo $row->title; ?></a></h4>
						<?php
							if ($showCategory) 
							{
							?>
								<br />		
								<span><?php echo $row->number_categories > 1 ? JText::_('EB_CATEGORIES') : JText::_('EB_CATEGORY'); ?>:&nbsp;&nbsp;<?php echo $row->categories ; ?></span>
							<?php	
							}
							if ($showLocation && strlen($row->location_name)) 
							{
							?>
								<br />		
								<a href="<?php echo JRoute::_('index.php?option=com_eventbooking&view=map&location_id='.$row->location_id.'&tmpl=component&format=html&Itemid='.$itemId); ?>" class="eb-colorbox-map"><?php echo $row->location_name ; ?></a>
							<?php	 
							}
						?>											
					</td>
				</tr>
			<?php
			}
		?>
	</table>
<?php	
} 
else 
{
?>
	<div class="eb_empty"><?php echo JText::_('EB_NO_UPCOMING_EVENTS') ?></div>
<?php	
}
?>