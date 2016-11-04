<?php
namespace Df\C1\Cml2\State\Import;
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
	 * @return \Df\C1\Cml2\Import\Data\Collection\Attributes
	 */
	public function getAttributes() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = \Df\C1\Cml2\Import\Data\Collection\Attributes::i(
				\Df\C1\Cml2\State\Import::s()->getFileCatalogAttributes()->getXml()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return \Df\C1\Cml2\Import\Data\Collection\Categories */
	public function getCategories() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = \Df\C1\Cml2\Import\Data\Collection\Categories::i(
				\Df\C1\Cml2\State\Import::s()->getFileCatalogStructure()->getXml()
				, array('', 'КоммерческаяИнформация', 'Классификатор', 'Группы', 'Группа')
			);
		}
		return $this->{__METHOD__};
	}

	/** @return \Df\C1\Cml2\Import\Data\Collection\Offers */
	public function getOffers() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = \Df\C1\Cml2\Import\Data\Collection\Offers::i(
				\Df\C1\Cml2\State\Import::s()->getFileOffers()->getXml()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return \Df\C1\Cml2\Import\Data\Collection\Offers */
	public function getOffersBase() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				\Df\C1\Cml2\State\Import::s()->getDocumentCurrentAsOffers()->isBase()
				? $this->getOffers()
				: \Df\C1\Cml2\Import\Data\Collection\Offers::i(
					\Df\C1\Cml2\State\Import::s()->getFileOffersBase()->getXml()
				)
			;
		}
		return $this->{__METHOD__};
	}
	
	/** @return \Df\C1\Cml2\Import\Data\Entity\Offer[] */
	public function getOffersConfigurableChild() {
		if (!isset($this->{__METHOD__})) {
			/** @var \Df\C1\Cml2\Import\Data\Entity\Offer[] $result  */
			$result = array();
			foreach ($this->getOffers() as $offer) {
				/** @var \Df\C1\Cml2\Import\Data\Entity\Offer $offer */
				if ($offer->isTypeConfigurableChild()) {
					$result[]= $offer;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
	
	/** @return \Df\C1\Cml2\Import\Data\Entity\Offer[] */
	public function getOffersConfigurableParent() {
		if (!isset($this->{__METHOD__})) {
			/** @var \Df\C1\Cml2\Import\Data\Entity\Offer[] $result  */
			$result = array();
			foreach ($this->getOffers() as $offer) {
				/** @var \Df\C1\Cml2\Import\Data\Entity\Offer $offer */
				if ($offer->isTypeConfigurableParent()) {
					$result[]= $offer;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
	
	/** @return \Df\C1\Cml2\Import\Data\Entity\Offer[] */
	public function getOffersSimple() {
		if (!isset($this->{__METHOD__})) {
			/** @var \Df\C1\Cml2\Import\Data\Entity\Offer[] $result  */
			$result = array();
			foreach ($this->getOffers() as $offer) {
				/** @var \Df\C1\Cml2\Import\Data\Entity\Offer $offer */
				if ($offer->isTypeSimple()) {
					$result[]= $offer;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return \Df\C1\Cml2\Import\Data\Collection\Products */
	public function getProducts() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = \Df\C1\Cml2\Import\Data\Collection\Products::i(
				\Df\C1\Cml2\State\Import::s()->getFileCatalogProducts()->getXml()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return \Df\C1\Cml2\State\Import\Collections */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}