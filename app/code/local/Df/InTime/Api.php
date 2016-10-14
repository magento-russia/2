<?php
class Df_InTime_Api extends Df_Core_Model {
	/**
		array(
			[0] => array(
				[CatalogNameEng] => Transportation types
				[Code] => 01
				[Name] => Дверь - Дверь
			)
			[1] => array(
				[CatalogNameEng] => Transportation types
				[Code] => 02
				[Name] => Дверь - Область
			)
			[2] => array(
				[CatalogNameEng] => Transportation types
				[Code] => 03
				[Name] => Дверь - Склад
			)
			[3] => array(
				[CatalogNameEng] => Transportation types
				[Code] => 04
				[Name] => Область - Дверь
			)
			[4] => array(
				[CatalogNameEng] => Transportation types
				[Code] => 05
				[Name] => Область - Область
			)
			[5] => array(
				[CatalogNameEng] => Transportation types
				[Code] => 06
				[Name] => Область - Склад
			)
			[6] => array(
				[CatalogNameEng] => Transportation types
				[Code] => 07
				[Name] => Склад - Дверь
			)
			[7] => array(
				[CatalogNameEng] => Transportation types
				[Code] => 08
				[Name] => Склад - Область
			)
			[8] => array(
				[CatalogNameEng] => Transportation types
				[Code] => 09
				[Name] => Склад - Склад
			)
		)
	 * @return mixed[]
	 */
	public function видыПеревозок() {return $this->справочник('Transportation types');}

	/**
		[0] => array(
			[CatalogNameEng] => Cargo
			[Code] => 00001
			[Name] => А/з+документы
		)
		[1] => array(
			[CatalogNameEng] => Cargo
			[Code] => 00002
			[Name] => Абразив. материалы
		)
		[2] => array(
			[CatalogNameEng] => Cargo
			[Code] => 00003
			[Name] => Аварийный стоп-выключатель
		)
		[3] => array(
			[CatalogNameEng] => Cargo
			[Code] => 00004
			[Name] => Автоаксессуары
		)
		[4] => array(
			[CatalogNameEng] => Cargo
			[Code] => 00005
			[Name] => авто-двиг.
		)
	 * @return mixed[]
	 */
	public function грузы() {return $this->справочник('Cargo');}

	/**
		[0] => array(
			[AppendField] => array(
				[AppendFieldName] => Main cargo type
				[AppendFieldValue] => 00339
			)
			[CatalogNameEng] => Cargo Items
			[Code] => 00071
			[Name] => Колеса легковые R13
		)
		[1] => array(
			[AppendField] => array(
				[AppendFieldName] => Main cargo type
				[AppendFieldValue] => 00338
			)
			[CatalogNameEng] => Cargo Items
			[Code] => 00072
			[Name] => Колеса легковые R13
		)
		[2] => array(
			[AppendField] => array(
				[AppendFieldName] => Main cargo type
				[AppendFieldValue] => 00922
			)
			[CatalogNameEng] => Cargo Items
			[Code] => 00073
			[Name] => Колеса легковые R13
		)
	 * @return mixed[]
	 */
	public function грузыЕдиничные() {return $this->справочник('Cargo Items');}

	/**
		array(
			[0] => array(
				[CatalogNameEng] => Additional services
				[Code] => 1
				[Name] => Подъем на этаж
			)
			[1] => array(
				[CatalogNameEng] => Additional services
				[Code] => 2
				[Name] => Спуск с этажа
			)
			[2] => array(
				[CatalogNameEng] => Additional services
				[Code] => 3
				[Name] => Обмен на груз
			)
			[3] => array(
				[CatalogNameEng] => Additional services
				[Code] => 4
				[Name] => Доставка багажа
			)
			[4] => array(
				[CatalogNameEng] => Additional services
				[Code] => 5
				[Name] => Поштучная сдача
			)
			[5] => array(
				[CatalogNameEng] => Additional services
				[Code] => 6
				[Name] => Обмен на документы
			)
			[6] => array(
				[CatalogNameEng] => Additional services
				[Code] => 7
				[Name] => Доставка на дату
			)
			[7] => array(
				[CatalogNameEng] => Additional services
				[Code] => 8
				[Name] => Доставка в супермаркет
			)
			[8] => array(
				[CatalogNameEng] => Additional services
				[Code] => 9
				[Name] => Выезд в супермаркет
			)
		)
	 * @return mixed[]
	 */
	public function дополнительныеУслуги() {return $this->справочник('Additional services');}

