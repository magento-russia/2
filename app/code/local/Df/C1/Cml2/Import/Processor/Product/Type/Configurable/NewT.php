<?php
namespace Df\C1\Cml2\Import\Processor\Product\Type\Configurable;
use Df\C1\Cml2\Import\Data\Entity\Offer;
use Df\C1\Cml2\Import\Data\Entity\OfferPart\OptionValue;
use Df\C1\Cml2\Import\Processor\Product\Type\Configurable;
class NewT extends Configurable {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		df_assert($this->getEntityOffer()->isTypeConfigurableParent());
		/** @var \Df_Catalog_Model_Product $product */
		$product = $this->getProductMagento();
		$product->addData(array(
			'can_save_configurable_attributes' => true
			,'can_save_custom_options' => true
			,'configurable_attributes_data' => $this->getConfigurableAttributesData()
			,'stock_data' => array(
				'use_config_manage_stock' => 1
				,'is_in_stock' => 1
				,'is_salable' => 1
			)
		));
		if ($this->getDocumentCurrentAsOffers()->hasPrices()) {
			$product->setData('configurable_products_data', $this->getConfigurableProductsData());
		}
		$product->saveRm($isMassUpdate = true);
		$product->reload();
		df_c1_reindex_product($product);
		df()->registry()->products()->addEntity($product);
		df_c1_log('Создан товар %s.', $product->getTitle());
	}

	/**
	 * @override
	 * @return \Df_Catalog_Model_Product
	 */
	protected function getProductMagento() {return dfc($this, function() {
		$this->getImporter()->import();
		return $this->getImporter()->getProduct();
	});}

	/**
	 * @override
	 * @return \Mage_Catalog_Model_Product_Type_Configurable
	 */
	protected function getTypeInstance() {return dfc($this, function() {
		/** @var \Mage_Catalog_Model_Product_Type_Configurable $result */
		$result = parent::getTypeInstance();
		$result->setUsedProductAttributeIds($this->getUsedProductAttributeIds());
		return $result;
	});}

	/** @return array(array(string => string|int)) */
	private function getConfigurableAttributesData() {return dfc($this, function() {return
		array_map(function(array $a) {return [
			'use_default' => 1
			,'position' => 0
			,'label' => dfa($a, 'frontend_label', $a['attribute_code'])
		] + $a;}, $this->getTypeInstance()->getConfigurableAttributesAsArray())
	;});}

	/** @return array(string => int) */
	private function getUsedProductAttributeIds() {return dfc($this, function() {
		/** @var array(string => int) $result */
		$result = [];
		/** @var string[] $labels */
		$labels = [];
		/** @var Offer|null $firstChild */
		$firstChild = df_first($this->getEntityOffer()->getConfigurableChildren());
		if ($firstChild) {
			foreach ($firstChild->getOptionValues() as $optionValue) {
				/** @var OptionValue $optionValue */
				/** @var \Df_Catalog_Model_Resource_Eav_Attribute $attribute */
				$attribute = $optionValue->getAttributeMagento();
				$result[$attribute->getName()] = $attribute->getId();
				$labels[]= $attribute->getFrontendLabel();
			}
		}
		df_c1_log("Для товара настраиваются параметры:\n%s.", df_csv_pretty_quote($labels));
		return $result;
	});}

	/**      
	 * Мы тут можем вызывать @uses \Df\C1\Cml2\Import\Processor\Product::getEntityOffer(),
	 * несмотря на то, что этот метод имеет область видимости «protected».
	 * http://php.net/manual/language.oop5.visibility.php
	 * «Visibility from other objects.
	 * Objects of the same type will have access to each others private and protected members
	 * even though they are not the same instances.»
	 * @param Configurable $masterProcessor
	 * @return void
	 */
	public static function p_new(Configurable $masterProcessor) {
		 self::ic(__CLASS__, $masterProcessor->getEntityOffer())->process()
	;}
}