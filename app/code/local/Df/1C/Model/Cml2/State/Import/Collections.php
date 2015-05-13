<?php
class Df_1C_Model_Cml2_State_Import_Collections extends Df_Core_Model_Abstract {
	/** @return Df_1C_Model_Cml2_Import_Data_Collection_Attributes */
	public function getAttributes() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_1C_Model_Cml2_Import_Data_Collection_Attributes::i(
					/**
					 * Если каталог разюит на несколько файлов,
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
					 */
					Df_1C_Model_Cml2_State_Import::s()->getFileCatalogStructure()->getXml()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Model_Cml2_Import_Data_Collection_Categories */
	public function getCategories() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_1C_Model_Cml2_Import_Data_Collection_Categories::i(
					Df_1C_Model_Cml2_State_Import::s()->getFileCatalogStructure()->getXml()
					,array(
						''
						,'КоммерческаяИнформация'
						,'Классификатор'
						,'Группы'
						,'Группа'
					)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Model_Cml2_Import_Data_Collection_Offers */
	public function getOffers() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_1C_Model_Cml2_Import_Data_Collection_Offers::i(
					Df_1C_Model_Cml2_State_Import::s()->getFileOffers()->getXml()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Model_Cml2_Import_Data_Collection_Offers */
	public function getOffersBase() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_1C_Model_Cml2_State_Import::s()->getDocumentCurrentAsOffers()->isBase()
				? $this->getOffers()
				: Df_1C_Model_Cml2_Import_Data_Collection_Offers::i(
					Df_1C_Model_Cml2_State_Import::s()->getFileOffersBase()->getXml()
				)
			;
		}
		return $this->{__METHOD__};
	}
	
	/** @return Df_1C_Model_Cml2_Import_Data_Entity_Offer[] */
	public function getOffersConfigurableChild() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_1C_Model_Cml2_Import_Data_Entity_Offer[] $result  */
			$result = array();
			foreach ($this->getOffers() as $offer) {
				/** @var Df_1C_Model_Cml2_Import_Data_Entity_Offer $offer */
				if ($offer->isTypeConfigurableChild()) {
					$result[]= $offer;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
	
	/** @return Df_1C_Model_Cml2_Import_Data_Entity_Offer[] */
	public function getOffersConfigurableParent() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_1C_Model_Cml2_Import_Data_Entity_Offer[] $result  */
			$result = array();
			foreach ($this->getOffers() as $offer) {
				/** @var Df_1C_Model_Cml2_Import_Data_Entity_Offer $offer */
				if ($offer->isTypeConfigurableParent()) {
					$result[]= $offer;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}
	
	/** @return Df_1C_Model_Cml2_Import_Data_Entity_Offer[] */
	public function getOffersSimple() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_1C_Model_Cml2_Import_Data_Entity_Offer[] $result  */
			$result = array();
			foreach ($this->getOffers() as $offer) {
				/** @var Df_1C_Model_Cml2_Import_Data_Entity_Offer $offer */
				if ($offer->isTypeSimple()) {
					$result[]= $offer;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Model_Cml2_Import_Data_Collection_Products */
	public function getProducts() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_1C_Model_Cml2_Import_Data_Collection_Products::i(
					Df_1C_Model_Cml2_State_Import::s()->getFileCatalogProducts()->getXml()
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_1C_Model_Cml2_State_Import_Collections */
	public static function s() {static $r; return $r ? $r : $r = new self;}

	/** @return Df_1C_Model_Cml2_State */
	private static function state() {return Df_1C_Model_Cml2_State::s();}
}