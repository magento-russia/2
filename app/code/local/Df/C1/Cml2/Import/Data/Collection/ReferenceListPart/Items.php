<?php
namespace Df\C1\Cml2\Import\Data\Collection\ReferenceListPart;
use Df\C1\Cml2\Import\Data\Entity\ReferenceListPart\Item;
class Items extends \Df\C1\Cml2\Import\Data\Collection {
	/**
	 * @override
	 * @return \Df\Xml\X[]
	 */
	protected function getImportEntitiesAsSimpleXMLElementArray() {return dfc($this, function() {return
		parent::getImportEntitiesAsSimpleXMLElementArray() ?: $this->e()->xpath($this->itemPath2())
	;});}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Item::class;}

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
	 * @see \Df\Xml\Parser\Collection::itemPath()
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
	 * @used-by \Df\C1\Cml2\Import\Data\Entity\Attribute\ReferenceList::getItems()
	 * @static
	 * @param \Df\Xml\X $e
	 * @return self
	 */
	public static function i(\Df\Xml\X $e) {return new self(array(self::$P__E => $e));}
}