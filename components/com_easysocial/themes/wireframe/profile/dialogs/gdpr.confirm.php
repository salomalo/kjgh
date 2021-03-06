<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<dialog>
	<width>500</width>
	<height>200</height>
	<selectors type="json">
	{
		"{closeButton}" : "[data-close-button]",
		"{submitButton}" : "[data-submit-button]",
		"{form}": "[data-gdpr-request-form]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click" : function() {
			this.parent.close();
		},

		"{submitButton} click" : function() {
			this.form().submit();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_ES_GDPR_REQUEST_DATA'); ?></title>
	<content>
		<div class="t-lg-mb--xl"><?php echo JText::_('COM_ES_GDPR_DOWNLOAD_DESC1');?></div>
		<div class="t-lg-mt--xl"><?php echo JText::sprintf('COM_ES_GDPR_DOWNLOAD_DESC2', $email);?></div>

		<form action="<?php echo JRoute::_('index.php');?>" method="post" data-gdpr-request-form>
			<?php echo $this->html('form.action', 'profile', 'download'); ?>
		</form>
	</content>
	<buttons>
		<button type="button" class="btn btn-es-default btn-sm" data-close-button><?php echo JText::_('COM_EASYSOCIAL_CLOSE_BUTTON'); ?></button>
		<button type="button" class="btn btn-es-primary btn-sm" data-submit-button><?php echo JText::_('COM_ES_GDPR_REQUEST_DATA_BUTTON');?></button>
	</buttons>
</dialog>
