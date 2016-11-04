<?php
namespace Df\C1\Cml2\Import\Data\Collection;
/**
 * Соответствует структуре вида:
		<ЗначениеРеквизита>
			<Наименование>ВидНоменклатуры</Наименование>
			<Значение>Продукты</Значение>
		</ЗначениеРеквизита>
 * В прежних версиях модуля 1С-Битрикс
 * подобная структура была возможна только в файлах каталога (catalog.xml)
 * внутри следующей структуры:
		<Товар>
			<ЗначенияРеквизитов>
				(...)
				<ЗначениеРеквизита>
					<Наименование>ВидНоменклатуры</Наименование>
					<Значение>Продукты</Значение>
				</ЗначениеРеквизита>
				(...)
			</ЗначенияРеквизитов>
		</Товар>
 * В новых версиях модуля 1С-Битрикс (ветка 4, CommerceML 2.0.8)
 * подобная структура была возможна и в файлах offers_*.xml:
	<Предложение>
		(...)
		<ЗначенияРеквизитов>
			(...)
			<ЗначениеРеквизита>
				<Наименование>Планируемая дата поступления</Наименование>
				<Значение>28.08.2014 0:00:00</Значение>
			</ЗначениеРеквизита>
			(...)
		</ЗначенияРеквизитов>
		(...)
	</Предложение>
 * При этом в файле offers_*.xml я заметил только единственный подобный реквизит:
 * «Планируемая дата поступления».
 * Он передаётся интернет-магазину в том случае,
 * когда для узла обмена 1С включена опция «Выгружать планируемую дату поступления товара».
 * При этом все остальные реквизиты по-прежнему передаются через файл catalog_*.xml.
 */
class RequisiteValues extends \Df\C1\Cml2\Import\Data\Collection {
	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return \Df\C1\Cml2\Import\Data\Entity\RequisiteValue::class;}

	/**
	 * @override
	 * @see \Df\Xml\Parser\Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return 'ЗначенияРеквизитов/ЗначениеРеквизита';}

	/**
	 * @used-by \Df\C1\Cml2\Import\Data\Entity::getRequisiteValues()
	 * @static
	 * @param \Df\Xml\X $e
	 * @return \Df\C1\Cml2\Import\Data\Collection\RequisiteValues
	 */
	public static function i(\Df\Xml\X $e) {return new self(array(self::$P__E => $e));}
}