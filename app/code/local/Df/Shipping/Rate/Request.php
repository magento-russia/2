<?php
use Df\Shipping\Exception\MethodNotApplicable as EMethodNotApplicable;
class Df_Shipping_Rate_Request extends Mage_Shipping_Model_Rate_Request {
	/**
	 * @param string $countryIso2Code
	 * @return Df_Shipping_Rate_Request
	 */
	public function checkCountryOriginIs($countryIso2Code) {
		df_param_iso2($countryIso2Code, 0);
		if ($countryIso2Code !== $this->getOriginCountryId()) {
			$this->throwException(
				'Доставка {название службы и способа доставки в творительном падеже}'
				. ' возможна только только <b>%s</b>.'
				, df_country($countryIso2Code)->getNameInFormOrigin()
			);
		}
		return $this;
	}

	/**
	 * @return void
	 * @throws EMethodNotApplicable
	 */
	public function checkPostalCodeDestinationIsRussian() {
		$this->checkPostalCodeIsRussian(
			$this->getDestinationPostalCode()
			, 'Укажите почтовый индекс адреса доставки.'
			, 'Система не понимает указанный Вами почтовый индекс адреса доставки: «%s».'
		);
	}

	/**
	 * @return void
	 * @throws EMethodNotApplicable
	 */
	public function checkPostalCodeOriginIsRussian() {
		$this->checkPostalCodeIsRussian(
			$this->getOriginPostalCode()
			, 'Администратор магазина должен указать почтовый индекс склада магазина.'
			, 'Система не понимает указанный администратором почтовый индекс склада магазина: «%s».'
		);
	}

	/**
	 * @param string $message
	 * @return string
	 */
	public function evaluateMessage($message) {
		return $this->getCarrier()->evaluateMessage($message, $this->getMessageVariables());
	}

	/** @return Df_Shipping_Carrier */
	public function getCarrier() {return $this->_carrier;}

