<?php
class Df_IPay_Block_Form extends Df_Payment_Block_Form {
	/**
	 * Перекрываем метод лишь для того,
	 * чтобы среда разработки знала класс способа оплаты
	 * @override
	 * @return Df_IPay_Model_Payment
	 */
	public function getMethod() {
		return parent::getMethod();
	}

	/** @return array */
	public function getPaymentOptions() {
		return
			$this->getMethod()->getRmConfig()->getConstManager()
				->getAvailablePaymentMethodsAsCanonicalConfigArray()
		;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getDefaultTemplate() {
		return 'df/ipay/form.phtml';
	}

	const _CLASS = __CLASS__;
}