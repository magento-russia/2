<?php
abstract class Df_1C_Model_Cml2_Import_Data_Entity_Attribute
	extends Df_1C_Model_Cml2_Import_Data_Entity {
	/**
	 * @param string $valueAsString
	 * @return string
	 */
	public function convertValueToMagentoFormat($valueAsString) {return $valueAsString;}

	/** @return string */
	public function getBackendModel() {return '';}

	/** @return string */
	public function getBackendType() {return '';}

	/** @return string */
	public function getExternalTypeName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = self::getExternalTypeNameStatic($this->e());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getFrontendInput() {return '';}

	/** @return string */
	public function getSourceModel() {return '';}

	/**
	 * @static
	 * @param Df_Varien_Simplexml_Element $entityAsSimpleXMLElement
	 * @return Df_1C_Model_Cml2_Import_Data_Entity
	 */
	public static function create(Df_Varien_Simplexml_Element $entityAsSimpleXMLElement) {
		/** @var string $elementType */
		$elementType = self::getExternalTypeNameStatic($entityAsSimpleXMLElement);
		/** @var string $itemClass */
		$itemClass =
			df_a(
				self::getTypeMap()
				,$elementType
				,Df_1C_Model_Cml2_Import_Data_Entity_Attribute_Text::_CLASS
			)
		;
		/** @var Df_1C_Model_Cml2_Import_Data_Entity $result */
		$result =
			df_model($itemClass, array(
				Df_1C_Model_Cml2_Import_Data_Entity::P__SIMPLE_XML => $entityAsSimpleXMLElement
			))
		;
		df_assert($result instanceof Df_1C_Model_Cml2_Import_Data_Entity);
		return $result;
	}

	/**
	 * @param Df_Varien_Simplexml_Element $entityAsSimpleXMLElement
	 * @return string
	 */
	private static function getExternalTypeNameStatic(
		Df_Varien_Simplexml_Element $entityAsSimpleXMLElement
	) {
		/**
		 * 1С:Управление торговлей 10.2 + дополнение от Битрикса:
		 *
			<Свойство>
				<Ид>b79b0fdd-c8a5-11e1-a928-4061868fc6eb</Ид>
				<Наименование>Производитель</Наименование>
				<ТипыЗначений>
					<ТипЗначений>
						<Тип>Справочник</Тип>
						<Описание>Значения свойств объектов</Описание>
						<ВариантыЗначений>
							<ВариантЗначения>
								<Ид>b79b0fde-c8a5-11e1-a928-4061868fc6eb</Ид>
								<Значение>Sony</Значение>
							</ВариантЗначения>
							<ВариантЗначения>
								<Ид>65fa6244-c8af-11e1-a928-4061868fc6eb</Ид>
								<Значение>Pentax</Значение>
							</ВариантЗначения>
						</ВариантыЗначений>
					</ТипЗначений>
				</ТипыЗначений>
			</Свойство>
		 */
		/** @var SimpleXMLElement[]|bool $externalTypeNames */
		$externalTypeNames = $entityAsSimpleXMLElement->xpathA('ТипыЗначений/ТипЗначений/Тип');
		if (0 === count($externalTypeNames)) {
			/**
			 * 1С:Управление торговлей 11,
			 * Управление торговлей для Украины 2.3.18.1:
			 *
				<Свойство>
					<Ид>69a1a785-f26f-11e1-990a-000c292511ad</Ид>
					<Наименование>Разрешение</Наименование>
					<ТипЗначений>Справочник</ТипЗначений>
					<ВариантыЗначений>
						<Справочник>
							<ИдЗначения>69a1a786-f26f-11e1-990a-000c292511ad</ИдЗначения>
							<Значение>HD Ready</Значение>
						</Справочник>
						<Справочник>
							<ИдЗначения>69a1a787-f26f-11e1-990a-000c292511ad</ИдЗначения>
							<Значение>Full HD</Значение>
						</Справочник>
					</ВариантыЗначений>
				</Свойство>
			 */
			$externalTypeNames = $entityAsSimpleXMLElement->xpathA('ТипЗначений');
		}
		/** @var string $result */
		$result = null;
		if (0 === count($externalTypeNames)) {
			/**
			 * Заметил, что в конфигурации «Управление торговлей для Украины»
			 * редакции 2.3.18.1 (магазин sb-s.com.ua)
			 * для одного свойства, которое в 1С настроено как булево,
			 * при выгрузке система не указывает тип значений.
			 * Выгружается оно системой вот так:
			 *
				<Свойство>
					<Ид>65ab3c04-88d2-11df-8003-00e04c595000</Ид>
					<Наименование>Публиковать</Наименование>
					<ДляТоваров>true</ДляТоваров>
				</Свойство>
			 *
			 * 11 июля 2013 года заметил,
			 * что в конфигурации «Управление торговлей для Украины»
			 * редакции 2.3.18.1 (магазин belle.com.ua)
			 * для одного свойства, которое в 1С настроено как «Значения свойств объектов»
			 * (по сути, локальный справочник), при выгрузке система не указывает тип значений.
			 * Выгружается оно системой вот так:
				<Свойства>
					<СвойствоНоменклатуры>
						<Ид>dd6bfa58-d7e9-11d9-bfbc-00112f3000a2</Ид>
						<Наименование>Канал сбыта</Наименование>
						<Обязательное>false</Обязательное>
						<Множественное>false</Множественное>
						<ИспользованиеСвойства>true</ИспользованиеСвойства>
					</СвойствоНоменклатуры>
				</Свойства>
			 */
			$result = self::TYPE__TEXT;
		}
		else {
			$result = (string)(df_a($externalTypeNames, 0));
		}
		df_result_string($result);
		return $result;
	}
	/** @return array(string => string) */
	private static function getTypeMap() {
		/** @var array(string => string) $typeMap */
		static $typeMap;
		if (!isset($typeMap)) {
			$typeMap = array(
				self::TYPE__REFERENCE_LIST => Df_1C_Model_Cml2_Import_Data_Entity_Attribute_ReferenceList::_CLASS
				,self::TYPE__DATE => Df_1C_Model_Cml2_Import_Data_Entity_Attribute_Date::_CLASS
				,self::TYPE__NUMBER => Df_1C_Model_Cml2_Import_Data_Entity_Attribute_Number::_CLASS
				,self::TYPE__BOOLEAN => Df_1C_Model_Cml2_Import_Data_Entity_Attribute_Boolean::_CLASS
				,self::TYPE__TEXT => Df_1C_Model_Cml2_Import_Data_Entity_Attribute_Text::_CLASS
			);
		}
		return $typeMap;
	}

	/**
	 * Используется из @see Df_1C_Model_Cml2_Import_Data_Collection_Attributes::getItemClass()
	 */
	const _CLASS = __CLASS__;
	const TYPE__BOOLEAN = 'Булево';
	const TYPE__DATE = 'Дата';
	const TYPE__NUMBER = 'Число';
	const TYPE__REFERENCE_LIST = 'Справочник';
	const TYPE__TEXT = '#Текст#';
}