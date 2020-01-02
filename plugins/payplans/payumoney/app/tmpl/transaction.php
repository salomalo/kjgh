<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($transaction_html) { ?>
<?php 
	$data = array('payment_key', 'gateway', 'mihpayid', 'mode', 'status', 'unmappedstatus',
					'key','txnid','amount',	'addedon', 'productinfo','firstname', 'email', 
					'phone', 'udf1', 'first_name', 'field9', 'PG_TYPE', 'encryptedPaymentId', 
					'bank_ref_num', 'bankcode', 'error', 'error_Message', 'amount_split', 
					'payuMoneyId', 'discount','net_amount_debit'); ?>
					
	<table class="app-table table table-hover">
		<thead>
			<tr>
				<td width="20%">
					<?php echo JText::_('Key'); ?>
				</td>
				<td>
					<?php echo JText::_('Value'); ?>
				</td>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($data as $key) { ?>
				<?php if (isset($transaction_html[$key])) { ?>
				<tr>
					<td>
						<?php echo $key;?>
					</td>
					<td>
						<?php echo $transaction_html[$key] ? $transaction_html[$key] : '&mdash;'; ?>
					</td>
				</tr>
				<?php } ?>
			<?php } ?>
		</tbody>
	</table>
<?php } ?>
