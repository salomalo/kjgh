<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2019 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;

$cols = 9;
JHtml::_('formbehavior.chosen', 'select');
$return = base64_encode(JUri::getInstance()->toString());

if (in_array('last_name', $this->coreFields))
{
	$showLastName = true;
	$cols++;
}
else
{
	$showLastName = false;
}

$rootUri = JUri::root(true);
$nullDate = JFactory::getDbo()->getNullDate();
$bootstrapHelper = EventbookingHelperBootstrap::getInstance();
?>
<script type="text/javascript">
	Joomla.submitbutton = function(pressbutton)
	{
		var form = document.adminForm;

		if (pressbutton == 'add')
		{
			if (form.filter_event_id.value == 0)
			{
				alert("<?php echo JText::_("EB_SELECT_EVENT_TO_ADD_REGISTRANT"); ?>");
				form.filter_event_id.focus();
				return;
			}
		}

		Joomla.submitform( pressbutton );
	}
</script>
<h1 class="eb-page-heading"><?php echo JText::_('EB_REGISTRANT_LIST'); ?></h1>
<div id="eb-registrants-management-page" class="eb-container">
    <div class="btn-toolbar" id="btn-toolbar">
		<?php echo JToolbar::getInstance('toolbar')->render('toolbar'); ?>
    </div>
<form action="<?php JRoute::_('index.php?option=com_eventbooking&view=registrants&Itemid='.$this->Itemid );?>" method="post" name="adminForm" id="adminForm">
	<div class="filters btn-toolbar clearfix mt-2 mb-2">
		<?php echo $this->loadTemplate('search_bar'); ?>
	</div>
