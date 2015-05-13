<?php
class Df_Psbank_Helper_Data extends Mage_Core_Helper_Data {
	/** @return string */
	public function generateNonce() {return substr(str_replace('.', '', uniqid('', true)), 0, 16);}

	/**
	 * @param array(string => string) $data
	 * @param string[] $paramNames
	 * @param string $password
	 * @return string
	 */
	public function generateSignature(array $data, array $paramNames, $password) {
		/** @var string $document */
		$document = '';
		foreach ($paramNames as $paramName) {
			/** @var string $paramName */
			/** @var string $paramValue */
			$paramValue = strval(df_a($data, $paramName));
			/** @var int $paramLength */
			/**
			 * Обратите внимание,
			 * что здесь нужно использовать именно функцию @see strlen, а не @see mb_strlen!
			 */
			$paramLength = strlen($paramValue);
			$document .= (!$paramLength ? '-' : ($paramLength . $paramValue));
		}
		return hash_hmac('sha1', $document, pack('H*', $password));
	}

	/** @return string */
	public function getTimestamp() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = mb_substr(gmdate('YmdHis\Z'), 0, -1);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Psbank_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}