<?php
class Df_Qa_Helper_Data extends Mage_Core_Helper_Abstract {
	/**
	 * @param mixed $value
	 * @param bool $addQuotes[optional]
	 * @return string
	 */
	public function convertValueToDebugString($value, $addQuotes = true) {
		/** @var string $result */
		if (is_object($value)) {
			$result = rm_sprintf('объект класса %s', get_class($value));
		}
		else if (is_array($value)) {
			$result = rm_sprintf('массив с %d элементами', count($value));
		}
		else if (is_null($value)) {
			$result = 'NULL';
		}
		else {
			$result = df_string($value);
		}
		if ($addQuotes) {
			$result = df_quote_russian($result);
		}
		return $result;
	}

	/** @return Df_Qa_Helper_Method */
	public function method() {
		return Df_Qa_Helper_Method::s();
	}

	/** @return Df_Qa_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}