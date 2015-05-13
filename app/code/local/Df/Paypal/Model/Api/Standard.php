<?php
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
		$order = $this->getDataUsingMethod('order');
		/** @var float $additionalDiscount */
		$additionalDiscount =
				rm_float($order->getData('reward_currency_amount'))
			+
				rm_float($order->getData('customer_balance_amount'))
		;
		if (0 < $additionalDiscount) {
			$additionalDiscountInUSD =
				$order->getOrderCurrency()->convert(
					$additionalDiscount, Df_Directory_Model_Currency::USD
				)
			;
			$result['discount_amount'] =
				$this->_filterAmount (
					rm_float(df_a($result, 'discount_amount')) + $additionalDiscountInUSD
				)
			;
		}
		if (rm_bool($this->getDataUsingMethod('is_line_items_enabled'))) {
			$result['amount'] =
				$this->_filterAmount (
					rm_float(df_a($result, 'amount')) - rm_float(df_a($result, 'shipping'))
				)
			;
		}
		return $result;
	}
}