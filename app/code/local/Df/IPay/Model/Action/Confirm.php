<?php
class Df_IPay_Model_Action_Confirm extends Df_IPay_Model_Action_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getRequestAsXml_Test() {
		/** @var string $result */
		$result =
			true//0 === rand (0, 1)
			? $this->getRequestAsXml_Test_Error()
			: $this->getRequestAsXml_Test_Success()
		;
		df_result_string($result);
		return $result;
	}

	/**
	 * @override
	 * @return Df_IPay_Model_Action_Confirm
	 */
	protected function processInternal() {
		if (!is_null($this->getRequestParam_ErrorText())) {
			$this->getOrder()
				->addStatusHistoryComment(
					$this->getRequestParam_ErrorText()
				)
			;
			$this->getOrder()->setData(Df_Sales_Const::ORDER_PARAM__IS_CUSTOMER_NOTIFIED, false);
			$this->getOrder()->save();
			/**
			 * После получения от iPay ОТРИЦАТЕЛЬНОГО TransactionResult
			 * (т.е. отмены операции)
			 * заказ должен быть разблокирован (т.е. снова разрешен для оплаты)
			 * либо отменен
			 */
			$this->getTransactionState()->clear();
		}
		else {
			if (!$this->getOrder()->canInvoice()) {
				df_error(
					'Заказ номер %d уже оплачен'
					,$this->getOrder()->getId()
				);
			}
			/** @var Mage_Sales_Model_Order_Invoice $invoice */
			$invoice = $this->getOrder()->prepareInvoice();
			$invoice->register();
			$invoice->capture();
			/** @var Mage_Core_Model_Resource_Transaction $transaction */
			$transaction = df_model(Df_Core_Const::CORE_RESOURCE_TRANSACTION_CLASS_MF);
			$transaction
				->addObject($invoice)
				->addObject($invoice->getOrder())
				->save()
			;
			$this->getOrder()
				->setState(
					Mage_Sales_Model_Order::STATE_PROCESSING
					,Mage_Sales_Model_Order::STATE_PROCESSING
					,rm_sprintf(
						$this->getMessage(Df_Payment_Model_Action_Confirm::CONFIG_KEY__MESSAGE__SUCCESS)
						,$invoice->getIncrementId()
					)
					,true
				)
			;
			$this->getOrder()->save();
			$this->getOrder()->sendNewOrderEmail();
		}
		$this->getResponseAsSimpleXmlElement()
			->appendChild(
				Df_Varien_Simplexml_Element::createNode('TransactionResult')
					->importArray(
						array(
							'ServiceProvider_TrxId' => $this->getOrder()->getIncrementId()
							,'Info' =>
								array(
									'InfoLine' =>
										is_null($this->getRequestParam_ErrorText())
										? $this->getRequestPayment()->getTransactionDescription()
										: 'Операция отменена'
								)
						)
					)
			)
		;
		return $this;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedRequestType() {
		return self::TRANSACTION_STATE__RESULT;
	}

	/** @return string */
	private function getRequestAsXml_Test_Error() {
		/** @var string $result */
		$result =
			df_text()->convertUtf8ToWindows1251("<?xml version='1.0' encoding='windows-1251' ?>
<ServiceProvider_Request>
	<Version>1</Version>
	<RequestType>TransactionResult</RequestType>
	<DateTime>20090124154050</DateTime>
	<PersonalAccount>22</PersonalAccount>
	<Currency>974</Currency>
	<RequestId>9221</RequestId>
	<TransactionResult>
		<TransactionId>6180433</TransactionId>
		<ServiceProvider_TrxId>8571502</ServiceProvider_TrxId>
		<ErrorText>Операция отменена</ErrorText>
	</TransactionResult>
</ServiceProvider_Request>
			")
		;
		df_result_string($result);
		return $result;
	}

	/** @return string */
	private function getRequestAsXml_Test_Success() {
		/** @var string $result */
		$result =
			df_text()->convertUtf8ToWindows1251("<?xml version='1.0' encoding='windows-1251' ?>
<ServiceProvider_Request>
	<Version>1</Version>
	<RequestType>TransactionResult</RequestType>
	<DateTime>20090124155800</DateTime>
	<PersonalAccount>22</PersonalAccount>
	<Currency>974</Currency>
	<RequestId>9221</RequestId>
	<TransactionResult>
		<TransactionId>6180433</TransactionId>
		<ServiceProvider_TrxId>8571502</ServiceProvider_TrxId>
	</TransactionResult>
</ServiceProvider_Request>
			")
		;
		df_result_string($result);
		return $result;
	}

	/** @return string|null */
	private function getRequestParam_ErrorText() {
		/** @var string|null $result */
		$result = $this->getRequestParam(self::REQUEST_PARAM__TRANSACTION_RESULT__ERROR_TEXT);
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	const _CLASS = __CLASS__;
	const REQUEST_PARAM__TRANSACTION_RESULT__ERROR_TEXT = 'TransactionResult/ErrorText';
	/**
	 * @static
	 * @param Df_IPay_ConfirmController $controller
	 * @return Df_IPay_Model_Action_Confirm
	 */
	public static function i(Df_IPay_ConfirmController $controller) {
		return new self(array(self::P__CONTROLLER => $controller));
	}
}