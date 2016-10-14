<?php
abstract class Df_1C_Cml2_Import_Data_Entity_Attribute extends Df_1C_Cml2_Import_Data_Entity {
	/**
	 * 2015-02-06
	 * @used-by Df_1C_Cml2_Import_Data_Entity_ProductPart_AttributeValue_Custom::getValueForDataflow()
	 * @used-by Df_1C_Cml2_Import_Data_Entity_ProductPart_AttributeValue_Custom::getValueForObject()
	 * Метод @used-by Df_Dataflow_Model_Import_Abstract_Row::getFieldValue()
	 * проверяет принадлежность результата
	 * @used-by Df_1C_Cml2_Import_Data_Entity_ProductPart_AttributeValue_Custom::getValueForDataflow()
	 * одному из типов: string|int|float|bool|null
	 * @param string|int|float|bool|null $value
	 * @return string|int|float|bool|null
	 */
	public function convertValueToMagentoFormat($value) {return $value;}

	/** @return string */
	public function getBackendModel() {return '';}

	/** @return string */
	public function getBackendType() {return '';}

	/** @return string */
	public function getExternalTypeName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = self::_type($this->e());
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getFrontendInput() {return '';}

	/** @return string */
	public function getSourceModel() {return '';}

	/**
	 * @static
	 * @used-by Df_1C_Cml2_Import_Data_Collection_Attributes::itemClassAdvanced()
	 * @param Df_Core_Sxe $e
	 * @return Df_1C_Cml2_Import_Data_Entity
	 */
	public static function getClass(Df_Core_Sxe $e) {
		/** @var array(string => string) $map */
		static $map = array(
			'Справочник' => Df_1C_Cml2_Import_Data_Entity_Attribute_ReferenceList::_C
			,'Дата' => Df_1C_Cml2_Import_Data_Entity_Attribute_Date::_C
			,'Число' => Df_1C_Cml2_Import_Data_Entity_Attribute_Number::_C
			,'Булево' => Df_1C_Cml2_Import_Data_Entity_Attribute_Boolean::_C
			,self::TYPE__TEXT => Df_1C_Cml2_Import_Data_Entity_Attribute_Text::_C
		);
		return df_a($map, self::_type($e), Df_1C_Cml2_Import_Data_Entity_Attribute_Text::_C);
	}

	/**
	 * @param Df_Core_Sxe $e
	 * @return string
	 */
	private static function _type(Df_Core_Sxe $e) {
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
		$externalTypeNames = $e->xpathA('ТипыЗначений/ТипЗначений/Тип');
		if (!$externalTypeNames) {
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
			$externalTypeNames = $e->xpathA('ТипЗначений');
		}
		/** @var string $result */
		$result = null;
		if (!$externalTypeNames) {
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
			$result = rm_leaf_s(rm_first($externalTypeNames));
		}
		df_result_string($result);
		return $result;
	}

	/** @used-by Df_1C_Cml2_Import_Data_Collection_Attributes::itemClass() */
	const _C = __CLASS__;
	/**
	 * 2015-08-04
	 * Почему-то PHP не разрешает использовать приватную станическую переменную
	 * в качестве ключа статического массива:
	 * «[E_PARSE] syntax error, unexpected '$TYPE__TEXT' (T_VARIABLE),
	 * expecting identifier (T_STRING) or class (T_CLASS)»
	 * Приходится использовать публичную константу,
	 * хотя за пределами класса она не используется.
	 * @var string
	 */
	const TYPE__TEXT = '#Текст#';
}