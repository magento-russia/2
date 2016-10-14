<?php
class Df_IPay_Model_Action_GetPaymentAmount extends Df_IPay_Model_Action_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getRequestAsXml_Test() {
		return df_1251_to("<?xml version='1.0' encoding='windows-1251' ?>
<ServiceProvider_Request>
	<Version>1</Version>
	<RequestType>ServiceInfo</RequestType>
	<DateTime>20090124153456</DateTime>
	<PersonalAccount>2</PersonalAccount>
	<Currency>974</Currency>
	<RequestId>9221</RequestId>
</ServiceProvider_Request>
		");
	}

	/**
	 * @override
	 * @see Df_Core_Model_Action::_process()
	 * @used-by Df_Core_Model_Action::process()
	 * @return void
	 */
	protected function _process() {
		$this->e()->appendChild(rm_xml_node('ServiceInfo')->importArray(array(
			'Name' => array(
				'Surname' => $this->order()->getCustomerLastname()
				,'FirstName' => $this->order()->getCustomerFirstname()
				,'Patronymic' => $this->order()->getCustomerMiddlename()
			)
			,'Amount' => array('Debt' => $this->getRequestPayment()->amount()->getAsInteger())
			,'Address' => array('City' => $this->getRequestPayment()->city())
			,'Info' => array('InfoLine' => $this->getRequestPayment()->getTransactionDescription())
		)));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedRequestType() {return self::$TRANSACTION_STATE__SERVICE_INFO;}
}