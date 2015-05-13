<?php
class Df_Core_Helper_ArrayColumn {
	/**
	 * 2015-02-07
	 * @link http://php.net/manual/function.array-column.php
	 * Эмуляцию для PHP версий ниже 5.5 взял отсюда:
	 * @link https://github.com/ramsey/array_column
	 * Как сказано: «It is written by PHP 5.5 array_column creator itself»
	 * @link http://stackoverflow.com/a/20746278/254475
	 * @param array $array
	 * @param mixed $column_key
	 * @param mixed $index_key [optional]
	 * @return array
	 */
	public function process($array, $column_key, $index_key = null) {
		// Using func_get_args() in order to check for proper number of
		// parameters and trigger errors exactly as the built-in array_column()
		// does in PHP 5.5.
		$argc = func_num_args();
		$params = func_get_args();
		if ($argc < 2) {
			trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
			return null;
		}
		if (!is_array($params[0])) {
			trigger_error(
				'array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given'
				, E_USER_WARNING
			);
			return null;
		}
		if (
				!is_int($params[1])
			&&
				!is_float($params[1])
			&&
				!is_string($params[1])
			&&
				!is_null($params[1])
			&&
				!(is_object($params[1]) && method_exists($params[1], '__toString'))
		) {
			trigger_error(
				'array_column(): The column key should be either a string or an integer', E_USER_WARNING
			);
			return false;
		}
		if (
				isset($params[2])
			&&
				!is_int($params[2])
			&&
				!is_float($params[2])
			&&
				!is_string($params[2])
			&&
				!(is_object($params[2]) && method_exists($params[2], '__toString'))
		) {
			trigger_error(
				'array_column(): The index key should be either a string or an integer', E_USER_WARNING
			);
			return false;
		}
		$paramsInput = $params[0];
		$paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;
		$paramsIndexKey = null;
		if (isset($params[2])) {
			if (is_float($params[2]) || is_int($params[2])) {
				$paramsIndexKey = (int)$params[2];
			}
			else {
				$paramsIndexKey = (string)$params[2];
			}
		}
		$resultArray = array();
		foreach ($paramsInput as $row) {
			$key = $value = null;
			$keySet = $valueSet = false;
			if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
				$keySet = true;
				$key = (string) $row[$paramsIndexKey];
			}
			if ($paramsColumnKey === null) {
				$valueSet = true;
				$value = $row;
			}
			else if (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
				$valueSet = true;
				$value = $row[$paramsColumnKey];
			}
			if ($valueSet) {
				if ($keySet) {
					$resultArray[$key] = $value;
				}
				else {
					$resultArray[] = $value;
				}
			}
		}
		return $resultArray;
	}

	/** @return Df_Core_Helper_ArrayColumn */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}


