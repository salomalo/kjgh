<?php
/**
 * ------------------------------------------------------------------------
 * JA City Guide Template
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

<!-- FOOTER -->
<footer id="t3-footer" class="wrap t3-footer">

	<?php if ($this->checkSpotlight('footnav', 'footer-1, footer-2, footer-3, footer-4, footer-5, footer-6')) : ?>
		<!-- FOOT NAVIGATION -->
		<div class="container">
			<?php $this->spotlight('footnav', 'footer-1, footer-2, footer-3, footer-4, footer-5, footer-6') ?>
		</div>
		<!-- //FOOT NAVIGATION -->
	<?php endif ?>

	<div class="container">
		<div class="t3-copyright">
				<div class="row">
					<div class="<?php echo $this->getParam('t3-rmvlogo', 1) ? 'col-sm-8' : 'col-sm-12' ?> copyright <?php $this->_c('footer') ?>">
						<jdoc:include type="modules" name="<?php $this->_p('footer') ?>" />
					</div>
					<?php if ($this->getParam('t3-rmvlogo', 1)): ?>
						<div class="col-sm-4 poweredby text-hide">
							<a class="t3-logo t3-logo-light t3-logo-small" href="http://t3-framework.org" title="<?php echo JText::_('T3_POWER_BY_TEXT') ?>"
							   target="_blank" <?php echo method_exists('T3', 'isHome') && T3::isHome() ? '' : 'rel="nofollow"' ?>><?php echo JText::_('T3_POWER_BY_HTML') ?></a>
						</div>
					<?php endif; ?>
				</div>
		</div>
	</div>

</footer>
<!-- //FOOTER -->