<?php
namespace Df\Payment;
use Df\Payment\Exception\Response as EResponse;
use Df\Payment\Request\Secondary as Secondary;
use Mage_Sales_Model_Order_Payment as OP;
use Mage_Sales_Model_Order_Payment_Transaction as Transaction;
abstract class Response extends \Df_Core_Model {
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
			$reportRows = [];
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

	/** @return Secondary */
	public function getRequest() {return $this->_request;}

	/** @return string */
	public function getTransactionName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = dfa(array(
				Transaction::TYPE_PAYMENT =>
					'прямое списание средств покупателя без предварительного блокирования'
				,Transaction::TYPE_AUTH =>
					'блокирование средств покупателя'
				,Transaction::TYPE_CAPTURE =>
					'приём ранее блокированных средств покупателя'
				,Transaction::TYPE_VOID =>
					'возврат ранее блокированных средств покупателю'
			), $this->getTransactionType());
		}
		return $this->{__METHOD__};
	}

	/**
	 * Используется методом @see \Df\Payment\Request\Secondary::logAsPaymentTransaction()
	 * @return bool
	 */
	public function isTransactionClosed() {return true;}

	/**
	 * @param \Mage_Payment_Model_Info $paymentInfo
	 * @return $this
	 */
	public function loadFromPaymentInfo(\Mage_Payment_Model_Info $paymentInfo) {
		/** @var array(string => string)|null $data */
		$data = $paymentInfo->getAdditionalInformation($this->getIdInPaymentInfo());
		if (!is_null($data)) {
			df_assert_array($data);
			$this->addData($data);
		}
		return $this;
	}

	/**
	 * @param OP $orderPayment
	 * @return $this
	 */
	public function postProcess(OP $orderPayment) {
		$this->logAsPaymentTransaction($orderPayment);
		$this->saveInPaymentInfo($orderPayment->getMethodInstance()->getInfoInstance());
		$this->throwOnFailure();
		return $this;
	}

	/**
	 * Для диагностики
	 * @param Secondary $request
	 * @return void
	 */
	public function setRequest(Secondary $request) {$this->_request = $request;}
	/** @var Secondary */
	private $_request;

	/**
	 * @return void
	 * @throws EResponse
	 */
	public function throwOnFailure() {
		if (!$this->isSuccessful()) {
			$this->throwException($this->getErrorMessage());
		}
	}

	/** @return string */
	protected function getExceptionClass() {return EResponse::class;}

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
	 * @param EResponse|string $message
	 * @return void
	 * @throws EResponse
	 */
	protected function throwException($message) {
		/** @var EResponse $exception */
		if ($message instanceof EResponse) {
			$exception = $message;
		}
		else {
			/** @var mixed[] $arguments */
			$arguments = func_get_args();
			/** @var string $exceptionClass */
			$exceptionClass = $this->getExceptionClass();
			/** @var EResponse $exception */
			$exception = new $exceptionClass(df_format($arguments), $this);
			df_assert($exception instanceof EResponse);
		}
		df_error($exception);
	}

	/**
	 * @param OP $orderPayment
	 * @return void
	 */
	private function logAsPaymentTransaction(OP $orderPayment) {
		$orderPayment->addData(array(
			// Обратите внимание, что при совпадении этих идентификаторов
			// ранняя информация будет перезаписана новой
			'transaction_id' => implode('-', array(
				$orderPayment->getOrder()->getIncrementId()
				,mb_strtolower(df_last(df_explode_class($this)))
				,df_dts(\Zend_Date::now(), 'HH:mm:ss')
			))
			,'is_transaction_closed' => $this->isTransactionClosed()
		));
		/** @noinspection PhpParamsInspection */
		$orderPayment->setTransactionAdditionalInfo(
			Transaction::RAW_DETAILS, $this->getReportAsArray()
		);
		/** @var Transaction $paymentTransaction */
		$paymentTransaction = $orderPayment->addTransaction(
			$type = $this->getTransactionType(), $salesDocument = $orderPayment->getOrder()
		);
		$paymentTransaction->save();
	}

	/**
	 * @param \Mage_Payment_Model_Info $paymentInfo
	 * @return $this
	 */
	private function saveInPaymentInfo(\Mage_Payment_Model_Info $paymentInfo) {
		$paymentInfo
			->setAdditionalInformation($this->getIdInPaymentInfo(), $this->getData())
			->save()
		;
		return $this;
	}

	/**
	 * @used-by \Df\Payment\Request\Secondary::getResponse()
	 * @param Secondary $request
	 * @param array(string => string) $params
	 * @return self
	 */
	public static function ic(Secondary $request, array $params) {
		/** @var string $class */
		$class = str_replace('Request', 'Response', get_class($request));
		/** @var self $result */
		$result = df_ic($class, __CLASS__, $params);
		$result->setRequest($request);
		return $result;
	}
}