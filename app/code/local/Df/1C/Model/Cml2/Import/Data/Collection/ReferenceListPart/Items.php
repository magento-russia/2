<?php
class Df_1C_Model_Cml2_Import_Data_Collection_ReferenceListPart_Items
	extends Df_1C_Model_Cml2_Import_Data_Collection {
	/**
	 * @override
	 * @return Df_Varien_Simplexml_Element[]
	 */
	protected function getImportEntitiesAsSimpleXMLElementArray() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Varien_Simplexml_Element[] $result */
			$result = parent::getImportEntitiesAsSimpleXMLElementArray();
			$this->{__METHOD__} = $result ? $result : $this->e()->xpath($this->getItemsXmlPathAsArray2());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getItemClass() {
		return Df_1C_Model_Cml2_Import_Data_Entity_ReferenceListPart_Item::_CLASS;
	}

	/**
	 * @override
	 * @return string[]
	 */
	protected function getItemsXmlPathAsArray() {
		return
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
			array('ТипыЗначений', 'ТипЗначений', 'ВариантыЗначений', 'ВариантЗначения')
		;
	}

	/** @return string[] */
	private function getItemsXmlPathAsArray2() {
		return
			/**
			 * 1С:Управление торговлей 11:
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
			array('ВариантыЗначений', 'Справочник')
		;
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_Varien_Simplexml_Element $element
	 * @return Df_1C_Model_Cml2_Import_Data_Collection_ReferenceListPart_Items
	 */
	public static function i(Df_Varien_Simplexml_Element $element) {
		return new self(array(self::P__SIMPLE_XML => $element));
	}
}