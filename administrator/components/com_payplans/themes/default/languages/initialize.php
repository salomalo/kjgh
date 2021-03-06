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
<form name="adminForm" id="adminForm" method="post" data-table-grid>
	<div class="panel languages-loader" data-languages-wrapper>
		<div class="panel-body">
			<i class="fa fa-refresh fa-spin"></i>
			&nbsp;
			<?php echo JText::_('COM_PP_INITIALIZING_LANGUAGE_LIST');?>
		</div>
	</div>

	<div class="alert alert-danger hide" data-initialize-error></div>
</form>