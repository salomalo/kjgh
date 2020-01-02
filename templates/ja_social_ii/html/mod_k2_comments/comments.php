<?php
/*
 * ------------------------------------------------------------------------
 * JA Social II template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
*/

// no direct access
defined('_JEXEC') or die;

?>

<div id="k2ModuleBox<?php echo $module->id; ?>" class="k2LatestCommentsBlock<?php if($params->get('moduleclass_sfx')) echo ' '.$params->get('moduleclass_sfx'); ?>">

	<?php if(count($comments)): ?>
	<ul>
		<?php foreach ($comments as $key=>$comment):	?>
		<li class="<?php echo ($key%2) ? "odd" : "even"; if(count($comments)==$key+1) echo ' lastItem'; ?>">
			<?php if($comment->userImage): ?>
			<a class="k2Avatar lcAvatar" href="<?php echo $comment->link; ?>" title="<?php echo K2HelperUtilities::cleanHtml($comment->commentText); ?>">
				<img src="<?php echo $comment->userImage; ?>" alt="<?php echo JFilterOutput::cleanText($comment->userName); ?>" style="width:<?php echo $lcAvatarWidth; ?>px;height:auto;" />
			</a>
			<?php endif; ?>
			
			<?php if($params->get('commenterName')): ?>
			<span class="lcUsername">
				<?php if(isset($comment->userLink)): ?>
				<a rel="author" href="<?php echo $comment->userLink; ?>"><?php echo $comment->userName; ?></a>
				<?php elseif($comment->commentURL): ?>
				<a target="_blank" rel="nofollow" href="<?php echo $comment->commentURL; ?>"><?php echo $comment->userName; ?></a>
				<?php else: ?>
				<?php echo $comment->userName; ?>
				<?php endif; ?>
			</span>
			<?php endif; ?>
			
			<?php if($params->get('commentDate')): ?>
			<span class="lcCommentDate">
				<?php if($params->get('commentDateFormat') == 'relative'): ?>
				<?php echo $comment->commentDate; ?>
				<?php else: ?>
				<?php echo JHTML::_('date', $comment->commentDate, JText::_('K2_DATE_FORMAT_LC3')); ?>
				<?php endif; ?>
			</span>
			<?php endif; ?>
	
			<div class="commenDetail">
				<?php if($params->get('commentLink')): ?>
				<a class="lcComment" href="<?php echo $comment->link; ?>"><span><?php echo $comment->commentText; ?></span></a>
				<?php else: ?>
				<span><?php echo $comment->commentText; ?></span>
				<?php endif; ?>

				<div class="clr"></div>

				<?php if($params->get('itemTitle')): ?>
				<span class="lcItemTitle"><i class="fa fa-file-text-o"></i> <a href="<?php echo $comment->itemLink; ?>"><?php echo $comment->title; ?></a></span>
				<?php endif; ?>

				<?php if($params->get('itemCategory')): ?>
				<span class="lcItemCategory"><i class="fa fa-folder-o"></i> <a href="<?php echo $comment->catLink; ?>"><?php echo $comment->categoryname; ?></a></span>
				<?php endif; ?>
			</div>
		</li>
		<?php endforeach; ?>
		<li class="clearList"></li>
	</ul>
	<?php endif; ?>

	<?php if($params->get('feed')): ?>
	<div class="k2FeedIcon">
		<a href="<?php echo JRoute::_('index.php?option=com_k2&view=itemlist&format=feed&moduleID='.$module->id); ?>" title="<?php echo JText::_('K2_SUBSCRIBE_TO_THIS_RSS_FEED'); ?>">
			<span><?php echo JText::_('K2_SUBSCRIBE_TO_THIS_RSS_FEED'); ?></span>
		</a>
		<div class="clr"></div>
	</div>
	<?php endif; ?>

</div>
