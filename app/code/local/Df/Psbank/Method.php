<?php
namespace Df\Psbank;
/** @method \Df\Psbank\Request\Payment getRequestPayment() */
class Method extends \Df\Payment\Method\WithRedirect {
	/**
	 * @see \Df\Payment\Method::canCapture()
	 * @override
	 * @return bool
	 */
	public function canCapture() {return true;}

	/**
	 * Результат метода говорит системе о том, поддерживает ли способ оплаты
	 * автоматизированное разблокирование (возврат покупателю)
	 * ранее зарезервированных (но не снятых со счёта покупателя) средств
	 * @override
	 * @param \Varien_Object $payment
	 * @return bool
	 */
	public function canVoid(\Varien_Object $payment) {return true;}
}