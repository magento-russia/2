<?php
class Df_Core_Model_Style_Selector_Collection extends Df_Varien_Data_Collection {
	/**
	 * @param string $selectorAsString
	 * @return Df_Core_Model_Style_Selector_Collection
	 */
	public function addHider($selectorAsString) {
		df_param_string($selectorAsString, 0);
		/** @var Df_Core_Model_Output_Css_Rule_Set $ruleSet */
		$ruleSet = Df_Core_Model_Output_Css_Rule_Set::i();
		$ruleSet->addItem(Df_Core_Model_Output_Css_Rule::i('display', 'none'));
		$this->addItem(Df_Core_Block_Element_Style_Selector::i($selectorAsString, $ruleSet));
		return $this;
	}

	/** @return string */
	protected function getItemClass() {return Df_Core_Block_Element_Style_Selector::_CLASS;}

	const _CLASS = __CLASS__;
	/** @return Df_Core_Model_Style_Selector_Collection */
	public static function i() {return new self;}
}