<?php
class Df_PayOnline_Helper_Data extends Mage_Core_Helper_Data {
	/**
	 * @param array(string => mixed) $params
	 * @return string[]
	 */
	public function preprocessSignatureParams(array $params) {
		/** @var string[] $result */
		$result = array();
		foreach ($params as $key => $value) {
			/** @var string $key */
			/** @var mixed $value */
			$result[]=
				implode(self::SIGNATURE_KEY_VALUE_SEPARATOR, array($key, df_string($value)))
			;
		}
		return $result;
	}

	const _CLASS = __CLASS__;
	const SIGNATURE_KEY_VALUE_SEPARATOR = '=';
	const SIGNATURE_PARTS_SEPARATOR = '&';

	/** @return Df_PayOnline_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}