<?php
class Df_Qiwi_Model_Action_Confirm extends Df_Payment_Model_Action_Confirm {
	/**
	 * @override
	 * @return Df_Qiwi_Model_Action_Confirm
	 */
	protected function alternativeProcessWithoutInvoicing() {
		parent::alternativeProcessWithoutInvoicing();
		$this->getOrder()->addStatusHistoryComment(
			$this->getPaymentStateMessage(
				rm_nat($this->getRequestValueServicePaymentState())
			)
		);
		$this->getOrder()->setData(Df_Sales_Const::ORDER_PARAM__IS_CUSTOMER_NOTIFIED, false);
		$this->getOrder()->save();
		return $this;
	}

	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function getRequestKeyOrderIncrementId() {
		return 'order_increment_id';
	}

	/**
	 * @override
	 * @return Df_Qiwi_Model_Action_Confirm
	 */
	public function process() {
		try {
			$this->getSoapServer()->handle();
		}
		catch(Exception $e) {
			/**
			 * Обратите внимание, что в при большинстве сбоев мы не попадём сюда,
			 * и вообще не попадём дальше handle(),
			 * по причине особенности работы класса SoapServer:
			 * @link https://bugs.php.net/bug.php?id=49513
			 */
			df_handle_entry_point_exception($e, false);
		}

		parent::process();
		return $this;
	}

	/**
	 * @param stdClass $params
	 * @return int
	 */
	public function updateBill($params) {
		/**
		 * Номер заказа надо указывать отдельным вызовом setParam,
		 * потому что getRequestKeyShopId() уже будет использовать указанное значение
		 */
		$this->getRequest()->setParam($this->getRequestKeyOrderIncrementId(), df_o($params, 'txn'));
		$this->getRequest()
			->setParams(
				array(
					$this->getRequestKeyShopId() => df_o($params, 'login')
					,$this->getRequestKeySignature() => df_o($params, 'password')
					/**
					 * Df_Payment_Model_Action_Confirm::getRequestValueServicePaymentState
					 * должен вернуть строку
					 */
					,$this->getRequestKeyServicePaymentState() => strval(df_o($params, 'status'))
				)
			)
		;
		return 0;
	}

	/**
	 * @override
	 * @return Df_Qiwi_Model_Action_Confirm
	 * @throws Mage_Core_Exception
	 */
	protected function checkPaymentAmount() {
		return $this;
	}

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
	protected function getResponseTextForSuccess() {
		return $this->getSoapServer()->getLastResponse();
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getSignatureFromOwnCalculations() {
		/** @var string $result */
		$result =
			strtoupper(md5(
				df_concat(
					$this->adjustSignatureParamEncoding($this->getRequestValueOrderIncrementId())
					,strtoupper(md5($this->adjustSignatureParamEncoding($this->getResponsePassword())))
				)
			))
		;
		return $result;
	}

	/**
	 * @override
	 * @return bool
	 */
	protected function needInvoice() {
		return self::PAYMENT_STATE__PROCESSED === rm_nat($this->getRequestValueServicePaymentState());
	}

	/**
	 * @param string $signatureParam
	 * @return string
	 */
	private function adjustSignatureParamEncoding($signatureParam) {
		df_param_string($signatureParam, 0);
		return df_text()->convertUtf8ToWindows1251($signatureParam);
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
		return rm_concat_clean(' '
			,df_h()->qiwi()->__($result)
			,rm_sprintf(df_h()->qiwi()->__('Код состояния платежа: «%d».'), $code)
		);
	}

	/** @return Df_Zf_Soap_Server */
	private function getSoapServer() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Zf_Soap_Server $result */
			$result =
				new Df_Zf_Soap_Server (
					Mage::getConfig()->getModuleDir('etc', 'Df_Qiwi') . DS. 'IShopClientWS.wsdl'
					,array('encoding' => 'UTF-8')
				)
			;
			// Soap 1.2 и так является значением по умолчанию,
			// но данным выражением мы явно это подчёркиваем.
			$result->setSoapVersion(SOAP_1_2);
			$result->setObject($this);
			$result->setReturnResponse(true);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
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
	/**
	 * @static
	 * @param Df_Qiwi_ConfirmController $controller
	 * @return Df_Qiwi_Model_Action_Confirm
	 */
	public static function i(Df_Qiwi_ConfirmController $controller) {
		return new self(array(self::P__CONTROLLER => $controller));
	}
}