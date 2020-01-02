<?php
/**
 * Form Field class for the Joomla RAD.
 * Supports a radiolist custom field.
 *
 * @package     Joomla.RAD
 * @subpackage  Form
 */

class RADFormFieldRadio extends RADFormField
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	protected $type = 'Radio';

	/**
	 * Method to get the field input markup.
	 *
	 * @param EventbookingHelperBootstrap $bootstrapHelper
	 *
	 * @return string The field input markup.
	 */
	protected function getInput($bootstrapHelper = null)
	{
		// Add uk-radio if UIKit3 is used
		if ($bootstrapHelper && $bootstrapHelper->getBootstrapVersion() === 'uikit3')
		{
			$this->addClass('uk-radio');
		}

		$options    = (array) $this->getOptions();
		$value      = trim($this->value);

		$data = [
			'name'            => $this->name,
			'options'         => $options,
			'value'           => $value,
			'attributes'      => $this->buildAttributes(),
			'bootstrapHelper' => $bootstrapHelper,
			'row'             => $this->row,
		];

		return EventbookingHelperHtml::loadCommonLayout('fieldlayout/radio.php', $data);
	}

	/**
	 * Get radio options
	 *
	 * @return array
	 * @throws Exception
	 */
	protected function getOptions()
	{
		$user = JFactory::getUser();

		if (is_array($this->row->values))
		{
			$values = $this->row->values;
		}
		elseif (strpos($this->row->values, "\r\n") !== false)
		{
			$values = explode("\r\n", $this->row->values);
		}
		else
		{
			$values = array($this->row->values);
		}

		$quantityValues = explode("\r\n", $this->row->quantity_values);

		if ($this->row->quantity_field && count($values) && count($quantityValues) && $this->eventId && !$user->authorise('eventbooking.registrantsmanagement', 'com_eventbooking'))
		{

			$multilingualValues = array();

			if (JLanguageMultilang::isEnabled())
			{
				$multilingualValues = RADFormField::getMultilingualOptions($this->row->id);
			}

			$optionQuantities = [];

			for ($i = 0, $n = count($values); $i < $n; $i++)
			{
				if (isset($quantityValues[$i]))
				{
					$optionQuantity                    = $quantityValues[$i];
					$optionQuantities[trim($values[$i])] = $optionQuantity;
				}
				else
				{
					$optionQuantities[trim($values[$i])] = 0;
				}
			}

			$quantityValues = $optionQuantities;

			$values = EventbookingHelperHtml::getAvailableQuantityOptions($values, $quantityValues, $this->eventId, $this->row->id, false, $multilingualValues);
		}

		if (count($values) == 0)
		{
			JFactory::getApplication()->enqueueMessage('There is no available option left for the field ' . $this->title, 'warning');
		}

		$config  = EventbookingHelper::getConfig();
		$options = [];

		foreach ($values as $value)
		{
			$optionValue = trim($value);

			if (!$config->show_available_number_for_each_quantity_option || empty($quantityValues[$optionValue]) || $user->authorise('eventbooking.registrantsmanagement', 'com_eventbooking'))
			{
				$optionText = $optionValue;
			}
			else
			{
				$optionText = $optionValue . ' ' . JText::sprintf('EB_QUANTITY_OPTION_AVAILABLE', $quantityValues[$optionValue]);
			}

			$options[$optionValue] = $optionText;
		}

		return $options;
	}
}
