<?php
/**
* @package      PayPlans
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="o-control-input">
	<select class="o-form-control" name="<?php echo $name;?>" <?php echo $multiple ? ' multiple="multiple" style="min-height: 60px;"' : '';?> <?php echo $attributes; ?>>

		<?php if (!$multiple) { ?>
			<option value=""><?php echo JText::_('COM_PP_SELECT_STATUS'); ?></option>
		<?php } ?>

		<?php foreach ($options as $key => $value) { ?>
		<option value="<?php echo $key;?>" <?php echo $selected && in_array($key, $selected) ? 'selected="selected"' : '';?>><?php echo $value;?></option>
		<?php } ?>
	</select>
</div>