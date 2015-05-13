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
			if (
					('checkout/success.phtml' === $result)
				&&
					$this->getPaymentMethod()
				&&
					($this->getPaymentMethod() instanceof Df_Payment_Model_Method_Base)
			) {
				/** @var Df_Payment_Model_Method_Base $paymentMethod */
				$paymentMethod = $this->getPaymentMethod();
				if ($paymentMethod->getTemplateSuccess()) {
					$result = $paymentMethod->getTemplateSuccess();
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
	
	/** @return Df_Sales_Model_Order|null */
	private function getOrder() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				!$this->getOrderIdRm() ? null : Df_Sales_Model_Order::ld($this->getOrderIdRm())
			);
		}
		return rm_n_get($this->{__METHOD__});
	}
	
	/** @return int */
	private function getOrderIdRm() {
		return rm_nat0(rm_session_checkout()->getDataUsingMethod('last_order_id'));
	}

	/** @return Mage_Payment_Model_Method_Abstract|null */
	private function getPaymentMethod() {
		return
			!$this->getOrder() || !$this->getOrder()->getPayment()
			? null
			: $this->getOrder()->getPayment()->getMethodInstance()
		;
	}
}