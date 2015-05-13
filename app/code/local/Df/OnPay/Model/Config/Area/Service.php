<?php
class Df_OnPay_Model_Config_Area_Service extends Df_Payment_Model_Config_Area_Service {
	/** @return string */
	public function getReceiptCurrency() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getVar(self::KEY__VAR__RECEIPT_CURRENCY);
			df_result_string($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getUrlPaymentPage() {
		return str_replace('{shop-id}', parent::getShopId(), parent::getUrlPaymentPage());
	}
	const KEY__VAR__RECEIPT_CURRENCY = 'receipt_currency';
}