	/**
		[0] => array(
			[AppendField] => array(
				[0] => array(
					[AppendFieldName] => State
					[AppendFieldValue] => Донецкая область
				)
				[1] => array(
					[AppendFieldName] => PresenceOfWarehouse
					[AppendFieldValue] => Да
				)
				[2] => array(
					[AppendFieldName] => Area
					[AppendFieldValue] => Ясиноватский район
				)
				[3] => array(
					[AppendFieldName] => CodeWarehouseDelivery
					[AppendFieldValue] => 0541
				)
			)
			[CatalogNameEng] => List of settlements
			[Code] => 000000330
			[Name] => Ясиноватая
		)
		[1] => array(
			[AppendField] => array(
				[0] => array(
					[AppendFieldName] => State
					[AppendFieldValue] => Ровенская область
				)
				[1] => array(
					[AppendFieldName] => PresenceOfWarehouse
					[AppendFieldValue] => Да
				)
				[2] => array(
					[AppendFieldName] => Area
					[AppendFieldValue] => Рокитновский район
				)
				[3] => array(
					[AppendFieldName] => CodeWarehouseDelivery
					[AppendFieldValue] => 1810
				)
			)
			[CatalogNameEng] => List of settlements
			[Code] => 000000333
			[Name] => Рокитное
		)
	 * @return mixed[]
	 */
	public function населённыеПункты() {return $this->справочник('List of settlements');}

	/**
		[0] => array(
			[AppendField] => array(
				[0] => array(
					[AppendFieldName] => Adress
					[AppendFieldValue] => пр. Кирова, 135
				)
				[1] => array(
					[AppendFieldName] => City
					[AppendFieldValue] => Днепропетровск
				)
				[2] => array(
					[AppendFieldName] => LiterCode
					[AppendFieldValue] => DNT
				)
				[3] => array(
					[AppendFieldName] => WarehouseNumberInCity
					[AppendFieldValue] => 8
				)
				[4] => array(
					[AppendFieldName] => State
					[AppendFieldValue] => Днепропетровская область
				)
				[5] => array(
					[AppendFieldName] => Parcel
					[AppendFieldValue] => Да
				)
				[6] => array(
					[AppendFieldName] => Tel
					[AppendFieldValue] => 067-619-72-54
				)
			)
			[CatalogNameEng] => Departments
			[Code] => 0408
			[Name] => Днепропетровск Титова
		)
		[1] => array(
			[AppendField] => array(
				[0] => array(
					[AppendFieldName] => Adress
					[AppendFieldValue] => ул. Ак. Павлова,88/7
				)
				[1] => array(
					[AppendFieldName] => City
					[AppendFieldValue] => Харьков
				)
				[2] => array(
					[AppendFieldName] => LiterCode
					[AppendFieldValue] => HKR
				)
				[3] => array(
					[AppendFieldName] => WarehouseNumberInCity
					[AppendFieldValue] => 13
				)
				[4] => array(
					[AppendFieldName] => State
					[AppendFieldValue] => Харьковская область
				)
				[5] => array(
					[AppendFieldName] => Parcel
					[AppendFieldValue] => Да
				)
				[6] => array(
					[AppendFieldName] => Tel
					[AppendFieldValue] => 067-619-72-56
				)
			)
			[CatalogNameEng] => Departments
			[Code] => 2113
			[Name] => Харьков Академика Павлова
		)
	 * @return mixed[]
	 */
	public function представительства() {return $this->справочник('Departments');}

	/**
		array(
			[0] => array(
				[CatalogNameEng] => Payment types
				[Code] => OTP
				[Name] => Отправитель
			)
			[1] => array(
				[CatalogNameEng] => Payment types
				[Code] => PNP
				[Name] => Оплата 50 50
			)
			[2] => array(
				[CatalogNameEng] => Payment types
				[Code] => PRV
				[Name] => Произвольно
			)
			[3] => array(
				[CatalogNameEng] => Payment types
				[Code] => OTL
				[Name] => Оплата третьим лицом
			)
			[4] => array(
				[CatalogNameEng] => Payment types
				[Code] => POL
				[Name] => Получатель
			)
	)
	 * @return mixed[]
	 */
	public function типыОплаты() {return $this->справочник('Payment types');}

