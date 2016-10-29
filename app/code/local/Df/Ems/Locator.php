<?php
// 2016-10-25
namespace Df\Ems;
use Df_Directory_Model_Country as Country;
// http://www.emspost.ru/ru/corp_clients/dogovor_docements/api/
class Locator extends \Df\Shipping\Locator {
	/**
	 * 2016-10-25
	 * @param Country $country
	 * @param int|null $regionId
	 * @param string $city
	 * @return string|null
	 */
	public static function find(Country $country, $regionId, $city) {return
		$country->isRussia() ? self::city($city) : self::country($country->getNameRussian())
	;}

	/**
	 * 2016-10-25
	 * @override
	 * @see \Df\Shipping\Locator::_map()
	 * @used-by \Df\Shipping\Locator::map()
	 * @param string $type
	 * @return array(string => string|int|array(string|int))
	 */
	protected function _map($type) {return array_column($this->locations($type), 'value', 'name');}

	/**
	 * 2016-10-25
	 * @param string $type
	 * @return array(array(string => string))
	 */
	private function locations($type) {return dfc($this, function($type) {return
		Request::i(['method' => 'ems.get.locations', 'plain' => df_bts(true), 'type' => $type])
			->p('locations')
	;}, func_get_args());}

	/**
	 * 2016-10-25
	 * @param string $name
	 * @return int|null
	 */
	private static function city($name) {return self::_find('cities', $name);}

	/**
	 * 2016-10-25
	 * @param string $name
	 * @return int|null
	 */
	private static function country($name) {return self::_find('countries', $name);}

	/**
	 * 2016-10-25
	 * @param int $id
	 * @return int|null
	 */
	private static function region($id) {return
		self::_find('regions', dftr(df_region_name_uc($id), [
			'СЕВЕРНАЯ ОСЕТИЯ — АЛАНИЯ РЕСПУБЛИКА' => 'СЕВЕРНАЯ ОСЕТИЯ-АЛАНИЯ РЕСПУБЛИКА'
			,'ТЫВА (ТУВА) РЕСПУБЛИКА' => 'ТЫВА РЕСПУБЛИКА'
			,'ХАНТЫ-МАНСИЙСКИЙ АВТОНОМНЫЙ ОКРУГ' => 'ХАНТЫ-МАНСИЙСКИЙ-ЮГРА АВТОНОМНЫЙ ОКРУГ'
		]));
	}
}