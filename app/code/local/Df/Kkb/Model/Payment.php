<?php
class Df_Kkb_Model_Payment extends Df_Payment_Model_Method_WithRedirect {
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
	 * @return Df_Kkb_Model_Payment
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}