<?php
class Df_LiqPay_Block_Form extends Df_Payment_Block_Form {
	/**
	 * Перекрываем метод лишь для того,
	 * чтобы среда разработки знала класс способа оплаты
	 * @override
	 * @return Df_LiqPay_Model_Payment
	 */
	public function getMethod() {return parent::getMethod();}

	/**
	 * @override
	 * @return string
	 */
	protected function getDefaultTemplate() {return 'df/liqpay/form.phtml';}
	const _CLASS = __CLASS__;
}