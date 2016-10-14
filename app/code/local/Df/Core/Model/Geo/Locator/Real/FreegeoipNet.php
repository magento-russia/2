<?php
/**
 * 2015-02-15
 * Дополнительный сервис геолокации Российской сборки Magento.
 * Основной сервис: @see Df_Core_Model_Geo_Locator_Real_IpgeobaseRu (ipgeobase.ru)
 * Основной сервис хорошо работает для России и Украины,
 * однако для других важных для Российской сборки Magentо стран
 * (Белоруссия, Казахстан, Киргизия) основной сервис ipgeobase.ru работает плохо:
 * он определяет только страну.
 *
 * По этой причине задействуем при необходимости доплнительный сервис freegeoip.net.
 * Обратите внимание что freegeoip.net возвращает все географические названия на английском языке.
 * Поэтому наиболее полезной информацией в ответе в данном случае являются не географические названия,
 * а географические координаты (широта и долгота),
 * которые freegeoip.net возвращает с точностью 3 знака после запятой
 * (для сравнения: основной сервис ipgeobase.ru @see Df_Core_Model_Geo_Locator_Real_IpgeobaseRu
 * для России и Украины возвращает географические координаты с точностью 6 знаков после запятой,
 * однако для остальных стран не возвращает их вовсе,
 * и вот тогда мы получаем географические координаты с freegeoip.net.
 *
 * 1) Белоруссия
 * Пример: 93.125.50.49 (Могилёв)
 * https://freegeoip.net/json/93.125.50.49
 * Ответ сервера:
	{
		ip: "93.125.50.49",
		country_code: "BY",
		country_name: "Belarus",
		region_code: "MA",
		region_name: "Mogilev",
		city: "Mogilev",
		zip_code: "",
		time_zone: "Europe/Minsk",
		latitude: 53.917,
		longitude: 30.345,
		metro_code: 0
	}
 *
 * 2) Казахстан
 * Пример: 178.91.6.28 (Кокшетау)
 * https://freegeoip.net/json/178.91.6.28
 * Ответ сервера:
	{
		ip: "178.91.6.28",
		country_code: "KZ",
		country_name: "Kazakhstan",
		region_code: "AKM",
		region_name: "Aqmola Oblysy",
		city: "KÃ¶kshetaÅ«",
		zip_code: "",
		time_zone: "Asia/Almaty",
		latitude: 53.282,
		longitude: 69.397,
		metro_code: 0
	}
 * Обратите внимание,
 * что сервис вернул название города (Кокшетау) не только латиницей, но и исковерканным.
 * Можно получить название неисковерканной латиницей в формате XML:
 * https://freegeoip.net/xml/178.91.6.28
	<Response>
		<IP>178.91.6.28</IP>
		<CountryCode>KZ</CountryCode>
		<CountryName>Kazakhstan</CountryName>
		<RegionCode>AKM</RegionCode>
		<RegionName>Aqmola Oblysy</RegionName>
		<City>Kökshetaū</City>
		<ZipCode/>
		<TimeZone>Asia/Almaty</TimeZone>
		<Latitude>53.282</Latitude>
		<Longitude>69.397</Longitude>
		<MetroCode>0</MetroCode>
	</Response>
 * Обратите внимание, что в данном случае сервис вернул название города (Kökshetaū)
 * не на английском языке, а просто некой латинской транслитерацией.
 * Но это всего лишь полезная информация на будущее:
 * в настоящее время мы используем из возвращаемой сервисом freegeoip.net информации
 * только географические координаты (широту и долготу).
 *
 * 3) Киргизия
 * Пример: 158.181.231.135 (Бишкек)
 * https://freegeoip.net/json/158.181.231.135
 * Ответ сервера:
	{
		ip: "158.181.231.135",
		country_code: "KG",
		country_name: "Kyrgyzstan",
		region_code: "",
		region_name: "",
		city: "",
		zip_code: "",
		time_zone: "Asia/Bishkek",
		latitude: 41,
		longitude: 75,
		metro_code: 0
	}
 * Обратите внимание,
 * что сервис вернул географические координаты (широту и долготу) без единого дробного знака.
 * Но и это лучше, чем ничего.
 */
class Df_Core_Model_Geo_Locator_Real_FreegeoipNet extends Df_Core_Model_Geo_Locator_Real {
	/** @return string|null */
	public function getLatude() {return $this->getProperty('Latude');}

	/** @return string|null */
	public function getLongitude() {return $this->getProperty('Longitude');}

	/**
	 * @override
	 * @return string
	 */
	protected function getConverter() {return 'convertJson';}

	/**
	 * @override
	 * @return string
	 */
	protected function getUrlTemplate() {return 'https://freegeoip.net/json/{ip}';}

	/**
	 * @param string $ipAddress
	 * @return Df_Core_Model_Geo_Locator_Real_FreegeoipNet
	 */
	public static function s($ipAddress) {return self::sc(__CLASS__, $ipAddress);}
}