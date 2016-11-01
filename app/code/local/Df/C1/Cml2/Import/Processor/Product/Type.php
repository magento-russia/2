<?php
abstract class Df_C1_Cml2_Import_Processor_Product_Type
	extends Df_C1_Cml2_Import_Processor_Product {
	/**
	 * Обратите внимание, что 1С может вполне не передавать цену.
	 * Это возможно в следующих ситуациях:
	 * 1) Когда цена на товар отсутствует в 1С
	 * 2) Когда передача цен отключена в настройках узла обмена
	 * (а это возможно, как минимум, в новых версиях модуля 1С-Битрикс (ветка 4)).
	 * 3) В новых версиях  модуля 1С-Битрикс (ветка 4, CommerceML 2.0.8)
	 * 1С передаёт цены не в файле offers.xml (как было в прежних версиях),
	 * а отдельным файлом prices_*.xml, который передаётся после файла offers_*.xml,
	 * в то время как файл offers_*.xml цен не содержит.
	 * @return float|null
	 */
	abstract protected function getPrice();
	/** @return string */
	abstract protected function getSku();
	/** @return string */
	abstract protected function getType();
	/** @return int */
	abstract protected function getVisibility();

	/** @return Df_Dataflow_Model_Importer_Product */
	protected function getImporter() {
		if (!isset($this->{__METHOD__})) {
			if (!$this->getExistingMagentoProduct()) {
				/** @var Df_C1_Cml2_Import_Data_Document_Offers $document */
				$document = $this->getState()->import()->getDocumentCurrentAsOffers();
				if (!$document->isBase() && ($document->hasPrices() || $document->hasStock())) {
					/** http://magento-forum.ru/topic/4898/ */
					df_error(
						'Узел 1С обмена данными с интернет-магазином настроен неправильно:'
						. ' 1С не передаёт в интернет-магазин предложения.'
						. "\nВключите в узле обмена передачу в интернет-магазин предложений."
						. "\nКак правило, для этого требуется на экране настроек узла обмена"
						. " на вкладке «Выгрузка товаров» поставить галку в графе «Предложения»."
					);
				}
			}
			$this->{__METHOD__} = new Df_Dataflow_Model_Importer_Product(array(
				/**
				 * Модули «1С:Управление торговлей» и «МойСклад»
				 * импортируют опции товара самостоятельно.
				 * Избежание вызова @see Df_Dataflow_Model_Importer_Product::importCustomOptions()
				 * ускоряет работу этих модулей.
				 */
				Df_Dataflow_Model_Importer_Product::P__SKIP_CUSTOM_OPTIONS => true
				,Df_Dataflow_Model_Importer_Product::P__ROW =>
					Df_Dataflow_Model_Import_Product_Row::i(array_merge(
						$this->getExistingMagentoProduct()
							? $this->getProductDataUpdateOnly()
							: $this->getProductDataNewOnly()
						, $this->getProductDataNewOrUpdate()
					))
			));
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string|int|float|bool|null) */
	protected function getProductDataNewOrUpdateBase() {
		df_assert_sku($this->getSku());
		/** @var array(string => string|int|float|bool|null) $result */
		$result = array(
			Df_C1_Const::ENTITY_EXTERNAL_ID => $this->getEntityOffer()->getExternalId()
			,'sku' => $this->getSku()
			,'category_ids' => df_csv($this->getCategoryIds())
			,'description' => $this->getDescription()
			,'short_description' => $this->getDescriptionShort()
			,'tax_class_id' => $this->taxClassId()
		);
		/**
		 * Позволяет менять артикул товара при импорте
		 * http://magento-forum.ru/topic/3653/
		 */
		if ($this->getSkuNew()) {
			df_assert_sku($this->getSkuNew());
			$result[Df_Dataflow_Model_Import_Product_Row::FIELD__SKU_NEW] = $this->getSkuNew();
		}
		/** @var bool $hasQuantity */
		$hasQuantity = !is_null($this->getEntityOffer()->getQuantity());
		if ($hasQuantity || !$this->getExistingMagentoProduct()) {
			/**
			 * 2015-01-23
			 * В Magento количество товара может быть дробным:
			 * @see Mage_CatalogInventory_Model_Stock_Item::getQty()
			 * @see Mage_CatalogInventory_Model_Stock_Item::getMinQty()
			 *
			 * В 1С количество товара также может быть дробным:
			 * http://magento-forum.ru/topic/4389/
			 */
			$result['qty'] = $hasQuantity ? $this->getEntityOffer()->getQuantity() : 0.0;
		}
		/** @var bool $hasPrice */
		$hasPrice = $this->getDocumentCurrentAsOffers()->hasPrices() && !is_null($this->getPrice());
		if (
				// Обратите внимание, что 1С может вполне не передавать цену.
				// Это возможно в следующих ситуациях:
				// 1) Когда цена на товар отсутствует в 1С
				// 2) Когда передача цен отключена в настройках узла обмена
				// (а это возможно, как минимум, в новых версиях модуля 1С-Битрикс (ветка 4)).
				// 3) В новых версиях  модуля 1С-Битрикс (ветка 4, CommerceML 2.0.8)
				// 1С передаёт цены не в файле offers.xml (как было в прежних версиях),
				// а отдельным файлом prices_*.xml, который передаётся после файла offers_*.xml,
				// в то время как файл offers_*.xml цен не содержит.
				$hasPrice
			||
				// Поле «price» является обязательным при импорте нового товара
				!$this->getExistingMagentoProduct()
		) {
			$result['price'] = $hasPrice ? $this->getPrice() : 0.0;
		}
		/**
		 * Обратите внимание, что метод
		 * @uses Df_C1_Cml2_Import_Data_Entity::getName()
		 * может вернуть null.
		 * В частности, новая версия модуля 1С-Битрикс (ветка 4, CommerceML 2.0.8)
		 * передаёт цены на товарные предложения отдельно от самих товарных предложений.
		 * Файл товарных предложений (offers_*.xml):
				<Предложение>
					<Ид>cbcf4968-55bc-11d9-848a-00112f43529a</Ид>
					<НомерВерсии>AAAAAQAAACk=</НомерВерсии>
					<ПометкаУдаления>false</ПометкаУдаления>
					<Наименование>Барбарис (конфеты)</Наименование>
				</Предложение>
		 * Файл цен (prices_*.xml):
				<Предложение>
					<Ид>cbcf4968-55bc-11d9-848a-00112f43529a</Ид>
					<Цены>
						<Цена>
							<Представление>60,94 RUB за кг</Представление>
							<ИдТипаЦены>ceb752cd-c697-11e2-8026-0015e9b8c48d</ИдТипаЦены>
							<ЦенаЗаЕдиницу>60.94</ЦенаЗаЕдиницу>
							<Валюта>RUB</Валюта>
						</Цена>
					</Цены>
				</Предложение>
		 * Так вот, файл цен не содержит наименования товарных предложений,
		 * однако это не мешает нам успешно обрабатывать такой файл,
		 * потому что файл товарных предложений импортируется всегда ранее файла цен,
		 * и товары уже присутствуют в базе данных магазина,
		 * достаточно лишь обновить их цены, наименование нам особо и не нужно.
		 *
		 * Аналогично, в новой версии модуля 1С-Битрикс (ветка 4, CommerceML 2.0.8)
		 * 1С передаёт товарные остатки отдельным файлом rests_*.xml,
		 * который имеет следующую структуру:
				<Предложение>
					<Ид>cbcf4968-55bc-11d9-848a-00112f43529a</Ид>
					<Остатки>
						<Остаток>
							<Количество>765</Количество>
						</Остаток>
					</Остатки>
				</Предложение>
		 * Как можно увидеть, наименование товарного предложения и в этом случае отсутствует.
		 */
		if ($this->getEntityOffer()->getName()) {
			$result['name'] = $this->getName();
			$result['product_name'] = $this->getEntityOffer()->getName();
		}
		$result['weight'] =
			$this->getEntityProduct()->getWeight()
			? $this->getEntityProduct()->getWeight()
			: (
				$this->getExistingMagentoProduct()
				? $this->getExistingMagentoProduct()->getWeight()
				: 0.0
			)
		;
		if (!is_null($this->getExistingMagentoProduct())) {
			/** @var Df_CatalogInventory_Model_Stock_Item|null $stockItem  */
			$stockItem = $this->getExistingMagentoProduct()->getStockItem();
			if (is_null($stockItem)) {
				$stockItem = Df_CatalogInventory_Model_Stock_Item::i();
				$stockItem->assignProduct($this->getExistingMagentoProduct());
			}
			$result['is_in_stock'] =
				(df_nat0($stockItem->getMinQty()) < $this->getEntityOffer()->getQuantity())
			;
		}
		else {
			/** @var int $minQty */
			$minQty = df_nat0($this->getStoreConfig(
				Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MIN_QTY
			));
			$result['is_in_stock'] = ($minQty < $this->getEntityOffer()->getQuantity());
		}
		if ($this->getDocumentCurrentAsOffers()->hasPrices()) {
			/** @var array(string => string) $tierPrices */
			$tierPrices = $this->getTierPricesInImporterFormat();
			if ($tierPrices) {
				$result = array_merge($result, $tierPrices);
			}
		}
		return $result;
	}
	
	/** @return array(string => string|int|float|bool|null) */
	protected function getProductDataUpdateOnly() {
		/** @var array(string => string|int|float|bool|null) $result */
		$result = array('store' => $this->getExistingMagentoProduct()->getStore()->getCode());
		return $result;
	}

	/** @return int[] */
	private function getCategoryIds() {
		if (!isset($this->{__METHOD__})) {
			/** @var int[] $result */
			$result = $this->getEntityProduct()->getCategoryIds();
			/**
			 * Сохраняем уже имеющиеся привязки товара к разделам
			 * http://magento-forum.ru/topic/3432/
			 */
			if ($this->getExistingMagentoProduct()) {
				/**
				 * 2015-02-07
				 * Раньше здесь стоял дефектный программный код
				 * $result = array_unique($result + $this->getExistingMagentoProduct()->getCategoryIds())
				 * Причиной такого дефектого кода
				 * было моё неправильное понимание операции «+» для массивов.
				 * На самом деле, операция
				 * array_unique(array(1,2,3) + array(3,4,5)) вернёт array(1,2,3).
				 * http://3v4l.org/rva29
				 * Дело в том, что операция «+» игнорирует те элементы второго массива,
				 * ключи которого присутствуют в первом массиве:
				 * «The keys from the first array will be preserved.
				 * If an array key exists in both arrays,
				 * then the element from the first array will be used
				 * and the matching key's element from the second array will be ignored.»
				 * http://php.net/manual/function.array-merge.php
				 *
				 * Правильно тут использовать @uses array_merge().
				 * Т.к. ключи массива — целочисленные, то результат применения @uses array_merge()
				 * может содержать повторяющиеся элементы,
				 * которые мы удаляем посредством @uses dfa_unique_fast().
				 * http://php.net/manual/function.array-merge.php
				 * «If, however, the arrays contain numeric keys,
				 * the later value will not overwrite the original value, but will be appended.»
				 */
				$result = dfa_unique_fast(array_merge(
					$result, $this->getExistingMagentoProduct()->getCategoryIds()
				));
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getDescription() {
		return $this->getDescriptionAbstract(
			$productField =	Df_Catalog_Model_Product::P__DESCRIPTION
			,$fieldsToUpdate = array(
				Df_C1_Config_Source_WhichDescriptionFieldToUpdate::V__DESCRIPTION
				,Df_C1_Config_Source_WhichDescriptionFieldToUpdate::V__BOTH
			)
		);
	}

	/**
	 * @param string $productField
	 * @param string[] $fieldsToUpdate
	 * @return string
	 */
	private function getDescriptionAbstract($productField, array $fieldsToUpdate) {
		df_param_string_not_empty($productField, 0);
		/** @var string $result */
		$result = df_1c_cfg()->product()->description()->getDefault();
		/** @var string|null $currentDescription */
		$currentDescription =
			!$this->getExistingMagentoProduct()
			? null
			: $this->getExistingMagentoProduct()->getDataUsingMethod($productField)
		;
		/** @var bool $canUpdateCurrentDescription */
		$canUpdateCurrentDescription =
				!df_1c_cfg()->product()->description()->preserveInUnique()
			||
				!$currentDescription
			||
				($currentDescription === df_1c_cfg()->product()->description()->getDefault())
		;
		// Обрабатываем случай,
		// когда в 1С на товарной карточке заполнено поле «Файл описания для сайта».
		if (
				(Df_Catalog_Model_Product::P__DESCRIPTION === $productField)
			&&
				$this->getEntityProduct()->getDescriptionFull()
		) {
			$result =
				$canUpdateCurrentDescription
				? $this->getEntityProduct()->getDescriptionFull()
				: $currentDescription
			;
		}
		else if (
			!in_array(df_1c_cfg()->product()->description()->whichFieldToUpdate(), $fieldsToUpdate)
		) {
			if ($currentDescription) {
				$result = $currentDescription;
			}
		}
		else {
			$result =
				$canUpdateCurrentDescription
				? $this->getEntityProduct()->getDescription()
				: $currentDescription
			;
		}
		df_result_string($result);
		return $result;
	}

	/** @return string */
	private function getDescriptionShort() {
		return $this->getDescriptionAbstract(
			$productField =	Df_Catalog_Model_Product::P__SHORT_DESCRIPTION
			,$fieldsToUpdate = array(
				Df_C1_Config_Source_WhichDescriptionFieldToUpdate::V__SHORT_DESCRIPTION
				,Df_C1_Config_Source_WhichDescriptionFieldToUpdate::V__BOTH
			)
		);
	}

	/** @return string */
	private function getName() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = $this->getEntityOffer()->getName();
			/** @noinspection PhpUndefinedMethodInspection */
			if (
				/**
				 * Обратите внимание, что метод
				 * @uses Df_C1_Cml2_Import_Data_Entity::getName()
				 * может вернуть null.
				 * В частности, новая версия модуля 1С-Битрикс (ветка 4, CommerceML 2.0.8)
				 * передаёт цены на товарные предложения отдельно от самих товарных предложений.
				 * Файл товарных предложений (offers_*.xml):
						<Предложение>
							<Ид>cbcf4968-55bc-11d9-848a-00112f43529a</Ид>
							<НомерВерсии>AAAAAQAAACk=</НомерВерсии>
							<ПометкаУдаления>false</ПометкаУдаления>
							<Наименование>Барбарис (конфеты)</Наименование>
						</Предложение>
				 * Файл цен (prices_*.xml):
						<Предложение>
							<Ид>cbcf4968-55bc-11d9-848a-00112f43529a</Ид>
							<Цены>
								<Цена>
									<Представление>60,94 RUB за кг</Представление>
									<ИдТипаЦены>ceb752cd-c697-11e2-8026-0015e9b8c48d</ИдТипаЦены>
									<ЦенаЗаЕдиницу>60.94</ЦенаЗаЕдиницу>
									<Валюта>RUB</Валюта>
								</Цена>
							</Цены>
						</Предложение>
				 * Так вот, файл цен не содержит наименования товарных предложений,
				 * однако это не мешает нам успешно обрабаатывать такой файл,
				 * потому что файл товарных предложений импортируется всегда ранее файла цен,
				 * и товары уже присутствуют в базе данных магазина,
				 * достаточно лишь обновить их цены, наименование нам особо и не нужно.
				 *
				 * Аналогично, в новой версии модуля 1С-Битрикс (ветка 4, CommerceML 2.0.8)
				 * 1С передаёт товарные остатки отдельным файлом rests_*.xml, который имеет следующую структуру:
						<Предложение>
							<Ид>cbcf4968-55bc-11d9-848a-00112f43529a</Ид>
							<Остатки>
								<Остаток>
									<Количество>765</Количество>
								</Остаток>
							</Остатки>
						</Предложение>
				 * Как можно увидеть, наименование товарного предложения и в этом случае отсутствует.
				 */
					!$result
				||
						/** http://magento-forum.ru/topic/3655/ */
						Df_C1_Config_Source_ProductNameSource::isFull(
							df_1c_cfg()->product()->name()->getSource()
						)
					&&
						// Небольшая тонкость.
						// Дело в том, что имя в товарном предложении может быть
						// «Active Kids Norveg кальсоны детские (112)»,
						// а имя в товаре — «Active Kids Norveg кальсоны детские».
						// Так бывает, когда в «1С:Управление торговлей»
						// характеристики заданы индивидуально для товара,
						// а не общие для вида номенклатуры.
						// В этом случае нам разумней подставить в товар в интернет-магазине
						// более информативное имя из товарного предложения
						// («Active Kids Norveg кальсоны детские (112)»).
						(
								$this->getEntityOffer()->getName()
							===
								$this->getEntityProduct()->getName()
					)
			) {
				$result = $this->getEntityProduct()->getNameFull();
			}
			df_result_string_not_empty($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string|int|float|bool|null) */
	private function getProductDataNewOnly() {
		/** @var array(string => string|int|float|bool|null) $result */
		$result = array(
			'websites' => $this->store()->getWebsite()->getId()
			,'attribute_set' => $this->getEntityProduct()->getAttributeSet()->getAttributeSetName()
			,'type' => $this->getType()
			,'product_type_id' => $this->getType()
			,'store' => $this->store()->getCode()
			,'store_id' => $this->storeId()
			,'has_options' => false
			,'meta_title' => null
			,'meta_description' => null
			,'image' => null
			,'small_image' => null
			,'thumbnail' => null
			,'url_key' => null
			,'url_path' => null
			,'image_label' => null
			,'small_image_label'	=> null
			,'thumbnail_label' => null
			,'country_of_manufacture' => null
			,'visibility' => $this->getVisibilityAsString()
			,'meta_keyword' => null
			,'use_config_min_qty' => true
			,'is_qty_decimal' => null
			,'use_config_backorders' => true
			,'use_config_min_sale_qty' => true
			,'use_config_max_sale_qty' => true
			,'low_stock_date' => null
			,'use_config_notify_stock_qty' => true
			,'manage_stock' => true
			,'use_config_manage_stock' => true
			,'stock_status_changed_auto' => null
			,'use_config_qty_increments' => true
			,'use_config_enable_qty_inc' => true
			,'is_decimal_divided' => null
			,'stock_status_changed_automatically' => null
			,'use_config_enable_qty_increments' => true
		);
		return $result;
	}

	/** @return array(string => string|int|float|bool|null) */
	private function getProductDataNewOrUpdate() {
		/** @var array(string => string|int|float|bool|null) $result */
		$result = $this->getProductDataNewOrUpdateBase();
		/**
		 * Нам не нужно заниматься товарными свойствами и опциями
		 * при обработке файлов rests_*.xml, prices_*.xml и т.п.
		 * Более того, такая обработка может привести к некоторым сбоям.
		 */
		if ($this->getDocumentCurrentAsOffers()->isBase()) {
			$result = array_merge($result
				,$this->getProductDataNewOrUpdateAttributeValues(
					$this->getEntityProduct()->getAttributeValuesCustom()->getItems()
				)
				,$this->getProductDataNewOrUpdateAttributeValues(array(
					Df_C1_Cml2_Import_Data_Entity_AttributeValue_ProcurementDate::i(
						$this->getEntityOffer()
					)
					,Df_C1_Cml2_Import_Data_Entity_AttributeValue_Barcode::i(
						$this->getEntityOffer()
					)
				))
				,$this->getProductDataNewOrUpdateOptionValues()
			);
		}
		return $result;
	}

	/**
	 * @param Df_C1_Cml2_Import_Data_Entity_AttributeValue[] $attributeValues
	 * @return array(string => string|int|float|bool|null)
	 */
	private function getProductDataNewOrUpdateAttributeValues($attributeValues) {
		/** @var array(string => string|int|float|bool|null) $result */
		$result = array();
		foreach ($attributeValues as $value) {
			/** @var Df_C1_Cml2_Import_Data_Entity_AttributeValue $value */
			if ($value->isValidForImport()) {
				$result[$value->getAttributeName()] = $value->getValueForDataflow();
			}
		}
		return $result;
	}

	/**
	 * Импорт значений настраиваемых опций настраиваемых товаров
	 * @return array(string => string|int|float|bool|null)
	 */
	private function getProductDataNewOrUpdateOptionValues() {
		/** @var array(string => string|int|float|bool|null) $result */
		$result = array();
		if ($this->getEntityOffer()->getOptionValues()->hasItems()) {
			df_1c()->create1CAttributeGroupIfNeeded(
				$this->getEntityProduct()->getAttributeSet()->getId()
			);
		}
		// Импорт значений настраиваемых опций
		if ($this->getEntityOffer()->isTypeConfigurableChild()) {
			$this->getEntityOffer()->getOptionValues()->addAbsentItems();
		}
		foreach ($this->getEntityOffer()->getOptionValues() as $optionValue) {
			/** @var Df_C1_Cml2_Import_Data_Entity_OfferPart_OptionValue $optionValue */
			Df_Catalog_Model_Installer_AddAttributeToSet::p(
				$optionValue->getAttributeMagento()->getAttributeCode()
				,$this->getEntityProduct()->getAttributeSet()->getId()
				,Df_C1_Const::PRODUCT_ATTRIBUTE_GROUP_NAME
			);
			/** @var Df_Eav_Model_Entity_Attribute_Option $option */
			$option = $optionValue->getOption();
			$result[$optionValue->getAttributeMagento()->getName()] = $option->getData('value');
		}
		return $result;
	}


	/**
	 * Позволяет менять артикул товара при импорте
	 * http://magento-forum.ru/topic/3653/
	 * @return string|null
	 */
	private function getSkuNew() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $result */
			$result = null;
			if ($this->getExistingMagentoProduct() && !$this->getEntityOffer()->isTypeConfigurableChild()) {
				/** @var string|null $skuFrom1C */
				$skuFrom1C = $this->getEntityProduct()->getSku();
				if (
						df_check_sku($this->getEntityProduct()->getSku())
					&&
						($this->getExistingMagentoProduct()->getSku() !== $skuFrom1C)
					&&
						!df_h()->catalog()->product()->isExist($skuFrom1C)
				) {
					$result = $skuFrom1C;
				}
			}
			$this->{__METHOD__} = df_n_set($result);
		}
		return df_n_get($this->{__METHOD__});
	}

	/** @return mixed[] */
	private function getTierPricesInImporterFormat() {
		if (!isset($this->{__METHOD__})) {
			/** @var mixed[] $result  */
			$result = array();
			/** @var Df_C1_Cml2_Import_Data_Entity_OfferPart_Price|null $mainPrice */
			$mainPrice = $this->getEntityOffer()->getPrices()->getMain();
			foreach ($this->getEntityOffer()->getPrices()->getItems() as $price) {
				/** @var Df_C1_Cml2_Import_Data_Entity_OfferPart_Price $price */
				if (is_null($mainPrice) || ($price->getId() !== $mainPrice->getId())) {
					/** @var int|null $customerGroupId */
					$customerGroupId = $price->getPriceType()->getCustomerGroupId();
					if ($customerGroupId) {
						/** @var string $groupPriceKey */
						$groupPriceKey = implode('_', array(
							'rm_tier_price', $this->store()->getWebsiteId(), $customerGroupId, 1
						));
						$result[$groupPriceKey] = $price->getPriceBase();
					}
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	private function getVisibilityAsString() {
		return dfa(Mage_Catalog_Model_Product_Visibility::getOptionArray(), $this->getVisibility());
	}

	/**
	 * 2015-08-09
	 * @used-by getProductDataNewOrUpdateBase()
	 * @return int|string
	 */
	private function taxClassId() {
		/** @var int|null $result */
		$result = $this->getEntityProduct()->taxClassId();
		return $result ? $result : df_tax_h()->__('None');
	}
}