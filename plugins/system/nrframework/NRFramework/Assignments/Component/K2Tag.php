<?php

/**
 * @author          Tassos.gr
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2018 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace NRFramework\Assignments\Component;

defined('_JEXEC') or die;

class K2Tag extends K2Base
{
    /**
     *  Pass check for K2 Tags
     *
     *  @return bool
     */
    public function pass()
    {
        if (empty($this->selection) || !$this->passContext())
        {
            return false;
        }

        return parent::pass();
    }

    /**
     *  Returns the assignment's value
     * 
     *  @return array K2 item tags
     */
	public function value()
	{
		return $this->getK2tags();
	}
}