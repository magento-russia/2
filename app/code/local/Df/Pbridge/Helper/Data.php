<?php
class Df_Pbridge_Helper_Data extends Enterprise_Pbridge_Helper_Data {
	/**
	 * Цель перекрытия —
	 * адаптация модуля «Удобное оформление заказа» к Magento Enterprise Edition.
	 *
	 * @override
	 * @param string $name template name
	 * @param string $block buttons block name
	 * @return string
	*/
	public function getReviewButtonTemplate($name, $block) {
		/** @var string $result */
		/** @var Mage_Sales_Model_Quote|null $quote */
		$quote = rm_session_checkout()->getQuote();
		if (
				$quote
			&&
				/**
				 * Обратите внимание, что метод @see Mage_Sales_Model_Quote::getPayment()
				 * всегда воовзврает объект (никогда не возвращает пустое значение).
				 *
				 * С другой стороны, метод @see Mage_Payment_Model_Info::getMethodInstance()
				 * возбудит исключительную ситуацию, если способ оплаты ещё неизвестен.
				 *
				 * Мы попадаем в эту точку программы при неизвестном способе оплаты
				 * в том случае, когда все шаги оформмления заказа отображены разом
				 * (включен модуль «Удобное оформление заказа»).
				 *
				 * Факт неизвестности способа оплаты можно установить
				 * проверкой на пустоту результата вызова $quote->getPayment()->getMethod().
				 *
				 * Смотрите также
				 * @see Df_Pbridge_Block_Checkout_Payment_Review_Container::getReviewButtonTemplate().
				 */
				$quote->getPayment()->getMethod()
			&&
				$quote->getPayment()->getMethodInstance()->getDataUsingMethod('is_deferred3d_check')
		) {
			$result = $name;
		}
		else {
			/** @var Mage_Core_Block_Template $blockObject */
			$blockObject = rm_layout()->getBlock($block);
			$result = !$blockObject ? '' : $blockObject->getTemplate();
		}
		return $result;
	}
}