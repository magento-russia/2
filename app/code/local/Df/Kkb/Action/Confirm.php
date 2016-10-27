<?php
namespace Df\Kkb\Action;
class Confirm extends \Df\Payment\Action\Confirm {
	/**
	 * @override
	 * @return void
	 * @throws \Df\Core\Exception
	 */
	protected function checkSignature() {
		/**
		 * Стандартная проверка подписи нам не нужна,
		 * потому что специфическая для Казкоммерцбанка проверка подписи
		 * производится в классе @see \Df\Kkb\Response\Payment
		 */
		if (!$this->getResponseAsObject()->isSuccessful()) {
			df_error('Заказ не был оплачен.');
		}
	}

	/**
	 * Использовать @see getConst() нельзя из-за рекурсии.
	 * Номер заказа мы получаем не традиционным способом (по ключу в ассоциативном массиве),
	 * а через $this->getResponseAsObject()->getOrderIncrementId()
	 * @override
	 * @return string
	 */
	protected function rkOII() {return 'отсутствует';}

	/**
	 * @override
	 * @return string
	 */
	protected function rOII() {return $this->getResponseAsObject()->getOrderIncrementId();}
	
	/**
	 * @override
	 * @return string
	 */
	protected function rAmountS() {return
		$this->getResponseAsObject()->getPaymentAmountInServiceCurrency()->getAsString()
	;}
	
	/** @return \Df\Kkb\Response\Payment */
	protected function getResponseAsObject() {return dfc($this, function() {return
		\Df\Kkb\Response\Payment::i(df_request('response'))
	;});}

	/**
	 * @override
	 * @param \Exception $e
	 * @return string
	 */
	protected function responseTextForError(\Exception $e) {return 0;}

	/**
	 * @override
	 * @return string
	 */
	protected function responseTextForSuccess() {return 0;}
	
	/**
	 * Стандартная проверка подписи нам не нужна,
	 * потому что специфическая для Казкоммерцбанка проверка подписи
	 * производится в классе @see \Df\Kkb\Response\Payment
	 * @override
	 * @return string
	 */
	protected function signatureOwn() {df_should_not_be_here(); return null;}

	/**
	 * @override
	 * @return bool
	 */
	protected function needCapture() {return false;}

	/**
	 * @override
	 * @see \Df\Payment\Action\Confirm::processResponseForSuccess()
	 * @used-by _process()
	 * @return void
	 */
	protected function processResponseForSuccess() {
		parent::processResponseForSuccess();
		$this->getResponseAsObject()->postProcess($this->payment());
	}
}