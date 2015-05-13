<?php
/**
 * Допускает только относящиеся к данному правилу подарки
 */
class Df_PromoGift_Model_Validate_Gift_RelatedToRuleGiven
	extends Df_Core_Model_Abstract
	implements Zend_Validate_Interface {
	/**
	 * Returns an array of message codes that explain why a previous isValid() call
	 * returned false.
	 *
	 * If isValid() was never called or if the most recent isValid() call
	 * returned true, then this method returns an empty array.
	 *
	 * This is now the same as calling array_keys() on the return value from getMessages().
	 * @return array
	 * @deprecated Since 1.5.0
	 */
	public function getErrors() {return array();}

	/**
	 * @param Df_PromoGift_Model_Gift|mixed $value
	 * @return boolean
	 * @throws Zend_Validate_Exception If validation of $value is impossible
	 */
	public function isValid($value) {
		return
				($value instanceof Df_PromoGift_Model_Gift)
			&&
				($value->getRuleId() === $this->getRuleId())
		;
	}

	/** @return array */
	public function getMessages() {return array();}

	/** @return int */
	private function getRuleId() {
		return $this->cfg(self::P__RULE_ID);
	}

	const P__RULE_ID = 'ruleId';

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__RULE_ID, self::V_INT);
	}
	const _CLASS = __CLASS__;
	/**
	 * @param int $ruleId
	 * @return Df_PromoGift_Model_Validate_Gift_RelatedToRuleGiven
	 */
	public static function i($ruleId) {return new self(array(self::P__RULE_ID => $ruleId));}
}