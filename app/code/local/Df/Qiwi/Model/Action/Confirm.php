<?php
class Df_Qiwi_Model_Action_Confirm extends Df_Payment_Model_Action_Confirm {
	/**
	 * @used-by Zend_Soap_Server::handle()
	 * @param stdClass $params
	 * @return int
	 */
	public function updateBill($params) {
		/**
		 * Номер заказа надо указывать отдельным вызовом setParam,
		 * потому что @see getRequestKeyShopId() уже будет использовать указанное значение
		 */
		$this->getRequest()->setParam($this->getRequestKeyOrderIncrementId(), dfo($params, 'txn'));
		$this->getRequest()->setParams(array(
			$this->getRequestKeyShopId() => dfo($params, 'login')
			,$this->getRequestKeySignature() => dfo($params, 'password')
			/**
			 * Df_Payment_Model_Action_Confirm::getRequestValueServicePaymentState
			 * должен вернуть строку
			 */
			,$this->getRequestKeyServicePaymentState() => strval(dfo($params, 'status'))
		));
		return 0;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function alternativeProcessWithoutInvoicing() {
		$this->comment($this->getPaymentStateMessage(df_nat($this->getRequestValueServicePaymentState())));
	}

	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function getRequestKeyOrderIncrementId() {return 'order_increment_id';}

	/**
	 * @override
	 * @param Exception $e
	 * @return string
	 */
	protected function getResponseTextForError(Exception $e) {
		return $this->getSoapServer()->getLastResponse();
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getResponseTextForSuccess() {return $this->getSoapServer()->getLastResponse();}

	/**
	 * @override
	 * @return string
	 */
	protected function getSignatureFromOwnCalculations() {
		/** @var string $result */
		$result = strtoupper(md5(df_cc(
			$this->adjustSignatureParamEncoding($this->getRequestValueOrderIncrementId())
			,strtoupper(md5($this->adjustSignatureParamEncoding($this->getResponsePassword())))
		)));
		return $result;
	}

	/**
	 * @override
	 * @return bool
	 */
	protected function needInvoice() {
		return self::PAYMENT_STATE__PROCESSED === df_nat($this->getRequestValueServicePaymentState());
	}

	/**
	 * @override
	 * @see Df_Payment_Model_Action_Confirm::_process()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function _process() {
		/** @uses updateBill() */
		$this->getSoapServer()->handle();
		parent::_process();
	}

	/**
	 * @param string $signatureParam
	 * @return string
	 */
	private function adjustSignatureParamEncoding($signatureParam) {
		df_param_string($signatureParam, 0);
		return df_1251_to($signatureParam);
	}

	/**
	 * @param int $code
	 * @return string
	 */
	private function getPaymentStateMessage($code) {
		df_param_integer($code, 0);
		/** @var string $result */
		$result = '';
		if ($code <= self::PAYMENT_STATE__BILL_CREATED) {
			$result = self::T__PAYMENT_STATE__BILL_CREATED;
		}
		else if ($code < self::PAYMENT_STATE__PROCESSED) {
			$result = self::T__PAYMENT_STATE__PROCESSING;
		}
		else if ($code === self::PAYMENT_STATE__PROCESSED) {
			$result = self::T__PAYMENT_STATE__PROCESSED;
		}
		else if ($code >= self::PAYMENT_STATE__CANCELLED) {
			$result = self::T__PAYMENT_STATE__CANCELLED__OTHER;
			if ($code === self::PAYMENT_STATE__CANCELLED__TERMINAL_ERROR) {
				$result = self::T__PAYMENT_STATE__CANCELLED__TERMINAL_ERROR;
			}
			else if ($code === self::PAYMENT_STATE__CANCELLED__AUTH_ERROR) {
				$result = self::T__PAYMENT_STATE__CANCELLED__AUTH_ERROR;
			}
			else if ($code === self::PAYMENT_STATE__CANCELLED__TIMEOUT) {
				$result = self::T__PAYMENT_STATE__CANCELLED__TIMEOUT;
			}
		}
		return df_ccc(' ', $result, "Код состояния платежа: «{$code}».");
	}

	/** @return Df_Zf_Soap_Server */
	private function getSoapServer() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Zf_Soap_Server $result */
			$result = new Df_Zf_Soap_Server(
				Mage::getConfig()->getModuleDir('etc', 'Df_Qiwi') . DS. 'IShopClientWS.wsdl'
				,array('encoding' => 'UTF-8')
			);
			// Soap 1.2 и так является значением по умолчанию,
			// но данным выражением мы явно это подчёркиваем.
			$result->setSoapVersion(SOAP_1_2);
			$result->setObject($this);
			$result->setReturnResponse(true);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}


	const PAYMENT_STATE__BILL_CREATED = 50;
	const PAYMENT_STATE__PROCESSING = 52;
	const PAYMENT_STATE__PROCESSED = 60;
	const PAYMENT_STATE__CANCELLED = 100;
	const PAYMENT_STATE__CANCELLED__TERMINAL_ERROR = 150;
	const PAYMENT_STATE__CANCELLED__AUTH_ERROR = 151;
	const PAYMENT_STATE__CANCELLED__OTHER = 160;
	const PAYMENT_STATE__CANCELLED__TIMEOUT = 161;
	const T__PAYMENT_STATE__BILL_CREATED = 'Счёт выставлен.';
	const T__PAYMENT_STATE__PROCESSING = 'Проводится платёж...';
	const T__PAYMENT_STATE__PROCESSED = 'Счёт оплачен.';
	const T__PAYMENT_STATE__CANCELLED__TERMINAL_ERROR = 'Счёт отменён из-за сбоя на терминале.';
	const T__PAYMENT_STATE__CANCELLED__AUTH_ERROR =
		'Счёт отменён. Возможные причины: недостаточно средств на балансе,отклонен абонентом при оплате с лицевого счета оператора сотовой связи и т.п.'
	;
	const T__PAYMENT_STATE__CANCELLED__OTHER = 'Счёт отменён.';
	const T__PAYMENT_STATE__CANCELLED__TIMEOUT = 'Счёт отменён, т.к. истекло время его оплаты.';
}