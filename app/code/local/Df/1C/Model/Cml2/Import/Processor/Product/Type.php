<?php
abstract class Df_1C_Model_Cml2_Import_Processor_Product_Type
	extends Df_1C_Model_Cml2_Import_Processor_Product {
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
				/** @var Df_1C_Model_Cml2_Import_Data_Document_Offers $document */
				$document = $this->getState()->import()->getDocumentCurrentAsOffers();
				if (
						!$document->isBase()
					&&
						($document->hasPrices() || $document->hasStock())
				) {
					/** @link http://magento-forum.ru/topic/4898/ */
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
				 * Модули «1С: Управление торговлей» и «МойСклад»
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

	/** @return array(string => string) */
	protected function getProductDataNewOrUpdateBase() {
		df_assert_sku($this->getSku());
		/** @var array(string => string) $result */
		$result = array(
			Df_Eav_Const::ENTITY_EXTERNAL_ID => $this->getEntityOffer()->getExternalId()
			,'sku' => $this->getSku()
			,'category_ids' => implode(',', $this->getCategoryIds())
			,'description' => $this->getDescription()
			,'short_description' => $this->getDescriptionShort()
		);
		/**
		 * Позволяет менять артикул товара при импорте
		 * @link http://magento-forum.ru/topic/3653/
		 */
		if ($this->getSkuNew()) {
			df_assert_sku($this->getSkuNew());
			$result[Df_Dataflow_Model_Import_Product_Row::FIELD__SKU_NEW] = $this->getSkuNew();
		}
		/** @var bool $hasQuantity */
		$hasQuantity = !is_null($this->getEntityOffer()->getQuantity());
		if (
				$hasQuantity
			||
				!$this->getExistingMagentoProduct()
		) {
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
		 * @see Df_1C_Model_Cml2_Import_Data_Entity::getName()
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
		if ($this->getEntityProduct()->getWeight()) {
			$result['weight'] = $this->getEntityProduct()->getWeight();
		}
		else {
			/** @var float|null $currentWeight */
			$currentWeight = null;
			if (!is_null($this->getExistingMagentoProduct())) {
				$currentWeight =
					$this->getExistingMagentoProduct()->getDataUsingMethod(
						Df_Catalog_Model_Product::P__WEIGHT
					)
				;
			}
			$result['weight'] = !$currentWeight ? 0.0 : $currentWeight;
		}
		if (!is_null($this->getExistingMagentoProduct())) {
			/** @var Mage_CatalogInventory_Model_Stock_Item|null $stockItem  */
			$stockItem = $this->getExistingMagentoProduct()->getDataUsingMethod('stock_item');
			if (is_null($stockItem)) {
				$stockItem->assignProduct($this->getExistingMagentoProduct());
			}
			$result['is_in_stock'] =
				(rm_nat0($stockItem->getMinQty()) < $this->getEntityOffer()->getQuantity())
			;
		}
		else {
			/** @var int $minQty */
			$minQty = rm_nat0(Mage::getStoreConfig(
				Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MIN_QTY
				,rm_state()->getStoreProcessed()
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
	
	/** @return array(string => string) */
	protected function getProductDataUpdateOnly() {
		$result = array('store' => $this->getExistingMagentoProduct()->getStore()->getCode());
		if (!$this->getExistingMagentoProduct()->getTaxClassId()) {
			$result['tax_class_id'] = df_mage()->taxHelper()->__('None');
		}
		return $result;
	}

	/** @return int[] */
	private function getCategoryIds() {
		if (!isset($this->{__METHOD__})) {
			/** @var int[] $result */
			$result = $this->getEntityProduct()->getCategoryIds();
			/**
			 * Сохраняем уже имеющиеся привязки товара к разделам
			 * @link http://magento-forum.ru/topic/3432/
			 */
			if (!is_null($this->getExistingMagentoProduct())) {
				$result = array_unique($result + $this->getExistingMagentoProduct()->getCategoryIds());
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
				Df_1C_Model_Config_Source_WhichDescriptionFieldToUpdate::VALUE__DESCRIPTION
				,Df_1C_Model_Config_Source_WhichDescriptionFieldToUpdate::VALUE__BOTH
			)
		);
	}

	/**
	 * @param string $productField
	 * @param string[] $fieldsToUpdate
	 * @return string
	 */
	private function getDescriptionAbstract($productField, array $fieldsToUpdate) {
		df_param_string($productField, 0);
		/** @var string $result */
		$result = df_cfg()->_1c()->product()->description()->getDefault();
		/** @var string|null $currentDescription */
		$currentDescription =
			is_null($this->getExistingMagentoProduct())
			? null
			: $this->getExistingMagentoProduct()->getDataUsingMethod($productField)
		;
		/** @var bool $canUpdateCurrentDescription */
		$canUpdateCurrentDescription =
				!df_cfg()->_1c()->product()->description()->preserveInUnique()
			||
				!$currentDescription
			||
				($currentDescription === df_cfg()->_1c()->product()->description()->getDefault())
		;
		/**
		 * Обрабатываем случай,
		 * когда в 1С на товарной карточке заполнено поле «Файл описания для сайта»
		 */
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
		else {
			if (
				!in_array(
					df_cfg()->_1c()->product()->description()->whichFieldToUpdate()
					,$fieldsToUpdate
				)
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
		}
		df_result_string($result);
		return $result;
	}

	/** @return string */
	private function getDescriptionShort() {
		return $this->getDescriptionAbstract(
			$productField =	Df_Catalog_Model_Product::P__SHORT_DESCRIPTION
			,$fieldsToUpdate = array(
				Df_1C_Model_Config_Source_WhichDescriptionFieldToUpdate::VALUE__SHORT_DESCRIPTION
				,Df_1C_Model_Config_Source_WhichDescriptionFieldToUpdate::VALUE__BOTH
			)
		);
	}

	/** @return string */
	private function getName() {
		if (!isset($this->{__METHOD__})) {
			/** @var string $result */
			$result = $this->getEntityOffer()->getName();
			if (
				/**
				 * Обратите внимание, что метод
				 * @see Df_1C_Model_Cml2_Import_Data_Entity::getName()
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
						(
							/**
							 * @link http://magento-forum.ru/topic/3655/
							 */
								Df_1C_Model_Config_Source_ProductNameSource::VALUE__NAME_FULL
							===
								df_cfg()->_1c()->product()->name()->getSource()
						)
					&&
						/**
						 * Небольшая тонкость.
						 * Дело в том, что имя в товарном предложении может быть
						 * «Active Kids Norveg кальсоны детские (112)»,
						 * а имя в товаре — «Active Kids Norveg кальсоны детские».
						 * Так бывает, когда в «1С: Управление торговлей»
						 * характеристики заданы индивидуально для товара,
						 * а не общие для вида номенклатуры.
						 * В этом случае нам разумней подставить в товар в интернет-магазине
						 * более информативное имя из товарного предложения
						 * («Active Kids Norveg кальсоны детские (112)»).
						 */
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

	/** @return string[] */
	private function getProductDataNewOnly() {
		/** @var string[] $result */
		$result = array(
			'websites' => rm_state()->getStoreProcessed()->getWebsite()->getId()
			,'attribute_set' =>
				$this->getEntityProduct()->getAttributeSet()->getAttributeSetName()
			,'type' => $this->getType()
			,'product_type_id' => $this->getType()
			,'store' => rm_state()->getStoreProcessed()->getCode()
			,'store_id' => rm_state()->getStoreProcessed()->getId()
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
			,'tax_class_id' => df_mage()->taxHelper()->__('None')
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

	/** @return array(string => string) */
	private function getProductDataNewOrUpdate() {
		/** @var array(string => string) $result */
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
					Df_1C_Model_Cml2_Import_Data_Entity_AttributeValue_ProcurementDate::i(
						$this->getEntityOffer()
					)
					,Df_1C_Model_Cml2_Import_Data_Entity_AttributeValue_Barcode::i(
						$this->getEntityOffer()
					)
				))
				,$this->getProductDataNewOrUpdateOptionValues()
			);
		}
		return $result;
	}

	/**
	 * @param Df_1C_Model_Cml2_Import_Data_Entity_AttributeValue[] $attributeValues
	 * @return array(string => string)
	 */
	private function getProductDataNewOrUpdateAttributeValues($attributeValues) {
		/** @var array(string => string) $result */
		$result = array();
		foreach ($attributeValues as $value) {
			/** @var Df_1C_Model_Cml2_Import_Data_Entity_AttributeValue $value */
			if ($value->isValidForImport()) {
				$result[$value->getAttributeName()] = $value->getValueForDataflow();
			}
		}
		return $result;
	}

	/**
	 * Импорт значений настраиваемых опций настраиваемых товаров
	 * @return array(string => string)
	 */
	private function getProductDataNewOrUpdateOptionValues() {
		/** @var array(string => string) $result */
		$result = array();
		if (0 < count($this->getEntityOffer()->getOptionValues())) {
			df_h()->_1c()->create1CAttributeGroupIfNeeded(
				$this->getEntityProduct()->getAttributeSet()->getId()
			);
		}
		// Импорт значений настраиваемых опций
		if ($this->getEntityOffer()->isTypeConfigurableChild()) {
			$this->getEntityOffer()->getOptionValues()->addAbsentItems();
		}
		foreach ($this->getEntityOffer()->getOptionValues() as $optionValue) {
			/** @var Df_1C_Model_Cml2_Import_Data_Entity_OfferPart_OptionValue $optionValue */
			Df_Catalog_Model_Installer_AddAttributeToSet::processStatic(
				$optionValue->getAttributeMagento()->getAttributeCode()
				,$this->getEntityProduct()->getAttributeSet()->getId()
				,Df_1C_Const::PRODUCT_ATTRIBUTE_GROUP_NAME
			);
			/** @var Mage_Eav_Model_Entity_Attribute_Option $option */
			$option = $optionValue->getOption();
			$result[$optionValue->getAttributeMagento()->getName()] = $option->getData('value');
		}
		return $result;
	}


	/**
	 * Позволяет менять артикул товара при импорте
	 * @link http://magento-forum.ru/topic/3653/
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
			$this->{__METHOD__} = rm_n_set($result);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return mixed[] */
	private function getTierPricesInImporterFormat() {
		if (!isset($this->{__METHOD__})) {
			/** @var mixed[] $result  */
			$result = array();
			/** @var Df_1C_Model_Cml2_Import_Data_Entity_OfferPart_Price|null $mainPrice */
			$mainPrice = $this->getEntityOffer()->getPrices()->getMain();
			foreach ($this->getEntityOffer()->getPrices()->getItems() as $price) {
				/** @var Df_1C_Model_Cml2_Import_Data_Entity_OfferPart_Price $price */
				if (is_null($mainPrice) || ($price->getId() !== $mainPrice->getId())) {
					/** @var int|null $customerGroupId */
					$customerGroupId = $price->getPriceType()->getCustomerGroupId();
					if ($customerGroupId) {
						/** @var string $groupPriceKey */
						$groupPriceKey =
							rm_sprintf(
								'rm_tier_price_%d_%d_%d'
								,rm_state()->getStoreProcessed()->getWebsiteId()
								,$customerGroupId
								,1
							)
						;
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
		/** @var string $result */
		$result =
			df_a(
				Mage_Catalog_Model_Product_Visibility::getOptionArray()
				,$this->getVisibility()
			)
		;
		df_result_string($result);
		return $result;
	}

	const _CLASS = __CLASS__;
}