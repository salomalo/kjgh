<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-stream-repost">

	<?php if ($message) { ?>
    <div class="es-stream-repost__text t-lg-mb--md"><?php echo $message;?></div>
    <?php } ?>

    <div class="es-stream-repost__meta">
        <div class="es-stream-repost__meta-inner">
			<div class="es-stream-repost__heading t-text--muted t-lg-mb--md">
				<i class="fa fa-retweet"></i>&nbsp; <?php echo JText::sprintf('COM_EASYSOCIAL_REPOSTED_FROM', $this->html('html.' . $sourceActor->getType(), $sourceActor->id));?>
			</div>
            
            <div class="es-stream-repost__content">
                <h4>
                    <a href="<?php echo $photo->getPermalink();?>"><?php echo $photo->_('title');?></a>
                </h4>

                <div>
                    <a href="<?php echo $photo->getPermalink();?>" data-es-photo="<?php echo $photo->id; ?>" title="<?php echo $this->html('string.escape', $photo->title . (($photo->caption!=='') ? ' - ' . $photo->caption : '')); ?>">
                        <img src="<?php echo $photo->getSource(); ?>" alt="<?php echo $this->html('string.escape', $photo->title . (($photo->caption!=='') ? ' - ' . $photo->caption : '')); ?>"/>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>