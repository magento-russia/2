<?php
/**
 * @method bool getIsDuplicate()
 * @method int|null getTaxClassId()
 * @method Df_Catalog_Model_Resource_Product getResource()
 */
class Df_Catalog_Model_Product extends Mage_Catalog_Model_Product {
	/** @return Df_Catalog_Model_Product */
	public function deleteImages() {
		/** @var Mage_Eav_Model_Entity_Attribute_Abstract[] $attributes */
		$attributes = $this->getTypeInstance()->getSetAttributes();
		df_assert_array($attributes);
		/** @var Mage_Eav_Model_Entity_Attribute_Abstract|null $mediaGalleryAttribute */
		$mediaGalleryAttribute = df_a($attributes, self::P__MEDIA_GALLERY);
		if (!is_null($mediaGalleryAttribute)) {
			df_assert($mediaGalleryAttribute instanceof Mage_Eav_Model_Entity_Attribute_Abstract);
			if (is_array($this->getMediaGallery())) {
				$this->getMediaGalleryImages();
				/** @var array|null $images */
				$images = df_a($this->getMediaGallery(), 'images');
				if (is_array($images)) {
					/** @var Mage_Catalog_Model_Product_Attribute_Backend_Media $backend */
					$backend = $mediaGalleryAttribute->getBackend();
					df_assert($backend instanceof Mage_Catalog_Model_Product_Attribute_Backend_Media);
					foreach ($images as $image){
						/** @var string|null $fileName */
						$fileName = df_a($image, 'file');
						if ($backend->getImage($this, $fileName)) {
							$backend->removeImage($this, $fileName);
						}
					}
				}
			}
		}
		return $this;
	}

	/** @return Df_Catalog_Model_Product */
	public function deleteOptions() {
		df_assert_array($this->getOptions());
		foreach ($this->getOptions() as $option) {
			/** @var Df_Catalog_Model_Product_Option $option */
			$option->deleteWithDependencies();
		}
		return $this;
	}

	/**
	 * @param bool $isMassUpdate [optional]
	 * @return Df_Catalog_Model_Product
	 * @throws Exception
	 */
	public function deleteRm($isMassUpdate = false) {
		/** @var bool $isMassupdatePrev */
		$isMassupdatePrev = $this->getIsMassupdate();
		/** @var bool $excludeUrlRewritePrev */
		$excludeUrlRewritePrev = $this->getExcludeUrlRewrite();
		$this->setIsMassupdate($isMassUpdate);
		$this->setExcludeUrlRewrite($isMassUpdate);
		rm_admin_begin();
		try {
			$this->delete();
		}
		catch(Exception $e) {
			rm_admin_end();
			$this->setIsMassupdate($isMassupdatePrev);
			$this->setExcludeUrlRewrite($excludeUrlRewritePrev);
			throw $e;
		}
		rm_admin_end();
		$this->setIsMassupdate($isMassupdatePrev);
		$this->setExcludeUrlRewrite($excludeUrlRewritePrev);
		return $this;
	}

	/**
	 * Возвращает этот же товар, но в другом, указанном магазине
	 * @param int $storeId
	 * @return Df_Catalog_Model_Product
	 */
	public function forStore($storeId) {
		rm_admin_begin();
		/**
		 * На случай, если сторонним модулям
		 * потребуется перекрыть класс Df_Catalog_Model_Product наследником
		 */
		$class = get_class($this);
		/** @var Df_Catalog_Model_Product $result */
		$result = new $class();
		$result->setStoreId($storeId);
		$result = df_load($result, $this->getId());
		rm_admin_end();
		return $result;
	}

	/** @return int */
	public function getAttributeSetId() {
		/**
		 * Нельзя использовать @rm_nat,
		 * @link http://magento-forum.ru/topic/4377/
		 * 2014-07-27:
		 * Заменил @see rm_nat0 на @see intval ради ускорения.
		 */
		return intval($this->_getData(self::P__ATTRIBUTE_SET_ID));
	}

