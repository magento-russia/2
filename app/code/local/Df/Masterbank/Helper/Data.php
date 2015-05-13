<?php
class Df_Masterbank_Helper_Data extends Mage_Core_Helper_Data {
	/**
	 * @param Df_Payment_Model_Request $request
	 * @return string
	 */
	public function getSignature(Df_Payment_Model_Request $request) {
		return
			md5(
				df_concat(
					$request->getServiceConfig()->getShopId()
					, $this->getTimestamp()
					, $request->getOrder()->getIncrementId()
					, $request->getAmount()->getAsString()
					, $request->getServiceConfig()->getRequestPassword()
				)
			)
		;
	}

	/** @return string */
	public function getTimestamp() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = gmdate(self::DATE_FORMAT, time());
		}
		return $this->{__METHOD__};
	}

	const DATE_FORMAT = 'YmdHis';

	/** @return Df_Masterbank_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}