<?php
namespace Df\EasyPay\Action;
class Confirm extends \Df\Payment\Action\Confirm {
	/**
	 * @override
	 * @return void
	 * @throws \Df\Core\Exception
	 */
	protected function checkPaymentAmount() {
		if ($this->rAmount()->getAsInteger() !== $this->amountFromOrder()->getAsInteger()) {
			$this->errorInvalidAmount();
		}
	}

	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function rkOII() {return 'order_mer_code';}

	/**
	 * @override
	 * @return string
	 */
	protected function signatureOwn() {return md5(implode([
		$this->rOII()
		/**
		 * Размер платежа всегда является целым числом,
		 * но EasyPay присылает его в формате с двумя знаками после запятой.
		 * Например: «103.00», а не «103».
		 * Поэтому не используем $this->rAmount()->getAsInteger()
		 */
		,$this->param('sum')
		,$this->rShopId()
		,$this->param('card')
		,$this->rTime()
		,$this->getResponsePassword()
	]));}

	/**
	 * @override
	 * @param \Exception $e
	 * @return void
	 */
	protected function processException(\Exception $e) {
		parent::processException($e);
		/**
		 * В случае, если Поставщик не может по техническим или другим причинам обработать уведомление,
		 * он должен ответить любым кодом ошибки, например "HTTP/1.0 400 Bad Request".
		 * Недопустимо отвечать кодом "HTTP/1.0 200 OK" на необработанное уведомление.
		 * https://ssl.easypay.by/notify/
		 */
		$this->response()->setHttpResponseCode(500);
	}

	/**
	 * Уведомление Поставщика о совершенном платеже осуществляется запросом,
	 * который будет отсылаться до тех пор, пока Поставщик его не примет,
	 * то есть не ответит ему кодом "HTTP/1.0 200 OK".
	 * https://ssl.easypay.by/notify/
	 * @override
	 * @see \Df\Payment\Action\Confirm::processResponseForSuccess()
	 * @used-by _process()
	 * @return void
	 */
	protected function processResponseForSuccess() {
		parent::processResponseForSuccess();
		$this->response()->setRawHeader('HTTP/1.0 200 OK');
	}
}