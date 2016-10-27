<?php
namespace Df\Payment;
class Exception extends \Df\Core\Exception {
	/**
	 * Если метод вернёт true, то система добавит к сообщению обрамление/пояснение
	 * из @see \Df\Payment\Config\Area\Frontend::getMessageFailure()
	 * @see \Df\Payment\Action\Confirm::showExceptionOnCheckoutScreen()
	 * @return bool
	 */
	public function needFraming() {return true;}
}