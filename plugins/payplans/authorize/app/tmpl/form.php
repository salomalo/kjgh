<?php
/**
* @package    PayPlans
* @copyright  Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license    GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="<?php echo JRoute::_('index.php');?>" method="post" data-authorize-form>

<div class="o-card o-card--borderless t-lg-mb--lg">
	<div class="o-card__header o-card__header--nobg t-lg-pl--no"><?php echo JText::_('COM_PP_CARD_DETAILS');?></div>

	<div class="o-card__body">
		<div class="o-form-group">
			<?php echo JText::sprintf('COM_PP_PAYMENT_VIA_AUTHORIZE', '<b>' . $this->html('html.amount', $amount, $invoice->getCurrency()) . '</b>'); ?>
		</div>

		<?php echo $this->html('form.card', array('card' => 'x_card_num', 'expire_month' => 'exp_month', 'expire_year' => 'exp_year', 'code' => 'x_card_code'),
			array('x_card_num' => $sandbox ? '4007000000027' : '', 'exp_month' => $sandbox ? '12' : '', 'exp_year' => $sandbox ? '2024' : '', 'x_card_code' => '123')
		); ?>
	</div>
</div>

<div class="o-card o-card--borderless t-lg-mb--lg">
	<div class="o-card__header o-card__header--nobg t-lg-pl--no"><?php echo JText::_('COM_PP_YOUR_DETAILS');?></div>

	<div class="o-card__body">
		<?php if ($params->get('company_name', false)) { ?>
			<?php echo $this->html('floatlabel.text', 'COM_PP_COMPANY_NAME_OPTIONAL', 'x_company', $sandbox ? 'Batman' : ''); ?>
		<?php } ?>

		<div class="o-grid o-grid--gutters">
			<div class="o-grid__cell">
				<?php echo $this->html('floatlabel.text', 'COM_PP_FIRST_NAME', 'x_first_name',  $sandbox ? 'John' : ''); ?>
			</div>

			<div class="o-grid__cell">
				<?php echo $this->html('floatlabel.text', 'COM_PP_LAST_NAME', 'x_last_name',  $sandbox ? 'doe' : ''); ?>
			</div>
		</div>

		<?php echo $this->html('floatlabel.text', 'COM_PP_EMAIL_ADDRESS', 'x_email',  $sandbox ? 'john@doe.com' : ''); ?>

		<?php if ($params->get('customer_id', false)) { ?>
			<?php echo $this->html('floatlabel.text', 'COM_PP_CUSTOMER_ID', 'x_cust_id', ''); ?>
		<?php } ?>

		<?php if ($params->get('phone_number', false) || $params->get('fax_number', false)) { ?>
		<div class="o-grid o-grid--gutters">
			<?php if ($params->get('phone_number', false)) { ?>
			<div class="o-grid__cell">
				<?php echo $this->html('floatlabel.text', 'COM_PP_TELEPHONE_NUMBER', 'x_phone',  $sandbox ? '01234567' : ''); ?>
			</div>
			<?php } ?>

			<?php if ($params->get('fax_number', false)) { ?>
			<div class="o-grid__cell">
				<?php echo $this->html('floatlabel.text', 'COM_PP_FAX_NUMBER', 'x_fax',  $sandbox ? '01234567' : ''); ?>
			</div>
			<?php } ?>
		</div>
		<?php } ?>

		<?php echo $this->html('floatlabel.text', 'COM_PP_ADDRESS', 'x_address', $sandbox ? 'Address line 1' : ''); ?>

		<div class="o-grid o-grid--gutters">
			<div class="o-grid__cell">
				<?php echo $this->html('floatlabel.text', 'COM_PP_CITY', 'x_city', $sandbox ? 'Gotham City' : ''); ?>
			</div>

			<div class="o-grid__cell">
				<?php echo $this->html('floatlabel.text', 'COM_PP_STATE', 'x_state', $sandbox ? 'State of Gotham' : ''); ?>
			</div>

			<div class="o-grid__cell">
				<?php echo $this->html('floatlabel.text', 'COM_PP_ZIP', 'x_zip', $sandbox ? '1234' : ''); ?>
			</div>
		</div>
		
		<?php echo $this->html('floatlabel.text', 'COM_PP_COUNTRY', 'x_country', $sandbox ? 'United States' : ''); ?>
	</div>
</div>

<div class="o-grid-sm">
	<?php echo $this->output('site/payment/default/cancel', array('payment' => $payment)); ?>

	<div class="o-grid-sm__cell o-grid-sm__cell--right">
		<button type="submit" class="btn btn-pp-primary btn--lg">
			<?php echo JText::_('COM_PP_COMPLETE_PAYMENT_BUTTON');?>
		</button>
	</div>
</div>

<?php echo $this->html('form.hidden', 'view', 'payment'); ?>
<?php echo $this->html('form.hidden', 'task', 'complete'); ?>
<?php echo $this->html('form.hidden', 'amount', $amount); ?>
<?php echo $this->html('form.hidden', 'payment_key', $payment->getKey()); ?>
</form>