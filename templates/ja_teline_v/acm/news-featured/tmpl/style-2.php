<?php
/**
 * ------------------------------------------------------------------------
 * JA Teline V Template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2018 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
*/
defined('_JEXEC') or die;

$aparams = JATemplateHelper::getParams();
$aparams->loadArray ($helper->toArray(true));
// get featured items
$catid = $aparams->get ('catid', 1);
$count_leading = $aparams->get ('featured_leading', 1);
$count_intro   = $aparams->get ('featured_intro', 3);
$count_links   = $aparams->get ('featured_links', 5);
$intro_columns = $aparams->get ('featured_intro_columns', 3);
$featured_count = $count_leading + $count_intro + $count_links;
$leading       = $intro = $links = array();

$items = JATemplateHelper::getArticles($aparams, $catid, $featured_count);

$i = 0;
foreach ($items as &$item) {

	if ($i < $count_leading) {
		$leading[] = $item;
	} elseif ($i < $count_leading + $count_intro) {
		$intro[] = $item;
	} else {
		$links[] = $item;
	}

	$i++;
}

// get global values
$show_intro = $aparams->get('show_intro');
$show_category = $aparams->get('show_category');
$show_readmore = $aparams->get('show_readmore');
$show_hits = $aparams->get('show_hits');
$show_author = $aparams->get('show_author');
$show_publish_date = $aparams->get('show_publish_date');
$block_position = $aparams->get('block_position');
$leading_title = $aparams->get('leading_title');
$show_leading_title = $aparams->get('show_leading_title');
?>

<div class="row style-2 magazine-featured equal-height">

		<div class="col col-md-8 magazine-featured-items">
			<?php if ($show_leading_title) : ?>
			<div class="magazine-section-heading">
				<h4><?php echo $leading_title; ?></h4>
			</div>
		<?php endif; ?>
			<?php if (count ($leading)): ?>
			<!-- Leading -->
				<?php
				$aparams->set('show_intro', $aparams->get('show_leading_intro', $show_intro));
				$aparams->set('show_category', $aparams->get('show_leading_category', $show_category));
				$aparams->set('show_readmore', $aparams->get('show_leading_readmore', $show_readmore));
				$aparams->set('block_position', $aparams->get('leading_block_position', $block_position));
				?>
			<div class="magazine-leading magazine-featured-leading">
				<?php foreach ($leading as $item) :?>
					<?php echo JATemplateHelper::render($item, 'joomla.content.intro', array('item' => $item, 'params' => $aparams)); ?>
				<?php endforeach; ?>
			</div>
			<!-- //Leading -->
			<?php endif ?>

			<?php if ($intro_count = count ($intro)): ?>
			<!-- Intro -->
				<?php
				$aparams->set('show_category', $aparams->get('show_link_category', $show_category));
				$aparams->set('show_hits', $aparams->get('show_link_hits', $show_hits));
				$aparams->set('show_author', $aparams->get('show_link_author', $show_author));
				$aparams->set('show_publish_date', $aparams->get('show_link_publish_date', $show_publish_date));
				$aparams->set('block_position', $aparams->get('link_block_position', $block_position));
				?>
			<div class="magazine-intro magazine-featured-intro">
				<?php $intro_index = 0; ?>
				<?php foreach ($intro as $item) : ?>
					<?php if($intro_index % $intro_columns == 0) : ?>
						<div class="row-articles">
					<?php endif ?>
					<div class="magazine-item col-sm-<?php echo round((12 / $intro_columns)) ?>">
						<?php echo JATemplateHelper::render($item, 'joomla.content.intro', array('item' => $item, 'params' => $aparams)); ?>
					</div>
					<?php $intro_index++; ?>
					<?php if(($intro_index % $intro_columns == 0) || $intro_index == $intro_count) : ?>
						</div>
					<?php endif ?>
				<?php endforeach; ?>
			</div>
			<!-- // Intro -->
			<?php endif ?>

		</div> <!-- //Left Column -->

		<?php if (count ($links)): ?>
		<?php
		$aparams->set('show_intro', 0);
		$aparams->set('show_readmore', 0);
		$aparams->set('show_category', $aparams->get('show_link_category', $show_category));
		$aparams->set('show_hits', $aparams->get('show_link_hits', $show_hits));
		$aparams->set('show_author', $aparams->get('show_link_author', $show_author));
		$aparams->set('show_publish_date', $aparams->get('show_link_publish_date', $show_publish_date));
		$aparams->set('block_position', $aparams->get('link_block_position', $block_position));
		?>
		<div class="col col-md-4 magazine-featured-links">
      <?php if($aparams->get('show_block_links_title')): ?>
			<div class="magazine-section-heading">
				<h4 class="line-head"><?php echo $aparams->get('block_links_title'); ?></h4>
			</div>
      <?php endif; ?>
			<!-- Links -->
			<div class="magazine-links">
				<?php foreach ($links as $item) :?>
					<?php echo JLayoutHelper::render('joomla.content.link.default', array('item' => $item, 'params' => $aparams)); ?>
				<?php endforeach; ?>
			</div>
			<!-- // Links -->
      
      <!-- Banner -->
      <div class="banner">
      <?php
        $document = JFactory::getDocument();
        $renderer = $document->loadRenderer('modules');
        $position = "banner-1";
        $options = array('style' => 'raw');
        echo $renderer->render($position, $options, null);
      ?>
      </div>
      <!-- // Banner -->
			<?php endif ?>
		</div> <!-- //Right Column -->

</div>