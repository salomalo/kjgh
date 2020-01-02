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
<select name="<?php echo $name;?>" class="pp-autocomplete o-form-control" <?php echo $attributes;?>>
	<?php foreach ($profileTypes as $profileType) { ?>
		<option value="<?php echo $profileType->id;?>" <?php echo $value == $profileType->id ? 'selected="selected"' : '';?>><?php echo JText::_($profileType->name);?></option>
	<?php } ?>
</select>