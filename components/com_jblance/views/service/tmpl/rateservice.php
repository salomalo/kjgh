<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	03 February 2015
 * @file name	:	views/service/tmpl/rateservice.php
 * @copyright   :	Copyright (C) 2012 - 2019 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Rate user (jblance)
 */
 defined('_JEXEC') or die('Restricted access');

 JHtml::_('jquery.framework');
 JHtml::_('behavior.formvalidator');

 $doc = JFactory::getDocument();
 $doc->addScript("components/com_jblance/js/barrating.js");
 $doc->addStyleSheet("components/com_jblance/css/barrating.css");
 $doc->addStyleSheet("components/com_jblance/css/customer/rateservice.css");

 $select 		 = JblanceHelper::get('helper.select');		// create an instance of the class SelectHelper
 $row			 = $this->row;
 $config		 = JblanceHelper::getConfig();
 $showUsername 	 = $config->showUsername;
 $nameOrUsername = ($showUsername) ? 'username' : 'name';

 //find the current user is the buyer or seller. If the user is buyer, then the target is seller and vice versa
 $user = JFactory::getUser();
 if($user->id == $row->buyer_id){
 	$actor = $row->buyer_id;
 	$target = $row->seller_id;
 	$target_rate_type = 'COM_JBLANCE_FREELANCER';	//freelancer is equal to seller
 }
 elseif($user->id == $row->seller_id){
 	$actor = $row->seller_id;
 	$target = $row->buyer_id;
 	$target_rate_type = 'COM_JBLANCE_BUYER';
 }
?>
<script type="text/javascript">
<!--
function validateForm(f){
	var valid = document.formvalidator.isValid(f);

	if(valid == true){

    }
    else {
		alert('<?php echo JText::_('COM_JBLANCE_FIEDS_HIGHLIGHTED_RED_COMPULSORY', true); ?>');
		return false;
    }
	return true;
}
jQuery(document).ready(function($){
    $("#quality_clarity").barrating("show", {
        showSelectedRating:true,
    });
    $("#communicate").barrating("show", {
        showSelectedRating:true
    });
    $("#expertise_payment").barrating("show", {
        showSelectedRating:true
    });
    $("#professional").barrating("show", {
        showSelectedRating:true
    });
    $("#hire_work_again").barrating("show", {
        showSelectedRating:true
    });
});
//-->
</script>
<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="userFormProject" id="userFormProject" class="form-validate form-horizontal" onsubmit="return validateForm(this);">
	<div class="jbl_h3title"><?php echo JText::_('COM_JBLANCE_RATE_USER'); ?></div>
	<div class='top-line'></div>
	<div class="control-group">
		<p style='color:#FF6010;font-size:18px;font-family: MicrosoftYaHei;margin-top:15px'>
