<?php
/**
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
defined('_JEXEC') or die;
?>

<?php
  $slideshow_style = $helper->get('slideshow-style');
  $items_position = $helper->get('position');
	$autoPlay = $helper->get('enable-autoplay');
  $slideshow_desktop_height = $helper->get('desktop-height');
	$mods = JModuleHelper::getModules($items_position);
?>
<div class="section-inner <?php echo $helper->get('block-extra-class'); ?>">	
	<div class="acm-container-slide slide-1 <?php echo $slideshow_style; ?>">
	
	  <!-- BEGIN: CONTAINER SLIDE [STYLE 3] -->
	  <div id="acm-slideshow-<?php echo $module->id ?>" class="carousel slide carousel-fade" data-ride="carousel" data-interval="<?php echo $autoPlay ;?>">
	
	    <div class="carousel-inner" <?php if ( trim($slideshow_desktop_height) ) : ?>style="height: <?php echo $helper->get('desktop-height')?>px;"<?php endif ?>>
				<?php
				echo $helper->renderModules($items_position,
					array(
						'style'=>'ACMContainerItems',
						'active'=>0,
						'tag'=>'div',
						'class'=>'item'
					))
				?>
	    </div>
			
			<div class="carousel-ctrl">
				<strong class="indicators-menu">MENU</strong>
				<ol class="carousel-indicators mn-carousel">
					<?php $i = 0; foreach ($mods as $mod) : $modparams = new JRegistry($mod->params) ;?>
						<li data-target="#acm-slideshow-<?php echo $module->id ?>" data-slide-to="<?php echo $i; ?>" class="<?php if($i++==0) echo 'active' ?>"><?php echo $mod->title ?></li>
					<?php endforeach; ?>
				</ol>
			</div>
	    
	  	<?php if($helper->get('enable-controls')): ?>
		 	<a data-slide="prev" role="button" href="#acm-slideshow-<?php echo $module->id ?>" class="left carousel-control"><i class="fa fa-angle-left"></i></a>
		 	<a data-slide="next" role="button" href="#acm-slideshow-<?php echo $module->id ?>" class="right carousel-control"><i class="fa fa-angle-right"></i></a>
		 	<?php endif; ?>
	
	  </div>
	  <!-- END: CONTAINER SLIDE [STYLE 3] -->
	  
	</div>
</div>