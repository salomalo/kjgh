<?php
/*
 * ------------------------------------------------------------------------
 * JA Social II template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
*/

defined('_JEXEC') or die;
?>
<?php
	if (!$this->getParam('addon_offcanvas_enable')) return ;
?>

<a aria-controls="off-canvas" aria-expanded="false" aria-label="Open Menu" class="btn btn-inverse off-canvas-toggle <?php $this->_c('off-canvas') ?>" type="button" data-pos="right" data-nav="#t3-off-canvas" data-effect="<?php echo $this->getParam('addon_offcanvas_effect', 'off-canvas-effect-4') ?>" title="open">
  <span class="bar-first"></span>
  <span class="bar-mid"></span>
  <span class="bar-last"></span>
</a>

<!-- OFF-CANVAS SIDEBAR -->
<div id="t3-off-canvas" class="t3-off-canvas <?php $this->_c('off-canvas') ?>">

  <div class="t3-off-canvas-header">
    <h2 class="t3-off-canvas-header-title"><?php echo JText::_('T3_SIDEBAR'); ?></h2>
    <a type="button" class="close" data-dismiss="modal" aria-hidden="true" arial-expanded="true" aria-label="Close Menu" title="close">&times;</a>
  </div>

  <div class="t3-off-canvas-body">
    <jdoc:include type="modules" name="<?php $this->_p('off-canvas') ?>" style="T3Xhtml" />
  </div>

</div>
<!-- //OFF-CANVAS SIDEBAR -->
