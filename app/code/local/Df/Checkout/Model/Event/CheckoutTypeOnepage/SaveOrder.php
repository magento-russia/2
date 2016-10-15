<?php
/**
 * Cообщение:		«checkout_type_onepage_save_order»
 * Источник:		Mage_Sales_Model_Service_Quote::submitOrder()
 * [code]
		Mage::dispatchEvent('checkout_type_onepage_save_order', array('order'=>$order, 'quote'=>$quote));
 * [/code]
 *
 */
class Df_Checkout_Model_Event_CheckoutTypeOnepage_SaveOrder
	extends Df_Checkout_Model_Event_SaveOrder_Abstract {
	/** @return Mage_Sales_Model_Quote */
	public function getQuote() {return $this->getEventParam(self::EVENT_PARAM__QUOTE);}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedEventPrefix() {return self::EXPECTED_EVENT_PREFIX;}

	/** @used-by Df_Checkout_Observer::checkout_type_onepage_save_order() */

	const EVENT_PARAM__QUOTE = 'quote';
	const EXPECTED_EVENT_PREFIX = 'checkout_type_onepage_save_order';
}