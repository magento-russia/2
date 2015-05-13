<?php
class Df_1C_Model_Cml2_Import_Data_Collection_Attributes
	extends Df_1C_Model_Cml2_Import_Data_Collection {
	/**
	 * @override
	 * @param Df_Varien_Simplexml_Element $entityAsSimpleXMLElement
	 * @return Df_1C_Model_Cml2_Import_Data_Entity
	 */
	protected function createItemFromSimpleXmlElement(Df_Varien_Simplexml_Element $entityAsSimpleXMLElement) {
		return Df_1C_Model_Cml2_Import_Data_Entity_Attribute::create($entityAsSimpleXMLElement);
	}

	/**
	 * @override
	 * @return Df_Varien_Simplexml_Element[]
	 */
	protected function getImportEntitiesAsSimpleXMLElementArray() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Varien_Simplexml_Element[] $result */
			$result = parent::getImportEntitiesAsSimpleXMLElementArray();
			/** @var Df_Varien_Simplexml_Element[] $entitiesFromAdditionalPath */
			$entitiesFromAdditionalPath =
				$this->e()->xpath($this->getItemsXmlPathAsArrayAdditional())
			;
			if (is_array($entitiesFromAdditionalPath)) {
				$result = array_merge($result, $entitiesFromAdditionalPath);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {return Df_1C_Model_Cml2_Import_Data_Entity_Attribute::_CLASS;}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getItemsXmlPathAsArray() {
		return array(
			''
			,'КоммерческаяИнформация'
			,'Классификатор'
			,'Свойства'
			,'Свойство'
		);
	}

	/**
	 * 11 июля 2013 года в магазине belle.com.ua
	 * («Управление торговлей для Украины» редации 2.3
	 * ,редакция платформы 1С:Предприятие — 10.3) заметил,
	 * что одно свойство описано в import.xml следующим образом:
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
	 * Обратите внимание на использование тега «СвойствоНоменклатуры»
	 * вместо стандартного тега «Свойство».
	 * Причём это происходит в типовой конфигурации
	 * (смотрел программный код той же конфигурации другого магазина).
	 * @return string[]
	 */
	private function getItemsXmlPathAsArrayAdditional() {
		return array(
			''
			,'КоммерческаяИнформация'
			,'Классификатор'
			,'Свойства'
			,'СвойствоНоменклатуры'
		);
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_Varien_Simplexml_Element $xml
	 * @return Df_1C_Model_Cml2_Import_Data_Collection_Attributes
	 */
	public static function i(Df_Varien_Simplexml_Element $xml) {
		return new self(array(self::P__SIMPLE_XML => $xml));
	}
}