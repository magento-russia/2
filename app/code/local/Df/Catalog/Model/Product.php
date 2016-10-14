<?php
/**
 * @method string|null getDescription()
 * @method int|null getEntityTypeId()
 * @method mixed getEvent()
 * @method bool|null getExcludeUrlRewrite()
 * @method bool|null getIsDuplicate()
 * @method bool|null getIsMassupdate()
 * @method string|null getMetaKeyword()
 * @method string|null getName()
 * @method Df_CatalogInventory_Model_Stock_Item|null getStockItem()
 * @method int|null getTaxClassId()
 * @method string|null getUrlKey()
 * @method Df_Catalog_Model_Resource_Product getResource()
 * @method Df_Catalog_Model_Product setAttributeSetId(int $value)
 * @method Df_Catalog_Model_Product setExcludeUrlRewrite(bool $value)
 * @method Df_Catalog_Model_Product setIsMassupdate(bool $value)
 * @method Df_Catalog_Model_Product setWebsiteIds(array $value)
 *
 * Обратите внимание, что ядро Magento иногда для обратной совместимости
 * пихает в качестве параметра объект класса Varien_Object,
 * а не класса Mage_CatalogInventory_Model_Stock_Item:
 * @see Mage_CatalogInventory_Model_Stock_Status::addStockStatusToProducts():
	foreach ($productCollection as $product) {
		 $object = new Varien_Object(array('is_in_stock' => $product->getData('is_salable')));
		 $product->setStockItem($object);
	 }
 * @method Df_Catalog_Model_Product setStockItem(Varien_Object $value)
 * @method Df_Catalog_Model_Product setStoreId(int $value)
 * $value: array(string => int|float)|null
 * @method Df_Catalog_Model_Product setTierPrice(array $value)
 * @method Df_Catalog_Model_Product setUrlKey(string $value)
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
		/** @uses Df_Catalog_Model_Product_Option::deleteWithDependencies() */
		df_each($this->getOptions(), 'deleteWithDependencies');
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
		catch (Exception $e) {
			rm_admin_end();
			$this->setIsMassupdate($isMassupdatePrev);
			$this->setExcludeUrlRewrite($excludeUrlRewritePrev);
			df_error($e);
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

	/** @return string|null */
	public function get1CId() {return $this->_getData(Df_1C_Const::ENTITY_EXTERNAL_ID);}

	/** @return Df_Eav_Model_Entity_Attribute_Set */
	public function getAttributeSet() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				df()->registry()->attributeSets()->findById($this->getAttributeSetId())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return int */
	public function getAttributeSetId() {
		/**
		 * Нельзя использовать @rm_nat,
		 * http://magento-forum.ru/topic/4377/
		 * 2014-07-27:
		 * Заменил @see rm_nat0 на (int) ради ускорения.
		 */
		return (int)$this->_getData(self::P__ATTRIBUTE_SET_ID);
	}

	/**
	 * Перекрываем родительский метод пока только ради модуля Яндекс.Маркет.
	 * Модулю Яндекс.Маркет нужно, чтобы адреса товарных страниц включали товарный раздел.
	 * Этого можно было бы достичь посредством
	 * Mage::register('current_category', $category)
	 * однако этот способ неэффективен по производительности:
	 * модулю Яндекс.Маркет изначально известны только идентификаторы
	 * привязанных к товару разделов, а загрузка этих товарных разделов
	 * только ради метода @see Mage_Catalog_Model_Product::getCategoryId()
	 * (где все равно используется только идентификатор) приведет к торможению системы.
	 * Именно поэтому перекрываем метод @see Mage_Catalog_Model_Product::getCategoryId() своим.
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

	/** @return Df_Catalog_Model_Category|null */
	public function getCategoryMain() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Catalog_Model_Category|null $result */
			$result = $this->getCategory();
			if (!$result && $this->getCategoryIds()) {
				$result = Df_Catalog_Model_Category::ld(
					rm_first($this->getCategoryIds()), $this->getStore()
				);
			}
			$this->{__METHOD__} = rm_n_set($result);
		}
		return rm_n_get($this->{__METHOD__});
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
					$result = rm_tax_h()->getPrice(
						$product = $this
						,$price =  $priceWithoutTax
						,$includingTax = true
						,$shippingAddress = null
						,$billingAddress = null
						,$ctc = null
						,$store = $product->getStore()
						,$priceIncludesTax = false
					);
					break;
				case Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE:
					$result = rm_tax_h()->getPrice(
						$product = $this
						,$price = $this->getMinimalPrice()
						,$includingTax = true
						,$shippingAddress = null
						,$billingAddress = null
						,$ctc = null
						,$store = $product->getStore()
						,$priceIncludesTax = null
					);
					/**
					 * Обратите внимание, что $result будет равно null,
					 * если товар отсутствует на складе
					 * @see Df_Catalog_Model_Product::getMinimalPrice()
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
						$result = is_null($result) ? $currentPrice : min($result, $currentPrice);
					}
					if (is_null($result)) {
						$result = 0.0;
					}
					break;
				case Mage_Catalog_Model_Product_Type::TYPE_BUNDLE:
					/** @var Mage_Bundle_Model_Product_Price $priceModel */
					$priceModel = $this->getPriceModel();
					df_assert($priceModel instanceof Mage_Bundle_Model_Product_Price);
					/**
					 * Метод @uses Mage_Bundle_Model_Product_Price::getTotalPrices()
					 * отсутствует в Magento CE 1.4.0.1
					 */
					/** @var bool $hasMethod_getTotalPrices */
					static $hasMethod_getTotalPrices;
					if (is_null($hasMethod_getTotalPrices)) {
						/**
						 * 2015-02-02
						 * Раньше здесь стоял код:
							$hasMethod_getTotalPrices = is_callable(array($priceModel, 'getTotalPrices'));
						 * Я так понимаю, что использовать @see is_callable() в Magento не стоит,
						 * потому что наличие @see Varien_Object::__call()
						 * приводит к тому, что @see is_callable() всегда возвращает true.
						 * Обратите внимание, что @uses method_exists(), в отличие от @see is_callable(),
						 * не гарантирует публичную доступность метода:
						 * т.е. метод может у класса быть, но вызывать его всё равно извне класса нельзя,
						 * потому что он имеет доступность private или protected.
						 * Пока эта проблема никак не решена.
						 */
						$hasMethod_getTotalPrices = method_exists($priceModel, 'getTotalPrices');
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
							rm_tax_h()->getPrice(
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
					 * http://magento-forum.ru/topic/4302/
					 * На самом деле, конкретно для модуля Яндекс.Маркет самым правильным
					 * (и реализованным теперь) решением
					 * стала просто отбраковка нестандартных системных типов товаров:
					 * @see Df_YandexMarket_Model_Yml_Processor_Offer::isEnabled()
					 */
					if (df_is_it_my_local_pc()) {
						Mage::log(sprintf('Неизвестный тип товара: «%s».', $this->getTypeId()));
					}
					/** @var float $priceWithoutTax */
					$priceWithoutTax = $this->getPriceModel()->getFinalPrice($qty = 1, $product = $this);
					$result =
						rm_tax_h()->getPrice(
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
			/**
			 * 2015-10-28
			 * Методы ядра могут мутить разное,
			 * но нам обязательно нужно вещественное (не целое) число.
			 * @used-by Df_YandexMarket_Model_Yml_Processor_Offer::isEnabled()
			 */
			$this->{__METHOD__} = floatval($result);
		}
		return $this->{__METHOD__};
	}

	/**
	 * @used-by isConfigurableChild()
	 * @return int[]
	 */
	public function getConfigurableParentIds() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * 2015-02-08
			 * Обратите внимание что данный метод используется методом @used-by isConfigurableChild(),
			 * поэтому мы не можем «оптимизировать код» вызовом здесь @used-by isConfigurableChild()
			 * и возвращением пустого массива на «true»: так мы попадём в рекурсию.
			 *
			 * Однако мы можем чуточку оптимизировать алгоритм по-другому,
			 * используя @uses Mage_Catalog_Model_Product::isConfigurable():
			 * если товар является настраиваемым,
			 * то он уж точно не может входить в состав других настраиваемых товаров,
			 * и мы можем просто вернуть пустой массив.
			 */
			$this->{__METHOD__} =
				$this->isConfigurable()
				? array()
				: Df_Catalog_Model_Product_Type_Configurable::s()->getParentIdsByChild($this->getId())
			;
		}
		return $this->{__METHOD__};
	}

	/** @return Df_Directory_Model_Country|null */
	public function getCountry() {
		if (!isset($this->{__METHOD__})) {
			/** @var string|null $code */
			$code = $this->getCountryIso2Code();
			/** @var Df_Directory_Model_Country|null $result */
			if (!$code) {
				$result = null;
			}
			else {
				if (!df_check_iso2($code)) {
					/**
					 * В магазине sekretsna.com сюда вместо 2-сивольного кода страны попало значение «Турция»,
					 * потому что администраторы магазина
					 * переделали стандартное товарное свойство «country_of_manufacture»,
					 * заменив стандартный справочник стран на нестандартные текстовые названия стран.
					 * http://magento-forum.ru/index.php?app=members&module=messaging&section=view&do=showConversation&topicID=2105
					 */
					$code = rm_country_ntc_ru($code);
				}
				$result = $code ? rm_country($code) : null;
			}
			$this->{__METHOD__} = rm_n_set($result);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/**
	 * Обратите внимание, что именно 2-буквенный код страны по стандарту ISO 3166-1 alpha-2
	 * является первичным идентификатором страны в Magento
	 * и в то же время идентификатором значения товарного свойства «Страна производства».
	 * https://ru.wikipedia.org/wiki/ISO_3166-1
	 * @return string|null
	 */
	public function getCountryIso2Code() {return $this->_getData(self::P__COUNTRY_OF_MANUFACTURE);}

	/** @return string|null */
	public function getCountryName() {return $this->getCountry() ? $this->getCountry()->getName() : null;}

	/**
	 * 2015-01-28
	 * Реализация метода сделана по примеру метода ядра
	 * @see Mage_Downloadable_Model_Product_Type::getLinks().
	 * Однако данный метод ядра не используется,
	 * потому что он возвращает массив элементов типа @see Mage_Downloadable_Model_Link,
	 * а нам нужно, чтобы элементы были типа @see Df_Downloadable_Model_Link.
	 *
	 * Обратите внимание, что если товар не является цифровым, то метод вернёт пустой массив.
	 * Вы можете самостоятельно предварительно проверить, является ли товар цифровым,
	 * вызовом метода @see Df_Catalog_Model_Product::isDownloadable()
	 *
	 * Обратите внимание, что получение прямых веб-адресов цифровых товаров
	 * пока не имеет практического применения,
	 * потому что при обращении по прямому веб-адресу цифрового товара
	 * веб-сервер возвращает ответ «403 Forbidden»,
	 * потому что в папке media/downloadable лежит файл .htaccess с правилами:
	 	Order deny,allow
	 	Deny from all
	 * @see Df_Downloadable_Model_Link::getUrl()
	 *
	 * @override
	 * @return array(int => Mage_Downloadable_Model_Link)
	 */
	public function getDownloadableLinks() {
		if (!isset($this->{__METHOD__})) {
			/** @var array(int => Mage_Downloadable_Model_Link) $result */
			$result = array();
			if ($this->isDownloadable()) {
				/** @var Df_Downloadable_Model_Resource_Link_Collection $links */
				$links = Df_Downloadable_Model_Link::c();
				$links->addProductToFilter($this->getId());
				$links->addTitleToResult($this->getStoreId());
				$links->addPriceToResult($this->getStore()->getWebsiteId());
				foreach ($links as $link) {
					/** @var Df_Downloadable_Model_Link $link */
					$link->setProduct($this);
					$result[$link->getId()] = $link;
				}
			}
			$this->{__METHOD__} = rm_n_set($result);
		}
		return rm_n_get($this->{__METHOD__});
	}

	/**
	 * 2015-01-28
	 * Обратите внимание, что downloadable samples —
	 * это всего лишь бесплатные образцы продащихся файлов.
	 * Сами продающиеся файлы можно получить методом @see getDownloadableLinks()
	 *
	 * Реализация метода сделана по примеру метода ядра
	 * @see Mage_Downloadable_Model_Product_Type::getSamples().
	 * Однако данный метод ядра не используется,
	 * потому что он возвращает коллекцию типа @see Mage_Downloadable_Model_Resource_Sample_Collection
	 * с элементами типа @see Mage_Downloadable_Model_Sample,
	 * а нам нужна коллекция типа @see Df_Downloadable_Model_Resource_Sample_Collection
	 * с элементами типа @see Df_Downloadable_Model_Sample.
	 *
	 * Обратите внимание, что метод не проверяет, является ли товар цифровым.
	 * Если товар не является цифровым, то метод вернёт пустую коллекцию.
	 * Чтобы не тратить ресурсы системы на конструирование коллекции и запросы к БД,
	 * Вы можете самостоятельно предварительно проверить, является ли товар цифровым,
	 * вызовом метода @see Df_Catalog_Model_Product::isDownloadable()
	 *
	 * @override
	 * @return Df_Downloadable_Model_Resource_Sample_Collection
	 */
	public function getDownloadableSamples() {
		if (!isset($this->{__METHOD__})) {
			/** @var Df_Downloadable_Model_Resource_Sample_Collection $result */
			$result = Df_Downloadable_Model_Sample::c();
			$result->addProductToFilter($this->getId());
			$result->addTitleToResult($this->getStoreId());
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

	/** @return string|null */
	public function getExternalId() {return $this->_getData(Df_1C_Const::ENTITY_EXTERNAL_ID);}

	/**
	 * Этот метод загружает из базы данных полный экземпляр товара.
	 * Метод имеет смысл вызывать в том случае, когда текущий экземпляр товара
	 * быз загружен из базы данных в составе коллекции (и тогда он содержит не всю информацию).
	 * @see reload()
	 * @return Df_Catalog_Model_Product
	 */
	public function getFull() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				!$this->isLoadedInCollection()
				? $this
				: self::ld($this->getId(), $this->getStoreId())
			;
		}
		return $this->{__METHOD__};
	}

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
	 * 2015-02-05
	 * Раньше тут стоял код:
		$id = @$this->_data[self::P__ID];
		return !$id ? null : (int)$id;
	 * Причём сказано было, что @ используется ради ускорения.
	 *
	 * Однако, согласно документации PHP:
	 * http://php.net/manual/language.operators.errorcontrol.php
	 * «If you have set a custom error handler function with set_error_handler()
	 * then it will still get called,
	 * but this custom error handler can (and should) call error_reporting()
	 * which will return 0 when the call that triggered the error was preceded by an @.»
	 *
	 * В нашем случае, если элемент массива с заданным ключом отсутствует,
	 * то PHP вызовет обработчик сбоев, установленный посредством @see set_error_handler()
	 * По этой причине не думаю, что @ даёт ускорение.
	 *
	 * Более того, там же в документации написано:
	 * «If the track_errors feature is enabled,
	 * any error message generated by the expression
	 * will be saved in the variable $php_errormsg.
	 * This variable will be overwritten on each error,
	 * so check early if you want to use it.»
	 * То есть, если включена опция «track_errors» (по умолчанию она отключена),
	 * http://php.net/manual/errorfunc.configuration.php#ini.track-errors
	 * то интерпретатор PHP ещё и будет запоминать
	 * все подобные несущественные в нашем случае сбои,
	 * когда в массиве отсутствует элемент с заданным ключом.
	 * Какое уж тут ускорение!
	 *
	 * Обратите внимание, что эти теоретические выкладки подтверждаются практикой,
	 * смотрите комментарий к методу @see Df_Core_Model_Settings::getValueCacheable()
	 *
	 * Поэтому переделал метод по-старинке.
	 * Обратите внимание, что нужно использовать именно @uses isset(),
	 * а не @see array_key_exists(),
	 * потому что если значение идентификатора товара
	 * присутствует в массиве @uses _data и равно null,
	 * то @uses isset() вернёт false, а @see array_key_exists() true,
	 * потому что если искомый ключ массива присутствует,
	 * но значение элемента с данным ключом равно null,
	 * то @uses isset() возвращает false (как нам и нужно),
	 * а @see array_key_exists() возвращает true
	 * (что в нашем коде привело бы к конвертации к целочисленному значению «0»
	 * и сбою при сохранении такого товара, потому что при сохранении модели
	 * отсутствие её новизна (отсутствие в базе данных)
	 * в современных версиях Magento CE определяется именно по строгому стравнению с null
	 * (если у модели идентификатор — целочисленное значение «0», то система считает,
	 * что идентификатор уже имеется и объект уже ранее сохранялся в базе данных.))
	 * @see Df_Core_Model_Resource_Db_UniqueChecker
	 * @see Mage_Core_Model_Resource_Db_Abstract::_checkUnique()
	 * http://php.net/manual/function.isset.php
	 * «Determine if a variable is set and is not NULL».
	 * http://stackoverflow.com/a/3210982
	 * @override
	 * @return int|null
	 */
	public function getId() {
		return isset($this->_data[self::P__ID]) ? (int)$this->_data[self::P__ID] : null;
	}

	/**
	 * Этот метод надо вызывать только для полнозагруженного товара
	 * и нельзя вызывать для элементов коллекции
	 * (чтобы для элемента коллекции получить полнозагруженную копию,
	 * используйте метод @see Df_Catalog_Model_Product::getFull()).
	 * Обратите внимание, что метод возвращает в том числе
	 * и ссылку на основную картинку товара.
	 * @return string[]
	 */
	public function getImageUrls() {
		if (!isset($this->{__METHOD__})) {
			/** @var string[] $result */
			$result = array();
			if (is_array($this->getMediaGallery())) {
				/** @var array(string => string)|null $images */
				$images = df_a($this->getMediaGallery(), 'images');
				if (is_array($images)) {
					foreach ($images as $image){
						/** @var string|null $fileName */
						$fileName = df_a($image, 'file');
						if ($fileName) {
							$result[]= $this->getMediaConfig()->getMediaUrl($fileName);
						}
					}
				}
			}
			$this->{__METHOD__} = $result;
		}
		return $this->{__METHOD__};
	}

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

	/** @return string|null */
	public function getManufacturerCode() {return $this->_getData(self::P__MANUFACTURER);}

	/** @return string|null */
	public function getManufacturerName() {
		return df_h()->catalog()->product()->getManufacturerNameByCode($this->getManufacturerCode());
	}

	/**
	 * Используйте @see Df_Catalog_Model_Product::getImageUrls(),
	 * чтобы получить массив всех веб-адресов картинок товара.
	 *
	 * Обратите внимание, что стандартный программный код иногда использует синтаксис:
	 * $this->getMediaGallery('images')
	 * Наш метод тоже поддерживает этот синтаксис.
	 * @param string|null $key [optional]
	 * @return mixed[]|null
	 */
	public function getMediaGallery($key = null) {
		/** @var mixed[]|null $result */
		$result = null;
		/** @var mixed[]|null $mediaGallery */
		$mediaGallery = $this->_getData(self::P__MEDIA_GALLERY);
		if (!is_null($mediaGallery)) {
			if (!is_array($mediaGallery)) {
				df_error(
					'Значением свойства «{property}» товара {product}'
					.' в настоящее время является строка «{value}» вместо массива.'
					."\nТаким образом, этот товар сейчас хранит информацию не обо всех своих картинках,"
					. ' а лишь об одной.'
					. "\nТак произошло из-за того, что товар был загружен из базы данных магазина"
					. " не поодиночке, а в составе коллекции."
					. "\nЧтобы получить информацию обо всех картинках товара — "
					. " загрузите данный товар поодиночке, посредством метода"
					. " Df_Catalog_Model_Product::getFull()."
					,array(
						'{property}' => self::P__MEDIA_GALLERY
						,'{product}' => $this->getTitle()
						,'{value}' => $mediaGallery
					)
				);
			}
			$result = is_null($key) ? $mediaGallery : df_a($mediaGallery, $key);
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
			$this->{__METHOD__} = $this->getMetaTitleDf();
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
			$select = rm_select()
				->from(array('maintable' => rm_table('catalog/product_index_price')), 'min_price')
				->where('? = maintable.entity_id', $this->getId())
				->where('? = maintable.website_id', $this->getStore()->getWebsiteId())
				->where('? = maintable.customer_group_id', 0)
				->where('? = maintable.tax_class_id', 0)
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

	/**
	 * @param string $title
	 * @return array
	 */
	public function getOptionsByTitle($title) {
		df_param_string_not_empty($title, 0);
		$result = array();
		foreach ($this->getOptions() as $option) {
			/** @var Df_Catalog_Model_Product_Option $option */
			if ($title === $option->getTitle()) {
				$result[]= $option;
			}
		}
		return $result;
	}

	/**
	 * 2015-02-13
	 * Родительский метод: @see Mage_Catalog_Model_Product::getResourceCollection()
	 * @override
	 * @return Df_Catalog_Model_Resource_Product_Collection
	 */
	public function getResourceCollection() {return self::c()->setStoreId($this->getStoreId());}

	/** @return string */
	public function getTitle() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = strtr('«{name}» [{sku}]', array(
				'{name}' => $this->getName(), '{sku}' => $this->getSku()
			));
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	public function getTypeName() {return self::getTypeNameById($this->getTypeId());}

	/**
	 * 2015-01-25
	 * Обратите внимание,
	 * что для некоторых типов (например, виртуальных и скачиваемых) нормально не иметь вес.
	 * Обратите также внимание,
	 * что если товар по своему типу (например, простой или настраиваемый) способен иметь вес,
	 * но информация о весе данного в базе данных интернет-магазина остутствует,
	 * то @see Df_Catalog_Model_Product::getWeight()
	 * вернёт не нулевой вес, а то значение, которое администратор интернет-магазина
	 * указал в качестве веса по умолчанию.
	 * @override
	 * @return float
	 */
	public function getWeight() {return $this->getWeightRaw();}

	/**
	 * @param bool $canLoadFull [optional]
	 * @param bool $canUseConfig [optional]
	 * @return float
	 */
	public function getWeightRaw($canLoadFull = true, $canUseConfig = true) {
		/**
		 * Раньше тут стояло parent::_getData(self::P__WEIGHT), что в корне неправильно,
		 * потому что в родительском классе @see Mage_Catalog_Model_Product
		 * метод @see Mage_Catalog_Model_Product::getWeight() присутствует,
		 * и он работает так:
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
			// Обратите внимание, что для некоторых типов (например, виртуальных и скачиваемых)
			// нормально не иметь вес.
			// Думаю, что перезагружать товар для уточнения веса
			// имеет смысл вообще только для простых товаров.
			if (Mage_Catalog_Model_Product_Type::TYPE_SIMPLE !== $this->getTypeId()) {
				// $result может быть равно NULL.
				// Явно устанавливаем значение 0.0,
				// чтобы получить результат вещественного типа.
				$result = 0.0;
			}
			else {
				if ($this->isLoadedInCollection() && $canLoadFull) {
					// Товар был взят из коллекции,
					// в которую не было добавлено свойство «вес».
					/** @var bool $inRecursion */
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
						 * (1С:Управление торговлей и Magento Dataflow)
						 */
						$result = $this->getFull()->getWeight(false, $canUseConfig);
						$inRecursion = false;
					}
				}
				if ($canUseConfig && (is_null($result) || (0.0 === rm_float($result)))) {
					$result = df_cfg()->shipping()->product()->getDefaultWeight($this->getStore());
				}
			}
		}
		return rm_float($result);
	}

	/** @return float */
	public function getWeightInKilogrammes() {return rm_weight()->inKilogrammes($this->getWeight());}

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

	/**
	 * 2015-02-08
	 * @uses getConfigurableParentIds()
	 * Обратите внимание, что обратную задачу — определить,
	 * является ли товар настраиваемым товаром (родителем) —
	 * мы можем просто вызовом метода @see Mage_Catalog_Model_Product::isConfigurable()
	 * @return bool
	 */
	public function isConfigurableChild() {
		if (!isset($this->{__METHOD__})) {
			/**
			 * 2015-02-08
			 * Чуточку оптимизировали алгорим, используя намеренно избыточное условие
			 * @uses Mage_Catalog_Model_Product::isConfigurable():
			 * если товар является настраиваемым,
			 * то он уж точно не может входить в состав других настраиваемых товаров,
			 * и мы можем просто вернуть пустой массив.
			 * Таким образом избавляемся от лишних вызовов функций и методов.
			 */
			$this->{__METHOD__} = !$this->isConfigurable() && !!$this->getConfigurableParentIds();
		}
		return $this->{__METHOD__};
	}

	/**
	 * @see getDownloadableSamples()
	 * @return bool
	 */
	public function isDownloadable() {
		return Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE === $this->getTypeId();
	}

	/** @return bool */
	public function isLoadedInCollection() {
		return rm_bool($this->_getData(self::P__RM__LOADED_IN_COLLECTION));
	}

	/** @return Df_Catalog_Model_Product */
	public function markAsLoadedInCollection() {
		$this->setData(self::P__RM__LOADED_IN_COLLECTION, true);
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
	 * (1С:Управление торговлей и Magento Dataflow).
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
	 * @param int $storeId [optional]
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
		catch (Exception $e) {
			rm_admin_end();
			df_error($e);
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
		 * http://stackoverflow.com/a/8543548/1164342
		 */
		/** @var bool $tierPriceIsNull */
		$tierPriceIsNull = is_null($this->getTierPrice());
		if ($tierPriceIsNull) {
			$this->setTierPrice(array('website_id' => 0));
		}
		try {
			$this->save();
		}
		catch (Exception $e) {
			rm_admin_end();
			$this->setIsMassupdate($isMassupdatePrev);
			$this->setExcludeUrlRewrite($excludeUrlRewritePrev);
			if ($tierPriceIsNull) {
				$this->setTierPrice(null);
			}
			df_error($e);
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
	 * @param string|null $value
	 * @return Df_Catalog_Model_Product
	 */
	public function set1CId($value) {
		$this->setData(Df_1C_Const::ENTITY_EXTERNAL_ID, $value);
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

	/**
	 * 2015-02-09
	 * Родительский метод: @see Mage_Catalog_Model_Product::_getResource()
	 * Обратите внимание, что родительский метод никогда не возвращает
	 * ресурсную модель @see Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Flat
	 * эта ресурсная модель (для режима денормализации) используется только коллекцией.
	 * @override
	 * @return Df_Catalog_Model_Resource_Product
	 */
	protected function _getResource() {return Df_Catalog_Model_Resource_Product::s();}

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
		return implode(' - ', array_filter(array(
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
		/**
		 * 2015-02-09
		 * Намеренно убрал вызов родительского метода @see Mage_Catalog_Product::_construct().
		 */
		/**
		 * Обязательно, иначе будет сбой при установке.
		 * Ведь наш класс перекрывает системный класс @see Mage_Catalog_Model_Product,
		 * а системный класс используется при установке ядра,
		 * в то время, когда Российская сборка ещё не инициализирована.
		 */
		Df_Core_Boot::run();
	}
	/**
	 * @used-by Df_1C_Cml2_Processor_Product_AddExternalId::_construct()
	 * @used-by Df_Catalog_Model_Resource_Category_Collection::_init()
	 * @used-by Df_Catalog_Model_XmlExport_Product::_construct()
	 * @used-by Df_Dataflow_Model_Importer_Product_Gallery::_construct()
	 * @used-by Df_Dataflow_Model_Importer_Product_Images::_construct()
	 * @used-by Df_Dataflow_Model_Importer_Product_Specialized::_construct()
	 * @used-by Df_Dataflow_Model_Registry_Collection_Products::getEntityClass()
	 * @used-by Df_Localization_Onetime_Dictionary_Rule_Conditions_Product::getEntityClass()
	 * @used-by Df_Localization_Onetime_Processor_Catalog_Product::_construct()
	 * @used-by Df_PromoGift_Model_Gift::getProduct()
	 * @used-by Df_PromoGift_Model_Gift::_construct()
	 * @used-by Df_Seo_Model_Processor_Image::_construct()
	 * @used-by Df_Seo_Model_Processor_MediaGallery::_construct()
	 * @used-by Df_Seo_Model_Processor_Image_Exif::_construct()
	 * @used-by Df_Seo_Model_Processor_Image_Renamer::_construct()
	 */
	const _C = __CLASS__;
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
	const P__RM__LOADED_IN_COLLECTION = 'rm__loaded_in_collection';
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

	/**
	 * @param bool $disableFlat [optional]
	 * @return Df_Catalog_Model_Resource_Product_Collection
	 */
	public static function c($disableFlat = false) {
		return new Df_Catalog_Model_Resource_Product_Collection(array(
			Df_Catalog_Model_Resource_Product_Collection::P__DISABLE_FLAT => $disableFlat
		));
	}

	/**
	 * @param Df_Core_Model_StoreM|int|string|bool|null $storeId [optional]
	 * @return Df_Catalog_Model_Product
	 */
	public static function createNew($storeId = null) {
		rm_admin_begin();
		/** @var Df_Catalog_Model_Product $result */
		$result = self::i();
		if (!is_null($storeId)) {
			$result->setStoreId($storeId);
		}
		$result->setStockItem(Df_CatalogInventory_Model_Stock_Item::i());
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