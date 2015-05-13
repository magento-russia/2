<?php
abstract class Df_1C_Model_Cml2_Import_Processor_Product extends Df_1C_Model_Cml2_Import_Processor {
	/** @return Df_1C_Model_Cml2_Import_Data_Entity_Offer */
	protected function getEntityOffer() {return $this->getEntity();}
	
	/** @return Df_1C_Model_Cml2_Import_Data_Entity_Product */
	protected function getEntityProduct() {
		/** @var Df_1C_Model_Cml2_Import_Data_Entity_Product $result */
		$result = $this->getEntityOffer()->getEntityProduct();
		/**
		 * Инициализируем как следует прикладной тип.
		 * Этот вызов был добавлен в версии 2.16.2
		 * для устранения дефекта отключенности опции used_in_product_listing
		 * для внешнего идентификатора,
		 * что приводило к незагрузке внешнего идентификатора в коллекцию
		 *
		 * После этого код создания внешнего идентификатора был поправлен
		 * (used_in_product_listing = 1 в методе
		 * Df_1C_Helper_Cml2_AttributeSet::addExternalIdToAttributeSet),
		 * а $result->getAttributeSet() гарантирует его вызов.
		 */
		$result->getAttributeSet();
		return $result;
	}	

	/** @return Df_Catalog_Model_Product|null */
	protected function getExistingMagentoProduct() {
		// Грязный хак.
		// Причём кэшировать результат метода нельзя :(
		// Надо переделать этот метод.
		$this->getEntityProduct();
		return df()->registry()->products()->findByExternalId($this->getEntityOffer()->getExternalId());
	}
	
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__ENTITY, Df_1C_Model_Cml2_Import_Data_Entity_Offer::_CLASS);
	}
	const _CLASS = __CLASS__;
}