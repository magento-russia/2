<?php
abstract class Df_1C_Cml2_Import_Processor_Product extends Df_1C_Cml2_Import_Processor {
	/** @return Df_1C_Cml2_Import_Data_Entity_Offer */
	protected function getEntityOffer() {return $this->getEntity();}
	
	/** @return Df_1C_Cml2_Import_Data_Entity_Product */
	protected function getEntityProduct() {
		/** @var Df_1C_Cml2_Import_Data_Entity_Product $result */
		$result = $this->getEntityOffer()->getEntityProduct();
		/**
		 * Инициализируем как следует прикладной тип.
		 * Этот вызов был добавлен в версии 2.16.2
		 * для устранения дефекта отключенности опции used_in_product_listing
		 * для внешнего идентификатора,
		 * что приводило к незагрузке внешнего идентификатора в коллекцию
		 *
		 * После этого код создания внешнего идентификатора был поправлен
		 * used_in_product_listing = 1 в методе
		 * @see rm_1c_add_external_id_attribute_to_set(),
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
		$this->_prop(self::$P__ENTITY, Df_1C_Cml2_Import_Data_Entity_Offer::_C);
	}

	/**
	 * @used-by Df_1C_Cml2_Import_Processor_Product_Part_Images::i()
	 * @used-by Df_1C_Cml2_Import_Processor_Product_Type_Configurable::i()
	 * @used-by Df_1C_Cml2_Import_Processor_Product_Type_Simple::i()
	 * @used-by Df_1C_Cml2_Import_Processor_Product_Type_Configurable::p()
	 * @used-by Df_1C_Cml2_Import_Processor_Product_Type_Configurable_Child::p()
	 * @used-by Df_1C_Cml2_Import_Processor_Product_Type_Configurable_New::p_new()
	 * @used-by Df_1C_Cml2_Import_Processor_Product_Type_Configurable_Update::p_update()
	 * @param string $class
	 * @param Df_1C_Cml2_Import_Data_Entity_Offer $offer
	 * @return Df_1C_Cml2_Import_Processor_Product
	 */
	protected static function ic($class, Df_1C_Cml2_Import_Data_Entity_Offer $offer) {
		return rm_ic($class, __CLASS__, array(self::$P__ENTITY => $offer));
	}
}