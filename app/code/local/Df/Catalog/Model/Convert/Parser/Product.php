<?php
class Df_Catalog_Model_Convert_Parser_Product extends Mage_Catalog_Model_Convert_Parser_Product {
	/**
	 * Цель перекрытия —
	 * предоставление возможности при экспорте через Dataflow
	 * экспортировать вместе с товаром
	 * товарные разделы, картинки, настраиваемые покупателем опции.
	 * @override
	 * @return Df_Catalog_Model_Convert_Parser_Product
	 */
	public function unparse() {
		try {
			$this->unparseRm();
		}
		catch (Exception $e) {
			$this->addException($e);
			df_handle_entry_point_exception($e, false);
		}
		return $this;
	}

	/**
	 * @param string $imagePath
	 * @return string
	 */
	private function adjustImagePath($imagePath) {
		df_param_string($imagePath, 0);
		/** @var string $result */
		$result = '';
		if ($imagePath) {
			/** @var string $result */
			$result = ('/' === $imagePath[0]) ? $imagePath : '/' . $imagePath;
		}
		return $result;
	}

	/**
	 * @param array $params
	 * @return string
	 */
	private function getAdditionalImagesAsString(array $params) {
		/** @var string $result */
		$result = '';
		/** @var Df_Catalog_Model_Product $product */
		$product = dfa($params, 'product');
		/** @var array(string => mixed) $row */
		$row = dfa($params, 'row');
		/** @var array(string => mixed) $mediaGallery */
		$mediaGallery = $product->getMediaGallery();
		/** @var array(array(string => mixed)) $images */
		$images = $mediaGallery['images'];
		/** @var string[] $imagesAsArray */
		$imagesAsArray = array();
		if (is_array($images)) {
			foreach ($images as $image) {
				/** @var array(string => mixed) $image */
				if (!$image['disabled']) {
					/** @var string $imagePath */
					$imagePath = dfa($image, 'file');
					df_assert_string_not_empty($imagePath);
					$imagePath = $this->adjustImagePath($imagePath);
					if (!(in_array($imagePath, $this->getPrimaryImages($row)))) {
						$imagesAsArray[]= $imagePath;
					}
				}
			}
			$result = implode(';', $imagesAsArray);
		}
		return $result;
	}

	/**
	 * @param array $row
	 * @return array
	 */
	private function getPrimaryImages(array $row) {
		$result = array();
		foreach ($this->getImageFields() as $field) {
			$image = dfa($row, $field);
			if (!empty ($image)) {
				$result[]= $this->adjustImagePath($image);
			}
		}
		/**
		 * С @see dfa_unique_fast() постоянно возникакает проблема:
		 * «array_flip(): Can only flip STRING and INTEGER values»
		 * http://magento-forum.ru/topic/4695/
		 * Лучше верну-ка старую добрую функцию @see array_unique()
		 */
		$result = array_unique($result);
		return $result;
	}

	/** @return array */
	private function getImageFields() {
		/** @var array $result */
		$result =
			/**
			 * Метод Mage_Catalog_Model_Product::getMediaAttributes()
			 * присутствует в том числе и в Magento 1.4.0.1,
			 * так что, вероятно, условное ветвление можно убрать.
			 */
			df_magento_version('1.5', '<')
			? $this->_imageFields
			: array_keys($this->_currentProduct->getMediaAttributes())
		;
		df_result_array($result);
		return $result;
	}

	/**
	 * Этот метод вызывает сам себя через @uses array_map()
	 * @param mixed $value
	 * @return mixed[]
	 */
	private function prepareForSerialization($value) {
		return
				is_array($value) && !empty($value)
			?
				array_combine(
					array_keys($value)
					/** @uses prepareForSerialization() */
					,array_map(array($this, __FUNCTION__), $value)
				)
			:
				(
						is_object($value)
					&&
						/**
						 * К сожалению, нельзя здесь для проверки публичности метода использовать @see is_callable(),
						 * потому что наличие @see Varien_Object::__call()
						 * приводит к тому, что @see is_callable всегда возвращает true.
						 * Обратите внимание, что @uses method_exists(), в отличие от @see is_callable(),
						 * не гарантирует публичную доступность метода:
						 * т.е. метод может у класса быть, но вызывать его всё равно извне класса нельзя,
						 * потому что он имеет доступность private или protected.
						 * Пока эта проблема никак не решена.
						 */
						/** @uses Varien_Object::toArray() */
						method_exists($value, 'toArray')
					? call_user_func(array($value, 'toArray'))
					: $value
				)
		;
	}

