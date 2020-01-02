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
<dialog>
	<width>500</width>
	<height>250</height>
	<selectors type="json">
	{
		"{cancelButton}": "[data-cancel-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancelButton} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_('Cancel Subscription'); ?></title>
	<content type="html">
			<p class="t-lg-mb--xl">
				<?php echo JText::_('The system encountered an error and was unable to locate any order for this subscription. If you believe this is a mistake, please get in touch with our support team.');?>
			</p>
		</form>
	</content>
	<buttons>
		<button data-cancel-button type="button" class="btn btn-pp-default-o"><?php echo JText::_('COM_PP_CLOSE_BUTTON'); ?></button>
	</buttons>
</dialog>
