<?php
/**
 * @package         Advanced Module Manager
 * @version         7.12.3PRO
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2019 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text as JText;

$text = JText::_('JTOOLBAR_NEW');
?>
<button onclick="location.href='index.php?option=com_advancedmodules&amp;view=select'" class="btn btn-small btn-success" title="<?php echo $text; ?>">
	<span class="icon-plus icon-white"></span>
	<?php echo $text; ?>
</button>
