<?php
class Df_Qiwi_Model_Payment extends Df_Payment_Model_Method_WithRedirect {
	/**
	 * @used-by Df_Qiwi_Block_Form::getQiwiCustomerPhone()
	 * @used-by Df_Qiwi_Model_Request_Payment::::getQiwiCustomerPhone()
	 * @return string
	 */
	public function getQiwiCustomerPhone() {
		return df_ccc(''
			,$this->getInfoInstance()->getAdditionalInformation(
				self::INFO_KEY__QIWI_CUSTOMER_PHONE__NETWORK_CODE
			)
			,$this->getInfoInstance()->getAdditionalInformation(
				self::INFO_KEY__QIWI_CUSTOMER_PHONE__SUFFIX
			)
		);
	}

	/**
	 * @override
	 * @return array
	 */
	protected function getCustomInformationKeys() {
		return
			array_merge(
				parent::getCustomInformationKeys()
				,array(
					self::INFO_KEY__QIWI_CUSTOMER_PHONE__NETWORK_CODE
					,self::INFO_KEY__QIWI_CUSTOMER_PHONE__SUFFIX
				)
			)
		;
	}

	const INFO_KEY__QIWI_CUSTOMER_PHONE__NETWORK_CODE = 'df_qiwi__qiwi_customer_phone__network_code';
	const INFO_KEY__QIWI_CUSTOMER_PHONE__SUFFIX = 'df_qiwi__qiwi_customer_phone__suffix';
}