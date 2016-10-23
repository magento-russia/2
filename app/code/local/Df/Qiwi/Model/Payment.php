<?php
class Df_Qiwi_Model_Payment extends Df_Payment_Model_Method_WithRedirect {
	/**
	 * @used-by Df_Qiwi_Block_Form::getQiwiCustomerPhone()
	 * @used-by Df_Qiwi_Model_Request_Payment::::getQiwiCustomerPhone()
	 * @return string
	 */
	public function getQiwiCustomerPhone() {return df_ccc('', $this->iia(
		self::KEY__PHONE_NETWORK_CODE, self::KEY__PHONE_SUFFIX
	));}

	/**
	 * @override
	 * @return array
	 */
	protected function getCustomInformationKeys() {
		return
			array_merge(
				parent::getCustomInformationKeys()
				,array(
					self::KEY__PHONE_NETWORK_CODE
					,self::KEY__PHONE_SUFFIX
				)
			)
		;
	}

	const KEY__PHONE_NETWORK_CODE = 'df_qiwi__qiwi_customer_phone__network_code';
	const KEY__PHONE_SUFFIX = 'df_qiwi__qiwi_customer_phone__suffix';
}