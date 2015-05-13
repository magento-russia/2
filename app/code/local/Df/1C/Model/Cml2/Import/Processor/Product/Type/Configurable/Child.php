<?php
class Df_1C_Model_Cml2_Import_Processor_Product_Type_Configurable_Child
	extends Df_1C_Model_Cml2_Import_Processor_Product_Type_Simple_Abstract {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		if ($this->getEntityOffer()->isTypeConfigurableChild()) {
			$this->getImporter()->import();
			/** @var Df_Catalog_Model_Product $product */
			$product = $this->getImporter()->getProduct();
			df_h()->_1c()->cml2()->reindexProduct($product);
			rm_1c_log(
				'%s товар «%s».'
				,!is_null($this->getExistingMagentoProduct()) ? 'Обновлён' : 'Создан'
				,$product->getName()
			);
			df()->registry()->products()->addEntity($product);
		}
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getSku() {return df_sku_adapt($this->getEntityOffer()->getExternalId());}

	/**
	 * @override
	 * @return int
	 */
	protected function getVisibility() {
		return Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE;
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_1C_Model_Cml2_Import_Data_Entity_Offer $offer
	 * @return Df_1C_Model_Cml2_Import_Processor_Product_Type_Configurable_Child
	 */
	public static function i(Df_1C_Model_Cml2_Import_Data_Entity_Offer $offer) {
		return new self(array(self::P__ENTITY => $offer));
	}
}