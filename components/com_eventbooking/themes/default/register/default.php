<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2019 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined( '_JEXEC' ) or die ;
$doc    = JFactory::getDocument();
$doc->addStyleSheet("components/com_jblance/css/customer/register_individual.css");

EventbookingHelperJquery::validateForm();

/* @var  $this EventbookingViewRegisterHtml */

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
	$headerText = JText::_('EB_INDIVIDUAL_REGISTRATION');

	if ($this->fieldSuffix && strlen(strip_tags($this->event->{'registration_form_message' . $this->fieldSuffix})))
	{
		$msg = $this->event->{'registration_form_message' . $this->fieldSuffix};
	}
	elseif ($this->fieldSuffix && strlen(strip_tags($this->message->{'registration_form_message' . $this->fieldSuffix})))
	{
		$msg = $this->message->{'registration_form_message' . $this->fieldSuffix};
	}
	elseif (strlen(strip_tags($this->event->registration_form_message)))
	{
		$msg = $this->event->registration_form_message;
	}
	else
	{
		$msg = $this->message->registration_form_message;
	}

	$msg = str_replace('[AMOUNT]', EventbookingHelper::formatCurrency($this->amount, $this->config, $this->event->currency_symbol), $msg);
}

$replaces = EventbookingHelperRegistration::buildEventTags($this->event, $this->config);

foreach ($replaces as $key => $value)
{
	$key        = strtoupper($key);
	$msg        = str_replace("[$key]", $value, $msg);
	$headerText = str_replace("[$key]", $value, $headerText);
}

if ($this->config->use_https)
{
	$url = JRoute::_('index.php?option=com_eventbooking&task=register.process_individual_registration&Itemid=' . $this->Itemid, false, 1);
}
else
{
	$url = JRoute::_('index.php?option=com_eventbooking&task=register.process_individual_registration&Itemid=' . $this->Itemid, false);
}

$selectedState = '';

/* @var EventbookingHelperBootstrap $bootstrapHelper*/
$bootstrapHelper     = $this->bootstrapHelper;
$controlGroupClass   = $bootstrapHelper->getClassMapping('control-group');
$inputPrependClass   = $bootstrapHelper->getClassMapping('input-prepend');
$inputAppendClass    = $bootstrapHelper->getClassMapping('input-append');
$addOnClass          = $bootstrapHelper->getClassMapping('add-on');
$controlLabelClass   = $bootstrapHelper->getClassMapping('control-label');
$controlsClass       = $bootstrapHelper->getClassMapping('controls');
$btnPrimary          = $bootstrapHelper->getClassMapping('btn btn-primary');
$formHorizontalClass = $bootstrapHelper->getClassMapping('form form-horizontal');

$layoutData = array(
	'controlGroupClass' => $controlGroupClass,
	'controlLabelClass' => $controlLabelClass,
	'controlsClass'     => $controlsClass,
);

