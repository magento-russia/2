<?php
/**
 * Для данного множества подарков возвращает подмножество, относящееся к заданному промо-правилу
 */
class Df_PromoGift_Model_Filter_Gift_Collection_ByRuleGiven extends Df_Core_Model_Filter_Collection {
	/** @return Zend_Validate_Interface */
	protected function createValidator() {
		return Df_PromoGift_Model_Validate_Gift_RelatedToRuleGiven::i($this->getRuleId());
	}

	/** @return int */
	private function getRuleId() {
		return $this->cfg(Df_PromoGift_Model_Validate_Gift_RelatedToRuleGiven::P__RULE_ID);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(Df_PromoGift_Model_Validate_Gift_RelatedToRuleGiven::P__RULE_ID, self::V_INT);
	}
	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_PromoGift_Model_Filter_Gift_Collection_ByRuleGiven
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}