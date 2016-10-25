<?php
class Df_Ems_Api_Locations_Regions extends Df_Ems_Api_Locations {
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
				$result = array_flip(array_map(
					array($this, 'getRegionIdInMagentoByRegionNameInEmsFormat')
					, array_column($this->getLocationsAsRawArray(), 'name', 'value')
				));
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
			$this->{__METHOD__} = Df_Core_Model_Cache::i(\Df\Shipping\Request::CACHE_TYPE, 30 * 86400);
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => int) */
	private function getMapFromMagentoRegionNameToMagentoRegionId() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Directory_Model_Resource_Region_Collection $regions */
			$regions = df_h()->directory()->getRussianRegions();
			$this->{__METHOD__} = array_combine(
				df_strtoupper($regions->walk('getName')), $regions->walk('getId')
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @used-by getMapFromMagentoRegionIdToEmsRegionId()
	 * http://php.net/manual/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * http://3v4l.org/OipEQ
	 * @param string $regionNameInEmsFormat
	 * @return int
	 */
	private function getRegionIdInMagentoByRegionNameInEmsFormat($regionNameInEmsFormat) {
		df_param_string($regionNameInEmsFormat, 0);
		/** @var array(string => string) $replacements */
		$replacements = array(
			'СЕВЕРНАЯ ОСЕТИЯ-АЛАНИЯ РЕСПУБЛИКА' => 'СЕВЕРНАЯ ОСЕТИЯ — АЛАНИЯ РЕСПУБЛИКА'
			,'ТЫВА РЕСПУБЛИКА' => 'ТЫВА (ТУВА) РЕСПУБЛИКА'
			,'ХАНТЫ-МАНСИЙСКИЙ-ЮГРА АВТОНОМНЫЙ ОКРУГ' => 'ХАНТЫ-МАНСИЙСКИЙ АВТОНОМНЫЙ ОКРУГ'
		);
		/** @var string $regionNameInMagentoFormat */
		$regionNameInMagentoFormat =
			dfa($replacements, $regionNameInEmsFormat, $regionNameInEmsFormat)
		;
		/** @var string $result */
		$result =
			dfa(
				$this->getMapFromMagentoRegionNameToMagentoRegionId()
				,$regionNameInMagentoFormat
				,0
			)
		;
		/** @var array $expectedlyNotTranslated */
		$expectedlyNotTranslated = array(
			'КАЗАХСТАН'
			,'ТАЙМЫРСКИЙ АО'
			,'ТАЙМЫРСКИЙ ДОЛГАНО-НЕНЕЦКИЙ РАЙОН'
		);
		if ((0 === $result) && !in_array($regionNameInMagentoFormat, $expectedlyNotTranslated)) {
			//df_notify('Не могу перевести: ' . $regionNameInMagentoFormat);
		}
		df_result_integer($result);
		return $result;
	}

	const _C = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Ems_Api_Locations_Regions
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}