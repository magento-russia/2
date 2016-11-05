<?php
namespace Df\C1\Cml2\Import\Processor\Product\Type\Configurable;
use Df\C1\Cml2\Import\Data\Entity\ProductPart\AttributeValue\Custom as AttributeValueCustom;
use Df\C1\Cml2\Import\Processor\Product\Type\Configurable;
class Update extends Configurable {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		df_assert($this->getEntityOffer()->isTypeConfigurableParent());
		/** @var \Df_Catalog_Model_Product $product */
		$product = $this->getExistingMagentoProduct();
		$product->addData(array_merge(
			$this->getProductDataNewOrUpdateAttributeValueIdsCustom()
			,$this->getProductDataNewOrUpdateBase()
			,$this->getProductDataUpdateOnly()
		));
		if ($this->getDocumentCurrentAsOffers()->hasPrices()) {
			$product->addData(array(
				'can_save_configurable_attributes' => true
				,'can_save_custom_options' => true
				/**
				 * Обратите внимание,
				 * что система учитывает ключ configurable_products_data,
				 * только если указан ключ configurable_attributes_data,
				 * поэтому мы обязаны указать ключ configurable_attributes_data,
				 * даже если там у нас данные не менялись.
				 */
				,'configurable_attributes_data' =>
					$this->getTypeInstance()->getConfigurableAttributesAsArray()
				,'configurable_products_data' => $this->getConfigurableProductsData()
			));
		}
		/**
		 * Код выше уже установил товару значение свойства category_ids,
		 * но в данном контексте — неправильно, в виде строки.
		 * Устанавливаем по-правильному.
		 */
		$product->setCategoryIds($this->getEntityProduct()->getCategoryIds());
		$product->saveRm($isMassUpdate = true);
		$product->reload();
		df_c1_reindex_product($product);
		df()->registry()->products()->addEntity($product);
		df_c1_log('Обновлён товар %s.', $product->getTitle());
	}

	/**
	 * @override
	 * @return \Df_Catalog_Model_Product
	 */
	protected function getProductMagento() {return $this->getExistingMagentoProduct();}

	/** @return array(string => string) */
	private function getProductDataNewOrUpdateAttributeValueIdsCustom() {return array_filter(df_map(
		function(AttributeValueCustom $v) {return [
			$v->getAttributeName(), !$v->getAttributeMagento() ? null : $v->getValueForObject()
		];}, $this->getEntityProduct()->getAttributeValuesCustom(), [], [], 0, true
	));}

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
	public static function p_update(Configurable $masterProcessor) {
		self::ic(__CLASS__, $masterProcessor->getEntityOffer())->process()
	;}
}