<?php
class Df_Spsr_Model_Locator extends Df_Shipping_Model_Locator {
	/**
	 * @override
	 * @return string
	 * @throws Exception
	 */
	public function getResult() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $result */
			$result = null;
			if (!$this->getCity()) {
				$this->throwExceptionNoCityOrigin();
			}
			/**
			 * Важное условие!
			 * Запрограммированный ниже алгоритм обязательно требует знания области склада магазина.
			 */
			if (!$this->getRegionName()) {
				$this->throwExceptionNoRegionOrigin();
			}
			/** @var Df_Spsr_Model_Location[] $locationsWithSameName */
			$locationsWithSameName = Df_Spsr_Model_Map::s()->getByCity($this->getCity());
			/** @var string $regionNameNormalized */
			$regionNameNormalized = Df_Spsr_Model_Location::i()->normalizeName($this->getRegionName());
			/** @var string $countryNameNormalized */
			$countryNameNormalized = Df_Cdek_Model_Location::i()->normalizeName($this->getCountryName());
			foreach ($locationsWithSameName as $location) {
				if (
						!$location->hasRegion()
					||
						/**
						 * Раньше условие было сформулировано неверно:
						 * if ($region === $this->getRegionName())
						 * Однако для Украины справочник областей в Российской сборке Magento
						 * содержит слово «область» в названии области (например: «Винницкая область»),
						 * а справочник, получаемый с сайта СПСР — не содержат
						 * (например: «Винницкая»)
						 */
						rm_contains($regionNameNormalized, $location->getRegion())
					||
						/**
						 * При запросе «Донецк» сервер СПСР возвращает в качестве региона «Украина»,
						 * а не «Донецкая обл.»:
							(
								[label] => Донецк / Украина ()
								[value] => Донецк
								[id] => 8175
								[city_owner_id] =>
								[region] => Украина
								[country] =>
							)
						 */
						($location->getRegion() === $countryNameNormalized)
				) {
					$result = $location->getId();
					break;
				}
			}
			if (!$result) {
				if (!is_null($this->getRequest())) {
					$this->throwExceptionInvalidLocation();
				}
				else {
					/**
					 * Тут неважно, какой будет исключительная ситуация.
					 * Она будет перехвачена в методе
					 * @see Df_Spsr_Model_Validator_Origin:validate()
					 */
					df_error();
				}
			}
			df_result_string($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Spsr_Model_Locator
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}