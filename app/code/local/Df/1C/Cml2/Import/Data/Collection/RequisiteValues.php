<?php
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
class Df_1C_Cml2_Import_Data_Collection_RequisiteValues extends Df_1C_Cml2_Import_Data_Collection {
	/**
	 * @override
	 * @see Df_Core_Xml_Parser_Collection::itemClass()
	 * @return string
	 */
	protected function itemClass() {return Df_1C_Cml2_Import_Data_Entity_RequisiteValue::_C;}

	/**
	 * @override
	 * @see Df_Core_Xml_Parser_Collection::itemPath()
	 * @return string|string[]
	 */
	protected function itemPath() {return 'ЗначенияРеквизитов/ЗначениеРеквизита';}

	/**
	 * @used-by Df_1C_Cml2_Import_Data_Entity::getRequisiteValues()
	 * @static
	 * @param Df_Core_Sxe $e
	 * @return Df_1C_Cml2_Import_Data_Collection_RequisiteValues
	 */
	public static function i(Df_Core_Sxe $e) {return new self(array(self::$P__E => $e));}
}