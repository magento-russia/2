<?php
class Df_WebMoney_Model_Config_Area_Service extends Df_Payment_Model_Config_Area_Service {
	/**
	 * @override
	 * @return string
	 */
	public function getCurrencyCode() {
		/** @var string $result */
		$result =
			$this->translateCurrencyCodeReversed(
				$this->getCurrencyCodeInServiceFormat()
			)
		;
		df_result_string_not_empty($result);
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	public function getCurrencyCodeInServiceFormat() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = strtoupper(substr($this->getShopId(),0, 1));
		}
		return $this->{__METHOD__};
	}
}