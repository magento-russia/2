<?php
class Df_1C_Cml2_Import_Processor_Product_Type_Configurable_Update
	extends Df_1C_Cml2_Import_Processor_Product_Type_Configurable {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		df_assert($this->getEntityOffer()->isTypeConfigurableParent());
		/** @var Df_Catalog_Model_Product $product */
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
		rm_1c_reindex_product($product);
		df()->registry()->products()->addEntity($product);
		rm_1c_log('Обновлён товар %s.', $product->getTitle());
	}

	/**
	 * @override
	 * @return Df_Catalog_Model_Product
	 */
	protected function getProductMagento() {return $this->getExistingMagentoProduct();}

	/** @return array(string => string) */
	private function getProductDataNewOrUpdateAttributeValueIdsCustom() {
		/** @var array(string => string) $result */
		$result = array();
		foreach ($this->getEntityProduct()->getAttributeValuesCustom() as $attributeValue) {
			/** @var Df_1C_Cml2_Import_Data_Entity_ProductPart_AttributeValue_Custom $attributeValue */
			if ($attributeValue->getAttributeMagento()) {
				$result[$attributeValue->getAttributeName()] = $attributeValue->getValueForObject();
			}
		}
		return $result;
	}

	/**
	 *
	 * @param Df_1C_Cml2_Import_Processor_Product_Type_Configurable $masterProcessor
	 * @return void
	 */
	public static function p_update(
		Df_1C_Cml2_Import_Processor_Product_Type_Configurable $masterProcessor
	) {
		/**
		 * Обратите внимание, что мы тут можем вызывать
		 * @uses Df_1C_Cml2_Import_Processor_Product::getEntityOffer(),
		 * несмотря на то, что этот метод имеет область видимости «protected».
		 * http://php.net/manual/language.oop5.visibility.php
		 * «Visibility from other objects.
		 * Objects of the same type will have access to each others private and protected members
		 * even though they are not the same instances.»
		 */
		self::ic(__CLASS__, $masterProcessor->getEntityOffer())->process();
	}
}