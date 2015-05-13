<?php
class Df_IPay_Model_Payment extends Df_Payment_Model_Method_WithRedirect {
	/** @return string|null */
	public function getMobileNetworkOperator() {
		return $this->getInfoInstance()->getAdditionalInformation(self::INFO_KEY__MOBILE_NETWORK_OPERATOR);
	}

	/**
	 * @override
	 * @return array
	 */
	protected function getCustomInformationKeys() {
		return array_merge(
			parent::getCustomInformationKeys()
			, array(self::INFO_KEY__MOBILE_NETWORK_OPERATOR)
		);
	}
	const INFO_KEY__MOBILE_NETWORK_OPERATOR = 'df_ipay__mobile_network_operator';
	/** @return Df_IPay_Model_Payment */
	public static function i() {return new self;}
	/** @return Df_IPay_Model_Payment */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}