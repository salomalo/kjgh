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
<select data-easysocialbadges-select name="<?php echo $name;?>[]" class="pp-autocomplete o-form-control" multiple="multiple" <?php echo $attributes;?>>
	<?php foreach ($badges as $badge) { ?>
	<option value="<?php echo $badge->id;?>" <?php echo $value && in_array($badge->id, $value) ? 'selected="selected"' : '';?>><?php echo JText::_($badge->title);?></option>
	<?php } ?>
</select>