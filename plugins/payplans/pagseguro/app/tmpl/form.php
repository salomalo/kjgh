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
<div class="pp-result-container">
	<form action="<?php echo $postUrl;?>" method="post" data-pp-pageseguro-form>
		<div class="pp-result">
			<div class="pp-result__icons">
				<i class="fas fa-spinner fa-pulse"></i>
			</div>

			<div class="pp-result__title">
				<?php echo JText::_('COM_PP_REDIRECT_TO_MERCHANT_HEADING'); ?>
			</div>

			<div class="pp-result__desc">
				<?php echo JText::_('COM_PP_REDIRECT_TO_MERCHANT'); ?>
			</div>

			<div class="pp-result__action">
				<button type="submit" class="btn btn-pp-primary btn--lg">
					<?php echo JText::_('COM_PP_PROCEED_TO_PAYMENT_BUTTON');?>
				</button>
			</div>
		</div>
	</form>
</div>