?>
<div id="eb-individual-registration-page" class="eb-container<?php echo $this->waitingList ? ' eb-waitinglist-individual-registration-form' : '';?>">
	<div>
		<p class="eb-page-heading"><?php echo $headerText; ?></p>
	</div>
	<?php
	if (strlen($msg))
	{
	?>
		<div class="eb-message"><?php echo JHtml::_('content.prepare', $msg); ?></div>
	<?php
	}

	if (!empty($this->ticketTypes))
	{
		echo $this->loadTemplate('tickets');
	}

	if (!$this->userId && ($this->config->user_registration || $this->config->show_user_login_section))
	{
		$validateLoginForm = true;

		echo $this->loadCommonLayout('register/register_login.php', $layoutData);
	}
	else
	{
		$validateLoginForm = false;
	}
	?>
	<form method="post" name="adminForm" id="adminForm" action="<?php echo $url; ?>" autocomplete="off" class="<?php echo $formHorizontalClass; ?>" enctype="multipart/form-data">
	<?php
		if (!$this->userId && $this->config->user_registration)
		{
			echo $this->loadCommonLayout('register/register_user_registration.php', $layoutData);
		}

		if ($this->collectMembersInformation)
        {
	    ?>
            <div class="clearfix" id="tickets_members_information">
                <?php
                    if (isset($this->ticketsMembers))
                    {
                        echo $this->ticketsMembers;
                    }
                ?>
            </div>
            <h3 class="eb-heading"><?php echo JText::_('EB_BILLING_INFORMATION'); ?></h3>
	    <?php
        }
		$fields = $this->form->getFields();

		if (isset($fields['state']))
		{
			$selectedState = $fields['state']->value;
		}

		foreach ($fields as $field)
		{

			echo $field->getControlGroup($bootstrapHelper);
		}

	    if ($this->totalAmount > 0 || (!empty($this->ticketTypes) && EventbookingHelperRegistration::showPriceColumnForTicketType($this->event->id)) || $this->form->containFeeFields())
		{
			$showPaymentInformation = true;
			$showTaxAmount          = ($this->event->tax_rate > 0);

		?>
			<h3 class="eb-heading"><?php echo JText::_('EB_PAYMENT_INFORMATION'); ?></h3>
		<?php
        $nullDate = JFactory::getDbo()->getNullDate();
        $showDiscountAmount = $this->enableCoupon
                || $this->discountAmount > 0
                || $this->discountRate > 0
                || $this->bundleDiscountAmount > 0
                || ($this->event->early_bird_discount_date && ($this->event->early_bird_discount_date != $nullDate) && $this->event->date_diff >= 0);

        $hasLateFee = ($this->event->late_fee_date != $nullDate)
            && $this->event->late_fee_date_diff >= 0
            && $this->event->late_fee_amount > 0;

        $showTaxAmount = $this->event->tax_rate > 0;

        $layoutData['currencySymbol']     = $this->event->currency_symbol ?: $this->config->currency_symbol;
        $layoutData['onCouponChange']     = 'calculateIndividualRegistrationFee();';
        $layoutData['addOnClass']         = $addOnClass;
        $layoutData['inputPrependClass']  = $inputPrependClass;
        $layoutData['inputAppendClass']   = $inputAppendClass;
        $layoutData['showDiscountAmount'] = $showDiscountAmount;
        $layoutData['showTaxAmount']      = $showTaxAmount;
        $layoutData['showGrossAmount']    = ($showDiscountAmount || $showTaxAmount || $hasLateFee || $this->showPaymentFee);

		echo $this->loadCommonLayout('register/register_payment_amount.php', $layoutData);

		if (!$this->waitingList)
		{
			$layoutData['registrationType'] = 'individual';
			echo $this->loadCommonLayout('register/register_payment_methods.php', $layoutData);
		}
	}

	if ($this->config->show_privacy_policy_checkbox || $this->config->show_subscribe_newsletter_checkbox)
    {
	    echo $this->loadCommonLayout('register/register_gdpr.php', $layoutData);
    }

	if ($articleId = $this->getTermsAndConditionsArticleId($this->event, $this->config))
	{
		$layoutData['articleId'] = $articleId;

		echo $this->loadCommonLayout('register/register_terms_and_conditions.php', $layoutData);
	}

	if ($this->showCaptcha)
	{
	    if ($this->captchaPlugin == 'recaptcha_invisible')
        {
            $style = ' style="display:none;"';
        }
        else
        {
            $style = '';
        }
	?>
		<div class="<?php echo $controlGroupClass;  ?>"<?php echo $style; ?>>
			<label class="<?php echo $controlLabelClass; ?>">
				<?php echo JText::_('EB_CAPTCHA'); ?><span class="required">*</span>
			</label>
			<div class="<?php echo $controlsClass; ?>">
				<?php echo $this->captcha; ?>
			</div>
		</div>
	<?php
	}

	if ($this->waitingList)
	{
		$buttonText = JText::_('EB_PROCESS');
	}
	else
	{
		$buttonText = JText::_('EB_PROCESS_REGISTRATION');
	}
	?>

		<div class="form-actions">
			<input type="button" class="cancel-btn" name="btnBack" value="取消" onclick="window.history.go(-1);" />
			<input type="submit" class="confirm-btn" name="btn-submit" id="btn-submit" value="确定" />
			<img id="ajax-loading-animation" src="<?php echo JUri::base(true);?>/media/com_eventbooking/ajax-loadding-animation.gif" style="display: none;"/>
		</div>
	<?php
	if (count($this->methods) == 1)
	{
	?>
		<input type="hidden" name="payment_method" value="<?php echo $this->methods[0]->getName(); ?>" />
	<?php
	}
	?>
	<input type="hidden" id="ticket_type_values" name="ticket_type_values" value="" />
	<input type="hidden" name="event_id" id="event_id" value="<?php echo $this->event->id ; ?>" />
	<input type="hidden" name="show_payment_fee" value="<?php echo (int)$this->showPaymentFee ; ?>" />
	<input type="hidden" id="card-nonce" name="nonce" />
		<script type="text/javascript">
			var eb_current_page = 'default';
			Eb.jQuery(document).ready(function($){
				<?php
					if ($this->amount == 0)
					{
					?>
						$('.payment_information').css('display', 'none');
					<?php
					}
				?>
				$("#adminForm").validationEngine('attach', {
					onValidationComplete: function(form, status){
						if (status == true) {
							form.on('submit', function(e) {
								e.preventDefault();
							});

							// Check and make sure at least one ticket type quantity is selected
							<?php
							if (!empty($this->ticketTypes))
							{
							?>
								var ticketTypesValue = '';
								var ticketName = '';
								var ticketQuantity = 0;
								$('select.ticket_type_quantity').each(function () {
									ticketName = $(this).attr('name');
									ticketQuantity = $(this).val();
									if (ticketQuantity > 0)
									{
										ticketTypesValue = ticketTypesValue + ticketName + ':' + ticketQuantity + ',';
									}
								});

								if (ticketTypesValue.length > 0)
								{
									ticketTypesValue = ticketTypesValue.substring(0, ticketTypesValue.length - 1);
								}

								$('#ticket_type_values').val(ticketTypesValue);
							<?php
							}
							?>

							form.find('#btn-submit').prop('disabled', true);

							var paymentMethod;

							if($('input:radio[name^=payment_method]').length)
							{
								paymentMethod = $('input:radio[name^=payment_method]:checked').val();
							}
							else
							{
								paymentMethod = $('input[name^=payment_method]').val();
							}

							// Stripe payment method
                            if (paymentMethod.indexOf('os_stripe') == 0)
                            {
                                // Old Stripe method
                                if (typeof stripePublicKey !== 'undefined' && $('#tr_card_number').is(":visible"))
                                {
                                    Stripe.card.createToken({
                                        number: $('#x_card_num').val(),
                                        cvc: $('#x_card_code').val(),
                                        exp_month: $('select[name^=exp_month]').val(),
                                        exp_year: $('select[name^=exp_year]').val(),
                                        name: $('#card_holder_name').val()
                                    }, stripeResponseHandler);

                                    return false;
                                }

                                // Stripe card element
                                if (typeof stripe !== 'undefined' && $('#stripe-card-form').is(":visible"))
                                {
                                    stripe.createToken(card).then(function(result) {
                                        if (result.error) {
                                            // Inform the customer that there was an error.
                                            //var errorElement = document.getElementById('card-errors');
                                            //errorElement.textContent = result.error.message;
                                            alert(result.error.message);
                                            $('#btn-submit').prop('disabled', false);
                                        } else {
                                            // Send the token to your server.
                                            stripeTokenHandler(result.token);
                                        }
                                    });

                                    return false;
                                }
                            }


							if (paymentMethod == 'os_squareup' && $('#tr_card_number').is(':visible'))
							{
								sqPaymentForm.requestCardNonce();

								return false;
							}

							return true;
						}
						return false;
					}
				});
				<?php
					if ($validateLoginForm)
					{
					?>
						$("#eb-login-form").validationEngine();
					<?php
					}
				?>
                buildStateFields('state', 'country', '<?php echo $selectedState; ?>');

                if (typeof stripe !== 'undefined' && $('#stripe-card-element').length > 0)
                {
                    var style = {
                        base: {
                            // Add your base input styles here. For example:
                            fontSize: '16px',
                            color: "#32325d",
                        }
                    };

                    // Create an instance of the card Element.
                    var card = elements.create('card', {style: style});

                    // Add an instance of the card Element into the `card-element` <div>.
                    card.mount('#stripe-card-element');
                }

				if ($('#email').val())
				{
					$('#email').validationEngine('validate');
				}

				<?php
				if ($this->amount == 0 && !empty($showPaymentInformation))
				{
				//The event is free because of discount, so we need to hide payment information
				?>
					$('.payment_information').css('display', 'none');
				<?php
				}
				?>
			})
			<?php
				echo EventbookingHelperPayments::writeJavascriptObjects();
			?>
		</script>
		<?php echo JHtml::_( 'form.token' ); ?>
	</form>
</div>
