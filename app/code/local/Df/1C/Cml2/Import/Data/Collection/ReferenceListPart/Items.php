<?php
class Df_1C_Cml2_Import_Data_Collection_ReferenceListPart_Items
	extends Df_1C_Cml2_Import_Data_Collection {
	/**
	 * @override
	 * @return Df_Core_Sxe[]
	 */
	protected function getImportEntitiesAsSimpleXMLElementArray() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Core_Sxe[] $result */
			$result = parent::getImportEntitiesAsSimpleXMLElementArray();
			$this->{__METHOD__} = $result ? $result : $this->e()->xpath($this->itemPath2());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @see Df_Core_Xml_Parser_Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Df_1C_Cml2_Import_Data_Entity_ReferenceListPart_Item::_C;}

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
	 * @override
	 * @see Df_Core_Xml_Parser_Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return 'ТипыЗначений/ТипЗначений/ВариантыЗначений/ВариантЗначения';}

	/** @return string[] */
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
	 * @return string
	 */
	private function itemPath2() {return 'ВариантыЗначений/Справочник';}

	/**
	 * @used-by Df_1C_Cml2_Import_Data_Entity_Attribute_ReferenceList::getItems()
	 * @static
	 * @param Df_Core_Sxe $e
	 * @return Df_1C_Cml2_Import_Data_Collection_ReferenceListPart_Items
	 */
	public static function i(Df_Core_Sxe $e) {return new self(array(self::$P__E => $e));}
}