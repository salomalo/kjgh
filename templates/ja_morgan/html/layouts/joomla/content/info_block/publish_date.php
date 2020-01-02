<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;
?>
			<dd class="published hasTooltip" title="<?php echo JText::sprintf('COM_CONTENT_PUBLISHED_DATE_ON', ''); ?>">
				<i class="ion-md-calendar"></i>
				<time datetime="<?php echo JHtml::_('date', $displayData['item']->publish_up, 'c'); ?>" itemprop="datePublished">
					<?php echo JHtml::_('date', $displayData['item']->publish_up, "M d, Y"); ?>
          <meta  itemprop="datePublished" content="<?php echo JHtml::_('date', $displayData['item']->publish_up, 'c'); ?>" />
          <meta  itemprop="dateModified" content="<?php echo JHtml::_('date', $displayData['item']->publish_up, 'c'); ?>" />
				</time>
			</dd>
