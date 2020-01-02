<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="pp-checkout-container">
	<?php echo $this->output('site/checkout/default/header', array('step' => 'payment', 'title' => 'COM_PP_CHECKOUT_THANK_YOU')); ?>

	<div class="pp-checkout-wrapper">
		<div class="pp-checkout-wrapper__sub-content">
			<div class="pp-checkout-menu">
				<div class="t-lg-mb--lg">
					<div class="pp-result-container">
						<div class="pp-result">
							<div class="pp-result__image">
								<img src="<?php echo JURI::root();?>media/com_payplans/images/pp-thankyou.png" />
							</div>

							<div class="pp-result__title">
								<?php echo JText::_('COM_PP_THANK_YOU_FOR_PAYMENT'); ?>
							</div>

							<div class="pp-result__desc">
								<?php echo JText::_('COM_PP_THANK_YOU_NOTE');?>

								<?php if (!$this->my->id && !$registration->requireActivation() && !$registration->requireAdminActivation()) { ?>
									<br /><br /><?php echo JText::_('COM_PP_LOGIN_TO_SITE');?>
								<?php } ?>
							</div>

							<?php if (!$this->my->id && $registration->requireActivation()) { ?>
							<div class="pp-result__desc">
								<div class="t-lg-mt--xl t-lg-mb--xl">
									<span class="o-label o-label--warning o-label--lg"><?php echo JText::_('COM_PP_NOTE');?></span>
								</div>

								<?php echo JText::_('COM_PP_ACTIVATE_ACCOUNT_NOTE');?>
							</div>
							<?php } elseif (!$this->my->id && $registration->requireAdminActivation()) { ?>
							<div class="pp-result__desc">
								<div class="t-lg-mt--xl t-lg-mb--xl">
									<span class="o-label o-label--warning o-label--lg"><?php echo JText::_('COM_PP_NOTE');?></span>
								</div>

								<?php echo JText::_('COM_PP_ADMIN_APPROVAL_ACCOUNT_NOTE');?>
							</div>
							<?php } ?>

							<div class="pp-result__action t-lg-mt--xl">
								<a href="<?php echo PPR::_('index.php?option=com_payplans&view=dashboard');?>" class="btn btn-pp-primary btn--lg t-lg-mt--xl">
									<?php echo JText::_('COM_PP_PROCEED_TO_DASHBOARD_BUTTON'); ?>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>