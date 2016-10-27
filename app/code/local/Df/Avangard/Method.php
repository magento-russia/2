<?php
namespace Df\Avangard;
class Method extends \Df\Payment\Method\WithRedirect {
	/**
	 * @see \Df\Payment\Method::canCapture()
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