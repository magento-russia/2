<?php
class Df_C1_Cml2_Import_Processor_Product_Type_Configurable_Child
	extends Df_C1_Cml2_Import_Processor_Product_Type_Simple_Abstract {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		if ($this->getEntityOffer()->isTypeConfigurableChild()) {
			$this->getImporter()->import();
			/** @var Df_Catalog_Model_Product $product */
			$product = $this->getImporter()->getProduct();
			df_c1_reindex_product($product);
			df_c1_log(
				'%s товар %s.'
				,!is_null($this->getExistingMagentoProduct()) ? 'Обновлён' : 'Создан'
				,$product->getTitle()
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

	/**
	 * @used-by Df_C1_Cml2_Import_Processor_Product_Type_Configurable::importChildren()
	 * @param Df_C1_Cml2_Import_Data_Entity_Offer $offer
	 * @return void
	 */
	public static function p(Df_C1_Cml2_Import_Data_Entity_Offer $offer) {
		self::ic(__CLASS__, $offer)->process();
	}
}