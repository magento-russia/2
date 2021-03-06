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
		// Используем наши улучшения экспорта только для товаров,
		// которые экспорта из лицензированных магазинов
		if (!df_enabled(Df_Core_Feature::DATAFLOW, $this->getStoreId())) {
			parent::unparse();
		}
		else {
			try {
				$this->unparseRm();
			}
			catch(Exception $e) {
				$this->addException($e);
				df_handle_entry_point_exception($e, false);
			}
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
		$product = df_a($params, 'product');
		/** @var array(string => mixed) $row */
		$row = df_a($params, 'row');
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
					$imagePath = df_a($image, 'file');
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
			$image = df_a($row, $field);
			if (!empty ($image)) {
				$result[]= $this->adjustImagePath($image);
			}
		}
		/**
		 * С @see rm_array_unique_fast() постоянно возникакает проблема
		 * array_flip(): Can only flip STRING and INTEGER values
		 * @link http://magento-forum.ru/topic/4695/
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
	 * Этот метод может быть приватным,
	 * несмотря на использование его как callable,
	 * потому что он используется как callable только внутри своего класса:
	 * @link http://php.net/manual/en/language.types.callable.php#113447
	 * Проверял, что это действительно допустимо, на различных версиях интерпретатора PHP:
	 * @link http://3v4l.org/OipEQ
	 *
	 * @param mixed $value
	 * @return mixed[]
	 */
	private function prepareForSerialization($value) {
		return
				is_array($value) && !empty($value)
			?
				df_array_combine(
					array_keys($value)
					,array_map(array($this, 'prepareForSerialization'), $value)
				)
			:
				(
							is_object($value)
						&&
							/**
							 * К сожалению, нельзя здесь для проверки публичности метода
							 * использовать is_callable,
							 * потому что наличие Varien_Object::__call
							 * приводит к тому, что is_callable всегда возвращает true.
							 */
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
			$row['df_custom_options'] =
				rm_sprintf(
					//'<![CDATA[%s]]>'
					'%s'
					,/**
					 * Метод Zend_Json::prettyPrint отсутствует в Magento 1.4.0.1
					 * Однако, мы можем поддержать его в более поздних версиях
					 * через свой нестандартный фильтр
					 */
					df_json_pretty_print(
						df_text()->adjustCyrillicInJson(
							/**
							 * Zend_Json::encode использует json_encode при наличии расширения PHP JSON
							 * и свой внутренний кодировщик при отсутствии расширения PHP JSON.
							 * @see Zend_Json::encode
							 * @link http://stackoverflow.com/questions/4402426/json-encode-json-decode-vs-zend-jsonencode-zend-jsondecode
							 * Обратите внимание,
							 * что расширение PHP JSON не входит в системные требования Magento.
							 * @link http://www.magentocommerce.com/system-requirements
							 * Поэтому использование Zend_Json::encode выглядит более правильным, чем json_encode.
							 */
							Zend_Json::encode(
								$optionsForSerialization
							)
						)
					)
				)
			;
		}
		return $this;
	}

	/** @return Df_Catalog_Model_Convert_Parser_Product */
	private function unparseRm() {
		$entityIds = $this->getData();
		foreach ($entityIds as $i => $entityId) {
			$product = $this->getProductModel()
				->setStoreId($this->getStoreId())
				->load($entityId);
			$this->setProductTypeInstance($product);
			$this->_currentProduct = $product;
			/* @var $product Mage_Catalog_Model_Product */

			$position = df_mage()->catalogHelper()->__('Line %d, SKU: %s', ($i+1), $product->getSku());
			$this->setPosition($position);
			$row =
				array(
					'store' => $this->getStore()->getCode()
					,'websites' => ''
					,'attribute_set' =>
						$this->getAttributeSetName(
							$product->getEntityTypeId()
							,$product->getAttributeSetId()
						)
					,'type' => $product->getTypeId()
					,'category_ids' => implode(',', $product->getCategoryIds())
			);
			if (Mage_Core_Model_Store::ADMIN_CODE === $this->getStore()->getCode()) {
				$websiteCodes = array();
				foreach ($product->getWebsiteIds() as $websiteId) {
					$websiteCode = Mage::app()->getWebsite($websiteId)->getCode();
					$websiteCodes[$websiteCode] = $websiteCode;
				}
				$row['websites'] = implode(',', $websiteCodes);
			}
			else {
				$row['websites'] = $this->getStore()->getWebsite()->getCode();
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

			if ($stockItem = $product->getStockItem()) {
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
			if (
					df_enabled(Df_Core_Feature::DATAFLOW_IMAGES, $this->getStoreId())
				&&
					df_cfg()->dataflow()->products()->getGallerySupport()
			) {
				$row['df_additional_images'] =
					$this->getAdditionalImagesAsString(
						array(
							'product' => $product
							,'row' => $row
						)
					)
				;
			}
			// END PATCH: Export of additional images

			// BEGIN PATCH: Export of Custom Options
			if (df_enabled(Df_Core_Feature::DATAFLOW_CO, $this->getStoreId()) && df_cfg()->dataflow()->products()->getCustomOptionsSupport()) {
				$this->unparseCustomOptions($product, $row);
			}
			// END PATCH: Export of Custom Options

			// BEGIN PATCH: Export of product categories
			if (
					df_enabled(Df_Core_Feature::DATAFLOW_CATEGORIES, $this->getStoreId())
				&&
					df_cfg()->dataflow()->products()->getEnhancedCategorySupport()
			) {
				$row['df_categories'] =
					Df_Dataflow_Model_Exporter_Product_Categories::i(
						array(
							'product' => $product
						)
					)->process()
				;
			}
			// END PATCH: Export of product categories
			$this->getBatchExportModel()
				->setId(null)
				->setBatchId($this->getBatchModel()->getId())
				->setBatchData($row)
				->setStatus(1)
				->save();
			$product->reset();
		}
		return $this;
	}

	/** @var Df_Catalog_Model_Product  */
	private $_currentProduct;
}