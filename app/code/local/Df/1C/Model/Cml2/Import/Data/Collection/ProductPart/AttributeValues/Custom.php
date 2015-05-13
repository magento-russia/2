<?php
class Df_1C_Model_Cml2_Import_Data_Collection_ProductPart_AttributeValues_Custom
	extends Df_1C_Model_Cml2_Import_Data_Collection {
	/** @return Df_1C_Model_Cml2_Import_Data_Entity_ProductPart_AttributeValue_Custom[] */
	public function getItems() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = parent::getItems();
			/**
			 * 11 февраля 2014 года заметил,
			 * что 1С:Управление торговлей 11.1 при использовании версии 2.05 протокола CommerceML
			 * и версии 8.3 платформы 1С:Предприятие
			 * при обмене данными с интернет-магазином передаёт информацию о производителе
			 * не в виде стандартного атрибута, а иначе:
			 *
				<КоммерческаяИнформация ВерсияСхемы="2.05" ДатаФормирования="2014-02-11T15:32:13">
					(...)
					<Каталог СодержитТолькоИзменения="false">
						(...)
						<Товары>
							<Товар>
								(...)
								<Изготовитель>
									<Ид>9bf2b1bf-8e9a-11e3-bd2c-742f68ccd0fb</Ид>
									<Наименование>Tecumseh</Наименование>
									<ОфициальноеНаименование>Tecumseh</ОфициальноеНаименование>
								</Изготовитель>
								(...)
							</Товар>
						</Товары>
					</Каталог>
				</КоммерческаяИнформация>
			 *
			 * @link http://magento-forum.ru/topic/4277/
			 * @link http://dev.1c-bitrix.ru/community/forums/forum26/topic51568
			 * @link http://forum.infostart.ru/forum26/topic87833/
			 * @link http://www.hostcms.ru/forums/2/8746/
			 */
			/** @var Df_Varien_Simplexml_Element|null $xmlManufacturer */
			$xmlManufacturer = $this->e()->descend('Изготовитель');
			if ($xmlManufacturer) {
				$this->{__METHOD__}[]=
					Df_1C_Model_Cml2_Import_Data_Entity_ProductPart_AttributeValue_Custom_Option_Manufacturer::i(
						$xmlManufacturer, $this->getProduct()
					)
				;
			}
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @param Df_Varien_Simplexml_Element $entityAsSimpleXMLElement
	 * @return Df_1C_Model_Cml2_Import_Data_Entity
	 */
	protected function createItemFromSimpleXmlElement(
		Df_Varien_Simplexml_Element $entityAsSimpleXMLElement
	) {
		/** @var mixed[] $elementAsArray */
		$elementAsArray = $entityAsSimpleXMLElement->asCanonicalArray();
		/** @var string|null $valueId */
		$valueId = df_a($elementAsArray, 'ИдЗначения');
		if (!$valueId) {
			/**
			 * В магазине sb-s.com.ua встречается такая конструкция:
			 *
	 			<ЗначенияСвойства>
					<Ид>6cc37c6d-7d15-11df-901f-00e04c595000</Ид>
					<Значение>6cc37c6e-7d15-11df-901f-00e04c595000</Значение>
				</ЗначенияСвойства>
			 *
			 * Похожую (но другую!) конструкцию встретил 11 июля 2013 года в магазине belle.com.ua:
				<ЗначенияСвойств>
					<ЗначенияСвойства>
						<Ид>dd6bfa58-d7e9-11d9-bfbc-00112f3000a2</Ид>
						<Значение>Розница</Значение>
					</ЗначенияСвойства>
				</ЗначенияСвойств>
			 *
			 * Обратите внимание, что в данном случае
			 * внутри тега «Значение» находится непосредственно значение,
			 * а не идентификатор значения, как в примере выше из магазина sb-s.com.ua.
			 *
			 * Причем соответствующее свойство описано в import.xml следующим образом:
				<Классификатор>
					(...)
					<Свойства>
						<СвойствоНоменклатуры>
							<Ид>dd6bfa58-d7e9-11d9-bfbc-00112f3000a2</Ид>
							<Наименование>Канал сбыта</Наименование>
							<Обязательное>false</Обязательное>
							<Множественное>false</Множественное>
							<ИспользованиеСвойства>true</ИспользованиеСвойства>
						</СвойствоНоменклатуры>
					</Свойства>
				</Классификатор>
			 *
			 * Обратите внимание на использование тега «СвойствоНоменклатуры»
			 * вместо стандартного тега «Свойство».
			 * Причём это происходит в типовой конфигурации
			 * «Управление торговлей для Украины» редации 2.3
			 * (редакция платформы 1С:Предприятие — 10.3)
			 */
			/** @var string $value */
			$value = df_a($elementAsArray, 'Значение');
			if (df_h()->_1c()->cml2()->isExternalId($value)) {
				$valueId = $value;
			}
		}
		/** @var string $itemClass */
		$itemClass =
			!$valueId
			? Df_1C_Model_Cml2_Import_Data_Entity_ProductPart_AttributeValue_Custom::_CLASS
			: Df_1C_Model_Cml2_Import_Data_Entity_ProductPart_AttributeValue_Custom_Option::_CLASS
		;
		/** @var Df_1C_Model_Cml2_Import_Data_Entity $result */
		$result = df_model($itemClass, array(
			Df_1C_Model_Cml2_Import_Data_Entity::P__SIMPLE_XML => $entityAsSimpleXMLElement
			,Df_1C_Model_Cml2_Import_Data_Entity_ProductPart_AttributeValue_Custom
				::P__PRODUCT => $this->getProduct()
		));
		df_assert($result instanceof Df_1C_Model_Cml2_Import_Data_Entity);
		return $result;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {
		return Df_1C_Model_Cml2_Import_Data_Entity_ProductPart_AttributeValue_Custom::_CLASS;
	}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getItemsXmlPathAsArray() {
		return array('ЗначенияСвойств', 'ЗначенияСвойства');
	}

	/** @return Df_1C_Model_Cml2_Import_Data_Entity_Product */
	private function getProduct() {return $this->cfg(self::P__PRODUCT);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__PRODUCT, Df_1C_Model_Cml2_Import_Data_Entity_Product::_CLASS);
	}
	const _CLASS = __CLASS__;
	const P__PRODUCT = 'product';
	/**
	 * @static
	 * @param Df_Varien_Simplexml_Element $element
	 * @param Df_1C_Model_Cml2_Import_Data_Entity_Product $product
	 * @return Df_1C_Model_Cml2_Import_Data_Collection_ProductPart_AttributeValues_Custom
	 */
	public static function i(
		Df_Varien_Simplexml_Element $element, Df_1C_Model_Cml2_Import_Data_Entity_Product $product
	) {
		return new self(array(self::P__SIMPLE_XML => $element, self::P__PRODUCT => $product));
	}
}