<?php
class Df_Phpquery_Helper_Data extends Mage_Core_Helper_Abstract {
	/** @return Df_Phpquery_LibLoader */
	public function lib() {return Df_Phpquery_LibLoader::s();}

	/**
	 * @param phpQueryObject $pqOptions
	 * @return array(string => string)
	 */
	public function parseOptions(phpQueryObject $pqOptions) {
		/** @var array(string => string) $result */
		$result = array();
		foreach ($pqOptions as $domOption) {
			/** @var DOMNode $domOption */
			/** @var string $label */
			$label = df_trim($domOption->textContent);
			// Этот алгоритм должен работать быстрее, чем df_pq($domOption)->val()
			if ('' !== $label) {
				/** @var string|null $value */
				$value = null;
				if (!is_null($domOption->attributes)) {
					/** @var DOMNode|null $domValue */
					$domValue = $domOption->attributes->getNamedItem('value');
					if (!is_null($domValue)) {
						$value = $domValue->nodeValue;
					}
				}
				$result[$label] = $value;
			}
		}
		return $result;
	}

	const _CLASS = __CLASS__;
	/** @return Df_Phpquery_Helper_Data */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}