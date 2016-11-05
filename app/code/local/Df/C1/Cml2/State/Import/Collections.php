<?php
namespace Df\C1\Cml2\State\Import;
use Df\C1\Cml2\Import\Data\Collection\Attributes;
use Df\C1\Cml2\Import\Data\Collection\Categories;
use Df\C1\Cml2\Import\Data\Collection\Offers;
use Df\C1\Cml2\Import\Data\Collection\Products;
use Df\C1\Cml2\Import\Data\Entity\Offer;
use Df\C1\Cml2\State\Import as I;
class Collections extends \Df_Core_Model {
	/**
	 * Если каталог разбит на несколько файлов,
	 * то товарные свойства содержатся именно в файле со структурой:
		  Процедура ВыгрузитьИнформациюОТоварах:
	 			(...)
				Если Пакет = 0 тогда
					СписокСкладов 	= РазбитаяСтруктураДанных.Склады;
					СписокСоглашений= РазбитаяСтруктураДанных.Соглашения;
					СписокСвойств 	= РазбитаяСтруктураДанных.Свойства;
					СписокЕдиниц 	= РазбитаяСтруктураДанных.Единицы;
				КонецЕсли;
				(...)
	 * @return Attributes
	 */
	public function getAttributes() {return dfc($this, function() {return
		Attributes::i(I::s()->getFileCatalogAttributes()->getXml())				
	;});}

	/** @return Categories */
	public function getCategories() {return dfc($this, function() {return
		Categories::i(I::s()->getFileCatalogStructure()->getXml(), [
			'', 'КоммерческаяИнформация', 'Классификатор', 'Группы', 'Группа'
		])				
	;});}

	/** @return Offers */
	public function getOffers() {return dfc($this, function() {return
		Offers::i(I::s()->getFileOffers()->getXml())				
	;});}

	/** @return Offers */
	public function getOffersBase() {return dfc($this, function() {return
		I::s()->getDocumentCurrentAsOffers()->isBase()
		? $this->getOffers()
		: Offers::i(I::s()->getFileOffersBase()->getXml())				
	;});}
	
	/** @return Offer[] */
	public function getOffersConfigurableChild() {return dfc($this, function() {return
		array_filter(df_map(function(Offer $o) {return
			$o->isTypeConfigurableChild() ? $o : null				
		;}, $this->getOffers()))	
	;});}
	
	/** @return Offer[] */
	public function getOffersConfigurableParent() {return dfc($this, function() {return
		array_filter(df_map(function(Offer $o) {return
			$o->isTypeConfigurableParent() ? $o : null				
		;}, $this->getOffers()))	
	;});}
	
	/** @return \Df\C1\Cml2\Import\Data\Entity\Offer[] */
	public function getOffersSimple() {return dfc($this, function() {return
		array_filter(df_map(function(Offer $o) {return
			$o->isTypeSimple() ? $o : null				
		;}, $this->getOffers()))	
	;});}

	/** @return Products */
	public function getProducts() {return dfc($this, function() {return
		Products::i(I::s()->getFileCatalogProducts()->getXml())				
	;});}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}