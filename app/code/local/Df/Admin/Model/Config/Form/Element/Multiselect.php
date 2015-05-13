<?php
class Df_Admin_Model_Config_Form_Element_Multiselect extends Varien_Data_Form_Element_Multiselect {
	/**
	 * @override
	 * @param array $option
	 * @param array $selected
	 * @return string
	 */
	protected function _optionToHtml($option, $selected)
	{
		$html = '<option value="'.$this->_escape($option['value']).'"';
		$html.= isset($option['title']) ? 'title="'.$this->_escape($option['title']).'"' : '';
		$html.= isset($option['style']) ? 'style="'.$option['style'].'"' : '';
		if (
				in_array((string)$option['value'], $selected)
			||
				/******************************
				 * Начало заплатки
				 */
				in_array(self::RM__ALL, $selected)
				/******************************
				 * Конец заплатки
				 */
		) {
			$html.= ' selected="selected"';
		}

		$html.= '>'.$this->_escape($option['label']). '</option>'."\n";
		return $html;
	}

	const _CLASS = __CLASS__;
	const RM__ALL = 'df-all';
}