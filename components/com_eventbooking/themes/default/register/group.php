<?php
/**
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2019 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */

defined( '_JEXEC' ) or die ;

JHtml::_('calendar', '', 'id', 'name');
JHtml::_('bootstrap.tooltip');

EventbookingHelperJquery::validateForm();

if ($this->config->accept_term ==1 && !$this->config->fix_term_and_condition_popup)
{
	EventbookingHelperJquery::colorbox();
}

if ($this->waitingList)
{
	$headerText = JText::_('EB_JOIN_WAITINGLIST');

	if (strlen(strip_tags($this->message->{'waitinglist_form_message' . $this->fieldSuffix})))
	{
		$msg = $this->message->{'waitinglist_form_message' . $this->fieldSuffix};
	}
	else
	{
		$msg = $this->message->waitinglist_form_message;
	}
}
else
{
	$headerText = JText::_('EB_GROUP_REGISTRATION');

	if ($this->fieldSuffix && strlen(strip_tags($this->event->{'registration_form_message_group' . $this->fieldSuffix})))
	{
		$msg = $this->event->{'registration_form_message_group' . $this->fieldSuffix};
	}
	elseif ($this->fieldSuffix && strlen(strip_tags($this->message->{'registration_form_message_group' . $this->fieldSuffix})))
	{
		$msg = $this->message->{'registration_form_message_group' . $this->fieldSuffix};
	}
	elseif (strlen(strip_tags($this->event->registration_form_message_group)))
	{
		$msg = $this->event->registration_form_message_group;
	}
	else
	{
		$msg = $this->message->registration_form_message_group;
	}
}

$replaces = EventbookingHelperRegistration::buildEventTags($this->event, $this->config);

foreach ($replaces as $key => $value)
{
    $key        = strtoupper($key);
    $msg        = str_replace("[$key]", $value, $msg);
    $headerText = str_replace("[$key]", $value, $headerText);
}
?>
<div id="eb-group-registration-form" class="eb-container<?php echo $this->waitingList ? ' eb-waitinglist-group-registration-form' : '';?>">
	<h1 class="eb-page-title"><?php echo $headerText; ?></h1>
	<?php
	if (strlen($msg))
	{
	?>
		<div class="eb-message"><?php echo JHtml::_('content.prepare', $msg); ; ?></div>
	<?php
	}

	if (!$this->bypassNumberMembersStep)
	{
	?>
		<div id="eb-number-group-members">
			<div class="eb-form-heading">
				<?php echo JText::_('EB_NUMBER_MEMBERS'); ?>
			</div>
			<div class="eb-form-content">

			</div>
		</div>
	<?php
	}

	if ($this->collectMemberInformation)
	{
	?>
		<div id="eb-group-members-information">
			<div class="eb-form-heading">
				<?php echo JText::_('EB_MEMBERS_INFORMATION'); ?>
			</div>
			<div class="eb-form-content"></div>
		</div>
	<?php
	}

	if($this->showBillingStep)
	{
	?>
		<div id="eb-group-billing">
			<div class="eb-form-heading">
				<?php echo JText::_('EB_BILLING_INFORMATION'); ?>
			</div>
			<div class="eb-form-content">

			</div>
		</div>
	<?php
	}
	?>
	<script type="text/javascript">
		var step = window.location.hash.substr(1);

		if (!step)
		{
			step = '<?php echo $this->defaultStep; ?>';
		}

		var returnUrl = "<?php echo base64_encode(JUri::getInstance()->toString().'#group_billing'); ?>";

		Eb.jQuery(document).ready(function($)
		{
			if (step == 'group_billing')
			{
				$.ajax({
					url: siteUrl + 'index.php?option=com_eventbooking&view=register&layout=group_billing&event_id=<?php echo $this->event->id; ?>&Itemid=<?php echo $this->Itemid; ?>&format=raw' + langLinkForAjax,
					dataType: 'html',
					success: function(html) {
					    var $billingFormContainer = $('#eb-group-billing .eb-form-content');
                        $billingFormContainer.html(html);
                        $billingFormContainer.slideDown('slow');

                        if ($('#email').val())
						{
							$('#email').validationEngine('validate');
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
			else if (step == 'group_members')
			{
				$.ajax({
					url: siteUrl + 'index.php?option=com_eventbooking&view=register&layout=group_members&event_id=<?php echo $this->event->id; ?>&Itemid=<?php echo $this->Itemid; ?>&format=raw' + langLinkForAjax,
					dataType: 'html',
					success: function(html) {
					    var $groupMembersFormContainer = $('#eb-group-members-information .eb-form-content');
						$groupMembersFormContainer.html(html);
						$groupMembersFormContainer.slideDown('slow');
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
			else
			{
				$.ajax({
					url: siteUrl + 'index.php?option=com_eventbooking&view=register&layout=number_members&event_id=<?php echo $this->event->id; ?>&Itemid=<?php echo $this->Itemid; ?>&format=raw' + langLinkForAjax,
					dataType: 'html',
					success: function(html) {
					    var $numberMembersFormContainer = $('#eb-number-group-members .eb-form-content');
                        $numberMembersFormContainer.html(html);
                        $numberMembersFormContainer.slideDown('slow');
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}

		});
	</script>
</div>