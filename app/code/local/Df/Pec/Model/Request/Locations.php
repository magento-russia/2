<?php
class Df_Pec_Model_Request_Locations extends Df_Shipping_Model_Request {
	/** @return array(string => string) */
	public function getResponseAsArray() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => string) $result */
			/** @var string $cacheKey */
			$cacheKey = $this->getCache()->makeKey(array($this, __FUNCTION__));
			$result = $this->getCache()->loadDataArray($cacheKey);
			if (!is_array($result)) {
				$result = $this->parseLocations();
				$this->getCache()->saveDataArray($cacheKey, $result);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryHost() {return 'pecom.ru';}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/ru/calc/towns.php';}

	/**
	 * @override
	 * @return bool
	 */
	protected function needConvertResponseFrom1251ToUtf8() {return true;}

	/** @return array(mixed => mixed) */
	private function parseLocations() {
		/** @var array(mixed => mixed) $result */
		/** @var array(string => int) $locationsFlatten */
		$locationsFlatten =
			call_user_func_array(
				'array_merge'
				,array_map('array_flip', array_values($this->response()->json()))
			)
		;
		df_assert_array($locationsFlatten);
		/** @var string[] $locationNames */
		$locationNames = array_keys($locationsFlatten);
		df_assert_array($locationNames);
		/** @var string[] $locationNamesProcessed */
		$locationNamesProcessed = array_map(array($this, 'processLocationName'), $locationNames);
		df_assert_array($locationNamesProcessed);
		$result =
			df_array_combine(
				$locationNamesProcessed
				,array_map('strval', array_values($locationsFlatten))
			)
		;
		return $result;
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @link http://php.net/manual/en/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * @link http://3v4l.org/OipEQ
	 *
	 * @param string $locationName
	 * @return string
	 */
	private function processLocationName($locationName) {
		df_param_string($locationName, 0);
		return mb_strtoupper(df_trim(rm_preg_match('#([^\(]+)#u', $locationName)));
	}

	const _CLASS = __CLASS__;
	/** @return Df_Pec_Model_Request_Locations */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}