<!--            深圳<span style='font-weight: 600'>·</span>龙华 -->
            <?php echo JblanceHelper::getLocationNames($row->id_location,'only-location','<span style="font-size: 20px;">&#8901;</span>'); ?>//<?php echo $row->service_title; ?>
		</p>
		<label class="control-label nopadding left-label-title" style='text-align: left'>会员昵称: </label>
		<div class="controls font16 service">
			<div style='margin-top:17px'>
				<span><?php
                    $target_user = JFactory::getUser($target);
                    echo $target_user->$nameOrUsername;//.' ('.JText::_($target_rate_type).')'; ?></span>
				<span>(<?php echo $row->group_name;?>)</span>
			</div>
		</div>
		<!-- 设计稿没有 暂时隐藏 -->
		<div style='display:none' class="controls font16">
			<span>天天</span><span>(服务商)</span>
			<?php echo $row->service_title; ?>
		</div>
		<!-- 设计稿没有 暂时隐藏 -->
		<label style='display:none' class="control-label nopadding left-label-title"><?php echo JText::_('COM_JBLANCE_SERVICE_TITLE'); ?>: </label>
		<!-- 设计稿没有 暂时隐藏 -->
		<div style='display:none' class="controls font16">
			<?php echo $row->service_title; ?>
		</div>
	</div>
	<!-- 设计稿没有 暂时隐藏 -->
	<div style='display:none' class="control-group">
		<label class="control-label nopadding"><?php echo JText::_('COM_JBLANCE_NAME'); ?>: </label>
		<div class="controls">
			<?php
			$target_user = JFactory::getUser($target);
			echo $target_user->$nameOrUsername;//.' ('.JText::_($target_rate_type).')'; ?>
		</div>
	</div>

	<div style='margin-left:166px'>
		<div class="control-group">
			<label class="control-label" style='font-size: 14px;font-family: MicrosoftYaHei;color:#666666;width: auto' for="quality_clarity"><?php echo ($target_rate_type == 'COM_JBLANCE_BUYER') ? JText::_('COM_JBLANCE_CLARITY_SPECIFICATION') : JText::_('COM_JBLANCE_QUALITY_OF_WORK'); ?>: </label>
			<div class="controls brating">
				<?php $rating = $select->getSelectRating('quality_clarity', 5);
				echo $rating; ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" style='font-size: 14px;font-family: MicrosoftYaHei;color:#666666;width: auto' for="communicate"><?php echo JText::_('COM_JBLANCE_COMMUNICATION'); ?>: </label>
			<div class="controls brating">
				<?php $rating = $select->getSelectRating('communicate', 5);
				echo $rating; ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" style='font-size: 14px;font-family: MicrosoftYaHei;color:#666666;width: auto' for="expertise_payment"><?php echo ($target_rate_type == 'COM_JBLANCE_BUYER') ? JText::_('COM_JBLANCE_PAYMENT_PROMPTNESS') : JText::_('COM_JBLANCE_EXPERTISE'); ?>: </label>
			<div class="controls brating">
				<?php $rating = $select->getSelectRating('expertise_payment', 5);
				echo $rating; ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" style='font-size: 14px;font-family: MicrosoftYaHei;color:#666666;width: auto' for="professional"><?php echo JText::_('COM_JBLANCE_PROFESSIONALISM'); ?>: </label>
			<div class="controls brating">
				<?php $rating = $select->getSelectRating('professional', 5);
				echo $rating; ?>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" style='font-size: 14px;font-family: MicrosoftYaHei;color:#666666;width: auto' for="hire_work_again"><?php echo ($target_rate_type == 'COM_JBLANCE_BUYER') ? JText::_('COM_JBLANCE_WORK_AGAIN') : JText::_('COM_JBLANCE_HIRE_AGAIN'); ?>: </label>
			<div class="controls brating">
				<?php $rating = $select->getSelectRating('hire_work_again', 5);
				echo $rating; ?>
			</div>
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" style='font-size: 14px;font-family: MicrosoftYaHei;color:#666666;width: auto' for="comments"><?php echo JText::_('COM_JBLANCE_COMMENTS'); ?>: </label>
		<div class="controls">
			<textarea name="comments" rows="5" class="input-xlarge required textarea-content"></textarea>
		</div>
	</div>
	<div class="form-actions">
		<button class='cancel-btn'>取消</button>
		<input type="submit" value="<?php echo JText::_('COM_JBLANCE_SUBMIT'); ?>" class="btn btn-primary submit-btn" />
	</div>

	<input type="hidden" name="option" value="com_jblance" />
	<input type="hidden" name="task" value="service.saverateuser" />
	<!-- <input type="hidden" name="id" value="0" /> -->
	<input type="hidden" name="actor" value="<?php echo $actor; ?>" />
	<input type="hidden" name="target" value="<?php echo $target; ?>" />
	<input type="hidden" name="project_id" value="<?php echo $row->order_id; ?>" />
	<input type="hidden" name="rate_type" value="<?php echo $target_rate_type; ?>" />
	<input type="hidden" name="type" value="COM_JBLANCE_SERVICE" />
	<?php echo JHtml::_('form.token'); ?>
</form>
