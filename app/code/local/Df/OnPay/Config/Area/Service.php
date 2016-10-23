<?php
class Df_OnPay_Config_Area_Service extends Df_Payment_Config_Area_Service {
	/** @return string */
	public function getReceiptCurrency() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getVar('receipt_currency');
			df_result_string($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getUrlPaymentPage() {
		return str_replace('{shop-id}', $this->getShopId(), parent::getUrlPaymentPage());
	}
}