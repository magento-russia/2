<?php
namespace Df\Kkb;
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
	 * @param Varien_Object $payment
	 * @return bool
	 */
	public function canVoid(Varien_Object $payment) {return true;}

	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return self
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}