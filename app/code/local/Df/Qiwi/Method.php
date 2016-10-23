<?php
class Df_Qiwi_Method extends Df_Payment_Method_WithRedirect {
	/**
	 * @used-by Df_Qiwi_Block_Form::phone()
	 * @used-by Df_Qiwi_Request_Payment::qPhone()
	 * @return string
	 */
	public function qPhone() {return df_ccc('', $this->iia(
		self::KEY__PHONE_NETWORK_CODE, self::KEY__PHONE_SUFFIX
	));}

	/**
	 * @override
	 * @see Df_Payment_Method::getCustomInformationKeys()
	 * @return array
	 */
	protected function getCustomInformationKeys() {return array_merge(
		parent::getCustomInformationKeys()
		,array(self::KEY__PHONE_NETWORK_CODE, self::KEY__PHONE_SUFFIX)
	);}

	/** @used-by app/design/frontend/rm/default/template/df/qiwi/form.phtml */
	const KEY__PHONE_NETWORK_CODE = 'df_qiwi__qiwi_customer_phone__network_code';
	/** @used-by app/design/frontend/rm/default/template/df/qiwi/form.phtml */
	const KEY__PHONE_SUFFIX = 'df_qiwi__qiwi_customer_phone__suffix';
}