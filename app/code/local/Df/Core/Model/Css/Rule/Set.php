<?php
/** @method Df_Core_Model_Css_Rule_Set addItem(Df_Core_Model_Css_Rule $item) */
class Df_Core_Model_Css_Rule_Set extends Df_Varien_Data_Collection {
	/**
	 * @used-by i()
	 * @used-by Df_Core_Model_Css::addHider()
	 * @param string $name
	 * @param string $value
	 * @param string|null $units [optional]
	 * @return void
	 */
	public function addRule($name, $value, $units = null) {
		$this->addItem(Df_Core_Model_Css_Rule::i($name, $value, $units));
	}

	/**
	 * @used-by Df_Core_Model_Css_Selector::getRulesAsText()
	 * @param bool $inline [optional]
	 * @return string
	 */
	public function getText($inline = false) {
		if (!isset($this->{__METHOD__}[$inline])) {
			/** @uses Df_Core_Model_Css_Rule::getText() */
			$this->{__METHOD__}[$inline] = implode($inline ? ' ' : "\n", $this->walk('getText'));
		}
		return $this->{__METHOD__}[$inline];
	}

	/**
	 * @override
	 * @see Df_Varien_Data_Collection::itemClass()
	 * @used-by Df_Varien_Data_Collection::addItem()
	 * @return string
	 */
	protected function itemClass() {return Df_Core_Model_Css_Rule::_C;}

	/** @used-by Df_Core_Model_Css_Selector::_construct() */
	const _C = __CLASS__;

	/**
	 * @used-by Df_Core_Model_Css::addSelectorSimple()
	 * @used-by Df_Core_Model_Css_Selector::getRuleSet()
	 * @param string $name [optional]
	 * @param string|null $value [optional]
	 * @param string|null $units [optional]
	 * @return Df_Core_Model_Css_Rule_Set
	 */
	public static function i($name = null, $value = null, $units = null) {
		/** @var Df_Core_Model_Css_Rule_Set $result */
		$result = new self;
		if ($name) {
			$result->addRule($name, $value, $units);
		}
		return $result;
	}
}