	/**
	 * @param Mage_Catalog_Model_Product $product
	 * @param array $row
	 * @return Df_Catalog_Model_Convert_Parser_Product
	 */
	private function unparseCustomOptions(Mage_Catalog_Model_Product $product, array &$row) {
		if ($product->getOptions()) {
			$optionsForSerialization = array();
			foreach ($product->getOptions() as $optionKey => $option) {
				/** @var Mage_Catalog_Model_Product_Option $option */
				$optionsForSerialization[$optionKey]=
					$option
						->setData('_values', $this->prepareForSerialization($option->getValues()))
						->toArray()
				;
				$optionsForSerialization[$optionKey]=
					$option
						->setData('_options', $this->prepareForSerialization($option->getOptions()))
						->toArray()
				;
			}
			$row['df_custom_options'] = df_json_prettify(Zend_Json::encode($optionsForSerialization));
		}
		return $this;
	}

	/** @return Df_Catalog_Model_Convert_Parser_Product */
	private function unparseRm() {
		$entityIds = $this->getData();
		foreach ($entityIds as $i => $entityId) {
			/** @var Df_Catalog_Model_Product $product */
			$product = $this->getProductModel();
			$product->setStoreId($this->getStoreId());
			$product->load($entityId);
			$this->setProductTypeInstance($product);
			$this->_currentProduct = $product;
			/* @var Df_Catalog_Model_Product $product */
			$position = df_mage()->catalogHelper()->__('Line %d, SKU: %s', ($i+1), $product->getSku());
			$this->setPosition($position);
			$row = array(
				'store' => $this->getStore()->getCode()
				,'websites' => ''
				,'attribute_set' => $this->getAttributeSetName(
					$product->getEntityTypeId(), $product->getAttributeSetId()
				)
				,'type' => $product->getTypeId()
				,'category_ids' => df_csv($product->getCategoryIds())
			);
			if (Mage_Core_Model_Store::ADMIN_CODE === $this->getStore()->getCode()) {
				$websiteCodes = array();
				foreach ($product->getWebsiteIds() as $websiteId) {
					$websiteCode = df_website($websiteId)->getCode();
					$websiteCodes[$websiteCode] = $websiteCode;
				}
				$row['websites'] = df_csv($websiteCodes);
			}
			else {
				$row['websites'] = df_website()->getCode();
				if ($this->getVar('url_field')) {
					$row['url'] = $product->getProductUrl(false);
				}
			}
			foreach ($product->getData() as $field => $value) {
				if (in_array($field, $this->_systemFields) || is_object($value)) {
					continue;
				}
				$attribute = $this->getAttribute($field);
				if (!$attribute) {
					continue;
				}

				if ($attribute->usesSource()) {
					$option = $attribute->getSource()->getOptionText($value);
					if ($value && empty($option)) {
						$message = df_mage()->catalogHelper()->__('Invalid option ID specified for %s (%s), skipping the record.', $field, $value);
						$this->addException($message, Mage_Dataflow_Model_Convert_Exception::ERROR);
						continue;
					}
					if (is_array($option)) {
						$value = implode(self::MULTI_DELIMITER, $option);
					} else {
						$value = $option;
					}
					unset($option);
				}
				else if (is_array($value)) {
					continue;
				}
				$row[$field] = $value;
			}
			$stockItem = $product->getStockItem();
			if ($stockItem) {
				foreach ($stockItem->getData() as $field => $value) {
					if (in_array($field, $this->_systemFields) || is_object($value)) {
						continue;
					}
					$row[$field] = $value;
				}
			}

			foreach ($this->getImageFields() as $field) {
				if (isset($row[$field])) {
					if ('no_selection' === $row[$field])  {
						$row[$field] = null;
					}
					else {
						$row[$field] = $this->adjustImagePath($row[$field]);
					}
				}
			}
			// BEGIN PATCH: Export of additional images
			if (df_cfg()->dataflow()->products()->getGallerySupport()) {
				$row['df_additional_images'] = $this->getAdditionalImagesAsString(array(
					'product' => $product, 'row' => $row)
				);
			}
			// END PATCH: Export of additional images

			// BEGIN PATCH: Export of Custom Options
			if (df_cfg()->dataflow()->products()->getCustomOptionsSupport()) {
				$this->unparseCustomOptions($product, $row);
			}
			// END PATCH: Export of Custom Options

			// BEGIN PATCH: Export of product categories
			if (df_cfg()->dataflow()->products()->getEnhancedCategorySupport()) {
				$row['df_categories'] =
					Df_Dataflow_Model_Exporter_Product_Categories::i(
						array(
							'product' => $product
						)
					)->process()
				;
			}
			// END PATCH: Export of product categories
			/** @noinspection PhpUndefinedMethodInspection */
			$this->getBatchExportModel()
				->setId(null)
				->setBatchId($this->getBatchModel()->getId())
				->setBatchData($row)
				->setStatus(1)
				->save()
			;
			$product->reset();
		}
		return $this;
	}

	/** @var Df_Catalog_Model_Product  */
	private $_currentProduct;
}