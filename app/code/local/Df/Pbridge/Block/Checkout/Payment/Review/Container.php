<?php
/** @noinspection PhpUndefinedClassInspection */
class Df_Pbridge_Block_Checkout_Payment_Review_Container
	extends Enterprise_Pbridge_Block_Checkout_Payment_Review_Container {
	/**
	 * Цель перекрытия —
	 * адаптация модуля «Удобное оформление заказа» к Magento Enterprise Edition.
	 * @override
	 * @used-by Mage_Core_Block_Abstract::toHtml()
	 * @return string
	 */
	protected function _toHtml() {
		/** @var string $result */
		/**
		 * Обратите внимание, что метод @see Mage_Sales_Model_Quote::getPayment()
		 * всегда возвращает объект (никогда не возвращает пустое значение).
		 *
		 * С другой стороны, метод @see Mage_Payment_Model_Info::getMethodInstance()
		 * возбудит исключительную ситуацию, если способ оплаты ещё неизвестен.
		 *
		 * Мы попадаем в эту точку программы при неизвестном способе оплаты
		 * в том случае, когда все шаги оформления заказа отображены разом
		 * (включен модуль «Удобное оформление заказа»).
		 *
		 * Факт неизвестности способа оплаты можно установить
		 * проверкой на пустоту результата вызова $quote->getPayment()->getMethod().
		 *
		 * Смотрите также @see Df_Pbridge_Helper_Data::getReviewButtonTemplate().
		 */
		/** @var Mage_Sales_Model_Quote_Payment $payment */
		$payment = df_quote()->getPayment();
		if ($payment->getMethod()
			&& $payment->getMethodInstance()->getDataUsingMethod('is_deferred3d_check')
		) {
			/** @noinspection PhpUndefinedMethodInspection */
			$this->setDataUsingMethod('method_code', $payment->getMethod());
			/** @noinspection PhpUndefinedClassInspection */
			$result = parent::_toHtml();
		}
		else {
			$result = '';
		}
		return $result;
	}
}

 