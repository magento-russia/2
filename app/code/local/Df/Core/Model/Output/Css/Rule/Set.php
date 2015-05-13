<?php
/**
 * @method Df_Core_Model_Output_Css_Rule_Set addItem(Df_Core_Model_Output_Css_Rule $item)
 */
class Df_Core_Model_Output_Css_Rule_Set extends Df_Varien_Data_Collection {
	/** @return string */
	public function __toString() {return $this->getText();}

	/**
	 * @param bool $inline[optional]
	 * @return string
	 */
	public function getText($inline = false) {
		df_param_boolean($inline, 0);
		if (!isset($this->{__METHOD__}[$inline])) {
			$this->{__METHOD__}[$inline] =
				implode(
					$inline ? ' ' : ''
					,$this->walk('getText', array($inline))
				)
			;
		}
		return $this->{__METHOD__}[$inline];
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {return Df_Core_Model_Output_Css_Rule::_CLASS;}

	const _CLASS = __CLASS__;
	/** @return Df_Core_Model_Output_Css_Rule_Set */
	public static function i() {return new self;}
}