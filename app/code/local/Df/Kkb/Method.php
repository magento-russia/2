<?php
class Df_Kkb_Method extends Df_Payment_Method_WithRedirect {
	/**
	 * @see Df_Payment_Method::canCapture()
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
	 * @return Df_Kkb_Method
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}