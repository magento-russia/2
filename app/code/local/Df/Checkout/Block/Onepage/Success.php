<?php
class Df_Checkout_Block_Onepage_Success extends Mage_Checkout_Block_Onepage_Success {
	/**
	 * Цель перекрытия —
	 * предоставление платёжным модулям возможности подмены
	 * стандартного шаблона сообщения об успешном оформлении заказа на свой.
	 * @override
	 * @return string
	 */
	public function getTemplate() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = parent::getTemplate();
			if ('checkout/success.phtml' === $result) {
				/** @var Df_Sales_Model_Order|null $order */
				$order = df_last_order(false);
				if ($order && $order->getPayment()) {
					/** @var Mage_Payment_Model_Method_Abstract|Df_Payment_Method|null $method */
					$method = $order->getPayment()->getMethodInstance();
					if ($method instanceof Df_Payment_Method && $method->getTemplateSuccess()) {
						$result = $method->getTemplateSuccess();
					}
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
}