	/** @return float */
	public function declaredValueBase() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				$this->getPackageValue()
				* $this->getCarrier()->configA()->getDeclaredValuePercent()
				/ 100.0
			;
		}
		return $this->{__METHOD__};
	}
	
	/** @return float */
	public function getDeclaredValueInHryvnias() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_currency_h()->convertFromBaseToHryvnias($this->declaredValueBase());
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	public function getDeclaredValueInRoubles() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_currency_h()->convertFromBaseToRoubles($this->declaredValueBase());
		}
		return $this->{__METHOD__};
	}
	
	/** @return float */
	public function getDeclaredValueInTenge() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_currency_h()->convertFromBaseToTenge($this->declaredValueBase());
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
			if (in_array(
				$this->getDestinationRegionName()
				,df_h()->directory()->country()->russia()->getFederalCities()
			)) {
				$result = $this->getDestinationRegionName();
			}
		}
		if (!is_null($result)) {
			df_result_string($result);
		}
		return $result;
	}
	
	/** @return Df_Localization_Morpher_Response|null */
	public function getDestinationCityMorpher() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set(
				!$this->getDestinationCity()
				? null
				: Df_Localization_Morpher::s()->getResponseSilent($this->getDestinationCity())
			);
		}
		return df_n_get($this->{__METHOD__});
	}

	/** @return Df_Directory_Model_Country|null */
	public function getDestinationCountry() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set(
				!$this->getDestinationCountryId()
				? null
				: Df_Directory_Model_Country::ld($this->getDestinationCountryId())
			);
		}
		return df_n_get($this->{__METHOD__});
	}

	/**
	 * Возвращает 2-буквенный код страны, куда должна осуществляться доставка,
	 * по стандарту ISO 3166-1 alpha-2.
	 * https://ru.wikipedia.org/wiki/ISO_3166-1
	 * @return string|null
	 */
	public function getDestinationCountryId() {
		/** @var string|null $result */
		$result = $this->_getData(self::P__DESTINATION__COUNTRY_ID);
		if (!is_null($result)) {
			df_result_iso2($result);
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
			$this->{__METHOD__} = df_n_set(
				df_h()->directory()->getRegions()->getItemById(
					$this->getDestinationRegionId()
				)
			);
		}
		return df_n_get($this->{__METHOD__});
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
			$this->{__METHOD__} = df_length()->inMetres(max($this->getDimensionsRough()));
		}
		return $this->{__METHOD__};
	}
	
	/** @return float */
	public function getDimensionMinRoughInMetres() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_length()->inMetres(min($this->getDimensionsRough()));
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
						max(dfa($result, $dimensionName, 0), $productDimension)
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
			$this->{__METHOD__} = df_length()->inMetres($this->getDimensionsRough());
		}
		return $this->{__METHOD__};
	}
	
	/** @return float */
	public function getDimensionsSumRoughInMetres() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_length()->inMetres(array_sum($this->getDimensionsRough()));
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	public function getHeightRough() {
		return dfa($this->getDimensionsRough(), Df_Catalog_Model_Product::P__HEIGHT);
	}
	
	/** @return float */
	public function getHeightRoughInCentimeters() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_length()->inCentimetres($this->getHeightRough());
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	public function getLengthRough() {
		return dfa($this->getDimensionsRough(), Df_Catalog_Model_Product::P__LENGTH);
	}
	
	/** @return float */
	public function getLengthRoughInCentimeters() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_length()->inCentimetres($this->getLengthRough());
		}
		return $this->{__METHOD__};
	}

	/** @return array(string => string) */
	public function getMessageVariables() {
		if (!isset($this->{__METHOD__})) { $this->{__METHOD__} = array(
			'{phone}' => df_cfg()->base()->getStorePhone($this->getStoreId())
			,'{телефон магазина}' => df_cfg()->base()->getStorePhone($this->getStoreId())
			,'{в месте доставки}' =>
				!$this->getDestinationCityMorpher()
				? sprintf('в населённом пункте «%s»', $this->getDestinationCity())
				: $this->getDestinationCityMorpher()->getWhere()
			,'{в место доставки}' => $this->вМесто()
			,'{из места отправки}' => $this->изМеста()
			,'{в месте отправки}' =>
				!$this->getOriginCityMorpher()
				? sprintf('в населённом пункте «%s»', $this->getOriginCity())
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
	
	/** @return Df_Localization_Morpher_Response|null */
	public function getOriginCityMorpher() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set(
				!$this->getOriginCity()
				? null
				: Df_Localization_Morpher::s()->getResponseSilent($this->getOriginCity())
			);
		}
		return df_n_get($this->{__METHOD__});
	}
	
	/** @return Df_Directory_Model_Country|null */
	public function getOriginCountry() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set(
				!$this->getOriginCountryId()
				? null
				: Df_Directory_Model_Country::ld($this->getOriginCountryId())
			);
		}
		return df_n_get($this->{__METHOD__});
	}

	/**
	 * Возвращает 2-буквенный код страны, из которой осуществляется доставка
	 * (страны склада интернет-магазина), по стандарту ISO 3166-1 alpha-2.
	 * https://ru.wikipedia.org/wiki/ISO_3166-1
	 * @return string
	 */
	public function getOriginCountryId() {return $this->_getData(self::P__ORIGIN__COUNTRY_ID);}

	/** @return string|null */
	public function getOriginPostalCode() {return $this->_getData(self::P__ORIGIN__POSTAL_CODE);}
	
	/** @return Df_Directory_Model_Region|null */
	public function getOriginRegion() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_n_set(
				!$this->getOriginRegionId()
				? null
				: df_h()->directory()->getRegions()->getItemById($this->getOriginRegionId())
			);
		}
		return df_n_get($this->{__METHOD__});
	}

	/** @return string */
	public function getOriginRegionalCenter() {
		return df_nts(
			is_null($this->getOriginRegion())
			? null
			: $this->getOriginRegion()->getData('df_capital')
		);
	}

	/**
	 * @param bool $throwExceptionIfNotFound [optional]
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
					общие настройки»→ «Расположение магазина» → «Область».'
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
				. ' общие настройки»→ «Расположение магазина» → «Область».'
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
				/**
				 * При включенном режиме денормализации
				 * габариты не будут загружены в коллекцию,
				 * если у свойств габаритов не включена опция
				 * «used_in_product_listing»
				 * (а до версии 2.16.2 Российской сборки эта опция была выключена).
				 * Версия 2.16.2 устраняет проблему.
				 * @see Df_Shipping_Setup_2_16_2
				 */
				->addAttributeToSelect(array(
					Df_Catalog_Model_Product::P__WIDTH
					,Df_Catalog_Model_Product::P__HEIGHT
					,Df_Catalog_Model_Product::P__LENGTH
					// 2013-10-05
					// Важно! Вес используется модулями доставки
					,Df_Catalog_Model_Product::P__WEIGHT
				))
			;
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	public function getQty() {
		if (!isset($this->{__METHOD__})) {
			/** @uses Mage_Sales_Model_Quote_Item::getQty() */
			$this->{__METHOD__} = array_sum(df_int_simple(df_each($this->getQuoteItemsSimple(), 'getQty')));
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
	 * http://stackoverflow.com/a/8301170
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
			$this->{__METHOD__} = array_product(array(
				$this->getLengthRoughInCentimeters()
				, $this->getWidthRoughInCentimeters()
				, $this->getHeightRoughInCentimeters()
			));
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
				$productVolume = array_product(df_length()->inMetres(
					$product->getWidth(), $product->getHeight(), $product->getLength()
				));
				$result += ($qty * $productVolume);
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	public function getWeightInGrammes() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_weight()->inGrammes($this->getPackageWeight());
		}
		return $this->{__METHOD__};
	}

	/** @return float */
	public function getWeightInKilogrammes() {return $this->getWeightInGrammes() / 1000;}

	/**
	 * @used-by Df_Shipping_Collector::errorInvalidWeight()
	 * @return string
	 */
	public function getWeightKgSD() {return df_f2i($this->getWeightInKilogrammes(), 1);}

	/** @return float */
	public function getWidthRough() {
		return dfa($this->getDimensionsRough(), Df_Catalog_Model_Product::P__WIDTH);
	}
	
	/** @return float */
	public function getWidthRoughInCentimeters() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_length()->inCentimetres($this->getWidthRough());
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function isDestinationMoscow() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->isMoscow(array(
				$this->getDestinationCity(), $this->getDestinationRegionName()
			));
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
				df_t()->areEqualCI($this->getDestinationCity(), $this->getDestinationRegionalCenter())
		;
	}

	/** @return bool */
	public function isDomestic() {
		return $this->getDestinationCountryId() === $this->getOriginCountryId();
	}

	/** @return bool */
	public function isOriginMoscow() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = $this->isMoscow(array(
				$this->getOriginCity(), $this->getOriginRegionName()
			));
		}
		return $this->{__METHOD__};
	}

	/** @return bool */
	public function isOriginCityRegionalCenter() {
		return
				$this->getOriginCity()
			&&
				df_t()->areEqualCI($this->getOriginCity(), $this->getOriginRegionalCenter())
		;
	}

	/** @return bool */
	public function isOriginRussia() {
		return Df_Directory_Helper_Country::ISO_2_CODE__RUSSIA === $this->getOriginCountryId();
	}

	/** @return bool */
	public function isOriginTheSameAsDestination() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = df_t()->areEqualCI($this->getDestinationCity(), $this->getOriginCity());
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string $message
	 * @return void
	 * @throws EMethodNotApplicable
	 */
	public function throwException($message) {
		df_error(new EMethodNotApplicable(
			$this->getCarrier(), $this->evaluateMessage(df_format(func_get_args()))
		));
	}

	/**
	 * @throws EMethodNotApplicable
	 * @return void
	 */
	public function throwExceptionInvalidDestination() {
		$this->throwException(
			'Доставка <b>{в место доставки}</b>'
			.' {название службы и способа доставки в творительном падеже} невозможна.'
		);
	}

	/**
	 * @throws EMethodNotApplicable
	 * @return void
	 */
	public function throwExceptionInvalidOrigin() {
		$this->throwException(
			'Доставка <b>{из места отправки}</b>'
			.' {название службы и способа доставки в творительном падеже} невозможна.'
		);
	}

	/**
	 * @used-by getMessageVariables()
	 * @used-by Df_Shipping_Collector_Conditional_WithForeign::errorInvalidCityDest()
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
	 * @used-by Df_Shipping_Collector_Conditional_WithForeign::errorInvalidCountryDest()
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
	 * @used-by Df_Shipping_Collector_Conditional_WithForeign::errorInvalidCityOrig()
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

	/**
	 * @used-by checkPostalCodeDestinationIsRussian()
	 * @used-by checkPostalCodeOriginIsRussian()
	 * @param string $value
	 * @param string $messageForEmpty
	 * @param string $messageForInvalid
	 * @return void
	 * @throws EMethodNotApplicable
	 */
	private function checkPostalCodeIsRussian($value, $messageForEmpty, $messageForInvalid) {
		if (!$value) {
			$this->throwException($messageForEmpty);
		}
		/** @var Zend_Validate_PostCode $validator */
		$validator = new Zend_Validate_PostCode(array('locale' => 'ru_RU'));
		if (!$validator->isValid($value)) {
			$this->throwException($messageForInvalid, $value);
		}
	}

	/** @return int|float[] */
	private function getDimensionDefaultValues() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Shipping_Settings_Product $settings */
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
				$result[]= (int)$quoteItem->getProduct()->getId();
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @param string[] $variants
	 * @return bool
	 */
	private function isMoscow(array $variants) {return in_array('МОСКВА', df_strtoupper($variants));}

	/**
	 * @used-by Df_Autotrading_Model_Api_Calculator::_construct()
	 * @used-by Df_Exline_Model_Locator::_construct()
	 * @used-by Df_Exline_Model_Request_Rate::_construct()
	 * @used-by Df_Kazpost_Model_Request_Rate::_construct()
	 * @used-by Df_KazpostEms_Model_Request_Rate::_construct()
	 * @used-by Df_Pec_Model_Api_Calculator::_construct()
	 * @used-by Df_PonyExpress_Model_Request_Rate::_construct()
	 * @used-by Df_Shipping_Model_Locator::_construct()
	 * @used-by Df_Spsr_Model_Api_Calculator::_construct()
	 */
	const _C = __CLASS__;
	const P__DESTINATION__CITY = 'dest_city';
	const P__DESTINATION__COUNTRY_ID = 'dest_country_id';
	const P__DESTINATION__POSTAL_CODE = 'dest_postcode';
	const P__DESTINATION__REGION_ID = 'dest_region_id';
	const P__ORIGIN__CITY = 'city';
	const P__ORIGIN__COUNTRY_ID = 'country_id';
	const P__ORIGIN__POSTAL_CODE = 'postcode';
	const P__ORIGIN__REGION_ID = 'region_id';

	/**
	 * @used-by getCarrier()
	 * @used-by i()
	 * @var Df_Shipping_Carrier
	 */
	private $_carrier;

	/**
	 * @static
	 * @param Df_Shipping_Carrier $carrier
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Shipping_Rate_Request
	 */
	public static function i(Df_Shipping_Carrier $carrier, array $parameters = array()) {
		$result = new self($parameters);
		$result->_carrier = $carrier;
		return $result;
	}
}