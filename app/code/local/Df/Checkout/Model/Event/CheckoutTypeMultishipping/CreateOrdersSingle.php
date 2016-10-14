<?php
/**
 * Cообщение:		«checkout_type_multishipping_create_orders_single»
 * Источник:		Mage_Sales_Model_Service_Quote::submitOrder()
 * [code]
		foreach ($shippingAddresses as $address) {
			$order = $this->_prepareOrder($address);
			$orders[]= $order;
			Mage::dispatchEvent(
				'checkout_type_multishipping_create_orders_single',array('order'=>$order, 'address'=>$address)
			);
		}
 * [/code]
 *
 */
class Df_Checkout_Model_Event_CheckoutTypeMultishipping_CreateOrdersSingle
	extends Df_Checkout_Model_Event_SaveOrder_Abstract {
	/** @return Df_Sales_Model_Quote_Address */
	public function getAddress() {return $this->getEventParam(self::EVENT_PARAM__ADDRESS);}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedEventPrefix() {return self::EXPECTED_EVENT_PREFIX;}

	/** @used-by Df_Checkout_Observer::checkout_type_multishipping_create_orders_single() */
	const _C = __CLASS__;
	const EVENT_PARAM__ADDRESS = 'address';
	const EXPECTED_EVENT_PREFIX = 'checkout_type_multishipping_create_orders_single';
}