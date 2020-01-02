<?php
/**
 * @package	Mosets Tree
 * @copyright	(C) 2005-present Mosets Consulting. All rights reserved.
 * @license	GNU General Public License
 * @author	Lee Cher Yeong <mtree@mosets.com>
 * @url		http://www.mosets.com/tree/
 */

defined('JPATH_BASE') or die();

use \Joomla\String\StringHelper;

/**
 * Parameter for filtering a field based on simple operator (=,>,<,>=,<= & !=)
 *
 * @author 	Lee Cher Yeong <mtree@mosets.com>
 * @package 	Mosets Tree
 * @subpackage	FormField
 * @since	2.2
 */

class JFormFieldFilterfield extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Filterfield';
	
	/**
	* Maximum length of a caption before it's cut off
	*
	* @access	protected
	* @var		int
	*/
	var $_max_caption_length = 23;
	
	function getInput()
	{
		$size = ( $this->element['size'] ? 'size="'.$this->element['size'].'"' : '' );
		$class = ( $this->element['class'] ? 'class="'.$this->element['class'].'"' : '' );
		
		if( empty($this->value) && !is_array($this->value) ) {
			$this->value = array('','=','');
		}
		
		$html 		= '';
		$db			= JFactory::getDBO();
		$db->setQuery(
			'SELECT cf_id AS value, caption AS text '
			. ' FROM #__mt_customfields '
			. ' WHERE published = 1 AND iscore = 0 AND field_type NOT IN (\'listingid\', \'category\', \'directory\', \'captcha\', \'associatedlisting\')'
		);
		$customfields[] = JHtml::_('select.option', '', JText::_('- Select a field -') );
		$customfields = array_merge( $customfields, $db->loadObjectList() );
		
		// trim long caption
		$i = 0;
		foreach( $customfields AS $customfield )
		{
			if( StringHelper::strlen($customfields[$i]->text) > ($this->_max_caption_length -3) )
			{
				$customfields[$i]->text = StringHelper::substr( $customfields[$i]->text, 0, ($this->_max_caption_length -3) ) . '...';
			}
			$i++;
		}
		$html .= JHtml::_('select.genericlist',  $customfields, $this->name.'[]', '', 'value', 'text', $this->value[0], $this->id);

		$operators[] = JHtml::_('select.option', 'LIKE', JText::_('contains'));
        $operators[] = JHtml::_('select.option', '=', JText::_('is equal to'));
		$operators[] = JHtml::_('select.option', '!=', JText::_('is not equal to'));
		$operators[] = JHtml::_('select.option', '>', JText::_('is more than'));
		$operators[] = JHtml::_('select.option', '<', JText::_('is less than'));

		$html .= JHtml::_('select.genericlist',  $operators, $this->name.'[]', '', 'value', 'text', $this->value[1], $this->id);
		
		$html .= '<input type="text" name="'.$this->name.'[]'.'" id="'.$this->id.'" value="'.$this->value[2].'" '.$class.' '.$size.' />';

		return $html;
		
	}
}
