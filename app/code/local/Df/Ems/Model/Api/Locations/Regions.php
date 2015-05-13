<?php
class Df_Ems_Model_Api_Locations_Regions extends Df_Ems_Model_Api_Locations_Abstract {
	/**
	 * @override
	 * @return string
	 */
	protected function getLocationType() {return 'regions';}

	/** @return array(int => string) */
	public function getMapFromMagentoRegionIdToEmsRegionId() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(int => string) $result */
			/** @var string $cacheKey */
			$cacheKey = $this->getCache()->makeKey(array($this, __FUNCTION__));
			$result = $this->getCache()->loadDataArray($cacheKey);
			if (!is_array($result)) {
				$result =
					df_array_combine(
						array_map(
							array($this, 'getRegionIdInMagentoByRegionNameInEmsFormat')
							,df_column(
								$this->getLocationsAsRawArray()
								,'name'
							)
						)
						,df_column(
							$this->getLocationsAsRawArray()
							,'value'
						)
					)
				;
				$this->getCache()->saveDataArray($cacheKey, $result);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Core_Model_Cache */
	private function getCache() {
		if (!isset($this->{__METHOD__})) {
			// Я думаю, будет нормальным обновлять кэш раз в месяц.
			// Уж пожизненно его точно не стоит хранить, ибо тарифы служб доставки меняются.
			$this->{__METHOD__} = Df_Core_Model_Cache::i(Df_Shipping_Model_Request::CACHE_TYPE, 30 * 86400);
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => int) */
	private function getMapFromMagentoRegionNameToMagentoRegionId() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => int) $result */
			$result = array();
			foreach (df_h()->directory()->getRussianRegions() as $region) {
				/** @var Df_Directory_Model_Region $region */
				$result[mb_strtoupper($region->getName())] = $region->getId();
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @link http://php.net/manual/en/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * @link http://3v4l.org/OipEQ
	 *
	 * @param string $regionNameInEmsFormat
	 * @return int
	 */
	private function getRegionIdInMagentoByRegionNameInEmsFormat($regionNameInEmsFormat) {
		df_param_string($regionNameInEmsFormat, 0);
		/** @var array $replacements */
		$replacements =
			array(
				'СЕВЕРНАЯ ОСЕТИЯ-АЛАНИЯ РЕСПУБЛИКА' => 'СЕВЕРНАЯ ОСЕТИЯ — АЛАНИЯ РЕСПУБЛИКА'
				,'ТЫВА РЕСПУБЛИКА' => 'ТЫВА (ТУВА) РЕСПУБЛИКА'
				,'ХАНТЫ-МАНСИЙСКИЙ-ЮГРА АВТОНОМНЫЙ ОКРУГ' => 'ХАНТЫ-МАНСИЙСКИЙ АВТОНОМНЫЙ ОКРУГ'
			)
		;
		/** @var string $regionNameInMagentoFormat */
		$regionNameInMagentoFormat =
			df_a(
				$replacements
				,$regionNameInEmsFormat
				,$regionNameInEmsFormat
			)
		;
		df_assert_string($regionNameInMagentoFormat);
		/** @var string $result */
		$result =
			df_a(
				$this->getMapFromMagentoRegionNameToMagentoRegionId()
				,$regionNameInMagentoFormat
				,0
			)
		;
		/** @var array $expectedlyNotTranslated */
		$expectedlyNotTranslated =
			array(
				'КАЗАХСТАН'
				,'ТАЙМЫРСКИЙ АО'
				,'ТАЙМЫРСКИЙ ДОЛГАНО-НЕНЕЦКИЙ РАЙОН'
			)
		;
		if ((0 === $result) && !in_array($regionNameInMagentoFormat, $expectedlyNotTranslated)) {
			//df_notify('Не могу перевести: ' . $regionNameInMagentoFormat);
		}
		df_result_integer($result);
		return $result;
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Ems_Model_Api_Locations_Regions
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}