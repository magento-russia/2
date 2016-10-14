<?php
/**
 * 2015-02-15
 * Основной сервис геолокации Российской сборки Magento.
 *
 * 1) Россия
 * Пример: 94.73.203.97 (Красноярск)
 * http://ipgeobase.ru:7020/geo?ip=94.73.203.97
 * Ответ сервера
	<ip-answer>
		<ip value="94.73.203.97">
			<inetnum>94.73.192.0 - 94.73.255.255</inetnum>
			<country>RU</country>
			<city>Красноярск</city>
			<region>Красноярский край</region>
			<district>Сибирский федеральный округ</district>
			<lat>56.001251</lat>
			<lng>92.885590</lng>
		</ip>
	</ip-answer>
 * Для России этот сервис работает очень хорошо.
 * Точно возвращает координаты: 6 знаков после запятой.
 * Хотелось бы ещё узнавать почтовый индекс,
 * но, видимо, при текущей точности публично доступных API
 * достоверно узнавать почтовый индекс нереально.
 *
 * 2) Украина
 * Пример: 31.135.147.31 (Сумы)
 * http://ipgeobase.ru:7020/geo?ip=31.135.147.31
 * Ответ сервера:
	<ip-answer>
		<ip value="31.135.147.31">
			<inetnum>31.135.128.0 - 31.135.159.255</inetnum>
			<country>UA</country>
			<city>Сумы</city>
			<region>Сумская область</region>
			<district>Центральная Украина</district>
			<lat>50.894405</lat>
			<lng>34.810600</lng>
		</ip>
	</ip-answer>
 * Для Украины этот сервис работает так же хорошо, как и для России.
 *
 * 3) Белоруссия
 * Пример: 93.125.50.49 (Могилёв)
 * http://ipgeobase.ru:7020/geo?ip=93.125.50.49
 * Ответ сервера:
	<ip-answer>
		<ip value="93.125.50.49">
			<inetnum>93.125.0.0 - 93.125.127.255</inetnum>
			<country>BY</country>
		</ip>
	</ip-answer>
 * Для Белоруссии этот сервис определяет только страну.
 *
 * 4) Казахстан
 * Пример: 178.91.6.28 (Кокшетау)
 * http://ipgeobase.ru:7020/geo?ip=178.91.6.28
 * Ответ сервера:
	<ip-answer>
		<ip value="178.91.6.28">
			<inetnum>178.88.0.0 - 178.91.255.255</inetnum>
			<country>KZ</country>
		</ip>
	</ip-answer>
 * Для Казахстана этот сервис определяет только страну.
 *
 * 5) Киргизия
 * Пример: 158.181.231.135 (Бишкек)
 * http://ipgeobase.ru:7020/geo?ip=158.181.231.135
 * Ответ сервера:
	<ip-answer>
		<ip value="158.181.231.135">
			<inetnum>158.181.128.0 - 158.181.255.255</inetnum>
			<country>KG</country>
		</ip>
	</ip-answer>
 * Для Киргизии этот сервис определяет только страну.
 */
class Df_Core_Model_Geo_Locator_Real_IpgeobaseRu extends Df_Core_Model_Geo_Locator_Real {
	/** @return string|null */
	public function getCity() {return $this->getProperty('city');}

	/** @return string|null */
	public function getCountryIso2() {return $this->getProperty('country');}

	/** @return string|null */
	public function getRegionName() {return $this->getProperty('region');}

	/** @return string|null */
	public function getLatude() {return $this->getProperty('lat');}

	/** @return string|null */
	public function getLongitude() {return $this->getProperty('lng');}

	/**
	 * @override
	 * @return string
	 */
	protected function getConverter() {return 'convertXml';}

	/**
	 * @override
	 * @return string
	 */
	protected function getPathBase() {return 'ip';}

	/**
	 * @override
	 * @return string
	 */
	protected function getUrlTemplate() {return 'http://ipgeobase.ru:7020/geo?ip={ip}';}

	/**
	 * @param string $ipAddress
	 * @return Df_Core_Model_Geo_Locator_Real_IpgeobaseRu
	 */
	public static function s($ipAddress) {return self::sc(__CLASS__, $ipAddress);}
}