	/**
	 * Перекрываем родительский метод пока только ради модуля Яндекс.Маркет.
	 * Модулю Яндекс.Маркет нужно, чтобы адреса товарных страниц включали товарный раздел.
	 * Этого можно было бы достичь посредством
	 * Mage::register('current_category', $category)
	 * однако этот способ неэффективен по производительности:
	 * модулю Яндекс.Маркет изначально известны только идентификаторы
	 * привязанных к товару разделов, а загрузка этих товарных разделов
	 * только ради метода Mage_Catalog_Model_Product::getCategoryId()
	 * (где все равно используется только идентификатор) приведет к торможению системы.
	 * Именно поэтому перекрываем метод Mage_Catalog_Model_Product::getCategoryId() своим.
	 * @override
	 * @return int|bool
	 */
	public function getCategoryId() {
		/** @var int|bool $result */
		$result = parent::getCategoryId();
		if (false === $result) {
			$result = $this->_getData(self::P__RM_CATEGORY_ID);
			if (!$result) {
				$result = false;
			}
		}
		return $result;
	}

	/** @return float */
	public function getCompositeFinalPriceWithTax() {
		if (!isset($this->{__METHOD__})) {
			/** @var float $result */
			$result = null;
			switch ($this->getTypeId()) {
				case Mage_Catalog_Model_Product_Type::TYPE_SIMPLE:
				case Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL:
					/** @var float $priceWithoutTax */
					$priceWithoutTax = $this->getPriceModel()->getFinalPrice($qty = 1, $product = $this);
					$result =
						df_mage()->taxHelper()->getPrice(
							$product = $this
							,$price =  $priceWithoutTax
							,$includingTax = true
							,$shippingAddress = null
							,$billingAddress = null
							,$ctc = null
							,$store = $product->getStore()
							,$priceIncludesTax = false
						)
					;
					break;
				case Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE:
					$result =
						df_mage()->taxHelper()->getPrice(
							$product = $this
							,$price = $this->getMinimalPrice()
							,$includingTax = true
							,$shippingAddress = null
							,$billingAddress = null
							,$ctc = null
							,$store = $product->getStore()
							,$priceIncludesTax = null
						)
					;
					/**
					 * Обратите внимание, что $result будет равно null,
					 * если товар отсутствует на складе
					 * @see Df_Catalog_Model_Product::getMinimalPrice
					 */
					if (is_null($result)) {
						$result = 0.0;
					}
					df_assert_float($result);
					break;
				case Mage_Catalog_Model_Product_Type::TYPE_GROUPED:
					/** @var Mage_Catalog_Model_Product_Type_Grouped $typeGrouped */
					$typeGrouped = $this->getTypeInstance($singleton = false);
					$typeGrouped->setStoreFilter($store = $this->getStore(), $product = $this);
					/** @var Df_Catalog_Model_Product[] $associatedProducts */
					$associatedProducts = $typeGrouped->getAssociatedProducts($product);
					foreach ($associatedProducts as $associatedProduct) {
						/** @var Df_Catalog_Model_Product $associatedProduct */
						/** @var float $currentPrice */
						$currentPrice = $associatedProduct->getCompositeFinalPriceWithTax();
						$result =
							is_null($result)
							? $currentPrice
							: min($result, $currentPrice)
						;
					}
					if (is_null($result)) {
						$result = 0.0;
					}
					break;
				case Mage_Catalog_Model_Product_Type::TYPE_BUNDLE:
					/** @var Mage_Bundle_Model_Product_Price $priceModel */
					$priceModel = $this->getPriceModel();
					/**
					 * Метод @see Mage_Bundle_Model_Product_Price::getTotalPrices()
					 * отсутствует в Magento CE 1.4.0.1
					 */
					/** @var bool $hasMethod_getTotalPrices */
					static $hasMethod_getTotalPrices;
					if (!isset($hasMethod_getTotalPrices)) {
						$hasMethod_getTotalPrices = is_callable(array($priceModel, 'getTotalPrices'));
					}
					$result =
						$hasMethod_getTotalPrices
						?
							$priceModel->getTotalPrices(
								$this
								,$which = 'min'
								,$includeTax = true
								,$takeTierPrice = false
							)
						:
							df_mage()->taxHelper()->getPrice(
								$this
								, $priceModel->getPrices($this, $which = 'min'), $includingTax = true
							)
					;
					break;
				default:
					/**
					 * Оказывается, что некоторые сторонние модули добавляют свои системные типы товаров.
					 * Например, в магазине all4gift.ru
					 * модуль Magestore Gift Card добавил системный тип товара «giftvoucher»:
					 * @link http://magento-forum.ru/topic/4302/
					 * На самом деле, конкретно для модуля Яндекс.Маркет самым правильным
					 * (и реализованным теперь) решением
					 * стала просто отбраковка нестандартных системных типов товаров:
					 * @see Df_YandexMarket_Model_Yml_Processor_Offer::isEnabled()
					 */
					if (df_is_it_my_local_pc()) {
						Mage::log(rm_sprintf('Неизвестный тип товара: «%s».', $this->getTypeId()));
					}
					/** @var float $priceWithoutTax */
					$priceWithoutTax = $this->getPriceModel()->getFinalPrice($qty = 1, $product = $this);
					$result =
						df_mage()->taxHelper()->getPrice(
							$product = $this
							,$price =  $priceWithoutTax
							,$includingTax = true
							,$shippingAddress = null
							,$billingAddress = null
							,$ctc = null
							,$store = $product->getStore()
							,$priceIncludesTax = false
						)
					;
					break;
			}
			df_result_float($result);
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return int[] */
	public function getConfigurableParentIds() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				Df_Catalog_Model_Product_Type_Configurable::s()
					->getParentIdsByChild($this->getId())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getDescription() {return df_nts($this->_getData(self::P__DESCRIPTION));}

	/** @return bool */
	public function getExcludeUrlRewrite() {
		return rm_bool($this->_getData(self::P__EXCLUDE_URL_REWRITE));
	}

	/** @return string|null */
	public function getExternalId() {return $this->_getData(Df_Eav_Const::ENTITY_EXTERNAL_ID);}

	/**
	 * Обратите внимание, что этот метод надлежит использовать только для простых товаров!
	 * Для настраиваемых товаров значения свойств «длина», «ширина», «высота»
	 * могу отсутствовать!
	 * @return float
	 */
	public function getHeight() {
		/** @var float $result */
		$result = rm_float(parent::_getData(self::P__HEIGHT));
		return $result ? $result : df_cfg()->shipping()->product()->getDefaultHeight();
	}

	/**
	 * @override
	 * @return int|null
	 */
	public function getId() {
		// Этот метод реализован именно в таком виде
		// ради ускорения работы системы
		/** @var int|null $id */
		$id = @$this->_data[self::P__ID];
		return !$id ? null : intval($id);
	}

	/** @return bool */
	public function getIsMassupdate() {return rm_bool($this->_getData(self::P__IS_MASSUPDATE));}

	/**
	 * Обратите внимание, что этот метод надлежит использовать только для простых товаров!
	 * Для настраиваемых товаров значения свойств «длина», «ширина», «высота»
	 * могу отсутствовать!
	 * @return float
	 */
	public function getLength() {
		/** @var float $result */
		$result = parent::_getData(self::P__LENGTH);
		if (is_null($result) || (0.0 === rm_float($result))) {
			$result = df_cfg()->shipping()->product()->getDefaultLength();
		}
		$result = rm_float($result);
		return $result;
	}

	/**
	 * Обратите внимание, что стандартный программный код иногда использует синтаксис:
	 * $this->getMediaGallery('images')
	 * Наш метод тоже поддерживает этот синтаксис.
	 * @param string|null $key[optional]
	 * @return mixed[]|null
	 */
	public function getMediaGallery($key = null) {
		/** @var mixed[]|null $result */
		$result = null;
		/** @var mixed[]|null $mediaGallery */
		$mediaGallery = $this->_getData(self::P__MEDIA_GALLERY);
		if (!is_null($mediaGallery)) {
			df_assert_array($mediaGallery);
			$result =
				is_null($key)
				? $mediaGallery
				: df_a($mediaGallery, $key)
			;
		}
		if (!is_null($result)) {
			df_result_array($result);
		}
		return $result;
	}

	/**
	 * На витринной странице товара
	 * этот метод вызывается 6 раз для одного и того же товара.
	 * @override
	 * @return string
	 */
	public function getMetaTitle() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df_enabled(Df_Core_Feature::SEO)
				? $this->getMetaTitleDf()
				: parent::_getData(self::P__META_TITLE)
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * Обратите внимание, что метод вернёт null,
	 * если товар отсутствует на складе
	 * @override
	 * @return float|null
	 */
	public function getMinimalPrice() {
		/** @var float|null $result */
		$result = parent::getMinimalPrice();
		if (is_null($result)) {
			/**
			 * @see Mage_Catalog_Model_Product::getMinimalPrice()
			 * вернёт непустое значение только в том случае,
			 * если товар был загружен не в одиночку, а коллекцией,
			 * и для коллекции перед загрузкой был вызван метод
			 * @see Mage_Catalog_Model_Resource_Product_Collection::addMinimalPrice()
			 * или @see Mage_Catalog_Model_Resource_Product_Collection::addPriceData().
			 * В противном случае значение свойства minimal_price надо загрузить вручную.
			 */
			/** @var Zend_Db_Select $select */
			$select =
				rm_select()
					->from(array('maintable' => rm_table('catalog/product_index_price')))
					->where('(? = maintable.entity_id)', $this->getId())
					->where('(? = maintable.website_id)', $this->getStore()->getWebsiteId())
					->where('(? = maintable.customer_group_id)', 0)
					->where('(? = maintable.tax_class_id)', 0)
			;
			/** @var Zend_Db_Statement_Pdo $query */
			$query = rm_conn()->query($select);
			/** @var array(string => string) $row */
			$row = $query->fetch($style = Zend_Db::FETCH_ASSOC);
			if (is_array($row)) {
				$result = df_a($row, 'min_price');
				if (!is_null($result)) {
					$result = rm_float($result);
				}
				$this->setData('minimal_price', $result);
			}
		}
		/**
		 * Обратите внимание, что метод вернёт null,
		 * если товар отсутствует на складе
		 */
		return $result;
	}

	/** @return string */
	public function getName() {return df_nts($this->_getData(self::P__NAME));}

	/**
	 * @param string $title
	 * @return array
	 */
	public function getOptionsByTitle($title) {
		df_param_string_not_empty($title, 0);
		$result = array();
		foreach ($this->getOptions() as $option) {
			/** @var Df_Catalog_Model_Product_Option $option */
			if ($title === $option->getDataUsingMethod(Df_Catalog_Model_Product_Option::P__TITLE)) {
				$result[]= $option;
			}
		}
		return $result;
	}

	/** @return string */
	public function getTypeName() {return self::getTypeNameById($this->getTypeId());}

	/** @return string|null */
	public function getUrlKey() {$this->getData(self::P__URL_KEY);}

	/**
	 * @override
	 * @return float
	 */
	public function getWeight() {
		/**
		 * Раньше тут стояло parent::_getData(self::P__WEIGHT), что в корне неправильно,
		 * потому что в родительском классе Mage_Catalog_Model_Product
		 * метод getWeight присутствует (@see Mage_Catalog_Model_Product::getWeight),
		 * и он работает как
		 * $this->getTypeInstance(true)->getWeight($this)
		 *
		 * Тупое parent::_getData(self::P__WEIGHT) не работает (возвращает пустое значение)
		 * для сложных типов товаров (например, для настраиваемого).
		 *
		 * Дефект было допущен 15 марта 2013 года (версия 2.17.3)
		 * и замечен мной только 21 сентября 2013 года.
		 * Что интересно, никто из клиентов за эти 6 месяцев дефекта не заметил.
		 */
		/** @var float $result */
		$result = parent::getWeight();
		if ((is_null($result) || (0.0 === rm_float($result)))) {
			/**
			 * Обратите внимание, что для некоторых типов товаров нормально не иметь вес.
			 * Например, для виртуальных и скачиваемых товаров.
			 * Думаю, что перезагружать товар для уточнения веса
			 * имеет смысл вообще только для простых товаров.
			 */
			if (Mage_Catalog_Model_Product_Type::TYPE_SIMPLE !== $this->getTypeId()) {
				// $result может быть равно NULL.
				// Явно устанавливаем значение 0.0,
				// чтобы получить результат вещественного типа.
				$result = 0.0;
			}
			else {
				// Видимо, товар был взят из коллекции,
				// в которую не было добавлено свойство «вес».
				/** @vare bool $inRecursion */
				static $inRecursion = false;
				if (!$inRecursion) {
					$inRecursion = true;
					/**
					 * Раньше тут стояло:
					 * $result = $this->reload()->getWeight();
					 * Этот код является неправильным!
					 * Метод @see Df_Catalog_Model_Product::reload() вообще очень опасен:
					 * он ведёт к утрате динамических свойств товара
					 * (тех свойств, которые не загружаются из БД).
					 *
					 * В настоящее время разумное применение метода
					 * @see Df_Catalog_Model_Product::reload()
					 * ограничивается обменом данными с внешними системами
					 * (1С: Управление торговлей и Magento Dataflow)
					 */
					$result = self::ld($this->getId(), $this->getStoreId())->getWeight();
					$inRecursion = false;
				}
				if ((is_null($result) || (0.0 === rm_float($result)))) {
					$result = df_cfg()->shipping()->product()->getDefaultWeight();
				}
				if (false === strpos(Mage::app()->getRequest()->getRequestUri(), 'sales/order/reorder')) {
					/**
					 * Для страницы перезаказа отсутствие веса в коллекции — это нормально
					 */
					/*df_notify_me(
						rm_sprintf(
							'У товара «%s» отсутствует вес на странице %s'
							,$this->getName()
							,Mage::app()->getRequest()->getRequestUri()
						)
					);
					df_bt();     */
				}
			}
		}
		return rm_float($result);
	}

	/**
	 * Обратите внимание, что этот метод надлежит использовать только для простых товаров!
	 * Для настраиваемых товаров значения свойств «длина», «ширина», «высота»
	 * могу отсутствовать!
	 * @return float
	 */
	public function getWidth() {
		/** @var float $result */
		$result = rm_float(parent::_getData(self::P__WIDTH));
		return $result ? $result : df_cfg()->shipping()->product()->getDefaultWidth();
	}

	/** @return bool */
	public function isConfigurableChild() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = !!$this->getConfigurableParentIds();
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Catalog_Model_Product */
	public function log() {
		/** @var array(string => string|int|float) $dataToLog */
		$dataToLog = array();
		foreach($this->getData() as $key => $value) {
			/** @var string $key */
			/** @var mixed $value */
			if (!is_object($value) && !is_array($value)) {
				$dataToLog[$key] = $value;
			}
		}
		Mage::log($dataToLog);
		return $this;
	}

	/** @return Df_Catalog_Model_Product */
	public function reindexPrices() {
		/** @var Mage_Catalog_Model_Resource_Product_Indexer_Price $indexer */
		$indexer = Mage::getResourceSingleton('catalog/product_indexer_price');
		$indexer->reindexProductIds($this->getId());
		return $this;
	}

	/** @return Df_Catalog_Model_Product */
	public function reindexStockStatus() {
		/** @var Mage_CatalogInventory_Model_Resource_Indexer_Stock $indexer */
		$indexer = Mage::getResourceSingleton('cataloginventory/indexer_stock');
		$indexer->reindexProducts(array($this->getId()));
		return $this;
	}

	/** @return Df_Catalog_Model_Product */
	public function reindexUrlRewrites() {
		df_mage()->catalog()->urlSingleton()->refreshProductRewrite($this->getId());
		return $this;
	}

	/**
	 * Обратите внимание, что этот метод очень опасен:
	 * он ведёт к утрате динамических свойств товара
	 * (тех свойств, которые не загружаются из БД).
	 *
	 * В настоящее время разумное применение метода
	 * @see Df_Catalog_Model_Product::reload()
	 * ограничивается обменом данными с внешними системами
	 * (1С: Управление торговлей и Magento Dataflow).
	 *
	 * @return Df_Catalog_Model_Product
	 */
	public function reload() {
		/** @var int|null $id */
		$id = $this->getId();
		/** @var int|null $storeId */
		$storeId = $this->getStoreId();
		$this->reset();
		$this->cleanCache();
		$this->setStoreId($storeId);
		$this->load($id);
		return $this;
	}

	/**
	 * @param array $attributeValues
	 * @param int $storeId[optional]
	 * @return Df_Catalog_Helper_Product
	 * @throws Exception
	 */
	public function saveAttributes(array $attributeValues, $storeId = null) {
		rm_nat($this->getId());
		rm_admin_begin();
		try {
			/** @var Mage_Catalog_Model_Product_Action $productAction */
			$productAction = Mage::getSingleton('catalog/product_action');
			if (is_null($storeId)) {
				$storeId = Mage_Core_Model_App::ADMIN_STORE_ID;
			}
			$productAction->updateAttributes(array($this->getId()), $attributeValues, $storeId);
		}
		catch(Exception $e) {
			rm_admin_end();
			throw $e;
		}
		rm_admin_end();
		return $this;
	}

	/**
	 * @param bool $isMassUpdate [optional]
	 * @return Df_Catalog_Model_Product
	 * @throws Exception
	 */
	public function saveRm($isMassUpdate = false) {
		/** @var bool $isMassupdatePrev */
		$isMassupdatePrev = $this->getIsMassupdate();
		/** @var bool $excludeUrlRewritePrev */
		$excludeUrlRewritePrev = $this->getExcludeUrlRewrite();
		$this->setIsMassupdate($isMassUpdate);
		$this->setExcludeUrlRewrite($isMassUpdate);
		rm_admin_begin();
		/**
		 * Эта странная заплатка устраняет дефект Magento CE/EE:
		 * без неё в результате сохранения товара при незаполненности свойства 'tier_price'
		 * прежние особые цены (tier prices) утрачиваются.
		 * @link http://stackoverflow.com/a/8543548/1164342
		 */
		/** @var bool $tierPriceIsNull */
		$tierPriceIsNull = is_null($this->getTierPrice());
		if ($tierPriceIsNull) {
			$this->setTierPrice(array('website_id' => 0));
		}
		try {
			$this->save();
		}
		catch(Exception $e) {
			rm_admin_end();
			$this->setIsMassupdate($isMassupdatePrev);
			$this->setExcludeUrlRewrite($excludeUrlRewritePrev);
			if ($tierPriceIsNull) {
				$this->setTierPrice(null);
			}
			throw $e;
		}
		rm_admin_end();
		$this->setIsMassupdate($isMassupdatePrev);
		$this->setExcludeUrlRewrite($excludeUrlRewritePrev);
		if ($tierPriceIsNull) {
			$this->setTierPrice(null);
		}
		return $this;
	}

	/**
	 * @param int $value
	 * @return Df_Catalog_Model_Product
	 */
	public function setAttributeSetId($value) {
		df_param_integer($value, 0);
		$this->setData(self::P__ATTRIBUTE_SET_ID, $value);
		return $this;
	}

	/**
	 * @param bool $value
	 * @return Df_Catalog_Model_Product
	 */
	public function setExcludeUrlRewrite($value) {
		$this->setData(self::P__EXCLUDE_URL_REWRITE, rm_bool($value));
		return $this;
	}

	/**
	 * @param bool $value
	 * @return Df_Catalog_Model_Product
	 */
	public function setIsMassupdate($value) {
		$this->setData(self::P__IS_MASSUPDATE, rm_bool($value));
		return $this;
	}

	/**
	 * Обратите внимание, что ядро Magento иногда для обратной совместимости
	 * пихает в качестве параметра объект класса Varien_Object,
	 * а не класса Mage_CatalogInventory_Model_Stock_Item:
	 * @see Mage_CatalogInventory_Model_Stock_Status::addStockStatusToProducts():
		foreach ($productCollection as $product) {
			 $object = new Varien_Object(array('is_in_stock' => $product->getData('is_salable')));
			 $product->setStockItem($object);
		 }
	 * @param Mage_CatalogInventory_Model_Stock_Item|Varien_Object $stockItem
	 * @return Df_Catalog_Model_Product
	 */
	public function setStockItem(Varien_Object $stockItem) {
		$this->setData(self::P__STOCK_ITEM, $stockItem);
		return $this;
	}

	/**
	 * @param int|null $value
	 * @return Df_Catalog_Model_Product
	 */
	public function setStoreId($value) {
		/**
		 * Убрал ради ускорения следующий код:
			if (!is_null($value)) {
				df_param_integer($value, 0);
			}
		 */
		$this->setData(self::P__STORE_ID, $value);
		return $this;
	}

	/**
	 * @param array(string => int|float)|null $value
	 * @return Df_Catalog_Model_Product
	 */
	public function setTierPrice($value) {
		if (is_null($value)) {
			$this->unsetData(self::P__TIER_PRICE);
		}
		else {
			df_param_array($value, 0);
			$this->setData(self::P__TIER_PRICE, $value);
		}
		return $this;
	}

	/**
	 * @param string $value
	 * @return Df_Catalog_Model_Product
	 */
	public function setUrlKey($value) {
		df_param_string($value, 0);
		$this->setData(self::P__URL_KEY, $value);
		return $this;
	}

	/** @return Df_Catalog_Model_Product */
	public function unsetAttributeSetId() {
		$this->unsetData(self::P__ATTRIBUTE_SET_ID);
		return $this;
	}

	/** @return Df_Catalog_Model_Product */
	public function unsetUrlKey() {
		$this->unsetData(self::P__URL_KEY);
		return $this;
	}

	/**
	 * @override
	 * @return Df_Catalog_Model_Product
	 */
	protected function _clearData() {
		parent::_clearData();
		unset($this->{__CLASS__ . '::getCompositeFinalPriceWithTax'});
		return $this;
	}

	/** @return string|null */
	private function getCategoryTail() {
		return !rm_state()->hasCategory() ? null : rm_state()->getCurrentCategory()->getName();
	}

	/** @return boolean */
	private function isCategoryTailEnabled() {
		return df_cfg()->seo()->html()->getAppendCategoryNameToProductTitleTag();
	}

	/** @return string|null */
	private function getCategoryTailIfEnabled() {
		return !$this->isCategoryTailEnabled() ? null : $this->getCategoryTail();
	}

	/** @return string */
	private function getDefaultProductRawMetaTitle() {
		return df_cfg()->seo()->html()->getDefaultPatternForProductTitleTag();
	}

	/** @return string */
	private function getMetaTitleDf() {
		return implode(' - ', df_clean(array(
			$this->processPatterns($this->getRawMetaTitle())
			,$this->getCategoryTailIfEnabled()
		)));
	}

	/** @return string */
	private function getRawMetaTitle() {
		return
			parent::_getData(self::P__META_TITLE)
			? parent::_getData(self::P__META_TITLE)
			: $this->getDefaultProductRawMetaTitle()
		;
	}

	/**
	 * @param string $text
	 * @return string
	 */
	private function processPatterns($text) {
		return Df_Seo_Model_Template_Processor::i(df_nts($text), array('product' => $this))->process();
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		/**
		 * Обязательно, иначе будет сбой при установке.
		 * Ведь наш класс перекрывает системный класс Mage_Catalog_Model_Product,
		 * а системный класс используется при установке ядра,
		 * в то время, когда Российская сборка ещё не инициализирована.
		 */
		Df_Core_Bootstrap::s()->init();
		$this->_init(Df_Catalog_Model_Resource_Product::mf());
	}
	const _CLASS = __CLASS__;
	/**
	 * @see app/code/core/Mage/Catalog/sql/catalog_setup/install-1.6.0.0.php:
		->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(), 'SKU')
	 */
	const MAX_LENGTH__SKU = 64;
	const P__ATTRIBUTE_SET_ID = 'attribute_set_id';
	const P__COUNTRY_OF_MANUFACTURE = 'country_of_manufacture';
	const P__DESCRIPTION = 'description';
	const P__EXCLUDE_URL_REWRITE = 'exclude_url_rewrite';
	const P__HAS_OPTIONS = 'has_options';
	const P__HEIGHT = 'height';
	const P__ID = Mage_Eav_Model_Entity::DEFAULT_ENTITY_ID_FIELD;
	const P__IMAGE = 'image';
	const P__IS_MASSUPDATE = 'is_massupdate';
	const P__IS_SALABLE = 'is_salable';
	const P__LENGTH = 'length';
	const P__MANUFACTURER = 'manufacturer';
	const P__MEDIA_GALLERY = 'media_gallery';
	const P__META_TITLE = 'meta_title';
	const P__NAME = 'name';
	const P__PRICE = 'price';
	const P__RM_CATEGORY_ID = 'rm_category_id';
	const P__SHORT_DESCRIPTION = 'short_description';
	const P__SKU = 'sku';
	const P__SMALL_IMAGE = 'small_image';
	const P__STOCK_ITEM = 'stock_item';
	const P__STORE_ID = 'store_id';
	const P__TAX_CLASS_ID = 'tax_class_id';
	const P__TIER_PRICE = 'tier_price';
	const P__TYPE_ID = 'type_id';
	const P__URL_KEY = 'url_key';
	const P__VISIBILITY = 'visibility';
	const P__WEIGHT = 'weight';
	const P__WIDTH = 'width';

	/** @return Df_Catalog_Model_Resource_Product_Collection */
	public static function c() {return self::s()->getCollection();}

	/**
	 * @param int|Mage_Core_Model_Store|string|null $storeId [optional]
	 * @return Df_Catalog_Model_Product
	 */
	public static function createNew($storeId = null) {
		rm_admin_begin();
		/** @var Df_Catalog_Model_Product $result */
		$result = self::i();
		if (!is_null($storeId)) {
			$result->setStoreId($storeId);
		}
		/** @var Mage_CatalogInventory_Model_Stock_Item $stockItem */
		$stockItem = Mage::getModel('cataloginventory/stock_item');
		$result->setStockItem($stockItem);
		rm_admin_end();
		return $result;
	}

	/**
	 * @static
	 * @return string[]
	 */
	public static function getMediaAttributeNames() {return array('image', 'small_image', 'thumbnail');}
	/**
	 * @param string $typeId
	 * @return string
	 */
	public static function getTypeNameById($typeId) {
		/** @var array(string => string) $map */
		static $map = array(
			Mage_Catalog_Model_Product_Type::TYPE_SIMPLE => 'простой'
			,Mage_Catalog_Model_Product_Type::TYPE_BUNDLE => 'комплект'
			,Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE => 'настраиваемый'
			,Mage_Catalog_Model_Product_Type::TYPE_GROUPED => 'группа'
			,Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL => 'виртуальный'
		);
		return df_a($map, $typeId, 'неизвестный');
	}
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Catalog_Model_Product
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
	/**
	 * @static
	 * В качестве $id можно передавать не только идентификатор, но и артикул.
	 * @param int|string $id
	 * @param int|null $storeId [optional]
	 * @return Df_Catalog_Model_Product
	 */
	public static function ld($id, $storeId = null) {
		rm_admin_begin();
		/** @var Df_Catalog_Model_Product $result */
		$result = self::i();
		if (!is_null($storeId)) {
			$result->setStoreId($storeId);
		}
		/** @var Df_Catalog_Model_Product $result */
		if (!df_check_integer($id)) {
			/**
			 * @see Mage_Catalog_Helper_Product::getProduct().
			 * А $result->loadByAttribute(self::P__SKU, $id) у меня почему-то не работает.
			 */
			/** @var string $sku */
			$sku = $id;
			$id = Df_Catalog_Helper_Product::s()->getIdBySku($sku);
			if (!$id) {
				df_error('Система не нашла в базе данных товар с артикулом «%s».', $sku);
			}
			df_assert_integer($id);
		}
		df_load($result, $id);
		rm_admin_end();
		return $result;
	}
	/**
	 * @see Df_Catalog_Model_Resource_Product_Collection::_construct()
	 * @return string
	 */
	public static function mf() {static $r; return $r ? $r : $r = rm_class_mf(__CLASS__);}
	/** @return void */
	public static function reindexFlat() {
		/**
		 * Константа @see Mage_Catalog_Helper_Product_Flat::CATALOG_FLAT_PROCESS_CODE
		 * отсутствует в Magento CE 1.4
		 */
		df_h()->index()->reindex('catalog_product_flat');
	}
	/** @return Df_Catalog_Model_Product */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}