<?php
abstract class Df_Payment_Model_Response extends Df_Core_Model {
	/** @return array(string => string) */
	abstract public function getReportAsArray();
	/** @return string */
	abstract public function getTransactionType();
	/** @return string */
	abstract protected function getErrorMessage();
	/** @return bool */
	abstract protected function isSuccessful();

	/**
	 * @override
	 * @return string
	 */
	public function getReport() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $reportRows */
			$reportRows = array();
			foreach ($this->getReportAsArray() as $key => $value) {
				/** @var string $key */
				/** @var string $value */
				$reportRows[]=
					in_array($key, $this->getKeysToSuppress())
					? $value
					: sprintf("{$key}: %s.", df_trim($value, '.'))
				;
			}
			$this->{__METHOD__} = df_cc_n($reportRows);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Payment_Model_Request_Secondary */
	public function getRequest() {return $this->_request;}

	/** @return string */
	public function getTransactionName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = dfa(array(
				Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT =>
					'прямое списание средств покупателя без предварительного блокирования'
				,Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH =>
					'блокирование средств покупателя'
				,Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE =>
					'приём ранее блокированных средств покупателя'
				,Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID =>
					'возврат ранее блокированных средств покупателю'
			), $this->getTransactionType());
		}
		return $this->{__METHOD__};
	}

	/**
	 * Используется методом @see Df_Payment_Model_Request_Secondary::logAsPaymentTransaction()
	 * @return bool
	 */
	public function isTransactionClosed() {return true;}

	/**
	 * @param Mage_Payment_Model_Info $paymentInfo
	 * @return Df_Payment_Model_Response
	 */
	public function loadFromPaymentInfo(Mage_Payment_Model_Info $paymentInfo) {
		/** @var array(string => string)|null $data */
		$data = $paymentInfo->getAdditionalInformation($this->getIdInPaymentInfo());
		if (!is_null($data)) {
			df_assert_array($data);
			$this->addData($data);
		}
		return $this;
	}

	/**
	 * @param Mage_Sales_Model_Order_Payment $orderPayment
	 * @return Df_Payment_Model_Response
	 */
	public function postProcess(Mage_Sales_Model_Order_Payment $orderPayment) {
		$this->logAsPaymentTransaction($orderPayment);
		$this->saveInPaymentInfo($orderPayment->getMethodInstance()->getInfoInstance());
		$this->throwOnFailure();
		return $this;
	}

	/**
	 * Для диагностики
	 * @param Df_Payment_Model_Request_Secondary $request
	 * @return void
	 */
	public function setRequest(Df_Payment_Model_Request_Secondary $request) {
		$this->_request = $request;
	}
	/** @var Df_Payment_Model_Request_Secondary */
	private $_request;

	/**
	 * @return void
	 * @throws Df_Payment_Exception_Response
	 */
	public function throwOnFailure() {
		if (!$this->isSuccessful()) {
			$this->throwException($this->getErrorMessage());
		}
	}

	/** @return string */
	protected function getExceptionClass() {return Df_Payment_Exception_Response::class;}

	/** @return string */
	protected function getIdInPaymentInfo() {return df_cts_lc_camel($this, '_');}

	/** @return string[] */
	protected function getKeysToSuppress() {return array();}

	/** @return bool */
	protected function isFailed() {return !$this->isSuccessful();}

	/**
	 * @param string $reportRow
	 * @return string|null
	 */
	protected function onFail($reportRow) {return $this->isFailed() ? $reportRow : null;}

	/**
	 * @param string $reportRow
	 * @return string|null
	 */
	protected function onSucc($reportRow) {return $this->isSuccessful() ? $reportRow : null;}

	/**
	 * @param Df_Payment_Exception_Response|string $message
	 * @return void
	 * @throws Df_Payment_Exception_Response
	 */
	protected function throwException($message) {
		/** @var Df_Payment_Exception_Response $exception */
		if ($message instanceof Df_Payment_Exception_Response) {
			$exception = $message;
		}
		else {
			/** @var mixed[] $arguments */
			$arguments = func_get_args();
			/** @var string $exceptionClass */
			$exceptionClass = $this->getExceptionClass();
			/** @var Df_Payment_Exception_Response $exception */
			$exception = new $exceptionClass(df_format($arguments), $this);
			df_assert($exception instanceof Df_Payment_Exception_Response);
		}
		df_error($exception);
	}

	/**
	 * @param Mage_Sales_Model_Order_Payment $orderPayment
	 * @return void
	 */
	private function logAsPaymentTransaction(Mage_Sales_Model_Order_Payment $orderPayment) {
		$orderPayment->addData(array(
			// Обратите внимание, что при совпадении этих идентификаторов
			// ранняя информация будет перезаписана новой
			'transaction_id' => implode('-', array(
				$orderPayment->getOrder()->getIncrementId()
				,mb_strtolower(df_last(df_explode_class($this)))
				,df_dts(Zend_Date::now(), 'HH:mm:ss')
			))
			,'is_transaction_closed' => $this->isTransactionClosed()
		));
		$orderPayment->setTransactionAdditionalInfo(
			$key = Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS
			,$value = $this->getReportAsArray()
		);
		/** @var Mage_Sales_Model_Order_Payment_Transaction $paymentTransaction */
		$paymentTransaction = $orderPayment->addTransaction(
			$type = $this->getTransactionType(), $salesDocument = $orderPayment->getOrder()
		);
		$paymentTransaction->save();
	}

	/**
	 * @param Mage_Payment_Model_Info $paymentInfo
	 * @return Df_Payment_Model_Response
	 */
	private function saveInPaymentInfo(Mage_Payment_Model_Info $paymentInfo) {
		$paymentInfo
			->setAdditionalInformation($this->getIdInPaymentInfo(), $this->getData())
			->save()
		;
		return $this;
	}

	/**
	 * @used-by Df_Payment_Model_Request_Secondary::getResponse()
	 * @param Df_Payment_Model_Request_Secondary $request
	 * @param array(string => string) $params
	 * @return Df_Payment_Model_Response
	 */
	public static function ic(Df_Payment_Model_Request_Secondary $request, array $params) {
		/** @var string $class */
		$class = str_replace('Request', 'Response', get_class($request));
		/** @var Df_Payment_Model_Response $result */
		$result = df_ic($class, __CLASS__, $params);
		$result->setRequest($request);
		return $result;
	}
}