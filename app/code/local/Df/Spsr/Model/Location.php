<?php
/**
 * 2018-01-05
 * "[СПСР-Экспресс] Пример ответа на `https://www.spsr.ru/webapi/autocomplete_city?city=<value>`":
 * https://df.tips/t/294
 */
final class Df_Spsr_Model_Location extends Df_Shipping_Model_Location {
	/**
	 * Результатом должна быть строка вида: «63249745|3»
	 * @override
	 * @see Df_Core_Model_Abstract::getId()
	 * @used-by Df_Spsr_Model_Locator::getResult()
	 * @return string
	 */
	public function getId() {return "{$this['city_id']}|{$this['city_owner_id']}";}

	/**
	 * @override
	 * @see Df_Shipping_Model_Location::getRegion()
	 * @used-by Df_Shipping_Model_Location::hasRegion()
	 * @used-by Df_Spsr_Model_Locator::getResult()
	 * @return string
	 */
	public function getRegion() {
		if (!isset($this->{__METHOD__})) {
			$city = $this->normalizeRegionName($this['name']); /** @var string $city */
			/** @var string $result */
			if (in_array($city, $this->normalizeName(array('Москва', 'Санкт-Петербург')))) {
				$result = $city;
			}
			else {
				$result = $this->normalizeRegionName($this['region']);
				if ($this->normalizeName('Белоруссия') === $result) {
					$result = $this->normalizeName('Беларусь');
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by Df_Spsr_Model_Locator::getResult()
	 * @used-by Df_Spsr_Model_Map::getByCity()
	 * @used-by Df_Spsr_Model_Map::requestLocationsFromServer()
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Spsr_Model_Location
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}