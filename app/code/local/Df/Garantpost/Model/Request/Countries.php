<?php
abstract class Df_Garantpost_Model_Request_Countries extends Df_Garantpost_Model_Request {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getOptionsSelector();

	/** @return array(string => int|null) */
	public function getResponseAsArray() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => int|null) $result */
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
	 * @param string $locationName
	 * @return string
	 */
	protected function normalizeLocationName($locationName) {
		$locationName = mb_strtoupper($locationName);
		$locationName = strtr($locationName, array(' О-ВА' => ' ОСТРОВА'));
		$locationName = df_trim($locationName, ', ');
		$locationName =
			df_a(
				array(
					'АНГИЛЬЯ' => 'АНГУИЛЛА'
					,'АНТИЛЬСКИЕ О.' => ''
					,'БЕЛОРУССИЯ' => 'БЕЛАРУСЬ'
					,'БОСНИЯ-ГЕРЦЕГОВИНА' => 'БЕРМУДСКИЕ ОСТРОВА'
					,'БРУНЕЙ' => 'БРУНЕЙ ДАРУССАЛАМ'
					,'ВИРГИНСКИЕ БРИТАНСКИЕ ОСТРОВА' => ''
					,'ГОНКОНГ' => 'ГОНКОНГ, ОСОБЫЙ АДМИНИСТРАТИВНЫЙ РАЙОН КИТАЯ'
					,'ДОМИНИКА' => 'ДОМИНИКАНСКАЯ РЕСПУБЛИКА'
					,'КАБО-ВЕРДЕ' => 'ОСТРОВА ЗЕЛЕНОГО МЫСА'
					,'КАЙМАН ОСТРОВА' => 'КАЙМАНОВЫ ОСТРОВА'
					,'КАНАРСКИЕ ОСТРОВА' => ''
					,'КИРГИЗИЯ' => 'КЫРГЫЗСТАН'
					,'КИТАЙ (КНР)' => 'КИТАЙ'
					,'КОНГО' => 'ДЕМОКРАТИЧЕСКАЯ РЕСПУБЛИКА КОНГО'
					,'КОРЕЯ (ЮЖНАЯ)' => 'РЕСПУБЛИКА КОРЕЯ'
					,'КОТ Д\'ИВУАР' => 'КОТ Д’ИВУАР'
					,'КЮРАСАО' => ''
					,'МАКАО' => 'МАКАО (ОСОБЫЙ АДМИНИСТРАТИВНЫЙ РАЙОН КНР)'
					,'МИКРОНЕЗИЯ' => ''
					,'МОЛДАВИЯ' => 'МОЛДОВА'
					,'МОНТСЕРРАТ' => 'МОНСЕРРАТ'
					,'ОАЭ' => 'ОБЪЕДИНЕННЫЕ АРАБСКИЕ ЭМИРАТЫ'
					,'ПАЛЕСТИНА' => 'ПАЛЕСТИНСКАЯ АВТОНОМИЯ'
					,'ПАПУА-Н.ГВИНЕЯ' => 'ПАПУА-НОВАЯ ГВИНЕЯ'
					,'САБА' => ''
					,'САЙПАН' => ''
					,'СЕНТ-БАРТОЛОМИ' => ''
					,'СЕНТ-ВИНСЕНТ' => 'СЕНТ-ВИНСЕНТ И ГРЕНАДИНЫ'
					,'СЕНТ-КИТС И НЕВИС' => 'СЕНТ-КИТТС И НЕВИС'
					,'СИРИЯ' => 'СИРИЙСКАЯ АРАБСКАЯ РЕСПУБЛИКА'
					,'СОЕДИНЕННЫЕ ШТАТЫ АМЕРИКИ' => 'США'
					,'ТЕРКС И КАЙКОС' => ''
					,'ЦАР' => 'ЦЕНТРАЛЬНО-АФРИКАНСКАЯ РЕСПУБЛИКА'
					,'ЧЕХИЯ' => 'ЧЕШСКАЯ РЕСПУБЛИКА'
					,'ЮАР' => 'ЮЖНАЯ АФРИКА'
				)
				,$locationName
				,$locationName
			)
		;
		/** @var string $result */
		$result = mb_strtoupper($locationName);
		df_result_string($result);
		return $result;
	}

	/** @return array(string => int|null) */
	private function parseLocations() {
		/** @var array(string => int|null) $result */
		$result = array();
		/** @var array $locations */
		$locations = array();
		/** @var array(string => string) $options */
		$options = $this->response()->options($this->getOptionsSelector());
		foreach ($options as $locationName => $locationId) {
			/** @var string $locationName */
			/** @var int $locationId */
			$locationName = $this->normalizeLocationName($locationName);
			if ($locationName) {
				$locations[$locationName]= $locationId;
			}
		}
		/** @var Df_Directory_Model_Resource_Country_Collection $countriesInMagentoFormatCollection */
		$countriesInMagentoFormatCollection = Df_Directory_Model_Resource_Country_Collection::i();
		/** @var array $countriesInMagentoFormat */
		$countriesInMagentoFormat =
			$countriesInMagentoFormatCollection
				->loadData()
				->toOptionArray(false)
		;
		/** @var array $countriesInMagentoFormatAsMap */
		$countriesInMagentoFormatAsMap =
			df_array_combine(
				df_column($countriesInMagentoFormat, 'value')
				,rm_uppercase(df_column($countriesInMagentoFormat, 'label'))
			)
		;
		df_assert_array($countriesInMagentoFormatAsMap);
		foreach ($countriesInMagentoFormatAsMap as $codeInMagento => $labelInMagento) {
			/** @var string $codeInMagento */
			/** @var string $labelInMagento */
			df_assert_string($codeInMagento);
			df_assert_string($labelInMagento);
			/** @var int|null $codeInService */
			$codeInService = df_a($locations, $labelInMagento);
			if (!is_null($codeInService)) {
				df_assert_integer($codeInService);
				$result[$codeInMagento] = $codeInService;
			}
		}
		return $result;
	}

	const _CLASS = __CLASS__;
}