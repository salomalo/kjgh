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
<table class="app-table table">
	<thead>
		<tr>
			<th class="center">
				<?php echo JText::_('COM_PP_CHART_COLUMN_DATE'); ?>
			</th>
			<th class="center">
				<?php echo JText::_('COM_PP_CHART_COLUMN_TOTAL_' . strtoupper($type)); ?>
			</th>
		</tr>
	</thead>
	<tbody>
	<?php if ($results) { ?>
		<?php $i = count($results) - 1; ?>
		<?php foreach ($results as $sale) { ?>
		<tr data-chart-list data-index="<?php echo $i; ?>"<?php echo !$sale['total_1'] ? ' class="t-hidden"' : ''; ?>>
			<th class="center">
				<?php echo $sale['date']->format('d F Y'); ?>
			</th>
			<th class="center">
				<?php echo $sale['total_1']; ?>
			</th>
		</tr>
		<?php $i--; ?>
		<?php } ?>
	<?php } ?>
	</tbody>
</table>