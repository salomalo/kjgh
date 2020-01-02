<div class="mt-page-index mt-template-<?php echo $this->template; ?> cat-id-<?php echo $this->cat->cat_id ;?> tlcat-id-<?php echo $this->tlcat_id ;?> row-fluid">
	<?php

	if( !empty($this->page_heading) ) {
		?>
		<h1><?php echo $this->page_heading; ?></h1>
		<?php
	}

	if ( (isset($this->cat->cat_image) && $this->cat->cat_image <> '') || (isset($this->cat->cat_desc) && $this->cat->cat_desc <> '') ) {
		?>
		<div id="cat-header">
			<h1 class="contentheading"><?php echo htmlspecialchars($this->cat->cat_name) ?><?php echo ($this->mtconf['show_category_rss']) ? $this->plugin('showrssfeed','new') : ''; ?></h1>
		</div>
		<?php
		echo '<div class="mt-index-self-desc">';
		if (isset($this->cat->cat_image) && $this->cat->cat_image <> '') {
			echo '<div class="index-image">';
			echo '<img src="' . $this->config->getjconf('live_site') . $this->config->get('relative_path_to_cat_small_image') . $this->cat->cat_image . '" alt="' . htmlspecialchars($this->cat->cat_name) . '" />';
			echo '</div>';
		}
		if ( isset($this->cat->cat_desc) && $this->cat->cat_desc <> '') {	echo $this->cat->cat_desc; }
		echo '</div>';
	}

	if( $this->config->getTemParam('displaySearch','1') ) { $this->display( 'sub_search.tpl.php' ); }

	if( $this->config->getTemParam('displayAlphaIndex','1') ) { $this->display( 'sub_alphaIndex.tpl.php' ); }

	if ( $this->config->get('display_categories') && is_array($this->categories)):
		?>
		<div class="title"><?php echo JText::_( 'COM_MTREE_CATEGORIES' ); ?></div>
		<?php
		$i = 0;

		foreach ($this->categories as $cat):

			if (!in_array($cat->cat_id, $this->authorised_cat_ids) )
			{
				continue;
			}

			$urlCategory = JRoute::_("index.php?option=$this->option&task=listcats&cat_id=$cat->cat_id&Itemid=$this->Itemid");

			if ( ($i % $numOfColumns) == 0) echo '<div class="row-fluid"><div class="span12"><div class="row-fluid">';
			echo '<div class="category span' . round(12/$numOfColumns) . '">';

			?>
			<div
				class="mt-index-cat-card"
				style="background-image: url('<?php
				if(!empty($cat->cat_image)) {
					echo $this->config->getjconf('live_site') . $this->config->get('relative_path_to_cat_original_image') . rawurlencode($cat->cat_image) . '';
				}
				?>');" alt="<?php echo htmlspecialchars($cat->cat_name); ?>">

				<?php
				echo '<a class="mt-index-cat-overlay-link" href="' . $urlCategory . '">';
				echo '</a>';

				?>

				<h2><?php
					echo htmlspecialchars($cat->cat_name);
					if($displayIndexCatCount) {
						echo '<span>' . MText::sprintf('X_CATEGORIES', $this->tlcat_id, $cat->cat_cats) . '</span>';
					}
					if($displayIndexListingCount) {
						echo '<span>' . MText::sprintf('X_LISTINGS', $this->tlcat_id, $cat->cat_links) . '</span>';
					}
					?></h2>

			</div>

			<?php
			/*
			 * Category Details
			 */
			$htmlCatDetails = '';
			if(!empty($cat->cat_desc) && $this->config->getTemParam('displayCatDesc','0')){
				$htmlCatDetails .= '<div class="mt-index-cat-desc">' . $cat->cat_desc . '</div>';
			}

			if (isset($this->sub_cats) && isset($this->sub_cats[$cat->cat_id]) && count($this->sub_cats[$cat->cat_id]) > 0) {
				$j = 0;
				$htmlCatDetails .= '<div class="mt-index-subcats">';

				foreach ($this->sub_cats[$cat->cat_id] AS $sub_cat):
					$htmlCatDetails .= '<a href="index.php?option=' . $this->option . '&task=listcats&cat_id=' . $sub_cat->cat_id . '&Itemid=' . $this->Itemid . '">';
					$htmlCatDetails .= htmlspecialchars($sub_cat->cat_name);
					$htmlCatDetails .= '</a>';
					$j++;
					if ($this->sub_cats_total[$cat->cat_id] > $j) {
						$lastSubCat = end($this->sub_cats[$cat->cat_id]);
						if ($j >= $numOfSubcatsToDisplay || $lastSubCat->cat_id == $sub_cat->cat_id) {
							$htmlCatDetails .= '<a href="' . $urlCategory . '">';
							$htmlCatDetails .= '...';
							$htmlCatDetails .= '</a>';
						} else {
							$htmlCatDetails .= ', ';
						}
					} elseif($this->sub_cats_total[$cat->cat_id] == $j) {
						// No more sub-categories
					}
				endforeach;
				$htmlCatDetails .= '</div>';
			}
			if(isset($this->cat_links) && !empty($this->cat_links[$cat->cat_id])) {
				$htmlCatDetails .= '<ul class="mt-index-cat-listings">';
				foreach($this->cat_links[$cat->cat_id] AS $cat_link) {
					$htmlCatDetails .= '<li>';
					$htmlCatDetails .= '<a href="index.php?option=' . $this->option . '&task=viewlink&link_id=' . $cat_link->link_id . '&Itemid=' . $this->Itemid . '">';
					$htmlCatDetails .= htmlspecialchars($cat_link->link_name);
					$htmlCatDetails .= '</a>';
					$htmlCatDetails .= '</li>';
				}
				$htmlCatDetails .= '</ul>';
			}

			if( !empty($htmlCatDetails) ) {
				echo '<div class="mt-index-cat-details">';
				echo $htmlCatDetails;
				echo '</div>';
			}

			echo '</div>';

			if ( ($i++ % $numOfColumns) == ($numOfColumns-1) || $i == count($this->authorised_cat_ids)) echo '</div></div></div>';
		endforeach;
	endif;

	// Browse by Tags
	include $this->loadTemplate( 'sub_indexBrowseByTags.tpl.php' ) ;

	if ( $this->show_add_listing_link ) {
		?>
		<p class="pull-right">
			<a href="<?php echo JRoute::_( "index.php?option=com_mtree&task=addlisting&cat_id=$this->cat_id&Itemid=$this->Itemid" ); ?>" class="btn btn-small">
				<span class="icon-plus"></span>
				<?php echo MText::_( 'ADD_YOUR_LISTING_HERE', $this->tlcat_id ); ?>
			</a>
		</p>
		<?php
	}
	?>
</div>
