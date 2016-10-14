<?php
class Df_Admin_Config_Form_Element_Multiselect extends Varien_Data_Form_Element_Multiselect {
	/**
	 * @override
	 * @param array $option
	 * @param array $selected
	 * @return string
	 */
	protected function _optionToHtml($option, $selected) {
		$html = '<option value="'.$this->_escape($option['value']).'"';
		$html.= isset($option['title']) ? 'title="'.$this->_escape($option['title']).'"' : '';
		$html.= isset($option['style']) ? 'style="'.$option['style'].'"' : '';
		// Заплатка состоит в добавлении условия || in_array(self::RM__ALL, $selected)
		if (in_array((string)$option['value'], $selected) || in_array(self::$RM__ALL, $selected)) {
			$html.= ' selected="selected"';
		}
		$html.= '>'.$this->_escape($option['label']). '</option>'."\n";
		return $html;
	}

	/** @var string */
	private static $RM__ALL = 'df-all';

	/**
	 * @used-by Df_Payment_Config_Area_Service::getSelectedPaymentMethods()
	 * @param mixed $value
	 * @return bool
	 */
	public static function isAll($value) {return self::$RM__ALL === $value;}
}