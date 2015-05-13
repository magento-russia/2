<?php
class Df_Core_Helper_Fp extends Mage_Core_Helper_Abstract {
	/**
	 * @param string[]|string $function
	 * @param array|Iterator $array
	 * @param mixed $paramsToAppend [optional]
	 * @param mixed $paramsToPrepend [optional]
	 * @return array|ArrayIterator
	 */
	public function map($function, $array, $paramsToAppend = array(), $paramsToPrepend = array()) {
		if (!is_array($paramsToAppend)) {
			$paramsToAppend = array($paramsToAppend);
		}
		if (!is_array($paramsToPrepend)) {
			$paramsToPrepend = array($paramsToPrepend);
		}
		return
			($array instanceof Iterator)
			? $this->mapIterator($function, $array, $paramsToAppend, $paramsToPrepend)
			: $this->mapArray($function, $array, $paramsToAppend, $paramsToPrepend)
		;
	}

	/**
	 * @param array|string $function
	 * @param Iterator $iterator
	 * @param mixed $paramsToAppend [optional]
	 * @param mixed $paramsToPrepend [optional]
	 * @return ArrayIterator
	 */
	private function mapIterator($function, Iterator $iterator, $paramsToAppend = array(), $paramsToPrepend = array()) {
		return new ArrayIterator($this->mapArray(
			$function, iterator_to_array($iterator), $paramsToAppend, $paramsToPrepend
		));
	}

	/**
	 * @param string[]|string $function
	 * @param array $array
	 * @param mixed $paramsToAppend [optional]
	 * @param mixed $paramsToPrepend [optional]
	 * @return array
	 */
	private function mapArray($function, array $array, $paramsToAppend = array(), $paramsToPrepend = array()) {
		$result = array();
		foreach ($array as $item) {
			$result[]= call_user_func_array($function, array_merge($paramsToPrepend, array($item), $paramsToAppend));
		}
		return $result;
	}

	/** @return Df_Core_Helper_Fp */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}