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

class EventbookingViewCompleteHtml extends RADViewHtml
{
	public $hasModel = false;

	/**
	 * Preview view data before it's being rendered
	 *
	 * @throws Exception
	 */
	protected function prepareView()
	{
		parent::prepareView();

		//Hardcoded the layout, it happens with some clients. Maybe it is a bug of Joomla core code, will find out it later
		$this->setLayout('default');

		$app              = JFactory::getApplication();
		$config           = EventbookingHelper::getConfig();
		$message          = EventbookingHelper::getMessages();
		$fieldSuffix      = EventbookingHelper::getFieldSuffix();
		$registrationCode = JFactory::getSession()->get('eb_registration_code', '');

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__eb_registrants')
			->where('registration_code = ' . $db->quote($registrationCode));
		$db->setQuery($query);
		$rowRegistrant = $db->loadObject();

		if (!$registrationCode || !$rowRegistrant)
		{
			$app->enqueueMessage(JText::_('EB_INVALID_REGISTRATION_CODE'), 'warning');
			$app->redirect(JUri::root(), 404);
		}

		if ($rowRegistrant->published == 0 && (in_array($rowRegistrant->payment_method, ['os_ideal', 'os_payu'])))
		{
			// Use online payment method and the payment is not success for some reason, we need to redirec to failure page
			$failureUrl = JRoute::_('index.php?option=com_eventbooking&view=failure&id=' . $rowRegistrant->id . '&Itemid=' . $this->Itemid, false, false);
			$app->enqueueMessage(JText::_('EB_PAYMENT_ERROR_MESSAGE'), 'warning');
			$app->redirect($failureUrl);
		}

		$rowEvent = EventbookingHelperDatabase::getEvent($rowRegistrant->event_id);

		if (strpos($rowRegistrant->payment_method, 'os_offline') !== false
			&& $rowRegistrant->published == 0
			&& $rowEvent->offline_payment_registration_complete_url
			&& filter_var($rowEvent->offline_payment_registration_complete_url, FILTER_VALIDATE_URL))
		{
			$app->redirect($rowEvent->offline_payment_registration_complete_url);
		}
		elseif ($rowEvent->registration_complete_url && filter_var($rowEvent->registration_complete_url, FILTER_VALIDATE_URL))
		{
			$app->redirect($rowEvent->registration_complete_url);
		}

		if ($rowRegistrant->published == 0 && strpos($rowRegistrant->payment_method, 'os_offline') !== false)
		{
			$offlineSuffix = str_replace('os_offline', '', $rowRegistrant->payment_method);

			if ($offlineSuffix && $fieldSuffix && EventbookingHelper::isValidMessage($message->{'thanks_message_offline' . $offlineSuffix . $fieldSuffix}))
			{
				$thankMessage = $message->{'thanks_message_offline' . $offlineSuffix . $fieldSuffix};
			}
			elseif ($offlineSuffix && EventbookingHelper::isValidMessage($message->{'thanks_message_offline' . $offlineSuffix}))
			{
				$thankMessage = $message->{'thanks_message_offline' . $offlineSuffix};
			}
			elseif ($fieldSuffix && EventbookingHelper::isValidMessage($rowEvent->{'thanks_message_offline' . $fieldSuffix}))
			{
				$thankMessage = $rowEvent->{'thanks_message_offline' . $fieldSuffix};
			}
			elseif ($fieldSuffix && EventbookingHelper::isValidMessage($message->{'thanks_message_offline' . $fieldSuffix}))
			{
				$thankMessage = $message->{'thanks_message_offline' . $fieldSuffix};
			}
			elseif (EventbookingHelper::isValidMessage($rowEvent->thanks_message_offline))
			{
				$thankMessage = $rowEvent->thanks_message_offline;
			}
			else
			{
				$thankMessage = $message->thanks_message_offline;
			}
		}
		else
		{
			if ($fieldSuffix && EventbookingHelper::isValidMessage($rowEvent->{'thanks_message' . $fieldSuffix}))
			{
				$thankMessage = $rowEvent->{'thanks_message' . $fieldSuffix};
			}
			elseif ($fieldSuffix && EventbookingHelper::isValidMessage($message->{'thanks_message' . $fieldSuffix}))
			{
				$thankMessage = $message->{'thanks_message' . $fieldSuffix};
			}
			elseif (EventbookingHelper::isValidMessage($rowEvent->thanks_message))
			{
				$thankMessage = $rowEvent->thanks_message;
			}
			else
			{
				$thankMessage = $message->thanks_message;
			}
		}

		$replaces = EventbookingHelperRegistration::getRegistrationReplaces($rowRegistrant, $rowEvent, 0, $config->multiple_booking, false);

		foreach ($replaces as $key => $value)
		{
			$key          = strtoupper($key);
			$thankMessage = str_ireplace("[$key]", $value, $thankMessage);
		}

		$thankMessage = EventbookingHelperRegistration::processQRCODE($rowRegistrant, $thankMessage);
		$trackingCode = $config->conversion_tracking_code;

		if (!empty($trackingCode))
		{
			$filterInput = JFilterInput::getInstance();

			$replaces['total_amount']           = $filterInput->clean($replaces['total_amount'], 'float');
			$replaces['discount_amount']        = $filterInput->clean($replaces['discount_amount'], 'float');
			$replaces['tax_amount']             = $filterInput->clean($replaces['tax_amount'], 'float');
			$replaces['amount']                 = $filterInput->clean($replaces['amount'], 'float');
			$replaces['payment_processing_fee'] = $filterInput->clean($replaces['payment_processing_fee'], 'float');

			foreach ($replaces as $key => $value)
			{
				$key          = strtoupper($key);
				$trackingCode = str_ireplace("[$key]", $value, $trackingCode);
			}
		}


		JPluginHelper::importPlugin('eventbooking');
		$app->triggerEvent('onPrepareRegistrationCompleteMessage', [$rowRegistrant, &$thankMessage]);

		$this->rowRegistrant          = $rowRegistrant;
		$this->message                = $thankMessage;
		$this->registrationCode       = $registrationCode;
		$this->print                  = $this->input->getInt('print', 0);
		$this->conversionTrackingCode = $trackingCode;
		$this->showPrintButton        = $config->get('show_print_button', '1');
		$this->bootstrapHelper        = EventbookingHelperBootstrap::getInstance();

		// Reset cart
		if ($config->multiple_booking)
		{
			$cart = new EventbookingHelperCart();
			$cart->reset();
		}
	}
}
