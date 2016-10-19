<?php
class Df_IPay_Action_Confirm extends Df_IPay_Action_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getRequestAsXml_Test() {
		return
			true//0 === rand (0, 1)
			? $this->getRequestAsXml_Test_Error()
			: $this->getRequestAsXml_Test_Success()
		;
	}

	/**
	 * @override
	 * @see Df_Core_Model_Action::_process()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function _process() {
		if (!is_null($this->getRequestParam_ErrorText())) {
			$this->comment($this->getRequestParam_ErrorText());
			/**
			 * После получения от iPay ОТРИЦАТЕЛЬНОГО TransactionResult
			 * (т.е. отмены операции)
			 * заказ должен быть разблокирован (т.е. снова разрешен для оплаты)
			 * либо отменен
			 */
			$this->getTransactionState()->clear();
		}
		else {
			if (!$this->order()->canInvoice()) {
				df_error('Заказ номер %d уже оплачен', $this->order()->getId());
			}
			/** @var Mage_Sales_Model_Order_Invoice $invoice */
			$invoice = $this->order()->prepareInvoice();
			$invoice->register();
			$invoice->capture();
			$this->saveInvoice($invoice);
			$this->order()->setState(
				Mage_Sales_Model_Order::STATE_PROCESSING
				,Mage_Sales_Model_Order::STATE_PROCESSING
				,df_sprintf(
					$this->getMessage(Df_Payment_Model_Action_Confirm::CONFIG_KEY__MESSAGE__SUCCESS)
					,$invoice->getIncrementId()
				)
				,true
			);
			$this->order()->save();
			$this->order()->sendNewOrderEmail();
		}
		$this->e()->appendChild(df_xml_node('TransactionResult')->importArray(array(
			'ServiceProvider_TrxId' => $this->order()->getIncrementId()
			,'Info' => array(
				'InfoLine' =>
					is_null($this->getRequestParam_ErrorText())
					? $this->getRequestPayment()->description()
					: 'Операция отменена'
			)
		)));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedRequestType() {return 'TransactionResult';}

	/** @return string */
	private function getRequestAsXml_Test_Error() {
		return df_1251_to("<?xml version='1.0' encoding='windows-1251' ?>
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
		");
	}

	/** @return string */
	private function getRequestAsXml_Test_Success() {
		return df_1251_to("<?xml version='1.0' encoding='windows-1251' ?>
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
		");
	}

	/** @return string|null */
	private function getRequestParam_ErrorText() {
		return $this->getRequestParam('TransactionResult/ErrorText');
	}
}