<?php
/**
 * @method Df_Sales_Model_Order|null getOrder()
 * @method bool|null getIsLineItemsEnabled()
 */
class Df_Paypal_Model_Api_Standard extends Mage_Paypal_Model_Api_Standard {
	/**
	 * Цель перекрытия:
	 *
	 * Помимо стандартных скидок Magento Community Edition
	 * мы должны учесть скидки накопительной программы и личного счёта.
	 *
	 * Модули "Накопительная программа" и "Личный счёт"
	 * не добавляют свои скидки к общей скидке.
	 *
	 * Поэтому нам надо учесть их скидки вручную
	 *
	 * @override
	 * @return array
	 */
	public function getStandardCheckoutRequest() {
		/** @var array(string => string) $result */
		$result = parent::getStandardCheckoutRequest();
		/** @var Df_Sales_Model_Order $order */
		$order = $this->getOrder();
		/** @var float $additionalDiscount */
		$additionalDiscount =
				df_float($order->getData('reward_currency_amount'))
			+
				df_float($order->getData('customer_balance_amount'))
		;
		if (0 < $additionalDiscount) {
			$additionalDiscountInUSD =
				$order->getOrderCurrency()->convert(
					$additionalDiscount, Df_Directory_Model_Currency::USD
				)
			;
			$result['discount_amount'] =
				$this->_filterAmount (
					df_float(dfa($result, 'discount_amount')) + $additionalDiscountInUSD
				)
			;
		}
		if ($this->getIsLineItemsEnabled()) {
			$result['amount'] =
				$this->_filterAmount (
					df_float(dfa($result, 'amount')) - df_float(dfa($result, 'shipping'))
				)
			;
		}
		return $result;
	}
}