<?php
abstract class Df_Masterbank_Model_Action_Confirm extends Df_Payment_Model_Action_Confirm {
	/** @return string */
	abstract protected function getResponseObjectClass();

	/**
	 * Использовать getConst нельзя из-за рекурсии.
	 * @override
	 * @return string
	 */
	protected function getRequestKeyOrderIncrementId() {
		return 'ORDER';
	}
	
	/** @return Df_Masterbank_Model_Response */
	protected function getResponseAsObject() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Masterbank_Model_Response $result */
			$result = df_model($this->getResponseObjectClass(), $this->getRequest()->getParams());
			$result->postProcess($this->getOrderPayment());
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getResponseTextForSuccess() {return 'OK';}
	
	/**
	 * @override
	 * @return string
	 */
	protected function getSignatureFromOwnCalculations() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * Опосредовано вызывает @see Df_Payment_Model_Response::postProcess()
			 * Лучшего способа вызвать postProcess,
			 * чем запихнуть в getSignatureFromOwnCalculations() — не придумал.
			 */
			$this->getResponseAsObject();
			$this->{__METHOD__} =
				strtolower(
					md5(
						df_concat(
							$this->getServiceConfig()->getShopId()
							, $this->getRequest()->getParam('TIMESTAMP')
							, $this->getRequestValueOrderIncrementId()
							, $this->getRequestValuePaymentAmountAsString()
							, $this->getRequest()->getParam('RESULT')
							, $this->getRequest()->getParam('RC')
							, $this->getRequest()->getParam('RRN')
							, $this->getRequest()->getParam('INT_REF')
							, $this->getRequest()->getParam('TRTYPE')
							, $this->getRequest()->getParam('AUTHCODE')
							, $this->getServiceConfig()->getRequestPassword()
						)
					)
				)
			;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
}