<?php
	if (count($this->items))
	{
	?>
		<table class="<?php echo $bootstrapHelper->getClassMapping('table table-striped table-bordered'); ?> table-hover">
		<thead>
			<tr>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
				</th>
				<th class="list_first_name">
					<?php echo JHtml::_('grid.sort',  JText::_('EB_FIRST_NAME'), 'tbl.first_name', $this->state->filter_order_Dir, $this->state->filter_order); ?>
				</th>
				<?php
					if ($showLastName)
					{
					?>
						<th class="list_last_name">
							<?php echo JHtml::_('grid.sort',  JText::_('EB_LAST_NAME'), 'tbl.last_name', $this->state->filter_order_Dir, $this->state->filter_order); ?>
						</th>
					<?php
					}
				?>
				<th class="list_event">
					<?php echo JHtml::_('grid.sort',  JText::_('EB_EVENT'), 'ev.title', $this->state->filter_order_Dir, $this->state->filter_order); ?>
				</th>
				<?php
					if ($this->config->show_event_date)
					{
						$cols++;
					?>
						<td class="list_event_date">
							<?php echo JHtml::_('grid.sort',  JText::_('EB_EVENT_DATE'), 'ev.event_date', $this->state->filter_order_Dir, $this->state->filter_order); ?>
						</td>
					<?php
					}
				?>
				<th class="list_email">
					<?php echo JHtml::_('grid.sort',  JText::_('EB_EMAIL'), 'tbl.email', $this->state->filter_order_Dir, $this->state->filter_order); ?>
				</th>
				<th class="list_registrant_number">
					<?php echo JHtml::_('grid.sort',  JText::_('EB_REGISTRANTS'), 'tbl.number_registrants', $this->state->filter_order_Dir, $this->state->filter_order); ?>
				</th>
                <th>
					<?php echo JHtml::_('grid.sort',  JText::_('EB_REGISTRATION_DATE'), 'tbl.register_date', $this->state->filter_order_Dir, $this->state->filter_order); ?>
                </th>
				<th class="list_amount">
					<?php echo JHtml::_('grid.sort',  JText::_('EB_AMOUNT'), 'tbl.amount', $this->state->filter_order_Dir, $this->state->filter_order); ?>
				</th>
				<?php
				foreach ($this->fields as $field)
				{
					$cols++;

					if ($field->is_core || $field->is_searchable)
					{
					?>
						<th class="title">
							<?php echo JHtml::_('grid.sort', JText::_($field->title), 'tbl.' . $field->name, $this->state->filter_order_Dir, $this->state->filter_order); ?>
						</th>
					<?php
					}
					else
					{
					?>
						<th class="title"><?php echo $field->title; ?></th>
					<?php
					}
				}

				if ($this->config->activate_deposit_feature)
				{
					$cols++;
				?>
					<th class="eb-payment-status" nowrap="nowrap">
						<?php echo JHtml::_('grid.sort',  JText::_('EB_PAYMENT_STATUS'), 'tbl.payment_status', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
				<?php
				}
				?>
				<th class="list_id">
					<?php echo JHtml::_('grid.sort',  JText::_('EB_REGISTRATION_STATUS'), 'tbl.published', $this->state->filter_order_Dir, $this->state->filter_order); ?>
				</th>
				<?php
				if ($this->config->activate_checkin_registrants)
				{
					$cols++;
				?>
					<th class="list_id">
						<?php echo JHtml::_('grid.sort',  JText::_('EB_CHECKED_IN'), 'tbl.checked_in', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
				<?php
				}
				if ($this->config->activate_invoice_feature)
				{
					$cols++;
				?>
					<th width="8%">
						<?php echo JHtml::_('grid.sort',  JText::_('EB_INVOICE_NUMBER'), 'tbl.invoice_number', $this->state->filter_order_Dir, $this->state->filter_order); ?>
					</th>
				<?php
				}
				?>
                <th width="3%" class="title" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort',  JText::_('EB_ID'), 'tbl.id', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
                </th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<?php
					if ($this->pagination->total > $this->pagination->limit)
					{
					?>
						<td colspan="<?php echo $cols; ?>">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					<?php
					}
				?>
			</tr>
		</tfoot>
		<tbody>
		<?php
		for ($i=0, $n=count( $this->items ); $i < $n; $i++)
		{
			$row      = $this->items[$i];
			$link     = JRoute::_('index.php?option=com_eventbooking&view=registrant&id=' . $row->id . '&Itemid=' . $this->Itemid . '&return=' . $return);
			$isMember = $row->group_id > 0 ? true : false;
			$img    = $row->checked_in ? 'tick.png' : 'publish_x.png';
			$alt    = $row->checked_in ? JText::_('EB_CHECKED_IN') : JText::_('EB_NOT_CHECKED_IN');
			$action = $row->checked_in ? JText::_('EB_UN_CHECKIN') : JText::_('EB_CHECKIN');
			$task   = $row->checked_in ? 'registrant.reset_check_in' : 'registrant.check_in_webapp';
			$checked 	= JHtml::_('grid.id',   $i, $row->id );
			?>
			<tr>
				<td>
					<?php echo $checked; ?>
				</td>
				<td>
					<a href="<?php echo $link; ?>">
						<?php echo $row->first_name ?>
					</a>
					<?php
					if ($row->is_group_billing)
					{
						echo '<br />' ;
						echo JText::_('EB_GROUP_BILLING');
					}
					if ($isMember)
					{
						$groupLink = JRoute::_('index.php?option=com_eventbooking&view=registrant&id=' . $row->group_id . '&Itemid=' . $this->Itemid. '&return=' . $return);
					?>
						<br />
						<?php echo JText::_('EB_GROUP'); ?><a href="<?php echo $groupLink; ?>"><?php echo $row->group_name ;  ?></a>
					<?php
					}
					?>
				</td>
				<?php
					if ($showLastName)
					{
					?>
						<td>
							<?php echo $row->last_name ; ?>
						</td>
					<?php
					}
				?>
				<td>
					<?php echo $row->title ; ?>
				</td>
				<?php
					if ($this->config->show_event_date)
					{
					?>
						<td>
							<?php
							if ($row->event_date == EB_TBC_DATE)
							{
								echo JText::_('EB_TBC');
							}
							else
							{
								echo JHtml::_('date', $row->event_date, $this->config->date_format, null);
							}
							?>
						</td>
					<?php
					}
				?>
				<td>
					<?php echo $row->email; ?>
				</td>
				<td class="center" style="font-weight: bold;">
					<?php echo $row->number_registrants; ?>
				</td>
                <td class="center">
	                <?php echo JHtml::_('date', $row->register_date, $this->config->date_format); ?>
                </td>
				<td align="right">
					<?php echo EventbookingHelper::formatAmount($row->amount, $this->config); ?>
				</td>
				<?php
				foreach ($this->fields as $field)
				{
					$fieldValue = isset($this->fieldsData[$row->id][$field->id]) ? $this->fieldsData[$row->id][$field->id] : '';

					if ($fieldValue && $field->fieldtype == 'File')
					{
						$fieldValue = '<a href="' . JRoute::_('index.php?option=com_eventbooking&task=controller.download_file&file_name=' . $fieldValue) . '">' . $fieldValue . '</a>';
					}
					?>
					<td>
						<?php echo $fieldValue; ?>
					</td>
					<?php
				}

				if ($this->config->activate_deposit_feature)
				{
				?>
					<td>
						<?php
						if($row->payment_status == 1)
						{
							echo JText::_('EB_FULL_PAYMENT');
						}
						elseif ($row->payment_status == 2)
						{
							echo JText::_('EB_DEPOSIT_PAID');
						}
						else
						{
							echo JText::_('EB_PARTIAL_PAYMENT');
						}
						?>
					</td>
				<?php
				}
				?>
				<td class="center">
					<?php
					switch ($row->published)
					{
						case 0 :
							echo JText::_('EB_PENDING');
							break;
						case 1 :
							echo JText::_('EB_PAID');
							break;
						case 2 :
							echo JText::_('EB_CANCELLED');
							break;
						case 3:
							echo JText::_('EB_WAITING_LIST');
							break;
					}
					?>
				</td>
				<?php
				if ($this->config->activate_checkin_registrants)
				{
				?>
					<td class="center">
						<a href="<?php echo JRoute::_('index.php?option=com_eventbooking&task='.$task.'&id='.$row->id.'&'.JSession::getFormToken().'=1'.'&Itemid='.$this->Itemid); ?>"><img src="<?php echo $rootUri . '/media/com_eventbooking/assets/images/' . $img; ?>" alt="<?php echo $alt; ?>" /></a>

                        <?php
                        if ($row->checked_in && $row->checked_in_at && $row->checked_in_at != $nullDate)
                        {
	                    ?>
                            <br /><?php echo JText::sprintf('EB_CHECKED_IN_AT', JHtml::_('date', $row->checked_in_at, $this->config->date_format.' H:i:s')); ?>
	                    <?php
                        }

                        if (!$row->checked_in && $row->checked_out_at && $row->checked_out_at != $nullDate)
                        {
	                    ?>
                            <br /><span style="color: red;"><?php echo JText::sprintf('EB_CHECKED_OUT_AT', JHtml::_('date', $row->checked_out_at, $this->config->date_format.' H:i:s')); ?></span>
	                    <?php
                        }
                        ?>
					</td>
				<?php
				}
				if ($this->config->activate_invoice_feature)
				{
				?>
					<td class="center">
						<?php
						if ($row->invoice_number)
						{
						?>
							<a href="<?php echo JRoute::_('index.php?option=com_eventbooking&task=registrant.download_invoice&id='.($row->cart_id ? $row->cart_id : ($row->group_id ? $row->group_id : $row->id))); ?>" title="<?php echo JText::_('EB_DOWNLOAD'); ?>"><?php echo EventbookingHelper::callOverridableHelperMethod('Helper', 'formatInvoiceNumber', [$row->invoice_number, $this->config, $row]) ; ?></a>
						<?php
						}
						?>
					</td>
				<?php
				}
				?>
                <td class="center">
					<?php echo $row->id; ?>
                </td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
	<?php
		echo JHtml::_(
			'bootstrap.renderModal',
			'collapseModal',
			array(
				'title' => JText::_('EB_MASS_MAIL'),
				'footer' => $this->loadTemplate('batch_footer')
			),
			$this->loadTemplate('batch_body')
		);
	}
	else
	{
	?>
		<div class="eb-message"><?php echo JText::_('EB_NO_REGISTRATION_RECORDS');?></div>
	<?php
	}
?>
	<input type="hidden" name="task" id="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->filter_order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->filter_order_Dir; ?>" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
</div>