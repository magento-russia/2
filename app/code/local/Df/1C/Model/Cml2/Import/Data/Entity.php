<?php
abstract class Df_1C_Model_Cml2_Import_Data_Entity extends Df_Core_Model_SimpleXml_Parser_Entity {
	/** @return string */
	public function getExternalId() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->getEntityParam('Ид');
			// Идентификатор у передаваемого из 1С:Управление торговлей
			// в интернет-магазин товара должен быть всегда
			df_result_string_not_empty($this->{__METHOD__});
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	public function getId() {
		/**
		 * Обратите внимание, что именно getId должен вызывать getExternalId,
		 * а не наоборот, потому что раньше у нас был только метод getExternalId,
		 * и потомки данного класса перекрывают именно метод getExternalId
		 */
		return $this->getExternalId();
	}

	/**
	 * Обратите внимание, что метод может вернуть null.
	 * В частности, новая версия модуля 1С-Битрикс (ветка 4, CommerceML 2.0.8)
	 * передаёт цены на товарные предложения отдельно от самих товарных предложений.
	 * Файл товарных предложений (offers_*.xml):
			<Предложение>
				<Ид>cbcf4968-55bc-11d9-848a-00112f43529a</Ид>
				<НомерВерсии>AAAAAQAAACk=</НомерВерсии>
				<ПометкаУдаления>false</ПометкаУдаления>
				<Наименование>Барбарис (конфеты)</Наименование>
			</Предложение>
	 * Файл цен (prices_*.xml):
			<Предложение>
				<Ид>cbcf4968-55bc-11d9-848a-00112f43529a</Ид>
				<Цены>
					<Цена>
						<Представление>60,94 RUB за кг</Представление>
						<ИдТипаЦены>ceb752cd-c697-11e2-8026-0015e9b8c48d</ИдТипаЦены>
						<ЦенаЗаЕдиницу>60.94</ЦенаЗаЕдиницу>
						<Валюта>RUB</Валюта>
					</Цена>
				</Цены>
			</Предложение>
	 * Так вот, файл цен не содержит наименования товарных предложений,
	 * однако это не мешает нам успешно обрабаатывать такой файл,
	 * потому что файл товарных предложений импортируется всегда ранее файла цен,
	 * и товары уже присутствуют в базе данных магазина,
	 * достаточно лишь обновить их цены, наименование нам особо и не нужно.
	 *
	 * Аналогично, в новой версии модуля 1С-Битрикс (ветка 4, CommerceML 2.0.8)
	 * 1С передаёт товарные остатки отдельным файлом rests_*.xml, который имеет следующую структуру:
			<Предложение>
				<Ид>cbcf4968-55bc-11d9-848a-00112f43529a</Ид>
				<Остатки>
					<Остаток>
						<Количество>765</Количество>
					</Остаток>
				</Остатки>
			</Предложение>
	 * Как можно увидеть, наименование товарного предложения и в этом случае отсутствует.
	 *
	 * @override
	 * @return string|null
	 */
	public function getName() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set($this->getEntityParam('Наименование'));
		}
		return rm_n_get($this->{__METHOD__});
	}

	/**
	 * На данный момент реквизиты могут быть только у сущностей product и offer.
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
	 * @param string
	 * @return string
	 */
	protected function getRequisiteValue($name) {
		/** @var Df_1C_Model_Cml2_Import_Data_Entity_RequisiteValue $requisiteValue */
		$requisiteValue = $this->getRequisiteValues()->findByName($name);
		return $requisiteValue ? $requisiteValue->getValue() : '';
	}

	/** @return Df_1C_Model_Cml2_Import_Data_Collection_RequisiteValues */
	protected function getRequisiteValues() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_1C_Model_Cml2_Import_Data_Collection_RequisiteValues::i($this->e())
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Данный метод никак не связан данным с классом,
	 * однако включён в класс для удобного доступа объектов класса к реестру
	 * (чтобы писать $this->getState() вместо Df_1C_Model_Cml2_State::s())
	 * @return Df_1C_Model_Cml2_State
	 */
	protected function getState() {return Df_1C_Model_Cml2_State::s();}

	const _CLASS = __CLASS__;
}