<?php
/**
 * ------------------------------------------------------------------------
 * JA Morgan Template
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

<div class="acm-accordion panel-group" id="acm-accordion<?php echo $module->id; ?>" role="tablist" aria-multiselectable="true">
  <?php $count = $helper->getRows('data.accordion-name'); ?>
  <?php for ($i=0; $i<$count; $i++) : ?>
  <div class="panel panel-default">
    <div class="panel-heading" role="tab" id="heading-<?php echo $module->id; ?><?php echo $i ?>">
      <h6 class="panel-title">
        <a class="<?php if($i!=0) echo 'collapsed' ?>" role="button" data-toggle="collapse" data-parent="#acm-accordion<?php echo $module->id; ?>" href="#collapse-<?php echo $module->id; ?><?php echo $i ?>" 
          aria-expanded="<?php if($i==0): echo 'true'; else: echo 'false'; endif; ?>" aria-controls="collapse-<?php echo $module->id; ?><?php echo $i ?>">
          <span class="marker">
            <span class="marker-close"><span class="fa fa-plus"></span></span>
            <span class="marker-open"><span class="fa fa-minus"></span></span>
          </span>
          <?php echo $helper->get('data.accordion-name', $i) ; ?>
        </a>
      </h6>
    </div>
    <div id="collapse-<?php echo $module->id; ?><?php echo $i ?>" class="panel-collapse collapse <?php if($i==0) echo 'in' ?>" role="tabpanel" aria-labelledby="heading-<?php echo $module->id; ?><?php echo $i ?>" aria-expanded="<?php if($i==0): echo 'true'; else: echo 'false'; endif; ?>">
      <div class="panel-body"><?php echo $helper->get('data.accordion-desc', $i) ; ?></div>
    </div>
  </div>
  <?php endfor ?>
</div>