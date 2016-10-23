<?php
class Df_Avangard_Method extends Df_Payment_Method_WithRedirect {
	/**
	 * @see Df_Payment_Method::canCapture()
	 * @override
	 * @return bool
	 */
	public function canCapture() {return true;}

	/**
	 * Результат метода говорит системе о том, поддерживает ли способ оплаты
	 * автоматизированный возврат оплаты покупателю
	 * @override
	 * @return bool
	 */
	public function canRefund() {return true;}

	/**
	 * @override
	 * @return string
	 */
	public function getTemplateSuccess() {return 'df/avangard/success.phtml';}
}