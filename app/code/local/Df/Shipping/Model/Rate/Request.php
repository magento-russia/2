<?php
class Df_Shipping_Model_Rate_Request extends Mage_Shipping_Model_Rate_Request {
	/**
	 * @param string $countryIso2Code
	 * @return Df_Shipping_Model_Method
	 */
	public function checkCountryOriginIs($countryIso2Code) {
		df_param_string_not_empty($countryIso2Code, 0);
		if (
				$countryIso2Code
			!==
				$this->getOriginCountryId()
		) {
			$this->throwException(
				'Доставка {название службы и способа доставки в творительном падеже}'
				. ' возможна только только <b>%s</b>.'
				, df_h()->directory()->country()->getByIso2Code($countryIso2Code)
					->getNameInFormOrigin()
			);
		}
		return $this;
	}

	/**
	 * @param string $message
	 * @return string
	 */
	public function evaluateMessage($message) {
		return $this->getCarrier()->evaluateMessage($message, $this->getMessageVariables());
	}

	/** @return Df_Shipping_Model_Carrier */
	public function getCarrier() {return $this->_getData(self::P__CARRIER);}

	/** @return float */
	public function declaredValueBase() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
					$this->getPackageValue()
				*
					$this->getCarrier()->getRmConfig()->admin()->getDeclaredValuePercent()
				/
					100.0
			;
		}
		return $this->{__METHOD__};
	}
	
	/** @return float */
	public function getDeclaredValueInHryvnias() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_currency()->convertFromBaseToHryvnias($this->declaredValueBase());
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	public function getDeclaredValueInRoubles() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_currency()->convertFromBaseToRoubles($this->declaredValueBase());
		}
		return $this->{__METHOD__};
	}
	
	/** @return float */
	public function getDeclaredValueInTenge() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_currency()->convertFromBaseToTenge($this->declaredValueBase());
		}
		return $this->{__METHOD__};
	}

	/** @return string|null */
	public function getDestinationCity() {
		/** @var string|null $result */
		$result = $this->_getData(self::P__DESTINATION__CITY);
		if (!$result) {
			/**
			 * Обратите внимание, что на странице корзины
			 * покупатель не может указать город в форме для расчёта тарифа доставки:
			 * в этой форме всего 3 поля: страна, субъект федерации (область) и почтовый индекс.
			 * При этом многие модули доставки неспособны рассчитывать тариф, не зная города.
			 * Однако, если субъект федерации — Москва или Санкт-Петербург,
			 * то мы можем подставить название субъекта федерации в качестве названия города.
			 */
			if (
				in_array(
					$this->getDestinationRegionName()
					,df_h()->directory()->country()->russia()->getFederalCities()
				)
			){
				$result = $this->getDestinationRegionName();
			}
		}
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}
	
	/** @return Df_Localization_Model_Morpher_Response|null */
	public function getDestinationCityMorpher() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				!$this->getDestinationCity()
				? null
				: Df_Localization_Model_Morpher::s()->getResponseSilent($this->getDestinationCity())
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return Df_Directory_Model_Country|null */
	public function getDestinationCountry() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				!$this->getDestinationCountryId()
				? null
				: Df_Directory_Model_Country::ld($this->getDestinationCountryId())
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return string|null */
	public function getDestinationCountryId() {
		/** @var string|null $result */
		$result = $this->_getData(self::P__DESTINATION__COUNTRY_ID);
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	/** @return string|null */
	public function getDestinationPostalCode() {
		/** @var string|null $result */
		$result = $this->_getData(self::P__DESTINATION__POSTAL_CODE);
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}

	/** @return Df_Directory_Model_Region|null */
	public function getDestinationRegion() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				df_h()->directory()->getRegions()->getItemById(
					$this->getDestinationRegionId()
				)
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return string|null */
	public function getDestinationRegionalCenter() {
		return
			is_null($this->getDestinationRegion())
			? null
			: $this->getDestinationRegion()->getData('df_capital')
		;
	}

	/** @return int|null */
	public function getDestinationRegionId() {
		/** @var int|null $result */
		$result = $this->_getData(self::P__DESTINATION__REGION_ID);
		if (!is_null($result)) {
			df_result_integer($result);
		}
		return $result;
	}

	/** @return string|null */
	public function getDestinationRegionName() {
		return
			!$this->getDestinationRegionId()
			? $this->getDestRegionCode()
			: df_h()->directory()->getRegionNameById($this->getDestinationRegionId())
		;
	}
	
	/** @return float */
	public function getDimensionMaxRoughInMetres() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df()->units()->length()->convertToMetres(max($this->getDimensionsRough()))
			;
		}
		return $this->{__METHOD__};
	}
	
	/** @return float */
	public function getDimensionMinRoughInMetres() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df()->units()->length()->convertToMetres(min($this->getDimensionsRough()))
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Примерные габариты (очень грубый алгоритм)
	 * @return float[]
	 */
	public function getDimensionsRough() {
		if (!isset($this->{__METHOD__})) {
			/** @var float[] $result */
			$result = array();
			foreach ($this->getQuoteItemsSimple() as $quoteItem) {
				/** @var Mage_Sales_Model_Quote_Item $quoteItem */
				/** @var Df_Catalog_Model_Product $product */
				$product = $this->getProductsWithDimensions()->getItemById($quoteItem->getProductId());
				df_assert($product);
				/** @var array(string => float) $productDimensions */
				$productDimensions = array(
					Df_Catalog_Model_Product::P__WIDTH => $product->getWidth()
					,Df_Catalog_Model_Product::P__HEIGHT => $product->getHeight()
					,Df_Catalog_Model_Product::P__LENGTH => $product->getLength()
				);
				foreach ($productDimensions as $dimensionName => $productDimension) {
					/** @var string $dimensionName */
					df_assert_string($dimensionName);
					/** @var float $productDimension */
					df_assert_float($productDimension);
					$result[$dimensionName] =
						max(df_a($result, $dimensionName, 0), $productDimension)
					;
				}
			}
			df_result_array($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	public function getDimensionsRoughInMetres() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df()->units()->length()->convertToMetres($this->getDimensionsRough());
		}
		return $this->{__METHOD__};
	}
	
	/** @return float */
	public function getDimensionsSumRoughInMetres() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df()->units()->length()->convertToMetres(array_sum($this->getDimensionsRough()))
			;
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	public function getHeightRough() {
		return df_a($this->getDimensionsRough(), Df_Catalog_Model_Product::P__HEIGHT);
	}
	
	/** @return float */
	public function getHeightRoughInCentimeters() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df()->units()->length()->convertToCentimetres($this->getHeightRough());
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	public function getLengthRough() {
		return df_a($this->getDimensionsRough(), Df_Catalog_Model_Product::P__LENGTH);
	}
	
	/** @return float */
	public function getLengthRoughInCentimeters() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df()->units()->length()->convertToCentimetres($this->getLengthRough());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param bool $isDestination
	 * @return Df_Shipping_Model_Locator
	 */
	public function getLocator($isDestination) {
		if (!isset($this->{__METHOD__}[$isDestination])) {
			/** @var string $locatorClass */
			$locatorClass =
				Df_Core_Model_ClassManager::s()->getResourceClass(
					$caller = $this->getCarrier()
					,$resourceSuffix = 'Model_Locator'
				)
			;
			$this->{__METHOD__}[$isDestination] =
				df_model($locatorClass, array(
					Df_Shipping_Model_Locator::P__CITY =>
						$isDestination ? $this->getDestinationCity() : $this->getOriginCity()
					,Df_Shipping_Model_Locator::P__REGION_ID =>
						$isDestination
						? $this->getDestinationRegionId($throwExceptionIfNotFound = false)
						: $this->getOriginRegionId($throwExceptionIfNotFound = false)
					,Df_Shipping_Model_Locator::P__REGION_NAME =>
						$isDestination
						? $this->getDestinationRegionName()
						: $this->getOriginRegionName()
					,Df_Shipping_Model_Locator::P__COUNTRY_ID =>
						$isDestination
						? $this->getDestinationCountryId()
						: $this->getOriginCountryId()
					,Df_Shipping_Model_Locator::P__REQUEST => $this
					,Df_Shipping_Model_Locator::P__IS_DESTINATION => $isDestination
				))
			;
			df_assert($this->{__METHOD__}[$isDestination] instanceof Df_Shipping_Model_Locator);
		}
		return $this->{__METHOD__}[$isDestination];
	}

	/** @return Df_Shipping_Model_Locator */
	public function getLocatorDestination() {return $this->getLocator($isDestination = true);}

	/** @return Df_Shipping_Model_Locator */
	public function getLocatorOrigin() {return $this->getLocator($isDestination = false);}

	/** @return array(string => string) */
	public function getMessageVariables() {
		if (!isset($this->{__METHOD__})) { $this->{__METHOD__} = array(
			'{phone}' => df_cfg()->base()->getStorePhone($this->getStoreId())
			,'{телефон магазина}' => df_cfg()->base()->getStorePhone($this->getStoreId())
			,'{в месте доставки}' =>
				!$this->getDestinationCityMorpher()
				? rm_sprintf('в населённом пункте «%s»', $this->getDestinationCity())
				: $this->getDestinationCityMorpher()->getWhere()
			,'{в место доставки}' => $this->вМесто()
			,'{из места отправки}' => $this->изМеста()
			,'{в месте отправки}' =>
				!$this->getOriginCityMorpher()
				? rm_sprintf('в населённом пункте «%s»', $this->getOriginCity())
				: $this->getOriginCityMorpher()->getWhere()
			,'{в страну доставки}' => $this->вСтрану()
			,'{из страны отправки}' =>
				!$this->getOriginCountry()
				? '{из страны отправки}'
				: $this->getOriginCountry()->getNameInFormOrigin()
		); }
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getOriginCity() {
		/** @var string $result */
		$result = $this->_getData(self::P__ORIGIN__CITY);
		if (!$result) {
			/**
			 * Обратите внимание, что на странице корзины
			 * покупатель не может указать город в форме для расчёта тарифа доставки:
			 * в этой форме всего 3 поля: страна, субъект федерации (область) и почтовый индекс.
			 * При этом многие модули доставки неспособны рассчитывать тариф, не зная города.
			 * Однако, если субъект федерации — Москва или Санкт-Петербург,
			 * то мы можем подставить название субъекта федерации в качестве названия города.
			 */
			if (
				in_array(
					$this->getOriginRegionName()
					,df_h()->directory()->country()->russia()->getFederalCities()
				)
			){
				$result = $this->getOriginRegionName();
			}
		}
		if (!$result) {
			$this->throwException(
				'Администратор магазина должен указать город магазина в графе
				«Система» → «Настройки» → «Продажи» → «Доставка:
				общие настройки»→ «Расположение магазина» → «Город».'
			);
		}
		return $result;
	}
	
	/** @return Df_Localization_Model_Morpher_Response|null */
	public function getOriginCityMorpher() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				!$this->getOriginCity()
				? null
				: Df_Localization_Model_Morpher::s()->getResponseSilent($this->getOriginCity())
			);
		}
		return rm_n_get($this->{__METHOD__});
	}
	
	/** @return Df_Directory_Model_Country|null */
	public function getOriginCountry() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				!$this->getOriginCountryId()
				? null
				: Df_Directory_Model_Country::ld($this->getOriginCountryId())
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return string */
	public function getOriginCountryId() {return $this->_getData(self::P__ORIGIN__COUNTRY_ID);}

	/** @return string|null */
	public function getOriginPostalCode() {return $this->_getData(self::P__ORIGIN__POSTAL_CODE);}
	
	/** @return Df_Directory_Model_Region|null */
	public function getOriginRegion() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = rm_n_set(
				!$this->getOriginRegionId()
				? null
				: df_h()->directory()->getRegions()->getItemById($this->getOriginRegionId())
			);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/** @return string */
	public function getOriginRegionalCenter() {
		return
			df_nts(
				is_null($this->getOriginRegion())
				? null
				: $this->getOriginRegion()->getData('df_capital')
			)
		;
	}

	/**
	 * @param bool $throwExceptionIfNotFound[optional]
	 * @return int
	 */
	public function getOriginRegionId($throwExceptionIfNotFound = true) {
		/** @var int $result */
		$result = $this->_getData(self::P__ORIGIN__REGION_ID);
		// Если указан регион такой страны, для которой в Magento отсутствует перечень регионов,
		// то $result здесь будет равно названию региона.
		// По этой причине прежняя проверка !$result является неправильной.
		if (!df_check_integer($result)) {
			if ($throwExceptionIfNotFound) {
				$this->throwException(
					'Администратор магазина должен указать регион магазина в графе
					«Система» → «Настройки» → «Продажи» → «Доставка:
					общие настройки»→ «Расположение магазина» → «Область, край, республика».'
				);
			}
			else {
				$result = 0;
			}
		}
		return $result;
	}

	/** @return string */
	public function getOriginRegionName() {
		/** @var string $result */
		$result = $this->_getData(self::P__ORIGIN__REGION_ID);
		/**
		 * Обратите внимание, что «region_id» для склада — это необязательно число.
		 * Если для страны склада отсутствует справочник областей, и администратор
		 * самостоятельно написал область склада в настройках Magento,
		 * то «region_id» вернёт строку (то, что написал администратор в настройках).
		 */
		if (df_check_integer($result)) {
			$result = df_h()->directory()->getRegionNameById($this->getOriginRegionId());
		}
		if (is_null($result)) {
			// Сюда мы можем попасть после пересоздания базы регионов
			// (у них при этом меняются идентификаторы)
			$this->throwException(
				'Администратор магазина должен указать область магазина в графе'
				. ' «Система» → «Настройки» → «Продажи» → «Доставка:'
				. ' общие настройки»→ «Расположение магазина» → «Область, край, республика».'
			);
		}
		df_result_string($result);
		return $result;
	}

	/** @return Df_Catalog_Model_Resource_Product_Collection */
	public function getProductsWithDimensions() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Catalog_Model_Resource_Product_Collection $result */
			$result = Df_Catalog_Model_Product::c();
			$result
				->addIdFilter($this->getProductIds())
				->addAttributeToSelect(
					/**
					 * При включенном режиме денормализации
					 * габариты не будут загружены в коллекцию,
					 * если у свойств габаритов не включена опция
					 * «used_in_product_listing»
					 * (а до версии 2.16.2 Российской сборки эта опция была выключена).
					 * Версия 2.16.2 устраняет проблему.
					 * @see Df_Shipping_Model_Setup_2_16_2
					 */
					array(
						Df_Catalog_Model_Product::P__WIDTH
						,Df_Catalog_Model_Product::P__HEIGHT
						,Df_Catalog_Model_Product::P__LENGTH
						/**
						 * 2013-10-05
						 * Важно! Вес используется модулями доставки, но раньше он
						 */
						,Df_Catalog_Model_Product::P__WEIGHT
					)
				)
			;
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	public function getQty() {
		if (!isset($this->{__METHOD__})) {
			/** @var int $result */
			$result = 0;
			foreach ($this->getQuoteItemsSimple() as $item) {
				/** @var Mage_Sales_Model_Quote_Item $item */
				$result += rm_nat0($item->getQty());
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Обратите внимание, что если заказ содержит настраиваемый товар,
	 * то на программном в объекте «quote»
	 * ему соответствует не один объект «строка заказа»,
	 * а два: один — для настраиваемого товара,
	 * и второй — для простого товара,
	 * являющегося выбранным покупателем вариантом настраиваемого товара.
	 *
	 * Если заказ содержит товарный комплект («bundle»),
	 * то на программном в объекте «quote»
	 * ему соответствует не один объект «строка заказа»,
	 * а несколько: один — для товарного комплекта,
	 * и другие — для простого товара из товарного комплекта.
	 *
	 * Аналогично — для сгруппированных товаров.
	 *
	 * @link http://stackoverflow.com/a/8301170/254475
	 *
	 * Поэтому мы при комплектации груза учитываем только простые товары.
	 *
	 * Обратите также внимание, что та строка заказа,
	 * которая описывает простой вариант составного товара — не содержит в себе цену товара
	 * (а также вес строки — row_weight), * и цену нужно узнавать из той строки, которая соответствует всему составному товару.
	 *
	 * Обратите также внимание, что та строка заказа,
	 * которая описывает простой вариант составного товара,
	 * хранит неправильное количество заказанного товара — 1,
	 * и количество нужно узнавать из той строки, которая соответствует всему составному товару.
	 *
	 * Обратите также внимание что связь между родительским и дочерники строками заказа
	 * устанавливается автоматически при загрузке коллекции:
	 * @see Mage_Sales_Model_Resource_Order_Item_Collection::_afterLoad
	 * @return Mage_Sales_Model_Quote_Item[]
	 */
	public function getQuoteItemsSimple() {
		if (!isset($this->{__METHOD__})) {
			/** @var Mage_Sales_Model_Quote_Item[] $result */
			$result = array();
			foreach ($this->getAllItems() as $quoteItem) {
				/** @var Mage_Sales_Model_Quote_Item $quoteItem */
				if (Mage_Catalog_Model_Product_Type::TYPE_SIMPLE === $quoteItem->getProductType()) {
					$result[]= $quoteItem;
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	public function getVolumeBoxInCubicCentimeters() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				array_product(
					array(
						$this->getLengthRoughInCentimeters()
						, $this->getWidthRoughInCentimeters()
						, $this->getHeightRoughInCentimeters()
					)
				)
			;
		}
		return $this->{__METHOD__};
	}
	
	/** @return float */
	public function getVolumeInCubicMetres() {
		if (!isset($this->{__METHOD__})) {
			/** @var float $result */
			$result = 0.0;
			foreach ($this->getQuoteItemsSimple() as $quoteItem) {
				/** @var Mage_Sales_Model_Quote_Item $quoteItem */
				/** @var int $qty */
				$qty = Df_Sales_Model_Quote_Item_Extended::i($quoteItem)->getQty();
				/** @var Df_Catalog_Model_Product $product */
				$product = $this->getProductsWithDimensions()->getItemById($quoteItem->getProductId());
				df_assert($product);
				/** @var float $productVolume */
				$productVolume =
					array_product(
						array_map(
							array(df()->units()->length(), 'convertToMetres')
							,array(
								$product->getWidth()
								,$product->getHeight()
								,$product->getLength()
							)
						)
					)
				;
				$result += ($qty * $productVolume);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	public function getWeightInGrammes() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df()->units()->weight()->convertToGrammes($this->getPackageWeight());
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	public function getWeightInKilogrammes() {return $this->getWeightInGrammes() / 1000;}

	/**
	 * @used-by Df_Shipping_Model_Method::throwExceptionInvalidWeight()
	 * @used-by Df_Shipping_Model_Collector_Simple::errorInvalidWeight()
	 * @return string
	 */
	public function getWeightKgSD() {return rm_flits($this->getWeightInKilogrammes(), 1);}

	/** @return float */
	public function getWidthRough() {
		return df_a($this->getDimensionsRough(), Df_Catalog_Model_Product::P__WIDTH);
	}
	
	/** @return float */
	public function getWidthRoughInCentimeters() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df()->units()->length()->convertToCentimetres($this->getWidthRough());
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function isDestinationMoscow() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				in_array(
					'МОСКВА'
					,rm_uppercase(
						array($this->getDestinationCity(), $this->getDestinationRegionName())
					)
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function isDestinationRussia() {
		return Df_Directory_Helper_Country::ISO_2_CODE__RUSSIA === $this->getDestinationCountryId();
	}

	/** @return bool */
	public function isDestinationCityRegionalCenter() {
		return
				$this->getDestinationCity()
			&&
				df_text()->areEqualCI($this->getDestinationCity(), $this->getDestinationRegionalCenter())
		;
	}

	/** @return bool */
	public function isDomestic() {
		return $this->getDestinationCountryId() === $this->getOriginCountryId();
	}

	/** @return bool */
	public function isOriginMoscow() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				in_array(
					'МОСКВА'
					, rm_uppercase(array($this->getOriginCity(), $this->getOriginRegionName()))
				)
			;
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function isOriginCityRegionalCenter() {
		return
				$this->getOriginCity()
			&&
				df_text()->areEqualCI($this->getOriginCity(), $this->getOriginRegionalCenter())
		;
	}

	/** @return bool */
	public function isOriginRussia() {
		return Df_Directory_Helper_Country::ISO_2_CODE__RUSSIA === $this->getOriginCountryId();
	}

	/** @return bool */
	public function isOriginTheSameAsDestination() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_text()->areEqualCI($this->getDestinationCity(), $this->getOriginCity())
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @throws Df_Core_Exception_Client
	 * @param string $message
	 * @return void
	 */
	public function throwException($message) {
		// Обратите внимание, что функция func_get_args() не может быть параметром другой функции.
		/** @var mixed[] $arguments */
		$arguments = func_get_args();
		$message = rm_sprintf($arguments);
		df_error(df_no_escape($this->evaluateMessage($message)));
	}

	/**
	 * @throws Df_Core_Exception_Client
	 */
	public function throwExceptionInvalidDestination() {
		$this->throwException(
			'Доставка <b>{в место доставки}</b>'
			.' {название службы и способа доставки в творительном падеже} невозможна.'
		);
	}

	/**
	 * @throws Df_Core_Exception_Client
	 */
	public function throwExceptionInvalidOrigin() {
		$this->throwException(
			'Доставка <b>{из места отправки}</b>'
			.' {название службы и способа доставки в творительном падеже} невозможна.'
		);
	}

	/**
	 * @used-by getMessageVariables()
	 * @used-by Df_Shipping_Model_Collector_Conditional_WithForeign::errorInvalidCityDest()
	 * @return string
	 */
	public function вМесто() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				!$this->getDestinationCityMorpher()
				? 'в ' . $this->getDestinationCity()
				: $this->getDestinationCityMorpher()->getInFormDestination()
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by getMessageVariables()
	 * @used-by Df_Shipping_Model_Collector_Conditional_WithForeign::errorInvalidCountryDest()
	 * @return string
	 */
	public function вСтрану() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				!$this->getDestinationCountry()
				? '{в страну доставки}'
				: $this->getDestinationCountry()->getNameInFormDestination()
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by getMessageVariables()
	 * @used-by Df_Shipping_Model_Collector_Conditional_WithForeign::errorInvalidCityOrig()
	 * @return string
	 */
	public function изМеста() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				!$this->getOriginCityMorpher()
				? 'из ' . $this->getOriginCity()
				: $this->getOriginCityMorpher()->getInFormOrigin()
			;
		}
		return $this->{__METHOD__};
	}

	/** @return int|float[] */
	private function getDimensionDefaultValues() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Shipping_Model_Settings_Product $settings */
			$settings = df_cfg()->shipping()->product();
			$this->{__METHOD__} = array(
				Df_Catalog_Model_Product::P__WIDTH => $settings->getDefaultWidth()
				,Df_Catalog_Model_Product::P__HEIGHT => $settings->getDefaultHeight()
				,Df_Catalog_Model_Product::P__LENGTH =>	$settings->getDefaultLength()
			);
		}
		return $this->{__METHOD__};
	}

	/** @return int[] */
	private function getProductIds() {
		if (!isset($this->{__METHOD__})) {
			/** @var int[] $result */
			$result = array();
			foreach ($this->getAllItems() as $quoteItem) {
				/** @var Mage_Sales_Model_Quote_Item $quoteItem */
				$result[]= intval($quoteItem->getProduct()->getId());
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	const _CLASS = __CLASS__;
	const P__CARRIER = 'carrier';
	const P__DESTINATION__CITY = 'dest_city';
	const P__DESTINATION__COUNTRY_ID = 'dest_country_id';
	const P__DESTINATION__POSTAL_CODE = 'dest_postcode';
	const P__DESTINATION__REGION_ID = 'dest_region_id';
	const P__ORIGIN__CITY = 'city';
	const P__ORIGIN__COUNTRY_ID = 'country_id';
	const P__ORIGIN__POSTAL_CODE = 'postcode';
	const P__ORIGIN__REGION_ID = 'region_id';
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Shipping_Model_Rate_Request
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}