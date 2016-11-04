<?php
namespace Df\C1\Cml2\Import\Processor\Product\Type\Configurable;
class Child extends \Df\C1\Cml2\Import\Processor\Product\Type\Simple\AbstractT {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		if ($this->getEntityOffer()->isTypeConfigurableChild()) {
			$this->getImporter()->import();
			/** @var \Df_Catalog_Model_Product $product */
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
		return \Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE;
	}

	/**
	 * @used-by \Df\C1\Cml2\Import\Processor\Product\Type\Configurable::importChildren()
	 * @param \Df\C1\Cml2\Import\Data\Entity\Offer $offer
	 * @return void
	 */
	public static function p(\Df\C1\Cml2\Import\Data\Entity\Offer $offer) {
		self::ic(__CLASS__, $offer)->process();
	}
}