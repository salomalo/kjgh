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
<script type="text/javascript">
$(document).ready(function() {

	<?php if (PP_INSTALLER == 'full') { ?>
		$('[data-installation-form]').submit();
	<?php } ?>

	<?php if (PP_INSTALLER == 'launcher') { ?>

	var loading = $('[data-checking]');
	var form = $('[data-installation-form]');

	// Hide submit button
	$('[data-installation-submit]').addClass('hide');

	// Validate api key
	$.ajax({
		type: 'POST',
		url: '<?php echo JURI::root();?>administrator/index.php?option=com_payplans&ajax=1&controller=license&task=verify',
		data: {
			"key": $('[data-api-key]').val()
		}
	}).done(function(result) {

		// Hide the loading
		loading.addClass('hide');

		// User is not allowed to install
		if (result.state == 400) {

			// Set the error message
			$('[data-api-errors]').removeClass('hide');
			$('[data-error-message]').html(result.message);
			$('[data-source-method]').addClass('hide');
			return false;
		}

		// Valid licenses
		// Valid licenses
		if (result.state == 200) {
			var submit = $('[data-installation-submit]');
			var licenses = $('[data-licenses]');
			var licensePlaceholder = $('[data-licenses-placeholder]');

			submit.removeClass('hide');

			// If there are multiple licenses, we need to request them to submit
			if (result.licenses.length > 1) {
				licenses.removeClass('hide');

				var output = $('<div>').html(result.html);
				output.find('select')
					.css('font-size', '13px')
					.css('padding', '6px')
					.css('width', '100%');

				licensePlaceholder.append(output);

				// Change the behavior of form submission
				submit.on('click', function() {
					form.submit();
				});
				return;
			}

			// If the user only has 1 license, just submit this immediately.
			licensePlaceholder.append(result.html);
			form.submit();
		}
	});
	<?php } ?>
});
</script>

<form action="index.php?option=com_payplans" method="post" name="installation" data-installation-form>
	<div class="hide alert alert-danger" data-source-errors data-api-errors>
		<p data-error-message style="margin-bottom: 15px;"><?php echo JText::_('COM_PP_INSTALLATION_METHOD_API_KEY_INVALID'); ?></p>
		<br />
		<a href="https://stackideas.com/forums" class="btn btn-danger btn-sm" target="_blank"><?php echo JText::_('COM_PP_INSTALLATION_CONTACT_SUPPORT');?></a>
	</div>

	<div class="form-inline hide" data-licenses>
		<div>
			<h3 style="text-decoration: underline;"><?php echo JText::_('Please Select A License');?></h3>
			<p style="margin: 25px 0;"><?php echo JText::_('Our system detected multiple licenses from your account. In order to proceed with the installation, please choose a license to associate with your installation');?></p>
			<div data-licenses-placeholder></div>
		</div>
	</div>

	<div class="installation-methods">
		<?php if (PP_INSTALLER == 'launcher') { ?>
		<div class="text-center" data-checking>
			<b class="ui loader" style="width: 48px; height: 48px;"></b>&nbsp;
			<b style="display: block; color: #666;margin-top: 20px;font-size: 24px;">Checking for valid licenses ...</b>
		</div>
		<input type="hidden" name="method" value="network" />
		<input type="text" value="<?php echo PP_KEY;?>" name="apikey" class="hidden" data-api-key />
		<?php } ?>

		<?php if (PP_INSTALLER == 'full' || PP_BETA) { ?>
		<input type="hidden" name="method" value="directory" />
		<?php } ?>
	</div>

	<input type="hidden" name="option" value="com_payplans" />
	<input type="hidden" name="active" value="<?php echo $active; ?>" />
	<input type="hidden" name="update" value="<?php echo $update;?>" />
</form>