	/**
		array(
			[0] => array(
				[CatalogNameEng] => Payment types POD
				[Code] => POL
				[Name] => Получатель
			)
			[1] => array(
				[CatalogNameEng] => Payment types POD
				[Code] => OTP
				[Name] => Отправитель
			)
		)
	 * @return mixed[]
	 */
	public function типыОплатыПостСервис() {return $this->справочник('Payment types POD');}

	/**
		[0] => array(
			[CatalogNameEng] => Packages
			[Code] => 00002
			[Name] => 2. БЕЗ УПАКОВКИ
		)
		[1] => array(
			[CatalogNameEng] => Packages
			[Code] => 00003
			[Name] => 3. БЕЗ ДОУПАКОВКИ
		)
		[2] => array(
			[CatalogNameEng] => Packages
			[Code] => 00004
			[Name] => 4. НЕ СООТВЕТСТВУЕТ УПАКОВКЕ
		)
		[3] => array(
			[CatalogNameEng] => Packages
			[Code] => 00005
			[Name] => 5. ОТКАЗ КЛИЕНТА ОТ УПАКОВКИ
		)
		[4] => array(
			[CatalogNameEng] => Packages
			[Code] => 00006
			[Name] => 6. СОРТИРОВКА (услуга)
		)
		[5] => array(
			[CatalogNameEng] => Packages
			[Code] => 00007
			[Name] => бирка + хомут пластиковые
		)
		[6] => array(
			[CatalogNameEng] => Packages
			[Code] => 00008
			[Name] => Бирка пластиковая, шт
		)
		[7] => array(
			[CatalogNameEng] => Packages
			[Code] => 00009
			[Name] => Бланк для лазерной печати, шт
		)
	 * @return mixed[]
	 */
	public function упаковки() {return $this->справочник('Packages');}

	/**
	 * @return string[]
	 */
	public function списокСправочников() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->call('AllCatalog');
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param array(string => mixed $params)
	 * @return mixed[]
	 */
	public function условияДоставки(array $params) {
		return $this->call('CalculateTTN', 'CalculateRequest', null, '', array('CalculateTTN' => $params));
	}

	/**
	 * @used-by списокСправочников()
	 * @used-by справочник()
	 * @used-by условияДоставки()
	 * @param string $method
	 * @param string|null $requestKey
	 * @param string|null $authKey
	 * @param string|null $responseKey [optional]
	 * @param array(string => mixed) $params [optional]
	 * @return mixed[]
	 */
	private function call(
		$method, $requestKey = null, $authKey = null, $responseKey = null, array $params = array()
	) {
		if (!$authKey) {
			$authKey = 'Auth';
		}
		$params[$authKey] = array(
		   'ID' => '3963433', 'KEY' => '4580c96a-d2bf-11e4-bb27-001b21c28106'
	    );
		if (!$requestKey) {
			$requestKey = $method . 'Request';
		}
		/** @var mixed $response */
		$response = rm_stdclass_to_array(
			$this->_soap->$method(array($requestKey => $params))->return
		);
		/** @var string $state */
		$state = df_a($response, 'InterfaceState');
		if ('OK' !== $state) {
			unset($params[$authKey]);
			df_error(new Df_Core_Exception("[{$method}] {$state}", rm_print_params($params)));
		}
		/** @var mixed[] $result */
		$result =
			'' === $responseKey
			? $response
			: df_a_deep($response, $responseKey ? $responseKey : $method)
		;
		df_result_array($result);
		return $result;
	}

	/**
	 * @param string $name
	 * @return mixed[]
	 */
	private function справочник($name) {
		if (!isset($this->{__METHOD__}[$name])) {
			$this->{__METHOD__}[$name] = $this->call(
				'CatalogList', null, 'AuthData', 'ListCatalog/Catalog', array('CatalogNameEng' => $name)
			);
		}
		return $this->{__METHOD__}[$name];
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		$this->_soap = new SoapClient(Mage::getModuleDir('etc', 'Df_InTime') . DS . 'api.wsdl');
	}
	/** @var SoapClient  */
	private $_soap;

	/** @return Df_InTime_Api */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}