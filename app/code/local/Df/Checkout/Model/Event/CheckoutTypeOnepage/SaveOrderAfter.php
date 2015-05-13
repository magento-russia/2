<?php
/**
 * Cообщение:		«checkout_type_onepage_save_order_after»
 * Источник:		Mage_Checkout_Model_Type_Onepage::saveOrder()
 * [code]
		Mage::dispatchEvent(
			'checkout_type_onepage_save_order_after'
			,array('order'=>$order, 'quote'=>$this->getQuote())
		);
 * [/code]
 */
class Df_Checkout_Model_Event_CheckoutTypeOnepage_SaveOrderAfter
	extends Df_Checkout_Model_Event_SaveOrder_Abstract {
	/** @return Mage_Sales_Model_Quote */
	public function getQuote() {
		/** @var Mage_Sales_Model_Quote $result */
		$result = $this->getEventParam(self::EVENT_PARAM__QUOTE);
		df_assert($result instanceof Mage_Sales_Model_Quote);
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedEventPrefix() {
		return self::EXPECTED_EVENT_PREFIX;
	}

	const _CLASS = __CLASS__;
	const EVENT_PARAM__QUOTE = 'quote';
	const EXPECTED_EVENT_PREFIX = 'checkout_type_onepage_save_order_after';
}