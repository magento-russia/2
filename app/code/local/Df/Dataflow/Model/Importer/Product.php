<?php
/**
 * @method Df_Dataflow_Model_Import_Product_Row getRow()
 */
class Df_Dataflow_Model_Importer_Product extends Df_Dataflow_Model_Importer_Row {
	/** @return Df_Catalog_Model_Product */
	public function getProduct() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Catalog_Model_Product $result */
			$result = df_product();
			rm_eav_reset($reindexFlat = false);
			// Перед загрузкой товара из базы данных устанавливаем для него магазин.
			$result->setStoreId($this->getRow()->getStore()->getId());
			if (!$this->getRow()->isProductNew()) {
				$result->load($this->getRow()->getId());
				if (rm_nat0($result->getId()) !== rm_nat0($this->getRow()->getId())) {
					df_error(
						'При обновлении данных товара произошёл сбой '
						.'загрузки из базы данных товар с артикулом «%s»'
						.' и идентификатором «%s».'
						."\nСбой произошёл при попытке установить товару следующие новые данные:\n%s"
						,$this->getRow()->getSku()
						,$this->getRow()->getId()
						,rm_print_params($this->getRow()->getAsArray())
					);
				}
			}
			else {
				// Новый товар
				if (!$this->getRow()->getProductType()) {
					df_error(
						'Для нового товара Вы должны обязательно указать его тип в поле «%s».'
						,Df_Dataflow_Model_Import_Product_Row::FIELD__PRODUCT_TYPE
					);
				}
				$result->setDataUsingMethod(
					Df_Catalog_Model_Product::P__TYPE_ID
					// Обратите внимание, что идентификатором типа товаров является строка, а не число.
					// Пример идентификатарора: «simple», «bundle».
					,$this->getRow()->getProductType()
				);
				if (!$this->getRow()->getAttributeSetId()) {
					df_error(
						'Для нового товара Вы должны обязательно указать'
						 . ' название его набора характеристик в поле «%s».'
						,Df_Dataflow_Model_Import_Product_Row::FIELD__ATTRIBUTE_SET
					);
				}
				$result->setDataUsingMethod(
					Df_Catalog_Model_Product::P__ATTRIBUTE_SET_ID
					,$this->getRow()->getAttributeSetId()
				);
				$result->setData('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return Df_Dataflow_Model_Importer_Row
	 */
	public function import() {
		if ($this->getRow()->isProductNew()) {
			// Убеждаемся в присутствии значений обязательных для нового товара полей
			$this->assertRequiredAttributesExistence();
		}
		$this->importCategoriesUsingStandardTechnology();
		$this->importCategoriesUsingAdvancedTechnology();
		$this->initWebsiteAttributeForProduct();
		$this->importAttributeValues();
		if (!$this->getProduct()->getDataUsingMethod('visibility')) {
			$this->getProduct()
				->getDataUsingMethod(
					Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE
				)
			;
		}
		$this->importInventoryData();
		$this->importTierPrices();
		$this->getProduct()->saveRm($isMassUpdate = true);
		/**
		 * Решение проблемы @link http://magento-forum.ru/topic/3963/
		 * Когда обмен выполняется с многовитринным магазином,
		 * то в магазине atletica.baaton.com почему-то не устанавливатся
		 * значения по-умолчанию (store = 0),
		 * и по этой причине товары не показываются на витрине.
		 * В чём дело-не понял, но оказывается значения можно устанавливать и вручную.
		 */
		if ($this->getRow()->getStore()->getId() !== Mage_Core_Model_App::ADMIN_STORE_ID) {
			/** @var Df_Catalog_Model_Product $productInCurrentScope */
			$productInCurrentScope = $this->getProduct()->forStore($this->getRow()->getStore()->getId());
			/** @var Df_Catalog_Model_Product $productInAdminScope */
			$productInAdminScope = $this->getProduct()->forStore(Mage_Core_Model_App::ADMIN_STORE_ID);
			/**
			 * @todo Возможно, достаточно копировать не все свойста,
			 * а только 4: status, visibility, name, tax_class_id
			 * (сделал такой вывод из анализа запроса SQL для административной таблицы товаров).
			 * А может и нет.
			 */
			/** @var string[] $keysToSkip */
			$keysToSkip =
				array_merge(
					array('store', 'store_id')
					/**
					 * Если не пропустить поля для картинок,
					 * то картинки будут дублироваться
					 */
					,Df_Catalog_Model_Product::getMediaAttributeNames()
				)
			;
			foreach ($productInCurrentScope->getData() as $key => $value) {
				/** @var string $key */
				/** @var string $value */
				if (!$productInAdminScope->hasData($key)) {
					if (!in_array($key, $keysToSkip)) {
						$productInAdminScope->setData($key, $value);
					}
				}
			}
			$productInAdminScope->saveRm($isMassUpdate = true);
		}
		$this->importImages();
		/**
		 * Модули «1С: Управление торговлей» и «МойСклад»
		 * импортируют опции товара самостоятельно.
		 * Избежание вызова @see Df_Dataflow_Model_Importer_Product::importCustomOptions()
		 * ускоряет работу этих модулей.
		 */
		if (!$this->needSkipCustomOptions()) {
			$this->importCustomOptions();
		}
		$this->importBundleData();
		// применяем ценовые правила
		$this->getProduct()->unsetData('is_massupdate');
		/** @var Mage_CatalogRule_Model_Rule $catalogRule */
		$catalogRule = df_model('catalogrule/rule');
		$catalogRule->applyAllRulesToProduct($this->getProduct()->getId());
		return $this;
	}

	/** @return Df_Dataflow_Model_Importer_Product */
	private function importBundleData() {
		if ('bundle' === $this->getProduct()->getTypeId()) {
			$this->reloadProduct();
			$this->getBundleImporter()->process();
			$this->getProduct()->saveRm($isMassUpdate = true);
		}
		return $this;
	}

	/** @return Df_Dataflow_Model_Importer_Product_Bundle */
	private function getBundleImporter() {
		if (!isset($this->_bundleImporter)) {
			$this->_bundleImporter = Df_Dataflow_Model_Importer_Product_Bundle::i(
				$this->getProduct(), $this->getRow()->getAsArray()
			);
		}
		return $this->_bundleImporter;
	}
	/** @var Df_Dataflow_Model_Importer_Product_Bundle */
	private $_bundleImporter;	

	/** @return Df_Dataflow_Model_Importer_Product */
	private function importCustomOptions() {
		if (
				df_enabled(Df_Core_Feature::DATAFLOW_CO, $this->getRow()->getStore()->getId())
			&& 
				df_cfg()->dataflow()->products()->getCustomOptionsSupport()
		) {
			$this->reloadProduct();
			$this->getCustomOptionsImporter()->process();
			$this->getProduct()->saveRm($isMassUpdate = true);
		}
		return $this;
	}

	/** @return Df_Dataflow_Model_Importer_Product_Options */
	private function getCustomOptionsImporter() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Dataflow_Model_Importer_Product_Options::i(
				$this->getProduct(), $this->getRow()->getAsArray()
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @throws Exception
	 * @return Df_Dataflow_Model_Importer_Product
	 */
	private function importImages() {
		/** @var array $primaryImages */
		$primaryImages = $this->getGalleryImporter()->getPrimaryImages();
		if (0 < count($primaryImages)) {
			$this->reloadProduct();
			/** @var bool $importAdditionalImagesIsAllowed */
			$importAdditionalImagesIsAllowed =
				df_enabled(Df_Core_Feature::DATAFLOW_IMAGES, $this->getRow()->getStore()->getId())
			;
			if (
					$importAdditionalImagesIsAllowed
				&&
					df_cfg()->dataflow()->products()->getDeletePreviousImages()
			) {
				//remove previous images
				$this->getProduct()->deleteImages();
			}
			foreach ($primaryImages as $file => $fields) {
				/** @var string $file */
				/** @var array $fields */
				df_assert_string($file);
				df_assert_array($fields);
				$imagePath = Mage::getBaseDir('media') . DS . 'import' . trim ($file);
				if (!is_file($imagePath)) {
					throw new Exception(rm_sprintf("Image file %s does not exist", $imagePath));
				}
				try {
					$this->getProduct()->addImageToMediaGallery(
						$imagePath
						,array('thumbnail','small_image','image')
						,$move = false
						,$exclude = false
					);
				}
				catch(Exception $e) {
					df_handle_entry_point_exception($e, false);
				}
			}
			if (
					$importAdditionalImagesIsAllowed
				&&
					df_cfg()->dataflow()->products()->getGallerySupport()
			) {
				$this->getGalleryImporter()->addAdditionalImagesToProduct();
			}
			$this->getProduct()->saveRm($isMassUpdate = true);
		}
		return $this;
	}

	/** @return Df_Dataflow_Model_Importer_Product_Gallery */
	private function getGalleryImporter() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = Df_Dataflow_Model_Importer_Product_Gallery::i(
				$this->getProduct(), $this->getRow()->getAsArray()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Dataflow_Model_Importer_Product */
	private function importInventoryData() {
		/** @var array $stockData */
		$stockData = array();
		foreach ($this->getInventoryFields() as $inventoryField) {
			/** @var string $inventoryField */
			df_assert_string($inventoryField);
			/** @var string|null $inventoryFieldValue */
			$inventoryFieldValue = $this->getRow()->getFieldValue($inventoryField);
			if (!is_null($inventoryFieldValue)) {
				if (in_array($inventoryField, $this->helper()->getNumericFields())) {
					$inventoryFieldValue = $this->getRow()->parseAsNumber($inventoryFieldValue);
				}
				$stockData[$inventoryField] = $inventoryFieldValue;
			}
		}
		$this->getProduct()->setDataUsingMethod('stock_data', $stockData);
		return $this;
	}

	/** @return Df_Dataflow_Model_Importer_Product */
	private function importTierPrices() {
		/** @var string $pattern */
		$pattern = '#^rm_tier_price_(\d+)_(\d+)_(\d+)$#';
		/** @var string[] $matches */
		$matches = array();
		/** @var array(array(string => int)) $newTierPrices */
		$newTierPrices = array();
		foreach ($this->getRow()->getAsArray() as $fieldName => $fieldValue) {
			/** @var string $fieldName */
			/** @var mixed $fieldValue */
			if (1 === preg_match($pattern, $fieldName, $matches)) {
				/** @var int $websiteId */
				//$websiteId = rm_nat0(df_a($matches, 1));
				/** @var int $customerGroupId */
				$customerGroupId = rm_nat0(df_a($matches, 2));
				/** @var int $qty */
				$qty = rm_nat0(df_a($matches, 3));
				$newTierPrices []=
					array(
						'website_id'  => 0
						,'cust_group' => $customerGroupId
						,'price_qty' => $qty
						,'price'  => $fieldValue
					)
				;
			}
		}
		/** @var array(array(string => int))|null $existingTierPrices */
		$existingTierPrices = $this->getProduct()->getDataUsingMethod('tier_price');
		if ($existingTierPrices) {
			foreach ($existingTierPrices as $existingTierPrice) {
				/** @var array(string => int) $existingTierPrice */
				/** @var int|null $matchedNewTierPrice */
				$matchedNewTierPriceIndex = null;
				foreach ($newTierPrices as $newTierPriceIndex => $newTierPrice) {
					/** @var int $newTierPriceIndex */
					/** @var array(string => int) $newTierPrice */
					if (
							df_a($existingTierPrice, 'website_id') == df_a($newTierPrices, 'website_id')
						&&
							df_a($existingTierPrice, 'cust_group') == df_a($newTierPrices, 'cust_group')
						&&
							df_a($existingTierPrice, 'price_qty') == df_a($newTierPrices, 'price_qty')
					) {
						$matchedNewTierPriceIndex = $newTierPriceIndex;
						break;
					}
				}
				if (is_null($matchedNewTierPriceIndex)) {
					$newTierPrices []= $existingTierPrice;
				}
			}
		}
		if ($newTierPrices) {
			$this->getProduct()->setDataUsingMethod('tier_price', $newTierPrices);
		}
		return $this;
	}

	/** @return array */
	private function getInventoryFields() {
		return $this->helper()->getInventoryFieldsByProductType($this->getProduct()->getTypeId());
	}

	/** @return Df_Dataflow_Model_Importer_Product */
	private function reloadProduct() {
		$this->getProduct()->reload();
		return $this;
	}

	/** @return Df_Dataflow_Model_Importer_Product */
	private function initWebsiteAttributeForProduct() {
		$this
			->initWebsiteAttributeFromStore()
			->initWebsiteAttributeFromWebsiteIdsField()
		;
		return $this;
	}

	/** @return Df_Dataflow_Model_Importer_Product */
	private function importCategoriesUsingStandardTechnology() {
		/** @var string|null $categoryIdsAsString */
		$categoryIdsAsString = $this->getRow()->getCategoryIdsAsString();
		if (!is_null($categoryIdsAsString)) {
			$this->getProduct()->setCategoryIds($categoryIdsAsString);
		}
		return $this;
	}

	/** @return Df_Dataflow_Model_Importer_Product */
	private function importCategoriesUsingAdvancedTechnology() {
		if (
				df_enabled(Df_Core_Feature::DATAFLOW_CATEGORIES, $this->getRow()->getStore()->getId())
			&&
				df_cfg()->dataflow()->products()->getEnhancedCategorySupport()
		) {
			Df_Dataflow_Model_Importer_Product_Categories::i(
				$this->getProduct(), $this->getRow()->getAsArray(), $this->getRow()->getStore()
			)->process();
		}
		return $this;
	}

	/** @return Df_Dataflow_Model_Importer_Product */
	private function initWebsiteAttributeFromStore() {
		/** @var array $websiteIds */
		$websiteIds = $this->getProduct()->getWebsiteIds();
		df_assert_array($websiteIds);
		if (!in_array($this->getRow()->getStore()->getWebsiteId(), $websiteIds)) {
			/**
			 * Это условие заимствовано из стандартного кода.
			 * Ценность не вполне осознана мной.
			 */
			if (0 !== $this->getRow()->getStore()->getId()) {
				$websiteIds[]= $this->getRow()->getStore()->getWebsiteId();
				$this->getProduct()->setDataUsingMethod('website_ids', $websiteIds);
			}
		}
		return $this;
	}

	/** @return Df_Dataflow_Model_Importer_Product */
	private function initWebsiteAttributeFromWebsiteIdsField() {
		if (0 < count($this->getRow()->getWebsites())) {
			/** @var array $websiteIds */
			$websiteIds = $this->getProduct()->getWebsiteIds();
			if (
					is_null($websiteIds)
				||
					/**
					 * Странное условие.
					 * Присутствует в оригинальном коде ядра.
					 */
					(0 === $this->getRow()->getStore()->getId())
			) {
				$websiteIds = array();
			}
			df_assert_array($websiteIds);
			foreach ($this->getRow()->getWebsites() as $website) {
				/** @var Mage_Core_Model_Website $website */
				if (!in_array($website->getId(), $websiteIds)) {
					$websiteIds[]= $website->getId();
				}
			}
			$this->getProduct()->setDataUsingMethod('website_ids', $websiteIds);
		}
		return $this;
	}

	/**
	 * @throws Exception
	 * @return Df_Dataflow_Model_Importer_Product
	 */
	private function importAttributeValues() {
		/********************************************************************
		 * Заплатка для локали.
		 * Стандартный программный код приводит к проблеме:
		 * если в импортируемом файле значения опций записаны в фомате одной локали,
		 * а при импорте установлена другая локаль, то значения не будут распознаны.
		 *
		 * Данная заплатка позволяет администратору устанавливать локаль для опций
		 * в профиле Magento Dataflow.
		 *
		 * Пример:
		 *
			<action type="vichy_dataflow/import_products_parser" method="parse">
				<var name="adapter">catalog/convert_adapter_product</var>
				<var name="method">parse</var>
				<var name="locale">en_US</var>
			</action>
		 */

		/** @var string $originalLocaleCode */
		$originalLocaleCode = df_mage()->core()->translateSingleton()->getLocale();
		/** @var Exception $exception */
		$exception = null;
		df_assert_string($originalLocaleCode);
		/** @var string|null $localeCodeFromBatchParams */
		$localeCodeFromBatchParams = $this->getConfigParam('locale');
		if (!is_null($localeCodeFromBatchParams)) {
			df_assert_string($localeCodeFromBatchParams);
			df_mage()->core()->translateSingleton()
				->setLocale($localeCodeFromBatchParams)
				->init('adminhtml', true)
			;
		}
		try {
			foreach ($this->getRow()->getAsArray() as $fieldName => $fieldValue) {
				/** @var string $fieldName */
				/** @var string|float|int|null $fieldValue */
				df_assert_string($fieldName);
				if (in_array($fieldName, $this->helper()->getInventoryFields())) {
					continue;
				}
				if (in_array($fieldName, $this->helper()->getIgnoredFields())) {
					continue;
				}
				if (is_null($fieldValue)) {
					continue;
				}
				/** @var Mage_Eav_Model_Entity_Attribute|null $fieldAttribute */
				$fieldAttribute = $this->getAttributeForField($fieldName);
				if (is_null($fieldAttribute)) {
					continue;
				}
				df_assert($fieldAttribute instanceof Mage_Eav_Model_Entity_Attribute);
				/**
				 * Позволяет менять артикул товара при импорте
				 * @link http://magento-forum.ru/topic/3653/
				 */
				if (Df_Catalog_Model_Product::P__SKU === $fieldName) {
					if ($this->getRow()->getSkuNew()) {
						$fieldValue = $this->getRow()->getSkuNew();
					}
				}
				/** @var bool $isArray */
				$isArray = false;
				/** @var string|array $valueToSet */
				$valueToSet = $fieldValue;
				if ('multiselect' === $fieldAttribute->getDataUsingMethod('frontend_input')) {
					$fieldValue =
						explode(
							Mage_Catalog_Model_Convert_Adapter_Product::MULTI_DELIMITER
							,$fieldValue
						)
					;
					$isArray = true;
					$valueToSet = array();
				}
				if ('decimal' === $fieldAttribute->getBackendType()) {
					$valueToSet = $this->getRow()->parseAsNumber($fieldValue);
				}
				if ($fieldAttribute->usesSource()) {
					/**
					 * Данный программный код приводит к проблеме:
					 * если в импортируемом файле значения опции записаны в фомате одной локали,
					 * а при импорте установлена другая локаль, то значения не будут распознаны.
					 */
					/** @var Mage_Eav_Model_Entity_Attribute_Source_Abstract $source */
					$source = $fieldAttribute->getSource();
					/** @var array $options */
					$options =
						/**
						 * Как ни странно, хотя базовый интерфейс Mage_Eav_Model_Entity_Attribute_Source_Interface
						 * определяет getAllOptions как метод без параметров,
						 * класс Mage_Core_Model_Design_Source_Design переопределяет этот метод как метод с параметром,
						 * причём значением по умолчанию является true.
						 *
						 * Не уверен, что вызов getAllOptions(false) — это лучшее решение, но так делает стандартный код.
						 */
						$source->getAllOptions(false)
					;
					df_assert_array($options);
					if ($isArray) {
						foreach ($options as $option) {
							/** @var array $option */
							df_assert_array($option);
							if (in_array(df_a($option, 'label'), $fieldValue)) {
								$valueToSet[]= df_a($option, 'value');
							}
						}
					}
					else {
						$valueToSet = false;
						foreach ($options as $option) {
							/** @var array $option */
							df_assert_array($option);
							if (df_a($option, 'label') === $fieldValue) {
								$valueToSet = df_a($option, 'value');
								break;
							}
						}
					}
				}
				$this->getProduct()->setData($fieldName, $valueToSet);
			}
		}
		/***************************
		 * Заключительная часть заплатки для локали
		 */
		catch(Exception $e) {
			$exception = $e;
		}
		if ($originalLocaleCode != df_mage()->core()->translateSingleton()->getLocale()) {
			df_mage()->core()->translateSingleton()
				->setLocale($originalLocaleCode)
				->init('adminhtml', true)
			;
		}
		if (!is_null($exception)) {
			throw $exception;
		}
		/***************************
		 * Конец заплатки для локали
		 */
		return $this;
	}

	/** @return Df_Dataflow_Model_Import_Product_Row */
	private function assertRequiredAttributesExistence() {
		foreach ($this->helper()->getRequiredFields() as $requiredField) {
			/** @var string $requiredField */
			df_assert_string($requiredField);
			/** @var Mage_Eav_Model_Entity_Attribute $attribute */
			$attribute = $this->getAttributeForField($requiredField);
			if (!is_null($attribute)) {
				df_assert($attribute instanceof Mage_Eav_Model_Entity_Attribute);
				if ($attribute->getDataUsingMethod('is_required')) {
					// убеждаемся в присутствии значений
					$this->getRow()->getFieldValue($requiredField, true);
				}
			}
		}
		return $this;
	}

	/**
	 * @param string $fieldName
	 * @return Mage_Eav_Model_Entity_Attribute|null
	 */
	private function getAttributeForField($fieldName) {
		/** @var Df_Catalog_Model_Resource_Eav_Attribute $result */
		$result = df_h()->dataflow()->registry()->attributes()->findByCode($fieldName);
		if (!$result) {
			$result = Df_Catalog_Model_Resource_Eav_Attribute::i();
			$result->loadByCode(rm_eav_id_product(), $fieldName);
			if (!$result->getId()) {
				$result = null;
			}
			else {
				df_h()->dataflow()->registry()->attributes()->addEntity($result);
				df_assert($result instanceof Mage_Eav_Model_Entity_Attribute);
				if ($result instanceof Mage_Catalog_Model_Resource_Eav_Attribute) {
					/** @var Mage_Catalog_Model_Resource_Eav_Attribute $result */
					/** @var array $applyTo */
					$applyTo = $result->getApplyTo();
					// apply_to позволяет ограничить область применения свойства
					if (!is_null($applyTo)) {
						df_assert_array($applyTo);
						/**
						 * Mage_Catalog_Model_Resource_Eav_Attribute::getApplyTo() возващает пустой массив,
						 * если свойство применим ко всем типам товара.
						 */
						if (0 < count($applyTo)) {
							if (!in_array($this->getProduct()->getTypeId(), $applyTo)) {
								/**
								 * Скопировал данную логику из метода
								 * Mage_Catalog_Model_Convert_Adapter_Product::getAttribute()
								 */
								$result = null;
							}
						}
					}
				}
			}
		}
		return $result;
	}

	/** @return Df_Catalog_Helper_Product_Dataflow */
	private function helper() {return Df_Catalog_Helper_Product_Dataflow::s();}

	/**
	 * Модули «1С: Управление торговлей» и «МойСклад»
	 * импортируют опции товара самостоятельно.
	 * Избежание вызова @see Df_Dataflow_Model_Importer_Product::importCustomOptions()
	 * ускоряет работу этих модулей.
	 * @return bool
	 */
	private function needSkipCustomOptions() {return $this->cfg(self::P__SKIP_CUSTOM_OPTIONS);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__SKIP_CUSTOM_OPTIONS, self::V_BOOL);
	}
	const _CLASS = __CLASS__;
	const P__SKIP_CUSTOM_OPTIONS = 'skip_custom_options';
	/**
	 * @static
	 * @param Df_Dataflow_Model_Import_Product_Row $row
	 * @return Df_Dataflow_Model_Importer_Product
	 */
	public static function i(Df_Dataflow_Model_Import_Product_Row $row) {
		return new self(array(self::P__ROW => $row));
	}
}