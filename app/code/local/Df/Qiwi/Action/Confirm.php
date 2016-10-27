<?php
class Df_Qiwi_Action_Confirm extends \Df\Payment\Action\Confirm {
	/**
	 * @used-by Zend_Soap_Server::handle()
	 * @param stdClass $params
	 * @return int
	 */
	public function updateBill($params) {
		/**
		 * Номер заказа надо указывать отдельным вызовом setParam,
		 * потому что @see rkShopId() уже будет использовать указанное значение
		 */
		$this->request()->setParam($this->rkOII(), dfo($params, 'txn'));
		$this->request()->setParams([
			$this->rkShopId() => dfo($params, 'login')
			,$this->rkSignature() => dfo($params, 'password')
			/**
			 * \Df\Payment\Action\Confirm::rState
			 * должен вернуть строку
			 */
			,$this->rkState() => strval(dfo($params, 'status'))
		]);
		return 0;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function alternativeProcessWithoutInvoicing() {
		$this->comment($this->stateMessage(df_nat($this->rState())));
	}

	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function rkOII() {return 'order_increment_id';}

	/**
	 * @override
	 * @param Exception $e
	 * @return string
	 */
	protected function responseTextForError(Exception $e) {return $this->soap()->getLastResponse();}

	/**
	 * @override
	 * @return string
	 */
	protected function responseTextForSuccess() {return $this->soap()->getLastResponse();}

	/**
	 * @override
	 * @return string
	 */
	protected function signatureOwn() {return
		strtoupper(md5(df_c(
			$this->adjustSignatureParamEncoding($this->rOII())
			,strtoupper(md5($this->adjustSignatureParamEncoding($this->getResponsePassword())))
		)))
	;}

	/**
	 * @override
	 * @return bool
	 */
	protected function needInvoice() {return self::$PROCESSED === df_nat($this->rState());}

	/**
	 * @override
	 * @see \Df\Payment\Action\Confirm::_process()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function _process() {
		/** @uses updateBill() */
		$this->soap()->handle();
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
	 * @param int $c
	 * @return string
	 */
	private function stateMessage($c) {
		df_param_integer($c, 0);
		/** @var string $result */
		$result = '';
		if ($c <= 50) {
			$result = 'Счёт выставлен.';
		}
		else if ($c < 52) {
			$result = 'Проводится платёж...';
		}
		else if ($c === self::$PROCESSED) {
			$result = 'Счёт оплачен.';
		}
		else if ($c >= 100) {
			if ($c === 150) {
				$result = 'Счёт отменён из-за сбоя на терминале.';
			}
			else if ($c === 151) {
				$result = 'Счёт отменён. Возможные причины: недостаточно средств на балансе,отклонен абонентом при оплате с лицевого счета оператора сотовой связи и т.п.';
			}
			else if ($c === 161) {
				$result = 'Счёт отменён, т.к. истекло время его оплаты.';
			}
			else {
				$result = 'Счёт отменён.';
			}
		}
		return df_cc_s($result, "Код состояния платежа: «{$c}».");
	}

	/** @return Df_Zf_Soap_Server */
	private function soap() {return dfc($this, function() {
		/** @var Df_Zf_Soap_Server $result */
		$result = new Df_Zf_Soap_Server(
			Mage::getConfig()->getModuleDir('etc', 'Df_Qiwi') . DS. 'IShopClientWS.wsdl'
			,['encoding' => 'UTF-8']
		);
		// Soap 1.2 и так является значением по умолчанию,
		// но данным выражением мы явно это подчёркиваем.
		$result->setSoapVersion(SOAP_1_2);
		$result->setObject($this);
		$result->setReturnResponse(true);
		return $result;
	});}

	/** @var int */
	private static $PROCESSED = 60;
}