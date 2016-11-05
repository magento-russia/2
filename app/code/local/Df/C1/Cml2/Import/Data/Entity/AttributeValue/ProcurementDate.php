<?php
namespace Df\C1\Cml2\Import\Data\Entity\AttributeValue;
use Df\C1\Cml2\Import\Data\Entity\Attribute\Date;
use Df\C1\Cml2\Import\Data\Entity\Offer;
class ProcurementDate extends \Df\C1\Cml2\Import\Data\Entity\AttributeValue\OfferPart {
	/**
	 * @override
	 * @see \Df\Xml\Parser\Entity::e()
	 * @return \Df\Xml\X
	 */
	public function e() {return $this->getRequisiteDDValue()->e();}

	/**
	 * @override
	 * @return bool
	 */
	public function isValidForImport() {return !!$this->getRequisiteDDValue();}

	/**
	 * 2015-02-06
	 * @used-by \Df\C1\Cml2\Import\Processor\Product\Type::getProductDataNewOrUpdateAttributeValues()
	 * Метод @used-by Df_Dataflow_Model_Import_Abstract_Row::getFieldValue()
	 * проверяет принадлежность результата @see getValueForDataflow()
	 * одному из типов: string|int|float|bool|null
	 * @override
	 * @return string|int|float|bool|null
	 */
	public function getValueForDataflow() {return dfc($this, function() {return
		!$this->getValue() ? '' : $this->getValue()->toString(\Zend_Date::ISO_8601)
	;});}

	/**
	 * @override
	 * @return \Df_Catalog_Model_Resource_Eav_Attribute|null
	 */
	protected function findMagentoAttributeInRegistry() {return
		df_attributes()->findByCode($this->getAttributeCodeNew())
	;}

	/**
	 * @override
	 * @return string
	 */
	protected function getAttributeCodeNew() {return 'rm_1c__procurement_date';}

	/**
	 * @override
	 * @return string
	 */
	protected function getAttributeExternalId() {return 'Планируемая дата поступления';}

	/**
	 * @override
	 * @return string
	 */
	protected function getAttributeFrontendLabel() {return 'Планируемая дата поступления';}

	/**
	 * @override
	 * @return \Df\C1\Cml2\Import\Data\Entity\Attribute
	 */
	protected function getAttributeTemplate() {return new Date;}

	/**
	 * @override
	 * @return array(string => string|int)
	 */
	protected function getCreationParamsCustom() {return [
		'is_searchable' => 0
		,'is_filterable' => 0
		,'is_comparable' => 0
		,'is_filterable_in_search' => 0
		,'is_visible_in_advanced_search' => 0
	];}

	/** @return \Df\C1\Cml2\Import\Data\Entity\RequisiteValue|null */
	private function getRequisiteDDValue() {return dfc($this, function() {return
		$this->getOffer()->getRequisiteValues()->findByName('Планируемая дата поступления')
	;});}

	/**
	 * «Планируемая дата поступления» товарного предложения.
	 * Модуль 1С-Битрикс передаёт эту дату в том случае,
	 * когда в настройках узла обмена включена опция
	 * «Выгружать планируемую дату поступления товара»,
	 * и для товара имеются заказы поставщикам с будущей датой поступления.
		ЗапросПоПланируемойДатеПоступления = "ВЫБРАТЬ
		|	ЗаказыПоставщикамОстатки.Номенклатура КАК Номенклатура,
		|	ЗаказыПоставщикамОстатки.Характеристика КАК Характеристика,
		|	ВЫБОР
		|		КОГДА ЗаказПоставщикуТовары.Ссылка.ПоступлениеОднойДатой
		|			ТОГДА ЗаказПоставщикуТовары.Ссылка.ДатаПоступления
		|		ИНАЧЕ ЗаказПоставщикуТовары.ДатаПоступления
		|	КОНЕЦ КАК ДатаПоступления,
		|	ЗаказыПоставщикамОстатки.ЗаказаноОстаток КАК Количество
		|ПОМЕСТИТЬ ВремПланируемыеДатыПоступления
		|ИЗ
		|	РегистрНакопления.ЗаказыПоставщикам.Остатки КАК ЗаказыПоставщикамОстатки
		|		ЛЕВОЕ СОЕДИНЕНИЕ Документ.ЗаказПоставщику.Товары КАК ЗаказПоставщикуТовары
		|		ПО ЗаказыПоставщикамОстатки.ЗаказПоставщику = ЗаказПоставщикуТовары.Ссылка
		|			И ЗаказыПоставщикамОстатки.Номенклатура = ЗаказПоставщикуТовары.Номенклатура
		|			И ЗаказыПоставщикамОстатки.Характеристика = ЗаказПоставщикуТовары.Характеристика
		|ГДЕ
		|	ЗаказыПоставщикамОстатки.ЗаказПоставщику.ДатаПоступления >= &ТекДата
		|
		|ИНДЕКСИРОВАТЬ ПО
		|	Номенклатура,
		|	Характеристика";
	 *
	 * @return \Zend_Date|null
	 */
	private function getValue() {return dfc($this, function() {return
		!$this->getRequisiteDDValue()
		? null
		// Обратите внимание, что формат именно H, а не HH: «28.08.2014 0:00:00»
		: new \Zend_Date($this->getRequisiteDDValue()->getValue(), 'y-MM-dd H:mm:ss')
	;});}

	/**
	 * @param Offer $offer
	 * @return self
	 */
	public static function i(Offer $offer) {return new self([self::P__OFFER => $offer]);}
}