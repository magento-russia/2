<?php
class Df_1C_Model_Cml2_Import_Processor_Product_Type_Configurable_New
	extends Df_1C_Model_Cml2_Import_Processor_Product_Type_Configurable {
	/**
	 * @override
	 * @return void
	 */
	public function process() {
		df_assert($this->getEntityOffer()->isTypeConfigurableParent());
		/** @var Df_Catalog_Model_Product $product */
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
		df_h()->_1c()->cml2()->reindexProduct($product);
		df()->registry()->products()->addEntity($product);
		rm_1c_log('Создан товар «%s».', $product->getName());
	}

	/**
	 * @override
	 * @return Df_Catalog_Model_Product
	 */
	protected function getProductMagento() {
		if (!isset($this->{__METHOD__})) {
			$this->getImporter()->import();
			$this->{__METHOD__} = $this->getImporter()->getProduct();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return Mage_Catalog_Model_Product_Type_Configurable
	 */
	protected function getTypeInstance() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Catalog_Model_Product_Type_Configurable $result */
			$result = parent::getTypeInstance();
			$result->setUsedProductAttributeIds($this->getUsedProductAttributeIds());
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return array(array(string => string|int)) */
	private function getConfigurableAttributesData() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(array(string => string|int)) $result */
			$result = array();
			foreach ($this->getTypeInstance()->getConfigurableAttributesAsArray() as $attribute) {
				/** @var array(string => string|int) $attribute */
				$result[]= array_merge($attribute, array(
					'use_default' => 1
					,'position' => 0
					,'label' => df_a($attribute, 'frontend_label', $attribute['attribute_code'])
				));
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => int) */
	private function getUsedProductAttributeIds() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(string => int) $result */
			$result = array();
			/** @var string[] $labels */
			$labels = array();
			/** @var Df_1C_Model_Cml2_Import_Data_Entity_Offer|null $firstChild */
			$firstChild = rm_first($this->getEntityOffer()->getConfigurableChildren());
			if ($firstChild) {
				foreach ($firstChild->getOptionValues() as $optionValue) {
					/** @var Df_1C_Model_Cml2_Import_Data_Entity_OfferPart_OptionValue $optionValue */
					/** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
					$attribute = $optionValue->getAttributeMagento();
					$result[$attribute->getName()] = $attribute->getId();
					$labels[]= $attribute->getFrontendLabel();
				}
			}
			rm_1c_log(
				"Для товара настраиваются параметры:\r\n%s.", df_quote_and_concat($labels)
			);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @buyer {buyer}
	 * @static
	 * @param Df_1C_Model_Cml2_Import_Processor_Product_Type_Configurable $masterProcessor
	 * @return Df_1C_Model_Cml2_Import_Processor_Product_Type_Configurable_New
	 */
	public static function i2(
		Df_1C_Model_Cml2_Import_Processor_Product_Type_Configurable $masterProcessor
	) {
		/**
		 * Обратите внимание, что мы тут можем вызывать
		 * @see Df_1C_Model_Cml2_Import_Processor_Product::getEntityOffer(),
		 * несмотря на то, что этот метод имеет область видимости «protected».
		 * @link http://php.net/manual/en/language.oop5.visibility.php
		 * «Visibility from other objects.
		 * Objects of the same type will have access to each others private and protected members
		 * even though they are not the same instances.»
		 */
		return new self(array(self::P__ENTITY => $masterProcessor->getEntityOffer()));
	}
}