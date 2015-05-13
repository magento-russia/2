<?php
/**
 * Для данного множества подарочных промо-правил возвращает подмножество,
 * относящееся к текущему заказу
 */
class Df_PromoGift_Model_Filter_Rule_Collection_ByCurrentQuote
	extends Df_Core_Model_Filter_Collection {
	/** @return Zend_Validate_Interface */
	protected function createValidator() {
		return Df_PromoGift_Model_Validate_Rule_ApplicableToCurrentQuote::i();
	}
	/** @return Df_PromoGift_Model_Filter_Rule_Collection_ByCurrentQuote */
	public static function i() {return new self;}
}