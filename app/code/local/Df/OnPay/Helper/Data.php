<?php
class Df_OnPay_Helper_Data extends Mage_Core_Helper_Data {
	/**
	 * @param array $params
	 * @return string
	 */
	public function generateSignature(array $params) {
		return md5(implode(self::SIGNATURE_PARTS_SEPARATOR, $params));
	}

	/**
	 * @param Df_Core_Model_Money $price
	 * @return string
	 */
	public function priceToString(Df_Core_Model_Money $price) {
		return
			$price->getOriginalAsFloat() === df_float($price->getIntegerPart())
			? number_format(round($price->getOriginalAsFloat(), 1), 1, '.', '')
			: $price->getAsString()
		;
	}


	const SIGNATURE_PARTS_SEPARATOR = ';';
	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}